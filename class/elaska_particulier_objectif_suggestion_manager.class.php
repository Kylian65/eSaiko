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

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
dol_include_once('/elaska/class/elaska_particulier_objectif.class.php');
dol_include_once('/elaska/class/elaska_particulier_demarche.class.php');
dol_include_once('/elaska/class/elaska_document.class.php');

/**
 * Gestionnaire de suggestions d'objectifs pour les particuliers
 * 
 * Système intelligent qui propose des objectifs pertinents aux particuliers
 * en fonction de leur situation et de leurs activités administratives
 * 
 * @package    Elaska
 * @subpackage Particuliers
 */
class ElaskaParticulierObjectifSuggestionManager
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
     * @var array     Catégories d'objectifs disponibles
     */
    protected $categories = array(
        'patrimoine' => 'Patrimoine',
        'logement' => 'Logement',
        'famille' => 'Famille',
        'emploi' => 'Emploi et carrière',
        'sante' => 'Santé',
        'retraite' => 'Préparation retraite',
        'fiscalite' => 'Optimisation fiscale',
        'finance' => 'Finance personnelle',
        'mobilite' => 'Mobilité',
        'administratif' => 'Organisation administrative'
    );
    
    /**
     * @var array     Modèles de suggestions prédéfinis
     */
    protected $modeles_suggestions = array();
    
    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->initialiserModelesSuggestions();
    }
    
    /**
     * Initialise les modèles de suggestions
     */
    protected function initialiserModelesSuggestions()
    {
        $this->modeles_suggestions = array(
            // Patrimoine
            array(
                'categorie' => 'patrimoine',
                'titre' => "Acquisition d'un bien immobilier",
                'description' => "Planifier et réaliser l'achat d'un bien immobilier, avec préparation de tous les aspects administratifs et financiers.",
                'duree_estimee' => 12, // mois
                'declencheurs' => array(
                    'documents' => array('bulletin_salaire', 'contrat_travail_cdi'),
                    'demarches' => array(),
                    'age' => array(25, 50),
                    'montant_epargne_min' => 10000
                ),
                'etapes_defaut' => array(
                    "Définir le budget et la capacité d'emprunt",
                    "Rechercher le bien immobilier idéal",
                    "Obtenir un financement bancaire",
                    "Finaliser l'acquisition avec le notaire",
                    "Mettre en place les assurances et garanties",
                    "Organiser le déménagement"
                )
            ),
            array(
                'categorie' => 'patrimoine',
                'titre' => "Optimisation de la gestion locative",
                'description' => "Structurer et optimiser la gestion de vos biens locatifs pour en maximiser la rentabilité.",
                'duree_estimee' => 6, // mois
                'declencheurs' => array(
                    'documents' => array('declaration_revenus_fonciers', 'bail_location'),
                    'demarches' => array(),
                    'age' => array(30, 70),
                    'montant_epargne_min' => 0
                ),
                'etapes_defaut' => array(
                    "Audit des baux et loyers en cours",
                    "Optimisation fiscale des revenus locatifs",
                    "Mise en place d'un système de gestion efficace",
                    "Révision des assurances propriétaire",
                    "Planification des travaux d'amélioration"
                )
            ),
            
            // Logement
            array(
                'categorie' => 'logement',
                'titre' => "Rénovation énergétique du logement",
                'description' => "Améliorer la performance énergétique de votre logement tout en bénéficiant des aides disponibles.",
                'duree_estimee' => 9, // mois
                'declencheurs' => array(
                    'documents' => array('facture_energie', 'taxe_habitation'),
                    'demarches' => array(),
                    'age' => array(30, 70),
                    'montant_epargne_min' => 3000
                ),
                'etapes_defaut' => array(
                    "Réaliser un diagnostic énergétique",
                    "Définir le programme de travaux prioritaires",
                    "Rechercher les dispositifs d'aides applicables",
                    "Sélectionner les artisans qualifiés",
                    "Monter les dossiers d'aides financières",
                    "Suivre et réceptionner les travaux",
                    "Finaliser les dossiers de subventions"
                )
            ),
            
            // Famille
            array(
                'categorie' => 'famille',
                'titre' => "Protection de la famille",
                'description' => "Mettre en place une stratégie complète de protection de votre famille (succession, assurances, etc.).",
                'duree_estimee' => 3, // mois
                'declencheurs' => array(
                    'documents' => array('livret_famille', 'acte_naissance'),
                    'demarches' => array('caf'),
                    'age' => array(30, 60),
                    'montant_epargne_min' => 0
                ),
                'etapes_defaut' => array(
                    "Bilan de la situation familiale",
                    "Optimisation de la couverture prévoyance",
                    "Organisation de la transmission patrimoniale",
                    "Mise à jour des bénéficiaires d'assurance-vie",
                    "Rédaction ou révision du testament",
                    "Mise en place des procurations nécessaires"
                )
            ),
            
            // Emploi
            array(
                'categorie' => 'emploi',
                'titre' => "Reconversion professionnelle",
                'description' => "Planifier et réussir une transition professionnelle vers un nouveau métier.",
                'duree_estimee' => 18, // mois
                'declencheurs' => array(
                    'documents' => array('bulletin_salaire', 'attestation_pole_emploi'),
                    'demarches' => array(),
                    'age' => array(25, 50),
                    'montant_epargne_min' => 5000
                ),
                'etapes_defaut' => array(
                    "Bilan de compétences",
                    "Définition du projet professionnel",
                    "Recherche de formations adaptées",
                    "Montage des dossiers de financement",
                    "Planification de la transition financière",
                    "Actualisation des documents professionnels",
                    "Développement du réseau professionnel cible"
                )
            ),
            
            // Santé
            array(
                'categorie' => 'sante',
                'titre' => "Optimisation couverture santé",
                'description' => "Analyser et optimiser votre couverture santé et prévoyance pour l'adapter à votre situation.",
                'duree_estimee' => 2, // mois
                'declencheurs' => array(
                    'documents' => array('attestation_securite_sociale', 'carte_mutuelle'),
                    'demarches' => array('mdph'),
                    'age' => array(20, 70),
                    'montant_epargne_min' => 0
                ),
                'etapes_defaut' => array(
                    "Analyse des besoins médicaux spécifiques",
                    "Comparatif des offres de complémentaire santé",
                    "Révision des garanties prévoyance",
                    "Optimisation fiscale des contrats",
                    "Mise en place des nouvelles couvertures"
                )
            ),
            
            // Retraite
            array(
                'categorie' => 'retraite',
                'titre' => "Préparation à la retraite",
                'description' => "Anticiper et préparer votre passage à la retraite sur tous les plans (administratif, financier).",
                'duree_estimee' => 24, // mois
                'declencheurs' => array(
                    'documents' => array('releve_carriere'),
                    'demarches' => array('retraite'),
                    'age' => array(55, 65),
                    'montant_epargne_min' => 0
                ),
                'etapes_defaut' => array(
                    "Audit complet des droits à la retraite",
                    "Stratégie de date de départ optimale",
                    "Consolidation de l'épargne retraite",
                    "Organisation patrimoniale pré-retraite",
                    "Préparation des dossiers pour tous les régimes",
                    "Planification des revenus complémentaires",
                    "Transition fiscale liée au changement de statut"
                )
            ),
            
            // Fiscalité
            array(
                'categorie' => 'fiscalite',
                'titre' => "Optimisation fiscale globale",
                'description' => "Mettre en place une stratégie d'optimisation fiscale adaptée à votre situation patrimoniale.",
                'duree_estimee' => 6, // mois
                'declencheurs' => array(
                    'documents' => array('avis_imposition'),
                    'demarches' => array('fiscal'),
                    'age' => array(35, 70),
                    'montant_epargne_min' => 20000
                ),
                'etapes_defaut' => array(
                    "Analyse complète de la situation fiscale",
                    "Identification des leviers d'optimisation",
                    "Structuration du patrimoine",
                    "Mise en place des investissements défiscalisants",
                    "Réorganisation des revenus",
                    "Planification des déclarations"
                )
            ),
            
            // Finance personnelle
            array(
                'categorie' => 'finance',
                'titre' => "Constitution d'un patrimoine financier",
                'description' => "Construire méthodiquement un patrimoine financier diversifié et performant.",
                'duree_estimee' => 36, // mois
                'declencheurs' => array(
                    'documents' => array('releve_bancaire', 'bulletin_salaire'),
                    'demarches' => array(),
                    'age' => array(25, 60),
                    'montant_epargne_min' => 5000
                ),
                'etapes_defaut' => array(
                    "Définition des objectifs financiers",
                    "Mise en place d'une épargne régulière",
                    "Création d'une réserve de sécurité",
                    "Diversification des placements",
                    "Optimisation de la fiscalité des revenus",
                    "Suivi et réajustement de la stratégie"
                )
            ),
            
            // Mobilité
            array(
                'categorie' => 'mobilite',
                'titre' => "Préparation à l'expatriation",
                'description' => "Organiser votre installation à l'étranger en anticipant toutes les démarches administratives.",
                'duree_estimee' => 8, // mois
                'declencheurs' => array(
                    'documents' => array('passeport', 'contrat_travail'),
                    'demarches' => array('ants_identite'),
                    'age' => array(25, 55),
                    'montant_epargne_min' => 10000
                ),
                'etapes_defaut' => array(
                    "Organisation des documents d'identité internationaux",
                    "Gestion de la résidence fiscale",
                    "Adaptation de la couverture sociale et santé",
                    "Organisation bancaire internationale",
                    "Protection du patrimoine laissé en France",
                    "Formalités administratives du pays d'accueil"
                )
            ),
            
            // Organisation administrative
            array(
                'categorie' => 'administratif',
                'titre' => "Transformation numérique administrative",
                'description' => "Digitaliser et organiser efficacement toute votre documentation administrative.",
                'duree_estimee' => 3, // mois
                'declencheurs' => array(
                    'documents' => array(),
                    'demarches' => array(),
                    'age' => array(20, 80),
                    'montant_epargne_min' => 0
                ),
                'etapes_defaut' => array(
                    "Audit de l'organisation actuelle",
                    "Mise en place d'un système de classement numérique",
                    "Numérisation des documents essentiels",
                    "Sécurisation des données sensibles",
                    "Configuration des accès et partages",
                    "Formation à la maintenance du système"
                )
            )
        );
    }
    
    /**
     * Génère des suggestions d'objectifs pour un particulier
     * 
     * @param int     $particulier_id     ID du particulier
     * @param array   $options            Options supplémentaires
     * @return array                      Liste des suggestions d'objectifs
     */
    public function genererSuggestions($particulier_id, $options = array())
    {
        global $conf, $langs;
        
        // Vérifier les paramètres
        if (empty($particulier_id)) {
            $this->error = $langs->trans('MissingParameter', 'particulier_id');
            return array();
        }
        
        // Options par défaut
        $options = array_merge(array(
            'max_suggestions' => 5,
            'categories' => array_keys($this->categories),
            'exclure_existants' => true
        ), $options);
        
        try {
            // Récupérer les données du particulier
            $particulier = $this->getInfosParticulier($particulier_id);
            
            // Récupérer les documents du particulier
            $documents = $this->getDocumentsParticulier($particulier_id);
            
            // Récupérer les démarches du particulier
            $demarches = $this->getDemarchesParticulier($particulier_id);
            
            // Récupérer les objectifs existants pour exclure des suggestions similaires
            $objectifs_existants = array();
            if ($options['exclure_existants']) {
                $objectifs_existants = $this->getObjectifsExistants($particulier_id);
            }
            
            // Calculer les scores pour chaque modèle de suggestion
            $suggestions = $this->calculerScoresSuggestions(
                $particulier,
                $documents,
                $demarches,
                $objectifs_existants,
                $options
            );
            
            // Limiter le nombre de suggestions
            $suggestions = array_slice($suggestions, 0, $options['max_suggestions']);
            
            return $suggestions;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            dol_syslog(__METHOD__ . ' ' . $this->error, LOG_ERR);
            return array();
        }
    }
    
    /**
     * Récupère les informations d'un particulier
     * 
     * @param int     $particulier_id     ID du particulier
     * @return array                      Informations du particulier
     */
    protected function getInfosParticulier($particulier_id)
    {
        global $langs;
        
        // Récupérer le particulier
        require_once DOL_DOCUMENT_ROOT.'/elaska/class/elaska_particulier.class.php';
        $particulier_obj = new ElaskaParticulier($this->db);
        $result = $particulier_obj->fetch($particulier_id);
        
        if ($result <= 0) {
            throw new Exception($langs->trans('RecordNotFound'));
        }
        
        // Calculer l'âge
        $age = 30; // Par défaut
        if (!empty($particulier_obj->date_naissance)) {
            $datenaiss = dol_stringtotime($particulier_obj->date_naissance);
            if ($datenaiss) {
                $age = date('Y') - date('Y', $datenaiss);
                // Ajustement si l'anniversaire n'est pas encore passé cette année
                if (date('md') < date('md', $datenaiss)) {
                    $age--;
                }
            }
        }
        
        // Récupérer le montant d'épargne (à adapter selon votre modèle de données)
        $montant_epargne = 0;
        // TODO: Récupérer le montant d'épargne depuis votre structure de données
        
        return array(
            'id' => $particulier_id,
            'nom' => $particulier_obj->nom,
            'prenom' => $particulier_obj->prenom,
            'age' => $age,
            'montant_epargne' => $montant_epargne,
            'situation_familiale' => $particulier_obj->situation_familiale,
            'nombre_enfants' => $particulier_obj->nombre_enfants ?? 0,
            'profession' => $particulier_obj->profession,
            'code_postal' => $particulier_obj->code_postal
        );
    }
    
    /**
     * Récupère les documents d'un particulier
     * 
     * @param int     $particulier_id     ID du particulier
     * @return array                      Liste des documents (types uniquement)
     */
    protected function getDocumentsParticulier($particulier_id)
    {
        $document = new ElaskaDocument($this->db);
        $documents = $document->fetchAll('', '', 0, 0, array('customsql' => 'fk_particulier = '.$particulier_id));
        
        $types = array();
        if (is_array($documents) && !empty($documents)) {
            foreach ($documents as $doc) {
                $types[] = $doc->type;
            }
        }
        
        return array_unique($types);
    }
    
    /**
     * Récupère les démarches d'un particulier
     * 
     * @param int     $particulier_id     ID du particulier
     * @return array                      Liste des démarches (types uniquement)
     */
    protected function getDemarchesParticulier($particulier_id)
    {
        $demarche = new ElaskaParticulierDemarche($this->db);
        $demarches = $demarche->fetchAll('', '', 0, 0, array('customsql' => 'fk_particulier = '.$particulier_id));
        
        $types = array();
        if (is_array($demarches) && !empty($demarches)) {
            foreach ($demarches as $dem) {
                $types[] = $dem->type;
            }
        }
        
        return array_unique($types);
    }
    
    /**
     * Récupère les objectifs existants d'un particulier
     * 
     * @param int     $particulier_id     ID du particulier
     * @return array                      Liste des objectifs existants
     */
    protected function getObjectifsExistants($particulier_id)
    {
        $objectif = new ElaskaParticulierObjectif($this->db);
        $objectifs = $objectif->fetchAll('', '', 0, 0, array('customsql' => 'fk_particulier = '.$particulier_id));
        
        $result = array();
        if (is_array($objectifs) && !empty($objectifs)) {
            foreach ($objectifs as $obj) {
                $result[] = array(
                    'id' => $obj->id,
                    'titre' => $obj->titre,
                    'categorie' => $obj->categorie
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Calcule les scores pour chaque suggestion
     * 
     * @param array   $particulier        Informations du particulier
     * @param array   $documents          Types de documents du particulier
     * @param array   $demarches          Types de démarches du particulier
     * @param array   $objectifs_existants Objectifs existants
     * @param array   $options            Options
     * @return array                      Suggestions triées par pertinence
     */
    protected function calculerScoresSuggestions($particulier, $documents, $demarches, $objectifs_existants, $options)
    {
        $suggestions = array();
        
        foreach ($this->modeles_suggestions as $modele) {
            // Filtrer par catégories demandées
            if (!in_array($modele['categorie'], $options['categories'])) {
                continue;
            }
            
            // Vérifier si un objectif similaire existe déjà
            $similaire = false;
            foreach ($objectifs_existants as $existant) {
                if (strtolower($existant['titre']) == strtolower($modele['titre']) ||
                    (isset($existant['categorie']) && $existant['categorie'] == $modele['categorie'] && 
                     similar_text(strtolower($existant['titre']), strtolower($modele['titre'])) > strlen($modele['titre']) * 0.7)) {
                    $similaire = true;
                    break;
                }
            }
            
            if ($similaire && $options['exclure_existants']) {
                continue;
            }
            
            // Calculer le score de pertinence
            $score = 0;
            
            // Score basé sur les documents
            if (!empty($modele['declencheurs']['documents'])) {
                foreach ($modele['declencheurs']['documents'] as $type_doc) {
                    if (in_array($type_doc, $documents)) {
                        $score += 20;
                    }
                }
            }
            
            // Score basé sur les démarches
            if (!empty($modele['declencheurs']['demarches'])) {
                foreach ($modele['declencheurs']['demarches'] as $type_dem) {
                    if (in_array($type_dem, $demarches)) {
                        $score += 30;
                    }
                }
            }
            
            // Score basé sur l'âge
            if (!empty($modele['declencheurs']['age']) && count($modele['declencheurs']['age']) == 2) {
                $age_min = $modele['declencheurs']['age'][0];
                $age_max = $modele['declencheurs']['age'][1];
                
                if ($particulier['age'] >= $age_min && $particulier['age'] <= $age_max) {
                    // Score maximal au milieu de la plage d'âge
                    $milieu = ($age_min + $age_max) / 2;
                    $distance = abs($particulier['age'] - $milieu);
                    $plage = ($age_max - $age_min) / 2;
                    $ratio = 1 - ($distance / $plage);
                    $score += 25 * max(0, $ratio);
                }
            }
            
            // Score basé sur le montant d'épargne
            if (!empty($modele['declencheurs']['montant_epargne_min'])) {
                if ($particulier['montant_epargne'] >= $modele['declencheurs']['montant_epargne_min']) {
                    $score += 15;
                }
            }
            
            // Ajouter à la liste des suggestions avec son score
            $suggestions[] = array_merge($modele, array(
                'score' => $score,
                'est_similaire' => $similaire
            ));
        }
        
        // Trier par score
        usort($suggestions, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        return $suggestions;
    }
    
    /**
     * Crée un objectif à partir d'une suggestion
     * 
     * @param int     $particulier_id     ID du particulier
     * @param array   $suggestion         Données de la suggestion
     * @param array   $donnees_additionnelles Données supplémentaires pour l'objectif
     * @return int                        ID de l'objectif créé ou < 0 si erreur
     */
    public function creerObjectifDepuisSuggestion($particulier_id, $suggestion, $donnees_additionnelles = array())
    {
        global $user, $langs;
        
        if (empty($particulier_id) || empty($suggestion)) {
            $this->error = $langs->trans('MissingParameter');
            return -1;
        }
        
        // Créer l'objectif
        $objectif = new ElaskaParticulierObjectif($this->db);
        $objectif->fk_particulier = $particulier_id;
        $objectif->titre = $suggestion['titre'];
        $objectif->description = $suggestion['description'];
        $objectif->categorie = $suggestion['categorie'];
        $objectif->priorite = isset($donnees_additionnelles['priorite']) ? $donnees_additionnelles['priorite'] : 2; // Priorité moyenne par défaut
        $objectif->statut = 'planification';
        $objectif->progression = 0;
        
        // Date d'échéance : par défaut la date actuelle + durée estimée
        $date_echeance = dol_time_plus_duree(dol_now(), $suggestion['duree_estimee'], 'm');
        if (!empty($donnees_additionnelles['date_echeance'])) {
            $date_echeance = $donnees_additionnelles['date_echeance'];
        }
        $objectif->date_echeance = $date_echeance;
        
        // Autres champs
        if (!empty($donnees_additionnelles['budget_prevu'])) {
            $objectif->budget_prevu = $donnees_additionnelles['budget_prevu'];
        }
        
        // Créer l'objectif
        $result = $objectif->create($user);
        if ($result <= 0) {
            $this->error = $objectif->error;
            $this->errors = $objectif->errors;
            return $result;
        }
        
        // Créer les étapes si elles sont définies dans la suggestion
        if (!empty($suggestion['etapes_defaut'])) {
            $this->creerEtapesObjectif($objectif->id, $suggestion['etapes_defaut']);
        }
        
        return $objectif->id;
    }
    
    /**
     * Crée les étapes pour un objectif
     * 
     * @param int     $objectif_id        ID de l'objectif
     * @param array   $etapes             Liste des titres d'étapes
     * @return bool                       True si succès, false sinon
     */
    protected function creerEtapesObjectif($objectif_id, $etapes)
    {
        global $user;
        
        if (empty($objectif_id) || empty($etapes)) {
            return false;
        }
        
        dol_include_once('/elaska/class/elaska_particulier_objectif_etape.class.php');
        
        $ordre = 1;
        foreach ($etapes as $titre_etape) {
            $etape = new ElaskaParticulierObjectifEtape($this->db);
            $etape->fk_objectif = $objectif_id;
            $etape->titre = $titre_etape;
            $etape->ordre = $ordre++;
            $etape->statut = 'a_faire';
            $etape->progression = 0;
            
            $result = $etape->create($user);
            if ($result <= 0) {
                dol_syslog("Échec de la création d'une étape pour l'objectif #$objectif_id: " . $etape->error, LOG_ERR);
                // Continuer malgré l'erreur
            }
        }
        
        return true;
    }
    
    /**
     * Retourne les catégories d'objectifs disponibles
     * 
     * @return array Liste des catégories (code => libellé)
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
    /**
     * Modifie les pondérations de score pour personnaliser les suggestions
     * 
     * @param array $ponderation Nouvelles pondérations
     * @return bool True si succès
     */
    public function setConfigurationPonderation($ponderation)
    {
        global $user, $conf;
        
        // Vérification des données
        if (!is_array($ponderation)) {
            $this->error = "Format de pondération invalide";
            return false;
        }
        
        // Convertir en JSON
        $json = json_encode($ponderation);
        
        // Enregistrer dans la configuration
        require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
        return dolibarr_set_const($this->db, 'ELASKA_SUGGESTION_OBJECTIF_PONDERATION', $json, 'chaine', 0, '', $conf->entity);
    }
}
