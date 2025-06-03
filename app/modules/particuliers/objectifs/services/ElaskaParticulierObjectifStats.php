<?php
/**
 * Statistiques sur les objectifs des particuliers
 * 
 * @package Elaska
 * @subpackage Particuliers
 * @author Elaska Dev Team
 * @version 4.5.0
 */
class ElaskaParticulierObjectifStats {
    /**
     * Génère des statistiques de réalisation d'objectifs
     * @param int $particulier_id ID du particulier
     * @return array Données statistiques
     */
    public static function genererStatistiques(int $particulier_id): array {
        // Récupérer les objectifs du particulier
        $objectifs_tous = ElaskaParticulierObjectif::findAllBy([
            'particulier_id' => $particulier_id
        ]);
        
        if (empty($objectifs_tous)) {
            return [
                'total' => 0,
                'statuts' => [],
                'categories' => [],
                'progression_moyenne' => 0,
                'taux_completion' => 0,
                'taux_abandon' => 0
            ];
        }
        
        // Statistiques générales
        $total = count($objectifs_tous);
        $stats = [
            'total' => $total,
            'statuts' => [],
            'categories' => [],
            'progression_moyenne' => 0,
            'taux_completion' => 0,
            'taux_abandon' => 0,
            'temps_moyen_completion' => 0
        ];
        
        // Initialisation des compteurs
        $somme_progression = 0;
        $objectifs_completes = 0;
        $objectifs_abandonnes = 0;
        $temps_total_completion = 0;
        $nb_objectifs_avec_temps = 0;
        
        // Analyse des objectifs
        foreach ($objectifs_tous as $objectif) {
            // Statistiques par statut
            $statut = $objectif->getStatut();
            if (!isset($stats['statuts'][$statut])) {
                $stats['statuts'][$statut] = 0;
            }
            $stats['statuts'][$statut]++;
            
            // Statistiques par catégorie
            $categorie = $objectif->getCategorie();
            if (!isset($stats['categories'][$categorie])) {
                $stats['categories'][$categorie] = 0;
            }
            $stats['categories'][$categorie]++;
            
            // Progression moyenne
            $somme_progression += $objectif->getProgression();
            
            // Taux de complétion et d'abandon
            if ($statut == 'complete') {
                $objectifs_completes++;
                
                // Temps de complétion
                $date_debut = $objectif->getDateCreation();
                $date_fin = $objectif->getDateCompletion();
                
                if ($date_debut && $date_fin) {
                    $temps_completion = $date_fin->diff($date_debut)->days;
                    $temps_total_completion += $temps_completion;
                    $nb_objectifs_avec_temps++;
                }
            } elseif ($statut == 'abandonne') {
                $objectifs_abandonnes++;
            }
        }
        
        // Calcul des moyennes et taux
        if ($total > 0) {
            $stats['progression_moyenne'] = $somme_progression / $total;
            $stats['taux_completion'] = ($objectifs_completes / $total) * 100;
            $stats['taux_abandon'] = ($objectifs_abandonnes / $total) * 100;
        }
        
        if ($nb_objectifs_avec_temps > 0) {
            $stats['temps_moyen_completion'] = $temps_total_completion / $nb_objectifs_avec_temps;
        }
        
        return $stats;
    }
    
