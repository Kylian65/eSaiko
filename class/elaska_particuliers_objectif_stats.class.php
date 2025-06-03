<?php
/* Copyright (C) 2025 Elaska Dev Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Module de statistiques pour les objectifs de vie des particuliers
 * 
 * @package    Elaska
 * @subpackage Particuliers
 */
class ElaskaParticulierObjectifStats
{
    /**
     * @var DoliDB    Database handler
     */
    public $db;
    
    /**
     * @var string    Error message
     */
    public $error;
    
    /**
     * @var array     Error messages
     */
    public $errors = array();
    
    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Récupère les statistiques globales pour tous les objectifs
     *
     * @param  array  $filtres     Filtres optionnels
     * @return array               Données statistiques
     */
    public function getStatistiquesGlobales($filtres = array())
    {
        global $conf, $langs;
        
        $stats = array(
            'total' => 0,
            'par_statut' => array(),
            'par_categorie' => array(),
            'progression_moyenne' => 0,
            'delai_moyen' => 0,
            'taux_completion' => 0,
            'budget_total' => 0,
            'montant_total_depense' => 0
        );
        
        // Construire la clause WHERE en fonction des filtres
        $where = "1=1";
        if (!empty($filtres['date_debut'])) {
            $where .= " AND date_creation >= '".$this->db->idate($filtres['date_debut'])."'";
        }
        if (!empty($filtres['date_fin'])) {
            $where .= " AND date_creation <= '".$this->db->idate($filtres['date_fin'])."'";
        }
        if (!empty($filtres['categorie'])) {
            $where .= " AND categorie = '".$this->db->escape($filtres['categorie'])."'";
        }
        if (!empty($filtres['statut'])) {
            if (is_array($filtres['statut'])) {
                $where .= " AND statut IN ('".implode("','", $filtres['statut'])."')";
            } else {
                $where .= " AND statut = '".$this->db->escape($filtres['statut'])."'";
            }
        }
        
        // Récupérer les données de base
        $sql = "SELECT COUNT(*) as total, 
                SUM(budget_prevu) as budget_total,
                SUM(montant_depense) as montant_depense,
                AVG(progression) as progression_moyenne
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where;
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            dol_syslog(__METHOD__." ".$this->error, LOG_ERR);
            return $stats;
        }
        
        $obj = $this->db->fetch_object($resql);
        if ($obj) {
            $stats['total'] = $obj->total;
            $stats['progression_moyenne'] = round($obj->progression_moyenne, 2);
            $stats['budget_total'] = $obj->budget_total;
            $stats['montant_total_depense'] = $obj->montant_depense;
            
            // Calculer le taux d'utilisation du budget
            if ($stats['budget_total'] > 0) {
                $stats['taux_utilisation_budget'] = round(($stats['montant_total_depense'] / $stats['budget_total']) * 100, 2);
            } else {
                $stats['taux_utilisation_budget'] = 0;
            }
        }
        
