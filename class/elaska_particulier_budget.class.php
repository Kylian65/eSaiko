<?php
/**
 * eLaska - Classe pour gérer le suivi budgétaire des particuliers
 * Date: 2025-06-03
 * Version: 2.0 (Version détaillée pour gestion complète du budget)
 * Auteur: Gemini
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_notification.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_dictionary_helper.class.php'; // Pour les dictionnaires

// Classes associées (à développer séparément)
// require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_budget_poste.class.php';
// require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_budget_operation.class.php';
// require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_budget_analyse.class.php';

if (!class_exists('ElaskaParticulierBudget', false)) {

class ElaskaParticulierBudget extends CommonObject
{
    /**
     * @var string Nom de l'élément
     */
    public $element = 'elaska_particulier_budget';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_budget';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'accounting@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;
    
    //
    // PROPRIÉTÉS DU BUDGET
    //
    
    /**
     * @var string Référence unique du budget
     */
    public $ref;
    
    /**
     * @var int ID du particulier lié à ce budget
     */
    public $fk_particulier;
    
    /**
     * @var string Libellé du budget (ex: "Budget Mensuel Juin 2025")
     */
    public $libelle;
    
    /**
     * @var string Type de budget (dictionnaire: MENSUEL, ANNUEL, PROJET, EVENEMENTIEL)
     */
    public $type_budget_code;
    
    /**
     * @var string Date de début (format YYYY-MM-DD)
     */
    public $date_debut;
    
    /**
     * @var string Date de fin (format YYYY-MM-DD)
     */
    public $date_fin;
    
    /**
     * @var double Revenus totaux estimés
     */
    public $revenus_total_estime;

    /**
     * @var double Revenus totaux réels
     */
    public $revenus_total_reel;
    
    /**
     * @var double Dépenses totales estimées
     */
    public $depenses_total_estime;

    /**
     * @var double Dépenses totales réelles
     */
    public $depenses_total_reel;
    
    /**
     * @var double Épargne prévisionnelle
     */
    public $epargne_previsionnelle;

    /**
     * @var double Épargne réelle
     */
    public $epargne_reelle;
    
    /**
     * @var double Reste à vivre estimé
     */
    public $reste_a_vivre_estime;

    /**
     * @var double Reste à vivre réel
     */
    public $reste_a_vivre_reel;
    
    /**
     * @var double Taux d'endettement calculé
     */
    public $taux_endettement;
    
    /**
     * @var string Statut du budget (dictionnaire: PREPARATION, ACTIF, TERMINE, ANALYSE, CLOTURE)
     */
    public $statut_budget_code;
    
    /**
     * @var int ID du budget précédent (si renouvellement)
     */
    public $fk_budget_precedent;
    
    /**
     * @var double Taux d'atteinte des objectifs d'épargne (%)
     */
    public $taux_atteinte_objectifs_epargne;

    /**
     * @var string Notes internes (pour les conseillers)
     */
    public $notes_internes;

    /**
     * @var string Notes client (visibles sur portail)
     */
    public $notes_client_portal;

    /**
     * @var string Historique des actions (format JSON)
     */
    public $historique_actions;

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
        'ref' => array('type' => 'varchar(30)', 'label' => 'Ref', 'enabled' => 1, 'position' => 5, 'notnull' => 1, 'visible' => 1, 'index' => 1),
        'fk_particulier' => array('type' => 'integer:ElaskaParticulier:custom/elaska/class/elaska_particulier.class.php', 'label' => 'Particulier', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1),
        'libelle' => array('type' => 'varchar(255)', 'label' => 'Libelle', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'type_budget_code' => array('type' => 'varchar(50)', 'label' => 'TypeBudget', 'enabled' => 1, 'position' => 30, 'notnull' => 1, 'visible' => 1),
        'date_debut' => array('type' => 'date', 'label' => 'DateDebut', 'enabled' => 1, 'position' => 40, 'notnull' => 1, 'visible' => 1),
        'date_fin' => array('type' => 'date', 'label' => 'DateFin', 'enabled' => 1, 'position' => 50, 'notnull' => 1, 'visible' => 1),
        'revenus_total_estime' => array('type' => 'double(24,8)', 'label' => 'RevenusTotalEstime', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'revenus_total_reel' => array('type' => 'double(24,8)', 'label' => 'RevenusTotalReel', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'depenses_total_estime' => array('type' => 'double(24,8)', 'label' => 'DepensesTotalEstime', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'depenses_total_reel' => array('type' => 'double(24,8)', 'label' => 'DepensesTotalReel', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'epargne_previsionnelle' => array('type' => 'double(24,8)', 'label' => 'EpargnePrevisionnelle', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'epargne_reelle' => array('type' => 'double(24,8)', 'label' => 'EpargneReelle', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'reste_a_vivre_estime' => array('type' => 'double(24,8)', 'label' => 'ResteAVivreEstime', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'reste_a_vivre_reel' => array('type' => 'double(24,8)', 'label' => 'ResteAVivreReel', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'taux_endettement' => array('type' => 'double(5,2)', 'label' => 'TauxEndettement', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'statut_budget_code' => array('type' => 'varchar(50)', 'label' => 'StatutBudget', 'enabled' => 1, 'position' => 150, 'notnull' => 1, 'visible' => 1, 'default' => 'PREPARATION'),
        'fk_budget_precedent' => array('type' => 'integer:ElaskaParticulierBudget:custom/elaska/class/elaska_particulier_budget.class.php', 'label' => 'BudgetPrecedent', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        'taux_atteinte_objectifs_epargne' => array('type' => 'integer', 'label' => 'TauxAtteinteObjectifsEpargne', 'enabled' => 1, 'position' => 170, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'notes_internes' => array('type' => 'text', 'label' => 'NotesInternes', 'enabled' => 1, 'position' => 180, 'notnull' => 0, 'visible' => 0),
        'notes_client_portal' => array('type' => 'text', 'label' => 'NotesClientPortal', 'enabled' => 1, 'position' => 190, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 200, 'notnull' => 0, 'visible' => 1),
        
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
        parent::__construct($db);
        
        // Valeurs par défaut
        if (!isset($this->status)) $this->status = 1;
        if (empty($this->statut_budget_code)) $this->statut_budget_code = self::STATUT_PREPARATION;
        
        $this->revenus_total_estime = isset($this->revenus_total_estime) ? $this->revenus_total_estime : 0;
        $this->revenus_total_reel = isset($this->revenus_total_reel) ? $this->revenus_total_reel : 0;
        $this->depenses_total_estime = isset($this->depenses_total_estime) ? $this->depenses_total_estime : 0;
        $this->depenses_total_reel = isset($this->depenses_total_reel) ? $this->depenses_total_reel : 0;
        $this->epargne_previsionnelle = isset($this->epargne_previsionnelle) ? $this->epargne_previsionnelle : 0;
        $this->epargne_reelle = isset($this->epargne_reelle) ? $this->epargne_reelle : 0;
        $this->reste_a_vivre_estime = isset($this->reste_a_vivre_estime) ? $this->reste_a_vivre_estime : 0;
        $this->reste_a_vivre_reel = isset($this->reste_a_vivre_reel) ? $this->reste_a_vivre_reel : 0;
        $this->taux_endettement = isset($this->taux_endettement) ? $this->taux_endettement : 0;
        $this->taux_atteinte_objectifs_epargne = isset($this->taux_atteinte_objectifs_epargne) ? $this->taux_atteinte_objectifs_epargne : 0;
    }

    /**
     * Crée un budget dans la base de données
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
        if (empty($this->type_budget_code)) {
            $this->error = 'TypeBudgetIsMandatory';
            return -1;
        }
        if (empty($this->date_debut) || empty($this->date_fin)) {
            $this->error = 'DatesDebutFinAreMandatory';
            return -1;
        }
        if ($this->date_debut > $this->date_fin) {
            $this->error = 'DateDebutCannotBeAfterDateFin';
            return -1;
        }
        
        // Vérification que le particulier existe
        $particulier = new ElaskaParticulier($this->db);
        if ($particulier->fetch($this->fk_particulier) <= 0) {
            $this->error = 'ParticulierDoesNotExist';
            return -1;
        }

        // Génération de la référence unique
        if (empty($this->ref)) {
            $params = array();
            $params['TYPE_BUDGET'] = $this->type_budget_code;
            $params['ANNEE'] = date('Y', dol_stringtotime($this->date_debut));
            $reference = ElaskaNumero::generateAndRecord($this->db, 'BUD', $this->element, 0, '', $params);
            if (empty($reference)) {
                $this->error = 'ErrorGeneratingReference';
                return -1;
            }
            $this->ref = $reference;
        }
        
        $result = $this->createCommon($user, $notrigger);
        
        if ($result > 0) {
            // Mettre à jour l'ID de l'objet dans ElaskaNumero
            ElaskaNumero::recordUsedNumber($this->db, $this->ref, 'BUD', $this->element, $this->id, $this->entity);

            // Ajouter à l'historique du particulier
            $particulier->addHistorique(
                $user,
                self::ACTION_CREATE,
                'budget',
                $this->id,
                'Création du budget : '.$this->libelle.' (Réf: '.$this->ref.')'
            );
        }
        
        return $result;
    }

    /**
     * Charge un budget depuis la base de données par son ID
     *
     * @param int $id      ID de l'enregistrement à charger
     * @param string $ref  Référence du budget
     * @return int         <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = '')
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Charge le budget "actuel" d'un particulier (le plus récent, actif et non clôturé)
     *
     * @param int $fk_particulier ID du particulier
     * @return ElaskaParticulierBudget|null Le budget actuel ou null si aucun
     */
    public function fetchCurrent($fk_particulier)
    {
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE fk_particulier = ".(int)$fk_particulier;
        $sql.= " AND status = 1"; // Actif
        $sql.= " AND statut_budget_code != '".self::STATUT_CLOTURE."'"; // Non clôturé
        $sql.= " ORDER BY date_fin DESC, date_creation DESC LIMIT 1";

        $resql = $this->db->query($sql);
        if ($resql && $this->db->num_rows($resql) > 0) {
            $obj = $this->db->fetch_object($resql);
            $this->fetch($obj->rowid);
            return $this;
        }
        return null;
    }

    /**
     * Met à jour un budget dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Vérifications de base
        if (empty($this->libelle)) {
            $this->error = 'LibelleIsMandatory';
            return -1;
        }
        if (empty($this->type_budget_code)) {
            $this->error = 'TypeBudgetIsMandatory';
            return -1;
        }
        if (empty($this->date_debut) || empty($this->date_fin)) {
            $this->error = 'DatesDebutFinAreMandatory';
            return -1;
        }
        if ($this->date_debut > $this->date_fin) {
            $this->error = 'DateDebutCannotBeAfterDateFin';
            return -1;
        }
        
        $this->fk_user_modif = $user->id;
        $result = $this->updateCommon($user, $notrigger);
        
        if ($result > 0 && !$notrigger) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    self::ACTION_UPDATE,
                    'budget',
                    $this->id,
                    'Mise à jour du budget : '.$this->libelle.' (Réf: '.$this->ref.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Supprime un budget de la base de données
     *
     * @param User $user      Utilisateur qui supprime
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        // Sauvegarde d'informations avant suppression pour l'historique
        $libelleBudget = $this->libelle;
        $refBudget = $this->ref;
        $idParticulier = $this->fk_particulier;
        
        $result = $this->deleteCommon($user, $notrigger);
        
        if ($result > 0 && !$notrigger && $idParticulier > 0) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($idParticulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    self::ACTION_DELETE,
                    'budget',
                    0, // ID à 0 car le budget est supprimé
                    'Suppression du budget : '.$libelleBudget.' (Réf: '.$refBudget.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Change le statut du budget
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau statut (PREPARATION, ACTIF, TERMINE, ANALYSE, CLOTURE)
     * @param string $commentaire Commentaire optionnel
     * @return int               <0 si erreur, >0 si OK
     */
    public function setStatut($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatutCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_budget_code;
        $this->statut_budget_code = $statut_code;
        
        // Mises à jour automatiques selon le statut
        if ($statut_code == self::STATUT_CLOTURE) {
            $this->status = 0; // Désactiver l'enregistrement si clôturé
        } elseif ($statut_code == self::STATUT_ACTIF && $this->status == 0) {
            $this->status = 1; // Réactiver l'enregistrement
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $this->ajouterActionHistorique($user, self::ACTION_CHANGEMENT_STATUT, $ancien_statut . ' → ' . $statut_code, $commentaire);
        
        $result = $this->update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $message = 'Changement de statut pour le budget "'.$this->libelle.'" (Réf: '.$this->ref.'): '.$ancien_statut.' → '.$statut_code;
                if (!empty($commentaire)) {
                    $message .= ' ('.$commentaire.')';
                }
                $particulier->addHistorique(
                    $user,
                    self::ACTION_CHANGEMENT_STATUT,
                    'budget',
                    $this->id,
                    $message,
                    array('statut_budget_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }

    /**
     * Met à jour les montants estimés du budget
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param double $revenus_estimes       Revenus totaux estimés
     * @param double $depenses_estimees     Dépenses totales estimées
     * @param double $epargne_prevue        Épargne prévisionnelle
     * @param string $commentaire          Commentaire optionnel
     * @return int                         <0 si erreur, >0 si OK
     */
    public function updateMontantsEstimes($user, $revenus_estimes, $depenses_estimees, $epargne_prevue, $commentaire = '')
    {
        $anciens_montants = array(
            'revenus' => $this->revenus_total_estime,
            'depenses' => $this->depenses_total_estime,
            'epargne' => $this->epargne_previsionnelle
        );

        $this->revenus_total_estime = $revenus_estimes;
        $this->depenses_total_estime = $depenses_estimees;
        $this->epargne_previsionnelle = $epargne_prevue;

        // Calcul du reste à vivre estimé
        $this->reste_a_vivre_estime = $this->revenus_total_estime - $this->depenses_total_estime - $this->epargne_previsionnelle;

        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }

        $this->ajouterActionHistorique($user, self::ACTION_UPDATE_ESTIMATIONS, 'Estimations mises à jour', $commentaire);
        
        $result = $this->update($user, 1);
        
        if ($result > 0) {
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    self::ACTION_UPDATE_ESTIMATIONS,
                    'budget',
                    $this->id,
                    'Mise à jour des montants estimés du budget : '.$this->libelle.' (Réf: '.$this->ref.')',
                    array('revenus_estimes' => array($anciens_montants['revenus'], $this->revenus_total_estime))
                );
            }
        }
        return $result;
    }

    /**
     * Met à jour les montants réels du budget
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param double $revenus_reels         Revenus totaux réels
     * @param double $depenses_reelles      Dépenses totales réelles
     * @param double $epargne_reelle        Épargne réelle
     * @param string $commentaire          Commentaire optionnel
     * @return int                         <0 si erreur, >0 si OK
     */
    public function updateMontantsReels($user, $revenus_reels, $depenses_reelles, $epargne_reelle, $commentaire = '')
    {
        $anciens_montants = array(
            'revenus' => $this->revenus_total_reel,
            'depenses' => $this->depenses_total_reel,
            'epargne' => $this->epargne_reelle
        );

        $this->revenus_total_reel = $revenus_reels;
        $this->depenses_total_reel = $depenses_reelles;
        $this->epargne_reelle = $epargne_reelle;

        // Calcul du reste à vivre réel
        $this->reste_a_vivre_reel = $this->revenus_total_reel - $this->depenses_total_reel - $this->epargne_reelle;

        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }

        $this->ajouterActionHistorique($user, self::ACTION_UPDATE_REELS, 'Montants réels mis à jour', $commentaire);
        
        $result = $this->update($user, 1);
        
        if ($result > 0) {
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    self::ACTION_UPDATE_REELS,
                    'budget',
                    $this->id,
                    'Mise à jour des montants réels du budget : '.$this->libelle.' (Réf: '.$this->ref.')',
                    array('revenus_reels' => array($anciens_montants['revenus'], $this->revenus_total_reel))
                );
            }
        }
        return $result;
    }

    /**
     * Calcule le taux d'endettement du particulier pour la période du budget
     * Nécessite les informations sur les revenus et les charges fixes (loyers, crédits)
     *
     * @param User $user Utilisateur effectuant l'action (pour l'historique)
     * @return double Taux d'endettement en pourcentage
     */
    public function calculerTauxEndettement($user)
    {
        // Pour un calcul précis, il faudrait récupérer les charges fixes du particulier
        // (loyers, mensualités de crédits en cours) depuis ElaskaParticulier ou ses démarches/crédits.
        // Pour l'instant, c'est un placeholder qui utilise les dépenses réelles.
        
        if ($this->revenus_total_reel > 0) {
            $this->taux_endettement = round(($this->depenses_total_reel / $this->revenus_total_reel) * 100, 2);
        } else {
            $this->taux_endettement = 0;
        }

        $this->ajouterActionHistorique($user, self::ACTION_CALCUL_TAUX_ENDETTEMENT, 'Taux d\'endettement recalculé: ' . $this->taux_endettement . '%');
        
        return $this->taux_endettement;
    }

    /**
     * Récupère le budget précédent lié à celui-ci
     *
     * @return ElaskaParticulierBudget|null Le budget précédent ou null
     */
    public function getBudgetPrecedent()
    {
        if (empty($this->fk_budget_precedent)) {
            return null;
        }
        $budget_precedent = new ElaskaParticulierBudget($this->db);
        if ($budget_precedent->fetch($this->fk_budget_precedent) > 0) {
            return $budget_precedent;
        }
        return null;
    }

    /**
     * Clôture le budget actuel et propose de créer le suivant
     *
     * @param User $user Utilisateur effectuant l'action
     * @param bool $creer_suivant Si true, tente de créer le budget suivant automatiquement
     * @return int <0 si erreur, >0 si OK
     */
    public function cloturerBudget($user, $creer_suivant = false)
    {
        // Vérifier que le budget n'est pas déjà clôturé
        if ($this->statut_budget_code == self::STATUT_CLOTURE) {
            $this->error = 'BudgetAlreadyClosed';
            return -1;
        }

        $result = $this->setStatut($user, self::STATUT_CLOTURE, 'Budget clôturé.');

        if ($result > 0 && $creer_suivant) {
            $new_budget = $this->creerBudgetSuivant($user);
            if ($new_budget > 0) {
                // Notification ou message à l'utilisateur
                dol_syslog('Next budget created automatically: ID ' . $new_budget, LOG_INFO);
            } else {
                dol_syslog('Failed to create next budget automatically after closure of ' . $this->ref, LOG_WARNING);
            }
        }
        return $result;
    }

    /**
     * Crée automatiquement le budget suivant basé sur la périodicité du budget actuel
     *
     * @param User $user Utilisateur effectuant l'action
     * @return int <0 si erreur, ID du nouveau budget si OK
     */
    public function creerBudgetSuivant($user)
    {
        if ($this->type_budget_code != self::TYPE_MENSUEL && $this->type_budget_code != self::TYPE_ANNUEL) {
            $this->error = 'AutoCreationOnlyForMonthlyOrAnnualBudgets';
            return -1;
        }

        try {
            $new_date_debut = new DateTime($this->date_fin);
            $new_date_debut->modify('+1 day'); // Le lendemain de la fin du budget actuel

            $new_date_fin = clone $new_date_debut;
            if ($this->type_budget_code == self::TYPE_MENSUEL) {
                $new_date_fin->modify('+1 month -1 day');
            } elseif ($this->type_budget_code == self::TYPE_ANNUEL) {
                $new_date_fin->modify('+1 year -1 day');
            }

            $new_libelle = $this->type_budget_code == self::TYPE_MENSUEL ? 
                           'Budget Mensuel ' . $new_date_debut->format('m/Y') :
                           'Budget Annuel ' . $new_date_debut->format('Y');

            $new_budget = new ElaskaParticulierBudget($this->db);
            $new_budget->fk_particulier = $this->fk_particulier;
            $new_budget->libelle = $new_libelle;
            $new_budget->type_budget_code = $this->type_budget_code;
            $new_budget->date_debut = $new_date_debut->format('Y-m-d');
            $new_budget->date_fin = $new_date_fin->format('Y-m-d');
            $new_budget->revenus_total_estime = $this->revenus_total_estime; // Reporter les estimations
            $new_budget->depenses_total_estime = $this->depenses_total_estime;
            $new_budget->epargne_previsionnelle = $this->epargne_previsionnelle;
            $new_budget->reste_a_vivre_estime = $this->reste_a_vivre_estime;
            $new_budget->fk_budget_precedent = $this->id;
            $new_budget->statut_budget_code = self::STATUT_PREPARATION; // Le nouveau budget est en préparation

            $result = $new_budget->create($user);
            if ($result > 0) {
                $this->ajouterActionHistorique($user, self::ACTION_CREATION_SUIVANT, 'Budget suivant créé: ' . $new_budget->ref);
                return $new_budget->id;
            } else {
                $this->error = $new_budget->error;
                return -1;
            }

        } catch (Exception $e) {
            dol_syslog('Error creating next budget: '.$e->getMessage(), LOG_ERR);
            $this->error = 'ErrorCreatingNextBudget';
            return -1;
        }
    }

    /**
     * Récupère tous les budgets d'un particulier
     *
     * @param int    $fk_particulier ID du particulier
     * @param string $type_filtre    Filtre optionnel par type de budget
     * @param string $statut_filtre  Filtre optionnel par statut de budget
     * @param string $orderby        Colonnes pour ORDER BY
     * @param int    $limit          Limite de résultats
     * @param int    $offset         Décalage pour pagination
     * @return array                 Tableau d'objets ElaskaParticulierBudget
     */
    public function fetchAllByParticulier($fk_particulier, $type_filtre = '', $statut_filtre = '', $orderby = 'date_fin DESC', $limit = 0, $offset = 0)
    {
        $budgets = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE fk_particulier = ".(int) $fk_particulier;
        $sql.= " AND entity IN (".getEntity($this->element).")";
        
        if (!empty($type_filtre)) {
            $sql.= " AND type_budget_code = '".$this->db->escape($type_filtre)."'";
        }
        if (!empty($statut_filtre)) {
            $sql.= " AND statut_budget_code = '".$this->db->escape($statut_filtre)."'";
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
                    $budgetTemp = new ElaskaParticulierBudget($this->db);
                    $budgetTemp->fetch($obj->rowid);
                    $budgets[] = $budgetTemp;
                    $i++;
                }
            }
            $this->db->free($resql);
        } else {
            dol_syslog(get_class($this)."::fetchAllByParticulier Error ".$this->db->lasterror(), LOG_ERR);
        }
        
        return $budgets;
    }

    /**
     * Ajoute du contenu aux notes internes avec date et séparateur
     *
     * @param User   $user      Utilisateur effectuant l'action
     * @param string $note      Texte à ajouter
     * @return int              <0 si erreur, >0 si OK
     */
    private function addToNotes($user, $note)
    {
        if (!empty($this->notes_internes)) {
            $this->notes_internes .= "\n\n" . date('Y-m-d H:i') . " - " . $note;
        } else {
            $this->notes_internes = date('Y-m-d H:i') . " - " . $note;
        }
        
        return $this->update($user, 1); // Mise à jour silencieuse
    }

    /**
     * Ajoute une action à l'historique spécifique du budget
     *
     * @param User   $user      Utilisateur effectuant l'action
     * @param string $type      Type d'action (libre ou utiliser les constantes de la classe)
     * @param string $details   Détails de l'action
     * @param string $commentaire Commentaire optionnel
     * @return void
     */
    private function ajouterActionHistorique($user, $type, $details, $commentaire = '')
    {
        $entry = date('Y-m-d H:i') . " - " . $user->getFullName($this->langs) . " - " . $type . " - " . $details;
        
        if (!empty($commentaire)) {
            $entry .= " - Commentaire: " . $commentaire;
        }
        
        if (!empty($this->historique_actions)) {
            $this->historique_actions = $entry . "\n\n" . $this->historique_actions;
        } else {
            $this->historique_actions = $entry;
        }
    }
    
    /**
     * Récupère l'historique des actions formaté
     *
     * @param bool   $html       True pour formater en HTML, false pour texte brut
     * @param int    $limit      Limite du nombre d'entrées à récupérer (0 = toutes)
     * @param string $filter     Filtre sur le type d'action (optionnel)
     * @return string|array      Historique formaté (string) ou tableau d'entrées (array)
     */
    public function getHistoriqueActions($html = false, $limit = 0, $filter = '')
    {
        if (empty($this->historique_actions)) {
            return $html ? '<em>Aucune action enregistrée</em>' : 'Aucune action enregistrée';
        }
        
        $entries = explode("\n\n", $this->historique_actions);
        
        if (!empty($filter)) {
            $filtered_entries = array();
            foreach ($entries as $entry) {
                if (strpos($entry, ' - ' . $filter . ' - ') !== false) {
                    $filtered_entries[] = $entry;
                }
            }
            $entries = $filtered_entries;
        }
        
        if ($limit > 0 && count($entries) > $limit) {
            $entries = array_slice($entries, 0, $limit);
        }
        
        if (!$html && is_array($entries)) {
            $structured_entries = array();
            foreach ($entries as $entry) {
                $parts = explode(' - ', $entry, 4); 
                if (count($parts) >= 3) {
                    $structured_entry = array(
                        'datetime' => $parts[0],
                        'user' => $parts[1],
                        'type' => $parts[2],
                        'details' => isset($parts[3]) ? $parts[3] : ''
                    );
                    $comment_pos = isset($parts[3]) ? strpos($parts[3], ' - Commentaire: ') : false;
                    if ($comment_pos !== false) {
                        $structured_entry['details'] = substr($parts[3], 0, $comment_pos);
                        $structured_entry['comment'] = substr(html_entity_decode($parts[3], ENT_QUOTES), $comment_pos + 15);
                    }
                    $structured_entries[] = $structured_entry;
                }
            }
            return $structured_entries;
        }
        
        if ($html) {
            $html_output = '<div class="historique-actions">';
            foreach ($entries as $entry) {
                $parts = explode(' - ', $entry, 4);
                if (count($parts) >= 3) {
                    $datetime = $parts[0];
                    $user_name = $parts[1];
                    $type = $parts[2];
                    $details = isset($parts[3]) ? $parts[3] : '';
                    
                    $class = '';
                    switch ($type) {
                        case self::ACTION_CHANGEMENT_STATUT: $class = 'bg-info'; break;
                        case self::ACTION_UPDATE_ESTIMATIONS: $class = 'bg-primary'; break;
                        case self::ACTION_UPDATE_REELS: $class = 'bg-success'; break;
                        case self::ACTION_CALCUL_TAUX_ENDETTEMENT: $class = 'bg-warning'; break;
                        case self::ACTION_CREATION_SUIVANT: $class = 'bg-purple'; break;
                        default: $class = '';
                    }
                    
                    $comment_html = '';
                    $comment_pos = strpos($details, ' - Commentaire: ');
                    if ($comment_pos !== false) {
                        $comment = substr($details, $comment_pos + 15);
                        $details = substr($details, 0, $comment_pos);
                        $comment_html = '<div class="historique-comment"><em>' . dol_htmlentities($comment) . '</em></div>';
                    }
                    
                    $html_output .= '<div class="historique-entry '.$class.'">';
                    $html_output .= '<div class="historique-header">';
                    $html_output .= '<span class="historique-date">' . dol_htmlentities($datetime) . '</span> - ';
                    $html_output .= '<span class="historique-user">' . dol_htmlentities($user_name) . '</span> - ';
                    $html_output .= '<span class="historique-type">' . dol_htmlentities($type) . '</span>';
                    $html_output .= '</div>';
                    $html_output .= '<div class="historique-details">' . dol_htmlentities($details) . '</div>';
                    $html_output .= $comment_html;
                    $html_output .= '</div>';
                }
            }
            $html_output .= '</div>';
            return $html_output;
        }
        
        return implode("\n\n", $entries);
    }

    /**
     * Liste des statuts de budget valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsValides()
    {
        return array(
            self::STATUT_PREPARATION,
            self::STATUT_ACTIF,
            self::STATUT_TERMINE,
            self::STATUT_ANALYSE,
            self::STATUT_CLOTURE
        );
    }

    /**
     * Récupère les options du dictionnaire des types de budget
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeBudgetOptions($langs, $usekeys = true, $show_empty = false)
    {
        return ElaskaDictionaryHelper::getDictionaryOptions($langs->db, 'c_elaska_part_budget_type', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts de budget
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutBudgetOptions($langs, $usekeys = true, $show_empty = false)
    {
        return ElaskaDictionaryHelper::getDictionaryOptions($langs->db, 'c_elaska_part_budget_statut', $usekeys, $show_empty);
    }

    /**
     * Définit des constantes pour les types de budget
     */
    const TYPE_MENSUEL = 'MENSUEL';
    const TYPE_ANNUEL = 'ANNUEL';
    const TYPE_PROJET = 'PROJET';
    const TYPE_EVENEMENTIEL = 'EVENEMENTIEL';

    /**
     * Définit des constantes pour les statuts de budget
     */
    const STATUT_PREPARATION = 'PREPARATION';
    const STATUT_ACTIF = 'ACTIF';
    const STATUT_TERMINE = 'TERMINE';
    const STATUT_ANALYSE = 'ANALYSE';
    const STATUT_CLOTURE = 'CLOTURE';

    /**
     * Définit des constantes pour les types d'actions dans l'historique
     */
    const ACTION_CREATE = 'CREATE';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';
    const ACTION_CHANGEMENT_STATUT = 'CHANGEMENT_STATUT';
    const ACTION_UPDATE_ESTIMATIONS = 'UPDATE_ESTIMATIONS';
    const ACTION_UPDATE_REELS = 'UPDATE_REELS';
    const ACTION_CALCUL_TAUX_ENDETTEMENT = 'CALCUL_TAUX_ENDETTEMENT';
    const ACTION_CREATION_SUIVANT = 'CREATION_SUIVANT';
}

} // Fin de la condition if !class_exists
