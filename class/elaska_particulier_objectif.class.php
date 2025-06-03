<?php
/**
 * eLaska - Classe pour gérer les objectifs de vie des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
// On charge ElaskaParticulier directement au début du fichier pour optimisation
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier.class.php';
// Inclusion de la classe de génération de numéros
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierObjectif', false)) {

class ElaskaParticulierObjectif extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_objectif';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_objectif';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'object@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    //
    // PROPRIÉTÉS DE L'OBJECTIF
    //
    
    /**
     * @var string Référence unique de l'objectif (générée par ElaskaNumero)
     */
    public $ref;
    
    /**
     * @var int ID du particulier lié à cet objectif
     */
    public $fk_particulier;
    
    /**
     * @var string Libellé de l'objectif
     */
    public $libelle;
    
    /**
     * @var string Description détaillée de l'objectif
     */
    public $description;
    
    /**
     * @var string Code du type d'objectif (dictionnaire)
     */
    public $type_objectif_code;
    
    /**
     * @var int Priorité de l'objectif (1=faible, 2=moyenne, 3=élevée)
     */
    public $priorite;
    
    /**
     * @var string Date d'échéance de l'objectif (format YYYY-MM-DD)
     */
    public $date_objectif;
    
    /**
     * @var int Progression en pourcentage (0-100)
     */
    public $progression;
    
    /**
     * @var float Montant estimé pour l'objectif (si financier)
     */
    public $montant_estime;
    
    /**
     * @var float Montant épargné/atteint pour l'objectif (si financier)
     */
    public $montant_epargne;
    
    /**
     * @var string Code du statut de réalisation (dictionnaire)
     */
    public $statut_realisation_code;
    
    /**
     * @var string Code de la catégorie de l'objectif (dictionnaire)
     */
    public $categorie_code;
    
    /**
     * @var int Horizon de réalisation en mois
     */
    public $horizon_mois;
    
    /**
     * @var int ID du conseiller référent pour cet objectif
     */
    public $fk_user_conseiller;
    
    /**
     * @var string Date de la dernière mise à jour de progression (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_derniere_progression;
    
    /**
     * @var int Objectif partagé avec conjoint (0=non, 1=oui)
     */
    public $partage_conjoint;
    
    /**
     * @var string Notes et observations sur l'objectif
     */
    public $notes;
    
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
        'type_objectif_code' => array('type' => 'varchar(50)', 'label' => 'TypeObjectif', 'enabled' => 1, 'position' => 40, 'notnull' => 1, 'visible' => 1),
        'priorite' => array('type' => 'integer', 'label' => 'Priorite', 'enabled' => 1, 'position' => 50, 'notnull' => 1, 'visible' => 1, 'default' => '2'),
        'date_objectif' => array('type' => 'date', 'label' => 'DateObjectif', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'progression' => array('type' => 'integer', 'label' => 'Progression', 'enabled' => 1, 'position' => 70, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'montant_estime' => array('type' => 'double(24,8)', 'label' => 'MontantEstime', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'montant_epargne' => array('type' => 'double(24,8)', 'label' => 'MontantEpargne', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'statut_realisation_code' => array('type' => 'varchar(50)', 'label' => 'StatutRealisation', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'categorie_code' => array('type' => 'varchar(50)', 'label' => 'Categorie', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'horizon_mois' => array('type' => 'integer', 'label' => 'HorizonMois', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'fk_user_conseiller' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'Conseiller', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'date_derniere_progression' => array('type' => 'datetime', 'label' => 'DerniereProgression', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'partage_conjoint' => array('type' => 'boolean', 'label' => 'PartageConjoint', 'enabled' => 1, 'position' => 150, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'notes' => array('type' => 'text', 'label' => 'Notes', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        
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
        
        // Par défaut, l'objectif est actif
        if (!isset($this->status)) $this->status = 1;
        
        // Valeurs par défaut
        $this->priorite = isset($this->priorite) ? $this->priorite : 2;
        $this->progression = isset($this->progression) ? $this->progression : 0;
        $this->partage_conjoint = isset($this->partage_conjoint) ? $this->partage_conjoint : 0;
        $this->montant_epargne = isset($this->montant_epargne) ? $this->montant_epargne : 0;
    }

    /**
     * Crée un objectif dans la base de données
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
        
        if (empty($this->libelle)) {
            $this->error = 'LibelleIsMandatory';
            return -1;
        }
        
        if (empty($this->type_objectif_code)) {
            $this->error = 'TypeObjectifIsMandatory';
            return -1;
        }
        
        // Valeurs par défaut
        if (empty($this->status)) $this->status = 1;
        if (empty($this->priorite)) $this->priorite = 2;
        if (!isset($this->progression)) $this->progression = 0;
        
        $this->fk_user_creat = $user->id;
        
        // Génération de la référence unique
        if (empty($this->ref)) {
            $reference = ElaskaNumero::generateAndRecord($this->db, 'O', $this->element);
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
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    'CREATE',
                    'objectif',
                    $this->id,
                    'Création de l\'objectif : '.$this->libelle.' (Réf: '.$this->ref.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Charge un objectif depuis la base de données par son ID
     *
     * @param int $id      ID de l'enregistrement à charger
     * @param string $ref  Référence de l'objectif
     * @return int         <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = '')
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Met à jour un objectif dans la base de données
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
        
        if (empty($this->type_objectif_code)) {
            $this->error = 'TypeObjectifIsMandatory';
            return -1;
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
                    'objectif',
                    $this->id,
                    'Mise à jour de l\'objectif : '.$this->libelle.' (Réf: '.$this->ref.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Supprime un objectif de la base de données
     *
     * @param User $user      Utilisateur qui supprime
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        // Sauvegarde d'informations avant suppression pour l'historique
        $libelleObjectif = $this->libelle;
        $refObjectif = $this->ref;
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
                    'objectif',
                    0,  // ID à 0 car l'objectif est supprimé
                    'Suppression de l\'objectif : '.$libelleObjectif.' (Réf: '.$refObjectif.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Charge les objectifs d'un particulier
     *
     * @param int    $fk_particulier ID du particulier
     * @param int    $status         Filtre optionnel par statut (0=inactif, 1=actif, -1=tous)
     * @param string $type_code      Filtre optionnel par type d'objectif
     * @param string $orderby        Colonnes pour ORDER BY
     * @param int    $limit          Limite de résultats
     * @param int    $offset         Décalage pour pagination
     * @return array                 Tableau d'objets ElaskaParticulierObjectif ou tableau vide si non trouvés
     */
    public function fetchAllByParticulier($fk_particulier, $status = 1, $type_code = '', $orderby = 'priorite DESC, date_objectif ASC', $limit = 0, $offset = 0)
    {
        $objectifs = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE fk_particulier = ".(int) $fk_particulier;
        
        if ($status >= 0) {
            $sql.= " AND status = ".(int) $status;
        }
        if (!empty($type_code)) {
            $sql.= " AND type_objectif_code = '".$this->db->escape($type_code)."'";
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
                    
                    $objectifTemp = new ElaskaParticulierObjectif($this->db);
                    $objectifTemp->fetch($obj->rowid);
                    $objectifs[] = $objectifTemp;
                    
                    $i++;
                }
            }
            $this->db->free($resql);
        } else {
            $this->error = $this->db->lasterror();
            return -1;
        }
        
        return $objectifs;
    }

    /**
     * Met à jour la progression d'un objectif
     *
     * @param User $user         Utilisateur effectuant l'action
     * @param int  $progression  Nouvelle valeur de progression (0-100)
     * @param string $commentaire Commentaire sur la mise à jour de progression
     * @return int               <0 si erreur, >0 si OK
     */
    public function updateProgression($user, $progression, $commentaire = '')
    {
        if ($progression < 0 || $progression > 100) {
            $this->error = 'ProgressionMustBeBetween0And100';
            return -1;
        }
        
        $ancienneProgression = $this->progression;
        $this->progression = $progression;
        $this->date_derniere_progression = dol_now();
        
        // Si on atteint 100%, mise à jour du statut de réalisation
        if ($progression == 100 && $this->statut_realisation_code != 'COMPLETED') {
            $this->statut_realisation_code = 'COMPLETED';
        } elseif ($progression < 100 && $this->statut_realisation_code == 'COMPLETED') {
            $this->statut_realisation_code = 'IN_PROGRESS';
        } elseif ($progression == 0 && $this->statut_realisation_code != 'NOT_STARTED') {
            $this->statut_realisation_code = 'NOT_STARTED';
        } elseif ($progression > 0 && $progression < 100 && $this->statut_realisation_code != 'IN_PROGRESS') {
            $this->statut_realisation_code = 'IN_PROGRESS';
        }
        
        $result = $this->update($user, 1); // notrigger = 1 pour éviter de créer un événement en double
        
        if ($result > 0) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $message = 'Mise à jour de la progression pour l\'objectif "'.$this->libelle.'" (Réf: '.$this->ref.') : '.$ancienneProgression.'% → '.$progression.'%';
                if (!empty($commentaire)) {
                    $message .= ' ('.$commentaire.')';
                }
                
                $particulier->addHistorique(
                    $user,
                    'UPDATE_PROGRESSION',
                    'objectif',
                    $this->id,
                    $message,
                    array('progression' => array($ancienneProgression, $progression))
                );
            }
        }
        
        return $result;
    }

    /**
     * Modifie le statut de réalisation d'un objectif
     *
     * @param User   $user   Utilisateur effectuant l'action
     * @param string $statut Nouveau statut de réalisation
     * @return int           <0 si erreur, >0 si OK
     */
    public function updateStatutRealisation($user, $statut)
    {
        if (!in_array($statut, array('NOT_STARTED', 'IN_PROGRESS', 'DELAYED', 'BLOCKED', 'COMPLETED', 'CANCELLED'))) {
            $this->error = 'InvalidStatusValue';
            return -1;
        }
        
        $ancienStatut = $this->statut_realisation_code;
        $this->statut_realisation_code = $statut;
        
        // Ajustement automatique de la progression selon le statut
        if ($statut == 'COMPLETED' && $this->progression < 100) {
            $this->progression = 100;
            $this->date_derniere_progression = dol_now();
        } elseif ($statut == 'NOT_STARTED' && $this->progression > 0) {
            $this->progression = 0;
            $this->date_derniere_progression = dol_now();
        } elseif ($statut == 'CANCELLED') {
            $this->status = 0; // Désactivation de l'objectif s'il est annulé
        }
        
        $result = $this->update($user, 1); // notrigger = 1 pour éviter de créer un événement en double
        
        if ($result > 0) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    'UPDATE_STATUT',
                    'objectif',
                    $this->id,
                    'Modification du statut de réalisation pour l\'objectif "'.$this->libelle.'" (Réf: '.$this->ref.') : '.$ancienStatut.' → '.$statut,
                    array('statut_realisation' => array($ancienStatut, $statut))
                );
            }
        }
        
        return $result;
    }

    /**
     * Met à jour le montant épargné pour un objectif financier
     *
     * @param User  $user    Utilisateur effectuant l'action
     * @param float $montant Nouveau montant épargné
     * @return int           <0 si erreur, >0 si OK
     */
    public function updateMontantEpargne($user, $montant)
    {
        if ($montant < 0) {
            $this->error = 'MontantCannotBeNegative';
            return -1;
        }
        
        $ancienMontant = $this->montant_epargne;
        $this->montant_epargne = $montant;
        
        // Mise à jour automatique de la progression si montant estimé défini
        if (!empty($this->montant_estime) && $this->montant_estime > 0) {
            $nouvelleProgression = min(100, round(($montant / $this->montant_estime) * 100));
            if ($nouvelleProgression != $this->progression) {
                $this->progression = $nouvelleProgression;
                $this->date_derniere_progression = dol_now();
                
                // Mise à jour du statut selon la progression
                if ($nouvelleProgression == 100) {
                    $this->statut_realisation_code = 'COMPLETED';
                } elseif ($nouvelleProgression > 0) {
                    $this->statut_realisation_code = 'IN_PROGRESS';
                }
            }
        }
        
        $result = $this->update($user, 1); // notrigger = 1 pour éviter de créer un événement en double
        
        if ($result > 0) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    'UPDATE_MONTANT',
                    'objectif',
                    $this->id,
                    'Mise à jour du montant épargné pour l\'objectif financier "'.$this->libelle.'" (Réf: '.$this->ref.') : '.price($ancienMontant).' → '.price($montant),
                    array('montant_epargne' => array($ancienMontant, $montant))
                );
            }
        }
        
        return $result;
    }

    /**
     * Ajoute une étape à cet objectif
     * 
     * @param User   $user     Utilisateur effectuant l'action
     * @param string $libelle  Libellé de l'étape
     * @param string $desc     Description de l'étape
     * @param string $date     Date cible pour l'étape (format YYYY-MM-DD)
     * @param int    $priorite Priorité de l'étape (1-3)
     * @return int             <0 si erreur, ID de l'étape si OK
     */
    public function addEtape($user, $libelle, $desc = '', $date = '', $priorite = 2)
    {
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_objectif_etape.class.php';
        
        $etape = new ElaskaParticulierObjectifEtape($this->db);
        $etape->fk_objectif = $this->id;
        $etape->libelle = $libelle;
        $etape->description = $desc;
        $etape->date_etape = $date;
        $etape->priorite = $priorite;
        $etape->status = 1;
        
        $result = $etape->create($user);
        
        if ($result > 0) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    'CREATE',
                    'objectif_etape',
                    $result,
                    'Ajout d\'une nouvelle étape "'.$libelle.'" à l\'objectif "'.$this->libelle.'" (Réf: '.$this->ref.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Récupère toutes les étapes d'un objectif
     * 
     * @param int $status   Filtre optionnel par statut (0=inactif, 1=actif, -1=tous)
     * @param int $completed Filtre par étapes complétées (0=non complétées, 1=complétées, -1=toutes)
     * @return array        Tableau d'objets ElaskaParticulierObjectifEtape
     */
    public function getEtapes($status = 1, $completed = -1)
    {
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_objectif_etape.class.php';
        
        $etape = new ElaskaParticulierObjectifEtape($this->db);
        return $etape->fetchAllByObjectif($this->id, $status, $completed);
    }

    /**
     * Calcule la progression de l'objectif en fonction de ses étapes
     * 
     * @param User $user Utilisateur effectuant l'action
     * @return int       <0 si erreur, >0 si OK
     */
    public function calculateProgressionFromEtapes($user)
    {
        $etapes = $this->getEtapes(1);
        if (empty($etapes)) {
            return 0; // Pas d'erreur mais pas d'étapes
        }
        
        $total = count($etapes);
        $completed = 0;
        
        foreach ($etapes as $etape) {
            if ($etape->completed) {
                $completed++;
            }
        }
        
        $progression = $total > 0 ? round(($completed / $total) * 100) : 0;
        
        return $this->updateProgression($user, $progression, 'Calculé à partir des étapes');
    }

    /**
     * Retourne le pourcentage de temps écoulé pour la réalisation de cet objectif
     * 
     * @return float Pourcentage du temps écoulé (0-100)
     */
    public function getTempsEcoule()
    {
        if (empty($this->date_creation) || empty($this->date_objectif)) {
            return 0;
        }
        
        try {
            $dateCreation = new DateTime($this->date_creation);
            $dateObjectif = new DateTime($this->date_objectif);
            $dateActuelle = new DateTime();
            
            // Si la date objectif est dépassée
            if ($dateActuelle > $dateObjectif) {
                return 100;
            }
            
            $dureeTotal = $dateCreation->diff($dateObjectif);
            $dureeEcoulee = $dateCreation->diff($dateActuelle);
            
            // Conversion en jours pour le calcul
            $joursDureeTotal = $dureeTotal->days;
            $joursDureeEcoulee = $dureeEcoulee->days;
            
            if ($joursDureeTotal > 0) {
                return min(100, round(($joursDureeEcoulee / $joursDureeTotal) * 100));
            }
            
            return 0;
        } catch (Exception $e) {
            dol_syslog('Error in ElaskaParticulierObjectif::getTempsEcoule: '.$e->getMessage(), LOG_ERR);
            return 0;
        }
    }

    /**
     * Vérifie si l'objectif est en retard
     * 
     * @return bool true si en retard, false sinon
     */
    public function isEnRetard()
    {
        // Si pas de date définie ou si objectif terminé, on considère qu'il n'est pas en retard
        if (empty($this->date_objectif) || $this->progression >= 100) {
            return false;
        }
        
        try {
            $dateObjectif = new DateTime($this->date_objectif);
            $dateActuelle = new DateTime();
            
            // Objectif en retard si la date est dépassée et progression < 100%
            if ($dateActuelle > $dateObjectif && $this->progression < 100) {
                return true;
            }
            
            // Vérification si le temps écoulé est disproportionné par rapport à la progression
            $tempsEcoule = $this->getTempsEcoule();
            if ($tempsEcoule > 75 && $this->progression < 50) {
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            dol_syslog('Error in ElaskaParticulierObjectif::isEnRetard: '.$e->getMessage(), LOG_ERR);
            return false;
        }
    }

    /**
     * Active ou désactive un objectif
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
     * Récupère les options du dictionnaire des types d'objectif
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeObjectifOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_objectif', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts de réalisation
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutRealisationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_realisation', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des catégories d'objectif
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getCategorieObjectifOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'categorie_objectif', $usekeys, $show_empty);
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
            // Remplacé dol_print_error par dol_syslog pour éviter l'affichage à l'utilisateur
            dol_syslog('Error in ElaskaParticulierObjectif::getOptionsFromDictionary: '.$db->lasterror(), LOG_ERR);
        }
        
        return $options;
    }
}

} // Fin de la condition if !class_exists