    /**
     * Analyse les tendances de progression
     * @param int $particulier_id ID du particulier
     * @param string $periode 'semaine', 'mois', 'trimestre', 'annee'
     * @return array Données de tendance
     */
    public static function analyserTendances(int $particulier_id, string $periode = 'mois'): array {
        // Déterminer l'intervalle selon la période
        $nb_periodes = 6; // Par défaut, 6 périodes (6 mois, 6 semaines, etc.)
        
        switch ($periode) {
            case 'semaine':
                $format_date = 'Y-W';
                $format_label = 'Semaine %V %Y';
                $intervalle = 'week';
                break;
            case 'trimestre':
                $format_date = 'Y-\QQ';
                $format_label = 'T%d %Y';
                $intervalle = '3 months';
                break;
            case 'annee':
                $format_date = 'Y';
                $format_label = '%Y';
                $intervalle = 'year';
                break;
            case 'mois':
            default:
                $format_date = 'Y-m';
                $format_label = '%b %Y';
                $intervalle = 'month';
                break;
        }
        
        // Générer les périodes
        $periodes = [];
        $date_fin = new DateTime();
        $date_debut = clone $date_fin;
        $date_debut->modify("-" . ($nb_periodes - 1) . " $intervalle");
        
        $date_courante = clone $date_debut;
        while ($date_courante <= $date_fin) {
            $key = $date_courante->format($format_date);
            $label = $date_courante->format($format_label);
            
            if ($periode == 'trimestre') {
                $quarter = ceil($date_courante->format('n') / 3);
                $label = sprintf($format_label, $quarter) . ' ' . $date_courante->format('Y');
            }
            
            $periodes[$key] = [
                'label' => $label,
                'nouveaux' => 0,
                'completes' => 0,
                'abandonnes' => 0,
                'actifs' => 0,
                'progression_moyenne' => 0
            ];
            
            $date_courante->modify("+1 $intervalle");
        }
        
        // Récupérer tous les objectifs du particulier
        $objectifs = ElaskaParticulierObjectif::findAllBy([
            'particulier_id' => $particulier_id
        ]);
        
        // Analyser chaque objectif
        foreach ($objectifs as $objectif) {
            // Périodes de création
            $creation_key = $objectif->getDateCreation()->format($format_date);
            if (isset($periodes[$creation_key])) {
                $periodes[$creation_key]['nouveaux']++;
            }
            
            // Périodes de complétion
            if ($objectif->getStatut() == 'complete' && $objectif->getDateCompletion()) {
                $completion_key = $objectif->getDateCompletion()->format($format_date);
                if (isset($periodes[$completion_key])) {
                    $periodes[$completion_key]['completes']++;
                }
            }
            
            // Périodes d'abandon
            if ($objectif->getStatut() == 'abandonne' && $objectif->getDateModification()) {
                $abandon_key = $objectif->getDateModification()->format($format_date);
                if (isset($periodes[$abandon_key])) {
                    $periodes[$abandon_key]['abandonnes']++;
                }
            }
            
            // Calculer les objectifs actifs et progression moyenne pour chaque période
            foreach ($periodes as $key => &$periode_data) {
                // Convertir la clé de période en dates réelles pour comparaison
                $periode_start = DateTime::createFromFormat($format_date, $key);
                $periode_end = clone $periode_start;
                $periode_end->modify("+1 $intervalle");
                $periode_end->modify("-1 second");
                
                if ($objectif->getDateCreation() <= $periode_end) {
                    // L'objectif existait pendant cette période
                    
                    // Vérifier s'il était actif
                    $etait_actif = false;
                    
                    if ($objectif->getStatut() == 'complete' && $objectif->getDateCompletion() > $periode_start) {
                        $etait_actif = true;
                    } elseif ($objectif->getStatut() == 'abandonne' && $objectif->getDateModification() > $periode_start) {
                        $etait_actif = true;
                    } elseif ($objectif->getStatut() != 'complete' && $objectif->getStatut() != 'abandonne') {
                        $etait_actif = true;
                    }
                    
                    if ($etait_actif) {
                        $periode_data['actifs']++;
                        $periode_data['progression_moyenne'] += $objectif->getProgression();
                    }
                }
            }
            unset($periode_data);
        }
        
        // Calculer les moyennes de progression
        foreach ($periodes as &$periode_data) {
            if ($periode_data['actifs'] > 0) {
                $periode_data['progression_moyenne'] /= $periode_data['actifs'];
            }
        }
        
        return array_values($periodes);
    }
    
    /**
     * Calcule le taux de réussite global sur les objectifs
     * @param int $particulier_id ID du particulier
     * @return float Taux de réussite (0-100)
     */
    public static function calculerTauxReussite(int $particulier_id): float {
        // Récupérer les objectifs terminés (complétés ou abandonnés)
        $objectifs_termines = ElaskaParticulierObjectif::findAllBy([
            'particulier_id' => $particulier_id,
            'statut' => ['complete', 'abandonne']
        ]);
        
        if (empty($objectifs_termines)) {
            return 0;
        }
        
        $total = count($objectifs_termines);
        $completes = 0;
        
        foreach ($objectifs_termines as $objectif) {
            if ($objectif->getStatut() == 'complete') {
                $completes++;
            }
        }
        
        return ($completes / $total) * 100;
    }
    
