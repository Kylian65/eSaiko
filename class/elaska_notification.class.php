<?php
/**
 * eLaska - Classe pour gérer les notifications système
 * Date: 2025-05-30
 * Version: 3.1 (Intégration complète des retours utilisateur)
 * Auteur: Kylian65 / IA Collaboration
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php'; // Pour l'objet User

if (!class_exists('ElaskaNotification', false)) {

class ElaskaNotification extends CommonObject
{
    public $element = 'elaska_notification';
    public $table_element = 'elaska_notification';
    public $picto = 'bell@elaska';

    // --- Champs de la table llx_elaska_notification ---
    public $rowid;
    public $type_notification_code;   // Code du type de notification (lié au dictionnaire)
    public $titre;                    // Titre de la notification
    public $message;                  // Contenu détaillé de la notification
    public $fk_user_dest;             // Utilisateur destinataire
    public $fk_object;                // ID de l'objet associé (optionnel)
    public $element_type;             // Type de l'objet associé (ex: 'elaska_dossier')
    public $url_action;               // URL permettant d'accéder à l'objet concerné
    public $date_envoi_programmee;    // Date d'envoi programmée (pour les envois différés)
    public $date_envoi;               // Date d'envoi effectif de la notification
    public $date_lecture;             // Date de lecture par l'utilisateur destinataire
    public $statut_notification_code; // Statut de la notification (lié au dictionnaire)
    public $priorite_notification_code; // Niveau de priorité (lié au dictionnaire)
    public $canal_notification_code_pref; // Canal de communication préféré (email, web, etc.)
    public $web_uniquement;           // Si true, notification uniquement visible dans l'interface web
    public $error_message_envoi;      // Message d'erreur en cas d'échec d'envoi
    
    // Champs techniques explicitement déclarés
    public $entity;                   // Entité Dolibarr
    public $date_creation;            // Date de création en base
    public $tms;                      // Date de dernière modification
    public $fk_user_creat;            // Utilisateur créateur
    public $fk_user_modif;            // Dernier utilisateur modificateur
    public $import_key;               // Clé d'import

    // Champs additionnels (non stockés en base)
    public $user_dest_lastname;       // Nom de famille du destinataire
    public $user_dest_firstname;      // Prénom du destinataire
    public $user_dest_login;          // Login du destinataire
    public $user_dest_email;          // Email du destinataire

    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'type_notification_code' => array('type' => 'varchar(50)', 'label' => 'TypeNotification', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1),
        'titre' => array('type' => 'varchar(255)', 'label' => 'Titre', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'message' => array('type' => 'text', 'label' => 'Message', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'fk_user_dest' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'Destinataire', 'enabled' => 1, 'position' => 40, 'notnull' => 1, 'visible' => 1),
        'fk_object' => array('type' => 'integer', 'label' => 'ObjetID', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'element_type' => array('type' => 'varchar(50)', 'label' => 'TypeObjet', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'url_action' => array('type' => 'varchar(255)', 'label' => 'URLAction', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 0),
        'date_envoi_programmee' => array('type' => 'datetime', 'label' => 'DateEnvoiProgrammee', 'enabled' => 1, 'position' => 75, 'notnull' => 0, 'visible' => 1),
        'date_envoi' => array('type' => 'datetime', 'label' => 'DateEnvoiEffectif', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'date_lecture' => array('type' => 'datetime', 'label' => 'DateLecture', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'statut_notification_code' => array('type' => 'varchar(30)', 'label' => 'Statut', 'enabled' => 1, 'position' => 100, 'notnull' => 1, 'visible' => 1, 'default' => 'A_ENVOYER'),
        'priorite_notification_code' => array('type' => 'varchar(30)', 'label' => 'Priorite', 'enabled' => 1, 'position' => 110, 'notnull' => 1, 'visible' => 1, 'default' => 'NORMALE'),
        'canal_notification_code_pref' => array('type' => 'varchar(30)', 'label' => 'CanalPrefere', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'web_uniquement' => array('type' => 'boolean', 'label' => 'WebUniquement', 'enabled' => 1, 'position' => 130, 'notnull' => 1, 'visible' => 1, 'default' => 0),
        'error_message_envoi' => array('type' => 'text', 'label' => 'ErreurEnvoi', 'enabled' => 1, 'position' => 135, 'notnull' => 0, 'visible' => 0),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 1, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => -2),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => 0, 'visible' => -2),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => -2),
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'default' => 1, 'enabled' => 1, 'visible' => -2, 'notnull' => 1, 'position' => 1002),
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
        if (empty($this->statut_notification_code)) $this->statut_notification_code = 'A_ENVOYER';
        if (empty($this->priorite_notification_code)) $this->priorite_notification_code = 'NORMALE';
        if (!isset($this->web_uniquement)) $this->web_uniquement = 0;
    }

    /**
     * Crée une notification dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        $result = $this->createCommon($user, $notrigger);
        if ($result > 0 && $this->statut_notification_code == 'A_ENVOYER' && empty($this->date_envoi_programmee)) {
            $this->send($user);
        }
        return $result;
    }

    /**
     * Charge une notification de la base de données et les informations sur le destinataire
     *
     * @param int    $id    Id de la notification
     * @param string $ref   Référence de la notification (non utilisé ici)
     * @return int          <0 si KO, >0 si OK
     */
    public function fetch($id, $ref = null)
    {
        $result = $this->fetchCommon($id, $ref);
        if ($result > 0 && $this->fk_user_dest > 0) {
            $user_dest = new User($this->db);
            if ($user_dest->fetch($this->fk_user_dest) > 0) {
                $this->user_dest_lastname = $user_dest->lastname;
                $this->user_dest_firstname = $user_dest->firstname;
                $this->user_dest_login = $user_dest->login;
                $this->user_dest_email = $user_dest->email;
            }
        }
        return $result;
    }

    /**
     * Met à jour une notification dans la base de données
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
     * Supprime une notification de la base de données
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        return $this->deleteCommon($user, $notrigger);
    }

    /**
     * Marque une notification comme lue
     *
     * @param User $user Utilisateur qui effectue l'action
     * @return int       <0 si erreur, >0 si OK
     */
    public function markAsRead($user)
    {
        if ($this->statut_notification_code == 'LUE' || $this->statut_notification_code == 'ACTION_EFFECTUEE') {
            return 1; // Déjà lue ou action effectuée
        }
        
        $this->statut_notification_code = 'LUE';
        $this->date_lecture = dol_now();
        
        return $this->update($user, 1); // notrigger = 1 car action système
    }

    /**
     * Marque une notification comme traitée (action effectuée)
     *
     * @param User $user Utilisateur qui effectue l'action
     * @return int       <0 si erreur, >0 si OK
     */
    public function markAsProcessed($user)
    {
        $this->statut_notification_code = 'ACTION_EFFECTUEE';
        if (empty($this->date_lecture)) {
            $this->date_lecture = dol_now();
        }
        
        return $this->update($user, 1); // notrigger = 1 car action système
    }

    /**
     * Envoie la notification selon son canal préféré
     *
     * @param User $user_action Utilisateur qui déclenche l'envoi
     * @return int              <0 si erreur, 0 si pas envoyée, >0 si OK
     */
    public function send($user_action)
    {
        // Vérifier si la notification est envoyable
        $can_send = false;
        if ($this->statut_notification_code == 'A_ENVOYER') {
            $can_send = true;
        } elseif ($this->statut_notification_code == 'PROGRAMMEE' && !empty($this->date_envoi_programmee)) {
            if ($this->db->jdatetotimestamp($this->date_envoi_programmee) <= dol_now()) {
                $can_send = true;
            }
        } elseif ($this->statut_notification_code == 'ERREUR_ENVOI') { // Permettre une nouvelle tentative
             $can_send = true;
        }

        if (!$can_send) {
            return 0; // Pas envoyable pour le moment ou déjà traitée
        }

        $this->error_message_envoi = ''; // Reset error message
        $sent_by_any_canal = false;

        // Logique d'envoi par canal (ici, seulement EMAIL si pas web_uniquement)
        if (!$this->web_uniquement) {
            // Vérifier le canal préféré ou utiliser email par défaut
            if ($this->canal_notification_code_pref == 'EMAIL' || empty($this->canal_notification_code_pref)) {
                if ($this->sendEmail($user_action)) {
                    $sent_by_any_canal = true;
                } else {
                    $this->error_message_envoi = $this->error;
                    dol_syslog(get_class($this)."::send Failed to send by email: ".$this->error, LOG_ERR);
                }
            } elseif ($this->canal_notification_code_pref == 'SMS') {
                // TODO: Implémenter logique pour SMS
                $this->error_message_envoi = "SMS sending not implemented yet";
                dol_syslog(get_class($this)."::send SMS sending not implemented", LOG_WARNING);
            }
            // Ajouter d'autres canaux au besoin
        } else {
            $sent_by_any_canal = true; // Considérée envoyée car elle est en UI uniquement
        }

        if ($sent_by_any_canal) {
            $this->statut_notification_code = 'ENVOYEE';
            $this->date_envoi = dol_now();
        } else {
            $this->statut_notification_code = 'ERREUR_ENVOI';
            if (empty($this->error_message_envoi)) {
                $this->error_message_envoi = "No suitable channel or general send failure.";
            }
        }
        
        return $this->update($user_action, 1); // notrigger = 1 car action système
    }
    
    /**
     * Envoie la notification par email
     *
     * @param User $user_action_obj Utilisateur qui fait l'action d'envoi
     * @return bool                 true si succès, false sinon
     */
    private function sendEmail($user_action_obj)
    {
        global $conf, $langs;
        
        if (empty($this->fk_user_dest)) {
            $this->error = "No destination user specified";
            dol_syslog(get_class($this)."::sendEmail ".$this->error, LOG_WARNING);
            return false;
        }
        
        // Charger l'email du destinataire si nécessaire
        if (empty($this->user_dest_email)) {
            $user_dest = new User($this->db);
            if ($user_dest->fetch($this->fk_user_dest) > 0) {
                $this->user_dest_email = $user_dest->email;
            }
        }

        if (empty($this->user_dest_email)) {
            $this->error = "No email for user ID ".$this->fk_user_dest;
            dol_syslog(get_class($this)."::sendEmail ".$this->error, LOG_WARNING);
            return false;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';
        
        $subject = $this->titre;
        
        // Préparer le corps du message : nl2br pour les retours à la ligne HTML, puis htmlentities pour la sécurité
        $message_html = nl2br(dol_escape_htmltag($this->message));
        
        // Ajouter le lien d'action si présent
        if (!empty($this->url_action)) {
            $message_html .= "<br /><br />".$langs->trans("ClickHereToAccess").": <a href='".$this->url_action."'>".$this->url_action."</a>";
        }
        
        // Ajouter le pied de page
        $footer = $conf->global->ELASKA_NOTIFICATION_EMAIL_FOOTER ?? $langs->trans("NotificationEmailFooter");
        $message_html .= "<br /><br />--<br />".$footer;
        
        // Configurer l'expéditeur
        $from_email = $conf->global->ELASKA_NOTIFICATION_EMAIL_FROM ?? $conf->global->MAIN_MAIL_EMAIL_FROM;
        $from_name = $conf->global->ELASKA_NOTIFICATION_EMAIL_FROM_NAME ?? $conf->global->MAIN_MAIL_FROM_NAME;
        
        // Créer et envoyer l'email
        $mailfile = new CMailFile(
            $subject,
            $this->user_dest_email,
            $from_email,
            $message_html,
            array(), // Pièces jointes
            array(), // Copies
            array(), // Copies cachées
            '', // Répondre à
            '', // Erreurs à
            0, // deliveryreceipt
            -1, // msgishtml (1 = HTML, 0 = texte, -1 = auto)
            null, // errors_to
            '', // css
            '', // trackid
            $from_name // from_name
        );
        
        // Préparer des substitutions pour les triggers email
        $substitutionarray = array(
            'notification_id' => $this->id,
            'notification_titre' => $this->titre,
            'notification_type' => $this->type_notification_code,
            'notification_recipient_id' => $this->fk_user_dest
        );

        // Envoyer l'email
        if ($mailfile->sendfile(null, $substitutionarray)) {
            return true;
        } else {
            $this->error = $mailfile->error;
            dol_syslog(get_class($this)."::sendEmail Error sending email: ".$this->error, LOG_ERR);
            return false;
        }
    }
        
    /**
     * Récupère les notifications non lues d'un utilisateur
     *
     * @param DoliDB $db      Base de données
     * @param int    $user_id ID de l'utilisateur
     * @param int    $limit   Nombre maximum de notifications à retourner (0 = pas de limite)
     * @return array          Tableau d'objets ElaskaNotification
     */
    public static function getUnreadNotificationsForUser($db, $user_id, $limit = 5)
    {
        $notifications = array();
        $sql = "SELECT n.rowid";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_notification as n";
        $sql .= " WHERE n.fk_user_dest = ".(int) $user_id;
        $sql .= " AND n.date_lecture IS NULL"; // Non lue
        $sql .= " AND n.statut_notification_code IN ('ENVOYEE', 'PROGRAMMEE')"; // Doit être au moins envoyée ou visiblement programmée
        $sql .= " ORDER BY n.priorite_notification_code ASC, n.date_creation DESC"; 
        if ($limit > 0) {
            $sql .= " LIMIT ".(int) $limit;
        }
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                $notification = new ElaskaNotification($db);
                if ($notification->fetch($obj->rowid) > 0) {
                    $notifications[] = $notification;
                }
            }
            $db->free($resql);
        } else { 
            dol_syslog("ElaskaNotification::getUnreadNotificationsForUser Error: ".$db->lasterror(), LOG_ERR); 
        }
        return $notifications;
    }
    
    /**
     * Compte les notifications non lues d'un utilisateur
     *
     * @param DoliDB $db      Base de données
     * @param int    $user_id ID de l'utilisateur
     * @return int            Nombre de notifications non lues
     */
    public static function countUnreadNotificationsForUser($db, $user_id)
    {
        $sql = "SELECT COUNT(*) as total";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_notification";
        $sql .= " WHERE fk_user_dest = ".(int) $user_id;
        $sql .= " AND date_lecture IS NULL";
        $sql .= " AND statut_notification_code IN ('ENVOYEE', 'PROGRAMMEE')";
        
        $resql = $db->query($sql);
        if ($resql) {
            $obj = $db->fetch_object($resql);
            $db->free($resql);
            return (int) $obj->total;
        }
        
        return 0;
    }

    /**
     * Récupère les notifications programmées dont la date d'envoi est passée
     *
     * @param DoliDB $db Base de données
     * @return array     Tableau d'objets ElaskaNotification à envoyer
     */
    public static function getPendingScheduledNotifications($db)
    {
        $notifications = array();
        $now_sql = "'".$db->idate(dol_now())."'";

        $sql = "SELECT n.rowid";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_notification as n";
        $sql .= " WHERE n.statut_notification_code = 'PROGRAMMEE'";
        $sql .= " AND n.date_envoi_programmee IS NOT NULL AND n.date_envoi_programmee <= ".$now_sql;
        $sql .= " ORDER BY n.date_envoi_programmee ASC";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                $notification = new ElaskaNotification($db);
                if ($notification->fetch($obj->rowid) > 0) {
                    $notifications[] = $notification;
                }
            }
            $db->free($resql);
        } else { 
            dol_syslog("ElaskaNotification::getPendingScheduledNotifications Error: ".$db->lasterror(), LOG_ERR); 
        }
        
        return $notifications;
    }
    
    /**
     * Crée une notification système pour un ou plusieurs utilisateurs
     *
     * @param DoliDB $db                  Base de données
     * @param User   $user_action         Utilisateur créant la notification
     * @param string $type_code           Code du type de notification
     * @param string $titre               Titre de la notification
     * @param string $message             Contenu du message
     * @param int    $fk_object           ID de l'objet concerné (0 si aucun)
     * @param string $element_type        Type de l'objet concerné
     * @param mixed  $user_ids_dest       ID ou tableau d'IDs des utilisateurs destinataires
     * @param string $url_action          URL pour accéder à l'objet concerné
     * @param string $priorite_code       Code de priorité ('NORMALE' par défaut)
     * @param bool   $web_uniquement      Si true, uniquement visible en UI (pas d'email)
     * @param string $date_envoi_programmee Date d'envoi programmée (null = immédiat)
     * @param string $canal_pref          Canal préféré pour l'envoi ('EMAIL' par défaut)
     * @return int                        Nombre de notifications créées avec succès
     */
    public static function createSystemNotification(
        $db, $user_action, $type_code, $titre, $message, 
        $fk_object, $element_type, $user_ids_dest, 
        $url_action = '', $priorite_code = 'NORMALE', 
        $web_uniquement = 0, $date_envoi_programmee = null, $canal_pref = 'EMAIL'
    ) {
        if (!is_array($user_ids_dest)) {
            $user_ids_dest = array($user_ids_dest);
        }
        
        $count_success = 0;
        foreach ($user_ids_dest as $user_id) {
            if (empty($user_id)) {
                continue;
            }

            $notif = new ElaskaNotification($db);
            $notif->type_notification_code = $type_code;
            $notif->titre = $titre;
            $notif->message = $message;
            $notif->fk_user_dest = (int) $user_id;
            $notif->fk_object = (int) $fk_object;
            $notif->element_type = $element_type;
            $notif->url_action = $url_action;
            $notif->priorite_notification_code = $priorite_code;
            $notif->web_uniquement = (int) $web_uniquement;
            $notif->canal_notification_code_pref = $canal_pref;
            
            if (!empty($date_envoi_programmee)) {
                $notif->date_envoi_programmee = $date_envoi_programmee;
                $notif->statut_notification_code = 'PROGRAMMEE';
            } else {
                $notif->statut_notification_code = 'A_ENVOYER';
            }
            
            // L'objet $user_action (User) est passé à create()
            if ($notif->create($user_action) > 0) {
                $count_success++;
            } else {
                dol_syslog("ElaskaNotification::createSystemNotification Failed to create notification for user ".$user_id.": ".$notif->error, LOG_ERR);
            }
        }
        
        return $count_success;
    }

    /**
     * Récupère des notifications en fonction de critères de filtre
     *
     * @param DoliDB $db     Base de données
     * @param array  $filter Tableau de critères de filtrage
     * @param int    $limit  Limite du nombre de résultats (0 = pas de limite)
     * @param string $order  Ordre de tri SQL
     * @return array         Tableau d'objets ElaskaNotification
     */
    public static function getNotifications($db, $filter = array(), $limit = 0, $order = 'date_creation DESC')
    {
        $notifications = array();
        $sql = "SELECT n.rowid";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_notification as n";
        $sql .= " WHERE 1=1";
        
        // Appliquer les filtres
        if (!empty($filter['fk_user_dest'])) {
            $sql .= " AND n.fk_user_dest = ".(int) $filter['fk_user_dest'];
        }
        
        if (!empty($filter['type_notification_code'])) {
            $sql .= " AND n.type_notification_code = '".$db->escape($filter['type_notification_code'])."'";
        }
        
        if (!empty($filter['statut_notification_code'])) {
            if (is_array($filter['statut_notification_code'])) {
                $sql .= " AND n.statut_notification_code IN (";
                $first = true;
                foreach ($filter['statut_notification_code'] as $status) {
                    if (!$first) $sql .= ",";
                    $sql .= "'".$db->escape($status)."'";
                    $first = false;
                }
                $sql .= ")";
            } else {
                $sql .= " AND n.statut_notification_code = '".$db->escape($filter['statut_notification_code'])."'";
            }
        }
        
        if (isset($filter['date_lecture_is_null']) && $filter['date_lecture_is_null']) {
            $sql .= " AND n.date_lecture IS NULL";
        }
        
        if (!empty($filter['fk_object']) && !empty($filter['element_type'])) {
            $sql .= " AND n.fk_object = ".(int) $filter['fk_object'];
            $sql .= " AND n.element_type = '".$db->escape($filter['element_type'])."'";
        }
        
        if (isset($filter['web_uniquement'])) {
            $sql .= " AND n.web_uniquement = ".(int) $filter['web_uniquement'];
        }
        
        // Appliquer l'ordre et la limite
        if (!empty($order)) {
            $sql .= " ORDER BY ".$order;
        }
        
        if ($limit > 0) {
            $sql .= " LIMIT ".(int) $limit;
        }
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                $notification = new ElaskaNotification($db);
                if ($notification->fetch($obj->rowid) > 0) {
                    $notifications[] = $notification;
                }
            }
            $db->free($resql);
        } else { 
            dol_syslog("ElaskaNotification::getNotifications Error: ".$db->lasterror(), LOG_ERR); 
        }
        
        return $notifications;
    }

    // --- Méthodes pour lire les dictionnaires ---
    /**
     * Méthode générique pour récupérer les options depuis un dictionnaire
     *
     * @param object $langs                     Objet Translate de Dolibarr
     * @param string $dictionary_table_suffix_short Suffixe court du nom de la table dictionnaire
     * @param bool   $usekeys                   True pour retourner tableau associatif code=>label
     * @param bool   $show_empty                True pour ajouter une option vide
     * @return array                            Tableau d'options
     */
    private static function getBaseOptionsFromDictionary($langs, $dictionary_table_suffix_short, $usekeys = true, $show_empty = false) {
        global $db;
        
        $options = array();
        if ($show_empty) {
            $options[''] = $langs->trans("SelectAnOption");
        }
        
        $sql = "SELECT code, label, tooltip, classe_css_badge, couleur_code, picto 
                FROM ".MAIN_DB_PREFIX."c_elaska_notification_".$db->escape($dictionary_table_suffix_short)." 
                WHERE active = 1 
                ORDER BY position ASC, label ASC";
        
        $resql = $db->query($sql); 
        if ($resql) { 
            while ($obj = $db->fetch_object($resql)) { 
                if ($usekeys) {
                    $options[$obj->code] = $langs->trans($obj->label);
                } else { 
                    $obj->label_translated = $langs->trans($obj->label);
                    if (isset($obj->tooltip)) {
                        $obj->tooltip_translated = $langs->trans($obj->tooltip);
                    }
                    $options[] = $obj; 
                } 
            } 
            $db->free($resql); 
        } else { 
            dol_print_error($db); 
        } 
        
        return $options;
    }

    /**
     * Récupère les options de types de notification
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getTypeNotificationOptions($langs, $usekeys = true, $show_empty = false) {
        // Le dictionnaire des types n'a pas tooltip, classe_css_badge, etc. directement.
        global $db;
        
        $options = array();
        if ($show_empty) {
            $options[''] = $langs->trans("SelectAnOption");
        }
        
        $sql = "SELECT code, label, description 
                FROM ".MAIN_DB_PREFIX."c_elaska_notification_type 
                WHERE active = 1 
                ORDER BY position ASC, label ASC";
        
        $resql = $db->query($sql); 
        if ($resql) { 
            while ($obj = $db->fetch_object($resql)) { 
                if ($usekeys) {
                    $options[$obj->code] = $langs->trans($obj->label);
                } else { 
                    $obj->label_translated = $langs->trans($obj->label);
                    if (isset($obj->description)) {
                        $obj->description_translated = $langs->trans($obj->description);
                    }
                    $options[] = $obj; 
                } 
            } 
            $db->free($resql); 
        } else { 
            dol_print_error($db); 
        } 
        
        return $options;
    }

    /**
     * Récupère les options de statuts de notification
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getStatutNotificationOptions($langs, $usekeys = true, $show_empty = false) {
        return self::getBaseOptionsFromDictionary($langs, 'statut', $usekeys, $show_empty);
    }

    /**
     * Récupère les options de priorités de notification
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getPrioriteNotificationOptions($langs, $usekeys = true, $show_empty = false) {
        return self::getBaseOptionsFromDictionary($langs, 'priorite', $usekeys, $show_empty);
    }

    /**
     * Récupère les options de canaux de notification
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getCanalNotificationOptions($langs, $usekeys = true, $show_empty = false) {
        return self::getBaseOptionsFromDictionary($langs, 'canal', $usekeys, $show_empty);
    }
}
}
?>
