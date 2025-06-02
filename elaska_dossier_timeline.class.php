<?php
/**
 * eLaska - Classe pour gérer les timelines configurables des dossiers
 * Date: 2025-05-30
 * Version: 4.0 (Version finale pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_task.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';

if (!class_exists('ElaskaDossierTimeline', false)) {

class ElaskaDossierTimeline extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_dossier_timeline';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_dossier_timeline';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'timeline@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var string Référence unique de la timeline
     */
    public $ref;
    
    /**
     * @var int ID du dossier parent
     */
    public $fk_dossier;
    
    /**
     * @var string Code du workflow utilisé comme modèle
     */
    public $fk_workflow_code;
    
    /**
     * @var string Titre de la timeline
     */
    public $titre_timeline;
    
    /**
     * @var string Description détaillée de la timeline
     */
    public $description_timeline;
    
    /**
     * @var int Pourcentage d'avancement global (0-100)
     */
    public $avancement_global_pct;
    
    /**
     * @var int ID de l'étape active
     */
    public $etape_active_id;
    
    /**
     * @var int Indique si la timeline est personnalisée (1) ou issue d'un modèle (0)
     */
    public $is_custom;
    
    /**
     * @var string Date de début prévue (format YYYY-MM-DD)
     */
    public $date_debut_prevue;
    
    /**
     * @var string Date de début réelle (format YYYY-MM-DD)
     */
    public $date_debut_reelle;
    
    /**
     * @var string Date de fin prévue (format YYYY-MM-DD)
     */
    public $date_fin_prevue;
    
    /**
     * @var string Date de fin réelle (format YYYY-MM-DD)
     */
    public $date_fin_reelle;
    
    /**
     * @var string Notes internes sur la timeline
     */
    public $notes_timeline;
    
    /**
     * @var string Type d'affichage (horizontal, vertical, etc.)
     */
    public $display_type;

    /**
     * @var array Étapes de la timeline
     */
    public $etapes = array();

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
        'ref' => array('type' => 'varchar(128)', 'label' => 'Ref', 'enabled' => 1, 'position' => 10, 'notnull' => 0, 'visible' => 1, 'unique' => 1),
        'fk_dossier' => array('type' => 'integer:ElaskaDossier:custom/elaska/class/elaska_dossier.class.php', 'label' => 'DossierParent', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'fk_workflow_code' => array('type' => 'varchar(50)', 'label' => 'WorkflowModel', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'titre_timeline' => array('type' => 'varchar(255)', 'label' => 'TitreTimeline', 'enabled' => 1, 'position' => 40, 'notnull' => 1, 'visible' => 1),
        'description_timeline' => array('type' => 'text', 'label' => 'DescriptionTimeline', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1, 'textarea_html' => 1),
        'avancement_global_pct' => array('type' => 'integer', 'label' => 'AvancementGlobalPct', 'enabled' => 1, 'position' => 60, 'notnull' => 1, 'visible' => 1, 'default' => 0),
        'etape_active_id' => array('type' => 'integer:ElaskaDossierTimelineEtape:custom/elaska/class/elaska_dossier_timeline.class.php', 'label' => 'EtapeActiveId', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'is_custom' => array('type' => 'boolean', 'label' => 'PersonnaliseeFlag', 'enabled' => 1, 'position' => 80, 'notnull' => 1, 'visible' => 1, 'default' => 0),
        'date_debut_prevue' => array('type' => 'date', 'label' => 'DateDebutPrevue', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'date_debut_reelle' => array('type' => 'date', 'label' => 'DateDebutReelle', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'date_fin_prevue' => array('type' => 'date', 'label' => 'DateFinPrevue', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'date_fin_reelle' => array('type' => 'date', 'label' => 'DateFinReelle', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'notes_timeline' => array('type' => 'text', 'label' => 'NotesTimeline', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'display_type' => array('type' => 'varchar(50)', 'label' => 'DisplayType', 'enabled' => 1, 'position' => 140, 'notnull' => 1, 'visible' => 1, 'default' => 'horizontal'),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 1, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => -2),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => 0, 'visible' => -2),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => -2),
        'status' => array('type' => 'integer', 'label' => 'Status', 'enabled' => 1, 'position' => 1001, 'notnull' => 1, 'visible' => 1, 'default' => 1),
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
        if (empty($this->status)) $this->status = 1;
        if (!isset($this->avancement_global_pct)) $this->avancement_global_pct = 0;
        if (empty($this->display_type)) $this->display_type = 'horizontal';
        if (!isset($this->is_custom)) $this->is_custom = 0;
        
        $this->etapes = array();
    }

    /**
     * Crée une timeline dans la base de données
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

        // Vérification des champs obligatoires
        if (empty($this->fk_dossier) || empty($this->titre_timeline)) {
            $this->error = "Le dossier parent et le titre sont obligatoires";
            $this->db->rollback();
            return -1;
        }

        // Pas de référence initiale, elle sera générée par ElaskaNumero
        $this->ref = '';
        
        // Création de l'objet timeline
        $result = $this->createCommon($user, $notrigger);

        if ($result > 0) {
            // Génération de la référence avec ElaskaNumero
            $params = array();
            
            // Paramètres pour le masque de numérotation
            if ($this->fk_dossier) {
                require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dossier.class.php';
                $dossier = new ElaskaDossier($this->db);
                if ($dossier->fetch($this->fk_dossier) > 0) {
                    $params['DOSSIER_REF'] = $dossier->ref;
                }
            }

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
                    dol_syslog("ElaskaDossierTimeline::create Failed to update definitive ref for ".$this->element." ".$this->id, LOG_ERR);
                }
            } else {
                $error++;
                dol_syslog("ElaskaDossierTimeline::create Failed to generate/record definitive ref for ".$this->element." ".$this->id, LOG_ERR);
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
     * Crée une timeline à partir d'un modèle de workflow
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function createFromWorkflowModel($user, $notrigger = 0)
    {
        global $conf;
        
        $error = 0;
        $this->db->begin();

        // Vérifications et valeurs par défaut
        if (empty($this->fk_workflow_code)) {
            $this->error = "Le code de workflow est obligatoire pour créer à partir d'un modèle";
            $this->db->rollback();
            return -1;
        }
        
        if (empty($this->avancement_global_pct)) $this->avancement_global_pct = 0;
        if (empty($this->status)) $this->status = 1;
        if (empty($this->display_type)) $this->display_type = 'horizontal';
        if (!isset($this->is_custom)) $this->is_custom = 0;

        // Pas de référence initiale, elle sera générée par ElaskaNumero
        $this->ref = '';

        // Création de la timeline de base
        $result_timeline = $this->createCommon($user, $notrigger);

        if ($result_timeline > 0) {
            // Génération de la référence avec ElaskaNumero
            $params = array();
            
            // Paramètres pour le masque de numérotation
            if ($this->fk_dossier) {
                require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dossier.class.php';
                $dossier = new ElaskaDossier($this->db);
                if ($dossier->fetch($this->fk_dossier) > 0) {
                    $params['DOSSIER_REF'] = $dossier->ref;
                }
            }
            
            if (!empty($this->fk_workflow_code)) {
                $params['WORKFLOW_CODE'] = $this->fk_workflow_code;
            }

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
                    dol_syslog("ElaskaDossierTimeline::createFromWorkflowModel Failed to update definitive ref", LOG_ERR);
                }
            } else {
                $error++;
                dol_syslog("ElaskaDossierTimeline::createFromWorkflowModel Failed to generate/record definitive ref", LOG_ERR);
            }

            // Création des étapes à partir du modèle de workflow
            if (!$error && $this->createEtapesFromWorkflowModel($user, $notrigger) < 0) {
                $this->error = "Erreur lors de la création des étapes depuis le modèle";
                $error++;
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
     * Crée les étapes à partir du modèle de workflow
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    private function createEtapesFromWorkflowModel($user, $notrigger = 0)
    {
        global $conf, $langs;
        
        // Vérifier si le modèle existe
        $sql = "SELECT * FROM ".MAIN_DB_PREFIX."elaska_dossier_workflow_etape";
        $sql .= " WHERE fk_workflow_code = '".$this->db->escape($this->fk_workflow_code)."'";
        $sql .= " AND entity IN (0, ".(int)$conf->entity.")";
        $sql .= " ORDER BY position ASC";
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = "Erreur base de données: ".$this->db->lasterror();
            return -1;
        }
        
        if ($this->db->num_rows($resql) == 0) {
            // Pas d'étapes dans le modèle, ce n'est pas une erreur
            return 1;
        }
        
        $first_etape_id = null;
        $last_created_etape_id = null;
        $previous_etape_object = null;
        
        // Date de début pour la première étape
        $current_start_date = $this->date_debut_prevue ? $this->db->jdatetotimestamp($this->date_debut_prevue) : dol_now();

        // Créer chaque étape du modèle
        while ($obj_model_etape = $this->db->fetch_object($resql)) {
            $etape = new ElaskaDossierTimelineEtape($this->db);
            
            // Informations de base
            $etape->fk_timeline = $this->id;
            $etape->titre_etape = $langs->trans($obj_model_etape->titre_etape);
            $etape->description_etape = $langs->trans($obj_model_etape->description_etape);
            $etape->position = $obj_model_etape->position;
            $etape->etape_type_code = $obj_model_etape->etape_type_code;
            $etape->statut_etape_code = 'A_FAIRE'; // Toutes les nouvelles étapes sont "À faire"
            $etape->duree_prevue_jours = (int) $obj_model_etape->duree_standard_jours;
            $etape->is_required = (int) $obj_model_etape->is_required;
            $etape->is_custom = 0; // Étape issue d'un modèle
            
            // Gérer les dépendances entre étapes
            if (!empty($obj_model_etape->lier_etape_precedente) && $last_created_etape_id) {
                $etape->fk_etape_precedente_requise = $last_created_etape_id;
            }

            // Calcul des dates prévisionnelles
            if (!empty($etape->fk_etape_precedente_requise) && $previous_etape_object && !empty($previous_etape_object->date_fin_prevue)) {
                // Dépendance: commencer le lendemain de la fin de l'étape précédente
                $etape->date_debut_prevue = $this->db->timestamp_to_idate($this->db->jdatetotimestamp($previous_etape_object->date_fin_prevue) + (24*60*60));
            } elseif ($previous_etape_object && !empty($previous_etape_object->date_fin_prevue)) {
                // Pas de dépendance, mais on suit la précédente
                $etape->date_debut_prevue = $this->db->timestamp_to_idate($this->db->jdatetotimestamp($previous_etape_object->date_fin_prevue) + (24*60*60));
            } else {
                // Première étape ou pas de date sur la précédente
                $etape->date_debut_prevue = $this->db->timestamp_to_idate($current_start_date);
            }

            // Calcul de la date de fin prévue
            if (!empty($etape->date_debut_prevue) && $etape->duree_prevue_jours > 0) {
                $start_ts_etape = $this->db->jdatetotimestamp($etape->date_debut_prevue);
                $etape->date_fin_prevue = $this->db->timestamp_to_idate($start_ts_etape + ($etape->duree_prevue_jours * 24 * 60 * 60));
            } elseif (!empty($etape->date_debut_prevue)) {
                // Durée 0 = jalon (même jour)
                $etape->date_fin_prevue = $etape->date_debut_prevue;
            }
            
            // Mise à jour de la date de début pour l'étape suivante
            $current_start_date = !empty($etape->date_fin_prevue) ? 
                $this->db->jdatetotimestamp($etape->date_fin_prevue) + (24*60*60) : 
                $current_start_date;

            // Créer l'étape
            if ($etape->create($user, $notrigger) <= 0) {
                $this->error = "Erreur lors de la création de l'étape: ".$etape->error;
                return -2;
            }
            
            // Mémoriser les IDs pour les références
            if (is_null($first_etape_id)) {
                $first_etape_id = $etape->id;
            }
            
            $last_created_etape_id = $etape->id;
            $previous_etape_object = clone $etape;
        }
        
        // Définir la première étape comme active
        if (!is_null($first_etape_id)) {
            $this->etape_active_id = $first_etape_id;
            
            // Mettre à jour directement dans la base
            $sql_update = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
            $sql_update .= " SET etape_active_id = ".(int)$first_etape_id;
            $sql_update .= " WHERE rowid = ".(int)$this->id;
            
            if (!$this->db->query($sql_update)) {
                $this->error = "Erreur lors de la mise à jour de l'étape active: ".$this->db->lasterror();
                return -3;
            }
        }
        
        return 1;
    }

    /**
     * Charge une timeline depuis la base de données
     *
     * @param int  $id         ID de la timeline
     * @param string $ref      Référence de la timeline
     * @param bool $loadetapes Indique s'il faut charger les étapes
     * @return int             <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = null, $loadetapes = true)
    {
        $result = $this->fetchCommon($id, $ref);
        
        if ($result > 0 && $loadetapes) {
            $this->loadEtapes();
        }
        
        return $result;
    }

    /**
     * Charge les étapes de la timeline
     *
     * @return int <0 si erreur, nombre d'étapes si OK
     */
    public function loadEtapes()
    {
        $this->etapes = array();
        
        if (empty($this->id)) return 0;
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape";
        $sql .= " WHERE fk_timeline = ".(int)$this->id;
        $sql .= " ORDER BY position ASC, rowid ASC";
        
        $resql = $this->db->query($sql);
        
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $etape = new ElaskaDossierTimelineEtape($this->db);
                if ($etape->fetch($obj->rowid) > 0) {
                    $this->etapes[] = $etape;
                }
            }
            $this->db->free($resql);
            return count($this->etapes);
        } else {
            $this->error = "Erreur lors du chargement des étapes: ".$this->db->lasterror();
            dol_syslog(get_class($this)."::loadEtapes ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     * Met à jour une timeline dans la base de données
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
     * Supprime une timeline et ses étapes
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        $this->db->begin();
        $error = 0;

        // Charger les étapes si nécessaire
        $this->loadEtapes();
        
        // Supprimer toutes les étapes
        foreach ($this->etapes as $etape) {
            if ($etape->delete($user, 1) < 0) { // notrigger=1
                $error++;
                $this->error = "Erreur lors de la suppression de l'étape ".$etape->id.": ".$etape->error;
                break;
            }
        }

        // Supprimer la timeline elle-même
        if (!$error) {
            if ($this->deleteCommon($user, $notrigger) < 0) {
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
     * Ajoute une nouvelle étape à la timeline
     *
     * @param User   $user              Utilisateur qui effectue l'action
     * @param string $titre             Titre de l'étape
     * @param string $description       Description de l'étape
     * @param int    $position          Position dans la timeline
     * @param int    $duree_jours       Durée prévue en jours
     * @param string $type_code         Code du type d'étape
     * @param string $statut_code       Code du statut initial
     * @param int    $fk_etape_prec     ID de l'étape précédente requise
     * @return int                      <0 si erreur, ID de l'étape si OK
     */
    public function addEtape($user, $titre, $description = '', $position = 0, $duree_jours = 0, $type_code = '', $statut_code = 'A_FAIRE', $fk_etape_prec = 0)
    {
        if (empty($this->id)) return -1;
        
        // Créer une nouvelle étape
        $etape = new ElaskaDossierTimelineEtape($this->db);
        $etape->fk_timeline = $this->id;
        $etape->titre_etape = $titre;
        $etape->description_etape = $description;
        
        // Si position non spécifiée, ajouter à la fin
        if ($position <= 0) {
            if (empty($this->etapes)) {
                $this->loadEtapes();
            }
            $position = count($this->etapes) + 1;
        }
        
        $etape->position = $position;
        $etape->etape_type_code = $type_code;
        $etape->statut_etape_code = $statut_code;
        $etape->duree_prevue_jours = $duree_jours;
        $etape->is_required = 1; // Par défaut
        $etape->is_custom = 1; // Étape personnalisée
        
        // Gestion des dépendances
        if ($fk_etape_prec > 0) {
            $etape->fk_etape_precedente_requise = $fk_etape_prec;
            
            // Récupérer l'étape précédente pour calculer la date de début
            $etape_prec = new ElaskaDossierTimelineEtape($this->db);
            if ($etape_prec->fetch($fk_etape_prec) > 0 && !empty($etape_prec->date_fin_prevue)) {
                $date_debut_ts = $this->db->jdatetotimestamp($etape_prec->date_fin_prevue) + (24*60*60); // J+1
                $etape->date_debut_prevue = $this->db->timestamp_to_idate($date_debut_ts);
            } else {
                $etape->date_debut_prevue = $this->db->timestamp_to_idate(dol_now());
            }
        } else {
            $etape->date_debut_prevue = $this->db->timestamp_to_idate(dol_now());
        }
        
        // Calcul de la date de fin prévue
        if ($duree_jours > 0) {
            $date_debut_ts = $this->db->jdatetotimestamp($etape->date_debut_prevue);
            $etape->date_fin_prevue = $this->db->timestamp_to_idate($date_debut_ts + ($duree_jours * 24 * 60 * 60));
        } else {
            $etape->date_fin_prevue = $etape->date_debut_prevue; // Jalon
        }
        
        // Créer l'étape
        $result = $etape->create($user);
        
        if ($result > 0) {
            // Recharger les étapes
            $this->loadEtapes();
            
            // Si c'est la première étape, la définir comme active
            if (count($this->etapes) == 1) {
                $this->etape_active_id = $etape->id;
                $this->update($user, 1); // notrigger = 1
            }
            
            return $etape->id;
        } else {
            $this->error = $etape->error;
            return -1;
        }
    }

    /**
     * Supprime une étape de la timeline
     *
     * @param User $user     Utilisateur qui effectue l'action
     * @param int  $etape_id ID de l'étape à supprimer
     * @return int           <0 si erreur, >0 si OK
     */
    public function removeEtape($user, $etape_id)
    {
        if (empty($this->id) || empty($etape_id)) return -1;
        
        // Charger l'étape
        $etape = new ElaskaDossierTimelineEtape($this->db);
        if ($etape->fetch($etape_id) <= 0) {
            $this->error = "Étape non trouvée";
            return -2;
        }
        
        // Vérifier que l'étape appartient bien à cette timeline
        if ($etape->fk_timeline != $this->id) {
            $this->error = "Cette étape n'appartient pas à cette timeline";
            return -3;
        }
        
        // Vérifier que l'étape n'est pas référencée comme prérequise par d'autres étapes
        $sql = "SELECT COUNT(*) as nb FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape";
        $sql .= " WHERE fk_etape_precedente_requise = ".(int)$etape_id;
        $sql .= " AND fk_timeline = ".(int)$this->id;
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = "Erreur lors de la vérification des dépendances: ".$this->db->lasterror();
            return -4;
        }
        
        $obj = $this->db->fetch_object($resql);
        if ($obj->nb > 0) {
            $this->error = "Impossible de supprimer cette étape car d'autres étapes en dépendent";
            return -5;
        }
        
        // Si l'étape à supprimer est l'étape active, trouver une alternative
        if ($this->etape_active_id == $etape_id) {
            // Charger toutes les étapes
            $this->loadEtapes();
            
            $nouvelle_etape_active_id = null;
            
            // Trouver la première étape non terminée
            foreach ($this->etapes as $e) {
                if ($e->id != $etape_id && $e->statut_etape_code != 'TERMINEE' && $e->statut_etape_code != 'ANNULEE') {
                    $nouvelle_etape_active_id = $e->id;
                    break;
                }
            }
            
            // Si aucune alternative, prendre la première étape
            if (is_null($nouvelle_etape_active_id) && count($this->etapes) > 1) {
                foreach ($this->etapes as $e) {
                    if ($e->id != $etape_id) {
                        $nouvelle_etape_active_id = $e->id;
                        break;
                    }
                }
            }
            
            // Mettre à jour l'étape active
            if (!is_null($nouvelle_etape_active_id)) {
                $this->etape_active_id = $nouvelle_etape_active_id;
                $this->update($user, 1); // notrigger = 1
            } else {
                $this->etape_active_id = null;
                $this->update($user, 1); // notrigger = 1
            }
        }
        
        // Supprimer l'étape
        $result = $etape->delete($user);
        
        if ($result > 0) {
            // Recalculer l'avancement
            $this->calculateProgress();
            $this->update($user, 1); // notrigger = 1
            
            // Recharger les étapes
            $this->loadEtapes();
            
            return 1;
        } else {
            $this->error = $etape->error;
            return -6;
        }
    }

    /**
     * Change le statut d'une étape
     *
     * @param User   $user               Utilisateur qui effectue l'action
     * @param int    $etape_id           ID de l'étape
     * @param string $nouveau_statut_code Nouveau code de statut
     * @param string $date_reelle        Date réelle (format YYYY-MM-DD)
     * @param string $commentaire        Commentaire sur le changement de statut
     * @return int                       <0 si erreur, >0 si OK
     */
    public function changeEtapeStatut($user, $etape_id, $nouveau_statut_code, $date_reelle = null, $commentaire = '')
    {
        if (empty($this->id) || empty($etape_id)) return -1;
        
        // Charger l'étape
        $etape = new ElaskaDossierTimelineEtape($this->db);
        if ($etape->fetch($etape_id) <= 0) {
            $this->error = "Étape non trouvée";
            return -2;
        }
        
        // Vérifier que l'étape appartient bien à cette timeline
        if ($etape->fk_timeline != $this->id) {
            $this->error = "Cette étape n'appartient pas à cette timeline";
            return -3;
        }
        
        // Vérifier si le statut change réellement
        if ($etape->statut_etape_code == $nouveau_statut_code) {
            return 0; // Pas de changement
        }
        
        // Enregistrer l'ancien statut
        $ancien_statut_code = $etape->statut_etape_code;
        
        // Mettre à jour le statut
        $etape->statut_etape_code = $nouveau_statut_code;
        
        // Gérer les dates réelles
        if ($nouveau_statut_code == 'EN_COURS' && empty($etape->date_debut_reelle)) {
            $etape->date_debut_reelle = !empty($date_reelle) ? $date_reelle : dol_now();
        }
        
        if (($nouveau_statut_code == 'TERMINEE' || $nouveau_statut_code == 'VALIDEE') && empty($etape->date_fin_reelle)) {
            $etape->date_fin_reelle = !empty($date_reelle) ? $date_reelle : dol_now();
        }
        
        // Mettre à jour l'étape
        if ($etape->update($user, 1) <= 0) { // notrigger = 1
            $this->error = "Erreur lors de la mise à jour du statut: ".$etape->error;
            return -4;
        }
        
        // Ajouter une entrée dans l'historique
        $this->addEtapeHistorique($user, $etape_id, $ancien_statut_code, $nouveau_statut_code, $commentaire);
        
        // Si l'étape est terminée et qu'elle était active, passer à l'étape suivante
        if (($nouveau_statut_code == 'TERMINEE' || $nouveau_statut_code == 'VALIDEE') && $this->etape_active_id == $etape_id) {
            $this->moveToNextStep($user, $etape_id);
        }
        
        // Recalculer l'avancement global
        $this->calculateProgress();
        $this->update($user, 1); // notrigger = 1
        
        return 1;
    }

    /**
     * Ajoute une entrée dans l'historique des statuts d'une étape
     *
     * @param User   $user              Utilisateur qui effectue l'action
     * @param int    $etape_id          ID de l'étape
     * @param string $ancien_statut_code Ancien code de statut
     * @param string $nouveau_statut_code Nouveau code de statut
     * @param string $commentaire       Commentaire sur le changement
     * @return int                      <0 si erreur, ID de l'historique si OK
     */
    private function addEtapeHistorique($user, $etape_id, $ancien_statut_code, $nouveau_statut_code, $commentaire = '')
    {
        global $conf;
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_history";
        $sql.= " (fk_etape, ancien_statut_code, nouveau_statut_code, date_changement, fk_user_action, commentaire, entity)";
        $sql.= " VALUES (";
        $sql.= (int)$etape_id . ", ";
        $sql.= "'" . $this->db->escape($ancien_statut_code) . "', ";
        $sql.= "'" . $this->db->escape($nouveau_statut_code) . "', ";
        $sql.= "'" . $this->db->idate(dol_now()) . "', ";
        $sql.= (int)$user->id . ", ";
        $sql.= "'" . $this->db->escape($commentaire) . "', ";
        $sql.= (int)$conf->entity;
        $sql.= ")";
        
        $resql = $this->db->query($sql);
        
        if ($resql) {
            return $this->db->last_insert_id(MAIN_DB_PREFIX."elaska_dossier_timeline_etape_history");
        } else {
            $this->error = "Erreur lors de l'ajout à l'historique: ".$this->db->lasterror();
            dol_syslog(get_class($this)."::addEtapeHistorique ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     * Calcule le pourcentage d'avancement global de la timeline
     *
     * @return int Pourcentage d'avancement (0-100)
     */
    public function calculateProgress()
    {
        if (empty($this->id)) return 0;
        
        // Charger les étapes si nécessaire
        if (empty($this->etapes)) {
            $this->loadEtapes();
        }
        
        if (empty($this->etapes)) {
            $this->avancement_global_pct = 0;
            return 0;
        }
        
        $nb_etapes_total = 0;
        $nb_etapes_terminees = 0;
        $nb_etapes_en_cours = 0;
        $poids_total = 0;
        $progression_ponderee = 0;
        
        // Compter les étapes terminées et en cours
        foreach ($this->etapes as $etape) {
            // Ne considérer que les étapes requises pour le calcul
            if ($etape->is_required) {
                $nb_etapes_total++;
                
                // Déterminer le poids de cette étape (durée ou poids fixe)
                $poids = max(1, $etape->duree_prevue_jours); // Minimum 1 jour
                $poids_total += $poids;
                
                // Calculer l'avancement selon le statut
                if ($etape->statut_etape_code == 'TERMINEE' || $etape->statut_etape_code == 'VALIDEE') {
                    $nb_etapes_terminees++;
                    $progression_ponderee += $poids;
                } elseif ($etape->statut_etape_code == 'EN_COURS') {
                    $nb_etapes_en_cours++;
                    // Pour une étape en cours, on compte la moitié du poids
                    $progression_ponderee += ($poids / 2);
                }
            }
        }
        
        // Calcul du pourcentage d'avancement
        if ($nb_etapes_total == 0) {
            $this->avancement_global_pct = 0;
        } elseif ($poids_total > 0) {
            $this->avancement_global_pct = round(($progression_ponderee / $poids_total) * 100);
        } else {
            $this->avancement_global_pct = round(($nb_etapes_terminees / $nb_etapes_total) * 100);
        }
        
        return $this->avancement_global_pct;
    }

    /**
     * Passe à l'étape suivante dans la timeline
     *
     * @param User $user           Utilisateur qui effectue l'action
     * @param int  $current_etape_id ID de l'étape courante (optionnel)
     * @return int                 <0 si erreur, ID de la nouvelle étape active si OK
     */
    public function moveToNextStep($user, $current_etape_id = null)
    {
        if (empty($this->id)) return -1;
        
        // Utiliser l'étape active courante si non spécifiée
        if (is_null($current_etape_id)) {
            $current_etape_id = $this->etape_active_id;
        }
        
        if (empty($current_etape_id)) {
            $this->error = "Aucune étape active définie";
            return -2;
        }
        
        // Charger les étapes si nécessaire
        if (empty($this->etapes)) {
            $this->loadEtapes();
        }
        
        // Trouver l'étape courante
        $current_pos = -1;
        foreach ($this->etapes as $key => $etape) {
            if ($etape->id == $current_etape_id) {
                $current_pos = $key;
                break;
            }
        }
        
        if ($current_pos < 0) {
            $this->error = "Étape courante non trouvée dans la timeline";
            return -3;
        }
        
        // Chercher la prochaine étape à activer
        $next_etape = null;
        
        for ($i = $current_pos + 1; $i < count($this->etapes); $i++) {
            $etape = $this->etapes[$i];
            
            // Vérifier si l'étape est éligible (ni terminée, ni annulée)
            if ($etape->statut_etape_code != 'TERMINEE' && $etape->statut_etape_code != 'VALIDEE' && $etape->statut_etape_code != 'ANNULEE') {
                // Vérifier les prérequis
                if (!empty($etape->fk_etape_precedente_requise)) {
                    // Charger l'étape prérequise
                    $prereq_etape = new ElaskaDossierTimelineEtape($this->db);
                    if ($prereq_etape->fetch($etape->fk_etape_precedente_requise) > 0) {
                        // Vérifier si le prérequis est terminé
                        if ($prereq_etape->statut_etape_code == 'TERMINEE' || $prereq_etape->statut_etape_code == 'VALIDEE') {
                            $next_etape = $etape;
                            break;
                        }
                    }
                } else {
                    // Pas de prérequis, cette étape est éligible
                    $next_etape = $etape;
                    break;
                }
            }
        }
        
        // Si aucune étape suivante n'est éligible, chercher n'importe quelle étape non terminée
        if (is_null($next_etape)) {
            for ($i = 0; $i < count($this->etapes); $i++) {
                if ($i != $current_pos) { // Ne pas revenir à l'étape courante
                    $etape = $this->etapes[$i];
                    if ($etape->statut_etape_code != 'TERMINEE' && $etape->statut_etape_code != 'VALIDEE' && $etape->statut_etape_code != 'ANNULEE') {
                        $next_etape = $etape;
                        break;
                    }
                }
            }
        }
        
        // Mettre à jour l'étape active
        if (!is_null($next_etape)) {
            $this->etape_active_id = $next_etape->id;
            
            // Si la nouvelle étape active est à faire, la passer en cours
            if ($next_etape->statut_etape_code == 'A_FAIRE') {
                $this->changeEtapeStatut($user, $next_etape->id, 'EN_COURS', dol_now(), 'Étape automatiquement mise en cours');
            }
            
            $this->update($user, 1); // notrigger = 1
            
            return $next_etape->id;
        } else {
            // Aucune étape suivante trouvée, conserver l'étape actuelle
            return $current_etape_id;
        }
    }

    /**
     * Récupère les options du dictionnaire des modèles de workflow
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getWorkflowModelOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, MAIN_DB_PREFIX.'c_elaska_dossier_workflow', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types d'étapes
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getEtapeTypeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, MAIN_DB_PREFIX.'c_elaska_dossier_etape_type', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts d'étapes
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getEtapeStatutOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, MAIN_DB_PREFIX.'c_elaska_dossier_etape_statut', $usekeys, $show_empty);
    }
    
    /**
     * Méthode générique pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                 Objet de traduction
     * @param string    $dictionary_table_full_name Nom complet de la table dictionnaire
     * @param bool      $usekeys               True pour retourner tableau associatif code=>label
     * @param bool      $show_empty            True pour ajouter une option vide
     * @return array                           Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_full_name, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label, picto FROM ".$dictionary_table_full_name;
        $sql .= " WHERE active = 1";
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
                    if (isset($obj->picto)) $obj_opt->picto = $obj->picto;
                    $options[] = $obj_opt;
                }
            }
            $db->free($resql);
        } else {
            dol_print_error($db);
        }
        
        return $options;
    }
}
}

// --- Classe ElaskaDossierTimelineEtape ---

if (!class_exists('ElaskaDossierTimelineEtape', false)) {

class ElaskaDossierTimelineEtape extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_dossier_timeline_etape';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_dossier_timeline_etape';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'step@elaska';

    /**
     * @var int ID de la timeline parente
     */
    public $fk_timeline;
    
    /**
     * @var string Titre de l'étape
     */
    public $titre_etape;
    
    /**
     * @var string Description détaillée de l'étape
     */
    public $description_etape;
    
    /**
     * @var int Position dans la timeline
     */
    public $position;
    
    /**
     * @var string Code du type d'étape
     */
    public $etape_type_code;
    
    /**
     * @var string Code du statut de l'étape
     */
    public $statut_etape_code;
    
    /**
     * @var int Durée prévue en jours
     */
    public $duree_prevue_jours;
    
    /**
     * @var int Indique si l'étape est requise (1) ou optionnelle (0)
     */
    public $is_required;
    
    /**
     * @var int Indique si l'étape est personnalisée (1) ou issue d'un modèle (0)
     */
    public $is_custom;
    
    /**
     * @var string Date de début prévue (format YYYY-MM-DD)
     */
    public $date_debut_prevue;
    
    /**
     * @var string Date de début réelle (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_debut_reelle;
    
    /**
     * @var string Date de fin prévue (format YYYY-MM-DD)
     */
    public $date_fin_prevue;
    
    /**
     * @var string Date de fin réelle (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_fin_reelle;
    
    /**
     * @var string Notes internes sur l'étape
     */
    public $notes_etape;
    
    /**
     * @var int ID de l'utilisateur responsable
     */
    public $fk_user_responsable;
    
    /**
     * @var int ID de l'étape précédente requise
     */
    public $fk_etape_precedente_requise;

    /**
     * @var array Documents liés à cette étape
     */
    public $linked_documents = array();
    
    /**
     * @var array Tâches liées à cette étape
     */
    public $linked_tasks = array();
    
    /**
     * @var array Commentaires liés à cette étape
     */
    public $linked_comments = array();

    // Champs techniques standard
    public $rowid;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $entity;

    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'fk_timeline' => array('type' => 'integer:ElaskaDossierTimeline:custom/elaska/class/elaska_dossier_timeline.class.php', 'label' => 'TimelineParent', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1),
        'titre_etape' => array('type' => 'varchar(255)', 'label' => 'TitreEtape', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'description_etape' => array('type' => 'text', 'label' => 'DescriptionEtape', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1, 'textarea_html' => 1),
        'position' => array('type' => 'integer', 'label' => 'Position', 'enabled' => 1, 'position' => 40, 'notnull' => 1, 'visible' => 1, 'default' => 0),
        'etape_type_code' => array('type' => 'varchar(50)', 'label' => 'TypeEtape', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'statut_etape_code' => array('type' => 'varchar(30)', 'label' => 'StatutEtape', 'enabled' => 1, 'position' => 60, 'notnull' => 1, 'visible' => 1),
        'duree_prevue_jours' => array('type' => 'integer', 'label' => 'DureePrevueJours', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'is_required' => array('type' => 'boolean', 'label' => 'Requis', 'enabled' => 1, 'position' => 80, 'notnull' => 1, 'visible' => 1, 'default' => 1),
        'is_custom' => array('type' => 'boolean', 'label' => 'Personnalise', 'enabled' => 1, 'position' => 90, 'notnull' => 1, 'visible' => 1, 'default' => 0),
        'date_debut_prevue' => array('type' => 'date', 'label' => 'DateDebutPrevueEtape', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'date_debut_reelle' => array('type' => 'datetime', 'label' => 'DateDebutReelleEtape', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'date_fin_prevue' => array('type' => 'date', 'label' => 'DateFinPrevueEtape', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'date_fin_reelle' => array('type' => 'datetime', 'label' => 'DateFinReelleEtape', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'notes_etape' => array('type' => 'text', 'label' => 'NotesEtape', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'fk_user_responsable' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'ResponsableEtape', 'enabled' => 1, 'position' => 150, 'notnull' => 0, 'visible' => 1),
        'fk_etape_precedente_requise' => array('type' => 'integer:ElaskaDossierTimelineEtape:custom/elaska/class/elaska_dossier_timeline.class.php', 'label' => 'EtapePrecedenteRequise', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'default' => 1, 'enabled' => 1, 'visible' => -2, 'notnull' => 1, 'position' => 1000),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 1, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => -2),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => 0, 'visible' => -2),
    );
    
    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
    }
    
    /**
     * Crée une étape en base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0) 
    { 
        global $conf;
        
        if (empty($this->entity) && !empty($conf->entity)) {
            $this->entity = $conf->entity;
        }
        
        return $this->createCommon($user, $notrigger);
    }

    /**
     * Charge une étape depuis la base de données
     *
     * @param int    $id    Id de l'étape
     * @param string $ref   Référence de l'étape (non utilisé)
     * @return int          <0 si KO, >0 si OK
     */
    public function fetch($id, $ref = '')
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Met à jour l'étape
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
     * Supprime l'étape et ses données associées
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        $this->db->begin();
        $error = 0;

        // Supprimer toutes les liaisons associées
        if ($this->unlinkAllDocuments() === false) $error++;
        if ($this->unlinkAllTasks() === false) $error++;
        if ($this->deleteAllComments() === false) $error++;
        if ($this->deleteHistory() < 0) $error++;
        
        if (!$error) {
            if ($this->deleteCommon($user, $notrigger) < 0) {
                $error++;
            }
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
     * Supprime l'historique des statuts de l'étape
     *
     * @return int <0 si erreur, nombre de lignes supprimées sinon
     */
    public function deleteHistory()
    {
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_history WHERE fk_etape = ".$this->id;
        $resql = $this->db->query($sql);
        
        if (!$resql) {
            $this->error = $this->db->lasterror();
            return -1;
        }
        
        return $this->db->affected_rows($resql);
    }
    
    /**
     * Méthodes de gestion des liaisons avec les documents
     */
    
    /**
     * Lie un document à l'étape
     *
     * @param int $fk_elaska_document ID du document à lier
     * @return bool                   true si OK, false si erreur
     */
    public function linkDocument($fk_elaska_document)
    {
        if (empty($this->id) || empty($fk_elaska_document)) {
            return false;
        }
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_document";
        $sql .= " (fk_etape, fk_elaska_document, date_liaison)";
        $sql .= " VALUES (".$this->id.", ".(int)$fk_elaska_document.", '".$this->db->idate(dol_now())."')";
        
        return $this->db->query($sql) ? true : false;
    }

    /**
     * Supprime la liaison avec un document
     *
     * @param int $fk_elaska_document ID du document à délier
     * @return bool                   true si OK, false si erreur
     */
    public function unlinkDocument($fk_elaska_document)
    {
        if (empty($this->id) || empty($fk_elaska_document)) {
            return false;
        }
        
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_document";
        $sql .= " WHERE fk_etape = ".$this->id;
        $sql .= " AND fk_elaska_document = ".(int)$fk_elaska_document;
        
        return $this->db->query($sql) ? true : false;
    }

    /**
     * Supprime toutes les liaisons avec des documents
     *
     * @return bool true si OK, false si erreur
     */
    public function unlinkAllDocuments()
    {
        if (empty($this->id)) {
            return false;
        }
        
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_document";
        $sql .= " WHERE fk_etape = ".$this->id;
        
        return $this->db->query($sql) ? true : false;
    }

    /**
     * Récupère les documents liés à l'étape
     *
     * @return array Tableau d'objets ElaskaDocument
     */
    public function getLinkedDocuments()
    {
        $documents = array();
        
        if (empty($this->id)) {
            return $documents;
        }
        
        $sql = "SELECT ed.rowid FROM ".MAIN_DB_PREFIX."elaska_document as ed";
        $sql .= " JOIN ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_document as l ON l.fk_elaska_document = ed.rowid";
        $sql .= " WHERE l.fk_etape = ".(int)$this->id;
        $sql .= " ORDER BY l.date_liaison DESC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $document = new ElaskaDocument($this->db);
                if ($document->fetch($obj->rowid) > 0) {
                    $documents[] = $document;
                }
            }
        }
        
        return $documents;
    }

    /**
     * Lie une tâche à l'étape
     *
     * @param int    $fk_task       ID de la tâche à lier
     * @param string $type_task_link Type de la tâche (elaska_task, projet_task)
     * @return bool                 true si OK, false si erreur
     */
    public function linkTask($fk_task, $type_task_link = 'elaska_task')
    {
        if (empty($this->id) || empty($fk_task)) {
            return false;
        }
        
        // Pour les tâches ElaskaTask, on peut aussi mettre à jour le champ fk_timeline_etape
        if ($type_task_link === 'elaska_task') {
            $task = new ElaskaTask($this->db);
            if ($task->fetch($fk_task) > 0) {
                $task->fk_timeline_etape = $this->id;
                $task->update($task->fk_user_creat);
            }
        }
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_task";
        $sql .= " (fk_etape, fk_task, type_task_link, date_liaison)";
        $sql .= " VALUES (".$this->id.", ".(int)$fk_task.", '".$this->db->escape($type_task_link)."', '".$this->db->idate(dol_now())."')";
        
        return $this->db->query($sql) ? true : false;
    }

    /**
     * Supprime la liaison avec une tâche
     *
     * @param int    $fk_task       ID de la tâche à délier
     * @param string $type_task_link Type de la tâche (elaska_task, projet_task)
     * @return bool                 true si OK, false si erreur
     */
    public function unlinkTask($fk_task, $type_task_link = 'elaska_task')
    {
        if (empty($this->id) || empty($fk_task)) {
            return false;
        }
        
        // Pour les tâches ElaskaTask, réinitialiser aussi le champ fk_timeline_etape
        if ($type_task_link === 'elaska_task') {
            $task = new ElaskaTask($this->db);
            if ($task->fetch($fk_task) > 0 && $task->fk_timeline_etape == $this->id) {
                $task->fk_timeline_etape = null;
                $task->update($task->fk_user_creat);
            }
        }
        
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_task";
        $sql .= " WHERE fk_etape = ".$this->id;
        $sql .= " AND fk_task = ".(int)$fk_task;
        $sql .= " AND type_task_link = '".$this->db->escape($type_task_link)."'";
        
        return $this->db->query($sql) ? true : false;
    }

    /**
     * Supprime toutes les liaisons avec des tâches
     *
     * @return bool true si OK, false si erreur
     */
    public function unlinkAllTasks()
    {
        if (empty($this->id)) {
            return false;
        }
        
        // Réinitialiser le champ fk_timeline_etape pour toutes les tâches eLaska liées
        $sql = "UPDATE ".MAIN_DB_PREFIX."elaska_task";
        $sql .= " SET fk_timeline_etape = NULL";
        $sql .= " WHERE fk_timeline_etape = ".(int)$this->id;
        $this->db->query($sql);
        
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_task";
        $sql .= " WHERE fk_etape = ".$this->id;
        
        return $this->db->query($sql) ? true : false;
    }

    /**
     * Récupère les tâches liées à l'étape
     *
     * @return array Tableau mixte d'objets ElaskaTask et Task (Dolibarr)
     */
    public function getLinkedTasks()
    {
        $tasks = array();
        
        if (empty($this->id)) {
            return $tasks;
        }

        // Récupérer les tâches ElaskaTask directement liées via le champ fk_timeline_etape
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_task";
        $sql .= " WHERE fk_timeline_etape = ".(int)$this->id;
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $task = new ElaskaTask($this->db);
                if ($task->fetch($obj->rowid) > 0) {
                    $task->source = 'elaska_direct'; // Marquer la source de liaison
                    $tasks[] = $task;
                }
            }
        }

        // Récupérer les tâches via la table de liaison (pour compatibilité et tâches Dolibarr)
        $sql = "SELECT l.fk_task, l.type_task_link FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_task as l";
        $sql .= " WHERE l.fk_etape = ".(int)$this->id;
        $sql .= " AND (l.type_task_link = 'projet_task' OR (l.type_task_link = 'elaska_task' AND NOT EXISTS (";
        $sql .= "   SELECT 1 FROM ".MAIN_DB_PREFIX."elaska_task et WHERE et.rowid = l.fk_task AND et.fk_timeline_etape = ".(int)$this->id;
        $sql .= " )))"; // Exclure les ElaskaTask déjà chargées via fk_timeline_etape
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                if ($obj->type_task_link == 'elaska_task') {
                    $task = new ElaskaTask($this->db);
                    if ($task->fetch($obj->fk_task) > 0) {
                        $task->source = 'elaska_liaison';
                        $tasks[] = $task;
                    }
                } elseif ($obj->type_task_link == 'projet_task') {
                    // Vérifier si la classe Task de Dolibarr est disponible
                    if (file_exists(DOL_DOCUMENT_ROOT.'/projet/class/task.class.php')) {
                        require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
                        $task = new Task($this->db);
                        if ($task->fetch($obj->fk_task) > 0) {
                            $task->source = 'projet_task';
                            $tasks[] = $task;
                        }
                    }
                }
            }
        }
        
        return $tasks;
    }

    /**
     * Ajoute un commentaire à l'étape
     *
     * @param User   $user             Utilisateur qui crée le commentaire
     * @param string $commentaire_text Texte du commentaire
     * @param int    $visible_client   Visible par le client (0/1)
     * @return int                     <0 si erreur, ID du commentaire si OK
     */
    public function addCommentToEtape($user, $commentaire_text, $visible_client = 0)
    {
        global $conf;
        
        if (empty($this->id) || empty($commentaire_text)) {
            return -1;
        }
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_comment";
        $sql .= " (fk_etape, fk_user_creat, date_creation, commentaire, visible_client, entity)";
        $sql .= " VALUES (";
        $sql .= (int) $this->id.",";
        $sql .= (is_object($user) ? (int) $user->id : 0).",";
        $sql .= "'".$this->db->idate(dol_now())."',";
        $sql .= "'".$this->db->escape($commentaire_text)."',";
        $sql .= (int) $visible_client.",";
        $sql .= (int) $conf->entity;
        $sql .= ")";
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            return -2;
        }
        
        return $this->db->last_insert_id(MAIN_DB_PREFIX."elaska_dossier_timeline_etape_comment");
    }

    /**
     * Récupère les commentaires liés à l'étape
     *
     * @return array Tableau d'objets (commentaires avec infos utilisateur)
     */
    public function getLinkedComments()
    {
        $comments = array();
        
        if (empty($this->id)) {
            return $comments;
        }
        
        $sql = "SELECT c.*, u.login as user_login, u.firstname as user_firstname, u.lastname as user_lastname";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_comment as c";
        $sql .= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON c.fk_user_creat = u.rowid";
        $sql .= " WHERE c.fk_etape = ".$this->id;
        $sql .= " ORDER BY c.date_creation ASC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                // Conversion de la date en timestamp lisible
                $obj->date_creation_ts = $this->db->jdate($obj->date_creation);
                $obj->date_creation_formatted = dol_print_date($obj->date_creation_ts, 'dayhour');
                $comments[] = $obj;
            }
        }
        
        return $comments;
    }

    /**
     * Supprime un commentaire
     *
     * @param int  $comment_id ID du commentaire
     * @param User $user       Utilisateur qui supprime
     * @return bool            true si OK, false si erreur
     */
    public function deleteComment($comment_id, $user)
    {
        if (empty($this->id) || empty($comment_id)) {
            return false;
        }
        
        // Vérifier que le commentaire appartient à l'étape
        $sql = "SELECT fk_user_creat FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_comment";
        $sql .= " WHERE rowid = ".(int)$comment_id." AND fk_etape = ".(int)$this->id;
        $resql = $this->db->query($sql);
        
        if ($resql && ($obj = $this->db->fetch_object($resql))) {
            // Vérifier les droits de suppression (admin, auteur du commentaire, ou responsable du module)
            if ($user->admin || $user->id == $obj->fk_user_creat || $user->rights->elaska->all->delete) {
                $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_comment";
                $sql .= " WHERE rowid = ".(int)$comment_id;
                
                return $this->db->query($sql) ? true : false;
            }
        }
        
        return false;
    }

    /**
     * Supprime tous les commentaires liés à l'étape
     *
     * @return bool true si OK, false si erreur
     */
    public function deleteAllComments()
    {
        if (empty($this->id)) {
            return false;
        }
        
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_comment";
        $sql .= " WHERE fk_etape = ".$this->id;
        
        return $this->db->query($sql) ? true : false;
    }

    /**
     * Récupère l'historique des changements de statut de l'étape
     *
     * @return array Tableau d'objets (historique avec infos utilisateur)
     */
    public function getStatusHistory()
    {
        $history = array();
        
        if (empty($this->id)) {
            return $history;
        }
        
        $sql = "SELECT h.*, u.login as user_login, u.firstname as user_firstname, u.lastname as user_lastname";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_dossier_timeline_etape_history as h";
        $sql .= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON h.fk_user_action = u.rowid";
        $sql .= " WHERE h.fk_etape = ".$this->id;
        $sql .= " ORDER BY h.date_changement DESC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                // Conversion de la date en timestamp lisible
                $obj->date_changement_ts = $this->db->jdate($obj->date_changement);
                $obj->date_changement_formatted = dol_print_date($obj->date_changement_ts, 'dayhour');
                $history[] = $obj;
            }
        }
        
        return $history;
    }
}
}