    /**
     * Identifie les catégories d'objectifs sous-performantes
     * @param int $particulier_id ID du particulier
     * @return array Catégories à améliorer
     */
    public static function identifierPointsAmelioration(int $particulier_id): array {
        // Récupérer les objectifs actifs et en pause
        $objectifs_actifs = ElaskaParticulierObjectif::findAllBy([
            'particulier_id' => $particulier_id,
            'statut' => ['actif', 'en_pause']
        ]);
        
        // Récupérer les objectifs terminés des 12 derniers mois
        $date_limite = new DateTime();
        $date_limite->modify('-12 months');
        
        $objectifs_termines = ElaskaParticulierObjectif::findAllBy([
            'particulier_id' => $particulier_id,
            'statut' => ['complete', 'abandonne'],
            'date_modification_after' => $date_limite->format('Y-m-d')
        ]);
        
        $objectifs = array_merge($objectifs_actifs, $objectifs_termines);
        
        if (empty($objectifs)) {
            return [];
        }
        
        // Statistiques par catégorie
        $stats_categories = [];
        
        foreach ($objectifs as $objectif) {
            $categorie = $objectif->getCategorie();
            
            if (!isset($stats_categories[$categorie])) {
                $stats_categories[$categorie] = [
                    'nom' => $categorie,
                    'total' => 0,
                    'completes' => 0,
                    'abandonnes' => 0,
                    'en_retard' => 0,
                    'progression_moyenne' => 0,
                    'score_performance' => 0
                ];
            }
            
            $stats_categories[$categorie]['total']++;
            $stats_categories[$categorie]['progression_moyenne'] += $objectif->getProgression();
            
            if ($objectif->getStatut() == 'complete') {
                $stats_categories[$categorie]['completes']++;
            } elseif ($objectif->getStatut() == 'abandonne') {
                $stats_categories[$categorie]['abandonnes']++;
            } elseif ($objectif->estEnRetard()) {
                $stats_categories[$categorie]['en_retard']++;
            }
        }
        
        // Calculer les scores de performance
        foreach ($stats_categories as &$stat) {
            // Calculer la moyenne de progression
            $stat['progression_moyenne'] /= $stat['total'];
            
            // Calculer le taux de complétion
            $taux_completion = $stat['completes'] / $stat['total'];
            
            // Calculer le taux d'abandon
            $taux_abandon = $stat['abandonnes'] / $stat['total'];
            
            // Calculer le taux d'objectifs en retard
            $taux_retard = $stat['en_retard'] / max(1, $stat['total'] - $stat['completes'] - $stat['abandonnes']);
            
            // Score de performance: 100% est parfait, 0% est mauvais
            $stat['score_performance'] = 
                ($taux_completion * 50) + 
                ((1 - $taux_abandon) * 30) + 
                ((1 - $taux_retard) * 20);
        }
        
        // Trier par score de performance (du pire au meilleur)
        usort($stats_categories, function($a, $b) {
            return $a['score_performance'] <=> $b['score_performance'];
        });
        
        // Retourner les 3 catégories les moins performantes
        return array_slice(array_values($stats_categories), 0, 3);
    }
    
    /**
     * Génère des recommandations basées sur l'analyse des objectifs
     * @param int $particulier_id ID du particulier
     * @return array Recommandations
     */
    public static function genererRecommandations(int $particulier_id): array {
        $recommandations = [];
        
        // Récupérer les statistiques générales
        $stats = self::genererStatistiques($particulier_id);
        
        // Recommandations basées sur le taux de complétion
        if ($stats['taux_completion'] < 30) {
            $recommandations[] = [
                'type' => 'amelioration',
                'titre' => "Améliorer votre taux de réussite d'objectifs",
                'description' => "Votre taux de réussite est actuellement bas. Essayez de définir des objectifs plus petits et plus réalisables.",
                'priorite' => 'haute'
            ];
        }
        
        // Recommandations basées sur le taux d'abandon
        if ($stats['taux_abandon'] > 40) {
            $recommandations[] = [
                'type' => 'alerte',
                'titre' => "Taux d'abandon d'objectifs élevé",
                'description' => "Vous abandonnez beaucoup d'objectifs. Essayez de vous concentrer sur moins d'objectifs à la fois.",
                'priorite' => 'haute'
            ];
        }
        
        // Recommandations basées sur la progression moyenne
        if ($stats['progression_moyenne'] < 20) {
            $recommandations[] = [
                'type' => 'conseil',
                'titre' => "Progression lente sur vos objectifs",
                'description' => "Votre progression globale est lente. Essayez de consacrer du temps régulièrement à vos objectifs, même par petites sessions.",
                'priorite' => 'moyenne'
            ];
        }
        
        // Recommandations basées sur les points d'amélioration
        $points_amelioration = self::identifierPointsAmelioration($particulier_id);
        
        foreach ($points_amelioration as $categorie) {
            if ($categorie['score_performance'] < 40) {
                $recommandations[] = [
                    'type' => 'categorie',
                    'titre' => "Difficulté avec les objectifs de {$categorie['nom']}",
                    'description' => "Vous semblez avoir du mal à progresser dans cette catégorie. Envisagez de revoir vos objectifs ou de demander de l'aide.",
                    'priorite' => 'moyenne',
                    'categorie' => $categorie['nom']
                ];
            }
        }
        
        return $recommandations;
    }
}
