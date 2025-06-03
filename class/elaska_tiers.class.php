<?php
/**
 * eLaska - Classe pour gérer les tiers (clients) spécifiques à eLaska
 * Date: 2025-05-30
 * Version: 3.0 (Version finale pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';

if (!class_exists('ElaskaTiers', false)) {

class ElaskaTiers extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_tiers';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_tiers';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'user@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var string Référence unique du tiers eLaska
     */
    public $ref;
    
    /**
     * @var string Code du type de client (dictionnaire)
     */
    public $type_client_code;
    
    /**
     * @var string Nom du tiers
     */
    public $nom;
    
    /**
     * @var int ID de la société Dolibarr liée
     */
    public $fk_soc;
    
    /**
     * @var string Code de la situation client (dictionnaire)
     */
    public $situation_client_code;
    
    /**
     * @var string Objectif de la collaboration
     */
    public $objectif_collaboration;
    
    /**
     * @var int ID de l'utilisateur conseiller
     */
    public $fk_user_conseiller;
    
    /**
     * @var int Score d'engagement (0-100)
     */
    public $score_engagement;
    
    /**
     * @var string Date de dernière interaction (format MySQL)
     */
    public $date_derniere_interaction;
    
    /**
     * @var string Date du premier contact (format MySQL)
     */
    public $date_premier_contact;
    
    /**
     * @var string Fréquence de contact souhaitée
     */
    public $frequence_contact_souhaite;
    
    /**
     * @var string Canal de communication préféré
     */
    public $canal_communication_prefere;
    
    /**
     * @var string Langue préférée (format ISO)
     */
    public $langue_preferee;
    
    /**
     * @var string Horaires de contact préférés
     */
    public $horaires_contact_preferes;
    
    /**
     * @var float Budget services annuel
     */
    public $budget_services_annuel;
    
    /**
     * @var string Source d'acquisition du client
     */
    public $source_acquisition;
    
    /**
     * @var string Campagne d'acquisition
     */
    public $campagne;
    
    /**
     * @var string Notes générales
     */
    public $notes_generales;
    
    /**
     * @var string Remarques internes
     */
    public $remarques_internes;
    
    /**
     * @var string Tags (séparés par virgules)
     */
    public $tags;
    
    /**
     * @var int Portail client activé (0/1)
     */
    public $portail_active;
    
    /**
     * @var string Date de dernière connexion au portail
     */
    public $portail_date_derniere_connexion;
    
    /**
     * @var string Niveau d'accès au portail
     */
    public $portail_niveau_acces;
    
    /**
     * @var int Abonnement newsletter (0/1)
     */
    public $abonnement_newsletter;
    
    /**
     * @var int Consentement marketing (0/1)
     */
    public $consentements_marketing;
    
    /**
     * @var int Consentement données sensibles (0/1)
     */
    public $consentements_donnees_sensibles;
    
    /**
     * @var int Consentement partage partenaires (0/1)
     */
    public $consentements_partage_partenaires;
    
    /**
     * @var string Date limite de conservation des données
     */
    public $date_limite_conservation;

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
        'ref' => array('type' => 'varchar(128)', 'label' => 'Ref', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1, 'unique' => 1),
        'type_client_code' => array('type' => 'varchar(50)', 'label' => 'ClientType', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'nom' => array('type' => 'varchar(255)', 'label' => 'Name', 'enabled' => 1, 'position' => 30, 'notnull' => 1, 'visible' => 1),
        'fk_soc' => array('type' => 'integer:Societe:societe/class/societe.class.php:1', 'label' => 'ThirdParty', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'situation_client_code' => array('type' => 'varchar(50)', 'label' => 'ElaskaClientSituation', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'objectif_collaboration' => array('type' => 'text', 'label' => 'CollaborationObjective', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'fk_user_conseiller' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'ReferentAdviser', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'score_engagement' => array('type' => 'integer', 'label' => 'EngagementScore', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'date_derniere_interaction' => array('type' => 'datetime', 'label' => 'LastInteractionDate', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'date_premier_contact' => array('type' => 'date', 'label' => 'FirstContactDate', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'frequence_contact_souhaite' => array('type' => 'varchar(50)', 'label' => 'DesiredContactFrequency', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'canal_communication_prefere' => array('type' => 'varchar(50)', 'label' => 'PreferredCommunicationChannel', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'langue_preferee' => array('type' => 'varchar(5)', 'label' => 'PreferredLanguage', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'horaires_contact_preferes' => array('type' => 'varchar(255)', 'label' => 'PreferredContactHours', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'budget_services_annuel' => array('type' => 'double(24,8)', 'label' => 'AnnualServicesBudget', 'enabled' => 1, 'position' => 150, 'notnull' => 0, 'visible' => 1),
        'source_acquisition' => array('type' => 'varchar(50)', 'label' => 'AcquisitionSource', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        'campagne' => array('type' => 'varchar(128)', 'label' => 'Campaign', 'enabled' => 1, 'position' => 170, 'notnull' => 0, 'visible' => 1),
        'notes_generales' => array('type' => 'text', 'label' => 'GeneralNotes', 'enabled' => 1, 'position' => 180, 'notnull' => 0, 'visible' => 1),
        'remarques_internes' => array('type' => 'text', 'label' => 'InternalComments', 'enabled' => 1, 'position' => 190, 'notnull' => 0, 'visible' => 0),
        'tags' => array('type' => 'varchar(255)', 'label' => 'Tags', 'enabled' => 1, 'position' => 200, 'notnull' => 0, 'visible' => 1),
        'portail_active' => array('type' => 'boolean', 'label' => 'PortalActive', 'enabled' => 1, 'position' => 210, 'notnull' => 1, 'visible' => 1, 'default' => 0),
        'portail_date_derniere_connexion' => array('type' => 'datetime', 'label' => 'LastPortalConnection', 'enabled' => 1, 'position' => 220, 'notnull' => 0, 'visible' => 1),
        'portail_niveau_acces' => array('type' => 'varchar(50)', 'label' => 'PortalAccessLevel', 'enabled' => 1, 'position' => 230, 'notnull' => 0, 'visible' => 1),
        'abonnement_newsletter' => array('type' => 'boolean', 'label' => 'NewsletterSubscription', 'enabled' => 1, 'position' => 240, 'notnull' => 1, 'visible' => 1, 'default' => 0),
        'consentements_marketing' => array('type' => 'boolean', 'label' => 'MarketingConsent', 'enabled' => 1, 'position' => 250, 'notnull' => 1, 'visible' => 1, 'default' => 0),
        'consentements_donnees_sensibles' => array('type' => 'boolean', 'label' => 'SensitiveDataConsent', 'enabled' => 1, 'position' => 260, 'notnull' => 1, 'visible' => 1, 'default' => 0),
        'consentements_partage_partenaires' => array('type' => 'boolean', 'label' => 'PartnersDataSharingConsent', 'enabled' => 1, 'position' => 270, 'notnull' => 1, 'visible' => 1, 'default' => 0),
        'date_limite_conservation' => array('type' => 'date', 'label' => 'DataRetentionLimit', 'enabled' => 1, 'position' => 280, 'notnull' => 0, 'visible' => 1),
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'visible' => 0, 'enabled' => 1, 'position' => 900, 'notnull' => 1, 'default' => '1'),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'visible' => 0, 'enabled' => 1, 'position' => 910, 'notnull' => 1),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'visible' => 0, 'enabled' => 1, 'position' => 920, 'notnull' => 1),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'visible' => 0, 'enabled' => 1, 'position' => 930),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'visible' => 0, 'enabled' => 1, 'position' => 940),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'visible' => 0, 'enabled' => 1, 'position' => 950),
        'status' => array('type' => 'integer', 'label' => 'Status', 'visible' => 1, 'enabled' => 1, 'position' => 1000, 'notnull' => 1, 'default' => '1', 'arrayofkeyval' => array('0' => 'Inactive', '1' => 'Active'))
    );
    
    /**
     * @var array Objets enfants associés (élément => objet)
     */
    protected $childtables = array();
    
    /**
     * Tableau des objets enfants chargés (cache)
     */
    protected $children = array();

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        global $conf;
        
        parent::__construct($db);
        
        // Par défaut, le tiers est actif
        if (!isset($this->status)) $this->status = 1;
        
        // Initialiser les tables enfants selon le type de client
        $this->childtables = array(
            'particulier' => 'elaska_particulier',
            'association' => 'elaska_association',
            'entreprise' => 'elaska_entreprise',
            'createur' => 'elaska_createur',
            'intervenant' => 'elaska_intervenant',
            'organisme' => 'elaska_organisme'
        );
    }

    /**
     * Crée un tiers dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        global $conf;
        $error = 0;
        $this->db->begin();
        
        // La référence sera générée par ElaskaNumero
        $this->ref = '';
        
        // Vérification des champs obligatoires
        if (empty($this->type_client_code)) {
            $this->error = "Le type de client est obligatoire";
            $this->db->rollback();
            return -1;
        }
        
        if (empty($this->nom)) {
            $this->error = "Le nom du tiers est obligatoire";
            $this->db->rollback();
            return -1;
        }
        
        $result = $this->createCommon($user, $notrigger);
        
        if ($result > 0 && class_exists('ElaskaNumero')) {
            // Génération de la référence avec ElaskaNumero
            $params = array();
            
            // Ajouter des paramètres spécifiques pour le masque de numérotation
            if (!empty($this->type_client_code)) {
                $params['TYPE_CLIENT'] = $this->type_client_code;
            }
            
            $final_ref = ElaskaNumero::generateAndRecord(
                $this->db,
                'elaska',
                $this->element,
                $this->id,
                '', // Utiliser le masque par défaut
                $params,
                $this->entity
            );
            
            if ($final_ref !== -1 && !empty($final_ref)) {
                $this->ref = $final_ref;
                if (!$this->updateRef($user)) {
                    $error++;
                    dol_syslog("ElaskaTiers::create Failed to update definitive ref for ".$this->element." ".$this->id, LOG_ERR);
                }
            } else {
                $error++;
                dol_syslog("ElaskaTiers::create Failed to generate/record definitive ref for ".$this->element." ".$this->id, LOG_ERR);
            }
        }
        
        if ($result < 0) {
            $error++;
        }
        
        if (!$error) {
            $this->db->commit();
            return $this->id;
        } else {
            $this->db->rollback();
            return -1;
        }
    }

    /**
     * Met à jour uniquement la référence après génération par ElaskaNumero
     *
     * @param User $user Utilisateur qui effectue la mise à jour
     * @return bool      true si succès, false si erreur
     */
    public function updateRef($user)
    {
        if (empty($this->id) || empty($this->ref)) return false;
        
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
        $sql .= " SET ref = '".$this->db->escape($this->ref)."'";
        
        if (is_object($user)) {
            $sql .= ", fk_user_modif = ".(int)$user->id;
        }
        
        $sql .= ", tms = '".$this->db->idate(dol_now())."'";
        $sql .= " WHERE rowid = ".(int)$this->id;
        
        dol_syslog(get_class($this)."::updateRef", LOG_DEBUG);
        $resql = $this->db->query($sql);
        
        if ($resql) {
            return true;
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this)."::updateRef ".$this->error, LOG_ERR);
            return false;
        }
    }

    /**
     * Charge un tiers depuis la base de données
     *
     * @param int    $id  ID du tiers
     * @param string $ref Référence du tiers
     * @return int        <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = null)
    {
        $result = $this->fetchCommon($id, $ref);

        // Chargement des données spécifiques selon le type de client
        if ($result > 0 && !empty($this->type_client_code)) {
            $this->loadTypeSpecificData();
        }

        return $result;
    }

    /**
     * Met à jour un tiers dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        return $this->updateCommon($user, $notrigger);
    }

    /**
     * Supprime un tiers de la base de données
     * et les données spécifiques associées
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        $this->db->begin();
        $error = 0;

        // Suppression des données spécifiques au type de client
        if (!$error && !empty($this->type_client_code)) {
            $error += !$this->deleteTypeSpecificData();
        }
        
        // Suppression des objets liés (dossiers, mandats, etc.)
        if (!$error) {
            $error += $this->deleteLinkedObjects($user);
        }
        
        // Suppression du tiers
        if (!$error) {
            $res = $this->deleteCommon($user, $notrigger);
            if ($res <= 0) $error++;
        }

        if ($error) {
            $this->db->rollback();
            return -1;
        } else {
            $this->db->commit();
            return 1;
        }
    }

    /**
     * Charge les données spécifiques au type de client
     * 
     * @return bool true si OK, false si erreur
     */
    protected function loadTypeSpecificData()
    {
        if (empty($this->type_client_code) || empty($this->id)) {
            return false;
        }

        if (!isset($this->childtables[$this->type_client_code])) {
            return false; // Type non supporté
        }

        $tablename = $this->childtables[$this->type_client_code];
        
        $sql = "SELECT * FROM ".MAIN_DB_PREFIX.$tablename;
        $sql.= " WHERE fk_elaska_tiers = ".(int)$this->id;
        
        $resql = $this->db->query($sql);
        if ($resql && ($obj = $this->db->fetch_object($resql))) {
            // Charger les propriétés dynamiquement
            foreach($obj as $key => $value) {
                $this->{$key} = $value;
            }
            
            // Mémoriser l'objet enfant dans le cache
            $this->children[$this->type_client_code] = $obj;
            return true;
        }
        
        return false;
    }

    /**
     * Supprime les données spécifiques au type de client
     * 
     * @return bool true si OK, false si erreur
     */
    protected function deleteTypeSpecificData()
    {
        if (empty($this->type_client_code) || empty($this->id)) {
            return false;
        }

        if (!isset($this->childtables[$this->type_client_code])) {
            return true; // Type non supporté, considéré comme succès
        }

        $tablename = $this->childtables[$this->type_client_code];
        
        $sql = "DELETE FROM ".MAIN_DB_PREFIX.$tablename;
        $sql.= " WHERE fk_elaska_tiers = ".(int)$this->id;
        
        if ($this->db->query($sql)) {
            return true;
        } else {
            $this->error = "Erreur lors de la suppression des données spécifiques: ".$this->db->lasterror();
            return false;
        }
    }
    
    /**
     * Supprime les objets liés (dossiers, mandats, communications, etc.)
     * 
     * @param User $user Utilisateur qui effectue l'action
     * @return int       0 si OK, nombre d'erreurs sinon
     */
    protected function deleteLinkedObjects($user)
    {
        $error = 0;

        // 1. Supprimer les dossiers liés
        $this->deleteDossiers($user, $error);
        
        // 2. Supprimer les mandats liés
        $this->deleteMandats($user, $error);
        
        // 3. Supprimer les communications liées
        $this->deleteCommunications($user, $error);
        
        // 4. Supprimer les documents liés
        $this->deleteDocuments($user, $error);
        
        // 5. Supprimer les contrats de service liés
        $this->deleteContratServices($user, $error);
        
        // 6. Supprimer les notifications liées
        $this->deleteNotifications($user, $error);

        return $error;
    }
    
    /**
     * Supprime les dossiers liés à ce tiers
     * 
     * @param User $user  Utilisateur qui effectue l'action
     * @param int  &$error Compteur d'erreurs
     */
    protected function deleteDossiers($user, &$error)
    {
        global $conf;
        
        // Charger la classe ElaskaDossier
        $classfile = DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dossier.class.php';
        if (!file_exists($classfile)) return;
        
        require_once $classfile;
        
        // Rechercher les dossiers liés à ce tiers
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_dossier";
        $sql.= " WHERE fk_elaska_tiers = ".(int)$this->id;
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                // Supprimer chaque dossier
                $dossier = new ElaskaDossier($this->db);
                if ($dossier->fetch($obj->rowid) > 0) {
                    if ($dossier->delete($user) <= 0) {
                        $error++;
                        $this->errors[] = "Erreur lors de la suppression du dossier ID ".$obj->rowid;
                    }
                }
            }
            $this->db->free($resql);
        }
    }
    
    /**
     * Supprime les mandats liés à ce tiers
     * 
     * @param User $user  Utilisateur qui effectue l'action
     * @param int  &$error Compteur d'erreurs
     */
    protected function deleteMandats($user, &$error)
    {
        global $conf;
        
        // Charger la classe ElaskaMandat
        $classfile = DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_mandat.class.php';
        if (!file_exists($classfile)) return;
        
        require_once $classfile;
        
        // Rechercher les mandats liés à ce tiers
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_mandat";
        $sql.= " WHERE fk_elaska_tiers = ".(int)$this->id;
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                // Supprimer chaque mandat
                $mandat = new ElaskaMandat($this->db);
                if ($mandat->fetch($obj->rowid) > 0) {
                    if ($mandat->delete($user) <= 0) {
                        $error++;
                        $this->errors[] = "Erreur lors de la suppression du mandat ID ".$obj->rowid;
                    }
                }
            }
            $this->db->free($resql);
        }
    }
    
    /**
     * Supprime les communications liées à ce tiers
     * 
     * @param User $user  Utilisateur qui effectue l'action
     * @param int  &$error Compteur d'erreurs
     */
    protected function deleteCommunications($user, &$error)
    {
        global $conf;
        
        // Charger la classe ElaskaCommunication
        $classfile = DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_communication.class.php';
        if (!file_exists($classfile)) return;
        
        require_once $classfile;
        
        // Rechercher les communications liées à ce tiers
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_communication";
        $sql.= " WHERE fk_elaska_tiers = ".(int)$this->id;
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                // Supprimer chaque communication
                $comm = new ElaskaCommunication($this->db);
                if ($comm->fetch($obj->rowid) > 0) {
                    if ($comm->delete($user) <= 0) {
                        $error++;
                        $this->errors[] = "Erreur lors de la suppression de la communication ID ".$obj->rowid;
                    }
                }
            }
            $this->db->free($resql);
        }
    }
    
    /**
     * Supprime les documents liés à ce tiers
     * 
     * @param User $user  Utilisateur qui effectue l'action
     * @param int  &$error Compteur d'erreurs
     */
    protected function deleteDocuments($user, &$error)
    {
        global $conf;
        
        // Charger la classe ElaskaDocument
        $classfile = DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
        if (!file_exists($classfile)) return;
        
        require_once $classfile;
        
        // Rechercher les documents liés à ce tiers
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_document";
        $sql.= " WHERE fk_elaska_tiers = ".(int)$this->id;
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                // Supprimer chaque document
                $doc = new ElaskaDocument($this->db);
                if ($doc->fetch($obj->rowid) > 0) {
                    if ($doc->delete($user) <= 0) {
                        $error++;
                        $this->errors[] = "Erreur lors de la suppression du document ID ".$obj->rowid;
                    }
                }
            }
            $this->db->free($resql);
        }
    }
    
    /**
     * Supprime les contrats de service liés à ce tiers
     * 
     * @param User $user  Utilisateur qui effectue l'action
     * @param int  &$error Compteur d'erreurs
     */
    protected function deleteContratServices($user, &$error)
    {
        global $conf;
        
        // Charger la classe ElaskaContratService
        $classfile = DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_contrat_service.class.php';
        if (!file_exists($classfile)) return;
        
        require_once $classfile;
        
        // Rechercher les contrats liés à ce tiers
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_contrat_service";
        $sql.= " WHERE fk_elaska_tiers = ".(int)$this->id;
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                // Supprimer chaque contrat
                $contrat = new ElaskaContratService($this->db);
                if ($contrat->fetch($obj->rowid) > 0) {
                    if ($contrat->delete($user) <= 0) {
                        $error++;
                        $this->errors[] = "Erreur lors de la suppression du contrat ID ".$obj->rowid;
                    }
                }
            }
            $this->db->free($resql);
        }
    }
    
    /**
     * Supprime les notifications liées à ce tiers
     * 
     * @param User $user  Utilisateur qui effectue l'action
     * @param int  &$error Compteur d'erreurs
     */
    protected function deleteNotifications($user, &$error)
    {
        global $conf;
        
        // Charger la classe ElaskaNotification
        $classfile = DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_notification.class.php';
        if (!file_exists($classfile)) return;
        
        require_once $classfile;
        
        // Rechercher les notifications liées à ce tiers
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_notification";
        $sql.= " WHERE fk_elaska_tiers = ".(int)$this->id;
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                // Supprimer chaque notification
                $notif = new ElaskaNotification($this->db);
                if ($notif->fetch($obj->rowid) > 0) {
                    if ($notif->delete($user) <= 0) {
                        $error++;
                        $this->errors[] = "Erreur lors de la suppression de la notification ID ".$obj->rowid;
                    }
                }
            }
            $this->db->free($resql);
        }
    }

    /**
     * Recherche un ElaskaTiers par l'ID de la société Dolibarr
     *
     * @param int $socid ID de la société
     * @return int <0 si erreur, 0 si non trouvé, >0 si trouvé
     */
    public function fetchBySoc($socid)
    {
        if (empty($socid)) return -1;
        
        $sql = 'SELECT t.rowid FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
        $sql.= ' WHERE t.fk_soc = '.(int) $socid;
        $sql.= ' AND t.entity IN ('.getEntity($this->element).')';
        
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($obj = $this->db->fetch_object($resql)) {
                return $this->fetch($obj->rowid);
            }
            return 0; // Rien trouvé
        }
        
        $this->error = $this->db->lasterror();
        return -1; // Erreur
    }
    
    /**
     * Retourne les options des types de client depuis le dictionnaire
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getClientTypeOptions($langs, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_type_client";
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
     * Retourne le label d'un type de client
     *
     * @param string    $type_code Code du type
     * @param Translate $langs      Objet de traduction
     * @return string               Label du type
     */
    public static function getClientTypeLabel($type_code, $langs)
    {
        $types = self::getClientTypeOptions($langs, true);
        return isset($types[$type_code]) ? $types[$type_code] : $langs->trans('Unknown');
    }
    
    /**
     * Retourne les options des situations client depuis le dictionnaire
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSituationClientOptions($langs, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_situation_client";
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
     * Récupère les fréquences de contact souhaitées
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getFrequenceContactOptions($langs, $usekeys = true, $show_empty = false)
    {
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $frequences = array(
            'quotidien' => 'Daily',
            'hebdomadaire' => 'Weekly',
            'bi_hebdomadaire' => 'TwiceWeekly',
            'mensuel' => 'Monthly',
            'trimestriel' => 'Quarterly',
            'semestriel' => 'BiAnnual',
            'annuel' => 'Annual',
            'sur_demande' => 'OnDemand'
        );
        
        foreach ($frequences as $key => $val) {
            if ($usekeys) {
                $options[$key] = $langs->trans($val);
            } else {
                $obj_option = new stdClass();
                $obj_option->code = $key;
                $obj_option->label = $val;
                $obj_option->label_translated = $langs->trans($val);
                $options[] = $obj_option;
            }
        }
        
        return $options;
    }
    
    /**
     * Ajoute un message d'erreur spécifique
     *
     * @param string $msg Message d'erreur
     */
    public function addError($msg)
    {
        $this->error = $msg;
        $this->errors[] = $msg;
    }
}
}
?>
