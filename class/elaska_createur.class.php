<?php
/**
 * eLaska - Classe pour gérer les données spécifiques aux créateurs/porteurs de projets
 * Date: 2025-05-31
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaCreateur', false)) {

class ElaskaCreateur extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_createur';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_createur';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'user-cog@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var int ID du tiers eLaska parent
     */
    public $fk_elaska_tiers;
    
    //
    // PROFIL ENTREPRENEURIAL
    //
    
    /**
     * @var string Code du type de projet (dictionnaire)
     */
    public $type_projet_code;
    
    /**
     * @var string Code du stade d'avancement du projet (dictionnaire)
     */
    public $stade_projet_code;
    
    /**
     * @var string Date de début d'accompagnement par eLaska (format YYYY-MM-DD)
     */
    public $date_debut_accompagnement;
    
    /**
     * @var string Motivations pour se lancer
     */
    public $motivation_entrepreneuriat_text;
    
    /**
     * @var string Code de l'expérience entrepreneuriale antérieure (dictionnaire)
     */
    public $experience_entrepreneuriale_anterieure_code;
    
    /**
     * @var string Détails si expérience antérieure
     */
    public $description_experience_entrepreneuriale;
    
    /**
     * @var int Formation spécifique à la création d'entreprise suivie (0=non, 1=oui)
     */
    public $formation_specifique_creation_entreprise_suivie;
    
    /**
     * @var string Nom de la formation création suivie
     */
    public $nom_formation_creation_suivie;
    
    /**
     * @var string Réseau personnel/professionnel mobilisable
     */
    public $reseau_personnel_professionnel_mobilisable_text;
    
    /**
     * @var string Code de la situation financière personnelle (dictionnaire)
     */
    public $situation_financiere_personnelle_code;
    
    /**
     * @var int Temps disponible pour le projet en heures/semaine
     */
    public $temps_disponible_projet_semaine_heures;
    
    /**
     * @var string Contraintes personnelles pour le projet
     */
    public $contraintes_personnelles_projet_text;
    
    /**
     * @var string Principales motivations pour la création
     */
    public $principales_motivations_creation_text;
    
    /**
     * @var string Principales craintes liées à la création
     */
    public $principales_craintes_creation_text;
    
    /**
     * @var string Code du statut actuel du créateur (dictionnaire)
     */
    public $statut_actuel_createur_code;
    
    //
    // PROJET ENTREPRENEURIAL
    //
    
    /**
     * @var string Nom du projet ou future entité
     */
    public $nom_projet;
    
    /**
     * @var string Code du secteur d'activité du projet (dictionnaire)
     */
    public $secteur_activite_projet_code;
    
    /**
     * @var string Description du concept/idée de projet
     */
    public $description_concept_projet;
    
    /**
     * @var string Code du type d'innovation/originalité du projet (dictionnaire)
     */
    public $originalite_innovation_projet_code;
    
    /**
     * @var string Description de la clientèle cible
     */
    public $clientele_cible_text;
    
    /**
     * @var string Besoin du marché identifié
     */
    public $besoin_marche_identifie_text;
    
    /**
     * @var string Proposition de valeur/différenciation
     */
    public $proposition_valeur_text;
    
    /**
     * @var int ID du document d'étude de marché (lien vers ElaskaDocument)
     */
    public $etude_marche_document_id;
    
    /**
     * @var string Description du business model
     */
    public $business_model_text;
    
    /**
     * @var string Principaux concurrents identifiés
     */
    public $concurrents_identifies_text;
    
    /**
     * @var string Partenaires clés identifiés
     */
    public $partenaires_cles_text;
    
    //
    // STRUCTURE ET JURIDIQUE
    //
    
    /**
     * @var string Code de la forme juridique envisagée (dictionnaire)
     */
    public $forme_juridique_envisagee_code;
    
    /**
     * @var string Code de l'avancement de la rédaction des statuts (dictionnaire)
     */
    public $statuts_redaction_avancement_code;
    
    /**
     * @var string Code de l'avancement des démarches administratives (dictionnaire)
     */
    public $demarches_administratives_avancement_code;
    
    /**
     * @var string Date d'immatriculation prévue (format YYYY-MM-DD)
     */
    public $date_immatriculation_prevue;
    
    /**
     * @var string Lieu d'implantation prévu
     */
    public $lieu_implantation_prevu;
    
    /**
     * @var int Besoin de locaux immédiat (0=non, 1=oui)
     */
    public $besoin_locaux_immediat;
    
    /**
     * @var string Code du type de locaux recherchés (dictionnaire)
     */
    public $type_locaux_recherches_code;
    
    /**
     * @var string Code de l'avancement de la recherche de locaux (dictionnaire)
     */
    public $recherche_locaux_avancement_code;
    
    //
    // FINANCEMENT DU PROJET
    //
    
    /**
     * @var float Budget global estimé du projet
     */
    public $budget_global_projet;
    
    /**
     * @var float Montant de l'apport personnel
     */
    public $apport_personnel_montant;
    
    /**
     * @var string Codes des types de financement recherchés (dictionnaire, peut être multiple)
     */
    public $types_financement_recherches_code;
    
    /**
     * @var string Détails sur financements complémentaires recherchés
     */
    public $financement_complementaire_recherche_text;
    
    /**
     * @var float Montant prêt d'honneur (si applicable)
     */
    public $pret_honneur_montant;
    
    /**
     * @var string Subventions identifiées
     */
    public $subventions_identifiees_text;
    
    /**
     * @var float Montant estimé des aides publiques
     */
    public $montant_aides_publiques_estime;
    
    /**
     * @var string Aides à la création envisagées
     */
    public $aides_creation_envisagees_text;
    
    /**
     * @var string Banques approchées/retenues
     */
    public $banques_approchees_text;
    
    /**
     * @var string Garanties proposées
     */
    public $garanties_proposees_text;
    
    /**
     * @var int ID du document business plan (lien vers ElaskaDocument)
     */
    public $business_plan_document_id;
    
    /**
     * @var int ID du document prévisionnel financier (lien vers ElaskaDocument)
     */
    public $previsionnel_financier_document_id;
    
    /**
     * @var int ID du document plan de financement détaillé (lien vers ElaskaDocument)
     */
    public $plan_financement_detaille_document_id;
    
    //
    // ACCOMPAGNEMENT
    //
    
    /**
     * @var string Date de début du projet (idée initiale) (format YYYY-MM-DD)
     */
    public $date_debut_projet_initial;
    
    /**
     * @var string Besoins d'accompagnement identifiés
     */
    public $besoins_accompagnement_specifiques_createur_text;
    
    /**
     * @var string Codes des besoins d'accompagnement spécifiques au projet (dictionnaire, peut être multiple)
     */
    public $besoin_accompagnement_specifique_projet_code;
    
    /**
     * @var string Autres organismes accompagnateurs
     */
    public $organismes_accompagnement_actuels;
    
    /**
     * @var string Nom de l'organisme accompagnateur principal
     */
    public $nom_organisme_accompagnateur_principal;
    
    /**
     * @var int Accompagnement par un organisme externe (0=non, 1=oui)
     */
    public $accompagnement_par_organisme_externe;
    
    /**
     * @var string Formations entrepreneuriales suivies
     */
    public $formations_suivies_text;
    
    /**
     * @var int ID du référent eLaska
     */
    public $fk_user_referent_creation;
    
    /**
     * @var string Prochaines étapes à court terme
     */
    public $prochaines_etapes_court_terme_text;
    
    /**
     * @var string Date de la prochaine étape d'accompagnement (format YYYY-MM-DD)
     */
    public $date_prochaine_etape_accompagnement;
    
    /**
     * @var int ID du document échéancier de création (lien vers ElaskaDocument)
     */
    public $echeancier_creation_document_id;
    
    /**
     * @var string Code du statut d'accompagnement par eLaska (dictionnaire)
     */
    public $statut_accompagnement_elaska_code;
    
    //
    // RISQUES ET ÉVALUATION
    //
    
    /**
     * @var string Freins/obstacles identifiés au projet
     */
    public $freins_identifies_text;
    
    /**
     * @var string Atouts principaux du projet
     */
    public $atouts_projet_text;
    
    /**
     * @var string Risques principaux identifiés
     */
    public $risques_principaux_identifies_text;
    
    /**
     * @var string Code de l'évaluation de viabilité du projet (dictionnaire)
     */
    public $evaluation_viabilite_projet_code;
    
    /**
     * @var string Code de la probabilité de réussite estimée (dictionnaire)
     */
    public $probabilite_reussite_estimee_code;
    
    /**
     * @var string Aléas anticipés et solutions envisagées
     */
    public $aleas_anticipes_text;
    
    /**
     * @var string Notes internes sur le créateur
     */
    public $notes_internes_createur;

    // Champs techniques standard
    public $rowid;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $entity;
    public $status;

    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'fk_elaska_tiers' => array('type' => 'integer:ElaskaTiers:custom/elaska/class/elaska_tiers.class.php', 'label' => 'ElaskaTiersID', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1, 'foreignkey' => 'elaska_tiers.rowid'),
        
        // PROFIL ENTREPRENEURIAL
        'type_projet_code' => array('type' => 'varchar(50)', 'label' => 'TypeProjetCode', 'enabled' => 1, 'position' => 20, 'notnull' => 0, 'visible' => 1),
        'stade_projet_code' => array('type' => 'varchar(50)', 'label' => 'StadeProjetCode', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'date_debut_accompagnement' => array('type' => 'date', 'label' => 'DateDebutAccompagnement', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'motivation_entrepreneuriat_text' => array('type' => 'text', 'label' => 'MotivationEntrepreneuriat', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'experience_entrepreneuriale_anterieure_code' => array('type' => 'varchar(50)', 'label' => 'ExperienceEntrepreneurialeAnterieureCode', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'description_experience_entrepreneuriale' => array('type' => 'text', 'label' => 'DescriptionExperienceEntrepreneuriale', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'formation_specifique_creation_entreprise_suivie' => array('type' => 'boolean', 'label' => 'FormationSpecifiqueCreationEntrepriseSuivie', 'enabled' => 1, 'position' => 80, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'nom_formation_creation_suivie' => array('type' => 'varchar(255)', 'label' => 'NomFormationCreationSuivie', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'reseau_personnel_professionnel_mobilisable_text' => array('type' => 'text', 'label' => 'ReseauPersonnelProfessionnelMobilisable', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'situation_financiere_personnelle_code' => array('type' => 'varchar(30)', 'label' => 'SituationFinancierePersonnelleCode', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'temps_disponible_projet_semaine_heures' => array('type' => 'integer', 'label' => 'TempsDisponibleProjetSemaineHeures', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'contraintes_personnelles_projet_text' => array('type' => 'text', 'label' => 'ContraintesPersonnellesProjet', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'principales_motivations_creation_text' => array('type' => 'text', 'label' => 'PrincipalesMotivationsCreation', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'principales_craintes_creation_text' => array('type' => 'text', 'label' => 'PrincipalesCraintesCreation', 'enabled' => 1, 'position' => 150, 'notnull' => 0, 'visible' => 1),
        'statut_actuel_createur_code' => array('type' => 'varchar(50)', 'label' => 'StatutActuelCreateurCode', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        
        // PROJET ENTREPRENEURIAL
        'nom_projet' => array('type' => 'varchar(255)', 'label' => 'NomProjet', 'enabled' => 1, 'position' => 200, 'notnull' => 0, 'visible' => 1),
        'secteur_activite_projet_code' => array('type' => 'varchar(50)', 'label' => 'SecteurActiviteProjetCode', 'enabled' => 1, 'position' => 210, 'notnull' => 0, 'visible' => 1),
        'description_concept_projet' => array('type' => 'text', 'label' => 'DescriptionConceptProjet', 'enabled' => 1, 'position' => 220, 'notnull' => 0, 'visible' => 1),
        'originalite_innovation_projet_code' => array('type' => 'varchar(50)', 'label' => 'OriginaliteInnovationProjetCode', 'enabled' => 1, 'position' => 230, 'notnull' => 0, 'visible' => 1),
        'clientele_cible_text' => array('type' => 'text', 'label' => 'ClienteleCible', 'enabled' => 1, 'position' => 240, 'notnull' => 0, 'visible' => 1),
        'besoin_marche_identifie_text' => array('type' => 'text', 'label' => 'BesoinMarcheIdentifie', 'enabled' => 1, 'position' => 250, 'notnull' => 0, 'visible' => 1),
        'proposition_valeur_text' => array('type' => 'text', 'label' => 'PropositionValeur', 'enabled' => 1, 'position' => 260, 'notnull' => 0, 'visible' => 1),
        'etude_marche_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'EtudeMarcheDocumentID', 'enabled' => 1, 'position' => 270, 'notnull' => 0, 'visible' => 1),
        'business_model_text' => array('type' => 'text', 'label' => 'BusinessModel', 'enabled' => 1, 'position' => 280, 'notnull' => 0, 'visible' => 1),
        'concurrents_identifies_text' => array('type' => 'text', 'label' => 'ConcurrentsIdentifies', 'enabled' => 1, 'position' => 290, 'notnull' => 0, 'visible' => 1),
        'partenaires_cles_text' => array('type' => 'text', 'label' => 'PartenairesCles', 'enabled' => 1, 'position' => 300, 'notnull' => 0, 'visible' => 1),
        
        // STRUCTURE ET JURIDIQUE
        'forme_juridique_envisagee_code' => array('type' => 'varchar(30)', 'label' => 'FormeJuridiqueEnvisageeCode', 'enabled' => 1, 'position' => 400, 'notnull' => 0, 'visible' => 1),
        'statuts_redaction_avancement_code' => array('type' => 'varchar(50)', 'label' => 'StatutsRedactionAvancementCode', 'enabled' => 1, 'position' => 410, 'notnull' => 0, 'visible' => 1),
        'demarches_administratives_avancement_code' => array('type' => 'varchar(50)', 'label' => 'DemarchesAdministrativesAvancementCode', 'enabled' => 1, 'position' => 420, 'notnull' => 0, 'visible' => 1),
        'date_immatriculation_prevue' => array('type' => 'date', 'label' => 'DateImmatriculationPrevue', 'enabled' => 1, 'position' => 430, 'notnull' => 0, 'visible' => 1),
        'lieu_implantation_prevu' => array('type' => 'text', 'label' => 'LieuImplantationPrevu', 'enabled' => 1, 'position' => 440, 'notnull' => 0, 'visible' => 1),
        'besoin_locaux_immediat' => array('type' => 'boolean', 'label' => 'BesoinLocauxImmediat', 'enabled' => 1, 'position' => 450, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'type_locaux_recherches_code' => array('type' => 'varchar(50)', 'label' => 'TypeLocauxRecherchesCode', 'enabled' => 1, 'position' => 460, 'notnull' => 0, 'visible' => 1),
        'recherche_locaux_avancement_code' => array('type' => 'varchar(50)', 'label' => 'RechercheLocauxAvancementCode', 'enabled' => 1, 'position' => 470, 'notnull' => 0, 'visible' => 1),
        
        // FINANCEMENT DU PROJET
        'budget_global_projet' => array('type' => 'double(24,8)', 'label' => 'BudgetGlobalProjet', 'enabled' => 1, 'position' => 500, 'notnull' => 0, 'visible' => 1),
        'apport_personnel_montant' => array('type' => 'double(24,8)', 'label' => 'ApportPersonnelMontant', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => 1),
        'types_financement_recherches_code' => array('type' => 'varchar(255)', 'label' => 'TypesFinancementRecherchesCode', 'enabled' => 1, 'position' => 520, 'notnull' => 0, 'visible' => 1),
        'financement_complementaire_recherche_text' => array('type' => 'text', 'label' => 'FinancementComplementaireRecherche', 'enabled' => 1, 'position' => 530, 'notnull' => 0, 'visible' => 1),
        'pret_honneur_montant' => array('type' => 'double(24,8)', 'label' => 'PretHonneurMontant', 'enabled' => 1, 'position' => 540, 'notnull' => 0, 'visible' => 1),
        'subventions_identifiees_text' => array('type' => 'text', 'label' => 'SubventionsIdentifiees', 'enabled' => 1, 'position' => 550, 'notnull' => 0, 'visible' => 1),
        'montant_aides_publiques_estime' => array('type' => 'double(24,8)', 'label' => 'MontantAidesPubliquesEstime', 'enabled' => 1, 'position' => 560, 'notnull' => 0, 'visible' => 1),
        'aides_creation_envisagees_text' => array('type' => 'text', 'label' => 'AidesCreationEnvisagees', 'enabled' => 1, 'position' => 570, 'notnull' => 0, 'visible' => 1),
        'banques_approchees_text' => array('type' => 'text', 'label' => 'BanquesApprochees', 'enabled' => 1, 'position' => 580, 'notnull' => 0, 'visible' => 1),
        'garanties_proposees_text' => array('type' => 'text', 'label' => 'GarantiesProposees', 'enabled' => 1, 'position' => 590, 'notnull' => 0, 'visible' => 1),
        'business_plan_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'BusinessPlanDocumentID', 'enabled' => 1, 'position' => 600, 'notnull' => 0, 'visible' => 1),
        'previsionnel_financier_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'PrevisionnelFinancierDocumentID', 'enabled' => 1, 'position' => 610, 'notnull' => 0, 'visible' => 1),
        'plan_financement_detaille_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'PlanFinancementDetailleDocumentID', 'enabled' => 1, 'position' => 620, 'notnull' => 0, 'visible' => 1),
        
        // ACCOMPAGNEMENT
        'date_debut_projet_initial' => array('type' => 'date', 'label' => 'DateDebutProjetInitial', 'enabled' => 1, 'position' => 700, 'notnull' => 0, 'visible' => 1),
        'besoins_accompagnement_specifiques_createur_text' => array('type' => 'text', 'label' => 'BesoinsAccompagnementSpecifiquesCreateur', 'enabled' => 1, 'position' => 710, 'notnull' => 0, 'visible' => 1),
        'besoin_accompagnement_specifique_projet_code' => array('type' => 'varchar(255)', 'label' => 'BesoinAccompagnementSpecifiqueProjetCode', 'enabled' => 1, 'position' => 720, 'notnull' => 0, 'visible' => 1),
        'organismes_accompagnement_actuels' => array('type' => 'text', 'label' => 'OrganismesAccompagnementActuels', 'enabled' => 1, 'position' => 730, 'notnull' => 0, 'visible' => 1),
        'nom_organisme_accompagnateur_principal' => array('type' => 'varchar(255)', 'label' => 'NomOrganismeAccompagnateurPrincipal', 'enabled' => 1, 'position' => 740, 'notnull' => 0, 'visible' => 1),
        'accompagnement_par_organisme_externe' => array('type' => 'boolean', 'label' => 'AccompagnementParOrganismeExterne', 'enabled' => 1, 'position' => 750, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'formations_suivies_text' => array('type' => 'text', 'label' => 'FormationsSuivies', 'enabled' => 1, 'position' => 760, 'notnull' => 0, 'visible' => 1),
        'fk_user_referent_creation' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserReferentCreation', 'enabled' => 1, 'position' => 770, 'notnull' => 0, 'visible' => 1),
        'prochaines_etapes_court_terme_text' => array('type' => 'text', 'label' => 'ProchainesEtapesCourtTerme', 'enabled' => 1, 'position' => 780, 'notnull' => 0, 'visible' => 1),
        'date_prochaine_etape_accompagnement' => array('type' => 'date', 'label' => 'DateProchaineEtapeAccompagnement', 'enabled' => 1, 'position' => 790, 'notnull' => 0, 'visible' => 1),
        'echeancier_creation_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'EcheancierCreationDocumentID', 'enabled' => 1, 'position' => 800, 'notnull' => 0, 'visible' => 1),
        'statut_accompagnement_elaska_code' => array('type' => 'varchar(50)', 'label' => 'StatutAccompagnementElaskaCode', 'enabled' => 1, 'position' => 810, 'notnull' => 0, 'visible' => 1),
        
        // RISQUES ET ÉVALUATION
        'freins_identifies_text' => array('type' => 'text', 'label' => 'FreinsIdentifies', 'enabled' => 1, 'position' => 900, 'notnull' => 0, 'visible' => 1),
        'atouts_projet_text' => array('type' => 'text', 'label' => 'AtoutsProjet', 'enabled' => 1, 'position' => 910, 'notnull' => 0, 'visible' => 1),
        'risques_principaux_identifies_text' => array('type' => 'text', 'label' => 'RisquesPrincipauxIdentifies', 'enabled' => 1, 'position' => 920, 'notnull' => 0, 'visible' => 1),
        'evaluation_viabilite_projet_code' => array('type' => 'varchar(30)', 'label' => 'EvaluationViabiliteProjetCode', 'enabled' => 1, 'position' => 930, 'notnull' => 0, 'visible' => 1),
        'probabilite_reussite_estimee_code' => array('type' => 'varchar(30)', 'label' => 'ProbabiliteReussiteEstimeeCode', 'enabled' => 1, 'position' => 940, 'notnull' => 0, 'visible' => 1),
        'aleas_anticipes_text' => array('type' => 'text', 'label' => 'AleasAnticipes', 'enabled' => 1, 'position' => 950, 'notnull' => 0, 'visible' => 1),
        'notes_internes_createur' => array('type' => 'text', 'label' => 'NotesInternesCreateur', 'enabled' => 1, 'position' => 960, 'notnull' => 0, 'visible' => 1),
        
        // CHAMPS TECHNIQUES
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'visible' => 0, 'enabled' => 1, 'position' => 1900, 'notnull' => 1, 'default' => '1'),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'visible' => 0, 'enabled' => 1, 'position' => 1910, 'notnull' => 1),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'visible' => 0, 'enabled' => 1, 'position' => 1920, 'notnull' => 1),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'visible' => 0, 'enabled' => 1, 'position' => 1930, 'notnull' => 1),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'visible' => 0, 'enabled' => 1, 'position' => 1940, 'notnull' => 0),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'visible' => 0, 'enabled' => 1, 'position' => 1950, 'notnull' => 0),
        'status' => array('type' => 'integer', 'label' => 'Status', 'visible' => 1, 'enabled' => 1, 'position' => 2000, 'notnull' => 1, 'default' => '1', 'arrayofkeyval' => array('0' => 'Inactive', '1' => 'Active'))
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        global $conf;
        
        parent::__construct($db);
        
        // Par défaut, le créateur est actif
        if (!isset($this->status)) $this->status = 1;
        
        // Valeurs par défaut pour les champs booléens
        $this->formation_specifique_creation_entreprise_suivie = isset($this->formation_specifique_creation_entreprise_suivie) ? $this->formation_specifique_creation_entreprise_suivie : 0;
        $this->besoin_locaux_immediat = isset($this->besoin_locaux_immediat) ? $this->besoin_locaux_immediat : 0;
        $this->accompagnement_par_organisme_externe = isset($this->accompagnement_par_organisme_externe) ? $this->accompagnement_par_organisme_externe : 0;
    }

    /**
     * Crée un créateur dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Vérifier que le lien vers ElaskaTiers est défini
        if (empty($this->fk_elaska_tiers)) {
            $this->error = 'ElaskaTiersIDIsMandatory';
            return -1;
        }
        
        // Valeurs par défaut
        if (empty($this->status)) $this->status = 1;
        
        $this->fk_user_creat = $user->id;
        
        return $this->createCommon($user, $notrigger);
    }

    /**
     * Charge un créateur depuis la base de données par son ID
     *
     * @param int $id         ID de l'enregistrement à charger
     * @param string $ref     Référence (non utilisée mais requise par héritage)
     * @return int            <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = '')
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Charge un créateur depuis la base de données par l'ID du tiers associé
     *
     * @param int $fk_elaska_tiers ID du tiers eLaska lié
     * @return int                 <0 si erreur, 0 si non trouvé, >0 si OK
     */
    public function fetchByTiersId($fk_elaska_tiers)
    {
        if (empty($fk_elaska_tiers)) return -1;
        
        $sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.$this->table_element;
        $sql.= ' WHERE fk_elaska_tiers = '.(int) $fk_elaska_tiers;
        $sql.= ' AND entity IN ('.getEntity($this->element).')';
        
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($obj = $this->db->fetch_object($resql)) {
                return $this->fetch($obj->rowid);
            }
            return 0; // Aucun enregistrement trouvé (pas forcément une erreur dans ce cas)
        }
        
        $this->error = $this->db->lasterror();
        return -1; // Erreur
    }

    /**
     * Met à jour un créateur dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        $this->fk_user_modif = $user->id;
        
        return $this->updateCommon($user, $notrigger);
    }

    /**
     * Supprime un créateur de la base de données
     *
     * @param User $user      Utilisateur qui supprime
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        return $this->deleteCommon($user, $notrigger);
    }

    /**
     * Définit les propriétés à partir des données du formulaire
     * 
     * @param array   $keyArray          Liste des propriétés à définir
     * @param string  $prefix            Préfixe pour les noms de variables POST
     * @param boolean $excludeUndefined  Ne pas définir les propriétés non présentes dans keyArray
     * @return int                       1 si OK, 0 si aucune propriété à définir, -1 si erreur
     */
    public function setPropsFromPost($keyArray, $prefix = '', $excludeUndefined = false)
    {
        if (!is_array($keyArray) || empty($keyArray)) return 0;
        
        $updated = false;
        
        foreach ($keyArray as $key) {
            // Ne mettre à jour que les champs qui ont un paramètre POST correspondant
            $postName = empty($prefix) ? $key : $prefix.$key;
            if (GETPOSTISSET($postName)) {
                // Traitement spécifique par type de champ
                if (isset($this->fields[$key])) {
                    $type = $this->fields[$key]['type'];
                    
                    if (strpos($type, 'date') === 0) {
                        // Traitement des dates (date, datetime, timestamp)
                        $this->$key = GETPOST($postName.'_year', 'int').'-'.GETPOST($postName.'_month', 'int').'-'.GETPOST($postName.'_day', 'int');
                        if (strpos($type, 'datetime') === 0) {
                            $this->$key .= ' '.GETPOST($postName.'_hour', 'int').':'.GETPOST($postName.'_min', 'int').':00';
                        }
                    } elseif (strpos($type, 'double') === 0) {
                        // Traitement des nombres décimaux
                        $this->$key = price2num(GETPOST($postName, 'alphanohtml'));
                    } elseif (strpos($type, 'integer') === 0) {
                        // Traitement des entiers
                        $this->$key = GETPOST($postName, 'int');
                    } elseif (strpos($type, 'text') === 0) {
                        // Traitement du texte riche
                        $this->$key = GETPOST($postName, 'restricthtml');
                    } else {
                        // Traitement par défaut
                        $this->$key = GETPOST($postName, 'alphanohtml');
                    }
                    $updated = true;
                } elseif (!$excludeUndefined) {
                    // Pour les champs non définis dans $fields mais qui sont dans l'objet
                    $this->$key = GETPOST($postName, 'alphanohtml');
                    $updated = true;
                }
            }
        }
        
        return $updated ? 1 : 0;
    }
    
    /**
     * Calcule le temps restant avant la date d'immatriculation prévue
     * 
     * @return int        Nombre de jours avant immatriculation, -1 si la date n'est pas définie
     */
    public function getJoursAvantImmatriculation()
    {
        if (empty($this->date_immatriculation_prevue)) {
            return -1;
        }
        
        try {
            $immatriculation_date = new DateTime($this->date_immatriculation_prevue);
            $today = new DateTime();
            
            if ($today > $immatriculation_date) {
                return 0; // La date est déjà passée
            }
            
            $interval = $today->diff($immatriculation_date);
            return $interval->days;
        } catch (Exception $e) {
            return -1;
        }
    }
    
    /**
     * Méthode factorisée pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                     Objet de traduction
     * @param string    $dictionary_table_suffix_short Suffixe court du nom de la table dictionnaire (ex: 'type_projet' pour llx_c_elaska_creat_type_projet)
     * @param bool      $usekeys                   True pour retourner tableau associatif code=>label
     * @param bool      $show_empty                True pour ajouter une option vide
     * @return array                               Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix_short, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_creat_".$dictionary_table_suffix_short;
        $sql.= " WHERE active = 1";
        $sql.= " ORDER BY position ASC, label ASC";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                if ($usekeys) {
                    $options[$obj->code] = $langs->trans($obj->label);
                } else {
                    $obj_option = new stdClass();
                    $obj_option->code = $obj->code;
                    $obj_option->label = $obj->label;
                    $obj_option->label_translated = $langs->trans($obj->label);
                    $options[] = $obj_option;
                }
            }
            $db->free($resql);
        } else {
            dol_print_error($db);
        }
        
        return $options;
    }
    
    /**
     * Récupère les options du dictionnaire des types de projet
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeProjetOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_projet', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des stades de projet
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStadeProjetOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'stade_projet', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des expériences entrepreneuriales
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getExperienceEntrepreneurialeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'exp_entrepreneuriale', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des secteurs d'activité pour les projets
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSecteurActiviteProjetOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'secteur_activite', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des formes juridiques envisagées
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getFormeJuridiqueEnvisageeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'forme_juridique_envisagee', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des avancements de rédaction de statuts
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
	 public function getFormattedMultiValues($field_code, $separator = ', ') {
    $method_name = 'get' . ucfirst(str_replace('_code', '', $field_code)) . 'Options';
    if (method_exists($this, $method_name)) {
        global $langs;
        $all_options = $this->$method_name($langs, true);
        $selected = self::getSelectedOptionsFromCSV($this->$field_code, $all_options);
        return implode($separator, $selected);
    }
    return '';
}
	 
    public static function getAvancementStatutsOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'avancement_statuts', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des avancements de démarches administratives
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getAvancementDemarchesAdminOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'avancement_demarches_admin', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de locaux recherchés
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeLocauxRecherchesOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_locaux_recherche', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des avancements de recherche de locaux
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getAvancementRechercheLocauxOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'avancement_recherche_locaux', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des évaluations de viabilité de projet
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getViabiliteProjetOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'viabilite_projet', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des probabilités de réussite estimées
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getProbaReussiteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'proba_reussite', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des situations financières personnelles
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSituationFinancierePersonnelleOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'situ_finance_perso', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types d'innovation
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeInnovationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_innovation', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des besoins d'accompagnement de projet
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getBesoinAccompProjetOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'besoin_accomp_projet', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de financement
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeFinancementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_financement', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts d'accompagnement
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutAccompagnementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_accomp', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts actuels du porteur
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutActuelPorteurOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_actuel_porteur', $usekeys, $show_empty);
    }
    
    /**
     * Génère une liste d'options multiples à partir d'une chaîne CSV de codes
     *
     * @param string    $csv_codes  Chaîne de codes séparés par des virgules
     * @param array     $options    Tableau associatif code=>label des options disponibles
     * @return array                Tableau associatif code=>label des options sélectionnées
     */
    public static function getSelectedOptionsFromCSV($csv_codes, $options)
    {
        if (empty($csv_codes)) return array();
        
        $selected_options = array();
        $codes_array = explode(',', $csv_codes);
        
        foreach ($codes_array as $code) {
            $code = trim($code);
            if (isset($options[$code])) {
                $selected_options[$code] = $options[$code];
            }
        }
        
        return $selected_options;
    }
    
    /**
     * Calcule le pourcentage d'apport personnel sur le budget global
     *
     * @return float        Pourcentage (0-100), -1 si données incomplètes
     */
    public function getApportPersonnelPourcentage()
    {
        if (empty($this->budget_global_projet) || empty($this->apport_personnel_montant) || $this->budget_global_projet <= 0) {
            return -1;
        }
        
        return ($this->apport_personnel_montant / $this->budget_global_projet) * 100;
    }
    public function getDocumentObject($document_field) {
    if (!empty($this->$document_field)) {
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
        $doc = new ElaskaDocument($this->db);
        if ($doc->fetch($this->$document_field) > 0) {
            return $doc;
        }
    }
    return null;
}
    /**
     * Calcule le montant manquant pour le financement du projet
     *
     * @return float        Montant manquant, -1 si données incomplètes
     */
    public function getMontantFinancementManquant()
    {
        if (empty($this->budget_global_projet)) {
            return -1;
        }
        
        $somme_financements = 0;
        
        // Apport personnel
        if (!empty($this->apport_personnel_montant)) {
            $somme_financements += $this->apport_personnel_montant;
        }
        
        // Prêt d'honneur
        if (!empty($this->pret_honneur_montant)) {
            $somme_financements += $this->pret_honneur_montant;
        }
        
        // Aides publiques estimées
        if (!empty($this->montant_aides_publiques_estime)) {
            $somme_financements += $this->montant_aides_publiques_estime;
        }
        
        return $this->budget_global_projet - $somme_financements;
    }
}

} // Fin de la condition if !class_exists
?>
