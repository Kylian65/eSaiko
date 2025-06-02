<?php
/**
 * eLaska - Classe pour gérer les communications
 * Date: 2025-05-30
 * Version: 2.0 (Version finale pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';

if (!class_exists('ElaskaCommunication', false)) {

class ElaskaCommunication extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_communication';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_communication';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'comments@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;
    
    /**
     * @var string Répertoire de sortie pour les fichiers liés à cet objet
     */
    public $common_dir_output_element;
    
    // Champs de la table llx_elaska_communication
    /**
     * @var int ID technique
     */
    public $rowid;
    
    /**
     * @var string Référence unique de la communication
     */
    public $ref;
    
    /**
     * @var int ID du tiers concerné
     */
    public $fk_elaska_tiers;
    
    /**
     * @var int ID du dossier concerné
     */
    public $fk_elaska_dossier;
    
    /**
     * @var string Date et heure de la communication (format MySQL)
     */
    public $date_communication;
    
    /**
     * @var string Code du type de communication (dictionnaire)
     */
    public $type_communication_code;
    
    /**
     * @var string Code du sens de communication (dictionnaire)
     */
    public $sens_communication_code;
    
    /**
     * @var string Code du canal de communication (dictionnaire)
     */
    public $canal_communication_code;
    
    /**
     * @var string Sujet de la communication
     */
    public $sujet;
    
    /**
     * @var string Corps du message au format HTML
     */
    public $corps_message_html;
    
    /**
     * @var string Notes brutes (non formatées)
     */
    public $notes_brutes;
    
    /**
     * @var int ID de l'utilisateur eLaska impliqué dans la communication
     */
    public $fk_user_contact_elaska;
    
    /**
     * @var string Nom de l'interlocuteur externe
     */
    public $interlocuteur_externe_nom;
    
    /**
     * @var string Email de l'interlocuteur externe
     */
    public $interlocuteur_externe_email;
    
    /**
     * @var string Téléphone de l'interlocuteur externe
     */
    public $interlocuteur_externe_tel;
    
    /**
     * @var string Fonction de l'interlocuteur externe
     */
    public $interlocuteur_externe_fonction;
    
    /**
     * @var int Durée de la communication en minutes
     */
    public $duree_minutes;
    
    /**
     * @var string Code du statut de la communication (dictionnaire)
     */
    public $statut_communication_code;
    
    /**
     * @var int ID de l'événement Dolibarr associé
     */
    public $fk_event_dolibarr;

    // Champs techniques standards
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $entity;
    public $status;

    /**
     * @var array Definition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'ref' => array('type' => 'varchar(128)', 'label' => 'RefCommunication', 'enabled' => 1, 'position' => 5, 'notnull' => 0, 'visible' => 1, 'unique' => 1),
        'fk_elaska_tiers' => array('type' => 'integer:ElaskaTiers:custom/elaska/class/elaska_tiers.class.php', 'label' => 'TiersElaskaConcerne', 'enabled' => 1, 'position' => 10, 'notnull' => 0, 'visible' => 1),
        'fk_elaska_dossier' => array('type' => 'integer:ElaskaDossier:custom/elaska/class/elaska_dossier.class.php', 'label' => 'DossierElaskaConcerne', 'enabled' => 1, 'position' => 15, 'notnull' => 0, 'visible' => 1),
        'date_communication' => array('type' => 'datetime', 'label' => 'DateCommunication', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'type_communication_code' => array('type' => 'varchar(30)', 'label' => 'TypeCommunication', 'enabled' => 1, 'position' => 25, 'notnull' => 1, 'visible' => 1),
        'sens_communication_code' => array('type' => 'varchar(15)', 'label' => 'SensCommunication', 'enabled' => 1, 'position' => 30, 'notnull' => 1, 'visible' => 1),
        'canal_communication_code' => array('type' => 'varchar(30)', 'label' => 'CanalCommunication', 'enabled' => 1, 'position' => 35, 'notnull' => 1, 'visible' => 1),
        'sujet' => array('type' => 'varchar(255)', 'label' => 'SujetCommunication', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'corps_message_html' => array('type' => 'text', 'label' => 'CorpsMessageHTML', 'enabled' => 1, 'position' => 45, 'notnull' => 0, 'visible' => 1, 'textarea_html' => 1),
        'notes_brutes' => array('type' => 'text', 'label' => 'NotesBrutesCommunication', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'fk_user_contact_elaska' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'IntervenantElaska', 'enabled' => 1, 'position' => 55, 'notnull' => 0, 'visible' => 1),
        'interlocuteur_externe_nom' => array('type' => 'varchar(255)', 'label' => 'NomInterlocuteurExterne', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'interlocuteur_externe_email' => array('type' => 'varchar(255)', 'label' => 'EmailInterlocuteurExterne', 'enabled' => 1, 'position' => 65, 'notnull' => 0, 'visible' => 1),
        'interlocuteur_externe_tel' => array('type' => 'varchar(50)', 'label' => 'TelInterlocuteurExterne', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'interlocuteur_externe_fonction' => array('type' => 'varchar(100)', 'label' => 'FonctionInterlocuteurExterne', 'enabled' => 1, 'position' => 75, 'notnull' => 0, 'visible' => 1),
        'duree_minutes' => array('type' => 'integer', 'label' => 'DureeMinutes', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'statut_communication_code' => array('type' => 'varchar(30)', 'label' => 'StatutCommunication', 'enabled' => 1, 'position' => 85, 'notnull' => 0, 'visible' => 1, 'default' => 'TRAITE'),
        'fk_event_dolibarr' => array('type' => 'integer:Agenda:comm/action/class/actioncomm.class.php', 'label' => 'EvenementAgendaDolibarr', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 1, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => -2),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => 0, 'visible' => -2),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => -2),
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'default' => 1, 'enabled' => 1, 'visible' => -2, 'notnull' => 1, 'position' => 1002),
        'status' => array('type' => 'integer', 'label' => 'Status', 'enabled' => 1, 'position' => 1003, 'notnull' => 1, 'visible' => -2, 'default' => '1'),
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
        
        // Initialisation des valeurs par défaut
        if (empty($this->date_communication)) $this->date_communication = dol_now();
        if (empty($this->statut_communication_code)) $this->statut_communication_code = 'TRAITE';
        if (!isset($this->status)) $this->status = 1;
        
        // Configuration du répertoire pour les fichiers liés (ECM Dolibarr)
        if (!empty($conf->elaska->dir_output)) {
            $this->common_dir_output_element = $conf->elaska->dir_output . '/' . $this->element;
        } else {
            $this->common_dir_output_element = DOL_DATA_ROOT . '/elaska/' . $this->element; // Fallback
        }
    }

    /**
     * Crée une communication dans la base de données
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

        // Vérifications des champs obligatoires
        if (empty($this->type_communication_code) || empty($this->sens_communication_code) || empty($this->canal_communication_code)) {
            $this->error = "Type, sens et canal de communication sont obligatoires";
            $this->db->rollback();
            return -1;
        }
        
        // Laisser ref vide pour qu'ElaskaNumero la génère après création
        $this->ref = '';
        
        // Date de communication par défaut = maintenant si non renseignée
        if (empty($this->date_communication)) $this->date_communication = dol_now();
        
        $result = $this->createCommon($user, $notrigger);
        
        if ($result > 0) {
            // Générer la référence définitive avec ElaskaNumero
            $params = array();
            
            // Ajouter des paramètres spécifiques pour le masque de numérotation
            if (!empty($this->type_communication_code)) {
                $params['TYPE_COMM'] = $this->type_communication_code;
            }
            
            if (!empty($this->sens_communication_code)) {
                $params['SENS'] = $this->sens_communication_code;
            }
            
            if (!empty($this->canal_communication_code)) {
                $params['CANAL'] = $this->canal_communication_code;
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
                    dol_syslog("ElaskaCommunication::create Failed to update definitive ref for ".$this->element." ".$this->id, LOG_ERR);
                }
            } else {
                dol_syslog("ElaskaCommunication::create Failed to generate/record definitive ref for ".$this->element." ".$this->id, LOG_ERR);
            }

            // Créer un événement dans l'agenda Dolibarr si demandé
            if (!$error && !empty($conf->agenda->enabled) && !empty($this->create_event) && empty($this->fk_event_dolibarr)) {
                $actioncomm_id = $this->createAgendaEvent($user);
                if ($actioncomm_id < 0) {
                    $error++;
                }
            }
        } else {
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
        if (empty($this->id) || !isset($this->ref)) return false;
        
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
     * Charge une communication depuis la base de données
     *
     * @param int    $id  ID de la communication
     * @param string $ref Référence de la communication
     * @return int        <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = null)
    {
        $result = $this->fetchCommon($id, $ref);
        
        // Vous pouvez ajouter des logiques post-fetch ici si nécessaire
        
        return $result;
    }

    /**
     * Met à jour une communication dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        global $conf;
        
        $error = 0;
        $this->db->begin();
        
        // Vérifications des champs obligatoires
        if (empty($this->type_communication_code) || empty($this->sens_communication_code) || empty($this->canal_communication_code)) {
            $this->error = "Type, sens et canal de communication sont obligatoires";
            $this->db->rollback();
            return -1;
        }
        
        $result = $this->updateCommon($user, $notrigger);
        if ($result < 0) {
            $error++;
        }
        
        if (!$error && !empty($conf->agenda->enabled) && !empty($this->update_event) && !empty($this->fk_event_dolibarr)) {
            if (!$this->updateAgendaEvent($user)) {
                $error++;
            }
        }
        
        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            $this->db->rollback();
            return -1;
        }
    }

    /**
     * Supprime une communication de la base de données
     * et les fichiers attachés si existants
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        global $conf;
        $this->db->begin();
        $error = 0;
        
        // 1. Supprimer les fichiers attachés
        $file_list = $this->getLinkedFilesList();
        if (is_array($file_list) && count($file_list) > 0) {
            foreach ($file_list as $file_info) {
                $relative_path_in_element_dir = $file_info['path'] . (empty($file_info['path']) ? '' : '/') . $file_info['name'];
                $full_path_for_delete = $this->common_dir_output_element . '/' . $relative_path_in_element_dir;
                
                $res_delete = dol_delete_file(
                    $this->common_dir_output_element . '/' . $file_info['path'],
                    $file_info['name'],
                    0,                 // deletecommondir
                    0,                 // deleteelementdir
                    $this,             // object
                    $this->element,    // element
                    $this->id          // element_id
                );
                
                if ($res_delete < 0) {
                    dol_syslog(get_class($this)."::delete Failed to delete linked file: ".$file_info['name'], LOG_WARNING);
                }
            }
        }
        
        // 2. Supprimer l'événement agenda associé si existant
        if (!$error && !empty($conf->agenda->enabled) && !empty($this->fk_event_dolibarr)) {
            if (!$this->deleteAgendaEvent($user)) {
                $error++;
            }
        }
        
        // 3. Supprimer la communication
        if (!$error) {
            $result = $this->deleteCommon($user, $notrigger);
            if ($result < 0) {
                $error++;
            }
        }
        
        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            $this->db->rollback();
            return -1;
        }
    }

    /**
     * Crée un événement dans l'agenda Dolibarr depuis cette communication
     *
     * @param User $user Utilisateur qui crée l'événement
     * @return int       <0 si erreur, ID de l'événement si OK
     */
    public function createAgendaEvent($user)
    {
        global $conf;
        
        if (empty($conf->agenda->enabled)) {
            return 0;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
        
        $actioncomm = new ActionComm($this->db);
        $actioncomm->type_code = 'AC_OTH'; // Type par défaut
        
        // Mapper type de communication vers type d'action Dolibarr si besoin
        switch ($this->canal_communication_code) {
            case 'EMAIL': $actioncomm->type_code = 'AC_EMAIL'; break;
            case 'TELEPHONE': $actioncomm->type_code = 'AC_TEL'; break;
            case 'RDV': $actioncomm->type_code = 'AC_RDV'; break;
        }
        
        // Définir le titre de l'événement
        $actioncomm->label = !empty($this->sujet) ? $this->sujet : $this->type_communication_code;
        
        // Description = corps du message ou notes brutes
        $actioncomm->note = !empty($this->corps_message_html) ? $this->corps_message_html : $this->notes_brutes;
        
        // Date et heure de l'événement
        $actioncomm->datep = $this->date_communication;
        
        // Durée si renseignée
        if (!empty($this->duree_minutes)) {
            $actioncomm->datef = dol_time_plus_duree($actioncomm->datep, $this->duree_minutes, 'm');
        }
        
        // Lier au tiers si défini
        if (!empty($this->fk_elaska_tiers)) {
            $actioncomm->socid = $this->fk_elaska_tiers;
        }
        
        // Utilisateur responsable
        $actioncomm->userownerid = !empty($this->fk_user_contact_elaska) ? $this->fk_user_contact_elaska : $user->id;
        
        // Contact externe
        if (!empty($this->interlocuteur_externe_nom)) {
            $actioncomm->contact_id = $this->findOrCreateContact();
        }
        
        // Créer l'événement
        $actioncomm_id = $actioncomm->create($user);
        
        if ($actioncomm_id > 0) {
            // Mettre à jour la communication avec l'ID de l'événement créé
            $this->fk_event_dolibarr = $actioncomm_id;
            $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
            $sql .= " SET fk_event_dolibarr = ".(int)$actioncomm_id;
            $sql .= " WHERE rowid = ".(int)$this->id;
            
            if (!$this->db->query($sql)) {
                dol_syslog(get_class($this)."::createAgendaEvent Unable to update fk_event_dolibarr", LOG_ERR);
            }
            
            return $actioncomm_id;
        } else {
            $this->error = $actioncomm->error;
            return -1;
        }
    }
    
    /**
     * Met à jour l'événement agenda associé à cette communication
     *
     * @param User $user Utilisateur qui met à jour l'événement
     * @return bool      true si succès, false si erreur
     */
    public function updateAgendaEvent($user)
    {
        if (empty($this->fk_event_dolibarr)) {
            return false;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
        
        $actioncomm = new ActionComm($this->db);
        if ($actioncomm->fetch($this->fk_event_dolibarr) <= 0) {
            dol_syslog(get_class($this)."::updateAgendaEvent Unable to fetch event ".$this->fk_event_dolibarr, LOG_ERR);
            return false;
        }
        
        // Mettre à jour les propriétés
        $actioncomm->label = !empty($this->sujet) ? $this->sujet : $this->type_communication_code;
        $actioncomm->note = !empty($this->corps_message_html) ? $this->corps_message_html : $this->notes_brutes;
        $actioncomm->datep = $this->date_communication;
        
        if (!empty($this->duree_minutes)) {
            $actioncomm->datef = dol_time_plus_duree($actioncomm->datep, $this->duree_minutes, 'm');
        }
        
        if (!empty($this->fk_elaska_tiers)) {
            $actioncomm->socid = $this->fk_elaska_tiers;
        }
        
        $actioncomm->userownerid = !empty($this->fk_user_contact_elaska) ? $this->fk_user_contact_elaska : $user->id;
        
        // Contact externe
        if (!empty($this->interlocuteur_externe_nom) && empty($actioncomm->contact_id)) {
            $actioncomm->contact_id = $this->findOrCreateContact();
        }
        
        if ($actioncomm->update($user) > 0) {
            return true;
        } else {
            $this->error = $actioncomm->error;
            return false;
        }
    }
    
    /**
     * Supprime l'événement agenda associé à cette communication
     *
     * @param User $user Utilisateur qui supprime l'événement
     * @return bool      true si succès, false si erreur
     */
    public function deleteAgendaEvent($user)
    {
        if (empty($this->fk_event_dolibarr)) {
            return true;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
        
        $actioncomm = new ActionComm($this->db);
        if ($actioncomm->fetch($this->fk_event_dolibarr) <= 0) {
            dol_syslog(get_class($this)."::deleteAgendaEvent Unable to fetch event ".$this->fk_event_dolibarr, LOG_ERR);
            return true; // On considère que c'est OK si l'événement n'existe pas/plus
        }
        
        if ($actioncomm->delete() > 0) {
            return true;
        } else {
            $this->error = $actioncomm->error;
            return false;
        }
    }
    
    /**
     * Recherche ou crée un contact dans Dolibarr pour l'interlocuteur externe
     *
     * @return int ID du contact trouvé ou créé, 0 si échec
     */
    private function findOrCreateContact()
    {
        // Cette méthode peut être implémentée pour créer automatiquement un contact
        // en fonction des informations interlocuteur_externe_*
        // Pour l'instant, on retourne 0 (pas de contact lié)
        return 0;
    }

    /**
     * Récupère la liste des fichiers liés à cette communication
     *
     * @return array Liste des fichiers avec leurs informations
     */
    public function getLinkedFilesList()
    {
        global $conf;
        $file_list_details = array();
        
        if (empty($this->id)) return $file_list_details;

        $nb_files = $this->fetch_linked_files();

        if ($nb_files >= 0 && isset($this->liste_items_attached) && is_array($this->liste_items_attached)) {
            foreach ($this->liste_items_attached as $key => $file_data) {
                $file_list_details[] = array(
                    'id'        => $file_data['id'], 
                    'name'      => $file_data['name'],
                    'path'      => $file_data['sharepath'], 
                    'level'     => $file_data['level'],
                    'fullname'  => $this->common_dir_output_element . '/' . $file_data['sharepath'] . '/' . $file_data['name'],
                    'size'      => $file_data['size'],
                    'date'      => $this->db->jdate($file_data['date']),
                    'mimetype'  => $file_data['type']
                );
            }
        } elseif ($nb_files < 0) {
            dol_syslog(get_class($this)."::getLinkedFilesList Error fetching linked files: ".$this->error, LOG_ERR);
        }
        
        return $file_list_details;
    }
    
    /**
     * Compte le nombre de communications pour un tiers donné
     *
     * @param int $fk_elaska_tiers ID du tiers
     * @return int                 Nombre de communications ou -1 si erreur
     */
    public static function countByTiers($db, $fk_elaska_tiers)
    {
        $sql = "SELECT COUNT(*) as nb FROM ".MAIN_DB_PREFIX."elaska_communication";
        $sql.= " WHERE fk_elaska_tiers = ".(int)$fk_elaska_tiers;
        $sql.= " AND entity IN (".getEntity('elaska_communication').")";
        
        $resql = $db->query($sql);
        if ($resql) {
            $obj = $db->fetch_object($resql);
            $db->free($resql);
            return (int) $obj->nb;
        } else {
            dol_syslog("ElaskaCommunication::countByTiers Error: ".$db->lasterror(), LOG_ERR);
            return -1;
        }
    }
    
    /**
     * Récupère les dernières communications pour un tiers
     *
     * @param DoliDB $db            Base de données
     * @param int    $fk_elaska_tiers ID du tiers
     * @param int    $limit         Nombre maximum de résultats (0 = pas de limite)
     * @return array|int            Tableau d'objets ElaskaCommunication ou <0 si erreur
     */
    public static function getLastByTiers($db, $fk_elaska_tiers, $limit = 0)
    {
        $list = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_communication";
        $sql.= " WHERE fk_elaska_tiers = ".(int)$fk_elaska_tiers;
        $sql.= " AND entity IN (".getEntity('elaska_communication').")";
        $sql.= " ORDER BY date_communication DESC";
        if ($limit > 0) $sql.= " LIMIT ".(int)$limit;
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                $comm = new ElaskaCommunication($db);
                if ($comm->fetch($obj->rowid) > 0) {
                    $list[] = $comm;
                }
            }
            $db->free($resql);
            return $list;
        } else {
            dol_syslog("ElaskaCommunication::getLastByTiers Error: ".$db->lasterror(), LOG_ERR);
            return -1;
        }
    }
    
    /**
     * Récupère les dernières communications pour un dossier
     *
     * @param DoliDB $db               Base de données
     * @param int    $fk_elaska_dossier ID du dossier
     * @param int    $limit            Nombre maximum de résultats (0 = pas de limite)
     * @return array|int               Tableau d'objets ElaskaCommunication ou <0 si erreur
     */
    public static function getLastByDossier($db, $fk_elaska_dossier, $limit = 0)
    {
        $list = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_communication";
        $sql.= " WHERE fk_elaska_dossier = ".(int)$fk_elaska_dossier;
        $sql.= " AND entity IN (".getEntity('elaska_communication').")";
        $sql.= " ORDER BY date_communication DESC";
        if ($limit > 0) $sql.= " LIMIT ".(int)$limit;
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                $comm = new ElaskaCommunication($db);
                if ($comm->fetch($obj->rowid) > 0) {
                    $list[] = $comm;
                }
            }
            $db->free($resql);
            return $list;
        } else {
            dol_syslog("ElaskaCommunication::getLastByDossier Error: ".$db->lasterror(), LOG_ERR);
            return -1;
        }
    }

    /**
     * Méthode générique pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                  Objet de traduction
     * @param string    $dictionary_table_suffix Suffixe du nom de la table dictionnaire
     * @param bool      $usekeys                True pour retourner tableau associatif code=>label
     * @param bool      $show_empty             True pour ajouter une option vide
     * @return array                            Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label, picto FROM ".MAIN_DB_PREFIX."c_elaska_comm_".$db->escape($dictionary_table_suffix);
        $sql .= " WHERE active = 1";
        $sql .= " ORDER BY position ASC, label ASC";
        
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
                    if (isset($obj->picto)) $obj_option->picto = $obj->picto;
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
     * Récupère les options du dictionnaire de types de communication
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeCommunicationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire de sens de communication
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSensCommunicationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'sens', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire de canaux de communication
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getCanalCommunicationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'canal', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire de statuts de communication
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutCommunicationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut', $usekeys, $show_empty);
    }
}
}
?>