<?php
/**
 * Gestionnaire des suggestions d'objectifs pour particuliers
 * 
 * @package Elaska
 * @subpackage Particuliers
 * @author Elaska Dev Team
 * @version 4.5.0
 */
class ElaskaParticulierObjectifSuggestionManager {
    /**
     * Types d'événements de vie supportés
     */
    const EVENEMENTS_VIE = [
        'naissance',
        'deces',
        'mariage',
        'divorce',
        'demenagement',
        'retraite',
        'emploi_nouveau',
        'emploi_perte',
        'maladie_grave',
        'achat_immobilier',
        'creation_entreprise'
    ];
    
    /**
     * Générer des suggestions d'objectifs pour un particulier
     * @param int $particulier_id ID du particulier
     * @return array Liste des suggestions générées
     */
    public static function genererSuggestions(int $particulier_id): array {
        ElaskaLog::info("Génération de suggestions d'objectifs pour particulier #$particulier_id");
        
        // Vérifier que le particulier existe
        $particulier = ElaskaParticulier::findById($particulier_id);
        if (!$particulier) {
            ElaskaLog::error("Particulier #$particulier_id non trouvé lors de la génération de suggestions");
            return [];
        }
        
        $suggestions = [];
        
        // Analyser les démarches récentes
        $suggestions_demarches = self::analyserDemarchesRecentes($particulier_id);
        $suggestions = array_merge($suggestions, $suggestions_demarches);
        
        // Identifier les événements de vie
        $suggestions_evenements = self::identifierEvenementsDeVie($particulier_id);
        $suggestions = array_merge($suggestions, $suggestions_evenements);
        
        // Analyser le profil (âge, situation familiale, etc.)
        $suggestions_profil = self::analyserProfil($particulier_id);
        $suggestions = array_merge($suggestions, $suggestions_profil);
        
        // Filtrer les suggestions
        $suggestions = self::filtrerSuggestions($suggestions);
        
        // Sauvegarder les suggestions filtrées
        $suggestions_sauvegardees = [];
        foreach ($suggestions as $suggestion_data) {
            $suggestion = new ElaskaParticulierObjectifSuggestion();
            $suggestion->setParticulierId($particulier_id);
            $suggestion->setTitre($suggestion_data['titre']);
            $suggestion->setDescription($suggestion_data['description']);
            $suggestion->setCategorie($suggestion_data['categorie']);
            $suggestion->setPriorite($suggestion_data['priorite']);
            $suggestion->setMetaDonnees($suggestion_data['meta_donnees'] ?? []);
            
            if ($suggestion->save()) {
                $suggestions_sauvegardees[] = $suggestion;
            }
        }
        
        ElaskaLog::info("Génération terminée: " . count($suggestions_sauvegardees) . " suggestions créées");
        
        return $suggestions_sauvegardees;
    }
    
    /**
     * Analyser les démarches administratives récentes pour suggérer des objectifs
     * @param int $particulier_id ID du particulier
     * @return array Suggestions basées sur les démarches
     */
    public static function analyserDemarchesRecentes(int $particulier_id): array {
        $suggestions = [];
        
        // Récupérer les démarches des 3 derniers mois
        $date_limite = new DateTime();
        $date_limite->modify('-3 months');
        
        $demarches = ElaskaParticulierDemarche::findAllBy([
            'particulier_id' => $particulier_id,
            'date_creation_after' => $date_limite->format('Y-m-d'),
            'limit' => 10
        ]);
        
        foreach ($demarches as $demarche) {
            switch ($demarche->getType()) {
                case 'changement_adresse':
                    $suggestions[] = [
                        'titre' => "Optimiser mon aménagement au nouvel emplacement",
                        'description' => "Suite à votre changement d'adresse, créez un plan d'action pour tirer le meilleur parti de votre nouvel environnement.",
                        'categorie' => 'logement',
                        'priorite' => 4,
                        'meta_donnees' => [
                            'source' => 'demarche',
                            'demarche_id' => $demarche->getId(),
                            'source_confiance' => 0.8
                        ]
                    ];
                    break;
                
                case 'declaration_fiscale':
                    $suggestions[] = [
                        'titre' => "Plan d'optimisation fiscale pour l'année prochaine",
                        'description' => "Basé sur votre dernière déclaration fiscale, établissez un plan pour optimiser votre situation fiscale durant l'année à venir.",
                        'categorie' => 'finance',
                        'priorite' => 4,
                        'meta_donnees' => [
                            'source' => 'demarche',
                            'demarche_id' => $demarche->getId(),
                            'source_confiance' => 0.9
                        ]
                    ];
                    break;
                
                case 'demande_allocation':
                    $suggestions[] = [
                        'titre' => "Faire un suivi régulier des aides auxquelles j'ai droit",
                        'description' => "Suite à votre demande d'allocation, mettez en place un système de veille pour vous assurer de bénéficier de toutes les aides auxquelles vous avez droit.",
                        'categorie' => 'finances',
                        'priorite' => 3,
                        'meta_donnees' => [
                            'source' => 'demarche',
                            'demarche_id' => $demarche->getId(),
                            'source_confiance' => 0.7
                        ]
                    ];
                    break;
                
                // Autres cas selon les types de démarches
            }
        }
        
        return $suggestions;
    }
    
