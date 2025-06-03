<?php
/**
 * eLaska - Classe principale pour gérer les dossiers clients
 * Date: 2025-05-30
 * Version: 3.0 (Documentation complète, implémentation des TODOs)
 * Auteur: Kylian65 / IA Collaboration
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
// require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dossier_timeline.class.php';

if (!class_exists('ElaskaDossier', false)) {

class ElaskaDossier extends CommonObject
{
    public $element = 'elaska_dossier';
    public $table_element = 'elaska_dossier';
    public $picto = 'folder@elaska';
    public $ismultientitymanaged = 1;

    // --- Champs de la table llx_elaska_dossier ---
    public $rowid;
    public $ref;                       // Référence unique du dossier
    public $fk_elaska_tiers;           // Lien vers la table elaska_tiers
    public $type_dossier_code;         // Type de dossier (code du dictionnaire)
    public $label;                     // Libellé descriptif du dossier
    public $description;               // Description détaillée
    public $statut_global_dossier_code;// Statut global (code du dictionnaire)
    public $progress;                  // Avancement en pourcentage (0-100)
    public $priorite_code;             // Priorité (code du dictionnaire)
    public $fk_user_resp;              // Responsable du dossier (ID utilisateur)
    public $date_ouverture;            // Date d'ouverture du dossier
    public $date_echeance_globale;     // Date d'échéance globale
    public $date_cloture_effective;    // Date de clôture effective
    public $objectifs;                 // Objectifs du dossier
    public $contraintes;               // Contraintes associées au dossier
    public $resultats_attendus;        // Résultats attendus
    public $next_action;               // Prochaine action à réaliser
    public $next_action_date;          // Date prévue pour la prochaine action
    public $budget_estime_global;      // Budget total estimé
    public $temps_total_estime_heures; // Temps total estimé (en heures)
    public $temps_total_passe_heures;  // Temps total passé (en heures)
    public $montant_ht;                // Montant HT (si applicable)
    public $tva_tx;                    // Taux de TVA (si applicable)
    public $montant_ttc;               // Montant TTC (si applicable)
    public $mode_facturation_code;     // Mode de facturation (code du dictionnaire)
    public $source_acquisition_code;   // Source d'acquisition du dossier (code du dictionnaire)
    public $fk_project;                // Projet Dolibarr lié (si applicable)
    public $fk_propal;                 // Proposition commerciale liée
    public $fk_commande;               // Commande liée
    public $fk_facture;                // Facture liée
    public $fk_contact_principal;      // Contact principal chez le client
    public $notes_internes;            // Notes internes (non visibles par le client)
    
    // Champs techniques explicitement déclarés
    public $entity;                    // Entité Dolibarr
    public $date_creation;             // Date de création en base
    public $tms;                       // Date de dernière modification
    public $fk_user_creat;             // Utilisateur créateur
    public $fk_user_modif;             // Dernier utilisateur modificateur
    public $import_key;                // Clé d'import
    public $status;                    // Statut technique (actif/inactif)

    public $timeline_etapes = array(); // Pour charger les étapes de la timeline associée

    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'ref' => array('type' => 'varchar(128)', 'label' => 'Ref', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1, 'unique' => 1),
        'fk_elaska_tiers' => array('type' => 'integer:ElaskaTiers:custom/elaska/class/elaska_tiers.class.php', 'label' => 'ClientELaska', 'enabled' => 1, 'position' => 15, 'notnull' => 1, 'visible' => 1),
        'type_dossier_code' => array('type' => 'varchar(50)', 'label' => 'TypeDossier', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'label' => array('type' => 'varchar(255)', 'label' => 'Label', 'enabled' => 1, 'position' => 30, 'notnull' => 1, 'visible' => 1),
        'description' => array('type' => 'text', 'label' => 'Description', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'statut_global_dossier_code' => array('type' => 'varchar(50)', 'label' => 'StatutGlobal', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'progress' => array('type' => 'integer', 'label' => 'AvancementGlobalPct', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'priorite_code' => array('type' => 'varchar(50)', 'label' => 'Priorite', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'fk_user_resp' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'ResponsableDossier', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'date_ouverture' => array('type' => 'date', 'label' => 'DateOuverture', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'date_echeance_globale' => array('type' => 'date', 'label' => 'DateEcheanceGlobale', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'date_cloture_effective' => array('type' => 'date', 'label' => 'DateClotureEffective', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'objectifs' => array('type' => 'text', 'label' => 'Objectifs', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'contraintes' => array('type' => 'text', 'label' => 'Contraintes', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'resultats_attendus' => array('type' => 'text', 'label' => 'ResultatsAttendus', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'next_action' => array('type' => 'text', 'label' => 'ProchaineAction', 'enabled' => 1, 'position' => 150, 'notnull' => 0, 'visible' => 1),
        'next_action_date' => array('type' => 'datetime', 'label' => 'DateProchaineAction', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        'budget_estime_global' => array('type' => 'double(24,8)', 'label' => 'BudgetEstimeGlobal', 'enabled' => 1, 'position' => 170, 'notnull' => 0, 'visible' => 1),
        'temps_total_estime_heures' => array('type' => 'double(24,2)', 'label' => 'TempsTotalEstimeHeures', 'enabled' => 1, 'position' => 180, 'notnull' => 0, 'visible' => 1),
        'temps_total_passe_heures' => array('type' => 'double(24,2)', 'label' => 'TempsTotalPasseHeures', 'enabled' => 1, 'position' => 190, 'notnull' => 0, 'visible' => 1),
        'montant_ht' => array('type' => 'double(24,8)', 'label' => 'MontantHT', 'enabled' => 1, 'position' => 200, 'notnull' => 0, 'visible' => 1),
        'tva_tx' => array('type' => 'double(6,3)', 'label' => 'TauxTVA', 'enabled' => 1, 'position' => 210, 'notnull' => 0, 'visible' => 1),
        'montant_ttc' => array('type' => 'double(24,8)', 'label' => 'MontantTTC', 'enabled' => 1, 'position' => 220, 'notnull' => 0, 'visible' => 1),
        'mode_facturation_code' => array('type' => 'varchar(50)', 'label' => 'ModeFacturation', 'enabled' => 1, 'position' => 230, 'notnull' => 0, 'visible' => 1),
        'source_acquisition_code' => array('type' => 'varchar(50)', 'label' => 'SourceAcquisition', 'enabled' => 1, 'position' => 240, 'notnull' => 0, 'visible' => 1),
        'fk_project' => array('type' => 'integer:Project:projet/class/project.class.php', 'label' => 'ProjetLie', 'enabled' => 1, 'position' => 250, 'notnull' => 0, 'visible' => 1),
        'fk_propal' => array('type' => 'integer:Propal:comm/propal/class/propal.class.php', 'label' => 'PropalLiee', 'enabled' => 1, 'position' => 260, 'notnull' => 0, 'visible' => 1),
        'fk_commande' => array('type' => 'integer:Commande:commande/class/commande.class.php', 'label' => 'CommandeLiee', 'enabled' => 1, 'position' => 270, 'notnull' => 0, 'visible' => 1),
        'fk_facture' => array('type' => 'integer:Facture:compta/facture/class/facture.class.php', 'label' => 'FactureLiee', 'enabled' => 1, 'position' => 280, 'notnull' => 0, 'visible' => 1),
        'fk_contact_principal' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactPrincipal', 'enabled' => 1, 'position' => 290, 'notnull' => 0, 'visible' => 1), // Lien vers llx_socpeople
        'notes_internes' => array('type' => 'text', 'label' => 'NotesInternes', 'enabled' => 1, 'position' => 300, 'notnull' => 0, 'visible' => 0),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 1, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => -2),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => 0, 'visible' => -2),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => -2),
        'status' => array('type' => 'integer', 'label' => 'StatusRecord', 'enabled' => 1, 'position' => 1001, 'notnull' => 1, 'visible' => 0, 'default' => 1), // Utiliser statut_global_dossier_code pour le statut métier
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
        if (empty($this->statut_global_dossier_code)) $this->statut_global_dossier_code = 'BROUILLON'; // Ou 'OUVERT'
        if (empty($this->priorite_code)) $this->priorite_code = 'NORMALE';
        if (!isset($this->progress)) $this->progress = 0;
        if (!isset($this->status)) $this->status = 1;
    }

    /**
     * Crée un dossier dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        if (empty($this->ref)) {
            $this->ref = $this->getNextNumero($this->db);
            if ($this->ref == -1) {
                $this->error = "Failed to generate reference number.";
                dol_syslog(get_class($this)."::create ".$this->error, LOG_ERR);
                return -1;
            }
        }
        
        if (empty($this->date_ouverture) && $this->statut_global_dossier_code != 'BROUILLON') {
             $this->date_ouverture = dol_now();
        }
        
        return $this->createCommon($user, $notrigger);
    }

    /**
     * Charge un dossier de la base de données
     *
     * @param int    $id    Id du dossier
     * @param string $ref   Référence du dossier
     * @return int          <0 si KO, >0 si OK
     */
    public function fetch($id, $ref = null)
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Met à jour un dossier dans la base de données
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
     * Supprime un dossier de la base de données et ses éléments liés
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        global $conf;
        
        $this->db->begin();
        
        // 1. Supprimer les timelines associées
        if (file_exists(DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dossier_timeline.class.php')) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dossier_timeline.class.php';
            
            $timeline = new ElaskaDossierTimeline($this->db);
            $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$timeline->table_element;
            $sql.= " WHERE fk_dossier = ".(int) $this->id;
            
            $resql = $this->db->query($sql);
            if ($resql) {
                while ($obj = $this->db->fetch_object($resql)) {
                    if ($timeline->fetch($obj->rowid) > 0) {
                        if ($timeline->delete($user, 1) < 0) {
                            $this->error = $timeline->error;
                            $this->db->rollback();
                            return -1;
                        }
                    }
                }
                $this->db->free($resql);
            } else {
                $this->error = $this->db->lasterror();
                $this->db->rollback();
                return -2;
            }
        }
        
        // 2. Délier/Supprimer les tâches associées
        if (file_exists(DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_task.class.php')) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_task.class.php';
            
            // Option 1: Supprimer les tâches
            if (!empty($conf->global->ELASKA_DELETE_TASKS_WITH_DOSSIER)) {
                $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_task";
                $sql.= " WHERE fk_elaska_dossier = ".(int) $this->id;
                
                $resql = $this->db->query($sql);
                if ($resql) {
                    $task = new ElaskaTask($this->db);
                    while ($obj = $this->db->fetch_object($resql)) {
                        if ($task->fetch($obj->rowid) > 0) {
                            if ($task->delete($user, 1) < 0) {
                                $this->error = $task->error;
                                $this->db->rollback();
                                return -3;
                            }
                        }
                    }
                    $this->db->free($resql);
                } else {
                    $this->error = $this->db->lasterror();
                    $this->db->rollback();
                    return -4;
                }
            } 
            // Option 2: Délier les tâches (sans les supprimer)
            else {
                $sql = "UPDATE ".MAIN_DB_PREFIX."elaska_task";
                $sql.= " SET fk_elaska_dossier = NULL";
                $sql.= " WHERE fk_elaska_dossier = ".(int) $this->id;
                
                $resql = $this->db->query($sql);
                if (!$resql) {
                    $this->error = $this->db->lasterror();
                    $this->db->rollback();
                    return -5;
                }
            }
        }
        
        // 3. Délier/Supprimer les documents associés
        if (file_exists(DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php')) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
            
            // Option 1: Supprimer les documents
            if (!empty($conf->global->ELASKA_DELETE_DOCUMENTS_WITH_DOSSIER)) {
                $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_document";
                $sql.= " WHERE fk_object = ".(int) $this->id;
                $sql.= " AND fk_object_type = '".$this->db->escape($this->element)."'";
                
                $resql = $this->db->query($sql);
                if ($resql) {
                    $doc = new ElaskaDocument($this->db);
                    while ($obj = $this->db->fetch_object($resql)) {
                        if ($doc->fetch($obj->rowid) > 0) {
                            if ($doc->delete($user, 1) < 0) {
                                $this->error = $doc->error;
                                $this->db->rollback();
                                return -6;
                            }
                        }
                    }
                    $this->db->free($resql);
                } else {
                    $this->error = $this->db->lasterror();
                    $this->db->rollback();
                    return -7;
                }
            } 
            // Option 2: Délier les documents (sans les supprimer)
            else {
                $sql = "UPDATE ".MAIN_DB_PREFIX."elaska_document";
                $sql.= " SET fk_object = NULL, fk_object_type = NULL";
                $sql.= " WHERE fk_object = ".(int) $this->id;
                $sql.= " AND fk_object_type = '".$this->db->escape($this->element)."'";
                
                $resql = $this->db->query($sql);
                if (!$resql) {
                    $this->error = $this->db->lasterror();
                    $this->db->rollback();
                    return -8;
                }
            }
        }
        
        // 4. Suppression du dossier lui-même
        $result = $this->deleteCommon($user, $notrigger);
        if ($result < 0) {
            $this->db->rollback();
            return -9;
        }
        
        $this->db->commit();
        return $result;
    }

    // --- Méthodes pour lire les dictionnaires ---
    /**
     * Méthode générique pour récupérer les options depuis un dictionnaire
     *
     * @param object $langs                    Objet Translate de Dolibarr
     * @param string $dictionary_table_suffix_short Suffixe court du nom de la table dictionnaire
     * @param bool   $usekeys                  True pour retourner tableau associatif code=>label
     * @param bool   $show_empty               True pour ajouter une option vide
     * @return array                           Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix_short, $usekeys = true, $show_empty = false) {
        global $db;
        $options = array();
        if ($show_empty) {
            $options[''] = $langs->trans("SelectAnOption");
        }
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_".$db->escape($dictionary_table_suffix_short)." WHERE active = 1 ORDER BY position ASC, label ASC";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                if ($usekeys) {
                    $options[$obj->code] = $langs->trans($obj->label);
                } else {
                    $obj_option = new stdClass();
                    $obj_option->code = $obj->code;
                    $obj_option->label = $obj->label; // Conserver la clé de traduction
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
     * Récupère les options de types de dossier
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getTypeDossierOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "dossier_type", $usekeys, $show_empty);
    }

    /**
     * Récupère les options de statuts globaux
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getStatutGlobalDossierOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "dossier_statut_global", $usekeys, $show_empty);
    }

    /**
     * Récupère les options de priorités
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getPrioriteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "dossier_priorite", $usekeys, $show_empty);
    }

    /**
     * Récupère les options de modes de facturation
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getModeFacturationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "dossier_mode_fact", $usekeys, $show_empty);
    }

    /**
     * Récupère les options de sources d'acquisition
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getSourceAcquisitionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "dossier_source_acquis", $usekeys, $show_empty);
    }

    // Méthodes pour les dictionnaires de type "Démarche"
    /**
     * Récupère les options de types généraux de démarche
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getDemarcheTypeGeneralOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "demarche_type_general", $usekeys, $show_empty);
    }

    /**
     * Récupère les options de sous-types de démarche
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getDemarcheSousTypeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "demarche_sous_type", $usekeys, $show_empty);
    }

    /**
     * Récupère les options de statuts de démarche
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getDemarcheStatutOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "demarche_statut", $usekeys, $show_empty);
    }

    /**
     * Récupère les options de complexité de démarche
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getDemarcheComplexiteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "demarche_complexite", $usekeys, $show_empty);
    }

    /**
     * Récupère les options de décision de démarche
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getDemarcheDecisionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "demarche_decision", $usekeys, $show_empty);
    }

    /**
     * Récupère les options de mode de suivi de démarche
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getDemarcheModeSuiviOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, "demarche_mode_suivi", $usekeys, $show_empty);
    }

    /**
     * Génère le prochain numéro de référence pour un dossier
     * Utilise ElaskaNumero si disponible, sinon génère selon un format prédéfini
     * 
     * @param DoliDB $db Base de données
     * @return string     Référence générée ou -1 si erreur
     */
    public function getNextNumero($db)
    {
        // Utiliser ElaskaNumero si disponible
        if (file_exists(DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php')) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';
            $numHelper = new ElaskaNumero($db, 'elaska', $this->element);
            
            // Ajouter des paramètres pour personnaliser la référence
            if (!empty($this->type_dossier_code)) {
                $numHelper->addParam('TYPE_DOSSIER', $this->type_dossier_code);
            }
            if (!empty($this->fk_elaska_tiers)) {
                $numHelper->addParam('ID_TIERS', $this->fk_elaska_tiers);
            }
            
            return $numHelper->getNextNumber();
        }
        
        // Méthode alternative si ElaskaNumero n'est pas disponible
        $prefix = 'DOS';
        if (!empty($this->type_dossier_code)) {
            $prefix = 'DOS-'.strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $this->type_dossier_code), 0, 3));
        }
        $year = date('Y');
        $month = date('m');

        // Format: PREFIX-YYYYMM-NNNN
        $mask_for_counter = $prefix.'-'.$year.$month;
        
        $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(ref, '-', -1) AS SIGNED)) as max_num";
        $sql .= " FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql .= " WHERE ref LIKE '".$db->escape($mask_for_counter)."-%'";
        $sql .= " AND entity = ".$this->entity;

        $resql = $db->query($sql);
        if (!$resql) {
            dol_print_error($db);
            $this->error = $db->lasterror();
            return -1;
        }
        
        $obj = $db->fetch_object($resql);
        $max_num = $obj->max_num;
        $next_num = ($max_num > 0 ? $max_num + 1 : 1);
        
        return $mask_for_counter.'-'.sprintf('%04d', $next_num);
    }

    /**
     * Crée une timeline pour ce dossier
     * 
     * @param User $user            Utilisateur créateur
     * @param string $fk_workflow_code Code du modèle de workflow (optionnel)
     * @param string $titre_timeline   Titre pour la timeline (optionnel, sinon généré)
     * @return ElaskaDossierTimeline|false L'objet timeline créé ou false si erreur
     */
    public function createTimeline($user, $fk_workflow_code = null, $titre_timeline = '')
    {
        global $langs;
        // S'assurer que la classe ElaskaDossierTimeline est chargée
        if (!class_exists('ElaskaDossierTimeline')) {
             require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dossier_timeline.class.php';
        }

        if (empty($this->id)) {
            $this->error = "Dossier ID is not set, cannot create timeline.";
            dol_syslog(get_class($this)."::createTimeline ".$this->error, LOG_ERR);
            return false;
        }

        $timeline = new ElaskaDossierTimeline($this->db);
        $timeline->fk_dossier = $this->id;

        if (!empty($fk_workflow_code)) {
            $timeline->fk_workflow_code = $fk_workflow_code;
            // Récupérer le libellé du modèle de workflow pour le titre par défaut
            $sql_label = "SELECT label FROM ".MAIN_DB_PREFIX."c_elaska_dossier_workflow WHERE code = '".$this->db->escape($fk_workflow_code)."'";
            $res_label = $this->db->query($sql_label);
            if ($res_label && $this->db->num_rows($res_label) > 0) {
                $obj_label = $this->db->fetch_object($res_label);
                $default_title = $langs->trans("TimelineForDossier", $this->ref) . " - " . $langs->trans($obj_label->label);
                $this->db->free($res_label);
            } else {
                $default_title = $langs->trans("TimelineForDossier", $this->ref) . " (" . $fk_workflow_code . ")";
            }
        } else {
            $timeline->is_custom = 1; // Timeline personnalisée si pas de modèle
            $default_title = $langs->trans("CustomTimelineForDossier", $this->ref);
        }

        $timeline->titre_timeline = !empty($titre_timeline) ? $titre_timeline : $default_title;
        $timeline->date_debut_prevue = $this->date_ouverture ?? dol_print_date(dol_now(), '%Y-%m-%d');

        // Utiliser createFromWorkflowModel si un code est fourni, sinon create() simple
        if (!empty($timeline->fk_workflow_code)) {
            if ($timeline->createFromWorkflowModel($user) > 0) {
                return $timeline;
            } else {
                $this->error = "Failed to create timeline from model: ".$timeline->error;
                dol_syslog(get_class($this)."::createTimeline ".$this->error, LOG_ERR);
                return false;
            }
        } else {
            if ($timeline->create($user) > 0) {
                return $timeline;
            } else {
                $this->error = "Failed to create custom timeline: ".$timeline->error;
                dol_syslog(get_class($this)."::createTimeline ".$this->error, LOG_ERR);
                return false;
            }
        }
    }
    
    /**
     * Récupère la timeline principale associée à ce dossier
     * 
     * @return ElaskaDossierTimeline|false La timeline ou false si non trouvée
     */
    public function getMainTimeline()
    {
        if (!class_exists('ElaskaDossierTimeline')) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dossier_timeline.class.php';
        }
        
        $timeline = new ElaskaDossierTimeline($this->db);
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$timeline->table_element;
        $sql .= " WHERE fk_dossier = ".(int) $this->id;
        $sql .= " AND status = 1"; // Timeline active
        $sql .= " ORDER BY date_creation DESC LIMIT 1"; // La plus récente
        
        $resql = $this->db->query($sql);
        if ($resql && $this->db->num_rows($resql) > 0) {
            $obj = $this->db->fetch_object($resql);
            if ($timeline->fetch($obj->rowid) > 0) {
                $this->db->free($resql);
                return $timeline;
            }
        }
        
        if (!$resql) {
            $this->error = "Error fetching timeline for dossier: ".$this->db->lasterror();
            dol_syslog(get_class($this)."::getMainTimeline ".$this->error, LOG_ERR);
        }
        
        return false;
    }
    
    /**
     * Charge les étapes de la timeline associée à ce dossier
     * 
     * @return int Nombre d'étapes chargées ou <0 si erreur
     */
    public function loadTimelineEtapes()
    {
        // Utiliser la nouvelle méthode getMainTimeline()
        $timeline = $this->getMainTimeline();
        
        if ($timeline) {
            // On s'assure que les étapes sont chargées dans la timeline
            if (empty($timeline->etapes)) {
                $timeline->loadEtapes();
            }
            
            $this->timeline_etapes = $timeline->etapes;
            return count($this->timeline_etapes);
        } else {
            // Si pas d'erreur spécifique dans getMainTimeline(), c'est simplement qu'il n'y a pas de timeline
            if (empty($this->error)) {
                $this->timeline_etapes = array();
                return 0;
            }
            return -1;
        }
    }
    
    /**
     * Récupère toutes les timelines associées à ce dossier
     *
     * @param int $active_only Ne récupérer que les timelines actives (1) ou toutes (0)
     * @return array|int       Tableau d'objets ElaskaDossierTimeline ou <0 si erreur
     */
    public function getAllTimelines($active_only = 1)
    {
        if (!class_exists('ElaskaDossierTimeline')) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dossier_timeline.class.php';
        }
        
        $timelines = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline";
        $sql .= " WHERE fk_dossier = ".(int) $this->id;
        if ($active_only) {
            $sql .= " AND status = 1";
        }
        $sql .= " ORDER BY date_creation DESC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $timeline = new ElaskaDossierTimeline($this->db);
                if ($timeline->fetch($obj->rowid) > 0) {
                    $timelines[] = $timeline;
                }
            }
            $this->db->free($resql);
            return $timelines;
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this)."::getAllTimelines ".$this->error, LOG_ERR);
            return -1;
        }
    }
    
    /**
     * Récupère les tâches liées à ce dossier
     *
     * @param string $filter Filtre SQL additionnel
     * @param string $order  Ordre de tri
     * @return array|int     Tableau d'objets ElaskaTask ou <0 si erreur
     */
    public function getTasks($filter = '', $order = 'date_previsionnelle ASC, priorite_code ASC')
    {
        if (!file_exists(DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_task.class.php')) {
            return array();
        }
        
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_task.class.php';
        
        $tasks = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_task";
        $sql .= " WHERE fk_elaska_dossier = ".(int) $this->id;
        if (!empty($filter)) {
            $sql .= " AND (".$filter.")";
        }
        if (!empty($order)) {
            $sql .= " ORDER BY ".$order;
        }
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $task = new ElaskaTask($this->db);
                if ($task->fetch($obj->rowid) > 0) {
                    $tasks[] = $task;
                }
            }
            $this->db->free($resql);
            return $tasks;
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this)."::getTasks ".$this->error, LOG_ERR);
            return -1;
        }
    }
    
    /**
     * Récupère les documents liés à ce dossier
     *
     * @param string $type_document_code Filtre optionnel sur le type de document
     * @return array|int                Tableau d'objets ElaskaDocument ou <0 si erreur
     */
    public function getDocuments($type_document_code = '')
    {
        if (!file_exists(DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php')) {
            return array();
        }
        
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
        
        $doc = new ElaskaDocument($this->db);
        
        $filter = '';
        if (!empty($type_document_code)) {
            $filter = " AND type_document_code = '".$this->db->escape($type_document_code)."'";
        }
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_document";
        $sql .= " WHERE fk_object = ".(int) $this->id;
        $sql .= " AND fk_object_type = '".$this->db->escape($this->element)."'";
        $sql .= $filter;
        $sql .= " ORDER BY date_document DESC, date_creation DESC";
        
        $documents = array();
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $document = new ElaskaDocument($this->db);
                if ($document->fetch($obj->rowid) > 0) {
                    $documents[] = $document;
                }
            }
            $this->db->free($resql);
            return $documents;
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this)."::getDocuments ".$this->error, LOG_ERR);
            return -1;
        }
    }
    
    /**
     * Met à jour le pourcentage d'avancement du dossier en fonction des étapes de timeline
     *
     * @param User $user      Utilisateur qui effectue la mise à jour
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function updateProgress($user, $notrigger = 0)
    {
        $timeline = $this->getMainTimeline();
        if (!$timeline) {
            return 0; // Pas d'erreur mais rien à faire
        }
        
        // Utiliser la méthode de calcul de progression de la timeline
        $progress = $timeline->calculateProgress();
        
        if ($progress >= 0) {
            $this->progress = $progress;
            return $this->update($user, $notrigger);
        }
        
        return 0;
    }
}
}
?>
