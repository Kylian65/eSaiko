<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches CPAM des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarcheCPAM', false)) {

class ElaskaParticulierDemarcheCPAM extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_cpam';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_cpam';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AUX DÉMARCHES CPAM
    //
    
    /**
     * @var string Numéro de sécurité sociale du bénéficiaire
     */
    public $numero_secu;
    
    /**
     * @var string Code du type de demande CPAM (dictionnaire)
     */
    public $type_demande_code;
    
    /**
     * @var string Code du statut de la demande (dictionnaire)
     */
    public $statut_demande_code;
    
    /**
     * @var string Date de début de l'arrêt maladie (format YYYY-MM-DD)
     */
    public $date_debut_arret;
    
    /**
     * @var string Date de fin de l'arrêt maladie (format YYYY-MM-DD)
     */
    public $date_fin_arret;
    
    /**
     * @var int Durée en jours de l'arrêt maladie
     */
    public $duree_arret_jours;
    
    /**
     * @var date Date de reprise anticipée (format YYYY-MM-DD)
     */
    public $date_reprise_anticipee;
    
    /**
     * @var date Date de prolongation (format YYYY-MM-DD)
     */
    public $date_prolongation;
    
    /**
     * @var string Type d'accident (travail, trajet, autre)
     */
    public $type_accident;
    
    /**
     * @var string Date de l'accident (format YYYY-MM-DD)
     */
    public $date_accident;
    
    /**
     * @var double Taux d'invalidité (en pourcentage)
     */
    public $taux_invalidite;
    
    /**
     * @var string Catégorie d'invalidité (1, 2 ou 3)
     */
    public $categorie_invalidite;
    
    /**
     * @var double Montant de l'indemnité journalière
     */
    public $montant_ij;
    
    /**
     * @var double Montant de la pension d'invalidité
     */
    public $montant_pension_invalidite;
    
    /**
     * @var string Date du dépôt de la demande (format YYYY-MM-DD)
     */
    public $date_depot_demande;
    
    /**
     * @var string Date de décision de la CPAM (format YYYY-MM-DD)
     */
    public $date_decision;
    
    /**
     * @var string Numéro de dossier CPAM
     */
    public $numero_dossier_cpam;
    
    /**
     * @var string Numéro de téléphone du centre CPAM
     */
    public $telephone_centre;
    
    /**
     * @var int ID du contact conseiller CPAM
     */
    public $fk_contact_conseiller;
    
    /**
     * @var string Centre CPAM de rattachement
     */
    public $centre_rattachement;
    
    /**
     * @var string Historique des actions spécifiques à la démarche CPAM
     */
    public $historique_actions;
    
    /**
     * @var string Pièces justificatives à fournir (format JSON)
     */
    public $pieces_justificatives;
    
    /**
     * @var string Observations médicales (confidentielles)
     */
    public $observations_medicales;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_cpam = array(
        'numero_secu' => array('type' => 'varchar(15)', 'label' => 'NumeroSecu', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'type_demande_code' => array('type' => 'varchar(50)', 'label' => 'TypeDemande', 'enabled' => 1, 'position' => 1110, 'notnull' => 1, 'visible' => 1),
        'statut_demande_code' => array('type' => 'varchar(50)', 'label' => 'StatutDemande', 'enabled' => 1, 'position' => 1120, 'notnull' => 1, 'visible' => 1),
        'date_debut_arret' => array('type' => 'date', 'label' => 'DateDebutArret', 'enabled' => 1, 'position' => 1130, 'notnull' => 0, 'visible' => 1),
        'date_fin_arret' => array('type' => 'date', 'label' => 'DateFinArret', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'duree_arret_jours' => array('type' => 'integer', 'label' => 'DureeArretJours', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'date_reprise_anticipee' => array('type' => 'date', 'label' => 'DateRepriseAnticipee', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'date_prolongation' => array('type' => 'date', 'label' => 'DateProlongation', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'type_accident' => array('type' => 'varchar(50)', 'label' => 'TypeAccident', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'date_accident' => array('type' => 'date', 'label' => 'DateAccident', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'taux_invalidite' => array('type' => 'double(8,2)', 'label' => 'TauxInvalidite', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'categorie_invalidite' => array('type' => 'varchar(10)', 'label' => 'CategorieInvalidite', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'montant_ij' => array('type' => 'double(24,8)', 'label' => 'MontantIJ', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'montant_pension_invalidite' => array('type' => 'double(24,8)', 'label' => 'MontantPensionInvalidite', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'date_depot_demande' => array('type' => 'date', 'label' => 'DateDepotDemande', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'date_decision' => array('type' => 'date', 'label' => 'DateDecision', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'numero_dossier_cpam' => array('type' => 'varchar(50)', 'label' => 'NumeroDossierCPAM', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1),
        'telephone_centre' => array('type' => 'varchar(20)', 'label' => 'TelephoneCentre', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'fk_contact_conseiller' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactConseiller', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'centre_rattachement' => array('type' => 'varchar(255)', 'label' => 'CentreRattachement', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 1300, 'notnull' => 0, 'visible' => 1),
        'pieces_justificatives' => array('type' => 'text', 'label' => 'PiecesJustificatives', 'enabled' => 1, 'position' => 1310, 'notnull' => 0, 'visible' => 1),
        'observations_medicales' => array('type' => 'text', 'label' => 'ObservationsMedicales', 'enabled' => 1, 'position' => 1320, 'notnull' => 0, 'visible' => 1, 'alwayseditable' => 0)
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Fusion des champs spécifiques CPAM avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_cpam);
        
        // Valeurs par défaut spécifiques aux démarches CPAM
        $this->type_demarche_code = 'CPAM';  // Force le code de type de démarche
        if (!isset($this->statut_demande_code)) $this->statut_demande_code = 'A_DEPOSER'; // Statut par défaut
    }

    /**
     * Crée une démarche CPAM dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à CPAM
        $this->type_demarche_code = 'CPAM';
        
        // Vérifications spécifiques aux démarches CPAM
        if (empty($this->type_demande_code)) {
            $this->error = 'TypeDemandeIsMandatory';
            return -1;
        }
        
        // Vérification du format du numéro de sécurité sociale si fourni
        if (!empty($this->numero_secu) && !$this->isValidNumeroSecu($this->numero_secu)) {
            $this->error = 'InvalidNumeroSecuFormat';
            return -1;
        }
        
        // Génération automatique du libellé s'il n'est pas fourni
        if (empty($this->libelle)) {
            $this->libelle = $this->getTypeDemandeLabel();
            if (!empty($this->date_debut_arret)) {
                $this->libelle .= ' du ' . dol_print_date($this->db->jdate($this->date_debut_arret), 'day');
            }
        }
        
        // Initialisation des pièces justificatives
        if (empty($this->pieces_justificatives)) {
            $this->pieces_justificatives = $this->getPiecesJustificativesParDefaut();
        }
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'une démarche CPAM
            $this->addToNotes($user, "Création d'une démarche CPAM de type " . $this->getTypeDemandeLabel());
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
     * Met à jour une démarche CPAM dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à CPAM
        $this->type_demarche_code = 'CPAM';
        
        // Vérifications spécifiques aux démarches CPAM
        if (empty($this->type_demande_code)) {
            $this->error = 'TypeDemandeIsMandatory';
            return -1;
        }
        
        // Vérification du format du numéro de sécurité sociale si fourni
        if (!empty($this->numero_secu) && !$this->isValidNumeroSecu($this->numero_secu)) {
            $this->error = 'InvalidNumeroSecuFormat';
            return -1;
        }
        
        // Vérification de la cohérence des dates d'arrêt
        if (!empty($this->date_debut_arret) && !empty($this->date_fin_arret) && $this->date_fin_arret < $this->date_debut_arret) {
            $this->error = 'DateFinArretCantBeBeforeDateDebutArret';
            return -1;
        }
        
        // Si type_demande est 'ARRET_MALADIE', vérifier que la date de début d'arrêt est renseignée
        if ($this->type_demande_code == 'ARRET_MALADIE' && empty($this->date_debut_arret)) {
            $this->error = 'DateDebutArretRequiredForArretMaladie';
            return -1;
        }
        
        // Si type_demande est 'ACCIDENT_TRAVAIL', vérifier que la date de l'accident est renseignée
        if ($this->type_demande_code == 'ACCIDENT_TRAVAIL' && empty($this->date_accident)) {
            $this->error = 'DateAccidentRequiredForAccidentTravail';
            return -1;
        }
        
        // Calcul automatique de la durée de l'arrêt si les dates sont renseignées
        if (!empty($this->date_debut_arret) && !empty($this->date_fin_arret)) {
            $this->calculerDureeArret();
        }
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le statut de la demande CPAM
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau code de statut
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function changerStatutDemande($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsDemandeValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatutDemandeCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_demande_code;
        $this->statut_demande_code = $statut_code;
        
        // Mises à jour automatiques selon le statut
        switch ($statut_code) {
            case 'DEPOSE':
                if (empty($this->date_depot_demande)) {
                    $this->date_depot_demande = date('Y-m-d');
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 50; // 50% de progression
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
                
            case 'PIECES_MANQUANTES':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 30; // 30% de progression
                break;
                
            case 'EN_TRAITEMENT':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 70; // 70% de progression
                break;
                
            case 'PAIEMENT_EN_COURS':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 90; // 90% de progression
                break;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $this->ajouterActionHistorique($user, 'CHANGEMENT_STATUT_DEMANDE', $ancien_statut . ' → ' . $statut_code, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CHANGEMENT_STATUT_CPAM';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutDemandeOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche CPAM "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_cpam',
                    $this->id,
                    $message,
                    array('statut_demande_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure les dates d'arrêt maladie
     *
     * @param User   $user                Utilisateur effectuant l'action
     * @param string $date_debut_arret    Date de début d'arrêt (YYYY-MM-DD)
     * @param string $date_fin_arret      Date de fin d'arrêt (YYYY-MM-DD)
     * @param string $commentaire         Commentaire optionnel
     * @return int                        <0 si erreur, >0 si OK
     */
    public function configurerDatesArret($user, $date_debut_arret, $date_fin_arret, $commentaire = '')
    {
        // Vérification des dates
        if (empty($date_debut_arret)) {
            $this->error = 'DateDebutArretObligatoire';
            return -1;
        }
        
        if (empty($date_fin_arret)) {
            $this->error = 'DateFinArretObligatoire';
            return -1;
        }
        
        // Vérification cohérence des dates
        if ($date_fin_arret < $date_debut_arret) {
            $this->error = 'DateFinArretNeDoitPasEtreAnterieureADateDebutArret';
            return -1;
        }
        
        $anciennes_dates = array(
            'debut' => $this->date_debut_arret,
            'fin' => $this->date_fin_arret,
            'duree' => $this->duree_arret_jours
        );
        
        $this->date_debut_arret = $date_debut_arret;
        $this->date_fin_arret = $date_fin_arret;
        
        // Calcul automatique de la durée
        $this->calculerDureeArret();
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Début arrêt: ".($anciennes_dates['debut'] ? dol_print_date($this->db->jdate($anciennes_dates['debut']), 'day') : 'Non défini');
        $details .= " → ".dol_print_date($this->db->jdate($this->date_debut_arret), 'day');
        $details .= "; Fin arrêt: ".($anciennes_dates['fin'] ? dol_print_date($this->db->jdate($anciennes_dates['fin']), 'day') : 'Non défini');
        $details .= " → ".dol_print_date($this->db->jdate($this->date_fin_arret), 'day');
        $details .= "; Durée: ".($anciennes_dates['duree'] ? $anciennes_dates['duree'].' jours' : 'Non défini');
        $details .= " → ".$this->duree_arret_jours." jours";
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_DATES_ARRET', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_DATES_ARRET_CPAM';
                
                $message = 'Configuration des dates d\'arrêt pour la démarche CPAM "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_cpam',
                    $this->id,
                    $message,
                    array(
                        'date_debut_arret' => array($anciennes_dates['debut'], $this->date_debut_arret),
                        'date_fin_arret' => array($anciennes_dates['fin'], $this->date_fin_arret)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une prolongation d'arrêt maladie
     *
     * @param User   $user             Utilisateur effectuant l'action
     * @param string $date_prolongation Date de prolongation (YYYY-MM-DD)
     * @param string $date_fin_arret    Nouvelle date de fin d'arrêt (YYYY-MM-DD)
     * @param string $commentaire       Commentaire optionnel
     * @return int                      <0 si erreur, >0 si OK
     */
    public function prolongerArret($user, $date_prolongation, $date_fin_arret, $commentaire = '')
    {
        // Vérifications
        if (empty($this->date_debut_arret)) {
            $this->error = 'ArretInitialNonDefini';
            return -1;
        }
        
        if (empty($date_prolongation) || empty($date_fin_arret)) {
            $this->error = 'DatesProlongationObligatoires';
            return -1;
        }
        
        // Vérification cohérence des dates
        if ($date_prolongation <= $this->date_debut_arret) {
            $this->error = 'DateProlongationDoitEtreApresDateDebutArret';
            return -1;
        }
        
        if ($date_fin_arret <= $date_prolongation) {
            $this->error = 'DateFinArretDoitEtreApresDateProlongation';
            return -1;
        }
        
        $ancienne_fin = $this->date_fin_arret;
        $ancienne_duree = $this->duree_arret_jours;
        $ancienne_prolongation = $this->date_prolongation;
        
        $this->date_prolongation = $date_prolongation;
        $this->date_fin_arret = $date_fin_arret;
        
        // Recalcul de la durée totale
        $this->calculerDureeArret();
        
        // Mise à jour du statut
        if ($this->statut_demande_code == 'ACCEPTE') {
            // On repasse en traitement pour la prolongation
            $this->statut_demande_code = 'EN_TRAITEMENT';
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Prolongation le ".dol_print_date($this->db->jdate($date_prolongation), 'day');
        $details .= "; Ancienne fin: ".($ancienne_fin ? dol_print_date($this->db->jdate($ancienne_fin), 'day') : 'Non définie');
        $details .= " → ".dol_print_date($this->db->jdate($this->date_fin_arret), 'day');
        $details .= "; Nouvelle durée totale: ".$this->duree_arret_jours." jours";
        
        $this->ajouterActionHistorique($user, 'PROLONGATION_ARRET', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'PROLONGATION_ARRET_CPAM';
                
                $message = 'Prolongation d\'arrêt pour la démarche CPAM "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_cpam',
                    $this->id,
                    $message,
                    array(
                        'date_prolongation' => array($ancienne_prolongation, $this->date_prolongation),
                        'date_fin_arret' => array($ancienne_fin, $this->date_fin_arret)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une reprise anticipée du travail
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param string $date_reprise_anticipee Date de reprise anticipée (YYYY-MM-DD)
     * @param string $commentaire            Commentaire optionnel
     * @return int                           <0 si erreur, >0 si OK
     */
    public function repriseAnticipee($user, $date_reprise_anticipee, $commentaire = '')
    {
        // Vérifications
        if (empty($this->date_debut_arret) || empty($this->date_fin_arret)) {
            $this->error = 'DatesArretNonDefinies';
            return -1;
        }
        
        if (empty($date_reprise_anticipee)) {
            $this->error = 'DateRepriseAnticipeeObligatoire';
            return -1;
        }
        
        // Vérification cohérence des dates
        if ($date_reprise_anticipee <= $this->date_debut_arret) {
            $this->error = 'DateRepriseAnticipeeDoitEtreApresDateDebutArret';
            return -1;
        }
        
        if ($date_reprise_anticipee >= $this->date_fin_arret) {
            $this->error = 'DateRepriseAnticipeeDoitEtreAvantDateFinArret';
            return -1;
        }
        
        $ancienne_reprise = $this->date_reprise_anticipee;
        $ancienne_fin = $this->date_fin_arret;
        $ancienne_duree = $this->duree_arret_jours;
        
        // Sauvegarde de l'ancienne date de fin d'arrêt
        $date_fin_arret_initiale = $this->date_fin_arret;
        
        $this->date_reprise_anticipee = $date_reprise_anticipee;
        $this->date_fin_arret = $date_reprise_anticipee; // La date de fin devient la date de reprise anticipée
        
        // Recalcul de la durée réelle
        $this->calculerDureeArret();
        
        // Mise à jour du statut
        if ($this->statut_demande_code != 'ACCEPTE' && $this->statut_demande_code != 'PAIEMENT_EN_COURS') {
            $this->statut_demande_code = 'ACCEPTE';
        }
        
        $this->statut_demarche_code = 'TERMINEE';
        $this->progression = 100;
        $this->date_cloture = dol_now();
        $this->fk_user_cloture = $user->id;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Reprise anticipée le ".dol_print_date($this->db->jdate($date_reprise_anticipee), 'day');
        $details .= "; Date de fin initiale: ".dol_print_date($this->db->jdate($date_fin_arret_initiale), 'day');
        $details .= "; Durée réelle: ".$this->duree_arret_jours." jours";
        
        $this->ajouterActionHistorique($user, 'REPRISE_ANTICIPEE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'REPRISE_ANTICIPEE_CPAM';
                
                $message = 'Reprise anticipée pour la démarche CPAM "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_cpam',
                    $this->id,
                    $message,
                    array(
                        'date_reprise_anticipee' => array($ancienne_reprise, $this->date_reprise_anticipee),
                        'date_fin_arret' => array($ancienne_fin, $this->date_fin_arret)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour les informations d'accident de travail
     *
     * @param User   $user           Utilisateur effectuant l'action
     * @param string $date_accident  Date de l'accident (YYYY-MM-DD)
     * @param string $type_accident  Type d'accident (travail, trajet, autre)
     * @param string $commentaire    Commentaire optionnel
     * @return int                   <0 si erreur, >0 si OK
     */
    public function updateAccident($user, $date_accident, $type_accident, $commentaire = '')
    {
        // Vérifications
        if (empty($date_accident) || empty($type_accident)) {
            $this->error = 'DateEtTypeAccidentObligatoires';
            return -1;
        }
        
        $ancienne_date = $this->date_accident;
        $ancien_type = $this->type_accident;
        
        $this->date_accident = $date_accident;
        $this->type_accident = $type_accident;
        
        // Si le type de demande n'était pas déjà défini comme accident
        if ($this->type_demande_code != 'ACCIDENT_TRAVAIL') {
            $this->type_demande_code = 'ACCIDENT_TRAVAIL';
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Date accident: ".($ancienne_date ? dol_print_date($this->db->jdate($ancienne_date), 'day') : 'Non définie');
        $details .= " → ".dol_print_date($this->db->jdate($this->date_accident), 'day');
        $details .= "; Type: ".($ancien_type ?: 'Non défini')." → ".$this->type_accident;
        
        $this->ajouterActionHistorique($user, 'MAJ_ACCIDENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_ACCIDENT_CPAM';
                
                $message = 'Mise à jour des informations d\'accident pour la démarche CPAM "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_cpam',
                    $this->id,
                    $message,
                    array(
                        'date_accident' => array($ancienne_date, $this->date_accident),
                        'type_accident' => array($ancien_type, $this->type_accident)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour les montants d'indemnisation
     *
     * @param User   $user                       Utilisateur effectuant l'action
     * @param double $montant_ij                 Montant des indemnités journalières
     * @param double $montant_pension_invalidite Montant de la pension d'invalidité (optionnel)
     * @param string $commentaire                Commentaire optionnel
     * @return int                               <0 si erreur, >0 si OK
     */
    public function updateMontantsIndemnisation($user, $montant_ij, $montant_pension_invalidite = null, $commentaire = '')
    {
        // Vérifications
        if ($montant_ij < 0 || ($montant_pension_invalidite !== null && $montant_pension_invalidite < 0)) {
            $this->error = 'MontantsMustBePositive';
            return -1;
        }
        
        $ancien_montant_ij = $this->montant_ij;
        $ancien_montant_pension = $this->montant_pension_invalidite;
        
        $this->montant_ij = $montant_ij;
        
        if ($montant_pension_invalidite !== null) {
            $this->montant_pension_invalidite = $montant_pension_invalidite;
            
            // Si on définit une pension d'invalidité, mettre à jour le type de demande
            if ($this->type_demande_code != 'INVALIDITE' && $montant_pension_invalidite > 0) {
                $this->type_demande_code = 'INVALIDITE';
            }
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "IJ: ".price($ancien_montant_ij)." → ".price($this->montant_ij);
        
        if ($montant_pension_invalidite !== null) {
            $details .= "; Pension invalidité: ".price($ancien_montant_pension)." → ".price($this->montant_pension_invalidite);
        }
        
        $this->ajouterActionHistorique($user, 'MAJ_MONTANTS_INDEMNISATION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_MONTANTS_CPAM';
                
                $message = 'Mise à jour des montants d\'indemnisation pour la démarche CPAM "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_cpam',
                    $this->id,
                    $message,
                    array(
                        'montant_ij' => array($ancien_montant_ij, $this->montant_ij)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour les informations d'invalidité
     *
     * @param User   $user                 Utilisateur effectuant l'action
     * @param double $taux_invalidite      Taux d'invalidité en pourcentage
     * @param string $categorie_invalidite Catégorie d'invalidité (1, 2 ou 3)
     * @param string $commentaire          Commentaire optionnel
     * @return int                         <0 si erreur, >0 si OK
     */
    public function updateInvalidite($user, $taux_invalidite, $categorie_invalidite, $commentaire = '')
    {
        // Vérifications
        if ($taux_invalidite < 0 || $taux_invalidite > 100) {
            $this->error = 'TauxInvaliditeMustBeBetween0And100';
            return -1;
        }
        
        if (!in_array($categorie_invalidite, array('1', '2', '3'))) {
            $this->error = 'InvalidCategorieInvalidite';
            return -1;
        }
        
        $ancien_taux = $this->taux_invalidite;
        $ancienne_categorie = $this->categorie_invalidite;
        
        $this->taux_invalidite = $taux_invalidite;
        $this->categorie_invalidite = $categorie_invalidite;
        
        // Si le type de demande n'était pas déjà défini comme invalidité
        if ($this->type_demande_code != 'INVALIDITE') {
            $this->type_demande_code = 'INVALIDITE';
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Taux invalidité: ".($ancien_taux ? $ancien_taux.'%' : 'Non défini')." → ".$this->taux_invalidite."%";
        $details .= "; Catégorie: ".($ancienne_categorie ?: 'Non définie')." → ".$this->categorie_invalidite;
        
        $this->ajouterActionHistorique($user, 'MAJ_INVALIDITE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_INVALIDITE_CPAM';
                
                $message = 'Mise à jour des informations d\'invalidité pour la démarche CPAM "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_cpam',
                    $this->id,
                    $message,
                    array(
                        'taux_invalidite' => array($ancien_taux, $this->taux_invalidite),
                        'categorie_invalidite' => array($ancienne_categorie, $this->categorie_invalidite)
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
                $action = 'MAJ_PIECES_CPAM';
                
                $message = 'Mise à jour des pièces justificatives pour la démarche CPAM "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_cpam',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }

    /**
     * Calcule la durée de l'arrêt en jours
     */
    protected function calculerDureeArret()
    {
        if (empty($this->date_debut_arret) || empty($this->date_fin_arret)) {
            $this->duree_arret_jours = 0;
            return;
        }
        
        try {
            $date_debut = new DateTime($this->date_debut_arret);
            $date_fin = new DateTime($this->date_fin_arret);
            
            // Ajout d'un jour car on compte le jour de début et le jour de fin
            $interval = $date_debut->diff($date_fin);
            $this->duree_arret_jours = $interval->days + 1;
        } catch (Exception $e) {
            dol_syslog('Error in ElaskaParticulierDemarcheCPAM::calculerDureeArret: '.$e->getMessage(), LOG_ERR);
            $this->duree_arret_jours = 0;
        }
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
            array('libelle' => 'Carte vitale', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Pièce d\'identité', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'RIB', 'obligatoire' => 1, 'fourni' => 0)
        );
        
        // Ajouter des pièces spécifiques selon le type de demande
        switch ($this->type_demande_code) {
            case 'ARRET_MALADIE':
                $pieces[] = array('libelle' => 'Avis d\'arrêt de travail', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation de salaire', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'ACCIDENT_TRAVAIL':
                $pieces[] = array('libelle' => 'Déclaration d\'accident de travail', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Certificat médical initial', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation de salaire AT', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'MALADIE_PROFESSIONNELLE':
                $pieces[] = array('libelle' => 'Déclaration de maladie professionnelle', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Certificat médical initial', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation d\'exposition au risque', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'INVALIDITE':
                $pieces[] = array('libelle' => 'Demande de pension d\'invalidité', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Certificat médical', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Justificatifs de ressources', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'REMBOURSEMENT_SOINS':
                $pieces[] = array('libelle' => 'Feuille de soins', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Prescription médicale', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'ALD':
                $pieces[] = array('libelle' => 'Protocole de soins', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Certificat médical détaillé', 'obligatoire' => 1, 'fourni' => 0);
                break;
        }
        
        return json_encode($pieces);
    }

    /**
     * Ajoute une entrée à l'historique des actions spécifiques de la démarche CPAM
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
     * Ajoute une action à l'historique spécifique de la démarche CPAM
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
                        case 'CHANGEMENT_STATUT_DEMANDE':
                            $class = 'bg-info';
                            break;
                        case 'CONFIGURATION_DATES_ARRET':
                            $class = 'bg-success';
                            break;
                        case 'PROLONGATION_ARRET':
                            $class = 'bg-warning';
                            break;
                        case 'REPRISE_ANTICIPEE':
                            $class = 'bg-danger';
                            break;
                        case 'MAJ_ACCIDENT':
                            $class = 'bg-primary';
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
     * Vérifie la validité d'un numéro de sécurité sociale
     *
     * @param string $numero Numéro de sécurité sociale
     * @return bool          True si le format est valide, false sinon
     */
    public function isValidNumeroSecu($numero)
    {
        // Nettoyage du numéro (enlever espaces et tirets)
        $numero = preg_replace('/[^0-9A-Za-z]/', '', $numero);
        
        // Format français : 13 chiffres + clé de 2 chiffres
        if (preg_match('/^[1-2][0-9]{12}[0-9]{2}$/', $numero)) {
            return true;
        }
        
        return false;
    }

    /**
     * Obtient le libellé du type de demande
     * 
     * @return string Libellé du type de demande
     */
    public function getTypeDemandeLabel()
    {
        $types = self::getTypeDemandeOptions($this->langs);
        return isset($types[$this->type_demande_code]) ? $types[$this->type_demande_code] : $this->type_demande_code;
    }
    
    /**
     * Liste des statuts de demande valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsDemandeValides()
    {
        return array(
            'A_DEPOSER',          // À déposer
            'DEPOSE',             // Dossier déposé
            'PIECES_MANQUANTES',  // Pièces manquantes
            'EN_TRAITEMENT',      // En cours de traitement
            'ACCEPTE',            // Demande acceptée
            'REFUSE',             // Demande refusée
            'PAIEMENT_EN_COURS',  // Paiement en cours
            'A_COMPLETER',        // À compléter
            'EXPERTISE_MEDICALE'  // En attente d'expertise médicale
        );
    }
    
    /**
     * Liste des types de demande CPAM valides
     *
     * @return array Codes des types de demande valides
     */
    public static function getTypesDemandeValides()
    {
        return array(
            'ARRET_MALADIE',          // Arrêt maladie
            'ACCIDENT_TRAVAIL',       // Accident du travail
            'MALADIE_PROFESSIONNELLE',// Maladie professionnelle
            'INVALIDITE',             // Invalidité
            'REMBOURSEMENT_SOINS',    // Remboursement de soins
            'ALD',                    // Affection Longue Durée
            'MATERNITE',              // Congé maternité
            'PATERNITE',              // Congé paternité
            'CMI',                    // Carte Mobilité Inclusion
            'CURE_THERMALE',          // Cure thermale
            'TRANSPORT_MEDICAL',      // Transport médical
            'CAPITAL_DECES',          // Capital décès
            'AAH',                    // Allocation aux Adultes Handicapés
            'RQTH',                   // Reconnaissance Qualité Travailleur Handicapé
            'AUTRE'                   // Autre type de démarche
        );
    }
    
    /**
     * Liste des types d'accident valides
     *
     * @return array Codes des types d'accident valides
     */
    public static function getTypesAccidentValides()
    {
        return array(
            'TRAVAIL',   // Accident du travail sur le lieu de travail
            'TRAJET',    // Accident de trajet domicile-travail
            'AUTRE'      // Autre type d'accident
        );
    }
    
    /**
     * Récupère les options du dictionnaire des types de demande
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeDemandeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'cpam_type_demande', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts de demande
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutDemandeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'cpam_statut_demande', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types d'accident
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeAccidentOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'cpam_type_accident', $usekeys, $show_empty);
    }
    
    /**
     * Vérifie si cette démarche spécifique est une démarche CPAM
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isCPAM()
    {
        return true;
    }
    
    /**
     * Récupère le contact conseiller CPAM associé à la démarche
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
    const ACTION_CHANGEMENT_STATUT_DEMANDE = 'CHANGEMENT_STATUT_DEMANDE';
    const ACTION_CONFIGURATION_DATES_ARRET = 'CONFIGURATION_DATES_ARRET';
    const ACTION_PROLONGATION_ARRET = 'PROLONGATION_ARRET';
    const ACTION_REPRISE_ANTICIPEE = 'REPRISE_ANTICIPEE';
    const ACTION_MAJ_ACCIDENT = 'MAJ_ACCIDENT';
    const ACTION_MAJ_MONTANTS_INDEMNISATION = 'MAJ_MONTANTS_INDEMNISATION';
    const ACTION_MAJ_INVALIDITE = 'MAJ_INVALIDITE';
    const ACTION_MISE_A_JOUR_PIECES = 'MISE_A_JOUR_PIECES';
    const ACTION_CONTACT_CONSEILLER = 'CONTACT_CONSEILLER';
    const ACTION_AJOUT_DOCUMENT = 'AJOUT_DOCUMENT';
    const ACTION_NOTE_INTERNE = 'NOTE_INTERNE';
}

} // Fin de la condition if !class_exists
