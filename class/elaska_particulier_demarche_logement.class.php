<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches logement des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 * Dernière modification: 2025-06-03 17:09:22 UTC
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarcheLogement', false)) {

class ElaskaParticulierDemarcheLogement extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_logement';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_logement';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AUX DÉMARCHES LOGEMENT
    //
    
    /**
     * @var string Numéro unique départemental (NUD)
     */
    public $numero_unique;
    
    /**
     * @var string Code du type de démarche logement (dictionnaire)
     */
    public $type_demarche_logement_code;
    
    /**
     * @var string Code du statut de la démarche (dictionnaire)
     */
    public $statut_demarche_logement_code;
    
    /**
     * @var string Code du département de la demande
     */
    public $departement_demande;
    
    /**
     * @var string Date du dépôt de la demande (format YYYY-MM-DD)
     */
    public $date_depot_demande;
    
    /**
     * @var string Date de renouvellement (format YYYY-MM-DD)
     */
    public $date_renouvellement;
    
    /**
     * @var string Date limite de renouvellement (format YYYY-MM-DD)
     */
    public $date_limite_renouvellement;
    
    /**
     * @var string Date de la dernière mise à jour (format YYYY-MM-DD)
     */
    public $date_derniere_maj;
    
    /**
     * @var string Nombre de personnes composant le foyer
     */
    public $nombre_personnes_foyer;
    
    /**
     * @var int Revenu fiscal de référence
     */
    public $revenu_fiscal_reference;
    
    /**
     * @var string Type de logement demandé (T1, T2, T3, etc.)
     */
    public $type_logement_demande;
    
    /**
     * @var int Surface minimale demandée en m²
     */
    public $surface_minimale;
    
    /**
     * @var double Loyer maximum souhaité
     */
    public $loyer_maximum;
    
    /**
     * @var string Communes souhaitées (format JSON)
     */
    public $communes_souhaitees;
    
    /**
     * @var int SIADL requis (0=non, 1=oui)
     */
    public $siadl_requis;
    
    /**
     * @var string Date d'obtention du SIADL (format YYYY-MM-DD)
     */
    public $date_obtention_siadl;
    
    /**
     * @var string Motif de la demande
     */
    public $motif_demande;
    
    /**
     * @var string Commentaires sur la situation
     */
    public $commentaires_situation;
    
    /**
     * @var string Organismes déposés (format JSON)
     */
    public $organismes_deposes;
    
    /**
     * @var string Date de la commission d'attribution (format YYYY-MM-DD)
     */
    public $date_commission_attribution;
    
    /**
     * @var int Proposition acceptée (0=non, 1=oui)
     */
    public $proposition_acceptee;
    
    /**
     * @var string Adresse du logement attribué
     */
    public $adresse_logement_attribue;
    
    /**
     * @var string Type du logement attribué (T1, T2, T3, etc.)
     */
    public $type_logement_attribue;
    
    /**
     * @var int Surface du logement attribué en m²
     */
    public $surface_logement_attribue;
    
    /**
     * @var double Loyer du logement attribué
     */
    public $loyer_logement_attribue;
    
    /**
     * @var double Charges du logement attribué
     */
    public $charges_logement_attribue;
    
    /**
     * @var double Dépôt de garantie du logement
     */
    public $depot_garantie;
    
    /**
     * @var string Bailleur du logement attribué
     */
    public $bailleur_logement;
    
    /**
     * @var string Date d'entrée dans les lieux (format YYYY-MM-DD)
     */
    public $date_entree_lieux;
    
    /**
     * @var int DALO déposé (0=non, 1=oui)
     */
    public $dalo_depose;
    
    /**
     * @var string Date de dépôt du DALO (format YYYY-MM-DD)
     */
    public $date_depot_dalo;
    
    /**
     * @var string Décision de la commission DALO
     */
    public $decision_dalo;
    
    /**
     * @var string Date de la décision DALO (format YYYY-MM-DD)
     */
    public $date_decision_dalo;
    
    /**
     * @var int Demande d'aide FSL (0=non, 1=oui)
     */
    public $demande_fsl;
    
    /**
     * @var double Montant de l'aide FSL demandée
     */
    public $montant_fsl_demande;
    
    /**
     * @var double Montant de l'aide FSL accordée
     */
    public $montant_fsl_accorde;
    
    /**
     * @var int ID du contact référent logement
     */
    public $fk_contact_referent;
    
    /**
     * @var string Historique des actions spécifiques à la démarche logement
     */
    public $historique_actions;
    
    /**
     * @var string Pièces justificatives à fournir (format JSON)
     */
    public $pieces_justificatives;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_logement = array(
        'numero_unique' => array('type' => 'varchar(20)', 'label' => 'NumeroUnique', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'type_demarche_logement_code' => array('type' => 'varchar(50)', 'label' => 'TypeDemarcheLogement', 'enabled' => 1, 'position' => 1110, 'notnull' => 1, 'visible' => 1),
        'statut_demarche_logement_code' => array('type' => 'varchar(50)', 'label' => 'StatutDemarcheLogement', 'enabled' => 1, 'position' => 1120, 'notnull' => 1, 'visible' => 1),
        'departement_demande' => array('type' => 'varchar(3)', 'label' => 'DepartementDemande', 'enabled' => 1, 'position' => 1130, 'notnull' => 1, 'visible' => 1),
        'date_depot_demande' => array('type' => 'date', 'label' => 'DateDepotDemande', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'date_renouvellement' => array('type' => 'date', 'label' => 'DateRenouvellement', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'date_limite_renouvellement' => array('type' => 'date', 'label' => 'DateLimiteRenouvellement', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'date_derniere_maj' => array('type' => 'date', 'label' => 'DateDerniereMaj', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'nombre_personnes_foyer' => array('type' => 'integer', 'label' => 'NombrePersonnesFoyer', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'revenu_fiscal_reference' => array('type' => 'integer', 'label' => 'RevenuFiscalReference', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'type_logement_demande' => array('type' => 'varchar(10)', 'label' => 'TypeLogementDemande', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'surface_minimale' => array('type' => 'integer', 'label' => 'SurfaceMinimale', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'loyer_maximum' => array('type' => 'double(24,8)', 'label' => 'LoyerMaximum', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'communes_souhaitees' => array('type' => 'text', 'label' => 'CommunesSouhaitees', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'siadl_requis' => array('type' => 'boolean', 'label' => 'SIADLRequis', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'date_obtention_siadl' => array('type' => 'date', 'label' => 'DateObtentionSIADL', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'motif_demande' => array('type' => 'varchar(255)', 'label' => 'MotifDemande', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1),
        'commentaires_situation' => array('type' => 'text', 'label' => 'CommentairesSituation', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'organismes_deposes' => array('type' => 'text', 'label' => 'OrganismesDeposes', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'date_commission_attribution' => array('type' => 'date', 'label' => 'DateCommissionAttribution', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1),
        'proposition_acceptee' => array('type' => 'boolean', 'label' => 'PropositionAcceptee', 'enabled' => 1, 'position' => 1300, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'adresse_logement_attribue' => array('type' => 'varchar(255)', 'label' => 'AdresseLogementAttribue', 'enabled' => 1, 'position' => 1310, 'notnull' => 0, 'visible' => 1),
        'type_logement_attribue' => array('type' => 'varchar(10)', 'label' => 'TypeLogementAttribue', 'enabled' => 1, 'position' => 1320, 'notnull' => 0, 'visible' => 1),
        'surface_logement_attribue' => array('type' => 'integer', 'label' => 'SurfaceLogementAttribue', 'enabled' => 1, 'position' => 1330, 'notnull' => 0, 'visible' => 1),
        'loyer_logement_attribue' => array('type' => 'double(24,8)', 'label' => 'LoyerLogementAttribue', 'enabled' => 1, 'position' => 1340, 'notnull' => 0, 'visible' => 1),
        'charges_logement_attribue' => array('type' => 'double(24,8)', 'label' => 'ChargesLogementAttribue', 'enabled' => 1, 'position' => 1350, 'notnull' => 0, 'visible' => 1),
        'depot_garantie' => array('type' => 'double(24,8)', 'label' => 'DepotGarantie', 'enabled' => 1, 'position' => 1360, 'notnull' => 0, 'visible' => 1),
        'bailleur_logement' => array('type' => 'varchar(255)', 'label' => 'BailleurLogement', 'enabled' => 1, 'position' => 1370, 'notnull' => 0, 'visible' => 1),
        'date_entree_lieux' => array('type' => 'date', 'label' => 'DateEntreeLieux', 'enabled' => 1, 'position' => 1380, 'notnull' => 0, 'visible' => 1),
        'dalo_depose' => array('type' => 'boolean', 'label' => 'DALODepose', 'enabled' => 1, 'position' => 1390, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'date_depot_dalo' => array('type' => 'date', 'label' => 'DateDepotDALO', 'enabled' => 1, 'position' => 1400, 'notnull' => 0, 'visible' => 1),
        'decision_dalo' => array('type' => 'varchar(255)', 'label' => 'DecisionDALO', 'enabled' => 1, 'position' => 1410, 'notnull' => 0, 'visible' => 1),
        'date_decision_dalo' => array('type' => 'date', 'label' => 'DateDecisionDALO', 'enabled' => 1, 'position' => 1420, 'notnull' => 0, 'visible' => 1),
        'demande_fsl' => array('type' => 'boolean', 'label' => 'DemandeFSL', 'enabled' => 1, 'position' => 1430, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'montant_fsl_demande' => array('type' => 'double(24,8)', 'label' => 'MontantFSLDemande', 'enabled' => 1, 'position' => 1440, 'notnull' => 0, 'visible' => 1),
        'montant_fsl_accorde' => array('type' => 'double(24,8)', 'label' => 'MontantFSLAccorde', 'enabled' => 1, 'position' => 1450, 'notnull' => 0, 'visible' => 1),
        'fk_contact_referent' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactReferent', 'enabled' => 1, 'position' => 1460, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 1470, 'notnull' => 0, 'visible' => 1),
        'pieces_justificatives' => array('type' => 'text', 'label' => 'PiecesJustificatives', 'enabled' => 1, 'position' => 1480, 'notnull' => 0, 'visible' => 1)
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Fusion des champs spécifiques logement avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_logement);
        
        // Valeurs par défaut spécifiques aux démarches logement
        $this->type_demarche_code = 'LOGEMENT';  // Force le code de type de démarche
        if (!isset($this->statut_demarche_logement_code)) $this->statut_demarche_logement_code = 'A_CONSTITUER'; // Statut par défaut
        if (!isset($this->siadl_requis)) $this->siadl_requis = 0;
        if (!isset($this->proposition_acceptee)) $this->proposition_acceptee = 0;
        if (!isset($this->dalo_depose)) $this->dalo_depose = 0;
        if (!isset($this->demande_fsl)) $this->demande_fsl = 0;
    }

    /**
     * Crée une démarche logement dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à LOGEMENT
        $this->type_demarche_code = 'LOGEMENT';
        
        // Vérifications spécifiques aux démarches logement
        if (empty($this->type_demarche_logement_code)) {
            $this->error = 'TypeDemarcheLogementIsMandatory';
            return -1;
        }
        
        if (empty($this->departement_demande)) {
            $this->error = 'DepartementDemandeIsMandatory';
            return -1;
        }
        
        // Génération automatique du libellé s'il n'est pas fourni
        if (empty($this->libelle)) {
            $this->libelle = $this->getTypeDemarcheLogementLabel();
            if (!empty($this->type_logement_demande)) {
                $this->libelle .= ' ' . $this->type_logement_demande;
            }
            $this->libelle .= ' - Dpt ' . $this->departement_demande;
            if (!empty($this->numero_unique)) {
                $this->libelle .= ' (NUD: ' . $this->numero_unique . ')';
            }
        }
        
        // Initialisation des pièces justificatives
        if (empty($this->pieces_justificatives)) {
            $this->pieces_justificatives = $this->getPiecesJustificativesParDefaut();
        }
        
        // Date de dépôt par défaut = aujourd'hui si non renseignée
        if ($this->type_demarche_logement_code == 'LOGEMENT_SOCIAL' && empty($this->date_depot_demande)) {
            $this->date_depot_demande = date('Y-m-d');
            
            // Calcul automatique de la date limite de renouvellement (un an après le dépôt)
            if (empty($this->date_limite_renouvellement)) {
                $this->date_limite_renouvellement = date('Y-m-d', strtotime('+1 year', strtotime($this->date_depot_demande)));
            }
        }
        
        // Initialisation des communes souhaitées
        if (empty($this->communes_souhaitees) && $this->type_demarche_logement_code == 'LOGEMENT_SOCIAL') {
            $this->communes_souhaitees = json_encode(array());
        }
        
        // Initialisation des organismes déposés
        if (empty($this->organismes_deposes) && $this->type_demarche_logement_code == 'LOGEMENT_SOCIAL') {
            $this->organismes_deposes = json_encode(array());
        }
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'une démarche logement
            $note = "Création d'une démarche logement de type " . $this->getTypeDemarcheLogementLabel();
            $note .= " dans le département " . $this->departement_demande;
            if (!empty($this->numero_unique)) {
                $note .= " (NUD: " . $this->numero_unique . ")";
            }
            $this->addToNotes($user, $note);
        }
        
        return $result;
    }

    /**
     * Ajoute du contenu aux notes avec date et séparateur
     *
     * @param User   $user      Utilisateur effectuant l'action
     * @param string $note      Texte à ajouter
     * @return int              <0 si erreur, >0 si OK
     */
    private function addToNotes($user, $note)
    {
        if (!empty($this->notes)) {
            $this->notes .= "\n\n" . date('Y-m-d H:i') . " - " . $note;
        } else {
            $this->notes = date('Y-m-d H:i') . " - " . $note;
        }
        
        return $this->update($user, 1); // Mise à jour silencieuse
    }

    /**
     * Met à jour une démarche logement dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à LOGEMENT
        $this->type_demarche_code = 'LOGEMENT';
        
        // Vérifications spécifiques aux démarches logement
        if (empty($this->type_demarche_logement_code)) {
            $this->error = 'TypeDemarcheLogementIsMandatory';
            return -1;
        }
        
        if (empty($this->departement_demande)) {
            $this->error = 'DepartementDemandeIsMandatory';
            return -1;
        }
        
        // Vérification de la cohérence des dates
        if (!empty($this->date_depot_demande) && !empty($this->date_limite_renouvellement) && $this->date_limite_renouvellement < $this->date_depot_demande) {
            $this->error = 'DateLimiteRenouvellementCantBeBeforeDateDepotDemande';
            return -1;
        }
        
        // Si une proposition est acceptée mais pas d'adresse, erreur
        if ($this->proposition_acceptee && empty($this->adresse_logement_attribue)) {
            $this->error = 'AdresseLogementAttribueRequiredWhenPropositionAcceptee';
            return -1;
        }
        
        // Si DALO déposé mais pas de date de dépôt, erreur
        if ($this->dalo_depose && empty($this->date_depot_dalo)) {
            $this->error = 'DateDepotDALORequiredWhenDALODepose';
            return -1;
        }
        
        // Si demande FSL mais pas de montant demandé, erreur
        if ($this->demande_fsl && $this->montant_fsl_demande <= 0) {
            $this->error = 'MontantFSLDemandeRequiredWhenDemandeFSL';
            return -1;
        }
        
        // Mise à jour automatique de la date de dernière MAJ
        $this->date_derniere_maj = date('Y-m-d');
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le statut de la démarche logement
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau code de statut
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function changerStatutDemarcheLogement($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsDemarcheLogementValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatutDemarcheLogementCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_demarche_logement_code;
        $this->statut_demarche_logement_code = $statut_code;
        
        // Mises à jour automatiques selon le statut
        switch ($statut_code) {
            case 'DOSSIER_CONSTITUE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 30; // 30% de progression
                break;
                
            case 'DOSSIER_DEPOSE':
                if (empty($this->date_depot_demande)) {
                    $this->date_depot_demande = date('Y-m-d');
                }
                // Calcul automatique de la date limite de renouvellement (un an après le dépôt)
                if (empty($this->date_limite_renouvellement)) {
                    $this->date_limite_renouvellement = date('Y-m-d', strtotime('+1 year', strtotime($this->date_depot_demande)));
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 40; // 40% de progression
                break;
                
            case 'NUMERO_UNIQUE_OBTENU':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 50; // 50% de progression
                break;
                
            case 'EN_INSTRUCTION':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 60; // 60% de progression
                break;
                
            case 'PROPOSITION_RECUE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 70; // 70% de progression
                break;
                
            case 'VISITE_PROGRAMMEE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 80; // 80% de progression
                break;
                
            case 'LOGEMENT_ATTRIBUE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 90; // 90% de progression
                $this->proposition_acceptee = 1; // Proposition acceptée
                break;
                
            case 'BAIL_SIGNE':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'DOSSIER_REFUSE':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'RENOUVELLEMENT_EFFECTUE':
                if (empty($this->date_renouvellement)) {
                    $this->date_renouvellement = date('Y-m-d');
                }
                // Mise à jour de la date limite de renouvellement (un an après)
                $this->date_limite_renouvellement = date('Y-m-d', strtotime('+1 year', strtotime($this->date_renouvellement)));
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 50; // Retour à 50% de progression
                break;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $this->ajouterActionHistorique($user, 'CHANGEMENT_STATUT_DEMARCHE', $ancien_statut . ' → ' . $statut_code, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CHANGEMENT_STATUT_LOGEMENT';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutDemarcheLogementOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche logement "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message,
                    array('statut_demarche_logement_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un dépôt de demande de logement social avec numéro unique
     *
     * @param User   $user                 Utilisateur effectuant l'action
     * @param string $date_depot_demande   Date de dépôt (YYYY-MM-DD)
     * @param string $numero_unique        Numéro unique départemental
     * @param string $commentaire         Commentaire optionnel
     * @return int                         <0 si erreur, >0 si OK
     */
    public function enregistrerDepot($user, $date_depot_demande, $numero_unique = '', $commentaire = '')
    {
        // Vérifications
        if (empty($date_depot_demande)) {
            $this->error = 'DateDepotDemandeObligatoire';
            return -1;
        }
        
        $ancienne_date_depot = $this->date_depot_demande;
        $ancien_numero_unique = $this->numero_unique;
        
        $this->date_depot_demande = $date_depot_demande;
        
        if (!empty($numero_unique)) {
            $this->numero_unique = $numero_unique;
        }
        
        // Calcul automatique de la date limite de renouvellement (un an après le dépôt)
        $this->date_limite_renouvellement = date('Y-m-d', strtotime('+1 year', strtotime($this->date_depot_demande)));
        
        // Mise à jour du statut
        if ($this->statut_demarche_logement_code == 'A_CONSTITUER' || $this->statut_demarche_logement_code == 'DOSSIER_CONSTITUE') {
            $this->statut_demarche_logement_code = 'DOSSIER_DEPOSE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 40;
        }
        
        // Si numéro unique fourni, mise à jour du statut
        if (!empty($numero_unique) && $this->statut_demarche_logement_code == 'DOSSIER_DEPOSE') {
            $this->statut_demarche_logement_code = 'NUMERO_UNIQUE_OBTENU';
            $this->progression = 50;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Dépôt de la demande le " . dol_print_date($this->db->jdate($date_depot_demande), 'day');
        
        if (!empty($numero_unique)) {
            $details .= "; Numéro unique: ".($ancien_numero_unique ?: 'Non défini')." → ".$this->numero_unique;
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_DEPOT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DEPOT_DEMANDE_LOGEMENT';
                
                $message = 'Dépôt de la demande de logement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message,
                    array('date_depot_demande' => array($ancienne_date_depot, $this->date_depot_demande))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un renouvellement de demande de logement social
     *
     * @param User   $user                   Utilisateur effectuant l'action
     * @param string $date_renouvellement    Date de renouvellement (YYYY-MM-DD)
     * @param string $commentaire           Commentaire optionnel
     * @return int                           <0 si erreur, >0 si OK
     */
    public function enregistrerRenouvellement($user, $date_renouvellement, $commentaire = '')
    {
        // Vérifications
        if (empty($date_renouvellement)) {
            $this->error = 'DateRenouvellementObligatoire';
            return -1;
        }
        
        $ancienne_date_renouvellement = $this->date_renouvellement;
        $ancienne_date_limite = $this->date_limite_renouvellement;
        
        $this->date_renouvellement = $date_renouvellement;
        
        // Calcul automatique de la nouvelle date limite de renouvellement (un an après)
        $this->date_limite_renouvellement = date('Y-m-d', strtotime('+1 year', strtotime($this->date_renouvellement)));
        
        // Mise à jour du statut
        if ($this->statut_demarche_logement_code != 'RENOUVELLEMENT_EFFECTUE') {
            $this->statut_demarche_logement_code = 'RENOUVELLEMENT_EFFECTUE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 50;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Renouvellement effectué le " . dol_print_date($this->db->jdate($date_renouvellement), 'day');
        $details .= "; Nouvelle date limite: " . dol_print_date($this->db->jdate($this->date_limite_renouvellement), 'day');
        
        $this->ajouterActionHistorique($user, 'RENOUVELLEMENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'RENOUVELLEMENT_LOGEMENT';
                
                $message = 'Renouvellement de la demande de logement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message,
                    array(
                        'date_renouvellement' => array($ancienne_date_renouvellement, $this->date_renouvellement),
                        'date_limite_renouvellement' => array($ancienne_date_limite, $this->date_limite_renouvellement)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure les critères de logement recherché
     *
     * @param User   $user                    Utilisateur effectuant l'action
     * @param string $type_logement_demande   Type de logement demandé (T1, T2, etc.)
     * @param int    $surface_minimale        Surface minimale en m²
     * @param double $loyer_maximum           Loyer maximum
     * @param array  $communes_souhaitees     Tableau des communes souhaitées
     * @param string $commentaire            Commentaire optionnel
     * @return int                            <0 si erreur, >0 si OK
     */
    public function configurerCriteresLogement($user, $type_logement_demande, $surface_minimale = 0, $loyer_maximum = 0, $communes_souhaitees = array(), $commentaire = '')
    {
        // Vérifications
        if (empty($type_logement_demande)) {
            $this->error = 'TypeLogementDemandeObligatoire';
            return -1;
        }
        
        $ancien_type_logement = $this->type_logement_demande;
        $ancienne_surface = $this->surface_minimale;
        $ancien_loyer = $this->loyer_maximum;
        $anciennes_communes = $this->communes_souhaitees;
        
        $this->type_logement_demande = $type_logement_demande;
        
        if ($surface_minimale > 0) {
            $this->surface_minimale = $surface_minimale;
        }
        
        if ($loyer_maximum > 0) {
            $this->loyer_maximum = $loyer_maximum;
        }
        
        if (!empty($communes_souhaitees)) {
            $this->communes_souhaitees = json_encode($communes_souhaitees);
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Type logement: ".($ancien_type_logement ?: 'Non défini')." → ".$this->type_logement_demande;
        
        if ($surface_minimale > 0) {
            $details .= "; Surface min: ".($ancienne_surface ?: '0')."m² → ".$this->surface_minimale."m²";
        }
        
        if ($loyer_maximum > 0) {
            $details .= "; Loyer max: ".price($ancien_loyer)." → ".price($this->loyer_maximum);
        }
        
        if (!empty($communes_souhaitees)) {
            $details .= "; Communes mises à jour";
        }
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_CRITERES_LOGEMENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_CRITERES_LOGEMENT';
                
                $message = 'Configuration des critères de logement pour "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message,
                    array('type_logement_demande' => array($ancien_type_logement, $this->type_logement_demande))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une proposition de logement
     *
     * @param User   $user                    Utilisateur effectuant l'action
     * @param string $date_commission         Date de la commission d'attribution (YYYY-MM-DD)
     * @param string $adresse_logement        Adresse du logement proposé
     * @param string $type_logement           Type de logement (T1, T2, etc.)
     * @param double $loyer_logement          Montant du loyer
     * @param string $bailleur                Nom du bailleur
     * @param string $commentaire            Commentaire optionnel
     * @return int                            <0 si erreur, >0 si OK
     */
    public function enregistrerProposition($user, $date_commission, $adresse_logement, $type_logement, $loyer_logement, $bailleur = '', $commentaire = '')
    {
        // Vérifications
        if (empty($date_commission)) {
            $this->error = 'DateCommissionObligatoire';
            return -1;
        }
        
        if (empty($adresse_logement)) {
            $this->error = 'AdresseLogementObligatoire';
            return -1;
        }
        
        if (empty($type_logement)) {
            $this->error = 'TypeLogementObligatoire';
            return -1;
        }
        
        if ($loyer_logement <= 0) {
            $this->error = 'LoyerLogementDoitEtrePositif';
            return -1;
        }
        
        $ancienne_date_commission = $this->date_commission_attribution;
        $ancienne_adresse = $this->adresse_logement_attribue;
        $ancien_type = $this->type_logement_attribue;
        $ancien_loyer = $this->loyer_logement_attribue;
        $ancien_bailleur = $this->bailleur_logement;
        
        $this->date_commission_attribution = $date_commission;
        $this->adresse_logement_attribue = $adresse_logement;
        $this->type_logement_attribue = $type_logement;
        $this->loyer_logement_attribue = $loyer_logement;
        
        if (!empty($bailleur)) {
            $this->bailleur_logement = $bailleur;
        }
        
        // Mise à jour du statut si ce n'est pas déjà plus avancé
        if ($this->statut_demarche_logement_code != 'PROPOSITION_RECUE' && 
            $this->statut_demarche_logement_code != 'VISITE_PROGRAMMEE' && 
            $this->statut_demarche_logement_code != 'LOGEMENT_ATTRIBUE' && 
            $this->statut_demarche_logement_code != 'BAIL_SIGNE') {
            $this->statut_demarche_logement_code = 'PROPOSITION_RECUE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 70;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Proposition le " . dol_print_date($this->db->jdate($date_commission), 'day');
        $details .= "; Logement: " . $this->type_logement_attribue . ", " . $this->adresse_logement_attribue;
        $details .= "; Loyer: " . price($this->loyer_logement_attribue);
        
        if (!empty($this->bailleur_logement)) {
            $details .= "; Bailleur: " . $this->bailleur_logement;
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_PROPOSITION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'PROPOSITION_LOGEMENT';
                
                $message = 'Proposition de logement pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message,
                    array(
                        'adresse_logement_attribue' => array($ancienne_adresse, $this->adresse_logement_attribue),
                        'loyer_logement_attribue' => array($ancien_loyer, $this->loyer_logement_attribue)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre l'acceptation d'une proposition et la signature du bail
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param string $date_entree_lieux     Date d'entrée dans les lieux (YYYY-MM-DD)
     * @param double $depot_garantie        Montant du dépôt de garantie
     * @param double $charges               Montant des charges
     * @param string $commentaire          Commentaire optionnel
     * @return int                          <0 si erreur, >0 si OK
     */
    public function enregistrerAcceptation($user, $date_entree_lieux, $depot_garantie = 0, $charges = 0, $commentaire = '')
    {
        // Vérifications
        if (empty($date_entree_lieux)) {
            $this->error = 'DateEntreeLieuxObligatoire';
            return -1;
        }
        
        if (empty($this->adresse_logement_attribue)) {
            $this->error = 'AucunePropositionEnregistree';
            return -1;
        }
        
        $ancienne_date_entree = $this->date_entree_lieux;
        $ancien_depot = $this->depot_garantie;
        $anciennes_charges = $this->charges_logement_attribue;
        $ancienne_proposition_acceptee = $this->proposition_acceptee;
        
        $this->date_entree_lieux = $date_entree_lieux;
        $this->proposition_acceptee = 1;
        
        if ($depot_garantie > 0) {
            $this->depot_garantie = $depot_garantie;
        }
        
        if ($charges > 0) {
            $this->charges_logement_attribue = $charges;
        }
        
        // Mise à jour du statut
        $this->statut_demarche_logement_code = 'BAIL_SIGNE';
        $this->statut_demarche_code = 'TERMINEE';
        $this->progression = 100;
        $this->date_cloture = dol_now();
        $this->fk_user_cloture = $user->id;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Logement accepté";
        $details .= "; Entrée dans les lieux prévue le " . dol_print_date($this->db->jdate($date_entree_lieux), 'day');
        
        if ($depot_garantie > 0) {
            $details .= "; Dépôt de garantie: " . price($this->depot_garantie);
        }
        
        if ($charges > 0) {
            $details .= "; Charges mensuelles: " . price($this->charges_logement_attribue);
        }
        
        $this->ajouterActionHistorique($user, 'ACCEPTATION_LOGEMENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ACCEPTATION_LOGEMENT';
                
                $message = 'Acceptation de logement et signature du bail pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message,
                    array(
                        'date_entree_lieux' => array($ancienne_date_entree, $this->date_entree_lieux),
                        'proposition_acceptee' => array($ancienne_proposition_acceptee, $this->proposition_acceptee)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un refus de proposition
     *
     * @param User   $user            Utilisateur effectuant l'action
     * @param string $motif_refus     Motif du refus
     * @param string $commentaire    Commentaire optionnel
     * @return int                    <0 si erreur, >0 si OK
     */
    public function refuserProposition($user, $motif_refus, $commentaire = '')
    {
        // Vérifications
        if (empty($motif_refus)) {
            $this->error = 'MotifRefusObligatoire';
            return -1;
        }
        
        if (empty($this->adresse_logement_attribue)) {
            $this->error = 'AucunePropositionEnregistree';
            return -1;
        }
        
        // On garde l'adresse et les infos du logement refusé pour traçabilité
        
        // Mise à jour du statut - on retourne à NUMERO_UNIQUE_OBTENU
        $this->statut_demarche_logement_code = 'NUMERO_UNIQUE_OBTENU';
        $this->statut_demarche_code = 'EN_COURS';
        $this->progression = 50;
        $this->proposition_acceptee = 0;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Refus de la proposition de logement: " . $motif_refus;
        $details .= "; Logement refusé: " . $this->type_logement_attribue . ", " . $this->adresse_logement_attribue;
        
        $this->ajouterActionHistorique($user, 'REFUS_PROPOSITION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'REFUS_PROPOSITION_LOGEMENT';
                
                $message = 'Refus de proposition de logement pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un dépôt de recours DALO
     *
     * @param User   $user             Utilisateur effectuant l'action
     * @param string $date_depot_dalo  Date de dépôt du DALO (YYYY-MM-DD)
     * @param string $motif_dalo       Motif du recours DALO
     * @param string $commentaire     Commentaire optionnel
     * @return int                     <0 si erreur, >0 si OK
     */
    public function enregistrerDALO($user, $date_depot_dalo, $motif_dalo, $commentaire = '')
    {
        // Vérifications
        if (empty($date_depot_dalo)) {
            $this->error = 'DateDepotDALOObligatoire';
            return -1;
        }
        
        if (empty($motif_dalo)) {
            $this->error = 'MotifDALOObligatoire';
            return -1;
        }
        
        $ancienne_date_depot = $this->date_depot_dalo;
        $ancien_dalo_depose = $this->dalo_depose;
        
        $this->dalo_depose = 1;
        $this->date_depot_dalo = $date_depot_dalo;
        $this->motif_demande = $motif_dalo; // On utilise le champ motif_demande pour stocker le motif du DALO
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Recours DALO déposé le " . dol_print_date($this->db->jdate($date_depot_dalo), 'day');
        $details .= "; Motif: " . $motif_dalo;
        
        $this->ajouterActionHistorique($user, 'DEPOT_DALO', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DEPOT_DALO_LOGEMENT';
                
                $message = 'Dépôt d\'un recours DALO pour la démarche logement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message,
                    array(
                        'date_depot_dalo' => array($ancienne_date_depot, $this->date_depot_dalo),
                        'dalo_depose' => array($ancien_dalo_depose, $this->dalo_depose)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une décision DALO
     *
     * @param User   $user                 Utilisateur effectuant l'action
     * @param string $date_decision_dalo   Date de la décision (YYYY-MM-DD)
     * @param string $decision_dalo        Texte de la décision
     * @param string $commentaire         Commentaire optionnel
     * @return int                         <0 si erreur, >0 si OK
     */
    public function enregistrerDecisionDALO($user, $date_decision_dalo, $decision_dalo, $commentaire = '')
    {
        // Vérifications
        if (!$this->dalo_depose) {
            $this->error = 'AucunDALODepose';
            return -1;
        }
        
        if (empty($date_decision_dalo)) {
            $this->error = 'DateDecisionDALOObligatoire';
            return -1;
        }
        
        if (empty($decision_dalo)) {
            $this->error = 'DecisionDALOObligatoire';
            return -1;
        }
        
        $ancienne_date_decision = $this->date_decision_dalo;
        $ancienne_decision = $this->decision_dalo;
        
        $this->date_decision_dalo = $date_decision_dalo;
        $this->decision_dalo = $decision_dalo;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Décision DALO reçue le " . dol_print_date($this->db->jdate($date_decision_dalo), 'day');
        $details .= "; Décision: " . $decision_dalo;
        
        $this->ajouterActionHistorique($user, 'DECISION_DALO', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DECISION_DALO_LOGEMENT';
                
                $message = 'Décision DALO pour la démarche logement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message,
                    array(
                        'date_decision_dalo' => array($ancienne_date_decision, $this->date_decision_dalo),
                        'decision_dalo' => array($ancienne_decision, $this->decision_dalo)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une demande FSL (Fonds de Solidarité pour le Logement)
     *
     * @param User   $user               Utilisateur effectuant l'action
     * @param double $montant_fsl_demande Montant FSL demandé
     * @param string $type_aide_fsl       Type d'aide FSL demandée
     * @param string $commentaire        Commentaire optionnel
     * @return int                        <0 si erreur, >0 si OK
     */
    public function enregistrerFSL($user, $montant_fsl_demande, $type_aide_fsl, $commentaire = '')
    {
        // Vérifications
        if ($montant_fsl_demande <= 0) {
            $this->error = 'MontantFSLDemandeDoitEtrePositif';
            return -1;
        }
        
        if (empty($type_aide_fsl)) {
            $this->error = 'TypeAideFSLObligatoire';
            return -1;
        }
        
        $ancien_demande_fsl = $this->demande_fsl;
        $ancien_montant_demande = $this->montant_fsl_demande;
        
        $this->demande_fsl = 1;
        $this->montant_fsl_demande = $montant_fsl_demande;
        $this->commentaires_situation = (!empty($this->commentaires_situation) ? $this->commentaires_situation . "\n\n" : '') . 
                                       "Type d'aide FSL demandée: " . $type_aide_fsl;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Demande FSL enregistrée";
        $details .= "; Montant demandé: " . price($montant_fsl_demande);
        $details .= "; Type d'aide: " . $type_aide_fsl;
        
        $this->ajouterActionHistorique($user, 'DEMANDE_FSL', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DEMANDE_FSL_LOGEMENT';
                
                $message = 'Demande FSL pour la démarche logement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message,
                    array(
                        'demande_fsl' => array($ancien_demande_fsl, $this->demande_fsl),
                        'montant_fsl_demande' => array($ancien_montant_demande, $this->montant_fsl_demande)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une décision FSL
     *
     * @param User   $user               Utilisateur effectuant l'action
     * @param double $montant_fsl_accorde Montant FSL accordé
     * @param string $commentaire        Commentaire optionnel
     * @return int                        <0 si erreur, >0 si OK
     */
    public function enregistrerDecisionFSL($user, $montant_fsl_accorde, $commentaire = '')
    {
        // Vérifications
        if (!$this->demande_fsl) {
            $this->error = 'AucuneDemandeFS';
            return -1;
        }
        
        if ($montant_fsl_accorde < 0) {
            $this->error = 'MontantFSLAccordeDoitEtrePositifOuZero';
            return -1;
        }
        
        $ancien_montant_accorde = $this->montant_fsl_accorde;
        
        $this->montant_fsl_accorde = $montant_fsl_accorde;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Décision FSL reçue";
        $details .= "; Montant accordé: " . price($montant_fsl_accorde);
        
        $this->ajouterActionHistorique($user, 'DECISION_FSL', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DECISION_FSL_LOGEMENT';
                
                $message = 'Décision FSL pour la démarche logement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message,
                    array('montant_fsl_accorde' => array($ancien_montant_accorde, $this->montant_fsl_accorde))
                );
            }
        }
        
        return $result;
    }

    /**
     * Ajoute ou met à jour une commune souhaitée
     * 
     * @param User   $user          Utilisateur effectuant l'action
     * @param string $commune_nom   Nom de la commune
     * @param string $commune_code  Code INSEE de la commune
     * @param string $commentaire   Commentaire optionnel
     * @return int                  <0 si erreur, >0 si OK
     */
    public function ajouterCommuneSouhaitee($user, $commune_nom, $commune_code, $commentaire = '')
    {
        // Vérifications
        if (empty($commune_nom) || empty($commune_code)) {
            $this->error = 'NomEtCodeCommuneObligatoires';
            return -1;
        }
        
        // Récupérer les communes déjà enregistrées
        $communes = $this->getCommunesSouhaitees();
        
        // Vérifier si la commune existe déjà
        $existe = false;
        foreach ($communes as $key => $commune) {
            if ($commune['code'] == $commune_code) {
                // Mettre à jour le nom si changé
                $communes[$key]['nom'] = $commune_nom;
                $existe = true;
                break;
            }
        }
        
        // Ajouter la commune si elle n'existe pas
        if (!$existe) {
            $communes[] = array(
                'nom' => $commune_nom,
                'code' => $commune_code
            );
        }
        
        // Mettre à jour le JSON
        $this->communes_souhaitees = json_encode($communes);
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = ($existe ? "Mise à jour" : "Ajout") . " de la commune souhaitée: " . $commune_nom . " (" . $commune_code . ")";
        
        $this->ajouterActionHistorique($user, 'AJOUT_COMMUNE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'AJOUT_COMMUNE_LOGEMENT';
                
                $message = ($existe ? 'Mise à jour' : 'Ajout') . ' de commune souhaitée pour la démarche logement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Supprime une commune souhaitée
     * 
     * @param User   $user          Utilisateur effectuant l'action
     * @param string $commune_code  Code INSEE de la commune
     * @param string $commentaire   Commentaire optionnel
     * @return int                  <0 si erreur, >0 si OK
     */
    public function supprimerCommuneSouhaitee($user, $commune_code, $commentaire = '')
    {
        // Vérifications
        if (empty($commune_code)) {
            $this->error = 'CodeCommuneObligatoire';
            return -1;
        }
        
        // Récupérer les communes déjà enregistrées
        $communes = $this->getCommunesSouhaitees();
        
        // Chercher et supprimer la commune
        $commune_nom = '';
        $trouve = false;
        $nouvelles_communes = array();
        
        foreach ($communes as $commune) {
            if ($commune['code'] == $commune_code) {
                $commune_nom = $commune['nom'];
                $trouve = true;
            } else {
                $nouvelles_communes[] = $commune;
            }
        }
        
        if (!$trouve) {
            $this->error = 'CommuneNonTrouvee';
            return -1;
        }
        
        // Mettre à jour le JSON
        $this->communes_souhaitees = json_encode($nouvelles_communes);
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Suppression de la commune souhaitée: " . $commune_nom . " (" . $commune_code . ")";
        
        $this->ajouterActionHistorique($user, 'SUPPRESSION_COMMUNE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'SUPPRESSION_COMMUNE_LOGEMENT';
                
                $message = 'Suppression de commune souhaitée pour la démarche logement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }

    /**
     * Ajoute ou met à jour un organisme déposé
     * 
     * @param User   $user             Utilisateur effectuant l'action
     * @param string $organisme_nom    Nom de l'organisme
     * @param string $date_depot       Date du dépôt (YYYY-MM-DD)
     * @param string $commentaire      Commentaire optionnel
     * @return int                     <0 si erreur, >0 si OK
     */
    public function ajouterOrganismeDepose($user, $organisme_nom, $date_depot, $commentaire = '')
    {
        // Vérifications
        if (empty($organisme_nom)) {
            $this->error = 'NomOrganismeObligatoire';
            return -1;
        }
        
        if (empty($date_depot)) {
            $this->error = 'DateDepotOrganismeObligatoire';
            return -1;
        }
        
        // Récupérer les organismes déjà enregistrés
        $organismes = $this->getOrganismesDeposes();
        
        // Vérifier si l'organisme existe déjà
        $existe = false;
        foreach ($organismes as $key => $organisme) {
            if ($organisme['nom'] == $organisme_nom) {
                // Mettre à jour la date
                $organismes[$key]['date_depot'] = $date_depot;
                $existe = true;
                break;
            }
        }
        
        // Ajouter l'organisme s'il n'existe pas
        if (!$existe) {
            $organismes[] = array(
                'nom' => $organisme_nom,
                'date_depot' => $date_depot
            );
        }
        
        // Mettre à jour le JSON
        $this->organismes_deposes = json_encode($organismes);
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = ($existe ? "Mise à jour" : "Ajout") . " de l'organisme: " . $organisme_nom;
        $details .= "; Date de dépôt: " . dol_print_date($this->db->jdate($date_depot), 'day');
        
        $this->ajouterActionHistorique($user, 'AJOUT_ORGANISME', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'AJOUT_ORGANISME_LOGEMENT';
                
                $message = ($existe ? 'Mise à jour' : 'Ajout') . ' d\'organisme pour la démarche logement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }

    /**
     * Met à jour les pièces justificatives à fournir
     * 
     * @param User   $user                   Utilisateur effectuant l'action
     * @param array  $pieces_justificatives  Tableau des pièces justificatives
     * @param string $commentaire            Commentaire optionnel
     * @return int                           <0 si erreur, >0 si OK
     */
    public function updatePiecesJustificatives($user, $pieces_justificatives, $commentaire = '')
    {
        if (empty($pieces_justificatives) || !is_array($pieces_justificatives)) {
            $this->error = 'InvalidPiecesJustificativesFormat';
            return -1;
        }
        
        $anciennes_pieces = $this->pieces_justificatives;
        $this->pieces_justificatives = json_encode($pieces_justificatives);
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Liste des pièces justificatives mise à jour";
        $this->ajouterActionHistorique($user, 'MISE_A_JOUR_PIECES', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_PIECES_LOGEMENT';
                
                $message = 'Mise à jour des pièces justificatives pour la démarche logement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_logement',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }

    /**
     * Récupère les communes souhaitées
     * 
     * @return array Tableau des communes souhaitées
     */
    public function getCommunesSouhaitees()
    {
        if (empty($this->communes_souhaitees)) {
            return array();
        }
        
        $communes = json_decode($this->communes_souhaitees, true);
        
        if (!is_array($communes)) {
            return array();
        }
        
        return $communes;
    }
    
    /**
     * Récupère les organismes déposés
     * 
     * @return array Tableau des organismes déposés
     */
    public function getOrganismesDeposes()
    {
        if (empty($this->organismes_deposes)) {
            return array();
        }
        
        $organismes = json_decode($this->organismes_deposes, true);
        
        if (!is_array($organismes)) {
            return array();
        }
        
        return $organismes;
    }

    /**
     * Récupère la liste des pièces justificatives formatée
     * 
     * @param bool $with_status Inclure le statut des pièces
     * @return array            Tableau des pièces justificatives
     */
    public function getPiecesJustificatives($with_status = true)
    {
        if (empty($this->pieces_justificatives)) {
            return array();
        }
        
        $pieces = json_decode($this->pieces_justificatives, true);
        
        if (!is_array($pieces)) {
            return array();
        }
        
        // Si on ne veut pas le statut, on simplifie le tableau
        if (!$with_status) {
            $simplified = array();
            foreach ($pieces as $piece) {
                $simplified[] = $piece['libelle'];
            }
            return $simplified;
        }
        
        return $pieces;
    }

    /**
     * Génère la liste par défaut des pièces justificatives au format JSON
     * 
     * @return string Liste des pièces justificatives au format JSON
     */
    protected function getPiecesJustificativesParDefaut()
    {
        $pieces = array(
            array('libelle' => 'Pièce d\'identité', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Justificatif de domicile', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Avis d\'imposition N-1', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Avis d\'imposition N-2', 'obligatoire' => 1, 'fourni' => 0)
        );
        
        // Ajouter des pièces spécifiques selon le type de démarche logement
        switch ($this->type_demarche_logement_code) {
            case 'LOGEMENT_SOCIAL':
                $pieces[] = array('libelle' => 'Formulaire CERFA de demande de logement social', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => '3 derniers bulletins de salaire', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation CAF', 'obligatoire' => 0, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Livret de famille', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'DALO':
                $pieces[] = array('libelle' => 'Formulaire CERFA recours DALO', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Justificatif de démarches de recherche de logement', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation d\'hébergement', 'obligatoire' => 0, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Notification de décision défavorable', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'FSL':
                $pieces[] = array('libelle' => 'Formulaire de demande FSL', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'RIB', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Justificatifs de ressources des 3 derniers mois', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Devis/Factures', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'AIDE_LOGEMENT':
                $pieces[] = array('libelle' => 'Demande d\'aide au logement CAF/MSA', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Bail complet signé', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation de loyer', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'RIB', 'obligatoire' => 1, 'fourni' => 0);
                break;
        }
        
        return json_encode($pieces);
    }

    /**
     * Ajoute une entrée à l'historique des actions spécifiques de la démarche logement
     *
     * @param User   $user      Utilisateur effectuant l'action
     * @param string $type      Type d'action (libre ou utiliser les constantes de la classe)
     * @param string $details   Détails de l'action
     * @param string $commentaire Commentaire optionnel
     * @return int              <0 si erreur, >0 si OK
     */
    public function addHistoriqueAction($user, $type, $details, $commentaire = '')
    {
        $this->ajouterActionHistorique($user, $type, $details, $commentaire);
        
        // Mise à jour en base de données
        return $this->update($user, 1); // Mise à jour silencieuse
    }
    
    /**
     * Ajoute une action à l'historique spécifique de la démarche logement
     *
     * @param User   $user      Utilisateur effectuant l'action
     * @param string $type      Type d'action
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
        
        // Découper en entrées individuelles
        $entries = explode("\n\n", $this->historique_actions);
        
        // Appliquer le filtre si nécessaire
        if (!empty($filter)) {
            $filtered_entries = array();
            foreach ($entries as $entry) {
                if (strpos($entry, ' - ' . $filter . ' - ') !== false) {
                    $filtered_entries[] = $entry;
                }
            }
            $entries = $filtered_entries;
        }
        
        // Limiter le nombre d'entrées si demandé
        if ($limit > 0 && count($entries) > $limit) {
            $entries = array_slice($entries, 0, $limit);
        }
        
        // Si demande de tableau, retourner les entrées sous forme de tableau structuré
        if (!$html && is_array($entries)) {
            $structured_entries = array();
            foreach ($entries as $entry) {
                $parts = explode(' - ', $entry, 4); // Max 4 parties (date/heure, utilisateur, type, détails+commentaire)
                if (count($parts) >= 3) {
                    $structured_entry = array(
                        'datetime' => $parts[0],
                        'user' => $parts[1],
                        'type' => $parts[2],
                        'details' => isset($parts[3]) ? $parts[3] : ''
                    );
                    
                    // Extraire le commentaire s'il existe
                    $comment_pos = isset($parts[3]) ? strpos($parts[3], ' - Commentaire: ') : false;
                    if ($comment_pos !== false) {
                        $structured_entry['details'] = substr($parts[3], 0, $comment_pos);
                        $structured_entry['comment'] = substr($parts[3], $comment_pos + 15); // 15 = longueur de ' - Commentaire: '
                    }
                    
                    $structured_entries[] = $structured_entry;
                }
            }
            return $structured_entries;
        }
        
        // Formater en HTML si demandé
        if ($html) {
            $html_output = '<div class="historique-actions">';
            foreach ($entries as $entry) {
                // Extraction des parties pour mise en forme
                $parts = explode(' - ', $entry, 4);
                if (count($parts) >= 3) {
                    $datetime = $parts[0];
                    $user = $parts[1];
                    $type = $parts[2];
                    $details = isset($parts[3]) ? $parts[3] : '';
                    
                    // Coloriser selon le type d'action
                    $class = '';
                    switch ($type) {
                        case 'CHANGEMENT_STATUT_DEMARCHE':
                            $class = 'bg-info';
                            break;
                        case 'ENREGISTREMENT_DEPOT':
                            $class = 'bg-success';
                            break;
                        case 'RENOUVELLEMENT':
                            $class = 'bg-warning';
                            break;
                        case 'ENREGISTREMENT_PROPOSITION':
                            $class = 'bg-primary';
                            break;
                        case 'ACCEPTATION_LOGEMENT':
                            $class = 'bg-success';
                            break;
                        case 'REFUS_PROPOSITION':
                            $class = 'bg-danger';
                            break;
                        case 'DEPOT_DALO':
                            $class = 'bg-warning';
                            break;
                        case 'DEMANDE_FSL':
                            $class = 'bg-info';
                            break;
                        default:
                            $class = '';
                    }
                    
                    // Extraire et formater le commentaire s'il existe
                    $comment_html = '';
                    $comment_pos = strpos($details, ' - Commentaire: ');
                    if ($comment_pos !== false) {
                        $comment = substr($details, $comment_pos + 15);
                        $details = substr($details, 0, $comment_pos);
                        $comment_html = '<div class="historique-comment"><em>' . dol_htmlentities($comment) . '</em></div>';
                    }
                    
                    // Générer le HTML
                    $html_output .= '<div class="historique-entry '.$class.'">';
                    $html_output .= '<div class="historique-header">';
                    $html_output .= '<span class="historique-date">' . dol_htmlentities($datetime) . '</span> - ';
                    $html_output .= '<span class="historique-user">' . dol_htmlentities($user) . '</span> - ';
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
        
        // Sinon retourner le texte brut
        return implode("\n\n", $entries);
    }
    
    /**
     * Recherche dans l'historique des actions
     * 
     * @param string $search Texte à rechercher
     * @return array         Tableau des entrées correspondantes
     */
    public function searchHistoriqueActions($search)
    {
        if (empty($this->historique_actions) || empty($search)) {
            return array();
        }
        
        $entries = explode("\n\n", $this->historique_actions);
        $results = array();
        
        foreach ($entries as $entry) {
            if (stripos($entry, $search) !== false) {
                $results[] = $entry;
            }
        }
        
        return $results;
    }

    /**
     * Obtient le libellé du type de démarche logement
     * 
     * @return string Libellé du type de démarche
     */
    public function getTypeDemarcheLogementLabel()
    {
        $types = self::getTypeDemarcheLogementOptions($this->langs);
        return isset($types[$this->type_demarche_logement_code]) ? $types[$this->type_demarche_logement_code] : $this->type_demarche_logement_code;
    }
    
    /**
     * Liste des statuts de démarche logement valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsDemarcheLogementValides()
    {
        return array(
            'A_CONSTITUER',          // Dossier à constituer
            'DOSSIER_CONSTITUE',     // Dossier constitué
            'DOSSIER_DEPOSE',        // Dossier déposé
            'NUMERO_UNIQUE_OBTENU',  // Numéro unique obtenu
            'EN_INSTRUCTION',        // Dossier en cours d'instruction
            'PROPOSITION_RECUE',     // Proposition de logement reçue
            'VISITE_PROGRAMMEE',     // Visite de logement programmée
            'LOGEMENT_ATTRIBUE',     // Logement attribué en commission
            'BAIL_SIGNE',            // Bail signé
            'DOSSIER_REFUSE',        // Dossier refusé
            'RENOUVELLEMENT_EFFECTUE', // Renouvellement effectué
        );
    }
    
    /**
     * Liste des types de démarche logement valides
     *
     * @return array Codes des types de démarche valides
     */
    public static function getTypesDemarcheLogementValides()
    {
        return array(
            'LOGEMENT_SOCIAL',     // Demande de logement social
            'DALO',                // Recours DALO
            'FSL',                 // Fonds de Solidarité pour le Logement
            'AIDE_LOGEMENT',       // Aide au logement (APL, ALF, ALS)
            'LOCAPASS',            // Garantie Loca-Pass
            'VISALE',              // Visa pour le Logement et l'Emploi
            'RENOVATION',          // Aide à la rénovation
            'AUTRE'                // Autre type de démarche logement
        );
    }
    
    /**
     * Récupère les options du dictionnaire des types de démarche logement
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeDemarcheLogementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'logement_type_demarche', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts de démarche logement
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutDemarcheLogementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'logement_statut_demarche', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de logement
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeLogementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'logement_type', $usekeys, $show_empty);
    }
    
    /**
     * Vérifie si cette démarche spécifique est une démarche logement
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isLogement()
    {
        return true;
    }
    
    /**
     * Récupère le contact référent logement associé à la démarche
     * 
     * @return Contact|null Contact ou null si non trouvé
     */
    public function getContactReferent()
    {
        if (empty($this->fk_contact_referent)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
        $contact = new Contact($this->db);
        
        if ($contact->fetch($this->fk_contact_referent) > 0) {
            return $contact;
        }
        
        return null;
    }
    
    /**
     * Définit des constantes pour les types d'actions standards dans l'historique
     */
    const ACTION_CHANGEMENT_STATUT_DEMARCHE = 'CHANGEMENT_STATUT_DEMARCHE';
    const ACTION_ENREGISTREMENT_DEPOT = 'ENREGISTREMENT_DEPOT';
    const ACTION_RENOUVELLEMENT = 'RENOUVELLEMENT';
    const ACTION_CONFIGURATION_CRITERES_LOGEMENT = 'CONFIGURATION_CRITERES_LOGEMENT';
    const ACTION_ENREGISTREMENT_PROPOSITION = 'ENREGISTREMENT_PROPOSITION';
    const ACTION_ACCEPTATION_LOGEMENT = 'ACCEPTATION_LOGEMENT';
    const ACTION_REFUS_PROPOSITION = 'REFUS_PROPOSITION';
    const ACTION_DEPOT_DALO = 'DEPOT_DALO';
    const ACTION_DECISION_DALO = 'DECISION_DALO';
    const ACTION_DEMANDE_FSL = 'DEMANDE_FSL';
    const ACTION_DECISION_FSL = 'DECISION_FSL';
    const ACTION_AJOUT_COMMUNE = 'AJOUT_COMMUNE';
    const ACTION_SUPPRESSION_COMMUNE = 'SUPPRESSION_COMMUNE';
    const ACTION_AJOUT_ORGANISME = 'AJOUT_ORGANISME';
    const ACTION_MISE_A_JOUR_PIECES = 'MISE_A_JOUR_PIECES';
    const ACTION_CONTACT_REFERENT = 'CONTACT_REFERENT';
    const ACTION_NOTE_INTERNE = 'NOTE_INTERNE';
}

} // Fin de la condition if !class_exists

// Dernière modification: 2025-06-03 17:16:07 UTC par Kylian65
