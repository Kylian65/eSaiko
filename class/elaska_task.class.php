<?php
/**
 * eLaska - Classe pour gérer les tâches
 * Date: 2025-05-30
 * Version: 4.0 (Version finale pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';

if (!class_exists('ElaskaTask', false)) {

class ElaskaTask extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_task';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_task';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'tasks@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var string Référence unique de la tâche
     */
    public $ref;
    
    /**
     * @var int ID du dossier associé
     */
    public $fk_dossier;
    
    /**
     * @var int ID de l'étape de timeline associée
     */
    public $fk_timeline_etape;
    
    /**
     * @var string Libellé de la tâche
     */
    public $label;
    
    /**
     * @var string Description détaillée de la tâche
     */
    public $description;
    
    /**
     * @var string Date de début prévue (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_start_planned;
    
    /**
     * @var string Date de fin prévue (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_end_planned;
    
    /**
     * @var string Date de début réelle (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_start_real;
    
    /**
     * @var string Date de fin réelle (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_end_real;
    
    /**
     * @var int ID de l'utilisateur assigné à la tâche
     */
    public $fk_user_assign;
    
    /**
     * @var int ID de l'utilisateur auteur de la tâche
     */
    public $fk_user_author;
    
    /**
     * @var int Pourcentage d'avancement (0-100)
     */
    public $progress;
    
    /**
     * @var string Code de priorité (dictionnaire)
     */
    public $priorite_task_code;
    
    /**
     * @var string Code de statut (dictionnaire)
     */
    public $statut_task_code;
    
    /**
     * @var string Code de type de tâche (dictionnaire)
     */
    public $type_task_code;
    
    /**
     * @var string Notes privées (visibles seulement par les employés)
     */
    public $notes_private;
    
    /**
     * @var string Notes publiques (potentiellement visibles par les clients)
     */
    public $notes_public;
    
    /**
     * @var float Charge de travail prévue en heures
     */
    public $planned_workload;
    
    /**
     * @var float Temps passé total en heures
     */
    public $spent_time;

    // Champs techniques
    public $rowid;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $status;
    public $entity;

    /**
     * @var array Historique des temps passés sur cette tâche
     */
    public $time_spent_records = array();

    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'ref' => array('type' => 'varchar(128)', 'label' => 'RefTask', 'enabled' => 1, 'position' => 10, 'notnull' => 0, 'visible' => 1, 'unique' => 1),
        'label' => array('type' => 'varchar(255)', 'label' => 'LabelTask', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'fk_dossier' => array('type' => 'integer:ElaskaDossier:custom/elaska/class/elaska_dossier.class.php', 'label' => 'DossierLieTask', 'enabled' => 1, 'position' => 25, 'notnull' => 0, 'visible' => 1),
        'fk_timeline_etape' => array('type' => 'integer:ElaskaDossierTimelineEtape:custom/elaska/class/elaska_dossier_timeline.class.php', 'label' => 'EtapeTimelineLieeTask', 'enabled' => 1, 'position' => 26, 'notnull' => 0, 'visible' => 1),
        'type_task_code' => array('type' => 'varchar(30)', 'label' => 'TypeTache', 'enabled' => 1, 'position' => 27, 'notnull' => 0, 'visible' => 1),
        'description' => array('type' => 'text', 'label' => 'DescriptionTask', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1, 'textarea_html' => 1),
        'date_start_planned' => array('type' => 'datetime', 'label' => 'DateDebutPrevueTask', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'date_end_planned' => array('type' => 'datetime', 'label' => 'DateFinPrevueTask', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'date_start_real' => array('type' => 'datetime', 'label' => 'DateDebutReelleTask', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'date_end_real' => array('type' => 'datetime', 'label' => 'DateFinReelleTask', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'fk_user_assign' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'AssigneATask', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'fk_user_author' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'AuteurTache', 'enabled' => 1, 'position' => 85, 'notnull' => 0, 'visible' => 0),
        'progress' => array('type' => 'integer', 'label' => 'AvancementTask', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'priorite_task_code' => array('type' => 'varchar(20)', 'label' => 'PrioriteTask', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1, 'default' => 'NORMAL'),
        'statut_task_code' => array('type' => 'varchar(20)', 'label' => 'StatutTask', 'enabled' => 1, 'position' => 110, 'notnull' => 1, 'visible' => 1, 'default' => 'TODO'),
        'planned_workload' => array('type' => 'double(24,2)', 'label' => 'ChargePrevueHeuresTask', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'spent_time' => array('type' => 'double(24,2)', 'label' => 'TempsPasseHeuresTask', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'notes_public' => array('type' => 'text', 'label' => 'NotesPubliquesTask', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'notes_private' => array('type' => 'text', 'label' => 'NotesPriveesTask', 'enabled' => 1, 'position' => 150, 'notnull' => 0, 'visible' => 0),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 1, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => -2),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => 0, 'visible' => -2),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => -2),
        'status' => array('type' => 'integer', 'label' => 'ActiveStatus', 'enabled' => 1, 'position' => 1001, 'notnull' => 1, 'visible' => 0, 'default' => 1),
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'default' => 1, 'enabled' => 1, 'visible' => -2, 'notnull' => 1, 'position' => 1002)
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
        if (empty($this->statut_task_code)) $this->statut_task_code = 'TODO';
        if (empty($this->priorite_task_code)) $this->priorite_task_code = 'NORMAL';
        if (!isset($this->progress)) $this->progress = 0;
        if (!isset($this->spent_time)) $this->spent_time = 0;
        if (!isset($this->status)) $this->status = 1;
        
        $this->time_spent_records = array();
    }

    /**
     * Crée une tâche dans la base de données
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

        // Vérifications et valeurs par défaut
        if (empty($this->label)) {
            $this->error = "Le libellé de la tâche est obligatoire";
            $this->db->rollback();
            return -1;
        }

        // Laissé vide pour ElaskaNumero
        $this->ref = '';
        
        // L'auteur logique de la tâche
        if (empty($this->fk_user_author) && is_object($user)) {
            $this->fk_user_author = $user->id;
        }
        
        // Date de début par défaut = maintenant pour les tâches TODO
        if (empty($this->date_start_planned) && $this->statut_task_code == 'TODO') {
            $this->date_start_planned = dol_now();
        }

        $result = $this->createCommon($user, $notrigger);
        
        if ($result > 0) {
            // Génération de la référence avec ElaskaNumero
            $params = array();
            
            // Paramètres pour le masque de numérotation
            if (!empty($this->type_task_code)) {
                $params['TYPE_TACHE'] = $this->type_task_code;
            }
            
            // Récupérer la référence du dossier parent si existant
            if (!empty($this->fk_dossier)) {
                require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dossier.class.php';
                $dossier = new ElaskaDossier($this->db);
                if ($dossier->fetch($this->fk_dossier) > 0) {
                    $params['DOSSIER_REF'] = $dossier->ref;
                }
            }

            // Générer la référence définitive
            $final_ref = ElaskaNumero::generateAndRecord(
                $this->db, 
                'elaska', 
                $this->element, 
                $this->id, 
                '', // Masque par défaut
                $params, 
                $this->entity
            );

            if ($final_ref !== -1 && !empty($final_ref)) {
                $this->ref = $final_ref;
                if (!$this->updateRef($user)) {
                    $error++;
                    dol_syslog("ElaskaTask::create Échec de la mise à jour de la référence définitive pour ".$this->element." ".$this->id, LOG_ERR);
                }
            } else {
                $error++;
                dol_syslog("ElaskaTask::create Échec de la génération de la référence définitive pour ".$this->element." ".$this->id, LOG_ERR);
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
     * Charge une tâche depuis la base de données
     *
     * @param int    $id  ID de la tâche
     * @param string $ref Référence de la tâche
     * @return int        <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = null)
    {
        $result = $this->fetchCommon($id, $ref);
        
        if ($result > 0) {
            // Charger les temps passés associés
            $this->getTimeSpentRecords();
        }
        
        return $result;
    }

    /**
     * Met à jour une tâche dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Recalculer le pourcentage d'avancement
        $this->progress = $this->calculateProgress();
        
        // Gérer les dates réelles en fonction du statut
        if ($this->statut_task_code == 'IN_PROGRESS' && empty($this->date_start_real)) {
            $this->date_start_real = dol_now();
        }
        
        if ($this->statut_task_code == 'DONE' && empty($this->date_end_real)) {
            $this->date_end_real = dol_now();
        }
        
        return $this->updateCommon($user, $notrigger);
    }

    /**
     * Supprime une tâche de la base de données avec tous ses éléments liés
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        $this->db->begin();
        $error = 0;

        // 1. Supprimer les temps passés associés
        if ($this->deleteAllSpentTime() < 0) {
            $this->error = "Erreur lors de la suppression des temps passés pour la tâche ".$this->id;
            $error++;
        }

        // 2. Supprimer les liaisons avec les étapes de timeline
        $sql_unlink = "DELETE FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_task";
        $sql_unlink .= " WHERE fk_task = ".(int)$this->id;
        $sql_unlink .= " AND type_task_link = '".$this->db->escape($this->element)."'";
        
        if (!$this->db->query($sql_unlink)) {
            $this->error = "Erreur lors de la dissociation de la tâche des étapes de timeline: ".$this->db->lasterror();
            $error++;
        }
        
        // 3. Supprimer les associations avec d'autres éléments eLaska (à adapter selon vos besoins)
        // Par exemple: événements, notifications, etc.

        // 4. Supprimer la tâche elle-même
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
            dol_syslog(get_class($this)."::delete Error: ".$this->error, LOG_ERR);
            return -1;
        }
    }
    
    /**
     * Change le statut d'une tâche
     *
     * @param User   $user               Utilisateur qui effectue l'action
     * @param string $nouveau_statut_code Code du nouveau statut
     * @return int                       <0 si erreur, >0 si OK
     */
    public function setStatus($user, $nouveau_statut_code)
    {
        if (empty($this->id)) return -1;
        
        // Vérifier si le statut est valide (pourrait être vérifié contre le dictionnaire)
        $statut_valides = array('TODO', 'IN_PROGRESS', 'DONE', 'CANCELLED', 'BLOCKED');
        if (!in_array($nouveau_statut_code, $statut_valides)) {
            $this->error = "Statut non valide: ".$nouveau_statut_code;
            return -2;
        }
        
        // Mettre à jour le statut
        $this->statut_task_code = $nouveau_statut_code;
        
        // Gérer les dates réelles en fonction du statut
        if ($nouveau_statut_code == 'IN_PROGRESS' && empty($this->date_start_real)) {
            $this->date_start_real = dol_now();
        }
        
        if ($nouveau_statut_code == 'DONE' && empty($this->date_end_real)) {
            $this->date_end_real = dol_now();
            
            // Pour les tâches terminées, s'assurer que le progrès est à 100%
            $this->progress = 100;
        }
        
        return $this->update($user, 1); // notrigger = 1 pour éviter les triggers en cascade
    }

    /**
     * Ajoute un temps passé sur la tâche
     *
     * @param User   $user            Utilisateur qui saisit le temps
     * @param float  $time_spent_hours Temps passé en heures
     * @param string $comment         Commentaire sur le temps passé
     * @param int    $date_record     Date d'enregistrement (timestamp Unix)
     * @param int    $fk_dossier_id   ID du dossier (optionnel, sinon utilise celui de la tâche)
     * @return int                    <0 si erreur, ID de l'enregistrement si OK
     */
    public function addSpentTime($user, $time_spent_hours, $comment = '', $date_record = null, $fk_dossier_id = 0)
    {
        global $conf;
        
        if (empty($this->id)) return -1;
        
        // Vérifier que le temps est positif
        if ($time_spent_hours <= 0) {
            $this->error = "Le temps passé doit être supérieur à zéro";
            return -2;
        }
        
        // Date par défaut = maintenant
        if (is_null($date_record)) {
            $date_record = dol_now();
        }
        
        // Utiliser le dossier associé à la tâche si aucun n'est spécifié
        if ($fk_dossier_id <= 0) {
            $fk_dossier_id = $this->fk_dossier;
        }
        
        $this->db->begin();
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_task_time";
        $sql .= " (fk_task, fk_user, date_record, time_spent_hours, comment, fk_dossier, entity)";
        $sql .= " VALUES (";
        $sql .= (int)$this->id . ", ";
        $sql .= (int)$user->id . ", ";
        $sql .= "'" . $this->db->idate($date_record) . "', ";
        $sql .= (float)$time_spent_hours . ", ";
        $sql .= "'" . $this->db->escape($comment) . "', ";
        $sql .= (int)$fk_dossier_id . ", ";
        $sql .= (int)$conf->entity;
        $sql .= ")";
        
        $resql = $this->db->query($sql);
        
        if (!$resql) {
            $this->error = "Erreur lors de l'ajout du temps passé: " . $this->db->lasterror();
            $this->db->rollback();
            return -3;
        }
        
        $new_time_id = $this->db->last_insert_id(MAIN_DB_PREFIX."elaska_task_time");
        
        // Mettre à jour le temps total passé et éventuellement le progrès
        $this->spent_time = (float)$this->spent_time + (float)$time_spent_hours;
        $this->progress = $this->calculateProgress();
        
        // Si c'est le premier temps passé, mettre à jour date_start_real
        if (empty($this->date_start_real)) {
            $this->date_start_real = $date_record;
        }
        
        // Si tâche en statut TODO, passer en IN_PROGRESS avec première saisie de temps
        if ($this->statut_task_code == 'TODO') {
            $this->statut_task_code = 'IN_PROGRESS';
        }
        
        // Mettre à jour la tâche avec les nouvelles valeurs
        $result = $this->update($user, 1); // notrigger = 1
        
        if ($result < 0) {
            $this->db->rollback();
            return -4;
        }
        
        $this->db->commit();
        return $new_time_id;
    }
    
    /**
     * Récupère la liste des temps passés sur cette tâche
     *
     * @return array Liste des enregistrements de temps
     */
    public function getTimeSpentRecords()
    {
        $this->time_spent_records = array();
        
        if (empty($this->id)) return $this->time_spent_records;
        
        $sql = "SELECT tt.*, u.login as user_login, u.firstname, u.lastname, u.photo";
        $sql.= " FROM ".MAIN_DB_PREFIX."elaska_task_time as tt";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON tt.fk_user = u.rowid";
        $sql.= " WHERE tt.fk_task = ".(int)$this->id;
        $sql.= " ORDER BY tt.date_record DESC";
        
        $resql = $this->db->query($sql);
        
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                // Convertir les dates en timestamp
                if (!empty($obj->date_record)) {
                    $obj->date_record_ts = $this->db->jdate($obj->date_record);
                }
                
                $this->time_spent_records[] = $obj;
            }
            $this->db->free($resql);
        } else {
            dol_syslog(get_class($this)."::getTimeSpentRecords Error ".$this->db->lasterror(), LOG_ERR);
        }
        
        return $this->time_spent_records;
    }
    
    /**
     * Supprime un enregistrement de temps passé spécifique
     *
     * @param int  $time_id ID de l'enregistrement de temps
     * @param User $user    Utilisateur qui effectue la suppression
     * @return int          <0 si erreur, >0 si OK
     */
    public function deleteSpentTime($time_id, $user)
    {
        if (empty($this->id)) return -1;
        
        $this->db->begin();
        
        // 1. Récupérer le temps à supprimer pour connaître sa valeur
        $sql = "SELECT time_spent_hours FROM ".MAIN_DB_PREFIX."elaska_task_time";
        $sql.= " WHERE rowid = ".(int)$time_id;
        $sql.= " AND fk_task = ".(int)$this->id;
        
        $resql = $this->db->query($sql);
        
        if (!$resql || $this->db->num_rows($resql) == 0) {
            $this->error = "Enregistrement de temps non trouvé ou n'appartient pas à cette tâche";
            $this->db->rollback();
            return -2;
        }
        
        $obj = $this->db->fetch_object($resql);
        $time_to_remove = (float)$obj->time_spent_hours;
        
        // 2. Supprimer l'enregistrement
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_task_time";
        $sql.= " WHERE rowid = ".(int)$time_id;
        $sql.= " AND fk_task = ".(int)$this->id;
        
        if (!$this->db->query($sql)) {
            $this->error = "Erreur lors de la suppression du temps passé: ".$this->db->lasterror();
            $this->db->rollback();
            return -3;
        }
        
        // 3. Mettre à jour le temps total passé et le progrès
        $this->spent_time = max(0, (float)$this->spent_time - $time_to_remove);
        $this->progress = $this->calculateProgress();
        
        $result = $this->update($user, 1); // notrigger = 1
        
        if ($result < 0) {
            $this->db->rollback();
            return -4;
        }
        
        $this->db->commit();
        return 1;
    }
    
    /**
     * Supprime tous les enregistrements de temps passé pour cette tâche
     *
     * @return int <0 si erreur, nombre d'enregistrements supprimés si OK
     */
    public function deleteAllSpentTime()
    {
        if (empty($this->id)) return 0;
        
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_task_time";
        $sql.= " WHERE fk_task = ".(int)$this->id;
        
        if ($this->db->query($sql)) {
            return $this->db->affected_rows($sql);
        } else {
            $this->error = "Erreur lors de la suppression des temps passés: ".$this->db->lasterror();
            dol_syslog(get_class($this)."::deleteAllSpentTime Error: ".$this->error, LOG_ERR);
            return -1;
        }
    }
    
    /**
     * Calcule le pourcentage d'avancement de la tâche
     *
     * @return int Pourcentage d'avancement (0-100)
     */
    public function calculateProgress()
    {
        // Si charge prévue et temps passé définis, calculer le % d'avancement
        if (!empty($this->planned_workload) && $this->planned_workload > 0 && $this->spent_time > 0) {
            $progress = round(((float)$this->spent_time / (float)$this->planned_workload) * 100);
            return min($progress, 100);
        }
        
        // Si tâche terminée sans charge prévue, 100%
        if ($this->statut_task_code == 'DONE' && (empty($this->planned_workload) || $this->planned_workload == 0)) {
            return 100;
        }
        
        // Si tâche revenue à TODO sans temps passé, 0%
        if ($this->statut_task_code == 'TODO' && $this->spent_time == 0) {
            return 0;
        }
        
        // Sinon, conserver le progrès actuel
        return $this->progress;
    }
    
    /**
     * Récupère l'utilisateur assigné à la tâche
     *
     * @param string $output_type Type de sortie: 'object' pour l'objet User, 'html' pour son NomUrl
     * @return mixed              Objet User, HTML ou null si aucun utilisateur assigné
     */
    public function getAssignedUser($output_type = 'object')
    {
        if ($this->fk_user_assign > 0) {
            require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
            
            $user_assigned = new User($this->db);
            if ($user_assigned->fetch($this->fk_user_assign) > 0) {
                if ($output_type == 'html') {
                    return $user_assigned->getNomUrl(1);
                } else {
                    return $user_assigned;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Formate une durée en heures selon différents formats
     *
     * @param float  $hours  Durée en heures
     * @param string $format Format de sortie: 'hours', 'short', 'long'
     * @return string        Durée formatée
     */
    public static function formatDuration($hours, $format = 'hours')
    {
        global $langs;
        
        if ($hours <= 0) return '';
        
        // Format simple en heures et minutes (ex: 3h15)
        if ($format == 'hours') {
            $h = floor($hours);
            $m = round(($hours - $h) * 60);
            
            if ($h > 0 && $m > 0) {
                return $h.'h'.$m;
            } elseif ($h > 0) {
                return $h.'h';
            } else {
                return $m.'min';
            }
        }
        
        // Format court en jours, heures, minutes (ex: 1j 2h 30min)
        if ($format == 'short') {
            $d = floor($hours / 8);
            $h = floor($hours % 8);
            $m = round((($hours * 60) % 60));
            
            $result = array();
            if ($d > 0) $result[] = $d.'j';
            if ($h > 0) $result[] = $h.'h';
            if ($m > 0) $result[] = $m.'min';
            
            return implode(' ', $result);
        }
        
        // Format long avec traduction (ex: 1 Jour 2 Heures 30 Minutes)
        $d = floor($hours / 8);
        $h = floor($hours % 8);
        $m = round((($hours * 60) % 60));
        
        $result = array();
        if ($d > 0) {
            $result[] = $d.' '.($d > 1 ? $langs->transnoentitiesnoconv("Days") : $langs->transnoentitiesnoconv("Day"));
        }
        if ($h > 0) {
            $result[] = $h.' '.($h > 1 ? $langs->transnoentitiesnoconv("Hours") : $langs->transnoentitiesnoconv("Hour"));
        }
        if ($m > 0) {
            $result[] = $m.' '.($m > 1 ? $langs->transnoentitiesnoconv("Minutes") : $langs->transnoentitiesnoconv("Minute"));
        }
        
        return implode(' ', $result);
    }
    
    /**
     * Récupère les tâches pour un dossier donné
     *
     * @param DoliDB $db        Base de données
     * @param int    $fk_dossier ID du dossier
     * @param string $status     Filtre optionnel sur le statut
     * @param string $orderBy    Ordre de tri (par défaut: date_start_planned DESC)
     * @return array|int         Tableau d'objets ElaskaTask ou <0 si erreur
     */
    public static function getTasksByDossier($db, $fk_dossier, $status = '', $orderBy = 'date_start_planned DESC')
    {
        $tasks = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_task";
        $sql.= " WHERE fk_dossier = ".(int)$fk_dossier;
        if (!empty($status)) {
            $sql.= " AND statut_task_code = '".$db->escape($status)."'";
        }
        $sql.= " ORDER BY ".$db->escape($orderBy);
        
        $resql = $db->query($sql);
        if (!$resql) {
            dol_syslog("ElaskaTask::getTasksByDossier Error ".$db->lasterror(), LOG_ERR);
            return -1;
        }
        
        while ($obj = $db->fetch_object($resql)) {
            $task = new ElaskaTask($db);
            if ($task->fetch($obj->rowid) > 0) {
                $tasks[] = $task;
            }
        }
        
        $db->free($resql);
        return $tasks;
    }
    
    /**
     * Récupère les tâches assignées à un utilisateur
     *
     * @param DoliDB $db              Base de données
     * @param int    $fk_user_assign  ID de l'utilisateur
     * @param string $status          Filtre optionnel sur le statut
     * @param int    $limit           Nombre maximum de résultats (0 = pas de limite)
     * @return array|int              Tableau d'objets ElaskaTask ou <0 si erreur
     */
    public static function getTasksByUser($db, $fk_user_assign, $status = '', $limit = 0)
    {
        $tasks = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_task";
        $sql.= " WHERE fk_user_assign = ".(int)$fk_user_assign;
        if (!empty($status)) {
            $sql.= " AND statut_task_code = '".$db->escape($status)."'";
        } else {
            // Par défaut, exclure les tâches terminées et annulées
            $sql.= " AND statut_task_code NOT IN ('DONE', 'CANCELLED')";
        }
        $sql.= " ORDER BY date_start_planned ASC";
        if ($limit > 0) {
            $sql.= " LIMIT ".(int)$limit;
        }
        
        $resql = $db->query($sql);
        if (!$resql) {
            dol_syslog("ElaskaTask::getTasksByUser Error ".$db->lasterror(), LOG_ERR);
            return -1;
        }
        
        while ($obj = $db->fetch_object($resql)) {
            $task = new ElaskaTask($db);
            if ($task->fetch($obj->rowid) > 0) {
                $tasks[] = $task;
            }
        }
        
        $db->free($resql);
        return $tasks;
    }
    
    /**
     * Méthode générique pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                  Objet de traduction
     * @param string    $dictionary_suffix      Suffixe du nom de la table dictionnaire
     * @param bool      $usekeys                True pour retourner tableau associatif code=>label
     * @param bool      $show_empty             True pour ajouter une option vide
     * @return array                            Tableau d'options
     */
    private static function getBaseOptionsFromDictionary($langs, $dictionary_suffix, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label, picto FROM ".MAIN_DB_PREFIX."c_elaska_task_".$db->escape($dictionary_suffix);
        $sql.= " WHERE active = 1";
        $sql.= " ORDER BY position ASC, label ASC";
        
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
                    if (!empty($obj->picto)) $obj_opt->picto = $obj->picto;
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
     * Récupère les options du dictionnaire de statuts de tâche
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutTaskOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getBaseOptionsFromDictionary($langs, 'statut', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire de priorités de tâche
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getPrioriteTaskOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getBaseOptionsFromDictionary($langs, 'priorite', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire de types de tâche
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeTaskOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getBaseOptionsFromDictionary($langs, 'type', $usekeys, $show_empty);
    }
}
}
?>
