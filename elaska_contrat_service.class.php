<?php
/**
 * eLaska - Classe pour gérer les contrats de service
 * Date: 2025-05-30
 * Version: 4.0 (Version finale et complète)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_contrat_service_ligne.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';

if (!class_exists('ElaskaContratService', false)) {

class ElaskaContratService extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_contrat_service';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_contrat_service';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'file-signature@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var string Numéro de référence unique
     */
    public $ref;
    
    /**
     * @var int ID du tiers eLaska client
     */
    public $fk_elaska_tiers;
    
    /**
     * @var int ID du dossier eLaska associé (optionnel)
     */
    public $fk_elaska_dossier;
    
    /**
     * @var int ID du mandat parent (optionnel)
     */
    public $fk_elaska_mandat;
    
    /**
     * @var string Libellé du contrat
     */
    public $label;
    
    /**
     * @var string Description détaillée des services
     */
    public $description_service;
    
    /**
     * @var string Date de début du contrat (format YYYY-MM-DD)
     */
    public $date_debut_contrat;
    
    /**
     * @var string Date de fin du contrat (format YYYY-MM-DD)
     */
    public $date_fin_contrat;
    
    /**
     * @var string Date de signature du contrat (format YYYY-MM-DD)
     */
    public $date_signature_contrat;
    
    /**
     * @var int Indicateur de reconduction tacite (1=oui, 0=non)
     */
    public $reconduction_tacite;
    
    /**
     * @var int Nombre de jours de préavis pour résiliation
     */
    public $preavis_jours;
    
    /**
     * @var string Code du type de contrat (lié au dictionnaire)
     */
    public $type_contrat_service_code;
    
    /**
     * @var string Code du statut du contrat (lié au dictionnaire)
     */
    public $statut_contrat_code;
    
    /**
     * @var string Code de périodicité de facturation (lié au dictionnaire)
     */
    public $periodicite_facturation_code;
    
    /**
     * @var float Montant HT total du contrat
     */
    public $montant_ht_total;
    
    /**
     * @var float Taux de TVA par défaut
     */
    public $tva_tx;
    
    /**
     * @var float Pourcentage de remise globale
     */
    public $remise_globale_percent;
    
    /**
     * @var string Conditions tarifaires détaillées
     */
    public $conditions_tarifaires;
    
    /**
     * @var string Modalités de résiliation
     */
    public $modalites_resiliation;
    
    /**
     * @var int ID de l'utilisateur responsable du contrat
     */
    public $fk_user_responsable;
    
    /**
     * @var string Notes internes (non visibles par le client)
     */
    public $notes_internes;
    
    /**
     * @var string Date du prochain renouvellement (format YYYY-MM-DD)
     */
    public $date_prochain_renouvellement;
    
    /**
     * @var string Date de la prochaine facturation (format YYYY-MM-DD)
     */
    public $date_prochaine_facturation;
    
    /**
     * @var string Date de la dernière facturation (format YYYY-MM-DD)
     */
    public $date_derniere_facturation;
    
    /**
     * @var int Indicateur pour les alertes de renouvellement (1=actif, 0=inactif)
     */
    public $alerte_renouvellement;
    
    /**
     * @var int ID du contrat parent (pour avenants)
     */
    public $fk_contrat_parent;
    
    /**
     * @var string Code du mode de paiement préféré
     */
    public $mode_paiement_pref_code;
    
    /**
     * @var int Jour du mois pour prélèvement
     */
    public $jour_prelevement;
    
    // Champs techniques standard de Dolibarr
    public $rowid;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $entity;
    public $status;
    
    /**
     * @var array Tableau d'objets ElaskaContratServiceLigne
     */
    public $lines = array();
    
    /**
     * @var array Tableau d'objets pour l'historique des facturations
     */
    public $facturations = array();

    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'ref' => array('type' => 'varchar(128)', 'label' => 'RefContratService', 'enabled' => 1, 'position' => 5, 'notnull' => 0, 'visible' => 1, 'unique' => 1),
        'fk_elaska_tiers' => array('type' => 'integer:ElaskaTiers:custom/elaska/class/elaska_tiers.class.php', 'label' => 'EntrepriseCliente', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1),
        'fk_elaska_dossier' => array('type' => 'integer:ElaskaDossier:custom/elaska/class/elaska_dossier.class.php', 'label' => 'DossierAssocie', 'enabled' => 1, 'position' => 15, 'notnull' => 0, 'visible' => 1),
        'fk_elaska_mandat' => array('type' => 'integer:ElaskaMandat:custom/elaska/class/elaska_mandat.class.php', 'label' => 'MandatParent', 'enabled' => 1, 'position' => 20, 'notnull' => 0, 'visible' => 1),
        'label' => array('type' => 'varchar(255)', 'label' => 'Label', 'enabled' => 1, 'position' => 25, 'notnull' => 1, 'visible' => 1),
        'description_service' => array('type' => 'text', 'label' => 'DescriptionService', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'date_debut_contrat' => array('type' => 'date', 'label' => 'DateDebutContrat', 'enabled' => 1, 'position' => 35, 'notnull' => 0, 'visible' => 1),
        'date_fin_contrat' => array('type' => 'date', 'label' => 'DateFinContrat', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'date_signature_contrat' => array('type' => 'date', 'label' => 'DateSignatureContrat', 'enabled' => 1, 'position' => 42, 'notnull' => 0, 'visible' => 1),
        'reconduction_tacite' => array('type' => 'integer', 'label' => 'ReconductionTacite', 'enabled' => 1, 'position' => 45, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'preavis_jours' => array('type' => 'integer', 'label' => 'PreavisJours', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'type_contrat_service_code' => array('type' => 'varchar(30)', 'label' => 'TypeContratService', 'enabled' => 1, 'position' => 55, 'notnull' => 1, 'visible' => 1),
        'statut_contrat_code' => array('type' => 'varchar(30)', 'label' => 'StatutContrat', 'enabled' => 1, 'position' => 60, 'notnull' => 1, 'visible' => 1, 'default' => 'BROUILLON'),
        'periodicite_facturation_code' => array('type' => 'varchar(30)', 'label' => 'PeriodiciteFacturation', 'enabled' => 1, 'position' => 65, 'notnull' => 1, 'visible' => 1),
        'montant_ht_total' => array('type' => 'double(24,8)', 'label' => 'MontantHTTotal', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'tva_tx' => array('type' => 'double(6,3)', 'label' => 'TVA', 'enabled' => 1, 'position' => 75, 'notnull' => 0, 'visible' => 1),
        'remise_globale_percent' => array('type' => 'double(6,3)', 'label' => 'RemiseGlobale', 'enabled' => 1, 'position' => 78, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'conditions_tarifaires' => array('type' => 'text', 'label' => 'ConditionsTarifaires', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'modalites_resiliation' => array('type' => 'text', 'label' => 'ModalitesResiliation', 'enabled' => 1, 'position' => 85, 'notnull' => 0, 'visible' => 1),
        'fk_user_responsable' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'ResponsableContrat', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'notes_internes' => array('type' => 'text', 'label' => 'NotesInternes', 'enabled' => 1, 'position' => 95, 'notnull' => 0, 'visible' => 0),
        'date_prochain_renouvellement' => array('type' => 'date', 'label' => 'DateProchainRenouvellement', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'date_prochaine_facturation' => array('type' => 'date', 'label' => 'DateProchaineFacturation', 'enabled' => 1, 'position' => 105, 'notnull' => 0, 'visible' => 1),
        'date_derniere_facturation' => array('type' => 'date', 'label' => 'DateDerniereFacturation', 'enabled' => 1, 'position' => 108, 'notnull' => 0, 'visible' => 1),
        'alerte_renouvellement' => array('type' => 'integer', 'label' => 'AlerteRenouvellement', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1, 'default' => 1),
        'fk_contrat_parent' => array('type' => 'integer:ElaskaContratService:custom/elaska/class/elaska_contrat_service.class.php', 'label' => 'ContratParent', 'enabled' => 1, 'position' => 115, 'notnull' => 0, 'visible' => 1),
        'mode_paiement_pref_code' => array('type' => 'varchar(30)', 'label' => 'ModePaiementPrefere', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'jour_prelevement' => array('type' => 'integer', 'label' => 'JourPrelevement', 'enabled' => 1, 'position' => 125, 'notnull' => 0, 'visible' => 1),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 1, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => -2),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => 0, 'visible' => -2),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => -2),
        'status' => array('type' => 'integer', 'label' => 'Status', 'enabled' => 1, 'position' => 1002, 'notnull' => 1, 'visible' => -2, 'default' => '1'),
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'default' => 1, 'enabled' => 1, 'visible' => -2, 'notnull' => 1, 'position' => 1003),
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
        if (empty($this->statut_contrat_code)) $this->statut_contrat_code = 'BROUILLON';
        if (!isset($this->reconduction_tacite)) $this->reconduction_tacite = 0;
        if (!isset($this->alerte_renouvellement)) $this->alerte_renouvellement = 1;
        if (!isset($this->montant_ht_total)) $this->montant_ht_total = 0;
        if (!isset($this->remise_globale_percent)) $this->remise_globale_percent = 0;
        if (!isset($this->status)) $this->status = 1;
    }

    /**
     * Crée un contrat de service dans la base de données
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
        if (empty($this->fk_elaska_tiers)) {
            $this->error = "Le client est obligatoire";
            $this->db->rollback();
            return -1;
        }
        
        if (empty($this->label)) {
            $this->error = "Le libellé du contrat est obligatoire";
            $this->db->rollback();
            return -2;
        }
        
        if (empty($this->type_contrat_service_code)) {
            $this->error = "Le type de contrat est obligatoire";
            $this->db->rollback();
            return -3;
        }
        
        if (empty($this->periodicite_facturation_code)) {
            $this->error = "La périodicité de facturation est obligatoire";
            $this->db->rollback();
            return -4;
        }

        // Référence temporaire pour createCommon
        $this->ref = '';
        
        // Calcul des dates de renouvellement et de facturation si non définies
        $this->calculerDatesPrevisionnelles();
        
        $result = $this->createCommon($user, $notrigger);
        
        if ($result > 0) {
            // Générer la référence définitive avec ElaskaNumero
            $params = array();
            // Ajouter des paramètres spécifiques pour le masque
            if (!empty($this->type_contrat_service_code)) {
                $params['TYPE_CONTRAT'] = $this->type_contrat_service_code;
            }
            if (!empty($this->fk_elaska_tiers)) {
                $params['ID_TIERS'] = $this->fk_elaska_tiers;
            }
            
            $final_ref = ElaskaNumero::generateAndRecord(
                $this->db,
                'elaska',
                $this->element,
                $this->id,
                '', // Utilise le modèle actif par défaut
                $params,
                $this->entity
            );
            
            if ($final_ref !== -1 && !empty($final_ref)) {
                $this->ref = $final_ref;
                if (!$this->updateRef($user)) {
                    $error++;
                    dol_syslog("ElaskaContratService::create Erreur lors de la mise à jour de la référence", LOG_ERR);
                }
            } else {
                $error++;
                dol_syslog("ElaskaContratService::create Erreur lors de la génération de la référence", LOG_ERR);
            }

            // Créer les lignes si elles existent
            if (!$error && !empty($this->lines) && is_array($this->lines)) {
                foreach ($this->lines as $line) {
                    if (empty($line->rowid) && is_object($line) && get_class($line) === 'ElaskaContratServiceLigne') {
                        $line->fk_contrat_service = $this->id;
                        $line->entity = $this->entity;
                        if ($line->create($user, 1) < 0) { // notrigger = 1 pour éviter les boucles
                            $this->error = $line->error;
                            $this->errors = array_merge($this->errors, $line->errors);
                            $error++;
                            break;
                        }
                    }
                }
                
                // Recalculer le total après création des lignes
                if (!$error) {
                    $this->updateTotalAmount($user, 1);
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
            return -10;
        }
    }

    /**
     * Met à jour uniquement la référence d'un contrat après génération via ElaskaNumero
     *
     * @param User $user Utilisateur qui effectue la mise à jour
     * @return bool       true si OK, false si KO
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
     * Charge un contrat de service de la base de données ainsi que ses lignes
     *
     * @param int    $id    Id du contrat
     * @param string $ref   Référence du contrat
     * @return int          <0 si KO, >0 si OK
     */
    public function fetch($id, $ref = null)
    {
        $result = $this->fetchCommon($id, $ref);
        
        if ($result > 0) {
            // Charger les lignes de contrat
            $this->loadLines();
            
            // Charger l'historique des facturations
            $this->fetchFacturations();
        }
        
        return $result;
    }

    /**
     * Met à jour un contrat de service dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        $error = 0;
        $this->db->begin();

        // Recalculer les dates prévisionnelles si nécessaire
        if ($this->reconduction_tacite && empty($this->date_prochain_renouvellement) && !empty($this->date_debut_contrat)) {
            $this->calculerDatesPrevisionnelles();
        }
        
        $result = $this->updateCommon($user, $notrigger);
        
        if ($result < 0) {
            $error++;
        }

        // Gestion des lignes: synchronisation entre la liste actuelle et celle de la base de données
        if (!$error && isset($this->lines) && is_array($this->lines) && !empty($this->id)) {
            // Récupérer les lignes actuelles en base
            $sql = "SELECT rowid FROM " . MAIN_DB_PREFIX . "elaska_contrat_service_ligne";
            $sql .= " WHERE fk_contrat_service = " . (int)$this->id;
            
            $resql = $this->db->query($sql);
            if (!$resql) {
                $this->error = $this->db->lasterror();
                $error++;
            } else {
                // Liste des ID de lignes en base
                $existing_line_ids = array();
                while ($obj = $this->db->fetch_object($resql)) {
                    $existing_line_ids[$obj->rowid] = $obj->rowid;
                }
                $this->db->free($resql);
                
                // Liste des ID de lignes dans $this->lines
                $current_line_ids = array();
                foreach ($this->lines as $line) {
                    if (is_object($line) && !empty($line->rowid)) {
                        $current_line_ids[$line->rowid] = $line->rowid;
                    }
                }
                
                // 1. Identifier et supprimer les lignes qui ne sont plus dans $this->lines
                foreach ($existing_line_ids as $line_id) {
                    if (!isset($current_line_ids[$line_id])) {
                        $line_to_delete = new ElaskaContratServiceLigne($this->db);
                        if ($line_to_delete->fetch($line_id) > 0) {
                            if ($line_to_delete->delete($user, 1) < 0) { // notrigger=1
                                $this->error = "Erreur lors de la suppression de la ligne " . $line_id . ": " . $line_to_delete->error;
                                $error++;
                                break;
                            }
                        }
                    }
                }
                
                // 2. Mettre à jour/créer les lignes de $this->lines
                if (!$error) {
                    foreach ($this->lines as $line) {
                        if (is_object($line)) {
                            if (!empty($line->rowid) && isset($existing_line_ids[$line->rowid])) {
                                // Ligne existante à mettre à jour
                                if ($line->update($user, 1) < 0) { // notrigger=1
                                    $this->error = "Erreur lors de la mise à jour de la ligne " . $line->rowid . ": " . $line->error;
                                    $error++;
                                    break;
                                }
                            } elseif (empty($line->rowid)) {
                                // Nouvelle ligne à créer
                                $line->fk_contrat_service = $this->id;
                                $line->entity = $this->entity;
                                if ($line->create($user, 1) < 0) { // notrigger=1
                                    $this->error = "Erreur lors de la création d'une nouvelle ligne: " . $line->error;
                                    $error++;
                                    break;
                                }
                            }
                        }
                    }
                }
                
                // 3. Recalculer le montant total
                if (!$error) {
                    $this->updateTotalAmount($user, 1);
                }
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
     * Supprime un contrat de service et ses lignes de la base de données
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        $this->db->begin();
        $error = 0;

        // Chargement des lignes si nécessaire
        if (empty($this->lines)) {
            $this->loadLines();
        }

        // Suppression des lignes de contrat (même avec CASCADE dans SQL)
        // Cela permet d'exécuter la logique de suppression propre à chaque ligne
        foreach ($this->lines as $line) {
            if ($line->delete($user, 1) < 0) { // notrigger=1
                $this->error .= ($this->error ? '; ' : '') . "Erreur lors de la suppression de la ligne " . $line->rowid . ": " . $line->error;
                $error++;
                break;
            }
        }

        // Suppression des facturations associées
        if (!$error) {
            $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_contrat_service_facturation";
            $sql.= " WHERE fk_contrat_service = ".(int)$this->id;
            
            if (!$this->db->query($sql)) {
                $this->error = $this->db->lasterror();
                dol_syslog(get_class($this)."::delete Error deleting contract billing history: ".$this->error, LOG_ERR);
                $error++;
            }
        }
        
        // Suppression des documents liés à ce contrat
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
                        // Option 1: Délier le document sans le supprimer
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

        // Suppression du contrat lui-même
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
     * Charge les lignes du contrat
     *
     * @return int <0 si KO, nombre de lignes si OK
     */
    public function loadLines()
    {
        $this->lines = array();
        
        if (empty($this->id)) return 0;
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_contrat_service_ligne";
        $sql .= " WHERE fk_contrat_service = " . (int)$this->id;
        $sql .= " ORDER BY rang ASC, rowid ASC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            $num_rows = $this->db->num_rows($resql);
            while ($obj = $this->db->fetch_object($resql)) {
                $line = new ElaskaContratServiceLigne($this->db);
                if ($line->fetch($obj->rowid) > 0) {
                    $this->lines[] = $line;
                }
            }
            $this->db->free($resql);
            return $num_rows;
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this)."::loadLines Error ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     * Ajoute une ligne au contrat de service
     *
     * @param ElaskaContratServiceLigne $line Ligne à ajouter
     * @param User                      $user Utilisateur qui effectue l'action
     * @return int                            <0 si erreur, ID de la ligne si OK
     */
    public function addLigne(ElaskaContratServiceLigne $line, $user)
    {
        if (empty($this->id)) {
            $this->error = "Le contrat parent n'est pas créé ou chargé";
            return -1;
        }
        
        $line->fk_contrat_service = $this->id;
        $line->entity = $this->entity;
        
        $result = $line->create($user);
        
        if ($result > 0) {
            $this->lines[] = $line;
            $this->updateTotalAmount($user, 1);
            return $result;
        } else {
            $this->error = "Échec de la création de la ligne: " . $line->error;
            return -2;
        }
    }

    /**
     * Met à jour une ligne de contrat
     *
     * @param ElaskaContratServiceLigne $line Ligne à mettre à jour
     * @param User                      $user Utilisateur qui effectue l'action
     * @return int                            <0 si erreur, >0 si OK
     */
    public function updateLigne(ElaskaContratServiceLigne $line, $user)
    {
        if (empty($line->rowid) || $line->fk_contrat_service != $this->id) {
            $this->error = "Ligne invalide ou non liée à ce contrat";
            return -1;
        }
        
        $result = $line->update($user);
        
        if ($result > 0) {
            $this->updateTotalAmount($user, 1);
            return $result;
        } else {
            $this->error = $line->error;
            return -2;
        }
    }

    /**
     * Supprime une ligne de contrat
     *
     * @param int  $line_id ID de la ligne à supprimer
     * @param User $user    Utilisateur qui effectue l'action
     * @return int          <0 si erreur, >0 si OK
     */
    public function deleteLigne($line_id, $user)
    {
        $line_to_delete = new ElaskaContratServiceLigne($this->db);
        
        if ($line_to_delete->fetch($line_id) > 0) {
            if ($line_to_delete->fk_contrat_service == $this->id) {
                $result = $line_to_delete->delete($user);
                
                if ($result > 0) {
                    $this->loadLines();
                    $this->updateTotalAmount($user, 1);
                    return 1;
                } else {
                    $this->error = "Échec de la suppression de la ligne: " . $line_to_delete->error;
                    return -2;
                }
            } else {
                $this->error = "Cette ligne n'appartient pas à ce contrat";
                return -3;
            }
        } else {
            $this->error = "Ligne non trouvée";
            return -4;
        }
    }

    /**
     * Calcule le montant total HT des lignes
     *
     * @return float Montant total HT
     */
    public function calculateTotalHTLignes()
    {
        $total = 0;
        
        if (empty($this->lines) && $this->id) {
            $this->loadLines();
        }
        
        foreach ($this->lines as $line) {
            // S'assurer que les montants sont calculés pour chaque ligne
            if (method_exists($line, 'calculerMontants')) {
                $line->calculerMontants();
            }
            $total += $line->montant_ligne_ht;
        }
        
        // Appliquer la remise globale si définie
        if (!empty($this->remise_globale_percent)) {
            $total = $total * (1 - ($this->remise_globale_percent / 100));
        }
        
        return price2num($total, 'MT');
    }

    /**
     * Calcule le montant total TTC des lignes
     *
     * @return float Montant total TTC
     */
    public function calculateTotalTTCLignes()
    {
        $total = 0;
        
        if (empty($this->lines) && $this->id) {
            $this->loadLines();
        }
        
        foreach ($this->lines as $line) {
            // S'assurer que les montants sont calculés pour chaque ligne
            if (method_exists($line, 'calculerMontants')) {
                $line->calculerMontants();
            }
            $total += $line->montant_ligne_ttc;
        }
        
        // Appliquer la remise globale si définie
        if (!empty($this->remise_globale_percent)) {
            $total = $total * (1 - ($this->remise_globale_percent / 100));
        }
        
        return price2num($total, 'MT');
    }

    /**
     * Met à jour le montant total du contrat en fonction des lignes
     *
     * @param User $user      Utilisateur qui effectue la mise à jour
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function updateTotalAmount($user, $notrigger = 1)
    {
        if (empty($this->id)) return -1;
        
        // Recharger les lignes pour être sûr d'avoir les valeurs à jour
        $this->loadLines();
        
        $this->montant_ht_total = $this->calculateTotalHTLignes();
        
        // Mettre à jour uniquement le montant total
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
        $sql .= " SET montant_ht_total = " . (float)$this->montant_ht_total;
        $sql .= ", fk_user_modif = " . (int)$user->id;
        $sql .= ", tms = '" . $this->db->idate(dol_now()) . "'";
        $sql .= " WHERE rowid = ".(int)$this->id;
        
        dol_syslog(get_class($this)."::updateTotalAmount", LOG_DEBUG);
        $resql = $this->db->query($sql);
        
        if (!$resql) {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this)."::updateTotalAmount ".$this->error, LOG_ERR);
            return -1;
        }
        
        return 1;
    }

    /**
     * Charge les facturations associées au contrat
     *
     * @return int <0 si KO, nombre d'enregistrements si OK
     */
    public function fetchFacturations()
    {
        $this->facturations = array();
        
        $sql = "SELECT rowid, date_facturation, montant_ht, montant_ttc, fk_facture_dolibarr, commentaire, date_creation, fk_user_creat";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_contrat_service_facturation";
        $sql .= " WHERE fk_contrat_service = " . (int)$this->id;
        $sql .= " ORDER BY date_facturation DESC, rowid DESC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            $num_rows = $this->db->num_rows($resql);
            while ($obj = $this->db->fetch_object($resql)) {
                $facturation = array(
                    'rowid' => $obj->rowid,
                    'date_facturation' => $this->db->jdate($obj->date_facturation),
                    'montant_ht' => $obj->montant_ht,
                    'montant_ttc' => $obj->montant_ttc,
                    'fk_facture_dolibarr' => $obj->fk_facture_dolibarr,
                    'commentaire' => $obj->commentaire,
                    'date_creation' => $this->db->jdate($obj->date_creation),
                    'fk_user_creat' => $obj->fk_user_creat
                );
                $this->facturations[] = $facturation;
            }
            $this->db->free($resql);
            return $num_rows;
        } else {
            $this->error = $this->db->lasterror();
            return -1;
        }
    }

    /**
     * Calcule les dates de renouvellement et facturation basées sur les infos de contrat
     * À appeler lors de la création ou modification du contrat
     */
    public function calculerDatesPrevisionnelles()
    {
        // Si pas de date de début, on ne peut pas calculer
        if (empty($this->date_debut_contrat)) {
            return;
        }
        
        // Calcul de la date de prochain renouvellement si reconduction tacite
        if ($this->reconduction_tacite && empty($this->date_prochain_renouvellement)) {
            if (!empty($this->date_fin_contrat)) {
                // Si date de fin définie, le renouvellement est à cette date
                $this->date_prochain_renouvellement = $this->date_fin_contrat;
            } else {
                // Sinon, on calcule 1 an après la date de début
                $date_debut_obj = new DateTime($this->date_debut_contrat);
                $date_debut_obj->add(new DateInterval('P1Y')); // Ajouter 1 an
                $this->date_prochain_renouvellement = $date_debut_obj->format('Y-m-d');
            }
        }
        
        // Calcul de la date de prochaine facturation selon périodicité
        if (empty($this->date_prochaine_facturation) && !empty($this->periodicite_facturation_code)) {
            $nb_jours = $this->getNbJoursPeriodicite($this->periodicite_facturation_code);
            
            if ($nb_jours > 0) {
                $date_debut_obj = new DateTime($this->date_debut_contrat);
                $date_debut_obj->add(new DateInterval('P'.$nb_jours.'D')); // Ajouter nb_jours
                $this->date_prochaine_facturation = $date_debut_obj->format('Y-m-d');
            }
        }
    }
    
    /**
     * Récupère le nombre de jours correspondant à une périodicité
     *
     * @param string $code_periodicite Code de la périodicité
     * @return int Nombre de jours
     */
    public function getNbJoursPeriodicite($code_periodicite)
    {
        if (empty($code_periodicite)) return 0;
        
        $sql = "SELECT nb_jours FROM ".MAIN_DB_PREFIX."c_elaska_contrat_service_periodicite_fact";
        $sql.= " WHERE code = '".$this->db->escape($code_periodicite)."'";
        $sql.= " AND active = 1";
        
        $resql = $this->db->query($sql);
        if ($resql && $this->db->num_rows($resql) > 0) {
            $obj = $this->db->fetch_object($resql);
            $this->db->free($resql);
            return (int)$obj->nb_jours;
        }
        
        return 0; // Par défaut
    }
    
    /**
     * Active un contrat de service
     *
     * @param User   $user       Utilisateur qui effectue l'action
     * @param string $date_debut Date de début (format YYYY-MM-DD), si null utilise la date actuelle
     * @return int               <0 si erreur, >0 si OK
     */
    public function activer($user, $date_debut = null)
    {
        if (empty($this->id)) return -1;
        
        // Si le statut n'est pas compatible avec l'activation
        if ($this->statut_contrat_code != 'BROUILLON' && $this->statut_contrat_code != 'VALIDE' && $this->statut_contrat_code != 'SUSPENDU') {
            $this->error = "Impossible d'activer un contrat avec le statut actuel";
            return -2;
        }
        
        // Définir la date de début si non fournie
        if (!empty($date_debut)) {
            $this->date_debut_contrat = $date_debut;
        } elseif (empty($this->date_debut_contrat)) {
            $this->date_debut_contrat = dol_print_date(dol_now(), '%Y-%m-%d');
        }
        
        // Mettre à jour le statut
        $this->statut_contrat_code = 'ACTIF';
        
        // Calculer les dates prévisionnelles
        $this->calculerDatesPrevisionnelles();
        
        // Activer les lignes si elles existent
        $this->updateStatutLignes('ACTIVE', $user);
        
        return $this->update($user);
    }
    
    /**
     * Suspend un contrat de service
     *
     * @param User   $user    Utilisateur qui effectue l'action
     * @param string $motif   Motif de la suspension
     * @return int            <0 si erreur, >0 si OK
     */
    public function suspendre($user, $motif = '')
    {
        if (empty($this->id)) return -1;
        
        // Si le statut n'est pas compatible avec la suspension
        if ($this->statut_contrat_code != 'ACTIF') {
            $this->error = "Impossible de suspendre un contrat qui n'est pas actif";
            return -2;
        }
        
        // Mettre à jour le statut
        $this->statut_contrat_code = 'SUSPENDU';
        
        // Ajouter le motif aux notes internes
        if (!empty($motif)) {
            $date = dol_print_date(dol_now(), 'dayhour');
            $this->notes_internes = ($this->notes_internes ? $this->notes_internes . "\n\n" : '') 
                                   . "*** " . $date . " - SUSPENSION ***\n" . $motif;
        }
        
        // Suspendre les lignes actives
        $this->updateStatutLignes('SUSPENDUE', $user);
        
        return $this->update($user);
    }
    
    /**
     * Résilie un contrat de service
     *
     * @param User   $user               Utilisateur qui effectue l'action
     * @param string $date_fin           Date de fin effective (format YYYY-MM-DD)
     * @param string $motif_resiliation  Motif de la résiliation
     * @return int                       <0 si erreur, >0 si OK
     */
    public function resilier($user, $date_fin, $motif_resiliation = '')
    {
        if (empty($this->id)) return -1;
        
        // Si le statut n'est pas compatible avec la résiliation
        if ($this->statut_contrat_code == 'TERMINE' || $this->statut_contrat_code == 'RESILIE') {
            $this->error = "Le contrat est déjà clôturé ou résilié";
            return -2;
        }
        
        // Mettre à jour le statut et la date de fin
        $this->statut_contrat_code = 'RESILIE';
        $this->date_fin_contrat = $date_fin;
        
        // Réinitialiser les dates de renouvellement et facturation
        $this->date_prochain_renouvellement = null;
        
        // Ajouter le motif aux notes internes
        if (!empty($motif_resiliation)) {
            $date = dol_print_date(dol_now(), 'dayhour');
            $this->notes_internes = ($this->notes_internes ? $this->notes_internes . "\n\n" : '') 
                                   . "*** " . $date . " - RÉSILIATION ***\n" . $motif_resiliation;
        }
        
        // Mettre à jour les lignes
        $this->updateStatutLignes('TERMINEE', $user);
        
        return $this->update($user);
    }
    
    /**
     * Termine un contrat de service (fin normale)
     *
     * @param User   $user     Utilisateur qui effectue l'action
     * @param string $date_fin Date de fin effective (format YYYY-MM-DD)
     * @return int             <0 si erreur, >0 si OK
     */
    public function terminer($user, $date_fin = null)
    {
        if (empty($this->id)) return -1;
        
        // Si le statut n'est pas compatible avec la terminaison
        if ($this->statut_contrat_code == 'TERMINE' || $this->statut_contrat_code == 'RESILIE') {
            $this->error = "Le contrat est déjà clôturé ou résilié";
            return -2;
        }
        
        // Mettre à jour le statut et la date de fin
        $this->statut_contrat_code = 'TERMINE';
        if (!empty($date_fin)) {
            $this->date_fin_contrat = $date_fin;
        } elseif (empty($this->date_fin_contrat)) {
            $this->date_fin_contrat = dol_print_date(dol_now(), '%Y-%m-%d');
        }
        
        // Réinitialiser les dates de renouvellement
        $this->date_prochain_renouvellement = null;
        
        // Mettre à jour les lignes
        $this->updateStatutLignes('TERMINEE', $user);
        
        return $this->update($user);
    }
    
    /**
     * Renouvelle un contrat de service
     *
     * @param User   $user             Utilisateur qui effectue l'action
     * @param string $nouvelle_fin     Nouvelle date de fin (format YYYY-MM-DD)
     * @param bool   $conserver_dates  Conserver les dates de période existantes
     * @return int                     <0 si erreur, >0 si OK
     */
    public function renouveler($user, $nouvelle_fin = null, $conserver_dates = false)
    {
        if (empty($this->id)) return -1;
        
        // Si le statut n'est pas compatible avec le renouvellement
        if ($this->statut_contrat_code != 'ACTIF' && $this->statut_contrat_code != 'TERMINE') {
            $this->error = "Impossible de renouveler un contrat avec le statut actuel";
            return -2;
        }
        
        // Mettre à jour le statut
        $this->statut_contrat_code = 'ACTIF';
        
        // Si date de fin fournie, la mettre à jour
        if (!empty($nouvelle_fin)) {
            $this->date_fin_contrat = $nouvelle_fin;
            
            // Calculer la nouvelle date de renouvellement
            if ($this->reconduction_tacite) {
                $this->date_prochain_renouvellement = $nouvelle_fin;
            }
        } 
        // Si pas de date fournie mais que le contrat en avait une
        else if (!empty($this->date_fin_contrat) && !$conserver_dates) {
            // Calculer nouvelle date de fin (1 an après la précédente)
            $date_fin_obj = new DateTime($this->date_fin_contrat);
            $date_fin_obj->add(new DateInterval('P1Y')); // Ajouter 1 an
            $this->date_fin_contrat = $date_fin_obj->format('Y-m-d');
            
            // Mettre à jour la date de renouvellement
            if ($this->reconduction_tacite) {
                $this->date_prochain_renouvellement = $this->date_fin_contrat;
            }
        }
        
        // Si on ne conserve pas les dates existantes, recalculer la prochaine facturation
        if (!$conserver_dates) {
            $this->calculerDateProchaineFacturation();
        }
        
        // Noter le renouvellement dans les notes internes
        $date = dol_print_date(dol_now(), 'dayhour');
        $this->notes_internes = ($this->notes_internes ? $this->notes_internes . "\n\n" : '') 
                               . "*** " . $date . " - RENOUVELLEMENT ***";
        if (!empty($this->date_fin_contrat)) {
            $this->notes_internes .= "\nNouvelle date de fin: " . $this->date_fin_contrat;
        }
        
        // Mettre à jour les lignes de contrat
        $this->updateStatutLignes('ACTIVE', $user);
        
        return $this->update($user);
    }
    
    /**
     * Met à jour les statuts des lignes du contrat
     *
     * @param string $nouveau_statut Nouveau statut à appliquer aux lignes
     * @param User   $user           Utilisateur qui effectue l'action
     * @return int                   <0 si erreur, nombre de lignes mises à jour si OK
     */
    public function updateStatutLignes($nouveau_statut, $user)
    {
        if (empty($this->id)) return -1;
        
        // Recharger les lignes pour être sûr d'avoir les valeurs à jour
        $this->loadLines();
        
        $nb_updated = 0;
        foreach ($this->lines as $line) {
            if ($line->statut_ligne_code != $nouveau_statut) {
                $line->statut_ligne_code = $nouveau_statut;
                if ($line->update($user, 1) > 0) {
                    $nb_updated++;
                } else {
                    $this->error = $line->error;
                    return -2;
                }
            }
        }
        
        return $nb_updated;
    }
    
    /**
     * Calcule la prochaine date de facturation à partir de la périodicité
     *
     * @param string $date_reference Date de référence pour le calcul (YYYY-MM-DD), null = date actuelle
     * @return string|null           Nouvelle date de facturation (YYYY-MM-DD) ou null si erreur
     */
    public function calculerDateProchaineFacturation($date_reference = null)
    {
        // Si pas de périodicité définie ou facturation ponctuelle
        if (empty($this->periodicite_facturation_code) || $this->periodicite_facturation_code == 'PONCTUELLE') {
            $this->date_prochaine_facturation = null;
            return null;
        }
        
        // Déterminer la date de référence
        if (empty($date_reference)) {
            $date_reference = dol_print_date(dol_now(), '%Y-%m-%d');
        }
        
        // Récupérer le nombre de jours pour cette périodicité
        $nb_jours = $this->getNbJoursPeriodicite($this->periodicite_facturation_code);
        
        if ($nb_jours > 0) {
            $date_ref_obj = new DateTime($date_reference);
            $date_ref_obj->add(new DateInterval('P'.$nb_jours.'D')); // Ajouter nb_jours
            $this->date_prochaine_facturation = $date_ref_obj->format('Y-m-d');
            return $this->date_prochaine_facturation;
        }
        
        return null;
    }
    
    /**
     * Vérifie si le contrat nécessite une alerte de renouvellement
     *
     * @param int $jours_avant_alerte Nombre de jours avant fin pour générer l'alerte
     * @return bool                   True si une alerte est nécessaire
     */
    public function necessite_alerte_renouvellement($jours_avant_alerte = 30)
    {
        // Si pas d'alerte activée ou statut non compatible
        if (!$this->alerte_renouvellement || 
            $this->statut_contrat_code != 'ACTIF' || 
            empty($this->date_fin_contrat) ||
            empty($this->date_prochain_renouvellement)) {
            return false;
        }
        
        // Date actuelle + jours_avant_alerte
        $date_alerte = new DateTime();
        $date_alerte->add(new DateInterval('P'.$jours_avant_alerte.'D'));
        
        // Date de fin du contrat
        $date_fin = new DateTime($this->date_fin_contrat);
        
        // Si la date d'alerte est après la date de fin, une alerte est nécessaire
        return ($date_alerte >= $date_fin);
    }
    
    /**
     * Enregistre une facturation dans l'historique
     *
     * @param User   $user           Utilisateur qui effectue l'action
     * @param string $date_facture   Date de facturation (format YYYY-MM-DD)
     * @param float  $montant_ht     Montant HT facturé
     * @param float  $montant_ttc    Montant TTC facturé
     * @param int    $fk_facture     ID de la facture Dolibarr associée (optionnel)
     * @param string $commentaire    Commentaire optionnel
     * @return int                   <0 si erreur, ID de l'historique si OK
     */
    public function enregistrerFacturation($user, $date_facture, $montant_ht, $montant_ttc, $fk_facture = null, $commentaire = '')
    {
        if (empty($this->id)) return -1;

        $this->db->begin();
        
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "elaska_contrat_service_facturation";
        $sql .= " (fk_contrat_service, date_facturation, montant_ht, montant_ttc, fk_facture_dolibarr, commentaire, date_creation, fk_user_creat, entity)";
        $sql .= " VALUES (";
        $sql .= (int) $this->id . ", ";
        $sql .= "'" . $this->db->idate(strtotime($date_facture)) . "', ";
        $sql .= (float) $montant_ht . ", ";
        $sql .= (float) $montant_ttc . ", ";
        $sql .= ($fk_facture ? (int) $fk_facture : "NULL") . ", ";
        $sql .= "'" . $this->db->escape($commentaire) . "', ";
        $sql .= "'" . $this->db->idate(dol_now()) . "', ";
        $sql .= (int) $user->id . ", ";
        $sql .= (int) $this->entity;
        $sql .= ")";
        
        $resql = $this->db->query($sql);
        
        if (!$resql) {
            $this->error = "Erreur lors de l'enregistrement de la facturation: " . $this->db->lasterror();
            $this->db->rollback();
            dol_syslog(get_class($this) . "::enregistrerFacturation " . $this->error, LOG_ERR);
            return -2;
        }
        
        $facturation_id = $this->db->last_insert_id(MAIN_DB_PREFIX . "elaska_contrat_service_facturation");
        
        // Mettre à jour la date de dernière facturation
        $this->date_derniere_facturation = $date_facture;
        
        $sql = "UPDATE " . MAIN_DB_PREFIX . "elaska_contrat_service";
        $sql .= " SET date_derniere_facturation = '" . $this->db->idate(strtotime($date_facture)) . "'";
        $sql .= ", date_prochaine_facturation = NULL"; // Réinitialiser la date de prochaine facturation
        $sql .= " WHERE rowid = " . (int) $this->id;
        
        $resql = $this->db->query($sql);
        
        if (!$resql) {
            $this->error = "Erreur lors de la mise à jour de la date de dernière facturation: " . $this->db->lasterror();
            $this->db->rollback();
            dol_syslog(get_class($this) . "::enregistrerFacturation " . $this->error, LOG_ERR);
            return -3;
        }
        
        // Calculer la prochaine date de facturation
        $this->calculerDateProchaineFacturation($date_facture);
        
        if (!empty($this->date_prochaine_facturation)) {
            $sql = "UPDATE " . MAIN_DB_PREFIX . "elaska_contrat_service";
            $sql .= " SET date_prochaine_facturation = '" . $this->db->idate(strtotime($this->date_prochaine_facturation)) . "'";
            $sql .= " WHERE rowid = " . (int) $this->id;
            
            $resql = $this->db->query($sql);
            
            if (!$resql) {
                $this->error = "Erreur lors de la mise à jour de la prochaine date de facturation: " . $this->db->lasterror();
                $this->db->rollback();
                dol_syslog(get_class($this) . "::enregistrerFacturation " . $this->error, LOG_ERR);
                return -4;
            }
        }
        
        $this->db->commit();
        
        // Recharger l'historique des facturations
        $this->fetchFacturations();
        
        return $facturation_id;
    }

    /**
     * Récupère les contrats qui doivent être facturés (date_prochaine_facturation <= date du jour)
     * 
     * @param DoliDB $db          Base de données
     * @param int    $entity      ID de l'entité (0 = toutes les entités)
     * @param int    $limit       Nombre maximum de contrats à retourner (0 = pas de limite)
     * @return array|int          Tableau d'objets ElaskaContratService ou <0 si erreur
     */
    public static function getContratsAFacturer($db, $entity = 0, $limit = 0)
    {
        $contrats = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_contrat_service";
        $sql.= " WHERE statut_contrat_code = 'ACTIF'";
        $sql.= " AND date_prochaine_facturation IS NOT NULL";
        $sql.= " AND date_prochaine_facturation <= '".dol_print_date(dol_now(), '%Y-%m-%d')."'";
        if ($entity > 0) $sql.= " AND entity = ".(int)$entity;
        $sql.= " ORDER BY date_prochaine_facturation ASC";
        if ($limit > 0) $sql.= " LIMIT ".(int)$limit;
        
        $resql = $db->query($sql);
        if (!$resql) {
            dol_syslog("ElaskaContratService::getContratsAFacturer Error ".$db->lasterror(), LOG_ERR);
            return -1;
        }
        
        while ($obj = $db->fetch_object($resql)) {
            $contrat = new ElaskaContratService($db);
            if ($contrat->fetch($obj->rowid) > 0) {
                $contrats[] = $contrat;
            }
        }
        
        $db->free($resql);
        return $contrats;
    }

    /**
     * Récupère les contrats qui nécessitent une alerte de renouvellement
     * 
     * @param DoliDB $db                Base de données
     * @param int    $jours_avant_alerte Nombre de jours avant échéance pour l'alerte
     * @param int    $entity             ID de l'entité (0 = toutes les entités)
     * @param int    $limit              Nombre maximum de contrats à retourner (0 = pas de limite)
     * @return array|int                 Tableau d'objets ElaskaContratService ou <0 si erreur
     */
    public static function getContratsARenouveler($db, $jours_avant_alerte = 30, $entity = 0, $limit = 0)
    {
        $date_limite = dol_time_plus_duree(dol_now(), $jours_avant_alerte, 'd');
        $date_limite_str = dol_print_date($date_limite, '%Y-%m-%d');
        
        $contrats = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_contrat_service";
        $sql.= " WHERE statut_contrat_code = 'ACTIF'";
        $sql.= " AND alerte_renouvellement = 1";
        $sql.= " AND date_fin_contrat IS NOT NULL";
        $sql.= " AND date_fin_contrat <= '".$db->escape($date_limite_str)."'";
        if ($entity > 0) $sql.= " AND entity = ".(int)$entity;
        $sql.= " ORDER BY date_fin_contrat ASC";
        if ($limit > 0) $sql.= " LIMIT ".(int)$limit;
        
        $resql = $db->query($sql);
        if (!$resql) {
            dol_syslog("ElaskaContratService::getContratsARenouveler Error ".$db->lasterror(), LOG_ERR);
            return -1;
        }
        
        while ($obj = $db->fetch_object($resql)) {
            $contrat = new ElaskaContratService($db);
            if ($contrat->fetch($obj->rowid) > 0) {
                $contrats[] = $contrat;
            }
        }
        
        $db->free($resql);
        return $contrats;
    }

    // --- Méthodes statiques pour lire les dictionnaires ---
    
    /**
     * Méthode générique pour récupérer les options depuis un dictionnaire
     *
     * @param object $langs                     Objet Translate de Dolibarr
     * @param string $dictionary_table_suffix_short Suffixe court du nom de la table dictionnaire
     * @param bool   $usekeys                   True pour retourner tableau associatif code=>label
     * @param bool   $show_empty                True pour ajouter une option vide
     * @return array                            Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix_short, $usekeys = true, $show_empty = false) {
        global $db;
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        // Le nom de la table est préfixé par llx_c_elaska_contrat_service_
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX;
        
        // Gestion spéciale pour la périodicité qui a une table différente
        if ($dictionary_table_suffix_short == 'periodicite_fact') {
            $sql .= "c_elaska_contrat_periodicite_fact";
        } else {
            $sql .= "c_elaska_contrat_service_".$db->escape($dictionary_table_suffix_short);
        }
        
        $sql .= " WHERE active = 1 ORDER BY position ASC, label ASC";
        
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
     * Récupère les options de types de contrat de service
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getTypeContratServiceOptions($langs, $usekeys = true, $show_empty = false) {
        return self::getOptionsFromDictionary($langs, 'type', $usekeys, $show_empty);
    }

    /**
     * Récupère les options de statuts de contrat de service
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getStatutContratServiceOptions($langs, $usekeys = true, $show_empty = false) {
        return self::getOptionsFromDictionary($langs, 'statut', $usekeys, $show_empty);
    }

    /**
     * Récupère les options de statuts de ligne de contrat
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getStatutLigneOptions($langs, $usekeys = true, $show_empty = false) {
        return self::getOptionsFromDictionary($langs, 'ligne_statut', $usekeys, $show_empty);
    }

    /**
     * Récupère les options de périodicité de facturation
     *
     * @param object $langs      Objet Translate de Dolibarr
     * @param bool   $usekeys    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty True pour ajouter une option vide
     * @return array             Tableau d'options
     */
    public static function getPeriodiciteFacturationOptions($langs, $usekeys = true, $show_empty = false) {
        return self::getOptionsFromDictionary($langs, 'periodicite_fact', $usekeys, $show_empty);
    }
}
}
?>