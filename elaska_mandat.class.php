<?php
/**
 * eLaska - Classe pour gérer les mandats des entreprises clientes
 * Date: 2025-05-30
 * Version: 2.0 (Version finale pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';

if (!class_exists('ElaskaMandat', false)) {

class ElaskaMandat extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_mandat';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_mandat';
    
    /**
     * @var string Icône utilisée pour l'objet
     */
    public $picto = 'signature@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var string Référence unique du mandat
     */
    public $ref;
    
    /**
     * @var int ID du tiers (entreprise cliente)
     */
    public $fk_elaska_tiers;
    
    /**
     * @var int ID du dossier associé (optionnel)
     */
    public $fk_elaska_dossier;
    
    /**
     * @var string Date de début du mandat (format YYYY-MM-DD)
     */
    public $date_debut_mandat;
    
    /**
     * @var string Date de fin prévue du mandat (format YYYY-MM-DD)
     */
    public $date_fin_prevue_mandat;
    
    /**
     * @var string Date de fin réelle du mandat (format YYYY-MM-DD)
     */
    public $date_fin_reelle_mandat;
    
    /**
     * @var string Description de la mission
     */
    public $description_mission;
    
    /**
     * @var string Objectifs du mandat
     */
    public $objectifs_mandat;
    
    /**
     * @var string Code du statut du mandat (lié au dictionnaire)
     */
    public $statut_mandat_code;
    
    /**
     * @var string Code du type de mandat (lié au dictionnaire)
     */
    public $type_mandat_code;
    
    /**
     * @var int ID de l'utilisateur responsable
     */
    public $fk_user_responsable;
    
    /**
     * @var float Budget alloué au mandat
     */
    public $budget_alloue;
    
    /**
     * @var float Budget déjà consommé
     */
    public $budget_consomme;
    
    /**
     * @var float Taux de facturation horaire
     */
    public $taux_facturation;
    
    /**
     * @var string Conditions particulières du mandat
     */
    public $conditions_particulieres;
    
    /**
     * @var string Notes internes (non visibles par le client)
     */
    public $notes_internes;
    
    /**
     * @var string Code du niveau de priorité (lié au dictionnaire)
     */
    public $niveau_priorite_code;
    
    /**
     * @var string Date de la prochaine revue du mandat (format YYYY-MM-DD)
     */
    public $date_prochaine_revue;
    
    /**
     * @var int Pourcentage d'avancement du mandat (0-100)
     */
    public $pourcentage_avancement;

    // Champs techniques de Dolibarr
    public $rowid;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $status;
    public $entity;

    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'ref' => array('type' => 'varchar(128)', 'label' => 'RefMandat', 'enabled' => 1, 'position' => 5, 'notnull' => 0, 'visible' => 1, 'unique' => 1),
        'fk_elaska_tiers' => array('type' => 'integer:ElaskaTiers:custom/elaska/class/elaska_tiers.class.php', 'label' => 'ClientMandat', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1),
        'fk_elaska_dossier' => array('type' => 'integer:ElaskaDossier:custom/elaska/class/elaska_dossier.class.php', 'label' => 'DossierLieMandat', 'enabled' => 1, 'position' => 15, 'notnull' => 0, 'visible' => 1),
        'date_debut_mandat' => array('type' => 'date', 'label' => 'DateDebutMandat', 'enabled' => 1, 'position' => 20, 'notnull' => 0, 'visible' => 1),
        'date_fin_prevue_mandat' => array('type' => 'date', 'label' => 'DateFinPrevueMandat', 'enabled' => 1, 'position' => 25, 'notnull' => 0, 'visible' => 1),
        'date_fin_reelle_mandat' => array('type' => 'date', 'label' => 'DateFinReelleMandat', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'description_mission' => array('type' => 'text', 'label' => 'DescriptionMissionMandat', 'enabled' => 1, 'position' => 35, 'notnull' => 0, 'visible' => 1, 'textarea_html' => 1),
        'objectifs_mandat' => array('type' => 'text', 'label' => 'ObjectifsMandat', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1, 'textarea_html' => 1),
        'statut_mandat_code' => array('type' => 'varchar(30)', 'label' => 'StatutMandat', 'enabled' => 1, 'position' => 45, 'notnull' => 1, 'visible' => 1, 'default' => 'BROUILLON'),
        'type_mandat_code' => array('type' => 'varchar(30)', 'label' => 'TypeMandat', 'enabled' => 1, 'position' => 50, 'notnull' => 1, 'visible' => 1),
        'fk_user_responsable' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'ResponsableMandat', 'enabled' => 1, 'position' => 55, 'notnull' => 0, 'visible' => 1),
        'budget_alloue' => array('type' => 'double(24,8)', 'label' => 'BudgetAlloueMandat', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'budget_consomme' => array('type' => 'double(24,8)', 'label' => 'BudgetConsommeMandat', 'enabled' => 1, 'position' => 65, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'taux_facturation' => array('type' => 'double(24,8)', 'label' => 'TauxFacturationMandat', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'conditions_particulieres' => array('type' => 'text', 'label' => 'ConditionsParticulieresMandat', 'enabled' => 1, 'position' => 75, 'notnull' => 0, 'visible' => 1, 'textarea_html' => 1),
        'notes_internes' => array('type' => 'text', 'label' => 'NotesInternesMandat', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 0),
        'niveau_priorite_code' => array('type' => 'varchar(20)', 'label' => 'PrioriteMandat', 'enabled' => 1, 'position' => 85, 'notnull' => 0, 'visible' => 1, 'default' => 'NORMALE'),
        'date_prochaine_revue' => array('type' => 'date', 'label' => 'DateProchaineRevueMandat', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'pourcentage_avancement' => array('type' => 'integer', 'label' => 'AvancementMandatPct', 'enabled' => 1, 'position' => 95, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 1, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => -2),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => 0, 'visible' => -2),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => -2),
        'status' => array('type' => 'integer', 'label' => 'StatusRecord', 'enabled' => 1, 'position' => 1001, 'notnull' => 1, 'visible' => 0, 'default' => 1),
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
        if (empty($this->statut_mandat_code)) $this->statut_mandat_code = 'BROUILLON';
        if (empty($this->niveau_priorite_code)) $this->niveau_priorite_code = 'NORMALE';
        if (!isset($this->pourcentage_avancement)) $this->pourcentage_avancement = 0;
        if (!isset($this->budget_consomme)) $this->budget_consomme = 0;
        if (!isset($this->status)) $this->status = 1;
    }

    /**
     * Crée un mandat dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        global $conf;
        
        // Vérification des champs obligatoires
        if (empty($this->fk_elaska_tiers)) {
            $this->error = "Le client est obligatoire";
            return -1;
        }
        
        if (empty($this->type_mandat_code)) {
            $this->error = "Le type de mandat est obligatoire";
            return -2;
        }

        // Référence laissée vide pour ElaskaNumero
        $this->ref = '';

        // Création de l'enregistrement principal
        $result = $this->createCommon($user, $notrigger);

        // Si la création a réussi, générer la référence définitive
        if ($result > 0) {
            $params = array();
            
            // Ajouter des paramètres dynamiques pour le masque de numérotation
            if (!empty($this->type_mandat_code)) {
                $params['TYPE_MANDAT'] = $this->type_mandat_code;
            }
            
            if (!empty($this->fk_elaska_tiers)) {
                $params['ID_TIERS'] = $this->fk_elaska_tiers;
            }
            
            // Générer et enregistrer la référence
            $final_ref = ElaskaNumero::generateAndRecord(
                $this->db,
                'elaska',
                $this->element,
                $this->id,
                '', // Utiliser le masque par défaut
                $params,
                $this->entity
            );

            // Mettre à jour la référence si générée avec succès
            if ($final_ref !== -1 && !empty($final_ref)) {
                $this->ref = $final_ref;
                if (!$this->updateRef($user)) {
                    dol_syslog("ElaskaMandat::create Échec de la mise à jour de la référence définitive pour ".$this->element." ".$this->id, LOG_ERR);
                }
            } else {
                dol_syslog("ElaskaMandat::create Échec de la génération de la référence définitive pour ".$this->element." ".$this->id, LOG_ERR);
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
     * Charge un mandat depuis la base de données
     *
     * @param int    $id  ID du mandat
     * @param string $ref Référence du mandat
     * @return int        <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = null)
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Met à jour un mandat dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Vérification du pourcentage d'avancement (0-100)
        if (isset($this->pourcentage_avancement)) {
            $this->pourcentage_avancement = min(100, max(0, (int)$this->pourcentage_avancement));
        }
        
        return $this->updateCommon($user, $notrigger);
    }

    /**
     * Supprime un mandat et ses éléments liés
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        $this->db->begin();
        $error = 0;

        // 1. Gestion des contrats de service liés à ce mandat
        if (file_exists(DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_contrat_service.class.php')) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_contrat_service.class.php';
            
            // Rechercher tous les contrats liés à ce mandat
            $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_contrat_service";
            $sql.= " WHERE fk_elaska_mandat = ".(int)$this->id;
            
            $resql = $this->db->query($sql);
            if ($resql) {
                while ($obj = $this->db->fetch_object($resql)) {
                    $contrat = new ElaskaContratService($this->db);
                    if ($contrat->fetch($obj->rowid) > 0) {
                        // Option 1: Supprimer le contrat
                        if ($contrat->delete($user, 1) < 0) { // notrigger = 1
                            $this->error .= ($this->error ? '; ' : '')."Erreur lors de la suppression du contrat lié ".$contrat->ref.": ".$contrat->error;
                            $error++;
                        }
                        
                        /* Option 2: Délier le contrat sans le supprimer
                        $contrat->fk_elaska_mandat = null;
                        if ($contrat->update($user, 1) < 0) {
                            $this->error .= ($this->error ? '; ' : '')."Erreur lors de la séparation du contrat lié ".$contrat->ref.": ".$contrat->error;
                            $error++;
                        }
                        */
                    }
                }
                $this->db->free($resql);
            } else {
                $this->error = $this->db->lasterror();
                $error++;
            }
        }
        
        // 2. Gestion des documents liés à ce mandat
        if (!$error && file_exists(DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php')) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
            
            $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_document";
            $sql.= " WHERE fk_object = ".(int)$this->id;
            $sql.= " AND fk_object_type = '".$this->db->escape($this->element)."'";
            
            $resql = $this->db->query($sql);
            if ($resql) {
                while ($obj = $this->db->fetch_object($resql)) {
                    $doc = new ElaskaDocument($this->db);
                    if ($doc->fetch($obj->rowid) > 0) {
                        // Option: Délier le document sans le supprimer
                        $doc->fk_object = null;
                        $doc->fk_object_type = null;
                        if ($doc->update($user, 1) < 0) {
                            $this->error = $doc->error;
                            $error++;
                            break;
                        }
                    }
                }
                $this->db->free($resql);
            } else {
                $this->error = $this->db->lasterror();
                $error++;
            }
        }
        
        // 3. Gestion des tâches associées au mandat
        if (!$error && file_exists(DOL_DOCUMENT_ROOT.'/projet/class/task.class.php')) {
            require_once DOL_DOCUMENT_ROOT.'/projet/class/task.class.php';
            
            if (property_exists('Task', 'array_options')) {
                $sql = "SELECT t.rowid FROM ".MAIN_DB_PREFIX."projet_task AS t";
                $sql.= " INNER JOIN ".MAIN_DB_PREFIX."projet_task_extrafields AS te ON t.rowid = te.fk_object";
                $sql.= " WHERE te.fk_elaska_mandat = ".(int)$this->id;
                
                $resql = $this->db->query($sql);
                if ($resql) {
                    while ($obj = $this->db->fetch_object($resql)) {
                        $task = new Task($this->db);
                        if ($task->fetch($obj->rowid) > 0) {
                            // Option 1: Supprimer la tâche
                            /* À décommenter si besoin
                            if ($task->delete($user) < 0) {
                                $this->error .= ($this->error ? '; ' : '')."Erreur lors de la suppression de la tâche ".$task->id.": ".$task->error;
                                $error++;
                            }
                            */
                            
                            // Option 2: Délier la tâche sans la supprimer
                            if (isset($task->array_options['options_fk_elaska_mandat'])) {
                                $task->array_options['options_fk_elaska_mandat'] = '';
                                if ($task->update($user) < 0) {
                                    $this->error .= ($this->error ? '; ' : '')."Erreur lors de la séparation de la tâche ".$task->id.": ".$task->error;
                                    $error++;
                                }
                            }
                        }
                    }
                    $this->db->free($resql);
                } else {
                    $this->error = $this->db->lasterror();
                    $error++;
                }
            }
        }

        // 4. Suppression du mandat lui-même
        if (!$error) {
            $result = $this->deleteCommon($user, $notrigger);
            if ($result < 0) {
                $error++;
            }
        }

        // Validation ou annulation de la transaction
        if (!$error) {
            $this->db->commit();
            return 1;
        } else {
            $this->db->rollback();
            dol_syslog("ElaskaMandat::delete Error: ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     * Active un mandat
     *
     * @param User   $user       Utilisateur qui effectue l'action
     * @param string $date_debut Date de début (format YYYY-MM-DD), si null utilise la date actuelle
     * @return int               <0 si erreur, >0 si OK
     */
    public function activer($user, $date_debut = null)
    {
        if (empty($this->id)) return -1;
        
        // Si le statut n'est pas compatible avec l'activation
        if ($this->statut_mandat_code != 'BROUILLON' && $this->statut_mandat_code != 'EN_ATTENTE' && $this->statut_mandat_code != 'SUSPENDU') {
            $this->error = "Impossible d'activer un mandat avec le statut actuel";
            return -2;
        }
        
        // Définir la date de début si non fournie
        if (!empty($date_debut)) {
            $this->date_debut_mandat = $date_debut;
        } elseif (empty($this->date_debut_mandat)) {
            $this->date_debut_mandat = dol_print_date(dol_now(), '%Y-%m-%d');
        }
        
        // Mettre à jour le statut
        $this->statut_mandat_code = 'ACTIF';
        
        return $this->update($user);
    }
    
    /**
     * Suspend un mandat
     *
     * @param User   $user  Utilisateur qui effectue l'action
     * @param string $motif Motif de la suspension
     * @return int          <0 si erreur, >0 si OK
     */
    public function suspendre($user, $motif = '')
    {
        if (empty($this->id)) return -1;
        
        // Si le statut n'est pas compatible avec la suspension
        if ($this->statut_mandat_code != 'ACTIF') {
            $this->error = "Impossible de suspendre un mandat qui n'est pas actif";
            return -2;
        }
        
        // Mettre à jour le statut
        $this->statut_mandat_code = 'SUSPENDU';
        
        // Ajouter le motif aux notes internes
        if (!empty($motif)) {
            $date = dol_print_date(dol_now(), 'dayhour');
            $this->notes_internes = ($this->notes_internes ? $this->notes_internes . "\n\n" : '') 
                                   . "*** " . $date . " - SUSPENSION ***\n" . $motif;
        }
        
        return $this->update($user);
    }
    
    /**
     * Termine un mandat (fin normale)
     *
     * @param User   $user     Utilisateur qui effectue l'action
     * @param string $date_fin Date de fin effective (format YYYY-MM-DD)
     * @return int             <0 si erreur, >0 si OK
     */
    public function terminer($user, $date_fin = null)
    {
        if (empty($this->id)) return -1;
        
        // Si le statut n'est pas compatible avec la terminaison
        if ($this->statut_mandat_code == 'TERMINE' || $this->statut_mandat_code == 'ANNULE') {
            $this->error = "Le mandat est déjà terminé ou annulé";
            return -2;
        }
        
        // Mettre à jour le statut et la date de fin
        $this->statut_mandat_code = 'TERMINE';
        
        if (!empty($date_fin)) {
            $this->date_fin_reelle_mandat = $date_fin;
        } elseif (empty($this->date_fin_reelle_mandat)) {
            $this->date_fin_reelle_mandat = dol_print_date(dol_now(), '%Y-%m-%d');
        }
        
        // Mettre à jour le pourcentage d'avancement s'il n'est pas déjà à 100%
        if ($this->pourcentage_avancement < 100) {
            $this->pourcentage_avancement = 100;
        }
        
        return $this->update($user);
    }
    
    /**
     * Mise à jour du pourcentage d'avancement du mandat
     * 
     * @param int  $nouveau_pourcentage Nouveau pourcentage d'avancement (0-100)
     * @param User $user                Utilisateur qui effectue l'action
     * @return int                      <0 si erreur, >0 si OK
     */
    public function updateAvancement($nouveau_pourcentage, $user)
    {
        if (empty($this->id)) return -1;
        
        // Borner le pourcentage entre 0 et 100
        $nouveau_pourcentage = min(100, max(0, (int)$nouveau_pourcentage));
        
        // Si le mandat est déjà terminé et on essaie de mettre moins de 100%
        if (($this->statut_mandat_code == 'TERMINE' || $this->statut_mandat_code == 'ANNULE') && $nouveau_pourcentage < 100) {
            $this->error = "Impossible de réduire l'avancement d'un mandat terminé ou annulé";
            return -2;
        }
        
        // Mise à jour du pourcentage
        $this->pourcentage_avancement = $nouveau_pourcentage;
        
        // Si on atteint 100%, proposer de terminer le mandat si statut = ACTIF
        if ($nouveau_pourcentage == 100 && $this->statut_mandat_code == 'ACTIF') {
            // On ne change pas automatiquement le statut, cela reste un choix utilisateur
            // mais on pourrait le faire si nécessaire:
            // $this->statut_mandat_code = 'TERMINE';
        }
        
        return $this->update($user);
    }
    
    /**
     * Mise à jour du budget consommé
     * 
     * @param float $montant_ajoute Montant à ajouter au budget consommé
     * @param User  $user           Utilisateur qui effectue l'action
     * @return int                  <0 si erreur, >0 si OK
     */
    public function addBudgetConsomme($montant_ajoute, $user)
    {
        if (empty($this->id)) return -1;
        
        if (!is_numeric($montant_ajoute)) {
            $this->error = "Le montant doit être numérique";
            return -2;
        }
        
        // Calcul du nouveau montant
        $ancien_montant = (float)$this->budget_consomme;
        $this->budget_consomme = price2num($ancien_montant + $montant_ajoute, 'MT');
        
        return $this->update($user);
    }

    /**
     * Retourne la liste des mandats actifs pour un tiers donné
     * 
     * @param DoliDB $db      Base de données
     * @param int    $id_tiers ID du tiers
     * @param string $statut   Statut des mandats à récupérer (optionnel)
     * @param int    $limit    Nombre maximum de résultats (0 = pas de limite)
     * @return array|int       Tableau d'objets ElaskaMandat ou <0 si erreur
     */
    public static function getMandatsByTiers($db, $id_tiers, $statut = '', $limit = 0)
    {
        $mandats = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_mandat";
        $sql.= " WHERE fk_elaska_tiers = ".(int)$id_tiers;
        if (!empty($statut)) {
            $sql.= " AND statut_mandat_code = '".$db->escape($statut)."'";
        }
        $sql.= " ORDER BY date_debut_mandat DESC";
        if ($limit > 0) $sql.= " LIMIT ".(int)$limit;
        
        $resql = $db->query($sql);
        if (!$resql) {
            dol_syslog("ElaskaMandat::getMandatsByTiers Error ".$db->lasterror(), LOG_ERR);
            return -1;
        }
        
        while ($obj = $db->fetch_object($resql)) {
            $mandat = new ElaskaMandat($db);
            if ($mandat->fetch($obj->rowid) > 0) {
                $mandats[] = $mandat;
            }
        }
        
        $db->free($resql);
        return $mandats;
    }

    /**
     * Récupère tous les mandats qui nécessitent une revue dans les prochains jours
     * 
     * @param DoliDB $db            Base de données
     * @param int    $jours_alerte  Nombre de jours avant revue pour l'alerte
     * @param int    $entity        ID de l'entité (0 = toutes les entités)
     * @param int    $limit         Nombre maximum de résultats (0 = pas de limite)
     * @return array|int            Tableau d'objets ElaskaMandat ou <0 si erreur
     */
    public static function getMandatsARevoir($db, $jours_alerte = 7, $entity = 0, $limit = 0)
    {
        $date_limite = dol_time_plus_duree(dol_now(), $jours_alerte, 'd');
        $date_limite_str = dol_print_date($date_limite, '%Y-%m-%d');
        
        $mandats = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_mandat";
        $sql.= " WHERE statut_mandat_code = 'ACTIF'";
        $sql.= " AND date_prochaine_revue IS NOT NULL";
        $sql.= " AND date_prochaine_revue <= '".$db->escape($date_limite_str)."'";
        if ($entity > 0) $sql.= " AND entity = ".(int)$entity;
        $sql.= " ORDER BY date_prochaine_revue ASC";
        if ($limit > 0) $sql.= " LIMIT ".(int)$limit;
        
        $resql = $db->query($sql);
        if (!$resql) {
            dol_syslog("ElaskaMandat::getMandatsARevoir Error ".$db->lasterror(), LOG_ERR);
            return -1;
        }
        
        while ($obj = $db->fetch_object($resql)) {
            $mandat = new ElaskaMandat($db);
            if ($mandat->fetch($obj->rowid) > 0) {
                $mandats[] = $mandat;
            }
        }
        
        $db->free($resql);
        return $mandats;
    }

    /**
     * Récupère les options du dictionnaire de statuts de mandat
     * 
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutMandatOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire de types de mandat
     * 
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeMandatOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire de niveaux de priorité
     * 
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getNiveauPrioriteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'priorite', $usekeys, $show_empty);
    }

    /**
     * Méthode générique pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                Objet de traduction
     * @param string    $dictionary_suffix    Suffixe du nom de la table dictionnaire
     * @param bool      $usekeys              True pour retourner tableau associatif code=>label
     * @param bool      $show_empty           True pour ajouter une option vide
     * @return array                          Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_suffix, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_mandat_".$db->escape($dictionary_suffix);
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
?>