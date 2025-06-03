<?php
/**
 * eLaska - Classe pour gérer les étapes des objectifs de vie des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
// On charge les classes nécessaires directement au début du fichier
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_objectif.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierObjectifEtape', false)) {

class ElaskaParticulierObjectifEtape extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_objectif_etape';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_objectif_etape';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'task@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    //
    // PROPRIÉTÉS DE L'ÉTAPE
    //
    
    /**
     * @var string Référence unique de l'étape (générée par ElaskaNumero)
     */
    public $ref;
    
    /**
     * @var int ID de l'objectif parent
     */
    public $fk_objectif;
    
    /**
     * @var string Libellé de l'étape
     */
    public $libelle;
    
    /**
     * @var string Description détaillée de l'étape
     */
    public $description;
    
    /**
     * @var string Date cible pour compléter l'étape (format YYYY-MM-DD)
     */
    public $date_etape;
    
    /**
     * @var string Code de priorité de l'étape (dictionnaire)
     */
    public $priorite_etape_code;
    
    /**
     * @var string Code du statut de l'étape (dictionnaire)
     */
    public $statut_etape_code;
    
    /**
     * @var string Date d'achèvement de l'étape (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_completion;
    
    /**
     * @var int ID de l'utilisateur qui a complété l'étape
     */
    public $fk_user_completion;
    
    /**
     * @var int Ordre d'affichage de l'étape
     */
    public $rang;
    
    /**
     * @var string Notes et observations sur l'étape
     */
    public $notes;
    
    /**
     * @var string Prérequis pour cette étape (autres étapes à compléter avant)
     */
    public $prerequis;
    
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
        'fk_objectif' => array('type' => 'integer:ElaskaParticulierObjectif:custom/elaska/class/elaska_particulier_objectif.class.php', 'label' => 'Objectif', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1),
        'libelle' => array('type' => 'varchar(255)', 'label' => 'Libelle', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'description' => array('type' => 'text', 'label' => 'Description', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'date_etape' => array('type' => 'date', 'label' => 'DateEtape', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'priorite_etape_code' => array('type' => 'varchar(50)', 'label' => 'PrioriteEtape', 'enabled' => 1, 'position' => 50, 'notnull' => 1, 'visible' => 1, 'default' => 'NORMAL'),
        'statut_etape_code' => array('type' => 'varchar(50)', 'label' => 'StatutEtape', 'enabled' => 1, 'position' => 60, 'notnull' => 1, 'visible' => 1, 'default' => 'NOT_STARTED'),
        'date_completion' => array('type' => 'datetime', 'label' => 'DateCompletion', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'fk_user_completion' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserCompletion', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'rang' => array('type' => 'integer', 'label' => 'Rang', 'enabled' => 1, 'position' => 90, 'notnull' => 1, 'visible' => 0, 'default' => '0'),
        'notes' => array('type' => 'text', 'label' => 'Notes', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'prerequis' => array('type' => 'varchar(255)', 'label' => 'Prerequis', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        
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
        
        // Par défaut, l'étape est active et non démarrée
        if (!isset($this->status)) $this->status = 1;
        if (!isset($this->statut_etape_code)) $this->statut_etape_code = 'NOT_STARTED';
        
        // Valeurs par défaut
        $this->priorite_etape_code = isset($this->priorite_etape_code) ? $this->priorite_etape_code : 'NORMAL';
        $this->rang = isset($this->rang) ? $this->rang : 0;
    }

    /**
     * Crée une étape dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Vérification des champs obligatoires
        if (empty($this->fk_objectif)) {
            $this->error = 'ObjectifIDIsMandatory';
            return -1;
        }
        
        // Vérification que l'objectif parent existe
        $objectif = new ElaskaParticulierObjectif($this->db);
        if ($objectif->fetch($this->fk_objectif) <= 0) {
            $this->error = 'ParentObjectifDoesNotExist';
            return -1;
        }
        
        if (empty($this->libelle)) {
            $this->error = 'LibelleIsMandatory';
            return -1;
        }
        
        // Valeurs par défaut
        if (empty($this->status)) $this->status = 1;
        if (empty($this->priorite_etape_code)) $this->priorite_etape_code = 'NORMAL';
        if (empty($this->statut_etape_code)) $this->statut_etape_code = 'NOT_STARTED';
        
        // Récupération du rang automatiquement (dernier rang + 10)
        if (empty($this->rang)) {
            $sql = "SELECT MAX(rang) as maxrang FROM ".MAIN_DB_PREFIX.$this->table_element;
            $sql .= " WHERE fk_objectif = ".(int) $this->fk_objectif;
            
            $resql = $this->db->query($sql);
            if ($resql) {
                $obj = $this->db->fetch_object($resql);
                $this->rang = (isset($obj->maxrang) && $obj->maxrang > 0) ? $obj->maxrang + 10 : 10;
                $this->db->free($resql);
            } else {
                $this->rang = 10;
                dol_syslog(get_class($this)."::create Error ".$this->db->lasterror(), LOG_ERR);
            }
        }
        
        $this->fk_user_creat = $user->id;
        
        // Génération de la référence unique
        if (empty($this->ref)) {
            $reference = ElaskaNumero::generateAndRecord($this->db, 'E', $this->element);
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
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier.class.php';
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($objectif->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    'CREATE',
                    'objectif_etape',
                    $this->id,
                    'Création d\'une étape pour l\'objectif "'.$objectif->libelle.'" (Réf: '.$objectif->ref.') : '.$this->libelle.' (Réf: '.$this->ref.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Charge une étape depuis la base de données par son ID
     *
     * @param int $id      ID de l'enregistrement à charger
     * @param string $ref  Référence de l'étape
     * @return int         <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = '')
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Met à jour une étape dans la base de données
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
        
        // Vérification que l'objectif parent existe s'il a été modifié
        if ($this->fk_objectif) {
            $objectif = new ElaskaParticulierObjectif($this->db);
            if ($objectif->fetch($this->fk_objectif) <= 0) {
                $this->error = 'ParentObjectifDoesNotExist';
                return -1;
            }
        }
        
        $this->fk_user_modif = $user->id;
        
        // Mise à jour dans la base de données
        $result = $this->updateCommon($user, $notrigger);
        
        // Si la mise à jour est réussie et qu'on doit déclencher les triggers
        if ($result > 0 && !$notrigger) {
            // Récupération de l'objectif parent et du particulier associé
            $objectif = new ElaskaParticulierObjectif($this->db);
            if ($objectif->fetch($this->fk_objectif) > 0) {
                // Ajouter à l'historique du particulier
                require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier.class.php';
                $particulier = new ElaskaParticulier($this->db);
                if ($particulier->fetch($objectif->fk_particulier) > 0) {
                    $particulier->addHistorique(
                        $user,
                        'UPDATE',
                        'objectif_etape',
                        $this->id,
                        'Mise à jour de l\'étape "'.$this->libelle.'" (Réf: '.$this->ref.') pour l\'objectif "'.$objectif->libelle.'" (Réf: '.$objectif->ref.')'
                    );
                }
            }
        }
        
        return $result;
    }

    /**
     * Supprime une étape de la base de données
     *
     * @param User $user      Utilisateur qui supprime
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        // Sauvegarde d'informations avant suppression pour l'historique
        $libelleEtape = $this->libelle;
        $refEtape = $this->ref;
        $idObjectif = $this->fk_objectif;
        
        // Récupération de l'objectif parent pour l'historique
        $objectif = new ElaskaParticulierObjectif($this->db);
        $objectif->fetch($idObjectif);
        $libelleObjectif = $objectif->libelle;
        $refObjectif = $objectif->ref;
        $idParticulier = $objectif->fk_particulier;
        
        // Suppression dans la base de données
        $result = $this->deleteCommon($user, $notrigger);
        
        // Si la suppression est réussie et qu'on doit déclencher les triggers
        if ($result > 0 && !$notrigger && $idParticulier > 0) {
            // Ajouter à l'historique du particulier
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier.class.php';
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($idParticulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    'DELETE',
                    'objectif_etape',
                    0,  // ID à 0 car l'étape est supprimée
                    'Suppression de l\'étape "'.$libelleEtape.'" (Réf: '.$refEtape.') de l\'objectif "'.$libelleObjectif.'" (Réf: '.$refObjectif.')'
                );
            }
            
            // Recalculer la progression de l'objectif après suppression de l'étape
            $objectif->calculateProgressionFromEtapes($user);
        }
        
        return $result;
    }

    /**
     * Charge toutes les étapes d'un objectif donné
     *
     * @param int    $fk_objectif ID de l'objectif parent
     * @param int    $status      Filtre optionnel par statut (0=inactif, 1=actif, -1=tous)
     * @param string $statut_code Filtre optionnel par code de statut d'étape ('COMPLETED', 'NOT_STARTED', etc.), '' pour tous
     * @param string $orderby     Colonnes pour ORDER BY
     * @return array              Tableau d'objets ElaskaParticulierObjectifEtape ou tableau vide
     */
    public function fetchAllByObjectif($fk_objectif, $status = 1, $statut_code = '', $orderby = 'rang ASC, priorite_etape_code ASC, date_etape ASC')
    {
        $etapes = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE fk_objectif = ".(int) $fk_objectif;
        
        if ($status >= 0) {
            $sql.= " AND status = ".(int) $status;
        }
        
        if (!empty($statut_code)) {
            $sql.= " AND statut_etape_code = '".$this->db->escape($statut_code)."'";
        }
        
        $sql.= " ORDER BY ".$orderby;
        
        $resql = $this->db->query($sql);
        if ($resql) {
            $num = $this->db->num_rows($resql);
            
            if ($num) {
                $i = 0;
                while ($i < $num) {
                    $obj = $this->db->fetch_object($resql);
                    
                    $etapeTemp = new ElaskaParticulierObjectifEtape($this->db);
                    $etapeTemp->fetch($obj->rowid);
                    $etapes[] = $etapeTemp;
                    
                    $i++;
                }
            }
            $this->db->free($resql);
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this)."::fetchAllByObjectif Error ".$this->db->lasterror(), LOG_ERR);
            return -1;
        }
        
        return $etapes;
    }

    /**
     * Change le statut d'une étape
     *
     * @param User   $user       Utilisateur effectuant l'action
     * @param string $statut_code Nouveau code de statut (NOT_STARTED, IN_PROGRESS, COMPLETED, CANCELLED, etc.)
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function setStatut($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = array('NOT_STARTED', 'IN_PROGRESS', 'BLOCKED', 'DELAYED', 'COMPLETED', 'CANCELLED');
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatusCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_etape_code;
        $this->statut_etape_code = $statut_code;
        
        // Mise à jour de la date et de l'utilisateur de complétion si statut COMPLETED
        if ($statut_code == 'COMPLETED') {
            $this->date_completion = dol_now();
            $this->fk_user_completion = $user->id;
        } elseif ($statut_code == 'NOT_STARTED' || $statut_code == 'CANCELLED') {
            $this->date_completion = null;
            $this->fk_user_completion = null;
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
            // Récupération de l'objectif parent et du particulier associé
            $objectif = new ElaskaParticulierObjectif($this->db);
            if ($objectif->fetch($this->fk_objectif) > 0) {
                // Ajouter à l'historique du particulier
                require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier.class.php';
                $particulier = new ElaskaParticulier($this->db);
                if ($particulier->fetch($objectif->fk_particulier) > 0) {
                    $action = 'UPDATE_STATUT';
                    
                    // Obtenir les libellés traduits des statuts
                    $statut_options = self::getStatutEtapeOptions($particulier->langs);
                    $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                    $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                    
                    $message = 'Changement de statut de l\'étape "'.$this->libelle.'" (Réf: '.$this->ref.') de l\'objectif "'.$objectif->libelle.'" (Réf: '.$objectif->ref.'): ' .
                               $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                    
                    if (!empty($commentaire)) {
                        $message .= ' avec le commentaire : '.$commentaire;
                    }
                    
                    $particulier->addHistorique(
                        $user,
                        $action,
                        'objectif_etape',
                        $this->id,
                        $message,
                        array('statut_etape_code' => array($ancien_statut, $statut_code))
                    );
                }
                
                // Recalculer la progression de l'objectif en fonction des étapes
                $objectif->calculateProgressionFromEtapes($user);
            }
        }
        
        return $result;
    }

    /**
     * Marque une étape comme complétée ou non complétée (compatibilité avec l'ancienne API)
     *
     * @param User $user       Utilisateur effectuant l'action
     * @param int  $completion 1=complétée, 0=non complétée
     * @param string $commentaire Commentaire optionnel sur l'achèvement
     * @return int             <0 si erreur, >0 si OK
     */
    public function setCompletion($user, $completion, $commentaire = '')
    {
        if ($completion) {
            return $this->setStatut($user, 'COMPLETED', $commentaire);
        } else {
            return $this->setStatut($user, 'NOT_STARTED', $commentaire);
        }
    }

    /**
     * Modifie l'ordre d'une étape
     *
     * @param User $user    Utilisateur effectuant l'action
     * @param int  $newRang Nouveau rang
     * @return int          <0 si erreur, >0 si OK
     */
    public function changeRang($user, $newRang)
    {
        $this->rang = $newRang;
        return $this->update($user);
    }

    /**
     * Réordonne toutes les étapes d'un objectif
     * 
     * @param DoliDB $db         Instance de la base de données
     * @param User $user         Utilisateur effectuant l'action
     * @param int  $fk_objectif  ID de l'objectif parent
     * @return int               <0 si erreur, >0 si OK
     */
    public static function reorderAll($db, $user, $fk_objectif)
    {
        $etape = new ElaskaParticulierObjectifEtape($db);
        $etapes = $etape->fetchAllByObjectif($fk_objectif, 1, '', 'priorite_etape_code DESC, date_etape ASC');
        
        if (is_array($etapes)) {
            $rang = 10;
            foreach ($etapes as $etape) {
                $etape->changeRang($user, $rang);
                $rang += 10;
            }
            return 1;
        }
        
        return -1;
    }

    /**
     * Vérifie si l'étape peut être complétée (prérequis satisfaits)
     * 
     * @return bool true si tous les prérequis sont satisfaits, false sinon
     */
    public function canBeCompleted()
    {
        // Si pas de prérequis, l'étape peut être complétée
        if (empty($this->prerequis)) {
            return true;
        }
        
        // Vérifier les prérequis (IDs d'étapes séparés par des virgules)
        $prerequisIds = explode(',', $this->prerequis);
        foreach ($prerequisIds as $prerequisId) {
            $prerequisId = trim($prerequisId);
            if (is_numeric($prerequisId) && $prerequisId > 0) {
                $etapePrereq = new ElaskaParticulierObjectifEtape($this->db);
                $result = $etapePrereq->fetch($prerequisId);
                if ($result > 0 && ($etapePrereq->statut_etape_code != 'COMPLETED' || $etapePrereq->status == 0)) {
                    return false; // Au moins un prérequis n'est pas satisfait
                }
            }
        }
        
        return true;
    }

    /**
     * Vérifie si l'étape est en retard
     * 
     * @return bool true si en retard, false sinon
     */
    public function isEnRetard()
    {
        // Si pas de date définie, étape complétée, ou étape inactive, elle n'est pas en retard
        if (empty($this->date_etape) || $this->statut_etape_code == 'COMPLETED' || $this->status == 0) {
            return false;
        }
        
        try {
            $dateEtape = new DateTime($this->date_etape);
            $dateActuelle = new DateTime();
            
            // Étape en retard si la date est dépassée et non complétée
            if ($dateActuelle > $dateEtape && $this->statut_etape_code != 'COMPLETED') {
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            dol_syslog('Error in ElaskaParticulierObjectifEtape::isEnRetard: '.$e->getMessage(), LOG_ERR);
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
        if (empty($this->date_etape)) {
            return null;
        }
        
        try {
            $dateEtape = new DateTime($this->date_etape);
            $dateActuelle = new DateTime();
            
            $diff = $dateEtape->diff($dateActuelle);
            $jours = $diff->days;
            
            // Si la date est passée, retourner un nombre négatif
            if ($dateActuelle > $dateEtape) {
                $jours = -$jours;
            }
            
            return $jours;
        } catch (Exception $e) {
            dol_syslog('Error in ElaskaParticulierObjectifEtape::getNombreJoursAvantEcheance: '.$e->getMessage(), LOG_ERR);
            return null;
        }
    }

    /**
     * Active ou désactive une étape
     * 
     * @param User $user   Utilisateur effectuant l'action
     * @param int  $status Nouveau statut (0=inactif, 1=actif)
     * @return int         <0 si erreur, >0 si OK
     */
    public function setStatus($user, $status)
    {
        $this->status = $status;
        
        $result = $this->update($user);
        
        if ($result > 0) {
            // Récupération de l'objectif parent et du particulier associé
            $objectif = new ElaskaParticulierObjectif($this->db);
            if ($objectif->fetch($this->fk_objectif) > 0) {
                // Recalculer la progression de l'objectif en fonction des étapes
                $objectif->calculateProgressionFromEtapes($user);
            }
        }
        
        return $result;
    }

    /**
     * Récupère l'utilisateur qui a complété l'étape
     * 
     * @return User|null Utilisateur ou null si non trouvé
     */
    public function getUserCompletion()
    {
        if (empty($this->fk_user_completion)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
        $user = new User($this->db);
        $result = $user->fetch($this->fk_user_completion);
        
        if ($result > 0) {
            return $user;
        }
        
        return null;
    }
    
    /**
     * Vérifie si l'étape est complétée
     * 
     * @return bool true si l'étape est complétée, false sinon
     */
    public function isCompleted()
    {
        return ($this->statut_etape_code == 'COMPLETED');
    }

    /**
     * Récupère les options du dictionnaire des statuts d'étapes
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutEtapeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'etape_statut', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des priorités d'étapes
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getPrioriteEtapeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'etape_priorite', $usekeys, $show_empty);
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
            dol_syslog('Error in ElaskaParticulierObjectifEtape::getOptionsFromDictionary: '.$db->lasterror(), LOG_ERR);
            
            // Valeurs par défaut en cas d'erreur pour les statuts d'étape
            if ($dictionary_table_suffix_short == 'etape_statut') {
                $options = array(
                    'NOT_STARTED' => $langs->trans('EtapeNonDemarree'),
                    'IN_PROGRESS' => $langs->trans('EtapeEnCours'),
                    'DELAYED' => $langs->trans('EtapeRetardee'),
                    'BLOCKED' => $langs->trans('EtapeBloquee'),
                    'COMPLETED' => $langs->trans('EtapeTerminee'),
                    'CANCELLED' => $langs->trans('EtapeAnnulee')
                );
            }
            // Valeurs par défaut pour les priorités d'étape
            elseif ($dictionary_table_suffix_short == 'etape_priorite') {
                $options = array(
                    'LOW' => $langs->trans('PrioriteBasse'),
                    'NORMAL' => $langs->trans('PrioriteNormale'),
                    'HIGH' => $langs->trans('PrioriteHaute'),
                    'URGENT' => $langs->trans('PrioriteUrgente')
                );
            }
        }
        
        return $options;
    }
}

} // Fin de la condition if !class_exists
