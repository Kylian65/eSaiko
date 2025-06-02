<?php
/**
 * eLaska - Classe pour gérer les documents des clients
 * Date: 2025-06-01
 * Version: 4.2 (Version améliorée avec liaison générique aux objets)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';
require_once DOL_DOCUMENT_ROOT.'/ecm/class/ecmfiles.class.php';

if (!class_exists('ElaskaDocument', false)) {

class ElaskaDocument extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_document';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_document';
    
    /**
     * @var string Icône utilisée
     */
    public $picto = 'file-text-o@elaska';
    
    /**
     * @var int Flag indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    // --- Champs de la table --- 
    /**
     * @var int ID technique
     */
    public $rowid;
    
    /**
     * @var string Référence unique du document
     */
    public $ref;
    
    /**
     * @var int ID du tiers eLaska concerné (peut être null si lié à un autre objet spécifique)
     */
    public $fk_elaska_tiers;
    
    /**
     * @var int ID d'un objet lié (générique pour liaison avec tout type d'objet)
     */
    public $fk_object;
    
    /**
     * @var string Type de l'objet lié (element name, ex: 'elaska_intervenant', 'elaska_organisme')
     */
    public $object_element;
    
    /**
     * @var string Code du type de document (dictionnaire)
     */
    public $type_document_code;
    
    /**
     * @var string Code du sous-type de document (dictionnaire)
     */
    public $sous_type_code;
    
    /**
     * @var string Libellé du document
     */
    public $libelle;
    
    /**
     * @var string Description détaillée
     */
    public $description;
    
    /**
     * @var string Date du document (YYYY-MM-DD)
     */
    public $date_document;
    
    /**
     * @var string Date d'ajout dans le système (YYYY-MM-DD HH:MM:SS)
     */
    public $date_ajout;
    
    /**
     * @var string Date d'expiration du document (YYYY-MM-DD)
     */
    public $date_expiration;
    
    /**
     * @var int ID de l'utilisateur qui a téléversé le document
     */
    public $fk_user_upload;
    
    /**
     * @var string Nom du fichier original
     */
    public $filename;
    
    /**
     * @var string Chemin relatif de stockage
     */
    public $filepath;
    
    /**
     * @var int Taille du fichier en octets
     */
    public $filesize;
    
    /**
     * @var string Type MIME du fichier
     */
    public $mimetype;
    
    /**
     * @var string Hash MD5 du fichier pour vérification d'intégrité
     */
    public $file_hash;
    
    /**
     * @var int Indique si c'est l'original (1) ou une copie (0)
     */
    public $est_original;
    
    /**
     * @var int Indique si le document est confidentiel (1) ou non (0)
     */
    public $est_confidentiel;
    
    /**
     * @var string Code du niveau d'accès (dictionnaire)
     */
    public $niveau_acces_code;
    
    /**
     * @var string Code du statut de validation (dictionnaire)
     */
    public $statut_validation_code;
    
    /**
     * @var string Commentaire lors de la validation
     */
    public $commentaire_validation;
    
    /**
     * @var int ID de l'utilisateur validateur
     */
    public $fk_user_validateur;
    
    /**
     * @var string Date de validation (YYYY-MM-DD HH:MM:SS)
     */
    public $date_validation;
    
    /**
     * @var string Code de la destination légale (dictionnaire)
     */
    public $dest_legale_code;
    
    /**
     * @var int Durée de conservation en années
     */
    public $duree_conservation;
    
    /**
     * @var string Date de fin de conservation (YYYY-MM-DD)
     */
    public $date_fin_conservation;
    
    /**
     * @var int ID de la démarche eLaska associée
     */
    public $fk_elaska_demarche;
    
    /**
     * @var string Tags associés au document
     */
    public $tags;
    
    /**
     * @var string Token pour URL de téléchargement public
     */
    public $public_download_url_token;
    
    /**
     * @var string Date d'expiration de l'URL publique (YYYY-MM-DD HH:MM:SS)
     */
    public $public_url_expiration;
    
    /**
     * @var int Nombre de téléchargements publics
     */
    public $nb_downloads;
    
    /**
     * @var string Date du dernier téléchargement (YYYY-MM-DD HH:MM:SS)
     */
    public $last_download_date;
    
    /**
     * @var int Indique si c'est un modèle (1) ou un document normal (0)
     */
    public $is_template;
    
    /**
     * @var string Version du document
     */
    public $version;
    
    /**
     * @var int ID du document parent (pour versions multiples)
     */
    public $fk_document_parent;
    
    /**
     * @var int Indique si les alertes d'expiration sont actives (1) ou non (0)
     */
    public $alerte_expiration_active;
    
    /**
     * @var int Délai en jours avant l'expiration pour alerter
     */
    public $delai_alerte_expiration_jours;
    
    /**
     * @var int Nombre de pages du document (pour PDF notamment)
     */
    public $nb_pages;
    
    /**
     * @var int Indique si le document est signé électroniquement (1) ou non (0)
     */
    public $est_signe_electroniquement;
    
    /**
     * @var string Date de signature électronique (YYYY-MM-DD HH:MM:SS)
     */
    public $date_signature_electronique;
    
    /**
     * @var string Informations sur la signature électronique
     */
    public $infos_signature_electronique;
    
    /**
     * @var string Détails du certificat de signature
     */
    public $certificat_signature_details;
    
    /**
     * @var string Emplacement de stockage physique (pour documents papier)
     */
    public $emplacement_stockage_physique;
    
    /**
     * @var string Commentaires généraux
     */
    public $commentaires_generaux;

    // Champs techniques standard
    public $entity;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $status;

    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type'=>'integer', 'label'=>'ID', 'visible'=>0, 'enabled'=>1, 'position'=>1, 'notnull'=>1, 'primary'=>1),
        'ref' => array('type'=>'varchar(128)', 'label'=>'RefDocument', 'visible'=>1, 'enabled'=>1, 'position'=>5, 'notnull'=>0, 'unique'=>1),
        'fk_elaska_tiers' => array('type'=>'integer:ElaskaTiers:custom/elaska/class/elaska_tiers.class.php', 'label'=>'TiersElaskaConcerne', 'visible'=>1, 'enabled'=>1, 'position'=>10, 'notnull'=>0),
        'fk_object' => array('type'=>'integer', 'label'=>'IDObjetLie', 'visible'=>1, 'enabled'=>1, 'position'=>12),
        'object_element' => array('type'=>'varchar(50)', 'label'=>'TypeObjetLie', 'visible'=>1, 'enabled'=>1, 'position'=>13),
        'type_document_code' => array('type'=>'varchar(50)', 'label'=>'TypeDocument', 'visible'=>1, 'enabled'=>1, 'position'=>15, 'notnull'=>1),
        'sous_type_code' => array('type'=>'varchar(50)', 'label'=>'SousTypeDocument', 'visible'=>1, 'enabled'=>1, 'position'=>20),
        'libelle' => array('type'=>'varchar(255)', 'label'=>'LibelleDocument', 'visible'=>1, 'enabled'=>1, 'position'=>25, 'notnull'=>1),
        'description' => array('type'=>'text', 'label'=>'DescriptionDocument', 'visible'=>1, 'enabled'=>1, 'position'=>30),
        'date_document' => array('type'=>'date', 'label'=>'DateDuDocument', 'visible'=>1, 'enabled'=>1, 'position'=>35),
        'date_ajout' => array('type'=>'datetime', 'label'=>'DateAjoutSysteme', 'visible'=>1, 'enabled'=>0, 'position'=>40),
        'date_expiration' => array('type'=>'date', 'label'=>'DateExpirationDocument', 'visible'=>1, 'enabled'=>1, 'position'=>45),
        'fk_user_upload' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UtilisateurUpload', 'visible'=>1, 'enabled'=>0, 'position'=>50),
        'filename' => array('type'=>'varchar(255)', 'label'=>'NomFichierOriginal', 'visible'=>1, 'enabled'=>1, 'position'=>55),
        'filepath' => array('type'=>'varchar(255)', 'label'=>'CheminStockageRelatif', 'visible'=>0, 'enabled'=>1, 'position'=>60),
        'filesize' => array('type'=>'integer', 'label'=>'TailleFichierOctets', 'visible'=>1, 'enabled'=>1, 'position'=>65),
        'mimetype' => array('type'=>'varchar(128)', 'label'=>'TypeMIME', 'visible'=>1, 'enabled'=>1, 'position'=>70),
        'file_hash' => array('type'=>'varchar(255)', 'label'=>'HashFichier', 'visible'=>0, 'enabled'=>1, 'position'=>75),
        'est_original' => array('type'=>'boolean', 'label'=>'EstOriginalPhysique', 'visible'=>1, 'enabled'=>1, 'position'=>80, 'default'=>'1'),
        'est_confidentiel' => array('type'=>'boolean', 'label'=>'EstConfidentiel', 'visible'=>1, 'enabled'=>1, 'position'=>85, 'default'=>'0'),
        'niveau_acces_code' => array('type'=>'varchar(30)', 'label'=>'NiveauAccesDocument', 'visible'=>1, 'enabled'=>1, 'position'=>90, 'default'=>'STANDARD_INTERNE'),
        'statut_validation_code' => array('type'=>'varchar(30)', 'label'=>'StatutValidationDocument', 'visible'=>1, 'enabled'=>1, 'position'=>95, 'default'=>'A_VERIFIER'),
        'commentaire_validation' => array('type'=>'text', 'label'=>'CommentaireValidation', 'visible'=>1, 'enabled'=>1, 'position'=>100),
        'fk_user_validateur' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UtilisateurValidateur', 'visible'=>1, 'enabled'=>1, 'position'=>105),
        'date_validation' => array('type'=>'datetime', 'label'=>'DateValidation', 'visible'=>1, 'enabled'=>1, 'position'=>110),
        'dest_legale_code' => array('type'=>'varchar(50)', 'label'=>'DestinationLegaleDocument', 'visible'=>1, 'enabled'=>1, 'position'=>115),
        'duree_conservation' => array('type'=>'integer', 'label'=>'DureeConservationAnnees', 'visible'=>1, 'enabled'=>1, 'position'=>120),
        'date_fin_conservation' => array('type'=>'date', 'label'=>'DateFinConservation', 'visible'=>1, 'enabled'=>1, 'position'=>125),
        'fk_elaska_demarche' => array('type'=>'integer:ElaskaDemarche:custom/elaska/class/elaska_demarche.class.php', 'label'=>'DemarcheLieeID', 'visible'=>1, 'enabled'=>1, 'position'=>130),
        'tags' => array('type'=>'varchar(255)', 'label'=>'TagsDocument', 'visible'=>1, 'enabled'=>1, 'position'=>145),
        'public_download_url_token' => array('type'=>'varchar(255)', 'label'=>'TokenURLPublique', 'visible'=>0, 'enabled'=>1, 'position'=>150),
        'public_url_expiration' => array('type'=>'datetime', 'label'=>'DateExpirationURLPublique', 'visible'=>0, 'enabled'=>1, 'position'=>155),
        'nb_downloads' => array('type'=>'integer', 'label'=>'NombreTelechargementsPublics', 'visible'=>0, 'enabled'=>1, 'position'=>160, 'default'=>'0'),
        'last_download_date' => array('type'=>'datetime', 'label'=>'DateDernierTelechargementPublic', 'visible'=>0, 'enabled'=>1, 'position'=>165),
        'is_template' => array('type'=>'boolean', 'label'=>'EstUnModeleDocument', 'visible'=>1, 'enabled'=>1, 'position'=>170, 'default'=>'0'),
        'version' => array('type'=>'varchar(20)', 'label'=>'VersionDocument', 'visible'=>1, 'enabled'=>1, 'position'=>175, 'default'=>'1.0'),
        'fk_document_parent' => array('type'=>'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label'=>'DocumentParentIDVersion', 'visible'=>1, 'enabled'=>1, 'position'=>180),
        'alerte_expiration_active' => array('type'=>'boolean', 'label'=>'AlerteExpirationActive', 'visible'=>1, 'enabled'=>1, 'position'=>185, 'default'=>'0'),
        'delai_alerte_expiration_jours' => array('type'=>'integer', 'label'=>'DelaiAlerteExpirationJours', 'visible'=>1, 'enabled'=>1, 'position'=>190, 'default'=>'30'),
        'nb_pages' => array('type'=>'integer', 'label'=>'NombreDePages', 'visible'=>1, 'enabled'=>1, 'position'=>195),
        'est_signe_electroniquement' => array('type'=>'boolean', 'label'=>'EstSigneElectroniquement', 'visible'=>1, 'enabled'=>1, 'position'=>200, 'default'=>'0'),
        'date_signature_electronique' => array('type'=>'datetime', 'label'=>'DateSignatureElectronique', 'visible'=>1, 'enabled'=>1, 'position'=>205),
        'infos_signature_electronique' => array('type'=>'text', 'label'=>'InformationsSignatureElectronique', 'visible'=>1, 'enabled'=>1, 'position'=>210),
        'certificat_signature_details' => array('type'=>'text', 'label'=>'CertificatSignatureDetails', 'visible'=>1, 'enabled'=>1, 'position'=>215),
        'emplacement_stockage_physique' => array('type'=>'varchar(255)', 'label'=>'EmplacementStockagePhysique', 'visible'=>1, 'enabled'=>1, 'position'=>220),
        'commentaires_generaux' => array('type'=>'text', 'label'=>'CommentairesGenerauxDocument', 'visible'=>1, 'enabled'=>1, 'position'=>225),
        'entity' => array('type'=>'integer', 'label'=>'Entity', 'visible'=>0, 'enabled'=>1, 'position'=>900, 'notnull'=>1, 'default'=>'1'),
        'date_creation' => array('type'=>'datetime', 'label'=>'DateCreationRecord', 'visible'=>0, 'enabled'=>0, 'position'=>910),
        'tms' => array('type'=>'timestamp', 'label'=>'DateModificationRecord', 'visible'=>0, 'enabled'=>0, 'position'=>920),
        'fk_user_creat' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserAuthorRecord', 'visible'=>0, 'enabled'=>0, 'position'=>930),
        'fk_user_modif' => array('type'=>'integer:User:user/class/user.class.php', 'label'=>'UserModifRecord', 'visible'=>0, 'enabled'=>0, 'position'=>940),
        'import_key' => array('type'=>'varchar(14)', 'label'=>'ImportId', 'visible'=>0, 'enabled'=>1, 'position'=>950),
        'status' => array('type'=>'integer', 'label'=>'StatusRecord', 'visible'=>0, 'enabled'=>1, 'position'=>1000, 'notnull'=>1, 'default'=>'1')
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Initialisation des valeurs par défaut
        if (!isset($this->est_original)) $this->est_original = 1;
        if (!isset($this->est_confidentiel)) $this->est_confidentiel = 0;
        if (empty($this->niveau_acces_code)) $this->niveau_acces_code = 'STANDARD_INTERNE';
        if (empty($this->statut_validation_code)) $this->statut_validation_code = 'A_VERIFIER';
        if (!isset($this->is_template)) $this->is_template = 0;
        if (empty($this->version)) $this->version = '1.0';
        if (!isset($this->alerte_expiration_active)) $this->alerte_expiration_active = 0;
        if (empty($this->delai_alerte_expiration_jours) && $this->delai_alerte_expiration_jours !== 0) $this->delai_alerte_expiration_jours = 30;
        if (!isset($this->est_signe_electroniquement)) $this->est_signe_electroniquement = 0;
        if (!isset($this->nb_downloads)) $this->nb_downloads = 0;
        if (!isset($this->status)) $this->status = 1;
    }

    /**
     * Crée un document dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas triggers
     * @return int            <0 si erreur, >0 si OK
     */
    public function create($user, $notrigger = 0)
    {
        global $conf;
        
        // Vérifications des champs obligatoires
        if (empty($this->fk_elaska_tiers) && (empty($this->fk_object) || empty($this->object_element))) {
            $this->error = 'Un document doit être lié soit à un ElaskaTiers, soit à un objet spécifique (fk_object + object_element)';
            dol_syslog($this->error, LOG_ERR);
            return -1;
        }
        
        if (empty($this->libelle)) {
            $this->error = 'LibelleIsMandatory';
            dol_syslog($this->error, LOG_ERR);
            return -1;
        }
        
        if (empty($this->type_document_code)) {
            $this->error = 'TypeDocumentCodeIsMandatory';
            dol_syslog($this->error, LOG_ERR);
            return -1;
        }
        
        // Laisser la référence vide pour ElaskaNumero
        $this->ref = '';
        
        // Initialisation des dates si nécessaire
        if (empty($this->date_ajout)) $this->date_ajout = dol_now();
        if (empty($this->fk_user_upload) && is_object($user)) $this->fk_user_upload = $user->id;

        // Calcul de la date de fin de conservation si possible
        if (empty($this->date_fin_conservation) && !empty($this->date_document) 
            && !empty($this->duree_conservation) && $this->duree_conservation > 0) {
            try {
                $date_document_obj = new DateTime($this->date_document);
                $date_document_obj->add(new DateInterval('P'.$this->duree_conservation.'Y'));
                $this->date_fin_conservation = $date_document_obj->format('Y-m-d');
            } catch (Exception $e) {
                dol_syslog(get_class($this)."::create Error calculating date_fin_conservation: ".$e->getMessage(), LOG_WARNING);
            }
        }
        
        // Création de l'enregistrement dans la base
        $result = $this->createCommon($user, $notrigger);

        // Si la création a réussi, générer la référence définitive
        if ($result > 0 && class_exists('ElaskaNumero')) {
            $params = array();
            // Paramètres spécifiques pour le masque de référence
            if (!empty($this->type_document_code)) {
                $params['TYPE_DOC'] = $this->type_document_code;
            }
            if (!empty($this->fk_elaska_tiers)) {
                $params['ID_TIERS'] = $this->fk_elaska_tiers;
            }
            if (!empty($this->object_element)) {
                $params['OBJ_TYPE'] = $this->object_element;
            }
            if (!empty($this->fk_object)) {
                $params['OBJ_ID'] = $this->fk_object;
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
                     dol_syslog("ElaskaDocument::create Failed to update definitive ref for ".$this->element." ".$this->id, LOG_ERR);
                }
            } else {
                dol_syslog("ElaskaDocument::create Failed to generate/record definitive ref for ".$this->element." ".$this->id, LOG_ERR);
            }
        }
        
        return $result;
    }

    /**
     * Met à jour uniquement la référence après génération par ElaskaNumero
     *
     * @param User $user Utilisateur qui effectue la mise à jour
     * @return bool      true si succès, false si erreur
     */
    public function updateRef($user) 
    {
        if (empty($this->id) || !isset($this->ref)) return false;
        
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
        $sql .= " SET ref = '".$this->db->escape($this->ref)."'";
        
        if (is_object($user)) {
            $sql .= ", fk_user_modif = ".$user->id;
        }
        
        $sql .= ", tms = '".$this->db->idate(dol_now())."'";
        $sql .= " WHERE rowid = ".$this->id;
        
        if ($this->db->query($sql)) {
            return true;
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this)."::updateRef Failed: ".$this->error, LOG_ERR);
            return false;
        }
    }

    /**
     * Charge un document depuis la base de données
     *
     * @param int    $id  ID du document
     * @param string $ref Référence du document
     * @return int        <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = null)
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Met à jour un document dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas triggers
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Recalculer la date de fin de conservation si nécessaire
        if (!empty($this->date_document) && !empty($this->duree_conservation) && $this->duree_conservation > 0) {
            try {
                $date_document_obj = new DateTime($this->date_document);
                $date_document_obj->add(new DateInterval('P'.$this->duree_conservation.'Y'));
                $this->date_fin_conservation = $date_document_obj->format('Y-m-d');
            } catch (Exception $e) {
                dol_syslog(get_class($this)."::update Error calculating date_fin_conservation: ".$e->getMessage(), LOG_WARNING);
            }
        }
        
        return $this->updateCommon($user, $notrigger);
    }

    /**
     * Supprime un document de la base de données et le fichier physique associé si présent
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas triggers
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        global $conf;
        
        $this->db->begin();
        
        // Déterminer le chemin du fichier à supprimer
        $fullpath_to_delete = $this->getPathToFile(true);

        // Suppression du fichier physique s'il existe
        if (!empty($fullpath_to_delete) && file_exists($fullpath_to_delete) && !is_dir($fullpath_to_delete)) {
            if (!@unlink($fullpath_to_delete)) {
                $this->error = "Failed to delete physical file: ".$fullpath_to_delete;
                dol_syslog(get_class($this)."::delete ".$this->error, LOG_ERR);
                $this->db->rollback();
                return -1;
            }
        }
        
        // Suppression de l'enregistrement dans la base
        $result = $this->deleteCommon($user, $notrigger);
        if ($result < 0) {
            $this->db->rollback();
            return -2;
        }
        
        $this->db->commit();
        return 1;
    }
    
    /**
     * Construit et retourne le chemin de stockage pour un document
     * 
     * @param bool $full_path Si true, retourne le chemin complet, sinon seulement le chemin relatif
     * @return string Chemin de stockage
     */
    public function getPathToFile($full_path = false)
    {
        global $conf;
        
        // Détermine le répertoire de base
        $base_dir = (!empty($conf->elaska->dir_output) ? $conf->elaska->dir_output : DOL_DATA_ROOT.'/elaska');
        
        // Construit le chemin relatif
        $relative_path = $this->entity;
        
        if (!empty($this->fk_elaska_tiers)) {
            $relative_path .= '/tiers/' . $this->fk_elaska_tiers;
        } elseif (!empty($this->object_element) && !empty($this->fk_object)) {
            $relative_path .= '/' . $this->object_element . '/' . $this->fk_object;
        } else {
            $relative_path .= '/common';
        }
        
        // Ajoute le type de document pour une meilleure organisation
        if (!empty($this->type_document_code)) {
            $relative_path .= '/' . $this->type_document_code;
        }
        
        // Ajoute des sous-dossiers par année/mois pour éviter trop de fichiers dans un même répertoire
        if (!empty($this->date_ajout)) {
            try {
                $date_obj = new DateTime($this->date_ajout);
                $year = $date_obj->format('Y');
                $month = $date_obj->format('m');
                $relative_path .= '/' . $year . '/' . $month;
            } catch (Exception $e) {
                // En cas d'erreur, ne pas ajouter de sous-dossier date
            }
        }
        
        // Si filepath est défini en plus, l'ajouter (structure personnalisée)
        if (!empty($this->filepath)) {
            $relative_path .= '/' . $this->filepath;
        }
        
        // Normaliser les séparateurs pour éviter les doubles //
        $relative_path = preg_replace('/\/+/', '/', $relative_path);
        $this->filepath = $relative_path; // Mise à jour du chemin relatif dans l'objet
        
        if ($full_path) {
            if (!empty($this->filename)) {
                return $base_dir . '/' . $relative_path . '/' . $this->filename;
            } else {
                return $base_dir . '/' . $relative_path;
            }
        } else {
            return $relative_path;
        }
    }
    
    /**
     * Gère le téléversement d'un fichier et met à jour l'objet ElaskaDocument
     * Version améliorée qui utilise ECMFiles de Dolibarr pour une meilleure intégration
     *
     * @param User   $user             Utilisateur qui effectue l'action
     * @param string $tmp_file_path    Chemin du fichier temporaire
     * @param string $original_filename Nom du fichier original
     * @param bool   $sanitize_filename Nettoyer le nom de fichier (true par défaut)
     * @return int                     <0 si erreur, >0 si OK
     */
    public function uploadFile($user, $tmp_file_path, $original_filename, $sanitize_filename = true)
    {
        global $conf, $langs;
        
        // Vérification du fichier source
        if (empty($tmp_file_path) || !file_exists($tmp_file_path) || !is_readable($tmp_file_path)) {
            $this->error = $langs->trans("ErrorUploadedFileNotFoundOrNotReadable");
            return -1;
        }

        // Nettoyer le nom de fichier si demandé
        if ($sanitize_filename) {
            $this->filename = dol_sanitizeFileName($original_filename);
        } else {
            $this->filename = $original_filename;
        }
        
        // Déterminer le répertoire cible
        $target_dir = $this->getPathToFile();
        $full_target_dir = (!empty($conf->elaska->dir_output) ? $conf->elaska->dir_output : DOL_DATA_ROOT.'/elaska') . '/' . $target_dir;
        
        // Création du répertoire de destination si nécessaire
        if (!dol_is_dir($full_target_dir)) {
            if (dol_mkdir($full_target_dir, 0777, true, true) < 0) { // true pour créer parents, true pour tout créer
                $this->error = $langs->trans("ErrorFailedToCreateDir", $full_target_dir);
                return -2;
            }
        }
        
        $full_target_path = $full_target_dir . '/' . $this->filename;

        // Déplacer le fichier vers sa destination finale
        if (dol_move_uploaded_file($tmp_file_path, $full_target_path, 1, 0, $_FILES['userfile']['error'] ?? null) > 0) {
            // Mettre à jour les métadonnées du fichier
            $this->filesize = filesize($full_target_path);
            $this->mimetype = dol_mimetype($full_target_path);
            
            // Générer un hash pour vérification d'intégrité
            if (function_exists('md5_file')) {
                $this->file_hash = md5_file($full_target_path);
            }
            
            // Compter les pages si c'est un PDF et que Imagick est disponible
            if (strtolower(pathinfo($this->filename, PATHINFO_EXTENSION)) == 'pdf' && class_exists('Imagick')) {
                try {
                    $imagick = new Imagick();
                    $imagick->pingImage($full_target_path);
                    $this->nb_pages = $imagick->getNumberImages();
                    $imagick->clear();
                } catch (Exception $e) {
                    dol_syslog("ElaskaDocument::uploadFile Imagick error for page count: ".$e->getMessage(), LOG_WARNING);
                    $this->nb_pages = null;
                }
            }
            
            // Intégration avec l'ECM de Dolibarr pour indexation des documents
            if (class_exists('ECMFiles') && empty($conf->global->ELASKA_DISABLE_ECM_INTEGRATION)) {
                // Définition du module et du sous-répertoire
                $modulepart = 'elaska';
                $relativepath = $target_dir . '/' . $this->filename;
                $relativepath = preg_replace('/^\/+/', '', $relativepath); // Supprime les / en début
                
                $ecmfile = new ECMFiles($this->db);
                $result = $ecmfile->fetch(0, '', $relativepath, $modulepart);
                
                // Si le fichier n'existe pas dans l'ECM, on le crée
                if ($result <= 0) {
                    $ecmfile->filepath = dirname($relativepath);
                    $ecmfile->filename = basename($relativepath);
                    $ecmfile->label = $this->libelle;
                    $ecmfile->fullpath_orig = $original_filename;
                    $ecmfile->gen_or_uploaded = 'uploaded';
                    $ecmfile->description = $this->description;
                    $ecmfile->keywords = $this->tags;
                    $ecmfile->src_object_type = $this->object_element ?: 'elaska_document';
                    $ecmfile->src_object_id = $this->id;
                    
                    $result = $ecmfile->create($user);
                    if ($result < 0) {
                        dol_syslog("ElaskaDocument::uploadFile Error creating ECM entry: ".$ecmfile->error, LOG_WARNING);
                        // On ne bloque pas le processus si l'indexation ECM échoue
                    }
                }
            }
            
            return 1; // Succès
        } else {
            // Gestion d'erreur
            $this->error = $langs->trans("ErrorFailedToMoveUploadedFile", $this->filename, $full_target_dir);
            if (isset($_FILES['userfile']['error']) && $_FILES['userfile']['error'] != UPLOAD_ERR_OK) {
                 $this->error .= ' ('.$langs->trans("Error".$_FILES['userfile']['error']).')';
            }
            dol_syslog("ElaskaDocument::uploadFile ".$this->error, LOG_ERR);
            return -3;
        }
    }

    /**
     * Génère un token pour permettre le téléchargement public d'un document
     *
     * @param int $validity_hours Durée de validité en heures (24 par défaut)
     * @return string             Token généré
     */
    public function generatePublicURL($validity_hours = 24)
    {
        global $conf;
        
        // Générer un token unique
        $this->public_download_url_token = dol_hash(uniqid(mt_rand(), true));
        
        // Définir la date d'expiration
        $this->public_url_expiration = dol_now() + ($validity_hours * 3600);
        
        // Sauvegarder les modifications
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
        $sql .= " SET public_download_url_token = '".$this->db->escape($this->public_download_url_token)."'";
        $sql .= ", public_url_expiration = '".$this->db->idate($this->public_url_expiration)."'";
        $sql .= " WHERE rowid = " . (int) $this->id;
        
        $this->db->query($sql);
        
        // Construire l'URL publique
        $public_url = dol_buildpath('/custom/elaska/public_download.php?token='.$this->public_download_url_token, 2);
        
        return $public_url;
    }

    /**
     * Vérifie si un token de téléchargement public est valide
     *
     * @param string $token Token à vérifier
     * @return int          ID du document si valide, 0 si expiré, <0 si erreur/inexistant
     */
    public static function checkPublicDownloadToken($db, $token)
    {
        if (empty($token)) return -1;
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_document";
        $sql.= " WHERE public_download_url_token = '".$db->escape($token)."'";
        $sql.= " AND public_url_expiration > '".$db->idate(dol_now())."'";
        
        $resql = $db->query($sql);
        if ($resql && $db->num_rows($resql) > 0) {
            $obj = $db->fetch_object($resql);
            return $obj->rowid;
        } elseif ($resql) {
            // Le token existe mais a expiré
            return 0;
        } else {
            return -2; // Erreur SQL
        }
    }

    /**
     * Valide un document
     *
     * @param User   $user       Utilisateur qui valide
     * @param string $commentaire Commentaire de validation (optionnel)
     * @return int               <0 si erreur, >0 si OK
     */
    public function validate($user, $commentaire = '')
    {
        $this->statut_validation_code = 'VALIDE';
        $this->fk_user_validateur = $user->id;
        $this->date_validation = dol_now();
        $this->commentaire_validation = $commentaire;
        
        return $this->update($user);
    }

    /**
     * Rejette un document
     *
     * @param User   $user  Utilisateur qui rejette
     * @param string $motif Motif du rejet
     * @return int          <0 si erreur, >0 si OK
     */
    public function reject($user, $motif)
    {
        $this->statut_validation_code = 'REJETE';
        $this->fk_user_validateur = $user->id;
        $this->date_validation = dol_now();
        $this->commentaire_validation = $motif;
        
        return $this->update($user);
    }

    /**
     * Vérifie si une alerte d'expiration doit être déclenchée
     *
     * @param int $seuilJours Seuil en jours avant expiration (si null, utilise le seuil de l'objet)
     * @return bool           True si une alerte doit être déclenchée
     */
    public function checkExpirationAlert($seuilJours = null)
    {
        if (!$this->alerte_expiration_active || empty($this->date_expiration)) {
            return false;
        }
        
        $seuil = is_null($seuilJours) ? $this->delai_alerte_expiration_jours : $seuilJours;
        if ($seuil <= 0) {
            return false;
        }
        
        $now_ts = dol_now();
        $expiration_ts = $this->db->jdatetotimestamp($this->date_expiration);
        
        // Alerte si la date d'expiration est dans le futur mais dans moins de $seuil jours
        return ($expiration_ts > $now_ts && $expiration_ts <= ($now_ts + ($seuil * 24 * 60 * 60)));
    }
    
    /**
     * Crée une nouvelle version d'un document existant
     *
     * @param User   $user         Utilisateur qui crée la nouvelle version
     * @param string $tmp_file_path Chemin du fichier temporaire pour la nouvelle version
     * @param string $orig_filename Nom du fichier original
     * @return int                  <0 si erreur, ID du nouveau document si OK
     */
    public function createNewVersion($user, $tmp_file_path, $orig_filename)
    {
        if (empty($this->id)) {
            $this->error = 'CannotCreateNewVersionOfUnsavedDocument';
            return -1;
        }
        
        // Créer un nouveau document qui sera la nouvelle version
        $newVersion = new ElaskaDocument($this->db);
        
        // Copier les propriétés du document actuel vers la nouvelle version
        foreach ($this->fields as $field => $def) {
            if ($field != 'rowid' && $field != 'ref' && $field != 'version' && 
                $field != 'date_creation' && $field != 'tms' && 
                $field != 'fk_user_creat' && $field != 'fk_user_modif') {
                $newVersion->$field = $this->$field;
            }
        }
        
        // Mettre à jour les spécificités de la nouvelle version
        $newVersion->fk_document_parent = $this->id;
        $newVersion->version = $this->incrementVersion($this->version);
        $newVersion->date_ajout = dol_now();
        $newVersion->fk_user_upload = $user->id;
        $newVersion->statut_validation_code = 'A_VERIFIER'; // Nouvelle version à valider
        $newVersion->date_validation = null;
        $newVersion->fk_user_validateur = null;
        
        // Créer l'entrée en base de données pour la nouvelle version
        $result = $newVersion->create($user);
        if ($result < 0) {
            $this->error = $newVersion->error;
            return -2;
        }
        
        // Téléverser le nouveau fichier pour la nouvelle version
        $uploadResult = $newVersion->uploadFile($user, $tmp_file_path, $orig_filename);
        if ($uploadResult < 0) {
            $this->error = $newVersion->error;
            // Si l'upload échoue, supprimer l'enregistrement créé
            $newVersion->delete($user);
            return -3;
        }
        
        return $newVersion->id;
    }
    
    /**
     * Incrémente une chaîne de version (ex: "1.0" -> "1.1", "1.9" -> "1.10")
     *
     * @param string $version Version actuelle
     * @return string         Version incrémentée
     */
    private function incrementVersion($version)
    {
        if (empty($version)) return "1.0";
        
        // Si la version contient un point, incrémenter la partie après le point
        if (strpos($version, '.') !== false) {
            list($major, $minor) = explode('.', $version, 2);
            $minor = (int)$minor + 1;
            return $major . '.' . $minor;
        } else {
            // Sinon, incrémenter simplement la version
            return (int)$version + 1;
        }
    }
    
    /**
     * Récupère tous les documents de la même famille de versions
     * (document actuel + ses ancêtres + ses descendants)
     *
     * @param string $orderBy Ordre de tri (par défaut version décroissante = plus récent d'abord)
     * @return array|int      Tableau d'objets ElaskaDocument ou <0 si erreur
     */
    public function getAllVersions($orderBy = 'version DESC')
    {
        if (empty($this->id)) return array();
        
        $result_array = array();
        $root_document_id = $this->getRootDocumentId();
        
        if ($root_document_id < 0) return -1;
        
        // Récupérer tous les documents de la même famille
        $sql = "WITH RECURSIVE version_tree AS (";
        $sql .= "  SELECT rowid, fk_document_parent, version FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql .= "  WHERE rowid = ".$root_document_id;
        $sql .= "  UNION ALL";
        $sql .= "  SELECT d.rowid, d.fk_document_parent, d.version FROM ".MAIN_DB_PREFIX.$this->table_element." d";
        $sql .= "  JOIN version_tree vt ON d.fk_document_parent = vt.rowid OR vt.fk_document_parent = d.rowid";
        $sql .= ") ";
        $sql .= "SELECT DISTINCT rowid FROM version_tree ORDER BY ".$orderBy;
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $doc = new ElaskaDocument($this->db);
                if ($doc->fetch($obj->rowid) > 0) {
                    $result_array[] = $doc;
                }
            }
            return $result_array;
        } else {
            $this->error = $this->db->lasterror();
            return -1;
        }
    }
    
    /**
     * Récupère l'ID du document racine de l'arbre de versions
     *
     * @return int ID du document racine, ou <0 si erreur
     */
    private function getRootDocumentId()
    {
        if (empty($this->id)) return -1;
        
        // Si pas de parent, c'est le document racine
        if (empty($this->fk_document_parent)) {
            return $this->id;
        }
        
        // Sinon, remonter jusqu'au document sans parent
        $current_id = $this->fk_document_parent;
        $max_iterations = 50; // Eviter les boucles infinies
        
        while ($max_iterations-- > 0) {
            $sql = "SELECT rowid, fk_document_parent FROM ".MAIN_DB_PREFIX.$this->table_element;
            $sql .= " WHERE rowid = ".$current_id;
            
            $resql = $this->db->query($sql);
            if (!$resql) return -2;
            
            $obj = $this->db->fetch_object($resql);
            if (!$obj) return -3;
            
            if (empty($obj->fk_document_parent)) {
                return $obj->rowid; // Trouvé le document racine
            } else {
                $current_id = $obj->fk_document_parent;
            }
        }
        
        return -4; // Boucle ou erreur de structure
    }

    // --- Méthodes statiques pour les dictionnaires ---
    
    /**
     * Méthode générique pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                      Objet de traduction
     * @param string    $dictionary_table_suffix    Suffixe du nom de la table dictionnaire
     * @param bool      $usekeys                    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty                 True pour ajouter une option vide
     * @param string    $fk_parent_code             Code parent pour dictionnaires hiérarchiques
     * @return array                                Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix, $usekeys = true, $show_empty = false, $fk_parent_code = null)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_doc_".$db->escape($dictionary_table_suffix);
        $sql .= " WHERE active = 1";
        
        // Pour les sous-types liés à un type parent
        if ($dictionary_table_suffix == 'sous_type' && !empty($fk_parent_code)) {
            $sql .= " AND fk_code_type_parent = '".$db->escape($fk_parent_code)."'";
        }
        
        $sql .= " ORDER BY position ASC, label ASC";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                if ($usekeys) {
                    $options[$obj->code] = $langs->trans($obj->label);
                } else {
                    $obj_opt = new stdClass();
                    $obj_opt->code = $obj->code;
                    $obj_opt->label = $obj->label;
                    $obj_opt->label_translated = $langs->trans($obj->label);
                    $options[] = $obj_opt;
                }
            }
            $db->free($resql);
        } else {
            dol_print_error($db);
        }
        
        return $options;
    }

    /**
     * Récupère les options de types de document
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeDocumentOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type', $usekeys, $show_empty);
    }

    /**
     * Récupère les options de sous-types de document pour un type parent
     *
     * @param Translate $langs           Objet de traduction
     * @param string    $type_parent_code Code du type parent
     * @param bool      $usekeys         True pour retourner tableau associatif code=>label
     * @param bool      $show_empty      True pour ajouter une option vide
     * @return array                     Tableau d'options
     */
    public static function getSousTypeDocumentOptions($langs, $type_parent_code, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'sous_type', $usekeys, $show_empty, $type_parent_code);
    }

    /**
     * Récupère les options de niveaux d'accès
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getNiveauAccesOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'niveau_acces', $usekeys, $show_empty);
    }

    /**
     * Récupère les options de statuts de validation
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutValidationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_validation', $usekeys, $show_empty);
    }

    /**
     * Récupère les options de destinations légales
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getDestLegaleOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'dest_legale', $usekeys, $show_empty);
    }
    
    /**
     * Recherche avancée de documents selon multiples critères
     *
     * @param array  $params        Tableau associatif de paramètres de recherche
     * @param int    $limit         Limite de résultats (0=pas de limite)
     * @param int    $offset        Offset pour la pagination
     * @param string $sortfield     Champ de tri
     * @param string $sortorder     Ordre de tri (ASC|DESC)
     * @return array|int            Tableau d'objets ElaskaDocument ou <0 si erreur
     */
    public static function searchDocuments($params = array(), $limit = 0, $offset = 0, $sortfield = 't.date_ajout', $sortorder = 'DESC')
    {
        global $db;
        
        $result_array = array();
        
        $sql = 'SELECT t.rowid FROM '.MAIN_DB_PREFIX.'elaska_document as t';
        $sql .= ' WHERE 1=1';
        
        // Ajout des conditions de recherche
        if (!empty($params['fk_elaska_tiers'])) {
            $sql .= ' AND t.fk_elaska_tiers = '.(int) $params['fk_elaska_tiers'];
        }
        if (!empty($params['fk_object']) && !empty($params['object_element'])) {
            $sql .= ' AND t.fk_object = '.(int) $params['fk_object'];
            $sql .= ' AND t.object_element = "'.$db->escape($params['object_element']).'"';
        }
        if (!empty($params['type_document_code'])) {
            $sql .= ' AND t.type_document_code = "'.$db->escape($params['type_document_code']).'"';
        }
        if (!empty($params['sous_type_code'])) {
            $sql .= ' AND t.sous_type_code = "'.$db->escape($params['sous_type_code']).'"';
        }
        if (!empty($params['statut_validation_code'])) {
            $sql .= ' AND t.statut_validation_code = "'.$db->escape($params['statut_validation_code']).'"';
        }
        if (!empty($params['keywords'])) {
            $keywords = explode(' ', $params['keywords']);
            $sql .= ' AND (';
            $word_conditions = array();
            foreach ($keywords as $keyword) {
                $word = trim($keyword);
                if (!empty($word)) {
                    $word = $db->escape($word);
                    $word_conditions[] = 't.libelle LIKE "%'.$word.'%" OR t.description LIKE "%'.$word.'%" OR t.tags LIKE "%'.$word.'%"';
                }
            }
            $sql .= implode(' OR ', $word_conditions);
            $sql .= ')';
        }
        if (!empty($params['date_document_start'])) {
            $sql .= ' AND t.date_document >= "'.$db->escape($params['date_document_start']).'"';
        }
        if (!empty($params['date_document_end'])) {
            $sql .= ' AND t.date_document <= "'.$db->escape($params['date_document_end']).'"';
        }
        if (isset($params['est_confidentiel'])) {
            $sql .= ' AND t.est_confidentiel = '.($params['est_confidentiel'] ? 1 : 0);
        }
        if (isset($params['is_template'])) {
            $sql .= ' AND t.is_template = '.($params['is_template'] ? 1 : 0);
        }
        if (!empty($params['fk_user_upload'])) {
            $sql .= ' AND t.fk_user_upload = '.(int) $params['fk_user_upload'];
        }
        if (!empty($params['mimetype'])) {
            $sql .= ' AND t.mimetype LIKE "'.$db->escape($params['mimetype']).'%"';
        }
        if (!empty($params['extension'])) {
            $sql .= ' AND t.filename LIKE "%.'.$db->escape($params['extension']).'"';
        }
        
        $sql .= ' AND t.entity IN ('.getEntity('elaska_document').')';
        
        // Tri
        if ($sortfield && $sortorder) {
            $sql .= ' ORDER BY '.$sortfield.' '.$sortorder;
        }
        
        // Pagination
        if ($limit > 0) {
            $sql .= $db->plimit($limit, $offset);
        }
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                $doc = new ElaskaDocument($db);
                if ($doc->fetch($obj->rowid) > 0) {
                    $result_array[] = $doc;
                }
            }
            return $result_array;
        } else {
            return -1;
        }
    }
    
    /**
     * Récupère tous les documents associés à un objet spécifique
     *
     * @param string $object_element Type de l'objet lié (ex: 'elaska_intervenant')
     * @param int    $fk_object      ID de l'objet lié
     * @param string $orderBy        Ordre de tri (optionnel)
     * @return array|int             Tableau d'objets ElaskaDocument ou <0 si erreur
     */
    public static function fetchByObject($object_element, $fk_object, $orderBy = 't.date_ajout DESC')
{
    global $db;
    
    if (empty($object_element) || empty($fk_object)) {
        return array();
    }
    
    $result_array = array();
    
    $sql = 'SELECT t.rowid FROM '.MAIN_DB_PREFIX.'elaska_document as t';
    $sql .= ' WHERE t.object_element = "'.$db->escape($object_element).'"';
    $sql .= ' AND t.fk_object = '.(int) $fk_object;
    $sql .= ' AND t.entity IN ('.getEntity('elaska_document').')';
    
    if (!empty($orderBy)) {
        $sql .= ' ORDER BY '.$orderBy;
    }
    
    $resql = $db->query($sql);
    if ($resql) {
        while ($obj = $db->fetch_object($resql)) {
            $doc = new ElaskaDocument($db);
            if ($doc->fetch($obj->rowid) > 0) {
                $result_array[] = $doc;
            }
        }
        return $result_array;
    } else {
        return -1;
    }
}

