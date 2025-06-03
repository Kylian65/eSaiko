<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches Pôle Emploi des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65Oui
 * Dernière modification: 2025-06-03 16:45:29 UTC
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarchePoleEmploi', false)) {

class ElaskaParticulierDemarchePoleEmploi extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_pole_emploi';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_pole_emploi';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AUX DÉMARCHES PÔLE EMPLOI
    //
    
    /**
     * @var string Identifiant Pôle Emploi
     */
    public $identifiant_pe;
    
    /**
     * @var string Code du type de démarche Pôle Emploi (dictionnaire)
     */
    public $type_demarche_pe_code;
    
    /**
     * @var string Code du statut de la démarche (dictionnaire)
     */
    public $statut_demarche_pe_code;
    
    /**
     * @var string Date de dernière actualisation (format YYYY-MM-DD)
     */
    public $date_derniere_actualisation;
    
    /**
     * @var string Date de prochaine actualisation (format YYYY-MM-DD)
     */
    public $date_prochaine_actualisation;
    
    /**
     * @var string Date d'inscription à Pôle Emploi (format YYYY-MM-DD)
     */
    public $date_inscription;
    
    /**
     * @var string Date de radiation le cas échéant (format YYYY-MM-DD)
     */
    public $date_radiation;
    
    /**
     * @var string Motif de radiation
     */
    public $motif_radiation;
    
    /**
     * @var string Catégorie de demandeur d'emploi (A, B, C, D, E)
     */
    public $categorie_demandeur;
    
    /**
     * @var double Montant des allocations mensuelles
     */
    public $montant_allocation;
    
    /**
     * @var int Nombre de jours d'allocation restants
     */
    public $jours_allocation_restants;
    
    /**
     * @var string Date de début des droits (format YYYY-MM-DD)
     */
    public $date_debut_droits;
    
    /**
     * @var string Date de fin des droits (format YYYY-MM-DD)
     */
    public $date_fin_droits;
    
    /**
     * @var string Type d'allocation (ARE, ASS, etc.)
     */
    public $type_allocation;
    
    /**
     * @var string Code ROME du métier recherché principal
     */
    public $code_rome_principal;
    
    /**
     * @var string Libellé du métier recherché principal
     */
    public $metier_recherche;
    
    /**
     * @var string Codes ROME additionnels séparés par des virgules
     */
    public $codes_rome_secondaires;
    
    /**
     * @var string Zone géographique de recherche
     */
    public $zone_recherche;
    
    /**
     * @var int Distance maximale de recherche en km
     */
    public $distance_max_km;
    
    /**
     * @var string Date du dernier entretien (format YYYY-MM-DD)
     */
    public $date_dernier_entretien;
    
    /**
     * @var string Date du prochain entretien (format YYYY-MM-DD)
     */
    public $date_prochain_entretien;
    
    /**
     * @var string Type de contrat recherché (CDI, CDD, etc.)
     */
    public $type_contrat_recherche;
    
    /**
     * @var int ID du contact conseiller Pôle Emploi
     */
    public $fk_contact_conseiller;
    
    /**
     * @var string Agence Pôle Emploi de rattachement
     */
    public $agence_rattachement;
    
    /**
     * @var string Plan d'action personnalisé (format JSON)
     */
    public $plan_action;
    
    /**
     * @var string Formations prévues ou en cours (format JSON)
     */
    public $formations;
    
    /**
     * @var int Indicateur de PPAE actif (0=non, 1=oui)
     */
    public $ppae_actif;
    
    /**
     * @var string Date de signature du PPAE (format YYYY-MM-DD)
     */
    public $date_signature_ppae;
    
    /**
     * @var string Historique des actions spécifiques à la démarche Pôle Emploi
     */
    public $historique_actions;
    
    /**
     * @var string Pièces justificatives à fournir (format JSON)
     */
    public $pieces_justificatives;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_pole_emploi = array(
        'identifiant_pe' => array('type' => 'varchar(20)', 'label' => 'IdentifiantPE', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'type_demarche_pe_code' => array('type' => 'varchar(50)', 'label' => 'TypeDemarchePE', 'enabled' => 1, 'position' => 1110, 'notnull' => 1, 'visible' => 1),
        'statut_demarche_pe_code' => array('type' => 'varchar(50)', 'label' => 'StatutDemarchePE', 'enabled' => 1, 'position' => 1120, 'notnull' => 1, 'visible' => 1),
        'date_derniere_actualisation' => array('type' => 'date', 'label' => 'DateDerniereActualisation', 'enabled' => 1, 'position' => 1130, 'notnull' => 0, 'visible' => 1),
        'date_prochaine_actualisation' => array('type' => 'date', 'label' => 'DateProchaineActualisation', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'date_inscription' => array('type' => 'date', 'label' => 'DateInscription', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'date_radiation' => array('type' => 'date', 'label' => 'DateRadiation', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'motif_radiation' => array('type' => 'varchar(255)', 'label' => 'MotifRadiation', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'categorie_demandeur' => array('type' => 'varchar(2)', 'label' => 'CategorieDemandeur', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'montant_allocation' => array('type' => 'double(24,8)', 'label' => 'MontantAllocation', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'jours_allocation_restants' => array('type' => 'integer', 'label' => 'JoursAllocationRestants', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'date_debut_droits' => array('type' => 'date', 'label' => 'DateDebutDroits', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'date_fin_droits' => array('type' => 'date', 'label' => 'DateFinDroits', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'type_allocation' => array('type' => 'varchar(50)', 'label' => 'TypeAllocation', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'code_rome_principal' => array('type' => 'varchar(5)', 'label' => 'CodeROMEPrincipal', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'metier_recherche' => array('type' => 'varchar(255)', 'label' => 'MetierRecherche', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'codes_rome_secondaires' => array('type' => 'varchar(50)', 'label' => 'CodesROMESecondaires', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1),
        'zone_recherche' => array('type' => 'varchar(255)', 'label' => 'ZoneRecherche', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'distance_max_km' => array('type' => 'integer', 'label' => 'DistanceMaxKm', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'date_dernier_entretien' => array('type' => 'date', 'label' => 'DateDernierEntretien', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1),
        'date_prochain_entretien' => array('type' => 'date', 'label' => 'DateProchainEntretien', 'enabled' => 1, 'position' => 1300, 'notnull' => 0, 'visible' => 1),
        'type_contrat_recherche' => array('type' => 'varchar(50)', 'label' => 'TypeContratRecherche', 'enabled' => 1, 'position' => 1310, 'notnull' => 0, 'visible' => 1),
        'fk_contact_conseiller' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactConseiller', 'enabled' => 1, 'position' => 1320, 'notnull' => 0, 'visible' => 1),
        'agence_rattachement' => array('type' => 'varchar(255)', 'label' => 'AgenceRattachement', 'enabled' => 1, 'position' => 1330, 'notnull' => 0, 'visible' => 1),
        'plan_action' => array('type' => 'text', 'label' => 'PlanAction', 'enabled' => 1, 'position' => 1340, 'notnull' => 0, 'visible' => 1),
        'formations' => array('type' => 'text', 'label' => 'Formations', 'enabled' => 1, 'position' => 1350, 'notnull' => 0, 'visible' => 1),
        'ppae_actif' => array('type' => 'boolean', 'label' => 'PPAEActif', 'enabled' => 1, 'position' => 1360, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'date_signature_ppae' => array('type' => 'date', 'label' => 'DateSignaturePPAE', 'enabled' => 1, 'position' => 1370, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 1380, 'notnull' => 0, 'visible' => 1),
        'pieces_justificatives' => array('type' => 'text', 'label' => 'PiecesJustificatives', 'enabled' => 1, 'position' => 1390, 'notnull' => 0, 'visible' => 1)
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Fusion des champs spécifiques Pôle Emploi avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_pole_emploi);
        
        // Valeurs par défaut spécifiques aux démarches Pôle Emploi
        $this->type_demarche_code = 'POLE_EMPLOI';  // Force le code de type de démarche
        if (!isset($this->statut_demarche_pe_code)) $this->statut_demarche_pe_code = 'A_FAIRE'; // Statut par défaut
        if (!isset($this->ppae_actif)) $this->ppae_actif = 0; // Par défaut pas de PPAE actif
        if (!isset($this->distance_max_km)) $this->distance_max_km = 30; // 30 km par défaut
    }

    /**
     * Crée une démarche Pôle Emploi dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à POLE_EMPLOI
        $this->type_demarche_code = 'POLE_EMPLOI';
        
        // Vérifications spécifiques aux démarches Pôle Emploi
        if (empty($this->type_demarche_pe_code)) {
            $this->error = 'TypeDemarchePEIsMandatory';
            return -1;
        }
        
        // Génération automatique du libellé s'il n'est pas fourni
        if (empty($this->libelle)) {
            $this->libelle = $this->getTypeDemarchePELabel();
            if (!empty($this->metier_recherche)) {
                $this->libelle .= ' - ' . $this->metier_recherche;
            }
            if (!empty($this->identifiant_pe)) {
                $this->libelle .= ' (ID: ' . $this->identifiant_pe . ')';
            }
        }
        
        // Initialisation des pièces justificatives
        if (empty($this->pieces_justificatives)) {
            $this->pieces_justificatives = $this->getPiecesJustificativesParDefaut();
        }
        
        // Si c'est une inscription, la date d'inscription par défaut est aujourd'hui
        if ($this->type_demarche_pe_code == 'INSCRIPTION' && empty($this->date_inscription)) {
            $this->date_inscription = date('Y-m-d');
        }
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'une démarche Pôle Emploi
            $note = "Création d'une démarche Pôle Emploi de type " . $this->getTypeDemarchePELabel();
            if (!empty($this->identifiant_pe)) {
                $note .= " pour l'identifiant PE " . $this->identifiant_pe;
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
     * Met à jour une démarche Pôle Emploi dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à POLE_EMPLOI
        $this->type_demarche_code = 'POLE_EMPLOI';
        
        // Vérifications spécifiques aux démarches Pôle Emploi
        if (empty($this->type_demarche_pe_code)) {
            $this->error = 'TypeDemarchePEIsMandatory';
            return -1;
        }
        
        // Vérification de la cohérence des dates
        if (!empty($this->date_debut_droits) && !empty($this->date_fin_droits) && $this->date_fin_droits < $this->date_debut_droits) {
            $this->error = 'DateFinDroitsCantBeBeforeDateDebutDroits';
            return -1;
        }
        
        // Vérification du code ROME s'il est fourni
        if (!empty($this->code_rome_principal) && !preg_match('/^[A-Z][0-9]{4}$/', $this->code_rome_principal)) {
            $this->error = 'InvalidCodeROMEFormat';
            return -1;
        }
        
        // Si une radiation est enregistrée mais sans motif
        if (!empty($this->date_radiation) && empty($this->motif_radiation)) {
            $this->error = 'MotifRadiationRequiredWithDateRadiation';
            return -1;
        }
        
        // Vérification de la catégorie du demandeur d'emploi
        if (!empty($this->categorie_demandeur) && !preg_match('/^[A-E]$/', $this->categorie_demandeur)) {
            $this->error = 'InvalidCategorieDemandeur';
            return -1;
        }
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le statut de la démarche Pôle Emploi
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau code de statut
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function changerStatutDemarchePE($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsDemarchePEValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatutDemarchePECode';
            return -1;
        }
        
        $ancien_statut = $this->statut_demarche_pe_code;
        $this->statut_demarche_pe_code = $statut_code;
        
        // Mises à jour automatiques selon le statut
        switch ($statut_code) {
            case 'DOCUMENTS_PREPARES':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 30; // 30% de progression
                break;
                
            case 'DEMARCHE_EN_COURS':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 50; // 50% de progression
                break;
                
            case 'ATTENTE_REPONSE_PE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 70; // 70% de progression
                break;
                
            case 'COMPLETEE':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'RADIEE':
                if (empty($this->date_radiation)) {
                    $this->date_radiation = date('Y-m-d');
                }
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'REJETEE':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
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
                $action = 'CHANGEMENT_STATUT_PE';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutDemarchePEOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche Pôle Emploi "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_pole_emploi',
                    $this->id,
                    $message,
                    array('statut_demarche_pe_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une actualisation Pôle Emploi
     *
     * @param User   $user                       Utilisateur effectuant l'action
     * @param string $date_actualisation         Date de l'actualisation (YYYY-MM-DD)
     * @param string $date_prochaine_actualisation Date de la prochaine actualisation (YYYY-MM-DD)
     * @param int    $jours_allocation_restants   Jours d'allocation restants après actualisation
     * @param string $commentaire                Commentaire optionnel
     * @return int                               <0 si erreur, >0 si OK
     */
    public function enregistrerActualisation($user, $date_actualisation, $date_prochaine_actualisation, $jours_allocation_restants = null, $commentaire = '')
    {
        // Vérifications
        if (empty($date_actualisation)) {
            $this->error = 'DateActualisationObligatoire';
            return -1;
        }
        
        $ancienne_date_actualisation = $this->date_derniere_actualisation;
        $ancienne_date_prochaine = $this->date_prochaine_actualisation;
        $anciens_jours_restants = $this->jours_allocation_restants;
        
        $this->date_derniere_actualisation = $date_actualisation;
        $this->date_prochaine_actualisation = $date_prochaine_actualisation;
        
        if ($jours_allocation_restants !== null) {
            $this->jours_allocation_restants = $jours_allocation_restants;
        }
        
        // Mise à jour du statut
        if ($this->statut_demarche_pe_code == 'A_FAIRE') {
            $this->statut_demarche_pe_code = 'DEMARCHE_EN_COURS';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 50;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Actualisation effectuée le " . dol_print_date($this->db->jdate($date_actualisation), 'day');
        $details .= "; Prochaine actualisation: " . dol_print_date($this->db->jdate($date_prochaine_actualisation), 'day');
        
        if ($jours_allocation_restants !== null) {
            $details .= "; Jours d'allocation restants: " . $this->jours_allocation_restants;
        }
        
        $this->ajouterActionHistorique($user, 'ACTUALISATION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ACTUALISATION_PE';
                
                $message = 'Actualisation Pôle Emploi pour "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_pole_emploi',
                    $this->id,
                    $message,
                    array('date_derniere_actualisation' => array($ancienne_date_actualisation, $this->date_derniere_actualisation))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure un entretien Pôle Emploi
     *
     * @param User   $user                 Utilisateur effectuant l'action
     * @param string $date_entretien       Date de l'entretien (YYYY-MM-DD)
     * @param bool   $est_dernier_entretien True si c'est un entretien passé, false si c'est à venir
     * @param string $commentaire          Commentaire optionnel
     * @return int                         <0 si erreur, >0 si OK
     */
    public function configurerEntretien($user, $date_entretien, $est_dernier_entretien = false, $commentaire = '')
    {
        // Vérifications
        if (empty($date_entretien)) {
            $this->error = 'DateEntretienObligatoire';
            return -1;
        }
        
        $ancienne_date_dernier = $this->date_dernier_entretien;
        $ancienne_date_prochain = $this->date_prochain_entretien;
        
        if ($est_dernier_entretien) {
            // C'est un entretien qui a eu lieu
            $this->date_dernier_entretien = $date_entretien;
        } else {
            // C'est un entretien à venir
            $this->date_prochain_entretien = $date_entretien;
            
            // Si date échéance non définie ou antérieure, on la met à jour
            if (empty($this->date_echeance) || $this->date_echeance < $date_entretien) {
                $this->date_echeance = $date_entretien;
            }
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        if ($est_dernier_entretien) {
            $details = "Entretien passé le " . dol_print_date($this->db->jdate($date_entretien), 'day');
        } else {
            $details = "Prochain entretien prévu le " . dol_print_date($this->db->jdate($date_entretien), 'day');
        }
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_ENTRETIEN', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_ENTRETIEN_PE';
                
                $message = 'Configuration d\'entretien Pôle Emploi pour "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $data_array = array();
                if ($est_dernier_entretien) {
                    $data_array['date_dernier_entretien'] = array($ancienne_date_dernier, $this->date_dernier_entretien);
                } else {
                    $data_array['date_prochain_entretien'] = array($ancienne_date_prochain, $this->date_prochain_entretien);
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_pole_emploi',
                    $this->id,
                    $message,
                    $data_array
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour les informations d'allocation
     *
     * @param User   $user                Utilisateur effectuant l'action
     * @param double $montant_allocation   Montant de l'allocation
     * @param string $type_allocation      Type d'allocation (ARE, ASS, etc.)
     * @param string $date_debut_droits    Date de début des droits (YYYY-MM-DD)
     * @param string $date_fin_droits      Date de fin des droits (YYYY-MM-DD)
     * @param int    $jours_allocation_restants Jours d'allocation restants
     * @param string $commentaire         Commentaire optionnel
     * @return int                        <0 si erreur, >0 si OK
     */
    public function updateAllocation($user, $montant_allocation, $type_allocation, $date_debut_droits, $date_fin_droits, $jours_allocation_restants = null, $commentaire = '')
    {
        // Vérifications
        if ($montant_allocation < 0) {
            $this->error = 'MontantAllocationDoitEtrePositifOuZero';
            return -1;
        }
        
        if (empty($type_allocation)) {
            $this->error = 'TypeAllocationObligatoire';
            return -1;
        }
        
        if (empty($date_debut_droits) || empty($date_fin_droits)) {
            $this->error = 'DateDebutEtFinDroitsObligatoires';
            return -1;
        }
        
        if ($date_fin_droits < $date_debut_droits) {
            $this->error = 'DateFinDroitsNeDoitPasEtreAnterieureADateDebutDroits';
            return -1;
        }
        
        $ancien_montant = $this->montant_allocation;
        $ancien_type = $this->type_allocation;
        $ancienne_date_debut = $this->date_debut_droits;
        $ancienne_date_fin = $this->date_fin_droits;
        $anciens_jours = $this->jours_allocation_restants;
        
        $this->montant_allocation = $montant_allocation;
        $this->type_allocation = $type_allocation;
        $this->date_debut_droits = $date_debut_droits;
        $this->date_fin_droits = $date_fin_droits;
        
        if ($jours_allocation_restants !== null) {
            $this->jours_allocation_restants = $jours_allocation_restants;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Montant allocation: ".price($ancien_montant)." → ".price($this->montant_allocation);
        $details .= "; Type: ".($ancien_type ?: 'Non défini')." → ".$this->type_allocation;
        $details .= "; Période: ";
        $details .= ($ancienne_date_debut ? dol_print_date($this->db->jdate($ancienne_date_debut), 'day') : 'Non définie');
        $details .= " → ";
        $details .= ($ancienne_date_fin ? dol_print_date($this->db->jdate($ancienne_date_fin), 'day') : 'Non définie');
        $details .= " à ";
        $details .= dol_print_date($this->db->jdate($this->date_debut_droits), 'day');
        $details .= " → ";
        $details .= dol_print_date($this->db->jdate($this->date_fin_droits), 'day');
        
        if ($jours_allocation_restants !== null) {
            $details .= "; Jours restants: ".($anciens_jours ?: '0')." → ".$this->jours_allocation_restants;
        }
        
        $this->ajouterActionHistorique($user, 'MAJ_ALLOCATION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_ALLOCATION_PE';
                
                $message = 'Mise à jour des informations d\'allocation pour la démarche Pôle Emploi "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_pole_emploi',
                    $this->id,
                    $message,
                    array(
                        'montant_allocation' => array($ancien_montant, $this->montant_allocation),
                        'type_allocation' => array($ancien_type, $this->type_allocation)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure la recherche d'emploi
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param string $metier_recherche       Métier recherché
     * @param string $code_rome_principal    Code ROME principal
     * @param string $codes_rome_secondaires Codes ROME secondaires (séparés par des virgules)
     * @param string $zone_recherche         Zone géographique de recherche
     * @param int    $distance_max_km        Distance maximale en km
     * @param string $type_contrat_recherche Type de contrat recherché
     * @param string $commentaire           Commentaire optionnel
     * @return int                          <0 si erreur, >0 si OK
     */
    public function configurerRecherche($user, $metier_recherche, $code_rome_principal = '', $codes_rome_secondaires = '', $zone_recherche = '', $distance_max_km = 0, $type_contrat_recherche = '', $commentaire = '')
    {
        // Vérifications
        if (empty($metier_recherche)) {
            $this->error = 'MetierRechercheObligatoire';
            return -1;
        }
        
        // Vérification du code ROME s'il est fourni
        if (!empty($code_rome_principal) && !preg_match('/^[A-Z][0-9]{4}$/', $code_rome_principal)) {
            $this->error = 'InvalidCodeROMEFormat';
            return -1;
        }
        
        $ancien_metier = $this->metier_recherche;
        $ancien_code_rome = $this->code_rome_principal;
        $anciens_codes_secondaires = $this->codes_rome_secondaires;
        $ancienne_zone = $this->zone_recherche;
        $ancienne_distance = $this->distance_max_km;
        $ancien_type_contrat = $this->type_contrat_recherche;
        
        $this->metier_recherche = $metier_recherche;
        
        if (!empty($code_rome_principal)) {
            $this->code_rome_principal = $code_rome_principal;
        }
        
        if (!empty($codes_rome_secondaires)) {
            $this->codes_rome_secondaires = $codes_rome_secondaires;
        }
        
        if (!empty($zone_recherche)) {
            $this->zone_recherche = $zone_recherche;
        }
        
        if ($distance_max_km > 0) {
            $this->distance_max_km = $distance_max_km;
        }
        
        if (!empty($type_contrat_recherche)) {
            $this->type_contrat_recherche = $type_contrat_recherche;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Métier recherché: ".($ancien_metier ?: 'Non défini')." → ".$this->metier_recherche;
        
        if (!empty($code_rome_principal) && $code_rome_principal != $ancien_code_rome) {
            $details .= "; Code ROME: ".($ancien_code_rome ?: 'Non défini')." → ".$this->code_rome_principal;
        }
        
        if (!empty($zone_recherche) && $zone_recherche != $ancienne_zone) {
            $details .= "; Zone: ".($ancienne_zone ?: 'Non définie')." → ".$this->zone_recherche;
        }
        
        if (!empty($type_contrat_recherche) && $type_contrat_recherche != $ancien_type_contrat) {
            $details .= "; Type de contrat: ".($ancien_type_contrat ?: 'Non défini')." → ".$this->type_contrat_recherche;
        }
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_RECHERCHE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_RECHERCHE_PE';
                
                $message = 'Configuration de la recherche d\'emploi pour la démarche Pôle Emploi "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_pole_emploi',
                    $this->id,
                    $message,
                    array(
                        'metier_recherche' => array($ancien_metier, $this->metier_recherche),
                        'code_rome_principal' => array($ancien_code_rome, $this->code_rome_principal)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un PPAE (Projet Personnalisé d'Accès à l'Emploi)
     *
     * @param User   $user                 Utilisateur effectuant l'action
     * @param string $date_signature_ppae   Date de signature du PPAE (YYYY-MM-DD)
     * @param array  $plan_action           Tableau contenant le plan d'action
     * @param string $commentaire          Commentaire optionnel
     * @return int                         <0 si erreur, >0 si OK
     */
    public function enregistrerPPAE($user, $date_signature_ppae, $plan_action = array(), $commentaire = '')
    {
        // Vérifications
        if (empty($date_signature_ppae)) {
            $this->error = 'DateSignaturePPAEObligatoire';
            return -1;
        }
        
        $ancienne_date_signature = $this->date_signature_ppae;
        $ancien_plan_action = $this->plan_action;
        $ancien_ppae_actif = $this->ppae_actif;
        
        $this->date_signature_ppae = $date_signature_ppae;
        $this->ppae_actif = 1; // PPAE activé
        
        if (!empty($plan_action)) {
            $this->plan_action = json_encode($plan_action);
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "PPAE signé le " . dol_print_date($this->db->jdate($date_signature_ppae), 'day');
        $details .= "; Statut: ".($ancien_ppae_actif ? 'Actif' : 'Inactif')." → Actif";
        
        if (!empty($plan_action)) {
            $details .= "; Plan d'action mis à jour";
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_PPAE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_PPAE_PE';
                
                $message = 'Enregistrement du PPAE pour la démarche Pôle Emploi "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_pole_emploi',
                    $this->id,
                    $message,
                    array(
                        'date_signature_ppae' => array($ancienne_date_signature, $this->date_signature_ppae),
                        'ppae_actif' => array($ancien_ppae_actif, $this->ppae_actif)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une formation
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param array  $formation    Tableau contenant les informations de la formation
     * @param string $commentaire Commentaire optionnel
     * @return int                <0 si erreur, >0 si OK
     */
    public function enregistrerFormation($user, $formation, $commentaire = '')
    {
        // Vérifications
        if (empty($formation) || !is_array($formation) || empty($formation['intitule'])) {
            $this->error = 'FormationInvalide';
            return -1;
        }
        
        // Récupérer les formations existantes
        $formations = $this->getFormations();
        
        // Ajouter la nouvelle formation
        $formation['id'] = uniqid(); // ID unique pour la formation
        $formation['date_ajout'] = date('Y-m-d');
        $formations[] = $formation;
        
        // Mettre à jour le champ formations
        $this->formations = json_encode($formations);
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Formation ajoutée: ".$formation['intitule'];
        
        if (!empty($formation['date_debut']) && !empty($formation['date_fin'])) {
            $details .= " du ".dol_print_date($this->db->jdate($formation['date_debut']), 'day');
            $details .= " au ".dol_print_date($this->db->jdate($formation['date_fin']), 'day');
        }
        
        if (!empty($formation['organisme'])) {
            $details .= "; Organisme: ".$formation['organisme'];
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_FORMATION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_FORMATION_PE';
                
                $message = 'Enregistrement d\'une formation pour la démarche Pôle Emploi "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_pole_emploi',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une radiation
     *
     * @param User   $user            Utilisateur effectuant l'action
     * @param string $date_radiation   Date de radiation (YYYY-MM-DD)
     * @param string $motif_radiation  Motif de la radiation
     * @param string $commentaire     Commentaire optionnel
     * @return int                    <0 si erreur, >0 si OK
     */
    public function enregistrerRadiation($user, $date_radiation, $motif_radiation, $commentaire = '')
    {
        // Vérifications
        if (empty($date_radiation)) {
            $this->error = 'DateRadiationObligatoire';
            return -1;
        }
        
        if (empty($motif_radiation)) {
            $this->error = 'MotifRadiationObligatoire';
            return -1;
        }
        
        $ancienne_date_radiation = $this->date_radiation;
        $ancien_motif_radiation = $this->motif_radiation;
        
        $this->date_radiation = $date_radiation;
        $this->motif_radiation = $motif_radiation;
        
        // Mise à jour du statut si ce n'est pas déjà RADIEE
        if ($this->statut_demarche_pe_code != 'RADIEE') {
            $this->statut_demarche_pe_code = 'RADIEE';
            $this->statut_demarche_code = 'TERMINEE';
            $this->progression = 100;
            $this->date_cloture = dol_now();
            $this->fk_user_cloture = $user->id;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Radiation le " . dol_print_date($this->db->jdate($date_radiation), 'day');
        $details .= "; Motif: " . $this->motif_radiation;
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_RADIATION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'RADIATION_PE';
                
                $message = 'Enregistrement d\'une radiation Pôle Emploi pour "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_pole_emploi',
                    $this->id,
                    $message,
                    array(
                        'date_radiation' => array($ancienne_date_radiation, $this->date_radiation),
                        'motif_radiation' => array($ancien_motif_radiation, $this->motif_radiation)
                    )
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
                $action = 'MAJ_PIECES_PE';
                
                $message = 'Mise à jour des pièces justificatives pour la démarche Pôle Emploi "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_pole_emploi',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }

    /**
     * Récupère les formations enregistrées
     * 
     * @return array Tableau des formations
     */
    public function getFormations()
    {
        if (empty($this->formations)) {
            return array();
        }
        
        $formations = json_decode($this->formations, true);
        
        if (!is_array($formations)) {
            return array();
        }
        
        return $formations;
    }

    /**
     * Récupère le plan d'action
     * 
     * @return array Tableau du plan d'action
     */
    public function getPlanAction()
    {
        if (empty($this->plan_action)) {
            return array();
        }
        
        $plan = json_decode($this->plan_action, true);
        
        if (!is_array($plan)) {
            return array();
        }
        
        return $plan;
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
     * Génère la liste par défaut des pièces justificatives au format JSON selon le type de démarche
     * 
     * @return string Liste des pièces justificatives au format JSON
     */
    protected function getPiecesJustificativesParDefaut()
    {
        $pieces = array(
            array('libelle' => 'Pièce d\'identité', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Justificatif de domicile', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'RIB', 'obligatoire' => 1, 'fourni' => 0)
        );
        
        // Ajouter des pièces spécifiques selon le type de démarche PE
        switch ($this->type_demarche_pe_code) {
            case 'INSCRIPTION':
                $pieces[] = array('libelle' => 'CV à jour', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation employeur', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Bulletins de salaire des 12 derniers mois', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'ACTUALISATION':
                // Pas de pièces supplémentaires spécifiques
                break;
                
            case 'FORMATION':
                $pieces[] = array('libelle' => 'CV à jour', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Lettre de motivation', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation de droits Pôle Emploi', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'AIDE_FINANCIERE':
                $pieces[] = array('libelle' => 'Justificatifs de ressources', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Devis ou factures', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation de situation Pôle Emploi', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Formulaire de demande d\'aide', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'CREATION_ENTREPRISE':
                $pieces[] = array('libelle' => 'Business plan', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Étude de marché', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Plan de financement', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation de stage de préparation', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'CONTESTATION':
                $pieces[] = array('libelle' => 'Courrier de contestation', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Justificatifs appuyant la contestation', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Décision contestée', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'MOBILITE':
                $pieces[] = array('libelle' => 'Justificatifs de déplacement', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Convocation à l\'entretien', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Formulaire de demande d\'aide à la mobilité', 'obligatoire' => 1, 'fourni' => 0);
                break;
        }
        
        return json_encode($pieces);
    }

    /**
     * Ajoute une entrée à l'historique des actions spécifiques de la démarche Pôle Emploi
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
     * Ajoute une action à l'historique spécifique de la démarche Pôle Emploi
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
                        case 'ACTUALISATION':
                            $class = 'bg-success';
                            break;
                        case 'CONFIGURATION_ENTRETIEN':
                            $class = 'bg-warning';
                            break;
                        case 'MAJ_ALLOCATION':
                            $class = 'bg-primary';
                            break;
                        case 'ENREGISTREMENT_RADIATION':
                            $class = 'bg-danger';
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
     * Obtient le libellé du type de démarche Pôle Emploi
     * 
     * @return string Libellé du type de démarche
     */
    public function getTypeDemarchePELabel()
    {
        $types = self::getTypeDemarchePEOptions($this->langs);
        return isset($types[$this->type_demarche_pe_code]) ? $types[$this->type_demarche_pe_code] : $this->type_demarche_pe_code;
    }
    
    /**
     * Liste des statuts de démarche Pôle Emploi valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsDemarchePEValides()
    {
        return array(
            'A_FAIRE',              // À faire
            'DOCUMENTS_PREPARES',   // Documents préparés
            'DEMARCHE_EN_COURS',    // Démarche en cours
            'ATTENTE_REPONSE_PE',   // En attente de réponse de Pôle Emploi
            'COMPLETEE',            // Démarche complétée
            'RADIEE',               // Radiation enregistrée
            'REJETEE',              // Démarche rejetée
            'SUSPENDUE'             // Démarche suspendue
        );
    }
    
    /**
     * Liste des types de démarche Pôle Emploi valides
     *
     * @return array Codes des types de démarche valides
     */
    public static function getTypesDemarchePEValides()
    {
        return array(
            'INSCRIPTION',          // Inscription à Pôle Emploi
            'ACTUALISATION',        // Actualisation mensuelle
            'RENDEZ_VOUS',          // Rendez-vous avec conseiller
            'FORMATION',            // Demande de formation
            'AIDE_FINANCIERE',      // Demande d'aide financière
            'ATTESTATION',          // Demande d'attestation
            'CREATION_ENTREPRISE',  // Accompagnement création d'entreprise
            'INDEMNISATION',        // Demande d'indemnisation
            'CONTESTATION',         // Contestation de décision
            'MOBILITE',             // Aide à la mobilité
            'SUIVI_PPAE',           // Suivi du PPAE
            'RADIATION',            // Gestion d'une radiation
            'CHANGEMENT_SITUATION', // Changement de situation
            'AUTRE'                 // Autre type de démarche
        );
    }
    
    /**
     * Liste des catégories de demandeur d'emploi valides
     *
     * @return array Codes des catégories valides
     */
    public static function getCategoriesDemandeurValides()
    {
        return array(
            'A', // Sans emploi, tenu d'accomplir des actes positifs de recherche d'emploi, à la recherche d'un emploi à temps plein à durée indéterminée
            'B', // Sans emploi, tenu d'accomplir des actes positifs de recherche d'emploi, à la recherche d'un emploi à temps partiel ou à durée déterminée
            'C', // Activité réduite, tenu d'accomplir des actes positifs de recherche d'emploi
            'D', // Sans emploi, non tenu d'accomplir des actes positifs de recherche d'emploi (stage, formation, maladie)
            'E'  // En emploi, non tenu d'accomplir des actes positifs de recherche d'emploi
        );
    }
    
    /**
     * Liste des types d'allocations valides
     *
     * @return array Codes des types d'allocations valides
     */
    public static function getTypesAllocationValides()
    {
        return array(
            'ARE',      // Allocation d'Aide au Retour à l'Emploi
            'ASS',      // Allocation de Solidarité Spécifique
            'AREF',     // Allocation d'Aide au Retour à l'Emploi Formation
            'RFF',      // Rémunération de Fin de Formation
            'AER',      // Allocation Équivalent Retraite
            'ATA',      // Allocation Temporaire d'Attente
            'AFD',      // Allocation de Fin de Droits
            'APS',      // Allocation Préretraite Progressive
            'AUD',      // Allocation Unique Dégressive
            'ARCE',     // Aide à la Reprise ou à la Création d'Entreprise
            'NON_INDEMNISE' // Non indemnisé
        );
    }
    
    /**
     * Récupère les options du dictionnaire des types de démarche PE
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeDemarchePEOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'pole_emploi_type_demarche', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts de démarche PE
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutDemarchePEOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'pole_emploi_statut_demarche', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types d'allocation
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeAllocationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'pole_emploi_type_allocation', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de contrat
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeContratOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'pole_emploi_type_contrat', $usekeys, $show_empty);
    }
    
    /**
     * Vérifie si cette démarche spécifique est une démarche Pôle Emploi
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isPoleEmploi()
    {
        return true;
    }
    
    /**
     * Récupère le contact conseiller Pôle Emploi associé à la démarche
     * 
     * @return Contact|null Contact ou null si non trouvé
     */
    public function getContactConseiller()
    {
        if (empty($this->fk_contact_conseiller)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
        $contact = new Contact($this->db);
        
        if ($contact->fetch($this->fk_contact_conseiller) > 0) {
            return $contact;
        }
        
        return null;
    }
    
    /**
     * Définit des constantes pour les types d'actions standards dans l'historique
     */
    const ACTION_CHANGEMENT_STATUT_DEMARCHE = 'CHANGEMENT_STATUT_DEMARCHE';
    const ACTION_ACTUALISATION = 'ACTUALISATION';
    const ACTION_CONFIGURATION_ENTRETIEN = 'CONFIGURATION_ENTRETIEN';
    const ACTION_MAJ_ALLOCATION = 'MAJ_ALLOCATION';
    const ACTION_CONFIGURATION_RECHERCHE = 'CONFIGURATION_RECHERCHE';
    const ACTION_ENREGISTREMENT_PPAE = 'ENREGISTREMENT_PPAE';
    const ACTION_ENREGISTREMENT_FORMATION = 'ENREGISTREMENT_FORMATION';
    const ACTION_ENREGISTREMENT_RADIATION = 'ENREGISTREMENT_RADIATION';
    const ACTION_MISE_A_JOUR_PIECES = 'MISE_A_JOUR_PIECES';
    const ACTION_CONTACT_CONSEILLER = 'CONTACT_CONSEILLER';
    const ACTION_NOTE_INTERNE = 'NOTE_INTERNE';
}

} // Fin de la condition if !class_exists
