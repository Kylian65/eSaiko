<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches bancaires des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarcheBancaire', false)) {

class ElaskaParticulierDemarcheBancaire extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_bancaire';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_bancaire';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AUX DÉMARCHES BANCAIRES
    //
    
    /**
     * @var string Code du type de démarche bancaire (dictionnaire)
     */
    public $type_bancaire_code;
    
    /**
     * @var string Code du statut de la démarche bancaire (dictionnaire)
     */
    public $statut_bancaire_code;
    
    /**
     * @var string Nom de l'établissement bancaire
     */
    public $nom_etablissement;
    
    /**
     * @var string Code de l'établissement bancaire (dictionnaire)
     */
    public $etablissement_code;
    
    /**
     * @var string Numéro de l'agence bancaire
     */
    public $numero_agence;
    
    /**
     * @var string Adresse de l'agence bancaire
     */
    public $adresse_agence;
    
    /**
     * @var string Numéro de téléphone de l'agence
     */
    public $telephone_agence;
    
    /**
     * @var string Email du conseiller
     */
    public $email_conseiller;
    
    /**
     * @var string Numéro de compte bancaire (format IBAN)
     */
    public $numero_compte;
    
    /**
     * @var string Type de compte (courant, épargne, etc.)
     */
    public $type_compte;
    
    /**
     * @var double Montant demandé (pour crédits)
     */
    public $montant_demande;
    
    /**
     * @var double Taux d'intérêt (pour crédits)
     */
    public $taux_interet;
    
    /**
     * @var int Durée en mois (pour crédits)
     */
    public $duree_mois;
    
    /**
     * @var double Montant de la mensualité (pour crédits)
     */
    public $montant_mensualite;
    
    /**
     * @var string Date de la demande (format YYYY-MM-DD)
     */
    public $date_demande;
    
    /**
     * @var string Date de décision (format YYYY-MM-DD)
     */
    public $date_decision;
    
    /**
     * @var string Date de début du contrat (format YYYY-MM-DD)
     */
    public $date_debut_contrat;
    
    /**
     * @var string Date de fin du contrat (format YYYY-MM-DD)
     */
    public $date_fin_contrat;
    
    /**
     * @var string Numéro de dossier banque
     */
    public $numero_dossier;
    
    /**
     * @var int ID du contact conseiller bancaire
     */
    public $fk_contact_conseiller;
    
    /**
     * @var double Taux d'endettement calculé
     */
    public $taux_endettement;
    
    /**
     * @var string TAEG (Taux Annuel Effectif Global)
     */
    public $taeg;
    
    /**
     * @var int Délai de réflexion/rétractation en jours
     */
    public $delai_retractation_jours;
    
    /**
     * @var string Garanties demandées (texte)
     */
    public $garanties;
    
    /**
     * @var string Assurance emprunteur (0=non, 1=oui)
     */
    public $assurance_emprunteur;
    
    /**
     * @var double Coût de l'assurance emprunteur mensuel
     */
    public $cout_assurance_mensuel;
    
    /**
     * @var string Code du type d'assurance emprunteur (dictionnaire)
     */
    public $type_assurance_code;
    
    /**
     * @var string Motif de refus en cas de rejet
     */
    public $motif_refus;
    
    /**
     * @var string Historique des actions spécifiques à la démarche bancaire
     */
    public $historique_actions;
    
    /**
     * @var string Pièces justificatives à fournir (format JSON)
     */
    public $pieces_justificatives;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_bancaire = array(
        'type_bancaire_code' => array('type' => 'varchar(50)', 'label' => 'TypeBancaire', 'enabled' => 1, 'position' => 1100, 'notnull' => 1, 'visible' => 1),
        'statut_bancaire_code' => array('type' => 'varchar(50)', 'label' => 'StatutBancaire', 'enabled' => 1, 'position' => 1110, 'notnull' => 1, 'visible' => 1),
        'nom_etablissement' => array('type' => 'varchar(255)', 'label' => 'NomEtablissement', 'enabled' => 1, 'position' => 1120, 'notnull' => 0, 'visible' => 1),
        'etablissement_code' => array('type' => 'varchar(50)', 'label' => 'CodeEtablissement', 'enabled' => 1, 'position' => 1130, 'notnull' => 0, 'visible' => 1),
        'numero_agence' => array('type' => 'varchar(50)', 'label' => 'NumeroAgence', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'adresse_agence' => array('type' => 'varchar(255)', 'label' => 'AdresseAgence', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'telephone_agence' => array('type' => 'varchar(20)', 'label' => 'TelephoneAgence', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'email_conseiller' => array('type' => 'varchar(255)', 'label' => 'EmailConseiller', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'numero_compte' => array('type' => 'varchar(50)', 'label' => 'NumeroCompte', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'type_compte' => array('type' => 'varchar(50)', 'label' => 'TypeCompte', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'montant_demande' => array('type' => 'double(24,8)', 'label' => 'MontantDemande', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'taux_interet' => array('type' => 'double(8,4)', 'label' => 'TauxInteret', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'duree_mois' => array('type' => 'integer', 'label' => 'DureeMois', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'montant_mensualite' => array('type' => 'double(24,8)', 'label' => 'MontantMensualite', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'date_demande' => array('type' => 'date', 'label' => 'DateDemande', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'date_decision' => array('type' => 'date', 'label' => 'DateDecision', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'date_debut_contrat' => array('type' => 'date', 'label' => 'DateDebutContrat', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1),
        'date_fin_contrat' => array('type' => 'date', 'label' => 'DateFinContrat', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'numero_dossier' => array('type' => 'varchar(50)', 'label' => 'NumeroDossier', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'fk_contact_conseiller' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactConseiller', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1),
        'taux_endettement' => array('type' => 'double(8,2)', 'label' => 'TauxEndettement', 'enabled' => 1, 'position' => 1300, 'notnull' => 0, 'visible' => 1),
        'taeg' => array('type' => 'varchar(20)', 'label' => 'TAEG', 'enabled' => 1, 'position' => 1310, 'notnull' => 0, 'visible' => 1),
        'delai_retractation_jours' => array('type' => 'integer', 'label' => 'DelaiRetractationJours', 'enabled' => 1, 'position' => 1320, 'notnull' => 0, 'visible' => 1),
        'garanties' => array('type' => 'text', 'label' => 'Garanties', 'enabled' => 1, 'position' => 1330, 'notnull' => 0, 'visible' => 1),
        'assurance_emprunteur' => array('type' => 'boolean', 'label' => 'AssuranceEmprunteur', 'enabled' => 1, 'position' => 1340, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'cout_assurance_mensuel' => array('type' => 'double(24,8)', 'label' => 'CoutAssuranceMensuel', 'enabled' => 1, 'position' => 1350, 'notnull' => 0, 'visible' => 1),
        'type_assurance_code' => array('type' => 'varchar(50)', 'label' => 'TypeAssurance', 'enabled' => 1, 'position' => 1360, 'notnull' => 0, 'visible' => 1),
        'motif_refus' => array('type' => 'text', 'label' => 'MotifRefus', 'enabled' => 1, 'position' => 1370, 'notnull' => 0, 'visible' => 1),
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
        
        // Fusion des champs spécifiques bancaires avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_bancaire);
        
        // Valeurs par défaut spécifiques aux démarches bancaires
        $this->type_demarche_code = 'BANCAIRE';  // Force le code de type de démarche
        if (!isset($this->statut_bancaire_code)) $this->statut_bancaire_code = 'PREPARATION'; // Statut par défaut
        if (!isset($this->assurance_emprunteur)) $this->assurance_emprunteur = 0; // Par défaut pas d'assurance
        if (!isset($this->delai_retractation_jours)) $this->delai_retractation_jours = 14; // 14 jours par défaut (standard légal)
    }

    /**
     * Crée une démarche bancaire dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à BANCAIRE
        $this->type_demarche_code = 'BANCAIRE';
        
        // Vérifications spécifiques aux démarches bancaires
        if (empty($this->type_bancaire_code)) {
            $this->error = 'TypeBancaireIsMandatory';
            return -1;
        }
        
        // Vérification du montant demandé pour les crédits
        if (in_array($this->type_bancaire_code, array('CREDIT_CONSO', 'CREDIT_AUTO', 'CREDIT_IMMOBILIER')) && empty($this->montant_demande)) {
            $this->error = 'MontantDemandeIsMandatory';
            return -1;
        }
        
        // Génération automatique du libellé s'il n'est pas fourni
        if (empty($this->libelle)) {
            $this->libelle = $this->getTypeBancaireLabel();
            if (!empty($this->nom_etablissement)) {
                $this->libelle .= ' - ' . $this->nom_etablissement;
            }
            if (!empty($this->montant_demande) && $this->montant_demande > 0) {
                $this->libelle .= ' - ' . price($this->montant_demande, 0, '', 1, -1, -1, 'EUR');
            }
        }
        
        // Date de demande par défaut = aujourd'hui si non renseignée
        if (empty($this->date_demande)) {
            $this->date_demande = date('Y-m-d');
        }
        
        // Initialisation des pièces justificatives
        if (empty($this->pieces_justificatives)) {
            $this->pieces_justificatives = $this->getPiecesJustificativesParDefaut();
        }
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'une démarche bancaire
            $note = "Création d'une démarche bancaire de type " . $this->getTypeBancaireLabel();
            if (!empty($this->nom_etablissement)) {
                $note .= " auprès de " . $this->nom_etablissement;
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
     * Met à jour une démarche bancaire dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à BANCAIRE
        $this->type_demarche_code = 'BANCAIRE';
        
        // Vérifications spécifiques aux démarches bancaires
        if (empty($this->type_bancaire_code)) {
            $this->error = 'TypeBancaireIsMandatory';
            return -1;
        }
        
        // Vérification de l'email du conseiller
        if (!empty($this->email_conseiller) && !filter_var($this->email_conseiller, FILTER_VALIDATE_EMAIL)) {
            $this->error = 'InvalidEmailFormat';
            return -1;
        }
        
        // Vérification de l'IBAN si fourni
        if (!empty($this->numero_compte) && !$this->isValidIBAN($this->numero_compte)) {
            $this->error = 'InvalidIBANFormat';
            return -1;
        }
        
        // Vérification de la cohérence des dates
        if (!empty($this->date_debut_contrat) && !empty($this->date_fin_contrat) && $this->date_fin_contrat < $this->date_debut_contrat) {
            $this->error = 'DateFinContratCantBeBeforeDateDebutContrat';
            return -1;
        }
        
        // Calcul automatique de la mensualité si nécessaire
        if (!empty($this->montant_demande) && !empty($this->taux_interet) && !empty($this->duree_mois) && empty($this->montant_mensualite)) {
            $this->montant_mensualite = $this->calculerMensualite();
        }
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le statut de la démarche bancaire
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau code de statut
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function changerStatutBancaire($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsBancaireValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatutBancaireCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_bancaire_code;
        $this->statut_bancaire_code = $statut_code;
        
        // Mises à jour automatiques selon le statut
        switch ($statut_code) {
            case 'DOSSIER_DEPOSE':
                if (empty($this->date_demande)) {
                    $this->date_demande = date('Y-m-d');
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 30; // 30% de progression
                break;
                
            case 'ANALYSE_EN_COURS':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 50; // 50% de progression
                break;
                
            case 'PIECES_MANQUANTES':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 40; // 40% de progression
                break;
                
            case 'PROPOSITION_RECUE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 70; // 70% de progression
                break;
                
            case 'ACCORD_CLIENT':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 80; // 80% de progression
                break;
                
            case 'ACCEPTE':
                if (empty($this->date_decision)) {
                    $this->date_decision = date('Y-m-d');
                }
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'REFUSE':
                if (empty($this->date_decision)) {
                    $this->date_decision = date('Y-m-d');
                }
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'ANNULE':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'EN_ATTENTE_SIGNATURE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 85; // 85% de progression
                break;
                
            case 'CONTRAT_SIGNE':
                if (empty($this->date_debut_contrat)) {
                    $this->date_debut_contrat = date('Y-m-d');
                }
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
        $this->ajouterActionHistorique($user, 'CHANGEMENT_STATUT', $ancien_statut . ' → ' . $statut_code, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CHANGEMENT_STATUT_BANCAIRE';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutBancaireOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche bancaire "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_bancaire',
                    $this->id,
                    $message,
                    array('statut_bancaire_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure les informations de l'établissement bancaire
     *
     * @param User   $user              Utilisateur effectuant l'action
     * @param string $nom_etablissement Nom de l'établissement bancaire
     * @param string $etablissement_code Code de l'établissement (optionnel)
     * @param string $numero_agence     Numéro de l'agence (optionnel)
     * @param string $adresse_agence    Adresse de l'agence (optionnel)
     * @param string $telephone_agence  Téléphone de l'agence (optionnel)
     * @param string $commentaire      Commentaire optionnel
     * @return int                      <0 si erreur, >0 si OK
     */
    public function configurerEtablissement($user, $nom_etablissement, $etablissement_code = '', $numero_agence = '', $adresse_agence = '', $telephone_agence = '', $commentaire = '')
    {
        // Vérifications
        if (empty($nom_etablissement)) {
            $this->error = 'NomEtablissementObligatoire';
            return -1;
        }
        
        $ancien_nom = $this->nom_etablissement;
        $ancien_code = $this->etablissement_code;
        $ancien_numero = $this->numero_agence;
        $ancienne_adresse = $this->adresse_agence;
        $ancien_telephone = $this->telephone_agence;
        
        $this->nom_etablissement = $nom_etablissement;
        
        if (!empty($etablissement_code)) {
            $this->etablissement_code = $etablissement_code;
        }
        
        if (!empty($numero_agence)) {
            $this->numero_agence = $numero_agence;
        }
        
        if (!empty($adresse_agence)) {
            $this->adresse_agence = $adresse_agence;
        }
        
        if (!empty($telephone_agence)) {
            $this->telephone_agence = $telephone_agence;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Établissement: ".($ancien_nom ?: 'Non défini')." → ".$this->nom_etablissement;
        
        if (!empty($etablissement_code) && $etablissement_code != $ancien_code) {
            $details .= "; Code: ".($ancien_code ?: 'Non défini')." → ".$this->etablissement_code;
        }
        
        if (!empty($numero_agence) && $numero_agence != $ancien_numero) {
            $details .= "; N° Agence: ".($ancien_numero ?: 'Non défini')." → ".$this->numero_agence;
        }
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_ETABLISSEMENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_ETABLISSEMENT_BANCAIRE';
                
                $message = 'Configuration de l\'établissement bancaire pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_bancaire',
                    $this->id,
                    $message,
                    array('nom_etablissement' => array($ancien_nom, $this->nom_etablissement))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure les informations du compte bancaire
     *
     * @param User   $user           Utilisateur effectuant l'action
     * @param string $numero_compte  Numéro de compte (IBAN)
     * @param string $type_compte    Type de compte
     * @param string $commentaire   Commentaire optionnel
     * @return int                   <0 si erreur, >0 si OK
     */
    public function configurerCompte($user, $numero_compte, $type_compte, $commentaire = '')
    {
        // Vérifications
        if (!empty($numero_compte) && !$this->isValidIBAN($numero_compte)) {
            $this->error = 'InvalidIBANFormat';
            return -1;
        }
        
        $ancien_numero = $this->numero_compte;
        $ancien_type = $this->type_compte;
        
        $this->numero_compte = $numero_compte;
        $this->type_compte = $type_compte;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Numéro de compte: ";
        $details .= (!empty($ancien_numero) ? $this->maskIBAN($ancien_numero) : 'Non défini');
        $details .= " → ";
        $details .= (!empty($this->numero_compte) ? $this->maskIBAN($this->numero_compte) : 'Non défini');
        $details .= "; Type: ".($ancien_type ?: 'Non défini')." → ".$this->type_compte;
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_COMPTE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_COMPTE_BANCAIRE';
                
                $message = 'Configuration du compte bancaire pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_bancaire',
                    $this->id,
                    $message,
                    array('type_compte' => array($ancien_type, $this->type_compte))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure les informations du crédit
     *
     * @param User   $user              Utilisateur effectuant l'action
     * @param double $montant_demande    Montant demandé
     * @param double $taux_interet       Taux d'intérêt
     * @param int    $duree_mois         Durée en mois
     * @param string $taeg               TAEG (optionnel)
     * @param string $commentaire       Commentaire optionnel
     * @return int                       <0 si erreur, >0 si OK
     */
    public function configurerCredit($user, $montant_demande, $taux_interet, $duree_mois, $taeg = '', $commentaire = '')
    {
        // Vérifications
        if ($montant_demande <= 0) {
            $this->error = 'MontantDemandeDoitEtrePositif';
            return -1;
        }
        
        if ($taux_interet < 0) {
            $this->error = 'TauxInteretDoitEtrePositifOuZero';
            return -1;
        }
        
        if ($duree_mois <= 0) {
            $this->error = 'DureeMoisDoitEtrePositif';
            return -1;
        }
        
        $ancien_montant = $this->montant_demande;
        $ancien_taux = $this->taux_interet;
        $ancienne_duree = $this->duree_mois;
        $ancien_taeg = $this->taeg;
        $ancienne_mensualite = $this->montant_mensualite;
        
        $this->montant_demande = $montant_demande;
        $this->taux_interet = $taux_interet;
        $this->duree_mois = $duree_mois;
        
        if (!empty($taeg)) {
            $this->taeg = $taeg;
        }
        
        // Calcul automatique de la mensualité
        $this->montant_mensualite = $this->calculerMensualite();
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Montant: ".price($ancien_montant)." → ".price($this->montant_demande);
        $details .= "; Taux: ".($ancien_taux !== null ? $ancien_taux.'%' : 'Non défini');
        $details .= " → ".$this->taux_interet.'%';
        $details .= "; Durée: ".($ancienne_duree ? $ancienne_duree.' mois' : 'Non définie');
        $details .= " → ".$this->duree_mois.' mois';
        $details .= "; Mensualité calculée: ".price($this->montant_mensualite);
        
        if (!empty($taeg) && $taeg != $ancien_taeg) {
            $details .= "; TAEG: ".($ancien_taeg ?: 'Non défini')." → ".$this->taeg;
        }
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_CREDIT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_CREDIT_BANCAIRE';
                
                $message = 'Configuration du crédit pour la démarche bancaire "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_bancaire',
                    $this->id,
                    $message,
                    array(
                        'montant_demande' => array($ancien_montant, $this->montant_demande),
                        'taux_interet' => array($ancien_taux, $this->taux_interet),
                        'duree_mois' => array($ancienne_duree, $this->duree_mois)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure les informations d'assurance emprunteur
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param int    $assurance_emprunteur   Assurance emprunteur (0=non, 1=oui)
     * @param double $cout_assurance_mensuel Coût mensuel de l'assurance
     * @param string $type_assurance_code    Code du type d'assurance
     * @param string $commentaire           Commentaire optionnel
     * @return int                           <0 si erreur, >0 si OK
     */
    public function configurerAssurance($user, $assurance_emprunteur, $cout_assurance_mensuel = 0, $type_assurance_code = '', $commentaire = '')
    {
        // Vérifications
        if ($assurance_emprunteur && $cout_assurance_mensuel < 0) {
            $this->error = 'CoutAssuranceMensuelDoitEtrePositifOuZero';
            return -1;
        }
        
        $ancien_assurance = $this->assurance_emprunteur;
        $ancien_cout = $this->cout_assurance_mensuel;
        $ancien_type = $this->type_assurance_code;
        
        $this->assurance_emprunteur = $assurance_emprunteur ? 1 : 0;
        
        if ($this->assurance_emprunteur) {
            $this->cout_assurance_mensuel = $cout_assurance_mensuel;
            $this->type_assurance_code = $type_assurance_code;
        } else {
            // Si pas d'assurance, remise à zéro des autres champs
            $this->cout_assurance_mensuel = 0;
            $this->type_assurance_code = '';
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Assurance emprunteur: ".($ancien_assurance ? 'Oui' : 'Non')." → ".($this->assurance_emprunteur ? 'Oui' : 'Non');
        
        if ($this->assurance_emprunteur) {
            $details .= "; Coût mensuel: ".price($ancien_cout)." → ".price($this->cout_assurance_mensuel);
            
            if (!empty($type_assurance_code)) {
                $details .= "; Type: ".($ancien_type ?: 'Non défini')." → ".$this->type_assurance_code;
            }
        }
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_ASSURANCE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_ASSURANCE_BANCAIRE';
                
                $message = 'Configuration de l\'assurance pour la démarche bancaire "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_bancaire',
                    $this->id,
                    $message,
                    array('assurance_emprunteur' => array($ancien_assurance, $this->assurance_emprunteur))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un refus de crédit
     *
     * @param User   $user         Utilisateur effectuant l'action
     * @param string $motif_refus  Motif du refus
     * @param string $commentaire  Commentaire optionnel
     * @return int                 <0 si erreur, >0 si OK
     */
    public function enregistrerRefus($user, $motif_refus, $commentaire = '')
    {
        // Vérifications
        if (empty($motif_refus)) {
            $this->error = 'MotifRefusObligatoire';
            return -1;
        }
        
        $ancien_motif = $this->motif_refus;
        $this->motif_refus = $motif_refus;
        
        // Mise à jour du statut
        if ($this->statut_bancaire_code != 'REFUSE') {
            $this->statut_bancaire_code = 'REFUSE';
            
            if (empty($this->date_decision)) {
                $this->date_decision = date('Y-m-d');
            }
            
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
        $details = "Demande refusée";
        $details .= "; Motif: ".$this->motif_refus;
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_REFUS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'REFUS_BANCAIRE';
                
                $message = 'Enregistrement d\'un refus pour la démarche bancaire "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_bancaire',
                    $this->id,
                    $message,
                    array('motif_refus' => array($ancien_motif, $this->motif_refus))
                );
            }
        }
        
        return $result;
    }

    /**
     * Calcule la mensualité du crédit
     * 
     * @return double Montant de la mensualité
     */
    protected function calculerMensualite()
    {
        // Si les paramètres ne sont pas définis, impossible de calculer
        if (empty($this->montant_demande) || empty($this->duree_mois) || $this->taux_interet === null) {
            return 0;
        }
        
        // Si taux nul, simple division
        if ($this->taux_interet == 0) {
            return $this->montant_demande / $this->duree_mois;
        }
        
        // Calcul avec la formule standard de crédit amortissable
        // M = P * (t/12) / (1 - (1 + t/12)^-n)
        // Où M = mensualité, P = principal, t = taux annuel en décimal, n = nombre de mois
        $taux_mensuel = $this->taux_interet / 100 / 12;
        $denominateur = 1 - pow(1 + $taux_mensuel, -$this->duree_mois);
        
        if ($denominateur == 0) {
            return 0; // Éviter division par zéro
        }
        
        $mensualite = $this->montant_demande * $taux_mensuel / $denominateur;
        
        return $mensualite;
    }

    /**
     * Vérifie la validité d'un IBAN
     *
     * @param string $iban IBAN à vérifier
     * @return bool       True si valide, false sinon
     */
    protected function isValidIBAN($iban)
    {
        // Simplification - en réalité, une validation IBAN complète nécessiterait 
        // un algorithme plus complexe avec la vérification de la clé de contrôle
        $iban = str_replace(' ', '', strtoupper($iban));
        
        // Vérification de base de la structure
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{10,30}$/', $iban)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Masque un IBAN pour l'affichage sécurisé
     * 
     * @param string $iban IBAN à masquer
     * @return string      IBAN masqué (seuls les 4 derniers caractères visibles)
     */
    protected function maskIBAN($iban)
    {
        $iban = str_replace(' ', '', $iban);
        $length = strlen($iban);
        
        if ($length <= 4) {
            return $iban;
        }
        
        return str_repeat('X', $length - 4) . substr($iban, -4);
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
                $action = 'MAJ_PIECES_BANCAIRE';
                
                $message = 'Mise à jour des pièces justificatives pour la démarche bancaire "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_bancaire',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
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
            array('libelle' => 'RIB', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Dernier avis d\'imposition', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => '3 derniers bulletins de salaire', 'obligatoire' => 1, 'fourni' => 0)
        );
        
        // Ajouter des pièces spécifiques selon le type de démarche bancaire
        switch ($this->type_bancaire_code) {
            case 'OUVERTURE_COMPTE':
                $pieces[] = array('libelle' => 'Formulaire d\'ouverture de compte', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'CREDIT_CONSO':
                $pieces[] = array('libelle' => 'Formulaire de demande de crédit', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Relevés de compte des 3 derniers mois', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'CREDIT_IMMOBILIER':
                $pieces[] = array('libelle' => 'Compromis de vente', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Plan de financement', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Relevés de compte des 3 derniers mois', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Justificatif d\'apport personnel', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'CREDIT_AUTO':
                $pieces[] = array('libelle' => 'Bon de commande véhicule', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Relevés de compte des 3 derniers mois', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'GESTION_PATRIMOINE':
                $pieces[] = array('libelle' => 'État du patrimoine', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Objectifs d\'investissement', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'MOBILITE_BANCAIRE':
                $pieces[] = array('libelle' => 'Mandat de mobilité bancaire', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Liste des organismes à prévenir', 'obligatoire' => 1, 'fourni' => 0);
                break;
        }
        
        return json_encode($pieces);
    }

    /**
     * Ajoute une entrée à l'historique des actions spécifiques de la démarche bancaire
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
     * Ajoute une action à l'historique spécifique de la démarche bancaire
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
                        case 'CHANGEMENT_STATUT':
                            $class = 'bg-info';
                            break;
                        case 'CONFIGURATION_ETABLISSEMENT':
                            $class = 'bg-success';
                            break;
                        case 'CONFIGURATION_COMPTE':
                            $class = 'bg-primary';
                            break;
                        case 'CONFIGURATION_CREDIT':
                            $class = 'bg-warning';
                            break;
                        case 'ENREGISTREMENT_REFUS':
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
     * Obtient le libellé du type de démarche bancaire
     * 
     * @return string Libellé du type de démarche bancaire
     */
    public function getTypeBancaireLabel()
    {
        $types = self::getTypeBancaireOptions($this->langs);
        return isset($types[$this->type_bancaire_code]) ? $types[$this->type_bancaire_code] : $this->type_bancaire_code;
    }
    
    /**
     * Obtient le libellé du statut bancaire
     * 
     * @return string Libellé du statut
     */
    public function getStatutBancaireLabel()
    {
        $statuts = self::getStatutBancaireOptions($this->langs);
        return isset($statuts[$this->statut_bancaire_code]) ? $statuts[$this->statut_bancaire_code] : $this->statut_bancaire_code;
    }
    
    /**
     * Liste des statuts bancaires valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsBancaireValides()
    {
        return array(
            'PREPARATION',          // Préparation du dossier
            'DOSSIER_DEPOSE',       // Dossier déposé auprès de l'établissement
            'PIECES_MANQUANTES',    // Pièces manquantes à fournir
            'ANALYSE_EN_COURS',     // Analyse du dossier en cours
            'PROPOSITION_RECUE',    // Proposition reçue de l'établissement
            'ACCORD_CLIENT',        // Accord du client sur la proposition
            'EN_ATTENTE_SIGNATURE', // En attente de signature
            'CONTRAT_SIGNE',        // Contrat signé
            'ACCEPTE',              // Demande acceptée
            'REFUSE',               // Demande refusée
            'ANNULE'                // Annulé par le client
        );
    }
    
    /**
     * Liste des types de démarche bancaire valides
     *
     * @return array Codes des types de démarche valides
     */
    public static function getTypesBancaireValides()
    {
        return array(
            'OUVERTURE_COMPTE',      // Ouverture de compte
            'CREDIT_CONSO',          // Crédit à la consommation
            'CREDIT_AUTO',           // Crédit auto
            'CREDIT_IMMOBILIER',     // Crédit immobilier
            'EPARGNE',               // Épargne
            'ASSURANCE_VIE',         // Assurance-vie
            'GESTION_PATRIMOINE',    // Conseil en gestion de patrimoine
            'MOBILITE_BANCAIRE',     // Mobilité bancaire (changement de banque)
            'CARTE_BANCAIRE',        // Demande de carte bancaire
            'MOYENS_PAIEMENT',       // Autres moyens de paiement
            'VIREMENT_PERMANENT',    // Mise en place de virements permanents
            'AUTORISATION_DECOUVERTE', // Demande d'autorisation de découvert
            'INCIDENT_BANCAIRE',     // Gestion d'un incident bancaire
            'INVESTISSEMENT',        // Investissement
            'AUTRE'                  // Autre type de démarche bancaire
        );
    }
    
    /**
     * Liste des types d'assurance emprunteur valides
     *
     * @return array Codes des types d'assurance valides
     */
    public static function getTypesAssuranceValides()
    {
        return array(
            'GROUPE',           // Assurance groupe de la banque
            'EXTERNE',          // Assurance externe (délégation)
            'PARTIELLE',        // Assurance partielle
            'SANS_ASSURANCE'    // Sans assurance (cas rares)
        );
    }
    
    /**
     * Récupère les options du dictionnaire des types de démarche bancaire
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeBancaireOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'bancaire_type', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts bancaires
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutBancaireOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'bancaire_statut', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des établissements bancaires
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getEtablissementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'bancaire_etablissement', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types d'assurance emprunteur
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeAssuranceOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'bancaire_type_assurance', $usekeys, $show_empty);
    }
    
    /**
     * Vérifie si cette démarche spécifique est une démarche bancaire
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isBancaire()
    {
        return true;
    }
    
    /**
     * Récupère le contact conseiller bancaire associé à la démarche
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
    const ACTION_CHANGEMENT_STATUT = 'CHANGEMENT_STATUT';
    const ACTION_CONFIGURATION_ETABLISSEMENT = 'CONFIGURATION_ETABLISSEMENT';
    const ACTION_CONFIGURATION_COMPTE = 'CONFIGURATION_COMPTE';
    const ACTION_CONFIGURATION_CREDIT = 'CONFIGURATION_CREDIT';
    const ACTION_CONFIGURATION_ASSURANCE = 'CONFIGURATION_ASSURANCE';
    const ACTION_ENREGISTREMENT_REFUS = 'ENREGISTREMENT_REFUS';
    const ACTION_MISE_A_JOUR_PIECES = 'MISE_A_JOUR_PIECES';
    const ACTION_CONTACT_CONSEILLER = 'CONTACT_CONSEILLER';
    const ACTION_AJOUT_DOCUMENT = 'AJOUT_DOCUMENT';
    const ACTION_NOTE_INTERNE = 'NOTE_INTERNE';
}

} // Fin de la condition if !class_exists