    /**
     * Identifie les événements de vie pour suggérer des objectifs associés
     * @param int $particulier_id ID du particulier
     * @return array Suggestions basées sur les événements de vie
     */
    public static function identifierEvenementsDeVie(int $particulier_id): array {
        $suggestions = [];
        
        // Récupérer les événements de vie récents (6 derniers mois)
        $date_limite = new DateTime();
        $date_limite->modify('-6 months');
        
        $evenements = ElaskaParticulierEvenementVie::findAllBy([
            'particulier_id' => $particulier_id,
            'date_after' => $date_limite->format('Y-m-d')
        ]);
        
        foreach ($evenements as $evenement) {
            switch ($evenement->getType()) {
                case 'naissance':
                    $suggestions[] = [
                        'titre' => "Planifier l'éducation financière de mon enfant",
                        'description' => "Suite à cette naissance, établissez un plan d'épargne et d'éducation financière pour assurer l'avenir de votre enfant.",
                        'categorie' => 'famille',
                        'priorite' => 5,
                        'meta_donnees' => [
                            'source' => 'evenement_vie',
                            'evenement_id' => $evenement->getId(),
                            'source_confiance' => 0.9
                        ]
                    ];
                    break;
                
                case 'demenagement':
                    $suggestions[] = [
                        'titre' => "Créer mon réseau social dans mon nouveau quartier",
                        'description' => "Suite à votre déménagement, établissez un plan pour vous intégrer et créer un réseau social dans votre nouveau quartier.",
                        'categorie' => 'social',
                        'priorite' => 3,
                        'meta_donnees' => [
                            'source' => 'evenement_vie',
                            'evenement_id' => $evenement->getId(),
                            'source_confiance' => 0.7
                        ]
                    ];
                    break;
                
                case 'emploi_nouveau':
                    $suggestions[] = [
                        'titre' => "Plan de développement de carrière sur 3 ans",
                        'description' => "Suite à votre nouvel emploi, établissez un plan de développement professionnel pour les 3 prochaines années.",
                        'categorie' => 'carriere',
                        'priorite' => 4,
                        'meta_donnees' => [
                            'source' => 'evenement_vie',
                            'evenement_id' => $evenement->getId(),
                            'source_confiance' => 0.8
                        ]
                    ];
                    break;
                
                // Autres types d'événements
            }
        }
        
        return $suggestions;
    }
    
