<?php
/**
 * eLaska - Classe pour gérer les démarches administratives des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
// On charge les classes nécessaires directement au début du fichier
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarche', false)) {

class ElaskaParticulierDemarche extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'demarche@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    //
    // PROPRIÉTÉS DE LA DÉMARCHE
    //
    
    /**
     * @var string Référence unique de la démarche (générée par ElaskaNumero)
     */
    public $ref;
    
    /**
     * @var int ID du particulier lié à cette démarche
     */
    public $fk_particulier;
    
    /**
     * @var string Libellé de la démarche
     */
    public $libelle;
    
    /**
     * @var string Description détaillée de la démarche
     */
    public $description;
    
    /**
     * @var string Code du type de démarche (dictionnaire)
     */
    public $type_demarche_code;
    
    /**
     * @var string Code du statut de la démarche (dictionnaire)
     */
    public $statut_demarche_code;
    
    /**
     * @var string Date d'échéance de la démarche (format YYYY-MM-DD)
     */
    public $date_echeance;
    
    /**
     * @var string Date de clôture de la démarche (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_cloture;
    
    /**
     * @var string Code de la priorité de la démarche (dictionnaire)
     */
    public $priorite_code;
    
    /**
     * @var int Progression en pourcentage (0-100)
     */
    public $progression;
    
    /**
     * @var string Date de la dernière mise à jour de progression (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_derniere_progression;
    
    /**
     * @var string Code de l'organisme concerné (dictionnaire)
     */
    public $organisme_code;
    
    /**
     * @var string Nom de l'organisme si non présent dans le dictionnaire
     */
    public $organisme_nom;
    
    /**
     * @var string Coordonnées de l'organisme
     */
    public $organisme_coordonnees;
    
    /**
     * @var string Code de la catégorie de la démarche (dictionnaire)
     */
    public $categorie_code;
    
    /**
     * @var string Numéro de dossier chez l'organisme
     */
    public $numero_dossier;
    
    /**
     * @var int ID de l'utilisateur assigné à cette démarche
     */
    public $fk_user_assign;
    
    /**
     * @var int ID de l'utilisateur qui a clos la démarche
     */
    public $fk_user_cloture;
    
    /**
     * @var int Budget prévisionnel de la démarche
     */
    public $budget_previsionnel;
    
    /**
     * @var int Budget effectif de la démarche
     */
    public $budget_effectif;
    
    /**
     * @var string Notes et observations sur la démarche
     */
    public $notes;
    
    /**
     * @var string Résultat de la démarche
     */
    public $resultat;
    
    /**
     * @var string Tags associés à la démarche (séparés par des virgules)
     */
    public $tags;
    
    /**
     * @var int ID du document principal lié à cette démarche
     */
    public $fk_document_principal;
    
    /**
     * @var int Indicateur de récurrence (0=non, 1=oui)
     */
    public $recurrence;
    
    /**
     * @var string Code de la fréquence de récurrence (dictionnaire)
     */
    public $recurrence_frequence_code;
    
    /**
     * @var string Date de la prochaine récurrence (format YYYY-MM-DD)
     */
    public $recurrence_prochaine_date;
    
    /**
     * @var int Nombre de récurrences restantes (0=illimité)
     */
    public $recurrence_nombre_restant;
    
    // Champs techniques standard
    public $rowid;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $entity;
    public $status; // État de l'enregistrement (0=inactif, 1=actif)

    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        // Définition des champs pour le gestionnaire d'objets
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'ref' => array('type' => 'varchar(30)', 'label' => 'Ref', 'enabled' => 1, 'position' => 5, 'notnull' => 1, 'visible' => 1, 'index' => 1),
        'fk_particulier' => array('type' => 'integer:ElaskaParticulier:custom/elaska/class/elaska_particulier.class.php', 'label' => 'Particulier', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1),
        'libelle' => array('type' => 'varchar(255)', 'label' => 'Libelle', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'description' => array('type' => 'text', 'label' => 'Description', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'type_demarche_code' => array('type' => 'varchar(50)', 'label' => 'TypeDemarche', 'enabled' => 1, 'position' => 40, 'notnull' => 1, 'visible' => 1),
        'statut_demarche_code' => array('type' => 'varchar(50)', 'label' => 'StatutDemarche', 'enabled' => 1, 'position' => 50, 'notnull' => 1, 'visible' => 1, 'default' => 'A_FAIRE'),
        'date_echeance' => array('type' => 'date', 'label' => 'DateEcheance', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'date_cloture' => array('type' => 'datetime', 'label' => 'DateCloture', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'priorite_code' => array('type' => 'varchar(50)', 'label' => 'Priorite', 'enabled' => 1, 'position' => 80, 'notnull' => 1, 'visible' => 1, 'default' => 'NORMAL'),
        'progression' => array('type' => 'integer', 'label' => 'Progression', 'enabled' => 1, 'position' => 90, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'date_derniere_progression' => array('type' => 'datetime', 'label' => 'DerniereProgression', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'organisme_code' => array('type' => 'varchar(50)', 'label' => 'OrganismeCode', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'organisme_nom' => array('type' => 'varchar(255)', 'label' => 'OrganismeNom', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'organisme_coordonnees' => array('type' => 'text', 'label' => 'OrganismeCoordonnees', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'categorie_code' => array('type' => 'varchar(50)', 'label' => 'Categorie', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'numero_dossier' => array('type' => 'varchar(100)', 'label' => 'NumeroDossier', 'enabled' => 1, 'position' => 150, 'notnull' => 0, 'visible' => 1),
        'fk_user_assign' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAssigned', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        'fk_user_cloture' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserCloture', 'enabled' => 1, 'position' => 170, 'notnull' => 0, 'visible' => 1),
        'budget_previsionnel' => array('type' => 'double(24,8)', 'label' => 'BudgetPrevisionnel', 'enabled' => 1, 'position' => 180, 'notnull' => 0, 'visible' => 1),
        'budget_effectif' => array('type' => 'double(24,8)', 'label' => 'BudgetEffectif', 'enabled' => 1, 'position' => 190, 'notnull' => 0, 'visible' => 1),
        'notes' => array('type' => 'text', 'label' => 'Notes', 'enabled' => 1, 'position' => 200, 'notnull' => 0, 'visible' => 1),
        'resultat' => array('type' => 'text', 'label' => 'Resultat', 'enabled' => 1, 'position' => 210, 'notnull' => 0, 'visible' => 1),
        'tags' => array('type' => 'varchar(255)', 'label' => 'Tags', 'enabled' => 1, 'position' => 220, 'notnull' => 0, 'visible' => 1),
        'fk_document_principal' => array('type' => 'integer', 'label' => 'DocumentPrincipal', 'enabled' => 1, 'position' => 230, 'notnull' => 0, 'visible' => 1),
        'recurrence' => array('type' => 'boolean', 'label' => 'Recurrence', 'enabled' => 1, 'position' => 240, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'recurrence_frequence_code' => array('type' => 'varchar(50)', 'label' => 'RecurrenceFrequence', 'enabled' => 1, 'position' => 250, 'notnull' => 0, 'visible' => 1),
        'recurrence_prochaine_date' => array('type' => 'date', 'label' => 'RecurrenceProchaineDate', 'enabled' => 1, 'position' => 260, 'notnull' => 0, 'visible' => 1),
        'recurrence_nombre_restant' => array('type' => 'integer', 'label' => 'RecurrenceNombreRestant', 'enabled' => 1, 'position' => 270, 'notnull' => 0, 'visible' => 1),
        
        // CHAMPS TECHNIQUES
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'enabled' => 1, 'position' => 900, 'notnull' => 1, 'visible' => 0, 'default' => '1'),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 910, 'notnull' => 1, 'visible' => 0),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 920, 'notnull' => 1, 'visible' => 0),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 930, 'notnull' => 1, 'visible' => 0),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 940, 'notnull' => 0, 'visible' => 0),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 950, 'notnull' => 0, 'visible' => 0),
        'status' => array('type' => 'integer', 'label' => 'Status', 'enabled' => 1, 'position' => 1000, 'notnull' => 1, 'visible' => 1, 'default' => '1'),
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
        
        // Par défaut, la démarche est active et non démarrée
        if (!isset($this->status)) $this->status = 1;
        if (!isset($this->statut_demarche_code)) $this->statut_demarche_code = 'A_FAIRE';
        
        // Valeurs par défaut
        $this->priorite_code = isset($this->priorite_code) ? $this->priorite_code : 'NORMAL';
        $this->progression = isset($this->progression) ? $this->progression : 0;
        $this->recurrence = isset($this->recurrence) ? $this->recurrence : 0;
    }

    /**
     * Crée une démarche dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Vérification des champs obligatoires
        if (empty($this->fk_particulier)) {
            $this->error = 'ParticulierIDIsMandatory';
            return -1;
        }
        
        // Vérification que le particulier existe
        $particulier = new ElaskaParticulier($this->db);
        if ($particulier->fetch($this->fk_particulier) <= 0) {
            $this->error = 'ParticulierDoesNotExist';
            return -1;
        }
        
        if (empty($this->libelle)) {
            $this->error = 'LibelleIsMandatory';
            return -1;
        }
        
        if (empty($this->type_demarche_code)) {
            $this->error = 'TypeDemarcheIsMandatory';
            return -1;
        }
        
        // Valeurs par défaut
        if (empty($this->status)) $this->status = 1;
        if (empty($this->statut_demarche_code)) $this->statut_demarche_code = 'A_FAIRE';
        if (empty($this->priorite_code)) $this->priorite_code = 'NORMAL';
        if (!isset($this->progression)) $this->progression = 0;
        if (!isset($this->recurrence)) $this->recurrence = 0;
        
        $this->fk_user_creat = $user->id;
        
        // Génération de la référence unique
        if (empty($this->ref)) {
            $reference = ElaskaNumero::generateAndRecord($this->db, 'D', $this->element);
            if (empty($reference)) {
                $this->error = 'ErrorGeneratingReference';
                return -1;
            }
            $this->ref = $reference;
        }
        
        // Création dans la base de données
        $result = $this->createCommon($user, $notrigger);
        
        // Si la création est réussie et qu'on doit déclencher les triggers
        if ($result > 0 && !$notrigger) {
            // Ajouter à l'historique du particulier
            $particulier->addHistorique(
                $user,
                'CREATE',
                'demarche',
                $this->id,
                'Création de la démarche : '.$this->libelle.' (Réf: '.$this->ref.')'
            );
        }
        
        return $result;
    }

    /**
     * Charge une démarche depuis la base de données par son ID
     *
     * @param int $id      ID de l'enregistrement à charger
     * @param string $ref  Référence de la démarche
     * @return int         <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = '')
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Met à jour une démarche dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Vérification des champs obligatoires
        if (empty($this->libelle)) {
            $this->error = 'LibelleIsMandatory';
            return -1;
        }
        
        if (empty($this->type_demarche_code)) {
            $this->error = 'TypeDemarcheIsMandatory';
            return -1;
        }
        
        // Vérification que le particulier existe s'il a été modifié
        if ($this->fk_particulier) {
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) <= 0) {
                $this->error = 'ParticulierDoesNotExist';
                return -1;
            }
        }
        
        $this->fk_user_modif = $user->id;
        
        // Mise à jour dans la base de données
        $result = $this->updateCommon($user, $notrigger);
        
        // Si la mise à jour est réussie et qu'on doit déclencher les triggers
        if ($result > 0 && !$notrigger) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    'UPDATE',
                    'demarche',
                    $this->id,
                    'Mise à jour de la démarche : '.$this->libelle.' (Réf: '.$this->ref.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Supprime une démarche de la base de données
     *
     * @param User $user      Utilisateur qui supprime
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        // Sauvegarde d'informations avant suppression pour l'historique
        $libelleDemarche = $this->libelle;
        $refDemarche = $this->ref;
        $idParticulier = $this->fk_particulier;
        
        // Suppression dans la base de données
        $result = $this->deleteCommon($user, $notrigger);
        
        // Si la suppression est réussie et qu'on doit déclencher les triggers
        if ($result > 0 && !$notrigger && $idParticulier > 0) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($idParticulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    'DELETE',
                    'demarche',
                    0,  // ID à 0 car la démarche est supprimée
                    'Suppression de la démarche : '.$libelleDemarche.' (Réf: '.$refDemarche.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Charge toutes les démarches d'un particulier donné
     *
     * @param int    $fk_particulier ID du particulier
     * @param int    $status      Filtre optionnel par statut (0=inactif, 1=actif, -1=tous)
     * @param string $type_code   Filtre optionnel par code de type de démarche
     * @param string $statut_code Filtre optionnel par code de statut de démarche
     * @param string $orderby     Colonnes pour ORDER BY
     * @param int    $limit       Limite de résultats
     * @param int    $offset      Décalage pour pagination
     * @return array              Tableau d'objets ElaskaParticulierDemarche ou tableau vide
     */
    public function fetchAllByParticulier($fk_particulier, $status = 1, $type_code = '', $statut_code = '', $orderby = 'date_echeance ASC', $limit = 0, $offset = 0)
    {
        $demarches = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE fk_particulier = ".(int) $fk_particulier;
        
        if ($status >= 0) {
            $sql.= " AND status = ".(int) $status;
        }
        
        if (!empty($type_code)) {
            $sql.= " AND type_demarche_code = '".$this->db->escape($type_code)."'";
        }
        
        if (!empty($statut_code)) {
            $sql.= " AND statut_demarche_code = '".$this->db->escape($statut_code)."'";
        }
        
        $sql.= " ORDER BY ".$orderby;
        
        if ($limit) {
            if ($offset) {
                $sql.= " LIMIT ".(int) $offset.",".(int) $limit;
            } else {
                $sql.= " LIMIT ".(int) $limit;
            }
        }
        
        $resql = $this->db->query($sql);
        if ($resql) {
            $num = $this->db->num_rows($resql);
            
            if ($num) {
                $i = 0;
                while ($i < $num) {
                    $obj = $this->db->fetch_object($resql);
                    
                    $demarcheTemp = new ElaskaParticulierDemarche($this->db);
                    $demarcheTemp->fetch($obj->rowid);
                    $demarches[] = $demarcheTemp;
                    
                    $i++;
                }
            }
            $this->db->free($resql);
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this)."::fetchAllByParticulier Error ".$this->db->lasterror(), LOG_ERR);
            return -1;
        }
        
        return $demarches;
    }

    /**
     * Change le statut d'une démarche
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau code de statut (A_FAIRE, EN_COURS, TERMINEE, etc.)
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function setStatut($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatusCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_demarche_code;
        $this->statut_demarche_code = $statut_code;
        
        // Mise à jour de la date et de l'utilisateur de clôture si statut final
        $statuts_finaux = array('TERMINEE', 'ANNULEE', 'REFUSEE', 'ABANDONNEE');
        
        if (in_array($statut_code, $statuts_finaux)) {
            $this->date_cloture = dol_now();
            $this->fk_user_cloture = $user->id;
            
            // Ajustement automatique de la progression
            if ($statut_code == 'TERMINEE' && $this->progression < 100) {
                $this->progression = 100;
                $this->date_derniere_progression = dol_now();
            }
        } elseif ($statut_code == 'A_FAIRE') {
            // Réinitialisation si retour au statut initial
            $this->date_cloture = null;
            $this->fk_user_cloture = null;
            
            // Ajustement de la progression
            if ($this->progression > 0) {
                $this->progression = 0;
                $this->date_derniere_progression = dol_now();
            }
        } elseif ($statut_code == 'EN_COURS' && $this->progression == 0) {
            // Démarrage de la démarche
            $this->progression = 10;
            $this->date_derniere_progression = dol_now();
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            if (!empty($this->notes)) {
                $this->notes .= "\n\n" . date('Y-m-d H:i') . " - " . $commentaire;
            } else {
                $this->notes = date('Y-m-d H:i') . " - " . $commentaire;
            }
        }
        
        $result = $this->update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'UPDATE_STATUT';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutDemarcheOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche',
                    $this->id,
                    $message,
                    array('statut_demarche_code' => array($ancien_statut, $statut_code))
                );
            }
            
            // Si cette démarche est récurrente et qu'elle vient d'être terminée, créer la prochaine instance
            if ($this->recurrence && in_array($statut_code, array('TERMINEE')) && !empty($this->recurrence_frequence_code)) {
                $this->creerProchaineRecurrence($user);
            }
        }
        
        return $result;
    }

    /**
     * Met à jour la progression d'une démarche
     *
     * @param User $user        Utilisateur effectuant l'action
     * @param int  $progression Nouvelle valeur de progression (0-100)
     * @param string $commentaire Commentaire optionnel sur la progression
     * @return int              <0 si erreur, >0 si OK
     */
    public function updateProgression($user, $progression, $commentaire = '')
    {
        if ($progression < 0 || $progression > 100) {
            $this->error = 'ProgressionMustBeBetween0And100';
            return -1;
        }
        
        $ancienne_progression = $this->progression;
        $this->progression = $progression;
        $this->date_derniere_progression = dol_now();
        
        // Mise à jour automatique du statut selon la progression
        if ($progression == 100 && $this->statut_demarche_code != 'TERMINEE') {
            $this->statut_demarche_code = 'TERMINEE';
            $this->date_cloture = dol_now();
            $this->fk_user_cloture = $user->id;
        } elseif ($progression > 0 && $progression < 100 && $this->statut_demarche_code == 'A_FAIRE') {
            $this->statut_demarche_code = 'EN_COURS';
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            if (!empty($this->notes)) {
                $this->notes .= "\n\n" . date('Y-m-d H:i') . " - " . $commentaire;
            } else {
                $this->notes = date('Y-m-d H:i') . " - " . $commentaire;
            }
        }
        
        $result = $this->update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'UPDATE_PROGRESSION';
                
                $message = 'Mise à jour de la progression pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.') : '.$ancienne_progression.'% → '.$progression.'%';
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche',
                    $this->id,
                    $message,
                    array('progression' => array($ancienne_progression, $progression))
                );
            }
        }
        
        return $result;
    }

        /**
     * Assigne une démarche à un utilisateur
     *
     * @param User $user       Utilisateur effectuant l'action
     * @param int  $fk_user_assign ID de l'utilisateur à qui assigner la démarche
     * @param string $commentaire Commentaire optionnel sur l'assignation
     * @return int              <0 si erreur, >0 si OK
     */
    public function assignTo($user, $fk_user_assign, $commentaire = '')
    {
        $ancien_assignataire_id = $this->fk_user_assign;
        
        // Vérifier que le nouvel utilisateur existe
        if ($fk_user_assign > 0) {
            require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
            $nouveau_user = new User($this->db);
            if ($nouveau_user->fetch($fk_user_assign) <= 0) {
                $this->error = 'UserDoesNotExist';
                return -1;
            }
        }
        
        $this->fk_user_assign = $fk_user_assign;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            if (!empty($this->notes)) {
                $this->notes .= "\n\n" . date('Y-m-d H:i') . " - " . $commentaire;
            } else {
                $this->notes = date('Y-m-d H:i') . " - " . $commentaire;
            }
        }
        
        $result = $this->update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ASSIGN';
                
                // Récupération des noms des utilisateurs
                $ancien_user = null;
                if ($ancien_assignataire_id > 0) {
                    $ancien_user = new User($this->db);
                    $ancien_user->fetch($ancien_assignataire_id);
                }
                
                $message = 'La démarche "'.$this->libelle.'" (Réf: '.$this->ref.') a été ';
                
                if ($fk_user_assign > 0) {
                    $message .= 'assignée à '.$nouveau_user->getFullName($particulier->langs);
                } else {
                    $message .= 'désassignée';
                }
                
                if ($ancien_user) {
                    $message .= ' (précédemment: '.$ancien_user->getFullName($particulier->langs).')';
                }
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche',
                    $this->id,
                    $message,
                    array('fk_user_assign' => array($ancien_assignataire_id, $fk_user_assign))
                );
            }
        }
        
        return $result;
    }

    /**
     * Configure les paramètres de récurrence d'une démarche
     *
     * @param User   $user                      Utilisateur effectuant l'action
     * @param int    $recurrence                Activer/désactiver la récurrence (0=non, 1=oui)
     * @param string $frequence_code            Code de fréquence (MENSUEL, TRIMESTRIEL, ANNUEL, etc.)
     * @param string $prochaine_date            Date de la prochaine récurrence (format YYYY-MM-DD)
     * @param int    $nombre_restant            Nombre de récurrences restantes (0=illimité)
     * @return int                              <0 si erreur, >0 si OK
     */
    public function configureRecurrence($user, $recurrence, $frequence_code = '', $prochaine_date = '', $nombre_restant = 0)
    {
        $this->recurrence = $recurrence ? 1 : 0;
        
        if ($this->recurrence) {
            // Vérification des valeurs obligatoires pour la récurrence
            if (empty($frequence_code)) {
                $this->error = 'FrequenceCodeRequiredForRecurrence';
                return -1;
            }
            
            // Valider le code de fréquence
            $frequences_valides = self::getFrequencesValides();
            if (!in_array($frequence_code, $frequences_valides)) {
                $this->error = 'InvalidFrequencyCode';
                return -1;
            }
            
            $this->recurrence_frequence_code = $frequence_code;
            
            // Déterminer la prochaine date si non spécifiée
            if (empty($prochaine_date)) {
                $this->recurrence_prochaine_date = $this->calculerProchaineDate($frequence_code);
            } else {
                $this->recurrence_prochaine_date = $prochaine_date;
            }
            
            $this->recurrence_nombre_restant = $nombre_restant;
        } else {
            // Réinitialiser les valeurs de récurrence
            $this->recurrence_frequence_code = null;
            $this->recurrence_prochaine_date = null;
            $this->recurrence_nombre_restant = null;
        }
        
        $result = $this->update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_RECURRENCE';
                
                if ($this->recurrence) {
                    $message = 'Configuration de la récurrence pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.') : ';
                    $message .= 'Fréquence = '.$this->recurrence_frequence_code.', ';
                    $message .= 'Prochaine date = '.dol_print_date($this->db->jdate($this->recurrence_prochaine_date), 'day');
                    if ($this->recurrence_nombre_restant > 0) {
                        $message .= ', Nombre restant = '.$this->recurrence_nombre_restant;
                    } else {
                        $message .= ', Sans limite';
                    }
                } else {
                    $message = 'Désactivation de la récurrence pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.'")';
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche',
                    $this->id,
                    $message,
                    array('recurrence' => array(!$this->recurrence, $this->recurrence))
                );
            }
        }
        
        return $result;
    }

    /**
     * Calcule la date de la prochaine occurrence en fonction de la fréquence
     *
     * @param string $frequence_code Code de fréquence (MENSUEL, TRIMESTRIEL, ANNUEL, etc.)
     * @param string $date_reference Date de référence (format YYYY-MM-DD), si vide utilise la date d'échéance ou la date actuelle
     * @return string                Date calculée (format YYYY-MM-DD)
     */
    public function calculerProchaineDate($frequence_code, $date_reference = '')
    {
        if (empty($date_reference)) {
            // Utiliser la date d'échéance si disponible, sinon la date actuelle
            $date_reference = !empty($this->date_echeance) ? $this->date_echeance : dol_now();
        }
        
        try {
            $date = new DateTime($date_reference);
            
            switch ($frequence_code) {
                case 'QUOTIDIEN':
                    $date->add(new DateInterval('P1D')); // +1 jour
                    break;
                case 'HEBDOMADAIRE':
                    $date->add(new DateInterval('P1W')); // +1 semaine
                    break;
                case 'BIMENSUEL':
                    $date->add(new DateInterval('P2W')); // +2 semaines
                    break;
                case 'MENSUEL':
                    $date->add(new DateInterval('P1M')); // +1 mois
                    break;
                case 'BIMESTRIEL':
                    $date->add(new DateInterval('P2M')); // +2 mois
                    break;
                case 'TRIMESTRIEL':
                    $date->add(new DateInterval('P3M')); // +3 mois
                    break;
                case 'QUADRIMESTRIEL':
                    $date->add(new DateInterval('P4M')); // +4 mois
                    break;
                case 'SEMESTRIEL':
                    $date->add(new DateInterval('P6M')); // +6 mois
                    break;
                case 'ANNUEL':
                    $date->add(new DateInterval('P1Y')); // +1 an
                    break;
                case 'BIANNUEL':
                    $date->add(new DateInterval('P2Y')); // +2 ans
                    break;
                default:
                    $date->add(new DateInterval('P1M')); // Par défaut +1 mois
                    break;
            }
            
            return $date->format('Y-m-d');
        } catch (Exception $e) {
            dol_syslog(get_class($this)."::calculerProchaineDate Error: ".$e->getMessage(), LOG_ERR);
            return date('Y-m-d', strtotime('+1 month')); // Par défaut +1 mois en cas d'erreur
        }
    }

    /**
     * Crée la prochaine instance d'une démarche récurrente
     *
     * @param User $user Utilisateur effectuant l'action
     * @return int       ID de la nouvelle démarche ou <0 si erreur
     */
    public function creerProchaineRecurrence($user)
    {
        // Vérifier que cette démarche est bien récurrente
        if (!$this->recurrence || empty($this->recurrence_frequence_code)) {
            $this->error = 'NotARecurrentDemarche';
            return -1;
        }
        
        // Vérifier s'il reste des récurrences à créer
        if ($this->recurrence_nombre_restant > 0) {
            // Décrémenter le nombre restant pour cette démarche
            $this->recurrence_nombre_restant--;
            $this->update($user, 1); // Mise à jour silencieuse
            
            // Si c'était la dernière récurrence, ne pas créer de nouvelle instance
            if ($this->recurrence_nombre_restant <= 0) {
                return 0;
            }
        }
        
        // Créer une nouvelle instance de démarche
        $nouvelleDemarche = new ElaskaParticulierDemarche($this->db);
        
        // Copier les attributs pertinents
        $nouvelleDemarche->fk_particulier = $this->fk_particulier;
        $nouvelleDemarche->libelle = $this->libelle;
        $nouvelleDemarche->description = $this->description;
        $nouvelleDemarche->type_demarche_code = $this->type_demarche_code;
        $nouvelleDemarche->statut_demarche_code = 'A_FAIRE';
        $nouvelleDemarche->date_echeance = $this->recurrence_prochaine_date;
        $nouvelleDemarche->priorite_code = $this->priorite_code;
        $nouvelleDemarche->progression = 0;
        $nouvelleDemarche->organisme_code = $this->organisme_code;
        $nouvelleDemarche->organisme_nom = $this->organisme_nom;
        $nouvelleDemarche->organisme_coordonnees = $this->organisme_coordonnees;
        $nouvelleDemarche->categorie_code = $this->categorie_code;
        $nouvelleDemarche->fk_user_assign = $this->fk_user_assign;
        $nouvelleDemarche->budget_previsionnel = $this->budget_previsionnel;
        $nouvelleDemarche->tags = $this->tags;
        
        // Configurer la récurrence pour la nouvelle démarche
        $nouvelleDemarche->recurrence = 1;
        $nouvelleDemarche->recurrence_frequence_code = $this->recurrence_frequence_code;
        $nouvelleDemarche->recurrence_prochaine_date = $this->calculerProchaineDate(
            $this->recurrence_frequence_code, 
            $this->recurrence_prochaine_date
        );
        $nouvelleDemarche->recurrence_nombre_restant = $this->recurrence_nombre_restant;
        
        // Créer la nouvelle démarche
        $result = $nouvelleDemarche->create($user);
        
        if ($result > 0) {
            // Ajouter une note supplémentaire pour indiquer que c'est une récurrence
            $nouvelleDemarche->notes = date('Y-m-d H:i') . " - Démarche créée automatiquement comme récurrence de la démarche ".$this->ref;
            $nouvelleDemarche->update($user, 1); // Mise à jour silencieuse
            
            // Mettre à jour l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    'CREATE_RECURRENCE',
                    'demarche',
                    $nouvelleDemarche->id,
                    'Création automatique de la démarche récurrente "'.$nouvelleDemarche->libelle.'" (Réf: '.$nouvelleDemarche->ref.') à partir de la démarche '.$this->ref
                );
            }
        }
        
        return $result;
    }

    /**
     * Vérifie si la démarche est en retard
     * 
     * @return bool true si en retard, false sinon
     */
    public function isEnRetard()
    {
        // Si pas de date d'échéance, démarche terminée ou inactive, elle n'est pas en retard
        $statuts_termines = array('TERMINEE', 'ANNULEE', 'REFUSEE', 'ABANDONNEE');
        
        if (empty($this->date_echeance) || in_array($this->statut_demarche_code, $statuts_termines) || $this->status == 0) {
            return false;
        }
        
        try {
            $dateEcheance = new DateTime($this->date_echeance);
            $dateActuelle = new DateTime();
            
            // Démarche en retard si la date est dépassée et non terminée
            if ($dateActuelle > $dateEcheance) {
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            dol_syslog('Error in ElaskaParticulierDemarche::isEnRetard: '.$e->getMessage(), LOG_ERR);
            return false;
        }
    }

    /**
     * Récupère le nombre de jours avant/après l'échéance
     * 
     * @return int Nombre de jours (négatif si dépassé)
     */
    public function getNombreJoursAvantEcheance()
    {
        if (empty($this->date_echeance)) {
            return null;
        }
        
        try {
            $dateEcheance = new DateTime($this->date_echeance);
            $dateActuelle = new DateTime();
            
            $diff = $dateEcheance->diff($dateActuelle);
            $jours = $diff->days;
            
            // Si la date est passée, retourner un nombre négatif
            if ($dateActuelle > $dateEcheance) {
                $jours = -$jours;
            }
            
            return $jours;
        } catch (Exception $e) {
            dol_syslog('Error in ElaskaParticulierDemarche::getNombreJoursAvantEcheance: '.$e->getMessage(), LOG_ERR);
            return null;
        }
    }

    /**
     * Récupère les démarches à venir/en retard pour un particulier
     * 
     * @param int    $fk_particulier ID du particulier
     * @param int    $jours_horizon  Nombre de jours à l'horizon pour considérer "à venir" (défaut: 30)
     * @param string $type_filtre    Type de filtre: 'retard' pour démarches en retard, 'a_venir' pour à venir, 'all' pour les deux
     * @param int    $limit          Limite de résultats
     * @return array                 Tableau de démarches
     */
    public static function getDemarchesAlerte($db, $fk_particulier, $jours_horizon = 30, $type_filtre = 'all', $limit = 0)
    {
        $demarche = new ElaskaParticulierDemarche($db);
        
        // Récupérer toutes les démarches actives non terminées
        $statuts_termines = array('TERMINEE', 'ANNULEE', 'REFUSEE', 'ABANDONNEE');
        $statuts_sql = '';
        foreach ($statuts_termines as $statut) {
            if (!empty($statuts_sql)) $statuts_sql .= ' AND ';
            $statuts_sql .= "statut_demarche_code != '".$db->escape($statut)."'";
        }
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$demarche->table_element;
        $sql.= " WHERE fk_particulier = ".(int) $fk_particulier;
        $sql.= " AND status = 1";
        if (!empty($statuts_sql)) {
            $sql.= " AND (".$statuts_sql.")";
        }
        
        // Filtre par date d'échéance
        $date_aujourdhui = date('Y-m-d');
        $date_horizon = date('Y-m-d', strtotime('+'.$jours_horizon.' days'));
        
        if ($type_filtre == 'retard') {
            // Uniquement les démarches en retard
            $sql.= " AND date_echeance < '".$db->idate($date_aujourdhui)."'";
        } elseif ($type_filtre == 'a_venir') {
            // Uniquement les démarches à venir dans l'horizon défini
            $sql.= " AND date_echeance >= '".$db->idate($date_aujourdhui)."'";
            $sql.= " AND date_echeance <= '".$db->idate($date_horizon)."'";
        } else {
            // Les deux types (retard + à venir)
            $sql.= " AND date_echeance <= '".$db->idate($date_horizon)."'";
        }
        
        // Tri par échéance
        $sql.= " ORDER BY date_echeance ASC";
        
        // Limite
        if ($limit > 0) {
            $sql.= " LIMIT ".(int) $limit;
        }
        
        $demarches = array();
        $resql = $db->query($sql);
        
        if ($resql) {
            $num = $db->num_rows($resql);
            
            if ($num) {
                $i = 0;
                while ($i < $num) {
                    $obj = $db->fetch_object($resql);
                    
                    $demarcheTemp = new ElaskaParticulierDemarche($db);
                    $demarcheTemp->fetch($obj->rowid);
                    $demarches[] = $demarcheTemp;
                    
                    $i++;
                }
            }
            $db->free($resql);
        } else {
            dol_syslog("ElaskaParticulierDemarche::getDemarchesAlerte Error: ".$db->lasterror(), LOG_ERR);
            return array();
        }
        
        return $demarches;
    }

    /**
     * Active ou désactive une démarche
     * 
     * @param User $user   Utilisateur effectuant l'action
     * @param int  $status Nouveau statut (0=inactif, 1=actif)
     * @return int         <0 si erreur, >0 si OK
     */
    public function setStatus($user, $status)
    {
        $this->status = $status;
        
        $result = $this->update($user);
        
        return $result;
    }

    /**
     * Vérifie si la démarche est terminée
     * 
     * @return bool true si la démarche est terminée, false sinon
     */
    public function isTerminee()
    {
        $statuts_termines = array('TERMINEE', 'ANNULEE', 'REFUSEE', 'ABANDONNEE');
        return in_array($this->statut_demarche_code, $statuts_termines);
    }

    /**
     * Récupère l'utilisateur assigné
     * 
     * @return User|null Utilisateur ou null si non trouvé
     */
    public function getUserAssigned()
    {
        if (empty($this->fk_user_assign)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
        $user = new User($this->db);
        $result = $user->fetch($this->fk_user_assign);
        
        if ($result > 0) {
            return $user;
        }
        
        return null;
    }

    /**
     * Récupère l'utilisateur qui a clôturé la démarche
     * 
     * @return User|null Utilisateur ou null si non trouvé
     */
    public function getUserCloture()
    {
        if (empty($this->fk_user_cloture)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
        $user = new User($this->db);
        $result = $user->fetch($this->fk_user_cloture);
        
        if ($result > 0) {
            return $user;
        }
        
        return null;
    }

    /**
     * Génère une alerte pour une démarche en fonction de sa date d'échéance
     * 
     * @param int $jours_alerte Nombre de jours avant échéance pour déclencher l'alerte (défaut: 7)
     * @return array            Tableau d'informations sur l'alerte ou null si pas d'alerte nécessaire
     */
    public function genererAlerte($jours_alerte = 7)
    {
        // Pas d'alerte si pas de date d'échéance ou si démarche déjà terminée
        if (empty($this->date_echeance) || $this->isTerminee() || $this->status == 0) {
            return null;
        }
        
        $jours_avant_echeance = $this->getNombreJoursAvantEcheance();
        
        // Si la démarche est en retard
        if ($jours_avant_echeance < 0) {
            return array(
                'type' => 'retard',
                'message' => 'Démarche "'.$this->libelle.'" (Réf: '.$this->ref.') en retard de '.abs($jours_avant_echeance).' jour(s)',
                'jours_avant_echeance' => $jours_avant_echeance,
                'demarche' => $this
            );
        }
        
        // Si la démarche est proche de son échéance
        if ($jours_avant_echeance <= $jours_alerte) {
            return array(
                'type' => 'approche',
                'message' => 'Démarche "'.$this->libelle.'" (Réf: '.$this->ref.') à échéance dans '.$jours_avant_echeance.' jour(s)',
                'jours_avant_echeance' => $jours_avant_echeance,
                'demarche' => $this
            );
        }
        
        return null;
    }

    /**
     * Liste des statuts valides pour les démarches
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsValides()
    {
        return array('A_FAIRE', 'EN_COURS', 'EN_ATTENTE', 'BLOQUEE', 'TERMINEE', 'ANNULEE', 'REFUSEE', 'ABANDONNEE');
    }

    /**
     * Liste des fréquences valides pour les récurrences
     *
     * @return array Codes des fréquences valides
     */
    public static function getFrequencesValides()
    {
        return array('QUOTIDIEN', 'HEBDOMADAIRE', 'BIMENSUEL', 'MENSUEL', 'BIMESTRIEL', 'TRIMESTRIEL', 'QUADRIMESTRIEL', 'SEMESTRIEL', 'ANNUEL', 'BIANNUEL');
    }

        /**
     * Récupère les options du dictionnaire des statuts de démarche
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutDemarcheOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_demarche', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des priorités
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getPrioriteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'priorite', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des organismes
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getOrganismeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'organisme', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des catégories de démarche
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getCategorieDemarcheOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'categorie_demarche', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des fréquences de récurrence
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getRecurrenceFrequenceOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'recurrence_frequence', $usekeys, $show_empty);
    }

    /**
     * Méthode factorisée pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                 Objet de traduction
     * @param string    $dictionary_table_suffix_short Suffixe court du nom de la table dictionnaire
     * @param bool      $usekeys               True pour retourner tableau associatif code=>label
     * @param bool      $show_empty            True pour ajouter une option vide
     * @return array                           Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix_short, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_part_".$dictionary_table_suffix_short;
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
            // Logguer l'erreur mais ne pas l'afficher à l'utilisateur
            dol_syslog('Error in ElaskaParticulierDemarche::getOptionsFromDictionary: '.$db->lasterror(), LOG_ERR);
            
            // Valeurs par défaut en cas d'erreur pour les statuts de démarche
            if ($dictionary_table_suffix_short == 'statut_demarche') {
                $options = array(
                    'A_FAIRE' => $langs->trans('DemarcheAFaire'),
                    'EN_COURS' => $langs->trans('DemarcheEnCours'),
                    'EN_ATTENTE' => $langs->trans('DemarcheEnAttente'),
                    'BLOQUEE' => $langs->trans('DemarcheBloquee'),
                    'TERMINEE' => $langs->trans('DemarcheTerminee'),
                    'ANNULEE' => $langs->trans('DemarcheAnnulee'),
                    'REFUSEE' => $langs->trans('DemarcheRefusee'),
                    'ABANDONNEE' => $langs->trans('DemarcheAbandonnee')
                );
            }
            // Valeurs par défaut pour les priorités
            elseif ($dictionary_table_suffix_short == 'priorite') {
                $options = array(
                    'BASSE' => $langs->trans('PrioriteBasse'),
                    'NORMAL' => $langs->trans('PrioriteNormale'),
                    'HAUTE' => $langs->trans('PrioriteHaute'),
                    'URGENTE' => $langs->trans('PrioriteUrgente')
                );
            }
            // Valeurs par défaut pour les fréquences de récurrence
            elseif ($dictionary_table_suffix_short == 'recurrence_frequence') {
                $options = array(
                    'QUOTIDIEN' => $langs->trans('FrequenceQuotidienne'),
                    'HEBDOMADAIRE' => $langs->trans('FrequenceHebdomadaire'),
                    'BIMENSUEL' => $langs->trans('FrequenceBimensuelle'),
                    'MENSUEL' => $langs->trans('FrequenceMensuelle'),
                    'BIMESTRIEL' => $langs->trans('FrequenceBimestrielle'),
                    'TRIMESTRIEL' => $langs->trans('FrequenceTrimestrielle'),
                    'QUADRIMESTRIEL' => $langs->trans('FrequenceQuadrimestrielle'),
                    'SEMESTRIEL' => $langs->trans('FrequenceSemestrielle'),
                    'ANNUEL' => $langs->trans('FrequenceAnnuelle'),
                    'BIANNUEL' => $langs->trans('FrequenceBiannuelle')
                );
            }
        }
        
        return $options;
    }

        /**
     * Ajoute ou associe un document à cette démarche
     *
     * @param User   $user       Utilisateur effectuant l'action
     * @param int    $document_id ID du document à associer
     * @param bool   $is_main_doc True pour définir comme document principal
     * @param string $commentaire Commentaire optionnel sur le document
     * @return int                <0 si erreur, >0 si OK
     */
    public function addDocument($user, $document_id, $is_main_doc = false, $commentaire = '')
    {
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
        
        // Vérifier que le document existe
        $document = new ElaskaDocument($this->db);
        if ($document->fetch($document_id) <= 0) {
            $this->error = 'DocumentDoesNotExist';
            return -1;
        }
        
        // Si c'est le document principal, mettre à jour la démarche
        if ($is_main_doc) {
            $ancien_doc_id = $this->fk_document_principal;
            $this->fk_document_principal = $document_id;
            
            $result = $this->update($user, 1); // mise à jour silencieuse
            
            if ($result > 0) {
                // Ajouter à l'historique
                $particulier = new ElaskaParticulier($this->db);
                if ($particulier->fetch($this->fk_particulier) > 0) {
                    $message = 'Document principal "'.$document->libelle.'" (Réf: '.$document->ref.') associé à la démarche "'.$this->libelle.'" (Réf: '.$this->ref.')';
                    
                    if (!empty($commentaire)) {
                        $message .= ' avec le commentaire : '.$commentaire;
                    }
                    
                    $particulier->addHistorique(
                        $user,
                        'ADD_DOCUMENT',
                        'demarche',
                        $this->id,
                        $message,
                        array('fk_document_principal' => array($ancien_doc_id, $document_id))
                    );
                }
            } else {
                return $result;
            }
        } else {
            // Sinon, créer une liaison dans la table de relation
            $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_demarche_document";
            $sql.= " (fk_demarche, fk_document, date_ajout, fk_user_ajout, commentaire)";
            $sql.= " VALUES (".(int)$this->id.", ".(int)$document_id.", '".$this->db->idate(dol_now())."', ";
            $sql.= (int)$user->id.", ".($commentaire ? "'".$this->db->escape($commentaire)."'" : "NULL").")";
            
            $this->db->begin();
            
            $resql = $this->db->query($sql);
            if (!$resql) {
                $this->error = $this->db->lasterror();
                $this->db->rollback();
                return -1;
            }
            
            // Ajouter à l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $message = 'Document "'.$document->libelle.'" (Réf: '.$document->ref.') associé à la démarche "'.$this->libelle.'" (Réf: '.$this->ref.')';
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    'ADD_DOCUMENT',
                    'demarche',
                    $this->id,
                    $message
                );
            }
            
            $this->db->commit();
            return 1;
        }
        
        return 1;
    }

    /**
     * Récupère le document principal de la démarche
     * 
     * @return ElaskaDocument|null Document principal ou null si non trouvé
     */
    public function getMainDocument()
    {
        if (empty($this->fk_document_principal)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
        $document = new ElaskaDocument($this->db);
        
        if ($document->fetch($this->fk_document_principal) > 0) {
            return $document;
        }
        
        return null;
    }

    /**
     * Récupère tous les documents associés à cette démarche
     * 
     * @param bool $include_main_doc Inclure le document principal dans la liste
     * @return array                 Tableau d'objets ElaskaDocument
     */
    public function getDocuments($include_main_doc = true)
    {
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
        $documents = array();
        
        // Ajouter d'abord le document principal s'il existe et qu'on veut l'inclure
        if ($include_main_doc && !empty($this->fk_document_principal)) {
            $document_principal = $this->getMainDocument();
            if ($document_principal) {
                $document_principal->is_main_doc = true; // Ajouter une propriété pour identifier le doc principal
                $documents[] = $document_principal;
            }
        }
        
        // Récupérer les autres documents liés
        $sql = "SELECT d.rowid FROM ".MAIN_DB_PREFIX."elaska_document as d";
        $sql.= " INNER JOIN ".MAIN_DB_PREFIX."elaska_demarche_document as dd ON dd.fk_document = d.rowid";
        $sql.= " WHERE dd.fk_demarche = ".(int) $this->id;
        // Exclure le document principal s'il est déjà dans la liste
        if (!empty($this->fk_document_principal)) {
            $sql.= " AND d.rowid != ".(int) $this->fk_document_principal;
        }
        $sql.= " ORDER BY d.date_creation DESC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $document = new ElaskaDocument($this->db);
                if ($document->fetch($obj->rowid) > 0) {
                    $document->is_main_doc = false;
                    $documents[] = $document;
                }
            }
            $this->db->free($resql);
        } else {
            dol_syslog('Error in ElaskaParticulierDemarche::getDocuments: '.$this->db->lasterror(), LOG_ERR);
        }
        
        return $documents;
    }

    /**
     * Supprime l'association avec un document
     * 
     * @param User $user       Utilisateur effectuant l'action
     * @param int  $document_id ID du document à dissocier
     * @return int             <0 si erreur, >0 si OK
     */
    public function removeDocument($user, $document_id)
    {
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
        
        // Vérifier que le document existe
        $document = new ElaskaDocument($this->db);
        if ($document->fetch($document_id) <= 0) {
            $this->error = 'DocumentDoesNotExist';
            return -1;
        }
        
        // Si c'est le document principal
        if ($this->fk_document_principal == $document_id) {
            $ancien_doc_id = $this->fk_document_principal;
            $this->fk_document_principal = null;
            
            $result = $this->update($user, 1); // mise à jour silencieuse
            
            if ($result > 0) {
                // Ajouter à l'historique
                $particulier = new ElaskaParticulier($this->db);
                if ($particulier->fetch($this->fk_particulier) > 0) {
                    $particulier->addHistorique(
                        $user,
                        'REMOVE_DOCUMENT',
                        'demarche',
                        $this->id,
                        'Document principal "'.$document->libelle.'" (Réf: '.$document->ref.') retiré de la démarche "'.$this->libelle.'" (Réf: '.$this->ref.')',
                        array('fk_document_principal' => array($ancien_doc_id, null))
                    );
                }
            } else {
                return $result;
            }
        }
        
        // Supprimer l'association dans la table de relation (dans tous les cas, pour être sûr)
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_demarche_document";
        $sql.= " WHERE fk_demarche = ".(int)$this->id." AND fk_document = ".(int)$document_id;
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            dol_syslog('Error in ElaskaParticulierDemarche::removeDocument: '.$this->db->lasterror(), LOG_ERR);
            return -1;
        }
        
        // Si ce n'était pas le document principal ou si on a déjà fait l'historique pour le doc principal
        if ($this->fk_document_principal != $document_id) {
            // Ajouter à l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    'REMOVE_DOCUMENT',
                    'demarche',
                    $this->id,
                    'Document "'.$document->libelle.'" (Réf: '.$document->ref.') retiré de la démarche "'.$this->libelle.'" (Réf: '.$this->ref.')'
                );
            }
        }
        
        return 1;
    }
}

} // Fin de la condition if !class_exists