        // Nombre d'objectifs par statut
        $sql = "SELECT statut, COUNT(*) as nb
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where."
                GROUP BY statut";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $stats['par_statut'][$obj->statut] = $obj->nb;
            }
        }
        
        // Nombre d'objectifs par catégorie
        $sql = "SELECT categorie, COUNT(*) as nb
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where."
                GROUP BY categorie";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $stats['par_categorie'][$obj->categorie] = $obj->nb;
            }
        }
        
        // Calculer le délai moyen de réalisation (pour les objectifs terminés)
        $sql = "SELECT AVG(DATEDIFF(date_fin, date_debut)) as delai_moyen
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where." AND statut = 'termine' AND date_fin IS NOT NULL AND date_debut IS NOT NULL";
        
        $resql = $this->db->query($sql);
        if ($resql && $obj = $this->db->fetch_object($resql)) {
            $stats['delai_moyen'] = $obj->delai_moyen;
        }
        
        // Calculer le taux de complétion (objectifs terminés / total)
        if ($stats['total'] > 0 && isset($stats['par_statut']['termine'])) {
            $stats['taux_completion'] = round(($stats['par_statut']['termine'] / $stats['total']) * 100, 2);
        }
        
        // Trier les catégories par nombre d'objectifs décroissant
        arsort($stats['par_categorie']);
        
        return $stats;
    }
    
    /**
     * Récupère les statistiques par particulier
     *
     * @param  int    $particulier_id    ID du particulier
     * @param  array  $filtres           Filtres optionnels
     * @return array                     Données statistiques
     */
    public function getStatistiquesParticulier($particulier_id, $filtres = array())
    {
        global $conf, $langs;
        
        if (empty($particulier_id)) {
            $this->error = $langs->trans('ParamMissing', 'particulier_id');
            return array();
        }
        
        $stats = array(
            'particulier_id' => $particulier_id,
            'total' => 0,
            'par_statut' => array(),
            'par_categorie' => array(),
            'progression_moyenne' => 0,
            'taux_completion' => 0,
            'objectifs_en_retard' => 0,
            'tendances' => array()
        );
        
        // Construire la clause WHERE en fonction des filtres
        $where = "fk_particulier = ".(int)$particulier_id;
        if (!empty($filtres['date_debut'])) {
            $where .= " AND date_creation >= '".$this->db->idate($filtres['date_debut'])."'";
        }
        if (!empty($filtres['date_fin'])) {
            $where .= " AND date_creation <= '".$this->db->idate($filtres['date_fin'])."'";
        }
        if (!empty($filtres['categorie'])) {
            $where .= " AND categorie = '".$this->db->escape($filtres['categorie'])."'";
        }
        
        // Récupérer les données de base
        $sql = "SELECT COUNT(*) as total, 
                AVG(progression) as progression_moyenne
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where;
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            dol_syslog(__METHOD__." ".$this->error, LOG_ERR);
            return $stats;
        }
        
        $obj = $this->db->fetch_object($resql);
        if ($obj) {
            $stats['total'] = $obj->total;
            $stats['progression_moyenne'] = round($obj->progression_moyenne, 2);
        }
        
        // Nombre d'objectifs par statut
        $sql = "SELECT statut, COUNT(*) as nb
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where."
                GROUP BY statut";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $stats['par_statut'][$obj->statut] = $obj->nb;
            }
        }
        
        // Nombre d'objectifs par catégorie
        $sql = "SELECT categorie, COUNT(*) as nb
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where."
                GROUP BY categorie";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $stats['par_categorie'][$obj->categorie] = $obj->nb;
            }
        }
        
        // Calculer le taux de complétion (objectifs terminés / total)
        if ($stats['total'] > 0 && isset($stats['par_statut']['termine'])) {
            $stats['taux_completion'] = round(($stats['par_statut']['termine'] / $stats['total']) * 100, 2);
        }
        
        // Compter les objectifs en retard
        $now = dol_now();
        $sql = "SELECT COUNT(*) as nb
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where." AND date_echeance < '".$this->db->idate($now)."'
                AND statut NOT IN ('termine', 'abandonne')";
        
        $resql = $this->db->query($sql);
        if ($resql && $obj = $this->db->fetch_object($resql)) {
            $stats['objectifs_en_retard'] = $obj->nb;
        }
        
        // Récupérer l'évolution des objectifs sur les derniers mois
        $stats['evolution'] = $this->getEvolutionObjectifs($particulier_id, 6); // 6 derniers mois
        
        // Analyser les tendances
        $stats['tendances'] = $this->detecterTendances($stats);
        
        // Trier les catégories par nombre d'objectifs décroissant
        arsort($stats['par_categorie']);
        
        return $stats;
    }
    
    /**
     * Récupère l'évolution du nombre d'objectifs sur une période
     *
     * @param  int    $particulier_id    ID du particulier (0 pour tous)
     * @param  int    $nbMois            Nombre de mois à analyser
     * @return array                     Données d'évolution par mois
     */
    public function getEvolutionObjectifs($particulier_id = 0, $nbMois = 12)
    {
        $evolution = array();
        
        // Générer les dates pour les X derniers mois
        for ($i = $nbMois - 1; $i >= 0; $i--) {
            $date = dol_time_plus_duree(dol_now(), -$i, 'm');
            $mois = dol_print_date($date, '%Y-%m');
            
            $evolution[$mois] = array(
                'mois' => $mois,
                'libelle' => dol_print_date($date, '%b %Y'),
                'crees' => 0,
                'termines' => 0
            );
        }
        
        // Construire la clause WHERE
        $where = "1=1";
        if ($particulier_id > 0) {
            $where .= " AND fk_particulier = ".(int)$particulier_id;
        }
        
        // Objectifs créés par mois
        $date_debut = dol_time_plus_duree(dol_now(), -$nbMois, 'm');
        $sql = "SELECT YEAR(date_creation) as annee, MONTH(date_creation) as mois, COUNT(*) as nb
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where." AND date_creation >= '".$this->db->idate($date_debut)."'
                GROUP BY YEAR(date_creation), MONTH(date_creation)
                ORDER BY YEAR(date_creation) ASC, MONTH(date_creation) ASC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $mois_key = sprintf('%04d-%02d', $obj->annee, $obj->mois);
                if (isset($evolution[$mois_key])) {
                    $evolution[$mois_key]['crees'] = $obj->nb;
                }
            }
        }
        
        // Objectifs terminés par mois
        $sql = "SELECT YEAR(date_fin) as annee, MONTH(date_fin) as mois, COUNT(*) as nb
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where." AND statut = 'termine' AND date_fin >= '".$this->db->idate($date_debut)."'
                GROUP BY YEAR(date_fin), MONTH(date_fin)
                ORDER BY YEAR(date_fin) ASC, MONTH(date_fin) ASC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $mois_key = sprintf('%04d-%02d', $obj->annee, $obj->mois);
                if (isset($evolution[$mois_key])) {
                    $evolution[$mois_key]['termines'] = $obj->nb;
                }
            }
        }
        
        // Convertir en tableau indexé numériquement
        return array_values($evolution);
    }
    
    /**
     * Détecte les tendances à partir des statistiques
     *
     * @param  array  $stats             Statistiques à analyser
     * @return array                     Tendances détectées
     */
    protected function detecterTendances($stats)
    {
        $tendances = array();
        
        // Pas assez de données pour détecter des tendances
        if ($stats['total'] < 3) {
            return array();
        }
        
        // Analyser l'évolution récente si disponible
        if (!empty($stats['evolution'])) {
            $evolution = $stats['evolution'];
            $nb_mois = count($evolution);
            
            if ($nb_mois > 3) {
                // Analyser les 3 derniers mois vs les 3 mois précédents
                $derniers3Mois = array_slice($evolution, -3);
                $precedents3Mois = array_slice($evolution, -6, 3);
                
                $crees_derniers = array_sum(array_column($derniers3Mois, 'crees'));
                $crees_precedents = array_sum(array_column($precedents3Mois, 'crees'));
                
                $termines_derniers = array_sum(array_column($derniers3Mois, 'termines'));
                $termines_precedents = array_sum(array_column($precedents3Mois, 'termines'));
                
                // Variation du nombre d'objectifs créés
                if ($crees_precedents > 0) {
                    $var_crees = (($crees_derniers - $crees_precedents) / $crees_precedents) * 100;
                    
                    if ($var_crees > 20) {
                        $tendances[] = array(
                            'type' => 'hausse',
                            'sujet' => 'creation',
                            'valeur' => round($var_crees, 1),
                            'message' => "Augmentation de la création d'objectifs (+".round($var_crees, 1)."%)"
                        );
                    } elseif ($var_crees < -20) {
                        $tendances[] = array(
                            'type' => 'baisse',
                            'sujet' => 'creation',
                            'valeur' => round($var_crees, 1),
                            'message' => "Diminution de la création d'objectifs (".round($var_crees, 1)."%)"
                        );
                    }
                }
                
                // Variation du nombre d'objectifs terminés
                if ($termines_precedents > 0) {
                    $var_termines = (($termines_derniers - $termines_precedents) / $termines_precedents) * 100;
                    
                    if ($var_termines > 20) {
                        $tendances[] = array(
                            'type' => 'hausse',
                            'sujet' => 'completion',
                            'valeur' => round($var_termines, 1),
                            'message' => "Augmentation des objectifs terminés (+".round($var_termines, 1)."%)"
                        );
                    } elseif ($var_termines < -20) {
                        $tendances[] = array(
                            'type' => 'baisse',
                            'sujet' => 'completion',
                            'valeur' => round($var_termines, 1),
                            'message' => "Diminution des objectifs terminés (".round($var_termines, 1)."%)"
                        );
                    }
                }
            }
        }
        
        // Analyse du taux de réussite
        if ($stats['total'] > 5 && isset($stats['taux_completion'])) {
            $taux_completion = $stats['taux_completion'];
            
            if ($taux_completion < 30) {
                $tendances[] = array(
                    'type' => 'alerte',
                    'sujet' => 'taux_completion',
                    'valeur' => $taux_completion,
                    'message' => "Taux de réussite des objectifs très bas ($taux_completion%)"
                );
            } elseif ($taux_completion > 80) {
                $tendances[] = array(
                    'type' => 'positif',
                    'sujet' => 'taux_completion',
                    'valeur' => $taux_completion,
                    'message' => "Excellent taux de réussite des objectifs ($taux_completion%)"
                );
            }
        }
        
        // Objectifs en retard
        if (isset($stats['objectifs_en_retard']) && $stats['objectifs_en_retard'] > 0) {
            $pct_retard = ($stats['objectifs_en_retard'] / $stats['total']) * 100;
            
            if ($pct_retard > 30) {
                $tendances[] = array(
                    'type' => 'alerte',
                    'sujet' => 'retard',
                    'valeur' => round($pct_retard, 1),
                    'message' => "Proportion élevée d'objectifs en retard (".round($pct_retard, 1)."%)"
                );
            }
        }
        
        // Analyse des catégories
        if (!empty($stats['par_categorie'])) {
            $max_categorie = array_keys($stats['par_categorie'])[0];
            $pct_max = ($stats['par_categorie'][$max_categorie] / $stats['total']) * 100;
            
            if ($pct_max > 60) {
                $tendances[] = array(
                    'type' => 'info',
                    'sujet' => 'categorie',
                    'valeur' => round($pct_max, 1),
                    'message' => "Forte concentration d'objectifs dans la catégorie \"$max_categorie\" (".round($pct_max, 1)."%)"
                );
            }
        }
        
        return $tendances;
    }
    
    /**
     * Génère un rapport des performances par catégorie d'objectifs
     *
     * @param  array  $filtres     Filtres optionnels
     * @return array               Données du rapport
     */
    public function getRapportPerformanceParCategorie($filtres = array())
    {
        global $conf, $langs;
        
        $rapport = array();
        
        // Construire la clause WHERE en fonction des filtres
        $where = "1=1";
        if (!empty($filtres['date_debut'])) {
            $where .= " AND date_creation >= '".$this->db->idate($filtres['date_debut'])."'";
        }
        if (!empty($filtres['date_fin'])) {
            $where .= " AND date_creation <= '".$this->db->idate($filtres['date_fin'])."'";
        }
        if (!empty($filtres['particulier_id'])) {
            $where .= " AND fk_particulier = ".(int)$filtres['particulier_id'];
        }
        
        // Requête pour obtenir les performances par catégorie
        $sql = "SELECT categorie, 
                COUNT(*) as total,
                SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) as termines,
                SUM(CASE WHEN statut = 'abandonne' THEN 1 ELSE 0 END) as abandonnes,
                AVG(progression) as progression_moyenne,
                AVG(CASE WHEN statut = 'termine' AND date_fin IS NOT NULL AND date_debut IS NOT NULL 
                    THEN DATEDIFF(date_fin, date_debut) ELSE NULL END) as duree_moyenne
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif
                WHERE ".$where."
                GROUP BY categorie
                ORDER BY total DESC";
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            dol_syslog(__METHOD__." ".$this->error, LOG_ERR);
            return $rapport;
        }
        
        while ($obj = $this->db->fetch_object($resql)) {
            $taux_reussite = ($obj->total > 0) ? ($obj->termines / $obj->total) * 100 : 0;
            $taux_abandon = ($obj->total > 0) ? ($obj->abandonnes / $obj->total) * 100 : 0;
            
            $rapport[] = array(
                'categorie' => $obj->categorie,
                'total' => $obj->total,
                'termines' => $obj->termines,
                'abandonnes' => $obj->abandonnes,
                'progression_moyenne' => round($obj->progression_moyenne, 2),
                'duree_moyenne' => round($obj->duree_moyenne, 2),
                'taux_reussite' => round($taux_reussite, 2),
                'taux_abandon' => round($taux_abandon, 2)
            );
        }
        
        return $rapport;
    }
    
    /**
     * Analyse les corrélations entre objectifs et démarches administratives
     *
     * @param  array  $filtres     Filtres optionnels
     * @return array               Données des corrélations
     */
    public function analyserCorrelationsDemarches($filtres = array())
    {
        global $conf, $langs;
        
        $correlations = array();
        
        // Construire la clause WHERE pour les objectifs
        $where_obj = "1=1";
        if (!empty($filtres['date_debut'])) {
            $where_obj .= " AND o.date_creation >= '".$this->db->idate($filtres['date_debut'])."'";
        }
        if (!empty($filtres['date_fin'])) {
            $where_obj .= " AND o.date_creation <= '".$this->db->idate($filtres['date_fin'])."'";
        }
        if (!empty($filtres['particulier_id'])) {
            $where_obj .= " AND o.fk_particulier = ".(int)$filtres['particulier_id'];
        }
        if (!empty($filtres['statut'])) {
            $where_obj .= " AND o.statut = '".$this->db->escape($filtres['statut'])."'";
        }
        
        // Requête pour analyser les corrélations
        $sql = "SELECT o.categorie, d.type as type_demarche, COUNT(*) as nb
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif as o
                JOIN ".MAIN_DB_PREFIX."elaska_particulier_objectif_demarche as od ON o.rowid = od.fk_objectif
                JOIN ".MAIN_DB_PREFIX."elaska_particulier_demarche as d ON d.rowid = od.fk_demarche
                WHERE ".$where_obj."
                GROUP BY o.categorie, d.type
                ORDER BY nb DESC";
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            dol_syslog(__METHOD__." ".$this->error, LOG_ERR);
            return $correlations;
        }
        
        while ($obj = $this->db->fetch_object($resql)) {
            if (!isset($correlations[$obj->categorie])) {
                $correlations[$obj->categorie] = array();
            }
            
            $correlations[$obj->categorie][$obj->type_demarche] = $obj->nb;
        }
        
        return $correlations;
    }
    
    /**
     * Génère des données pour un tableau de bord
     *
     * @param  int    $particulier_id    ID du particulier (0 pour global)
     * @return array                     Données pour le tableau de bord
     */
    public function getDonneesDashboard($particulier_id = 0)
    {
        global $conf, $langs;
        
        $donnees = array(
            'resume' => array(),
            'evolution' => array(),
            'performance_categories' => array(),
            'objectifs_recents' => array()
        );
        
        // Récupérer les statistiques générales
        $filtres = array();
        if ($particulier_id > 0) {
            $stats = $this->getStatistiquesParticulier($particulier_id);
            $donnees['resume'] = array(
                'total' => $stats['total'],
                'en_cours' => isset($stats['par_statut']['en_cours']) ? $stats['par_statut']['en_cours'] : 0,
                'termines' => isset($stats['par_statut']['termine']) ? $stats['par_statut']['termine'] : 0,
                'en_retard' => $stats['objectifs_en_retard'],
                'progression_moyenne' => $stats['progression_moyenne'],
                'taux_completion' => $stats['taux_completion']
            );
        } else {
            $stats = $this->getStatistiquesGlobales();
            $donnees['resume'] = array(
                'total' => $stats['total'],
                'en_cours' => isset($stats['par_statut']['en_cours']) ? $stats['par_statut']['en_cours'] : 0,
                'termines' => isset($stats['par_statut']['termine']) ? $stats['par_statut']['termine'] : 0,
                'progression_moyenne' => $stats['progression_moyenne'],
                'taux_completion' => $stats['taux_completion'],
                'delai_moyen' => $stats['delai_moyen']
            );
        }
        
        // Récupérer l'évolution sur 6 mois
        $donnees['evolution'] = $this->getEvolutionObjectifs($particulier_id, 6);
        
        // Récupérer les performances par catégorie
        $filtres = array();
        if ($particulier_id > 0) {
            $filtres['particulier_id'] = $particulier_id;
        }
        $donnees['performance_categories'] = $this->getRapportPerformanceParCategorie($filtres);
        
        // Récupérer les objectifs récents
        $sql = "SELECT rowid, titre, categorie, statut, progression, date_creation, date_echeance
                FROM ".MAIN_DB_PREFIX."elaska_particulier_objectif";
        
        if ($particulier_id > 0) {
            $sql .= " WHERE fk_particulier = ".(int)$particulier_id;
        }
        
        $sql .= " ORDER BY date_creation DESC LIMIT 10";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $donnees['objectifs_recents'][] = array(
                    'id' => $obj->rowid,
                    'titre' => $obj->titre,
                    'categorie' => $obj->categorie,
                    'statut' => $obj->statut,
                    'progression' => $obj->progression,
                    'date_creation' => $this->db->jdate($obj->date_creation),
                    'date_echeance' => $obj->date_echeance ? $this->db->jdate($obj->date_echeance) : null
                );
            }
        }
        
        // Ajouter les tendances
        $donnees['tendances'] = isset($stats['tendances']) ? $stats['tendances'] : array();
        
        return $donnees;
    }
    
    /**
     * Exporte les statistiques au format CSV
     *
     * @param  array  $stats       Données statistiques à exporter
     * @param  array  $options     Options d'export
     * @return string              Contenu CSV
     */
    public function exportCSV($stats, $options = array())
    {
        if (empty($stats)) {
            return '';
        }
        
        $csv = '';
        
        // En-têtes des colonnes
        $headers = array('Catégorie', 'Total', 'Terminés', 'En cours', 'Taux de réussite (%)', 'Progression moyenne (%)');
        $csv .= implode(';', $headers)."\n";
        
        // Données par catégorie
        if (!empty($stats['par_categorie'])) {
            foreach ($stats['par_categorie'] as $categorie => $nombre) {
                $termines = isset($stats['par_statut']['termine']) ? $stats['par_statut']['termine'] : 0;
                $en_cours = isset($stats['par_statut']['en_cours']) ? $stats['par_statut']['en_cours'] : 0;
                $taux = ($stats['total'] > 0) ? ($termines / $stats['total']) * 100 : 0;
                
                $ligne = array(
                    $categorie,
                    $nombre,
                    $termines,
                    $en_cours,
                    round($taux, 2),
                    $stats['progression_moyenne']
                );
                
                $csv .= implode(';', $ligne)."\n";
            }
        }
        
        // Ligne de total
        $total_termines = isset($stats['par_statut']['termine']) ? $stats['par_statut']['termine'] : 0;
        $total_en_cours = isset($stats['par_statut']['en_cours']) ? $stats['par_statut']['en_cours'] : 0;
        $total = array(
            'TOTAL',
            $stats['total'],
            $total_termines,
            $total_en_cours,
            $stats['taux_completion'],
            $stats['progression_moyenne']
        );
        
        $csv .= implode(';', $total)."\n";
        
        return $csv;
    }
}