/**
 * Gère le téléchargement d'un document avec mise à jour des statistiques
 *
 * @param bool $increment_counter Si true, incrémente le compteur de téléchargements
 * @return void                   Envoie directement le fichier au navigateur
 */
public function download($increment_counter = true)
{
    global $conf;
    
    // Récupérer le chemin complet du fichier
    $filepath = $this->getPathToFile(true);
    
    // Vérifier l'existence du fichier
    if (empty($filepath) || !file_exists($filepath)) {
        header('HTTP/1.0 404 Not Found');
        echo 'File not found';
        exit;
    }
    
    // Mettre à jour les statistiques si demandé
    if ($increment_counter) {
        $this->nb_downloads = $this->nb_downloads + 1;
        $this->last_download_date = dol_now();
        
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " SET nb_downloads = " . $this->nb_downloads;
        $sql.= ", last_download_date = '".$this->db->idate($this->last_download_date)."'";
        $sql.= " WHERE rowid = " . (int) $this->id;
        
        $this->db->query($sql);
    }
    
    // Configuration des en-têtes pour le téléchargement
    header('Content-Description: File Transfer');
    header('Content-Type: ' . ($this->mimetype ?: 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="'.$this->filename.'"');
    header('Content-Length: ' . filesize($filepath));
    header('Pragma: public');
    
    // Lecture et envoi du fichier
    readfile($filepath);
    exit;
}

/**
 * Vérifie les droits d'accès au document
 *
 * @param User $user Utilisateur souhaitant accéder au document
 * @return bool      true si l'accès est autorisé, false sinon
 */
public function checkAccess($user)
{
    global $conf;
    
    // Si l'utilisateur est administrateur ou super administrateur, toujours autoriser
    if ($user->admin || $user->rights->elaska->admin) {
        return true;
    }
    
    // Vérifier les droits selon le niveau d'accès du document
    switch ($this->niveau_acces_code) {
        case 'PUBLIC':
            return true;
            
        case 'STANDARD_INTERNE':
            // Vérifie que l'utilisateur a un droit de base sur les documents
            return !empty($user->rights->elaska->document->read);
            
        case 'CONFIDENTIEL':
            // Pour les documents confidentiels, vérifier les droits spécifiques
            if (!empty($user->rights->elaska->document->read_confidential)) {
                return true;
            }
            
            // Vérifier si l'utilisateur est le créateur du document
            if ($this->fk_user_creat == $user->id) {
                return true;
            }
            
            // Vérifier si l'utilisateur est le référent du tiers lié
            if (!empty($this->fk_elaska_tiers) && !empty($conf->global->ELASKA_REFERENT_CAN_ACCESS_ALL_DOCS)) {
                require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_tiers.class.php';
                $tiers = new ElaskaTiers($this->db);
                if ($tiers->fetch($this->fk_elaska_tiers) > 0 && $tiers->fk_user_referent == $user->id) {
                    return true;
                }
            }
            
            return false;
            
        case 'RESTREINT':
            // Pour les documents à accès restreint, vérifier si l'utilisateur est explicitement autorisé
            // Cette logique pourrait utiliser une table de liaison document_access non implémentée ici
            
            // Vérifier si l'utilisateur a des droits avancés
            if (!empty($user->rights->elaska->document->read_all)) {
                return true;
            }
            
            // Vérifier si l'utilisateur est le créateur du document
            if ($this->fk_user_creat == $user->id || $this->fk_user_upload == $user->id) {
                return true;
            }
            
            return false;
            
        default:
            // Par défaut, vérifier les droits de base
            return !empty($user->rights->elaska->document->read);
    }
}

/**
 * Retourne l'URL de téléchargement du document
 *
 * @param bool $absolute Si true, retourne une URL absolue
 * @return string       URL de téléchargement
 */
public function getDownloadUrl($absolute = false)
{
    global $conf;
    
    if (!empty($this->public_download_url_token)) {
        // URL publique si disponible
        return dol_buildpath('/custom/elaska/public_download.php?token='.$this->public_download_url_token, $absolute ? 2 : 1);
    } else {
        // URL interne nécessitant authentification
        return dol_buildpath('/custom/elaska/document_download.php?id='.$this->id, $absolute ? 2 : 1);
    }
}

/**
 * Retourne une URL de prévisualisation du document (si possible)
 *
 * @return string|null URL de prévisualisation ou null si non prévisualisable
 */
public function getPreviewUrl()
{
    if (empty($this->filename)) {
        return null;
    }
    
    // Vérifier si le document est prévisualisable (PDF, image, etc.)
    $extension = pathinfo($this->filename, PATHINFO_EXTENSION);
    $previewable_extensions = array('pdf', 'jpg', 'jpeg', 'png', 'gif');
    
    if (!in_array(strtolower($extension), $previewable_extensions)) {
        return null;
    }
    
    return dol_buildpath('/custom/elaska/document_preview.php?id='.$this->id, 1);
}

/**
 * Clone un document avec optionnellement une copie du fichier physique
 *
 * @param User $user            Utilisateur effectuant le clonage
 * @param bool $clone_file      Si true, clone aussi le fichier physique
 * @return int                  <0 si erreur, ID du nouveau document si succès
 */
public function clone($user, $clone_file = true)
{
    global $conf, $langs;
    
    // Créer un nouveau document
    $newdoc = new ElaskaDocument($this->db);
    
    // Copier les propriétés du document actuel
    foreach ($this->fields as $field => $def) {
        if ($field != 'rowid' && $field != 'ref' && $field != 'date_creation' && 
            $field != 'tms' && $field != 'fk_user_creat' && $field != 'fk_user_modif' &&
            $field != 'import_key') {
            $newdoc->$field = $this->$field;
        }
    }
    
    // Mettre à jour les informations spécifiques au clone
    $newdoc->libelle = $langs->trans('CopyOf') . ' ' . $this->libelle;
    $newdoc->date_ajout = dol_now();
    $newdoc->fk_user_upload = $user->id;
    $newdoc->public_download_url_token = null;
    $newdoc->nb_downloads = 0;
    $newdoc->last_download_date = null;
    
    // Créer l'entrée en base
    $result = $newdoc->create($user);
    if ($result < 0) {
        $this->error = $newdoc->error;
        return -1;
    }
    
    // Si demandé, copier le fichier physique
    if ($clone_file && !empty($this->filename)) {
        $source_path = $this->getPathToFile(true);
        if (file_exists($source_path)) {
            $newdoc->filename = $this->filename;
            $target_path = $newdoc->getPathToFile(true);
            
            // Créer le répertoire cible si nécessaire
            $target_dir = dirname($target_path);
            if (!dol_is_dir($target_dir)) {
                dol_mkdir($target_dir);
            }
            
            // Copier le fichier
            if (!copy($source_path, $target_path)) {
                $newdoc->error = 'ErrorFailedToCopyFile';
                $newdoc->delete($user);
                return -2;
            }
            
            // Mettre à jour les métadonnées du fichier
            $newdoc->filesize = filesize($target_path);
            $newdoc->mimetype = $this->mimetype;
            $newdoc->update($user);
        }
    }
    
    return $newdoc->id;
}
}
}
?>