    /**
     * Analyse le profil du particulier pour générer des suggestions
     * @param int $particulier_id ID du particulier
     * @return array Suggestions basées sur le profil
     */
    private static function analyserProfil(int $particulier_id): array {
        $suggestions = [];
        $particulier = ElaskaParticulier::findById($particulier_id);
        
        if (!$particulier) {
            return [];
        }
        
        // Suggestions basées sur l'âge
        $age = $particulier->getAge();
        
        if ($age >= 25 && $age <= 35) {
            $suggestions[] = [
                'titre' => "Établir un plan d'épargne pour l'achat immobilier",
                'description' => "À votre âge, c'est le moment idéal pour commencer à épargner en vue d'un achat immobilier.",
                'categorie' => 'finance',
                'priorite' => 4,
                'meta_donnees' => [
                    'source' => 'profil_age',
                    'source_confiance' => 0.7,
                    'age' => $age
                ]
            ];
        }
        
        if ($age >= 45 && $age <= 55) {
            $suggestions[] = [
                'titre' => "Revoir ma stratégie de préparation à la retraite",
                'description' => "Il est temps de faire un bilan et d'optimiser votre plan de retraite pour les années à venir.",
                'categorie' => 'finance',
                'priorite' => 4,
                'meta_donnees' => [
                    'source' => 'profil_age',
                    'source_confiance' => 0.8,
                    'age' => $age
                ]
            ];
        }
        
        // Suggestions basées sur la situation familiale
        $situation = $particulier->getSituationFamiliale();
        
        if ($situation == 'en_couple' && $particulier->getNombreEnfants() == 0) {
            $suggestions[] = [
                'titre' => "Planifier notre avenir familial",
                'description' => "Réfléchissez ensemble à vos projets familiaux et établissez un plan pour les réaliser.",
                'categorie' => 'famille',
                'priorite' => 3,
                'meta_donnees' => [
                    'source' => 'profil_situation',
                    'source_confiance' => 0.6,
                    'situation' => $situation
                ]
            ];
        }
        
        return $suggestions;
    }
    
    /**
     * Filtre les suggestions pour éviter les doublons ou les suggestions non pertinentes
     * @param array $suggestions Liste des suggestions brutes
     * @return array Liste des suggestions filtrées et priorisées
     */
    public static function filtrerSuggestions(array $suggestions): array {
        if (empty($suggestions)) {
            return [];
        }
        
        // Éliminer les doublons potentiels (basés sur le titre)
        $titres_uniques = [];
        $suggestions_filtrees = [];
        
        foreach ($suggestions as $suggestion) {
            $titre_normalise = ElaskaTextUtils::normalizeText($suggestion['titre']);
            
            if (!isset($titres_uniques[$titre_normalise])) {
                $titres_uniques[$titre_normalise] = true;
                $suggestions_filtrees[] = $suggestion;
            }
        }
        
        // Trier par priorité
        usort($suggestions_filtrees, function($a, $b) {
            return $b['priorite'] <=> $a['priorite'];
        });
        
        // Limiter à 5 suggestions maximum
        return array_slice($suggestions_filtrees, 0, 5);
    }
    
    /**
     * Récupère les suggestions non traitées pour un particulier
     * @param int $particulier_id ID du particulier
     * @return array Liste des suggestions actives
     */
    public static function getSuggestionsActives(int $particulier_id): array {
        return ElaskaParticulierObjectifSuggestion::findAllBy([
            'particulier_id' => $particulier_id,
            'est_accepte' => false,
            'est_rejete' => false,
            'order_by' => 'priorite DESC'
        ]);
    }
    
    /**
     * Génère et envoie une notification pour les nouvelles suggestions
     * @param int $particulier_id ID du particulier
     * @param array $suggestions Liste des suggestions générées
     * @return bool Succès de l'opération
     */
    public static function notifierNouvellesSuggestions(int $particulier_id, array $suggestions): bool {
        if (empty($suggestions)) {
            return false;
        }
        
        $notification = new ElaskaNotification();
        $notification->setType('objectif_suggestions');
        $notification->setDestinataire($particulier_id);
        
        $count = count($suggestions);
        $notification->setTitre("$count nouvelle(s) suggestion(s) d'objectifs");
        
        $contenu = "Nous avons généré $count suggestion(s) d'objectifs personnalisés pour vous :";
        foreach ($suggestions as $i => $suggestion) {
            if ($i < 3) { // Limiter à 3 suggestions dans la notification
                $contenu .= "\n- " . $suggestion->getTitre();
            }
        }
        
        if ($count > 3) {
            $contenu .= "\n... et " . ($count - 3) . " autre(s).";
        }
        
        $contenu .= "\n\nConsultez-les dans votre espace personnel !";
        
        $notification->setContenu($contenu);
        $notification->setLien("/particulier/objectifs/suggestions");
        $notification->setPriorite(ElaskaNotification::PRIORITE_NORMALE);
        
        return $notification->save();
    }
}
