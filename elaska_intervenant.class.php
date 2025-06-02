<?php
/**
 * eLaska - Classe pour gérer les données des intervenants partenaires
 * Date: 2025-06-01
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_organisme.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaIntervenant', false)) {

class ElaskaIntervenant extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_intervenant';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_intervenant';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'user-tie@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var int ID du tiers eLaska parent
     */
    public $fk_elaska_tiers;
    
    /**
     * @var int ID de l'organisme auquel est rattaché l'intervenant (si applicable)
     */
    public $fk_elaska_organisme;
    
    //
    // IDENTITÉ PROFESSIONNELLE
    //
    
    /**
     * @var string Code du type d'intervenant (dictionnaire)
     */
    public $type_intervenant_code;
    
    /**
     * @var string Fonction/poste occupé
     */
    public $fonction_intervenant;
    
    /**
     * @var string Code du métier principal (dictionnaire)
     */
    public $metier_principal_code;
    
    /**
     * @var int Ancienneté dans la profession (en années)
     */
    public $anciennete_profession_annees;
    
    /**
     * @var string Résumé de l'expérience professionnelle
     */
    public $experience_professionnelle_text;
    
    /**
     * @var int ID du document CV (lien vers ElaskaDocument)
     */
    public $cv_document_id;
    
    /**
     * @var string Diplômes et certifications détenus
     */
    public $diplomes_certifications_text;
    
    /**
     * @var string Code du niveau de formation (dictionnaire)
     */
    public $niveau_formation_code;
    
    /**
     * @var string Langues maîtrisées
     */
    public $langues_maitrisees_text;
    
    /**
     * @var string Code du statut juridique d'intervention (dictionnaire)
     */
    public $statut_juridique_intervention_code;
    
    //
    // EXPERTISE ET COMPÉTENCES
    //
    
    /**
     * @var string Code des domaines d'expertise (dictionnaire, peut être multiple)
     */
    public $domaines_expertise_intervenant_code;
    
    /**
     * @var string Spécialités techniques
     */
    public $specialites_techniques_text;
    
    /**
     * @var string Méthodologies maîtrisées
     */
    public $methodologies_maitrisees_text;
    
    /**
     * @var string Outils maîtrisés
     */
    public $outils_maitrise_text;
    
    /**
     * @var string Code du niveau d'expertise (dictionnaire)
     */
    public $niveau_expertise_code;
    
    /**
     * @var string Code des secteurs d'activité d'expertise (dictionnaire, peut être multiple)
     */
    public $secteurs_activite_expertise_code;
    
    /**
     * @var string Références professionnelles antérieures
     */
    public $references_anterieures_text;
    
    //
    // MODALITÉS D'INTERVENTION
    //
    
    /**
     * @var string Code du type d'intervention (dictionnaire, peut être multiple)
     */
    public $type_intervention_code;
    
    /**
     * @var string Code de la zone d'intervention géographique (dictionnaire)
     */
    public $zone_intervention_geographique_code;
    
    /**
     * @var int Distance maximale de déplacement en kilomètres
     */
    public $mobilite_maximale_km;
    
    /**
     * @var float Taux journalier moyen
     */
    public $taux_journalier_moyen;
    
    /**
     * @var string Code de disponibilité en présentiel (dictionnaire)
     */
    public $disponibilite_presentiel_code;
    
    /**
     * @var string Code de disponibilité en distanciel (dictionnaire)
     */
    public $disponibilite_distanciel_code;
    
    /**
     * @var int Disponibilité moyenne hebdomadaire en heures
     */
    public $disponibilite_moyenne_hebdo_heures;
    
    /**
     * @var string Contraintes de planning récurrentes
     */
    public $contraintes_planning_text;
    
    /**
     * @var int Délai d'intervention moyen en jours
     */
    public $delai_intervention_moyen_jours;
    
    //
    // RELATIONS AVEC ELASKA
    //
    
    /**
     * @var string Date de début de la collaboration avec eLaska (format YYYY-MM-DD)
     */
    public $date_debut_collaboration_elaska;
    
    /**
     * @var string Code du statut de la collaboration (dictionnaire)
     */
    public $statut_collaboration_elaska_code;
    
    /**
     * @var int ID de l'utilisateur eLaska référent
     */
    public $fk_user_referent_elaska;
    
    /**
     * @var int Nombre de missions réalisées pour eLaska
     */
    public $nombre_missions_elaska;
    
    /**
     * @var float Note moyenne de satisfaction (0-5)
     */
    public $evaluation_moyenne_satisfaction;
    
    /**
     * @var int ID du document convention avec eLaska (lien vers ElaskaDocument)
     */
    public $convention_elaska_document_id;
    
    /**
     * @var string Date de la dernière intervention (format YYYY-MM-DD)
     */
    public $date_derniere_intervention;
    
    /**
     * @var string Notes internes sur l'intervenant
     */
    public $notes_internes_intervenant_text;
    
    //
    // COMPÉTENCES SPÉCIFIQUES
    //
    
    /**
     * @var string Compétences techniques spécifiques
     */
    public $competences_techniques_specifiques_text;
    
    /**
     * @var string Compétences fonctionnelles spécifiques
     */
    public $competences_fonctionnelles_specifiques_text;
    
    /**
     * @var string Compétences sectorielles spécifiques
     */
    public $competences_sectorielles_specifiques_text;
    
    /**
     * @var string Code du public accompagné (dictionnaire, peut être multiple)
     */
    public $public_accompagne_expertise_code;
    
    //
    // DÉVELOPPEMENT ET FORMATION
    //
    
    /**
     * @var string Formations que l'intervenant peut dispenser
     */
    public $formations_dispensees_text;
    
    /**
     * @var string Formations récemment suivies
     */
    public $formations_suivies_recentes_text;
    
    /**
     * @var string Date de la dernière formation suivie (format YYYY-MM-DD)
     */
    public $date_derniere_formation;
    
    /**
     * @var int ID du document plan de développement (lien vers ElaskaDocument)
     */
    public $plan_developpement_competences_document_id;
    
    //
    // STATISTIQUES ET SUIVI
    //
    
    /**
     * @var float Durée totale des interventions réalisées en jours
     */
    public $duree_totale_interventions_jours;
    
    /**
     * @var float Chiffre d'affaires généré avec cet intervenant
     */
    public $montant_ca_genere;
    
    /**
     * @var string Date de la prochaine disponibilité (format YYYY-MM-DD)
     */
    public $date_prochaine_disponibilite;
    
    /**
     * @var string Date du prochain entretien d'évaluation (format YYYY-MM-DD)
     */
    public $date_prochain_entretien;
    
    /**
     * @var int Nombre de missions annulées
     */
    public $nombre_missions_annulees;
    
    /**
     * @var string Points forts de l'intervenant
     */
    public $points_forts_intervenant_text;
    
    /**
     * @var string Points d'amélioration/vigilance
     */
    public $points_amelioration_text;

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
        'fk_elaska_organisme' => array('type' => 'integer:ElaskaOrganisme:custom/elaska/class/elaska_organisme.class.php', 'label' => 'ElaskaOrganismeID', 'enabled' => 1, 'position' => 20, 'notnull' => 0, 'visible' => 1),
        
        // IDENTITÉ PROFESSIONNELLE
        'type_intervenant_code' => array('type' => 'varchar(50)', 'label' => 'TypeIntervenantCode', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'fonction_intervenant' => array('type' => 'varchar(255)', 'label' => 'FonctionIntervenant', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'metier_principal_code' => array('type' => 'varchar(50)', 'label' => 'MetierPrincipalCode', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'anciennete_profession_annees' => array('type' => 'integer', 'label' => 'AncienneteProfessionAnnees', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'experience_professionnelle_text' => array('type' => 'text', 'label' => 'ExperienceProfessionnelle', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'cv_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'CVDocumentID', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'diplomes_certifications_text' => array('type' => 'text', 'label' => 'DiplomesCertifications', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'niveau_formation_code' => array('type' => 'varchar(50)', 'label' => 'NiveauFormationCode', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'langues_maitrisees_text' => array('type' => 'text', 'label' => 'LanguesMaitrisees', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'statut_juridique_intervention_code' => array('type' => 'varchar(50)', 'label' => 'StatutJuridiqueInterventionCode', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        
        // EXPERTISE ET COMPÉTENCES
        'domaines_expertise_intervenant_code' => array('type' => 'varchar(255)', 'label' => 'DomainesExpertiseIntervenantCode', 'enabled' => 1, 'position' => 200, 'notnull' => 0, 'visible' => 1),
        'specialites_techniques_text' => array('type' => 'text', 'label' => 'SpecialitesTechniques', 'enabled' => 1, 'position' => 210, 'notnull' => 0, 'visible' => 1),
        'methodologies_maitrisees_text' => array('type' => 'text', 'label' => 'MethodologiesMaitrisees', 'enabled' => 1, 'position' => 220, 'notnull' => 0, 'visible' => 1),
        'outils_maitrise_text' => array('type' => 'text', 'label' => 'OutilsMaitrise', 'enabled' => 1, 'position' => 230, 'notnull' => 0, 'visible' => 1),
        'niveau_expertise_code' => array('type' => 'varchar(50)', 'label' => 'NiveauExpertiseCode', 'enabled' => 1, 'position' => 240, 'notnull' => 0, 'visible' => 1),
        'secteurs_activite_expertise_code' => array('type' => 'varchar(255)', 'label' => 'SecteursActiviteExpertiseCode', 'enabled' => 1, 'position' => 250, 'notnull' => 0, 'visible' => 1),
        'references_anterieures_text' => array('type' => 'text', 'label' => 'ReferencesAnterieures', 'enabled' => 1, 'position' => 260, 'notnull' => 0, 'visible' => 1),
        
        // MODALITÉS D'INTERVENTION
        'type_intervention_code' => array('type' => 'varchar(255)', 'label' => 'TypeInterventionCode', 'enabled' => 1, 'position' => 300, 'notnull' => 0, 'visible' => 1),
        'zone_intervention_geographique_code' => array('type' => 'varchar(50)', 'label' => 'ZoneInterventionGeographiqueCode', 'enabled' => 1, 'position' => 310, 'notnull' => 0, 'visible' => 1),
        'mobilite_maximale_km' => array('type' => 'integer', 'label' => 'MobiliteMaximaleKm', 'enabled' => 1, 'position' => 320, 'notnull' => 0, 'visible' => 1),
        'taux_journalier_moyen' => array('type' => 'double(24,8)', 'label' => 'TauxJournalierMoyen', 'enabled' => 1, 'position' => 330, 'notnull' => 0, 'visible' => 1),
        'disponibilite_presentiel_code' => array('type' => 'varchar(50)', 'label' => 'DisponibilitePresentielCode', 'enabled' => 1, 'position' => 340, 'notnull' => 0, 'visible' => 1),
        'disponibilite_distanciel_code' => array('type' => 'varchar(50)', 'label' => 'DisponibiliteDistancielCode', 'enabled' => 1, 'position' => 350, 'notnull' => 0, 'visible' => 1),
        'disponibilite_moyenne_hebdo_heures' => array('type' => 'integer', 'label' => 'DisponibiliteMoyenneHebdoHeures', 'enabled' => 1, 'position' => 360, 'notnull' => 0, 'visible' => 1),
        'contraintes_planning_text' => array('type' => 'text', 'label' => 'ContraintesPlanning', 'enabled' => 1, 'position' => 370, 'notnull' => 0, 'visible' => 1),
        'delai_intervention_moyen_jours' => array('type' => 'integer', 'label' => 'DelaiInterventionMoyenJours', 'enabled' => 1, 'position' => 380, 'notnull' => 0, 'visible' => 1),
        
        // RELATIONS AVEC ELASKA
        'date_debut_collaboration_elaska' => array('type' => 'date', 'label' => 'DateDebutCollaborationElaska', 'enabled' => 1, 'position' => 400, 'notnull' => 0, 'visible' => 1),
        'statut_collaboration_elaska_code' => array('type' => 'varchar(50)', 'label' => 'StatutCollaborationElaskaCode', 'enabled' => 1, 'position' => 410, 'notnull' => 0, 'visible' => 1),
        'fk_user_referent_elaska' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserReferentElaska', 'enabled' => 1, 'position' => 420, 'notnull' => 0, 'visible' => 1),
        'nombre_missions_elaska' => array('type' => 'integer', 'label' => 'NombreMissionsElaska', 'enabled' => 1, 'position' => 430, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'evaluation_moyenne_satisfaction' => array('type' => 'double(4,2)', 'label' => 'EvaluationMoyenneSatisfaction', 'enabled' => 1, 'position' => 440, 'notnull' => 0, 'visible' => 1),
        'convention_elaska_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'ConventionElaskaDocumentID', 'enabled' => 1, 'position' => 450, 'notnull' => 0, 'visible' => 1),
        'date_derniere_intervention' => array('type' => 'date', 'label' => 'DateDerniereIntervention', 'enabled' => 1, 'position' => 460, 'notnull' => 0, 'visible' => 1),
        'notes_internes_intervenant_text' => array('type' => 'text', 'label' => 'NotesInternesIntervenant', 'enabled' => 1, 'position' => 470, 'notnull' => 0, 'visible' => 1),
        
        // COMPÉTENCES SPÉCIFIQUES
        'competences_techniques_specifiques_text' => array('type' => 'text', 'label' => 'CompetencesTechniquesSpecifiques', 'enabled' => 1, 'position' => 500, 'notnull' => 0, 'visible' => 1),
        'competences_fonctionnelles_specifiques_text' => array('type' => 'text', 'label' => 'CompetencesFonctionnellesSpecifiques', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => 1),
        'competences_sectorielles_specifiques_text' => array('type' => 'text', 'label' => 'CompetencesSectoriellesSpecifiques', 'enabled' => 1, 'position' => 520, 'notnull' => 0, 'visible' => 1),
        'public_accompagne_expertise_code' => array('type' => 'varchar(255)', 'label' => 'PublicAccompagneExpertiseCode', 'enabled' => 1, 'position' => 530, 'notnull' => 0, 'visible' => 1),
        
        // DÉVELOPPEMENT ET FORMATION
        'formations_dispensees_text' => array('type' => 'text', 'label' => 'FormationsDispensees', 'enabled' => 1, 'position' => 600, 'notnull' => 0, 'visible' => 1),
        'formations_suivies_recentes_text' => array('type' => 'text', 'label' => 'FormationsSuiviesRecentes', 'enabled' => 1, 'position' => 610, 'notnull' => 0, 'visible' => 1),
        'date_derniere_formation' => array('type' => 'date', 'label' => 'DateDerniereFormation', 'enabled' => 1, 'position' => 620, 'notnull' => 0, 'visible' => 1),
        'plan_developpement_competences_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'PlanDeveloppementCompetencesDocumentID', 'enabled' => 1, 'position' => 630, 'notnull' => 0, 'visible' => 1),
        
        // STATISTIQUES ET SUIVI
        'duree_totale_interventions_jours' => array('type' => 'double(8,2)', 'label' => 'DureeTotaleInterventionsJours', 'enabled' => 1, 'position' => 700, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'montant_ca_genere' => array('type' => 'double(24,8)', 'label' => 'MontantCAGenere', 'enabled' => 1, 'position' => 710, 'notnull' => 0, 'visible' => 1),
        'date_prochaine_disponibilite' => array('type' => 'date', 'label' => 'DateProchaineDisponibilite', 'enabled' => 1, 'position' => 720, 'notnull' => 0, 'visible' => 1),
        'date_prochain_entretien' => array('type' => 'date', 'label' => 'DateProchainEntretien', 'enabled' => 1, 'position' => 730, 'notnull' => 0, 'visible' => 1),
        'nombre_missions_annulees' => array('type' => 'integer', 'label' => 'NombreMissionsAnnulees', 'enabled' => 1, 'position' => 740, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'points_forts_intervenant_text' => array('type' => 'text', 'label' => 'PointsFortsIntervenant', 'enabled' => 1, 'position' => 750, 'notnull' => 0, 'visible' => 1),
        'points_amelioration_text' => array('type' => 'text', 'label' => 'PointsAmelioration', 'enabled' => 1, 'position' => 760, 'notnull' => 0, 'visible' => 1),
        
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
        
        // Par défaut, l'intervenant est actif
        if (!isset($this->status)) $this->status = 1;
        
        // Valeurs par défaut pour les champs numériques
        $this->anciennete_profession_annees = isset($this->anciennete_profession_annees) ? $this->anciennete_profession_annees : 0;
        $this->mobilite_maximale_km = isset($this->mobilite_maximale_km) ? $this->mobilite_maximale_km : 0;
        $this->disponibilite_moyenne_hebdo_heures = isset($this->disponibilite_moyenne_hebdo_heures) ? $this->disponibilite_moyenne_hebdo_heures : 0;
        $this->delai_intervention_moyen_jours = isset($this->delai_intervention_moyen_jours) ? $this->delai_intervention_moyen_jours : 0;
        $this->nombre_missions_elaska = isset($this->nombre_missions_elaska) ? $this->nombre_missions_elaska : 0;
        $this->duree_totale_interventions_jours = isset($this->duree_totale_interventions_jours) ? $this->duree_totale_interventions_jours : 0;
        $this->nombre_missions_annulees = isset($this->nombre_missions_annulees) ? $this->nombre_missions_annulees : 0;
    }

    /**
     * Crée un intervenant dans la base de données
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
        
        $result = $this->createCommon($user, $notrigger);
        
        // Si création réussie et liée à un organisme, mettre à jour les statistiques de l'organisme
        if ($result > 0 && !empty($this->fk_elaska_organisme)) {
            $this->updateOrganismeStats();
        }
        
        return $result;
    }

    /**
     * Charge un intervenant depuis la base de données par son ID
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
     * Charge un intervenant depuis la base de données par l'ID du tiers associé
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
            return 0; // Aucun enregistrement trouvé
        }
        
        $this->error = $this->db->lasterror();
        return -1; // Erreur
    }

    /**
     * Met à jour un intervenant dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Sauvegarder l'ancien organisme pour comparer après mise à jour
        $old_organisme_id = $this->fk_elaska_organisme;
        
        $this->fk_user_modif = $user->id;
        
        $result = $this->updateCommon($user, $notrigger);
        
        // Si mise à jour réussie, vérifier s'il faut mettre à jour les statistiques des organismes
        if ($result > 0) {
            // Si changement d'organisme, mettre à jour les stats pour l'ancien et le nouveau
            if ($old_organisme_id != $this->fk_elaska_organisme) {
                if ($old_organisme_id > 0) {
                    $this->updateOrganismeStats($old_organisme_id);
                }
                if ($this->fk_elaska_organisme > 0) {
                    $this->updateOrganismeStats();
                }
            } else if ($this->fk_elaska_organisme > 0) {
                // Sinon mettre à jour uniquement l'organisme actuel
                $this->updateOrganismeStats();
            }
        }
        
        return $result;
    }

    /**
     * Supprime un intervenant de la base de données
     *
     * @param User $user      Utilisateur qui supprime
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        // Sauvegarder l'organisme pour mettre à jour ses stats après suppression
        $old_organisme_id = $this->fk_elaska_organisme;
        
        $result = $this->deleteCommon($user, $notrigger);
        
        // Si suppression réussie et lié à un organisme, mettre à jour les statistiques de l'organisme
        if ($result > 0 && $old_organisme_id > 0) {
            $this->updateOrganismeStats($old_organisme_id);
        }
        
        return $result;
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
     * Met à jour les statistiques de l'organisme associé
     *
     * @param int $organisme_id ID de l'organisme (facultatif, utilise $this->fk_elaska_organisme si non fourni)
     * @return int              <0 si erreur, >0 si OK
     */
    public function updateOrganismeStats($organisme_id = 0)
    {
        if ($organisme_id <= 0) {
            $organisme_id = $this->fk_elaska_organisme;
        }
        
        if ($organisme_id <= 0) {
            return 0; // Pas d'organisme associé
        }
        
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_organisme.class.php';
        $organisme = new ElaskaOrganisme($this->db);
        
        if ($organisme->fetch($organisme_id) <= 0) {
            return -1;
        }
        
        return $organisme->updateStatistiques();
    }

    /**
     * Récupère l'organisme associé à l'intervenant
     *
     * @return ElaskaOrganisme|null Objet ElaskaOrganisme si trouvé, null sinon
     */
    public function getOrganisme()
    {
        if (empty($this->fk_elaska_organisme)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_organisme.class.php';
        $organisme = new ElaskaOrganisme($this->db);
        
        if ($organisme->fetch($this->fk_elaska_organisme) > 0) {
            return $organisme;
        }
        
        return null;
    }

    /**
     * Méthode factorisée pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                     Objet de traduction
     * @param string    $dictionary_table_suffix_short Suffixe court du nom de la table dictionnaire (ex: 'type_intervenant' pour llx_c_elaska_interv_type_intervenant)
     * @param bool      $usekeys                   True pour retourner tableau associatif code=>label
     * @param bool      $show_empty                True pour ajouter une option vide
     * @return array                               Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix_short, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_interv_".$dictionary_table_suffix_short;
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
     * Récupère les options du dictionnaire des types d'intervenant
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeIntervenantOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_intervenant', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des métiers principaux
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getMetierPrincipalOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'metier_principal', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des niveaux de formation
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getNiveauFormationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'niveau_formation', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts juridiques d'intervention
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutJuridiqueInterventionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_juridique', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des domaines d'expertise
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getDomainesExpertiseOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'domaines_expertise', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des secteurs d'activité
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSecteursActiviteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'secteurs_activite', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des niveaux d'expertise
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getNiveauExpertiseOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'niveau_expertise', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types d'intervention
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeInterventionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_intervention', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des zones d'intervention
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getZoneInterventionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'zone_intervention', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des disponibilités
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getDisponibiliteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'disponibilite', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts de collaboration
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutCollaborationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_collaboration', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des publics accompagnés
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getPublicAccompagneOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'public_accompagne', $usekeys, $show_empty);
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
     * Formate les valeurs multi-sélection pour l'affichage
     *
     * @param string    $field_code    Nom du champ contenant les codes CSV
     * @param string    $separator     Séparateur pour la concaténation des libellés
     * @return string                  Chaîne formatée des libellés correspondants
     */
    public function getFormattedMultiValues($field_code, $separator = ', ')
    {
        $method_name = 'get' . ucfirst(str_replace('_code', '', $field_code)) . 'Options';
        if (method_exists($this, $method_name)) {
            global $langs;
            $all_options = $this->$method_name($langs, true);
            $selected = self::getSelectedOptionsFromCSV($this->$field_code, $all_options);
            return implode($separator, $selected);
        }
        return '';
    }
    
    /**
     * Récupère un document associé à partir de son ID
     *
     * @param string $document_field    Nom du champ contenant l'ID du document
     * @return ElaskaDocument|null      Objet ElaskaDocument si trouvé, null sinon
     */
    public function getDocumentObject($document_field)
    {
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
     * Calcule le nombre de jours depuis le début de la collaboration avec eLaska
     * 
     * @return int   Nombre de jours, -1 si la date n'est pas définie
     */
    public function getJoursDepuisDebutCollaboration()
    {
        if (empty($this->date_debut_collaboration_elaska)) {
            return -1;
        }
        
        try {
            $debut_date = new DateTime($this->date_debut_collaboration_elaska);
            $today = new DateTime();
            $interval = $today->diff($debut_date);
            return $interval->days;
        } catch (Exception $e) {
            return -1;
        }
    }
    
    /**
     * Calcule le nombre de jours depuis la dernière intervention
     * 
     * @return int   Nombre de jours, -1 si la date n'est pas définie
     */
    public function getJoursDepuisDerniereIntervention()
    {
        if (empty($this->date_derniere_intervention)) {
            return -1;
        }
        
        try {
            $derniere_date = new DateTime($this->date_derniere_intervention);
            $today = new DateTime();
            $interval = $today->diff($derniere_date);
            return $interval->days;
        } catch (Exception $e) {
            return -1;
        }
    }
    
    /**
     * Vérifie si l'intervenant est actuellement disponible
     * 
     * @return bool   true si disponible, false sinon
     */
    public function isDisponible()
    {
        if (empty($this->date_prochaine_disponibilite)) {
            // Si pas de date de prochaine disponibilité, vérifier le statut de collaboration
            return $this->statut_collaboration_elaska_code === 'ACTIF';
        }
        
        try {
            $dispo_date = new DateTime($this->date_prochaine_disponibilite);
            $today = new DateTime();
            return ($today >= $dispo_date && $this->statut_collaboration_elaska_code === 'ACTIF');
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Calcule le ratio de conversion (missions réalisées / missions totales)
     * 
     * @return float   Ratio de 0 à 1, -1 si pas de données
     */
    public function getRatioConversion()
    {
        $total_missions = $this->nombre_missions_elaska + $this->nombre_missions_annulees;
        
        if ($total_missions <= 0) {
            return -1;
        }
        
        return $this->nombre_missions_elaska / $total_missions;
    }
    
    /**
     * Récupère le chiffre d'affaires moyen par mission
     * 
     * @return float   CA moyen par mission, -1 si pas de données
     */
    public function getCAMoyenParMission()
    {
        if ($this->nombre_missions_elaska <= 0 || empty($this->montant_ca_genere)) {
            return -1;
        }
        
        return $this->montant_ca_genere / $this->nombre_missions_elaska;
    }
    
    /**
     * Met à jour les statistiques de l'intervenant
     *
     * @return int     <0 si erreur, >0 si OK
     */
    public function updateStatistiques()
    {
        global $user;
        
        // Ici, vous pouvez ajouter du code pour mettre à jour les statistiques
        // comme nombre_missions_elaska, duree_totale_interventions_jours, montant_ca_genere, etc.
        // en interrogeant d'autres tables comme les missions, interventions, etc.
        // Exemple fictif:
        /*
        $sql = 'SELECT COUNT(*) as nb, SUM(duree_jours) as duree_totale, SUM(montant) as ca_total 
                FROM '.MAIN_DB_PREFIX.'elaska_mission 
                WHERE fk_elaska_intervenant = '.$this->id.' 
                AND statut_mission_code = "TERMINEE"';
                
        $resql = $this->db->query($sql);
        if ($resql && ($obj = $this->db->fetch_object($resql))) {
            $this->nombre_missions_elaska = $obj->nb;
            $this->duree_totale_interventions_jours = $obj->duree_totale;
            $this->montant_ca_genere = $obj->ca_total;
        }
        */
        
        $this->fk_user_modif = $user->id;
        $result = $this->update($user, 1); // 1 = ne pas déclencher de trigger
        
        return $result;
    }
}

} // Fin de la condition if !class_exists
?>