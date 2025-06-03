<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches fiscales des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarcheImpot', false)) {

class ElaskaParticulierDemarcheImpot extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_impot';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_impot';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AUX DÉMARCHES FISCALES
    //
    
    /**
     * @var string Numéro fiscal du contribuable
     */
    public $numero_fiscal;
    
    /**
     * @var string Numéro de déclarant en ligne
     */
    public $numero_declarant;
    
    /**
     * @var string Code du type de déclaration (dictionnaire)
     */
    public $type_declaration_code;
    
    /**
     * @var string Code du statut de la déclaration (dictionnaire)
     */
    public $statut_declaration_code;
    
    /**
     * @var int Année fiscale concernée (format YYYY)
     */
    public $annee_fiscale;
    
    /**
     * @var string Date limite de déclaration (format YYYY-MM-DD)
     */
    public $date_limite_declaration;
    
    /**
     * @var string Date d'envoi de la déclaration (format YYYY-MM-DD)
     */
    public $date_envoi_declaration;
    
    /**
     * @var double Revenu fiscal de référence
     */
    public $revenu_fiscal_reference;
    
    /**
     * @var double Montant de l'impôt dû
     */
    public $montant_impot_du;
    
    /**
     * @var double Montant estimé de l'impôt (avant calcul définitif)
     */
    public $montant_impot_estime;
    
    /**
     * @var string Date de l'avis d'imposition (format YYYY-MM-DD)
     */
    public $date_avis_imposition;
    
    /**
     * @var string Numéro de l'avis d'imposition
     */
    public $numero_avis_imposition;
    
    /**
     * @var int Flag indiquant si dégrèvement demandé (0=non, 1=oui)
     */
    public $degrevement_demande;
    
    /**
     * @var string Motif du dégrèvement
     */
    public $motif_degrevement;
    
    /**
     * @var double Montant du dégrèvement demandé
     */
    public $montant_degrevement;
    
    /**
     * @var string Statut du dégrèvement (en attente, accordé, refusé)
     */
    public $statut_degrevement;
    
    /**
     * @var string Code du mode de paiement (mensuel, trimestriel, annuel)
     */
    public $mode_paiement_code;
    
    /**
     * @var int ID du contact au centre des impôts
     */
    public $fk_contact_impots;
    
    /**
     * @var string Centre des impôts de rattachement
     */
    public $centre_impots;
    
    /**
     * @var string Historique des actions spécifiques à la démarche fiscale
     */
    public $historique_actions;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_impot = array(
        'numero_fiscal' => array('type' => 'varchar(50)', 'label' => 'NumeroFiscal', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'numero_declarant' => array('type' => 'varchar(50)', 'label' => 'NumeroDeclarant', 'enabled' => 1, 'position' => 1110, 'notnull' => 0, 'visible' => 1),
        'type_declaration_code' => array('type' => 'varchar(50)', 'label' => 'TypeDeclaration', 'enabled' => 1, 'position' => 1120, 'notnull' => 1, 'visible' => 1),
        'statut_declaration_code' => array('type' => 'varchar(50)', 'label' => 'StatutDeclaration', 'enabled' => 1, 'position' => 1130, 'notnull' => 1, 'visible' => 1),
        'annee_fiscale' => array('type' => 'integer', 'label' => 'AnneeFiscale', 'enabled' => 1, 'position' => 1140, 'notnull' => 1, 'visible' => 1),
        'date_limite_declaration' => array('type' => 'date', 'label' => 'DateLimiteDeclaration', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'date_envoi_declaration' => array('type' => 'date', 'label' => 'DateEnvoiDeclaration', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'revenu_fiscal_reference' => array('type' => 'double(24,8)', 'label' => 'RevenuFiscalReference', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'montant_impot_du' => array('type' => 'double(24,8)', 'label' => 'MontantImpotDu', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'montant_impot_estime' => array('type' => 'double(24,8)', 'label' => 'MontantImpotEstime', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'date_avis_imposition' => array('type' => 'date', 'label' => 'DateAvisImposition', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'numero_avis_imposition' => array('type' => 'varchar(50)', 'label' => 'NumeroAvisImposition', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'degrevement_demande' => array('type' => 'boolean', 'label' => 'DegrevementDemande', 'enabled' => 1, 'position' => 1220, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'motif_degrevement' => array('type' => 'text', 'label' => 'MotifDegrevement', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'montant_degrevement' => array('type' => 'double(24,8)', 'label' => 'MontantDegrevement', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'statut_degrevement' => array('type' => 'varchar(50)', 'label' => 'StatutDegrevement', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'mode_paiement_code' => array('type' => 'varchar(50)', 'label' => 'ModePaiement', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1),
        'fk_contact_impots' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactImpots', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'centre_impots' => array('type' => 'varchar(255)', 'label' => 'CentreImpots', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1)
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Fusion des champs spécifiques aux impôts avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_impot);
        
        // Valeurs par défaut spécifiques aux démarches fiscales
        $this->type_demarche_code = 'IMPOT';  // Force le code de type de démarche
        if (!isset($this->statut_declaration_code)) $this->statut_declaration_code = 'A_PREPARER'; // Statut par défaut
        if (!isset($this->annee_fiscale)) $this->annee_fiscale = date('Y') - 1; // Année précédente par défaut
        if (!isset($this->degrevement_demande)) $this->degrevement_demande = 0; // Pas de dégrèvement par défaut
    }

    /**
     * Crée une démarche fiscale dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à IMPOT
        $this->type_demarche_code = 'IMPOT';
        
        // Vérifications spécifiques aux démarches fiscales
        if (empty($this->type_declaration_code)) {
            $this->error = 'TypeDeclarationIsMandatory';
            return -1;
        }
        
        if (empty($this->annee_fiscale) || !is_numeric($this->annee_fiscale) || strlen($this->annee_fiscale) != 4) {
            $this->error = 'InvalidFiscalYear';
            return -1;
        }
        
        // Génération automatique du libellé s'il n'est pas fourni
        if (empty($this->libelle)) {
            $this->libelle = $this->getTypeDeclarationLabel() . ' ' . $this->annee_fiscale;
        }
        
        // Date d'échéance = date limite de déclaration si fournie
        if (!empty($this->date_limite_declaration) && empty($this->date_echeance)) {
            $this->date_echeance = $this->date_limite_declaration;
        }
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'une démarche fiscale
            $this->addToNotes($user, "Création d'une démarche fiscale pour " . $this->getTypeDeclarationLabel() . " de l'année " . $this->annee_fiscale);
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
     * Met à jour une démarche fiscale dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à IMPOT
        $this->type_demarche_code = 'IMPOT';
        
        // Vérifications spécifiques aux démarches fiscales
        if (empty($this->type_declaration_code)) {
            $this->error = 'TypeDeclarationIsMandatory';
            return -1;
        }
        
        if (empty($this->annee_fiscale) || !is_numeric($this->annee_fiscale) || strlen($this->annee_fiscale) != 4) {
            $this->error = 'InvalidFiscalYear';
            return -1;
        }
        
        // Si demande de dégrèvement cochée, vérifier qu'un montant est saisi
        if ($this->degrevement_demande && empty($this->montant_degrevement) && $this->montant_degrevement !== 0.0) {
            $this->error = 'MontantDegrevementRequiredWhenDegrevementDemande';
            return -1;
        }
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le statut de la déclaration fiscale
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau code de statut
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function changerStatutDeclaration($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsDeclarationValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatutDeclarationCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_declaration_code;
        $this->statut_declaration_code = $statut_code;
        
        // Mises à jour automatiques selon le statut
        switch ($statut_code) {
            case 'PREPAREE':
                // Si la déclaration est préparée mais pas encore envoyée
                if ($this->statut_demarche_code == 'A_FAIRE') {
                    $this->statut_demarche_code = 'EN_COURS';
                    $this->progression = 50; // 50% de progression
                }
                break;
                
            case 'ENVOYEE':
                // Si la déclaration est envoyée
                if (empty($this->date_envoi_declaration)) {
                    $this->date_envoi_declaration = date('Y-m-d');
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 80; // 80% de progression
                break;
                
            case 'VALIDEE':
                // Si la déclaration est validée par l'administration fiscale
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'REJETEE':
                // Si la déclaration est rejetée, revenir en préparation
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 30; // 30% de progression
                break;
                
            case 'EN_RECTIFICATION':
                // Si la déclaration est en rectification
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 60; // 60% de progression
                break;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $this->ajouterActionHistorique($user, 'CHANGEMENT_STATUT_DECLARATION', $ancien_statut . ' → ' . $statut_code, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CHANGEMENT_STATUT_IMPOT';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutDeclarationOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche fiscale "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_impot',
                    $this->id,
                    $message,
                    array('statut_declaration_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour les montants d'imposition
     *
     * @param User   $user                    Utilisateur effectuant l'action
     * @param double $revenu_fiscal_reference Revenu fiscal de référence
     * @param double $montant_impot_du        Montant de l'impôt dû
     * @param double $montant_impot_estime    Montant estimé de l'impôt (optionnel)
     * @param string $commentaire             Commentaire optionnel
     * @return int                            <0 si erreur, >0 si OK
     */
    public function updateMontants($user, $revenu_fiscal_reference, $montant_impot_du, $montant_impot_estime = null, $commentaire = '')
    {
        $ancien_revenu = $this->revenu_fiscal_reference;
        $ancien_impot = $this->montant_impot_du;
        $ancienne_estimation = $this->montant_impot_estime;
        
        $this->revenu_fiscal_reference = $revenu_fiscal_reference;
        $this->montant_impot_du = $montant_impot_du;
        
        if ($montant_impot_estime !== null) {
            $this->montant_impot_estime = $montant_impot_estime;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Revenu fiscal: ".price($ancien_revenu)." → ".price($this->revenu_fiscal_reference)."; ";
        $details .= "Impôt dû: ".price($ancien_impot)." → ".price($this->montant_impot_du);
        
        if ($montant_impot_estime !== null) {
            $details .= "; Estimation: ".price($ancienne_estimation)." → ".price($this->montant_impot_estime);
        }
        
        $this->ajouterActionHistorique($user, 'MISE_A_JOUR_MONTANTS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_MONTANTS_IMPOT';
                
                $message = 'Mise à jour des montants de la démarche fiscale "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_impot',
                    $this->id,
                    $message,
                    array(
                        'revenu_fiscal_reference' => array($ancien_revenu, $this->revenu_fiscal_reference),
                        'montant_impot_du' => array($ancien_impot, $this->montant_impot_du)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre l'avis d'imposition
     *
     * @param User   $user                   Utilisateur effectuant l'action
     * @param string $numero_avis_imposition Numéro de l'avis d'imposition
     * @param string $date_avis_imposition   Date de l'avis d'imposition (YYYY-MM-DD)
     * @param string $commentaire            Commentaire optionnel
     * @return int                           <0 si erreur, >0 si OK
     */
    public function enregistrerAvisImposition($user, $numero_avis_imposition, $date_avis_imposition, $commentaire = '')
    {
        if (empty($numero_avis_imposition)) {
            $this->error = 'NumeroAvisImpositionObligatoire';
            return -1;
        }
        
        if (empty($date_avis_imposition)) {
            $this->error = 'DateAvisImpositionObligatoire';
            return -1;
        }
        
        $ancien_numero = $this->numero_avis_imposition;
        $ancienne_date = $this->date_avis_imposition;
        
        $this->numero_avis_imposition = $numero_avis_imposition;
        $this->date_avis_imposition = $date_avis_imposition;
        
        // Mise à jour du statut
        if ($this->statut_declaration_code != 'VALIDEE') {
            $this->statut_declaration_code = 'VALIDEE';
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
        $details = "Numéro avis: ".($ancien_numero ?: 'Non défini')." → ".$this->numero_avis_imposition."; ";
        $details .= "Date avis: ".($ancienne_date ? dol_print_date($this->db->jdate($ancienne_date), 'day') : 'Non définie');
        $details .= " → ".dol_print_date($this->db->jdate($this->date_avis_imposition), 'day');
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_AVIS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_AVIS_IMPOT';
                
                $message = 'Enregistrement de l\'avis d\'imposition pour la démarche fiscale "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_impot',
                    $this->id,
                    $message,
                    array(
                        'numero_avis_imposition' => array($ancien_numero, $this->numero_avis_imposition),
                        'date_avis_imposition' => array($ancienne_date, $this->date_avis_imposition)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Demande un dégrèvement d'impôt
     *
     * @param User   $user               Utilisateur effectuant l'action
     * @param double $montant_degrevement Montant du dégrèvement demandé
     * @param string $motif_degrevement   Motif du dégrèvement
     * @param string $commentaire         Commentaire optionnel
     * @return int                        <0 si erreur, >0 si OK
     */
    public function demanderDegrevement($user, $montant_degrevement, $motif_degrevement, $commentaire = '')
    {
        if (empty($montant_degrevement) && $montant_degrevement !== 0.0) {
            $this->error = 'MontantDegrevementObligatoire';
            return -1;
        }
        
        if (empty($motif_degrevement)) {
            $this->error = 'MotifDegrevementObligatoire';
            return -1;
        }
        
        $this->degrevement_demande = 1;
        $this->montant_degrevement = $montant_degrevement;
        $this->motif_degrevement = $motif_degrevement;
        $this->statut_degrevement = 'EN_ATTENTE';
        
        // Réouverture de la démarche si elle était terminée
        if ($this->statut_demarche_code == 'TERMINEE') {
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 90;
            $this->date_cloture = null;
            $this->fk_user_cloture = null;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Demande de dégrèvement: ".price($this->montant_degrevement);
        $details .= "; Motif: ".$this->motif_degrevement;
        
        $this->ajouterActionHistorique($user, 'DEMANDE_DEGREVEMENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DEMANDE_DEGREVEMENT_IMPOT';
                
                $message = 'Demande de dégrèvement pour la démarche fiscale "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_impot',
                    $this->id,
                    $message,
                    array(
                        'degrevement_demande' => array(0, 1),
                        'montant_degrevement' => array(0, $this->montant_degrevement)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour le statut d'une demande de dégrèvement
     *
     * @param User   $user              Utilisateur effectuant l'action
     * @param string $statut_degrevement Nouveau statut du dégrèvement ('EN_ATTENTE', 'ACCORDE', 'REFUSE')
     * @param string $commentaire        Commentaire optionnel
     * @return int                       <0 si erreur, >0 si OK
     */
    public function updateStatutDegrevement($user, $statut_degrevement, $commentaire = '')
    {
        if (!$this->degrevement_demande) {
            $this->error = 'AucuneDemandeDegrevementExistante';
            return -1;
        }
        
        $statuts_valides = array('EN_ATTENTE', 'ACCORDE', 'REFUSE', 'ANNULE');
        if (!in_array($statut_degrevement, $statuts_valides)) {
            $this->error = 'StatutDegrevementInvalide';
            return -1;
        }
        
        $ancien_statut = $this->statut_degrevement;
        $this->statut_degrevement = $statut_degrevement;
        
        // Si le dégrèvement est accordé ou refusé, clôturer la démarche
        if ($statut_degrevement == 'ACCORDE' || $statut_degrevement == 'REFUSE') {
            $this->statut_demarche_code = 'TERMINEE';
            $this->progression = 100;
            $this->date_cloture = dol_now();
            $this->fk_user_cloture = $user->id;
        }
        
        // Si le dégrèvement est annulé, retour au statut précédent
        if ($statut_degrevement == 'ANNULE') {
            $this->degrevement_demande = 0;
            $this->statut_degrevement = '';
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Statut dégrèvement: ".($ancien_statut ?: 'Non défini')." → ".$this->statut_degrevement;
        
        $this->ajouterActionHistorique($user, 'MAJ_STATUT_DEGREVEMENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_STATUT_DEGREVEMENT_IMPOT';
                
                $message = 'Mise à jour du statut de dégrèvement pour la démarche fiscale "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_impot',
                    $this->id,
                    $message,
                    array('statut_degrevement' => array($ancien_statut, $this->statut_degrevement))
                );
            }
        }
        
        return $result;
    }

    /**
     * Ajoute une entrée à l'historique des actions spécifiques de la démarche fiscale
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
     * Ajoute une action à l'historique spécifique de la démarche fiscale
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
                        case 'CHANGEMENT_STATUT_DECLARATION':
                            $class = 'bg-info';
                            break;
                        case 'MISE_A_JOUR_MONTANTS':
                            $class = 'bg-success';
                            break;
                        case 'ENREGISTREMENT_AVIS':
                            $class = 'bg-warning';
                            break;
                        case 'DEMANDE_DEGREVEMENT':
                            $class = 'bg-danger';
                            break;
                        case 'MAJ_STATUT_DEGREVEMENT':
                            $class = 'bg-purple';
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
     * Obtient le libellé du type de déclaration
     * 
     * @return string Libellé du type de déclaration
     */
    public function getTypeDeclarationLabel()
    {
        $types = self::getTypeDeclarationOptions($this->langs);
        return isset($types[$this->type_declaration_code]) ? $types[$this->type_declaration_code] : $this->type_declaration_code;
    }
    
    /**
     * Liste des statuts de déclaration valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsDeclarationValides()
    {
        return array(
            'A_PREPARER',       // À préparer
            'PREPAREE',         // Préparée mais pas encore envoyée
            'ENVOYEE',          // Envoyée à l'administration fiscale
            'VALIDEE',          // Validée par l'administration fiscale
            'REJETEE',          // Rejetée par l'administration fiscale
            'EN_RECTIFICATION', // En cours de rectification
            'RECTIFIEE'         // Rectifiée et validée
        );
    }
    
    /**
     * Liste des types de déclaration valides
     *
     * @return array Codes des types de déclaration valides
     */
    public static function getTypesDeclarationValides()
    {
        return array(
            'IR',                      // Impôt sur le Revenu
            'TF',                      // Taxe Foncière
            'TH',                      // Taxe d'Habitation
            'DECLARATION_REVENUS',     // Déclaration de Revenus
            'IFI',                     // Impôt sur la Fortune Immobilière
            'CVEC',                    // Contribution de Vie Étudiante et de Campus
            'TAXE_AMENAGEMENT',        // Taxe d'Aménagement
            'CFE',                     // Cotisation Foncière des Entreprises
            'REGULARISATION',          // Régularisation fiscale
            'AUTRE'                    // Autre type
        );
    }
    
    /**
     * Liste des modes de paiement valides
     *
     * @return array Codes des modes de paiement valides
     */
    public static function getModesPaiementValides()
    {
        return array(
            'MENSUEL',            // Paiement mensuel
            'TRIMESTRIEL',        // Paiement trimestriel
            'ANNUEL',             // Paiement annuel
            'PRELEVEMENT_A_ECHEANCE', // Prélèvement à l'échéance
            'TIERS_PAYEUR',       // Tiers payeur
            'ETALEMENT',          // Étalement du paiement
            'AUTRE'               // Autre mode
        );
    }
    
    /**
     * Récupère les options du dictionnaire des types de déclaration
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeDeclarationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'impot_type_declaration', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts de déclaration
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutDeclarationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'impot_statut_declaration', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des modes de paiement
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getModePaiementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'impot_mode_paiement', $usekeys, $show_empty);
    }
    
    /**
     * Vérifie si cette démarche spécifique est une démarche fiscale
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isImpot()
    {
        return true;
    }
    
    /**
     * Récupère le contact du centre des impôts associé à la démarche
     * 
     * @return Contact|null Contact ou null si non trouvé
     */
    public function getContactImpots()
    {
        if (empty($this->fk_contact_impots)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
        $contact = new Contact($this->db);
        
        if ($contact->fetch($this->fk_contact_impots) > 0) {
            return $contact;
        }
        
        return null;
    }
    
    /**
     * Définit des constantes pour les types d'actions standards dans l'historique
     */
    const ACTION_CHANGEMENT_STATUT_DECLARATION = 'CHANGEMENT_STATUT_DECLARATION';
    const ACTION_MISE_A_JOUR_MONTANTS = 'MISE_A_JOUR_MONTANTS';
    const ACTION_ENREGISTREMENT_AVIS = 'ENREGISTREMENT_AVIS';
    const ACTION_DEMANDE_DEGREVEMENT = 'DEMANDE_DEGREVEMENT';
    const ACTION_MAJ_STATUT_DEGREVEMENT = 'MAJ_STATUT_DEGREVEMENT';
    const ACTION_CONTACT_CENTRE_IMPOTS = 'CONTACT_CENTRE_IMPOTS';
    const ACTION_DECLARATION_EN_LIGNE = 'DECLARATION_EN_LIGNE';
    const ACTION_AJOUT_DOCUMENT = 'AJOUT_DOCUMENT';
    const ACTION_NOTE_INTERNE = 'NOTE_INTERNE';
}

} // Fin de la condition if !class_exists
