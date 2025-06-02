<?php
/**
 * eLaska - Classe pour gérer les données des organismes partenaires
 * Date: 2025-06-01
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaOrganisme', false)) {

class ElaskaOrganisme extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_organisme';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_organisme';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'building@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var int ID du tiers eLaska parent
     */
    public $fk_elaska_tiers;
    
    //
    // IDENTITÉ ET INFORMATIONS GÉNÉRALES
    //
    
    /**
     * @var string Code du type d'organisme (dictionnaire)
     */
    public $type_organisme_code;
    
    /**
     * @var string Code du secteur d'intervention (dictionnaire)
     */
    public $secteur_intervention_code;
    
    /**
     * @var string Code du niveau territorial (dictionnaire)
     */
    public $niveau_territorial_code;
    
    /**
     * @var string Date de création de l'organisme (format YYYY-MM-DD)
     */
    public $date_creation_organisme;
    
    /**
     * @var string Description des missions principales
     */
    public $description_missions_text;
    
    /**
     * @var string Domaines d'expertise spécifiques
     */
    public $domaines_expertise_text;
    
    /**
     * @var string Zone d'intervention géographique
     */
    public $zone_intervention_geographique_text;
    
    /**
     * @var string Numéro d'agrément si organisme agréé
     */
    public $agrement_numero;
    
    /**
     * @var string Code du label qualité (dictionnaire)
     */
    public $label_qualite_code;
    
    //
    // STRUCTURE ET ORGANISATION
    //
    
    /**
     * @var string Code du statut juridique de l'organisme (dictionnaire)
     */
    public $statut_juridique_organisme_code;
    
    /**
     * @var string Tutelle ou autorité de rattachement
     */
    public $organisme_tutelle_text;
    
    /**
     * @var int Nombre total de salariés
     */
    public $effectif_total;
    
    /**
     * @var int Nombre d'intervenants/consultants
     */
    public $effectif_intervenants;
    
    /**
     * @var float Budget annuel approximatif
     */
    public $budget_annuel;
    
    /**
     * @var string Sources de financement principales
     */
    public $sources_financement_text;
    
    /**
     * @var int ID du document organigramme (lien vers ElaskaDocument)
     */
    public $organigramme_document_id;
    
    /**
     * @var string Certifications qualité détenues
     */
    public $certifications_qualite_text;
    
    /**
     * @var string Agréments spécifiques détenus
     */
    public $agrements_specifiques_text;
    
    //
    // SERVICES ET PRESTATIONS
    //
    
    /**
     * @var string Codes des types de prestations principales (dictionnaire, peut être multiple)
     */
    public $types_prestations_principales_code;
    
    /**
     * @var string Code des modalités d'intervention (dictionnaire)
     */
    public $modalites_intervention_code;
    
    /**
     * @var string Information sur la tarification
     */
    public $tarification_standard_text;
    
    /**
     * @var string Délais moyens d'intervention
     */
    public $delais_intervention_moyens_text;
    
    /**
     * @var string Conditions particulières
     */
    public $conditions_intervention_text;
    
    /**
     * @var int ID du document catalogue de prestations (lien vers ElaskaDocument)
     */
    public $catalogue_prestations_document_id;
    
    /**
     * @var string Références clients principales
     */
    public $principales_references_text;
    
    //
    // PARTENARIATS ET RÉSEAUX
    //
    
    /**
     * @var string Réseaux auxquels appartient l'organisme
     */
    public $reseaux_appartenance_text;
    
    /**
     * @var string Partenaires principaux
     */
    public $partenaires_principaux_text;
    
    /**
     * @var int ID du document conventions de partenariat (lien vers ElaskaDocument)
     */
    public $conventions_partenariat_document_id;
    
    //
    // RELATIONS AVEC ELASKA
    //
    
    /**
     * @var string Date de début de la relation (format YYYY-MM-DD)
     */
    public $date_debut_partenariat_elaska;
    
    /**
     * @var string Nom du référent principal côté organisme
     */
    public $referent_organisme_nom;
    
    /**
     * @var string Fonction du référent
     */
    public $referent_organisme_fonction;
    
    /**
     * @var string Contact du référent
     */
    public $referent_organisme_contact;
    
    /**
     * @var int ID de l'utilisateur eLaska référent
     */
    public $fk_user_referent_elaska;
    
    /**
     * @var string Code du type de partenariat avec eLaska (dictionnaire)
     */
    public $type_partenariat_elaska_code;
    
    /**
     * @var int ID du document convention avec eLaska (lien vers ElaskaDocument)
     */
    public $convention_elaska_document_id;
    
    /**
     * @var string Date de fin de convention (format YYYY-MM-DD)
     */
    public $date_fin_convention_elaska;
    
    /**
     * @var string Code du statut du partenariat avec eLaska (dictionnaire)
     */
    public $statut_partenariat_elaska_code;
    
    /**
     * @var string Observations sur le partenariat
     */
    public $observations_partenariat_text;
    
    //
    // COMMUNICATION ET CONTACT
    //
    
    /**
     * @var string Site web spécifique
     */
    public $site_web_organisme;
    
    /**
     * @var string Présence sur les réseaux sociaux
     */
    public $reseaux_sociaux_organisme_text;
    
    /**
     * @var int ID du document plaquette de présentation (lien vers ElaskaDocument)
     */
    public $plaquette_presentation_document_id;
    
    /**
     * @var string Autres supports de communication
     */
    public $supports_communication_text;
    
    //
    // STATISTIQUES ET SUIVI
    //
    
    /**
     * @var int Nombre d'intervenants actifs liés
     */
    public $nombre_intervenants_actifs;
    
    /**
     * @var float Note moyenne de satisfaction (0-5)
     */
    public $note_satisfaction_moyenne;
    
    /**
     * @var int Nombre de missions réalisées
     */
    public $nombre_missions_realisees;
    
    /**
     * @var float Chiffre d'affaires réalisé avec cet organisme
     */
    public $montant_ca_realise;
    
    /**
     * @var string Date de la dernière mission réalisée (format YYYY-MM-DD)
     */
    public $date_derniere_mission;
    
    /**
     * @var string Date du prochain rendez-vous (format YYYY-MM-DD)
     */
    public $date_prochain_rdv;
    
    /**
     * @var string Date de la dernière évaluation (format YYYY-MM-DD)
     */
    public $date_derniere_evaluation;
    
    //
    // NOTES ET DOCUMENTS ADDITIONNELS
    //
    
    /**
     * @var string Notes internes sur l'organisme
     */
    public $notes_internes_organisme;
    
    /**
     * @var string Points forts de l'organisme
     */
    public $points_forts_text;
    
    /**
     * @var string Points de vigilance concernant l'organisme
     */
    public $points_vigilance_text;

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
        
        // IDENTITÉ ET INFORMATIONS GÉNÉRALES
        'type_organisme_code' => array('type' => 'varchar(50)', 'label' => 'TypeOrganismeCode', 'enabled' => 1, 'position' => 20, 'notnull' => 0, 'visible' => 1),
        'secteur_intervention_code' => array('type' => 'varchar(50)', 'label' => 'SecteurInterventionCode', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'niveau_territorial_code' => array('type' => 'varchar(50)', 'label' => 'NiveauTerritorialCode', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'date_creation_organisme' => array('type' => 'date', 'label' => 'DateCreationOrganisme', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'description_missions_text' => array('type' => 'text', 'label' => 'DescriptionMissions', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'domaines_expertise_text' => array('type' => 'text', 'label' => 'DomainesExpertise', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'zone_intervention_geographique_text' => array('type' => 'text', 'label' => 'ZoneInterventionGeographique', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'agrement_numero' => array('type' => 'varchar(100)', 'label' => 'AgrementNumero', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'label_qualite_code' => array('type' => 'varchar(50)', 'label' => 'LabelQualiteCode', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        
        // STRUCTURE ET ORGANISATION
        'statut_juridique_organisme_code' => array('type' => 'varchar(50)', 'label' => 'StatutJuridiqueOrganismeCode', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'organisme_tutelle_text' => array('type' => 'text', 'label' => 'OrganismeTutelle', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'effectif_total' => array('type' => 'integer', 'label' => 'EffectifTotal', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'effectif_intervenants' => array('type' => 'integer', 'label' => 'EffectifIntervenants', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'budget_annuel' => array('type' => 'double(24,8)', 'label' => 'BudgetAnnuel', 'enabled' => 1, 'position' => 150, 'notnull' => 0, 'visible' => 1),
        'sources_financement_text' => array('type' => 'text', 'label' => 'SourcesFinancement', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        'organigramme_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'OrganigrammeDocumentID', 'enabled' => 1, 'position' => 170, 'notnull' => 0, 'visible' => 1),
        'certifications_qualite_text' => array('type' => 'text', 'label' => 'CertificationsQualite', 'enabled' => 1, 'position' => 180, 'notnull' => 0, 'visible' => 1),
        'agrements_specifiques_text' => array('type' => 'text', 'label' => 'AgrementsSpecifiques', 'enabled' => 1, 'position' => 190, 'notnull' => 0, 'visible' => 1),
        
        // SERVICES ET PRESTATIONS
        'types_prestations_principales_code' => array('type' => 'varchar(255)', 'label' => 'TypesPrestationsPrincipalesCode', 'enabled' => 1, 'position' => 200, 'notnull' => 0, 'visible' => 1),
        'modalites_intervention_code' => array('type' => 'varchar(50)', 'label' => 'ModalitesInterventionCode', 'enabled' => 1, 'position' => 210, 'notnull' => 0, 'visible' => 1),
        'tarification_standard_text' => array('type' => 'text', 'label' => 'TarificationStandard', 'enabled' => 1, 'position' => 220, 'notnull' => 0, 'visible' => 1),
        'delais_intervention_moyens_text' => array('type' => 'text', 'label' => 'DelaisInterventionMoyens', 'enabled' => 1, 'position' => 230, 'notnull' => 0, 'visible' => 1),
        'conditions_intervention_text' => array('type' => 'text', 'label' => 'ConditionsIntervention', 'enabled' => 1, 'position' => 240, 'notnull' => 0, 'visible' => 1),
        'catalogue_prestations_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'CataloguePrestationsDocumentID', 'enabled' => 1, 'position' => 250, 'notnull' => 0, 'visible' => 1),
        'principales_references_text' => array('type' => 'text', 'label' => 'PrincipalesReferences', 'enabled' => 1, 'position' => 260, 'notnull' => 0, 'visible' => 1),
        
        // PARTENARIATS ET RÉSEAUX
        'reseaux_appartenance_text' => array('type' => 'text', 'label' => 'ReseauxAppartenance', 'enabled' => 1, 'position' => 270, 'notnull' => 0, 'visible' => 1),
        'partenaires_principaux_text' => array('type' => 'text', 'label' => 'PartenairesPrincipaux', 'enabled' => 1, 'position' => 280, 'notnull' => 0, 'visible' => 1),
        'conventions_partenariat_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'ConventionsPartenariatDocumentID', 'enabled' => 1, 'position' => 290, 'notnull' => 0, 'visible' => 1),
        
        // RELATIONS AVEC ELASKA
        'date_debut_partenariat_elaska' => array('type' => 'date', 'label' => 'DateDebutPartenariatElaska', 'enabled' => 1, 'position' => 300, 'notnull' => 0, 'visible' => 1),
        'referent_organisme_nom' => array('type' => 'varchar(255)', 'label' => 'ReferentOrganismeNom', 'enabled' => 1, 'position' => 310, 'notnull' => 0, 'visible' => 1),
        'referent_organisme_fonction' => array('type' => 'varchar(255)', 'label' => 'ReferentOrganismeFonction', 'enabled' => 1, 'position' => 320, 'notnull' => 0, 'visible' => 1),
        'referent_organisme_contact' => array('type' => 'varchar(255)', 'label' => 'ReferentOrganismeContact', 'enabled' => 1, 'position' => 330, 'notnull' => 0, 'visible' => 1),
        'fk_user_referent_elaska' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserReferentElaska', 'enabled' => 1, 'position' => 340, 'notnull' => 0, 'visible' => 1),
        'type_partenariat_elaska_code' => array('type' => 'varchar(50)', 'label' => 'TypePartenariatElaskaCode', 'enabled' => 1, 'position' => 350, 'notnull' => 0, 'visible' => 1),
        'convention_elaska_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'ConventionElaskaDocumentID', 'enabled' => 1, 'position' => 360, 'notnull' => 0, 'visible' => 1),
        'date_fin_convention_elaska' => array('type' => 'date', 'label' => 'DateFinConventionElaska', 'enabled' => 1, 'position' => 370, 'notnull' => 0, 'visible' => 1),
        'statut_partenariat_elaska_code' => array('type' => 'varchar(50)', 'label' => 'StatutPartenariatElaskaCode', 'enabled' => 1, 'position' => 380, 'notnull' => 0, 'visible' => 1),
        'observations_partenariat_text' => array('type' => 'text', 'label' => 'ObservationsPartenariat', 'enabled' => 1, 'position' => 390, 'notnull' => 0, 'visible' => 1),
        
        // COMMUNICATION ET CONTACT
        'site_web_organisme' => array('type' => 'varchar(255)', 'label' => 'SiteWebOrganisme', 'enabled' => 1, 'position' => 400, 'notnull' => 0, 'visible' => 1),
        'reseaux_sociaux_organisme_text' => array('type' => 'text', 'label' => 'ReseauxSociauxOrganisme', 'enabled' => 1, 'position' => 410, 'notnull' => 0, 'visible' => 1),
        'plaquette_presentation_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'PlaquettePresentationDocumentID', 'enabled' => 1, 'position' => 420, 'notnull' => 0, 'visible' => 1),
        'supports_communication_text' => array('type' => 'text', 'label' => 'SupportsCommunication', 'enabled' => 1, 'position' => 430, 'notnull' => 0, 'visible' => 1),
        
        // STATISTIQUES ET SUIVI
        'nombre_intervenants_actifs' => array('type' => 'integer', 'label' => 'NombreIntervenantsActifs', 'enabled' => 1, 'position' => 500, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'note_satisfaction_moyenne' => array('type' => 'double(4,2)', 'label' => 'NoteSatisfactionMoyenne', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => 1),
        'nombre_missions_realisees' => array('type' => 'integer', 'label' => 'NombreMissionsRealisees', 'enabled' => 1, 'position' => 520, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'montant_ca_realise' => array('type' => 'double(24,8)', 'label' => 'MontantCARealisé', 'enabled' => 1, 'position' => 530, 'notnull' => 0, 'visible' => 1),
        'date_derniere_mission' => array('type' => 'date', 'label' => 'DateDerniereMission', 'enabled' => 1, 'position' => 540, 'notnull' => 0, 'visible' => 1),
        'date_prochain_rdv' => array('type' => 'date', 'label' => 'DateProchainRDV', 'enabled' => 1, 'position' => 550, 'notnull' => 0, 'visible' => 1),
        'date_derniere_evaluation' => array('type' => 'date', 'label' => 'DateDerniereEvaluation', 'enabled' => 1, 'position' => 560, 'notnull' => 0, 'visible' => 1),
        
        // NOTES ET DOCUMENTS ADDITIONNELS
        'notes_internes_organisme' => array('type' => 'text', 'label' => 'NotesInternesOrganisme', 'enabled' => 1, 'position' => 600, 'notnull' => 0, 'visible' => 1),
        'points_forts_text' => array('type' => 'text', 'label' => 'PointsForts', 'enabled' => 1, 'position' => 610, 'notnull' => 0, 'visible' => 1),
        'points_vigilance_text' => array('type' => 'text', 'label' => 'PointsVigilance', 'enabled' => 1, 'position' => 620, 'notnull' => 0, 'visible' => 1),
        
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
        
        // Par défaut, l'organisme est actif
        if (!isset($this->status)) $this->status = 1;
        
        // Valeurs par défaut pour les champs numériques
        $this->effectif_total = isset($this->effectif_total) ? $this->effectif_total : 0;
        $this->effectif_intervenants = isset($this->effectif_intervenants) ? $this->effectif_intervenants : 0;
        $this->nombre_intervenants_actifs = isset($this->nombre_intervenants_actifs) ? $this->nombre_intervenants_actifs : 0;
        $this->nombre_missions_realisees = isset($this->nombre_missions_realisees) ? $this->nombre_missions_realisees : 0;
    }

    /**
     * Crée un organisme dans la base de données
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
     * Charge un organisme depuis la base de données par son ID
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
     * Charge un organisme depuis la base de données par l'ID du tiers associé
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
     * Met à jour un organisme dans la base de données
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
     * Supprime un organisme de la base de données
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
     * Méthode factorisée pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                     Objet de traduction
     * @param string    $dictionary_table_suffix_short Suffixe court du nom de la table dictionnaire (ex: 'type_organisme' pour llx_c_elaska_org_type_organisme)
     * @param bool      $usekeys                   True pour retourner tableau associatif code=>label
     * @param bool      $show_empty                True pour ajouter une option vide
     * @return array                               Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix_short, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_org_".$dictionary_table_suffix_short;
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
     * Récupère les options du dictionnaire des types d'organisme
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeOrganismeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_organisme', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des secteurs d'intervention
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSecteurInterventionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'secteur_intervention', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des niveaux territoriaux
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getNiveauTerritorialOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'niveau_territorial', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des labels qualité
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getLabelQualiteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'label_qualite', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts juridiques d'organisme
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutJuridiqueOrganismeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_juridique', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de prestations
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypesPrestationsOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'types_prestations', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des modalités d'intervention
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getModalitesInterventionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'modalites_intervention', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de partenariat avec eLaska
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypePartenariatElaskaOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_partenariat_elaska', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts de partenariat
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutPartenariatOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_partenariat', $usekeys, $show_empty);
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
     * Calcule le nombre de jours depuis le début du partenariat avec eLaska
     * 
     * @return int   Nombre de jours, -1 si la date n'est pas définie
     */
    public function getJoursDepuisDebutPartenariat()
    {
        if (empty($this->date_debut_partenariat_elaska)) {
            return -1;
        }
        
        try {
            $debut_date = new DateTime($this->date_debut_partenariat_elaska);
            $today = new DateTime();
            $interval = $today->diff($debut_date);
            return $interval->days;
        } catch (Exception $e) {
            return -1;
        }
    }
    
    /**
     * Calcule le nombre de jours avant la fin de la convention avec eLaska
     * 
     * @return int   Nombre de jours, -1 si la date n'est pas définie
     */
    public function getJoursAvantFinConvention()
    {
        if (empty($this->date_fin_convention_elaska)) {
            return -1;
        }
        
        try {
            $fin_date = new DateTime($this->date_fin_convention_elaska);
            $today = new DateTime();
            
            if ($today > $fin_date) {
                return 0; // La date est déjà passée
            }
            
            $interval = $today->diff($fin_date);
            return $interval->days;
        } catch (Exception $e) {
            return -1;
        }
    }
    
    /**
     * Vérifie si la convention est expirée
     * 
     * @return bool   true si expirée, false sinon ou si pas de date
     */
    public function isConventionExpiree()
    {
        if (empty($this->date_fin_convention_elaska)) {
            return false;
        }
        
        try {
            $fin_date = new DateTime($this->date_fin_convention_elaska);
            $today = new DateTime();
            return ($today > $fin_date);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Récupère tous les intervenants associés à cet organisme
     *
     * @return array     Tableau d'objets ElaskaIntervenant, array() si erreur
     */
    public function getIntervenants()
    {
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_intervenant.class.php';
        
        $sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'elaska_intervenant';
        $sql.= ' WHERE fk_elaska_organisme = '.(int) $this->id;
        $sql.= ' AND entity IN ('.getEntity('elaska_intervenant').')';
        
        $list = array();
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $intervenant = new ElaskaIntervenant($this->db);
                $intervenant->fetch($obj->rowid);
                $list[] = $intervenant;
            }
            return $list;
        }
        
        return array();
    }
    
    /**
     * Met à jour le nombre d'intervenants actifs liés à cet organisme
     *
     * @return int     <0 si erreur, >=0 si OK (nombre d'intervenants)
     */
    public function updateNombreIntervenantsActifs()
    {
        $sql = 'SELECT COUNT(*) as nb FROM '.MAIN_DB_PREFIX.'elaska_intervenant';
        $sql.= ' WHERE fk_elaska_organisme = '.(int) $this->id;
        $sql.= ' AND entity IN ('.getEntity('elaska_intervenant').')';
        $sql.= ' AND statut_collaboration_elaska_code = "ACTIF"';
        
        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            $this->nombre_intervenants_actifs = $obj->nb;
            
            // Mise à jour en base
            $sql = 'UPDATE '.MAIN_DB_PREFIX.$this->table_element;
            $sql .= ' SET nombre_intervenants_actifs = '.$this->nombre_intervenants_actifs;
            $sql .= ' WHERE rowid = '.$this->id;
            
            $result = $this->db->query($sql);
            if ($result) {
                return $this->nombre_intervenants_actifs;
            } else {
                $this->error = $this->db->lasterror();
                return -1;
            }
        }
        
        $this->error = $this->db->lasterror();
        return -1;
    }
    
    /**
     * Met à jour les statistiques globales de l'organisme
     *
     * @return int     <0 si erreur, >0 si OK
     */
    public function updateStatistiques()
    {
        global $user;
        
        // Mise à jour du nombre d'intervenants actifs
        if ($this->updateNombreIntervenantsActifs() < 0) {
            return -1;
        }
        
        // Autres mises à jour statistiques à implémenter selon les besoins
        // Exemples: nombre_missions_realisees, montant_ca_realise, note_satisfaction_moyenne
        
        $this->fk_user_modif = $user->id;
        $result = $this->update($user, 1); // 1 = ne pas déclencher de trigger
        
        return $result;
    }
}

} // Fin de la condition if !class_exists
?>