<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches de retraite des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarcheRetraite', false)) {

class ElaskaParticulierDemarcheRetraite extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_retraite';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_retraite';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AUX DÉMARCHES RETRAITE
    //
    
    /**
     * @var string Numéro de sécurité sociale du bénéficiaire
     */
    public $numero_secu;
    
    /**
     * @var string Code du type de retraite (dictionnaire)
     */
    public $type_retraite_code;
    
    /**
     * @var string Code du statut de la demande de retraite (dictionnaire)
     */
    public $statut_demande_code;
    
    /**
     * @var string Code de l'organisme principal de retraite (dictionnaire)
     */
    public $organisme_principal_code;
    
    /**
     * @var string Date prévue de départ à la retraite (format YYYY-MM-DD)
     */
    public $date_depart_prevue;
    
    /**
     * @var string Date effective de départ à la retraite (format YYYY-MM-DD)
     */
    public $date_depart_effective;
    
    /**
     * @var string Date de dépôt de la demande (format YYYY-MM-DD)
     */
    public $date_depot_demande;
    
    /**
     * @var string Date de début de versement de la pension (format YYYY-MM-DD)
     */
    public $date_debut_versement;
    
    /**
     * @var double Montant estimé de la pension
     */
    public $montant_pension_estime;
    
    /**
     * @var double Montant réel de la pension
     */
    public $montant_pension_reel;
    
    /**
     * @var int Nombre de trimestres cotisés
     */
    public $nb_trimestres_cotises;
    
    /**
     * @var int Nombre de trimestres validés
     */
    public $nb_trimestres_valides;
    
    /**
     * @var int Nombre de trimestres requis pour taux plein
     */
    public $nb_trimestres_requis;
    
    /**
     * @var double Taux de décote appliqué (en pourcentage)
     */
    public $taux_decote;
    
    /**
     * @var double Taux de surcote appliqué (en pourcentage)
     */
    public $taux_surcote;
    
    /**
     * @var string Carrière longue (0=non, 1=oui)
     */
    public $carriere_longue;
    
    /**
     * @var string Handicap reconnu (0=non, 1=oui)
     */
    public $handicap_reconnu;
    
    /**
     * @var int ID du contact conseiller retraite
     */
    public $fk_contact_conseiller;
    
    /**
     * @var string Code des régimes complémentaires concernés (séparés par des virgules)
     */
    public $regimes_complementaires;
    
    /**
     * @var string Liste des organismes retraite concernés (format JSON)
     */
    public $organismes_concernes;
    
    /**
     * @var string Historique des actions spécifiques à la démarche retraite
     */
    public $historique_actions;
    
    /**
     * @var string Pièces justificatives à fournir (format JSON)
     */
    public $pieces_justificatives;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_retraite = array(
        'numero_secu' => array('type' => 'varchar(15)', 'label' => 'NumeroSecu', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'type_retraite_code' => array('type' => 'varchar(50)', 'label' => 'TypeRetraite', 'enabled' => 1, 'position' => 1110, 'notnull' => 1, 'visible' => 1),
        'statut_demande_code' => array('type' => 'varchar(50)', 'label' => 'StatutDemande', 'enabled' => 1, 'position' => 1120, 'notnull' => 1, 'visible' => 1),
        'organisme_principal_code' => array('type' => 'varchar(50)', 'label' => 'OrganismePrincipal', 'enabled' => 1, 'position' => 1130, 'notnull' => 1, 'visible' => 1),
        'date_depart_prevue' => array('type' => 'date', 'label' => 'DateDepartPrevue', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'date_depart_effective' => array('type' => 'date', 'label' => 'DateDepartEffective', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'date_depot_demande' => array('type' => 'date', 'label' => 'DateDepotDemande', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'date_debut_versement' => array('type' => 'date', 'label' => 'DateDebutVersement', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'montant_pension_estime' => array('type' => 'double(24,8)', 'label' => 'MontantPensionEstime', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'montant_pension_reel' => array('type' => 'double(24,8)', 'label' => 'MontantPensionReel', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'nb_trimestres_cotises' => array('type' => 'integer', 'label' => 'NbTrimestresCotises', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'nb_trimestres_valides' => array('type' => 'integer', 'label' => 'NbTrimestresValides', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'nb_trimestres_requis' => array('type' => 'integer', 'label' => 'NbTrimestresRequis', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'taux_decote' => array('type' => 'double(8,2)', 'label' => 'TauxDecote', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'taux_surcote' => array('type' => 'double(8,2)', 'label' => 'TauxSurcote', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'carriere_longue' => array('type' => 'boolean', 'label' => 'CarriereLongue', 'enabled' => 1, 'position' => 1250, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'handicap_reconnu' => array('type' => 'boolean', 'label' => 'HandicapReconnu', 'enabled' => 1, 'position' => 1260, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'fk_contact_conseiller' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactConseiller', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'regimes_complementaires' => array('type' => 'varchar(255)', 'label' => 'RegimesComplementaires', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'organismes_concernes' => array('type' => 'text', 'label' => 'OrganismesConcernes', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 1300, 'notnull' => 0, 'visible' => 1),
        'pieces_justificatives' => array('type' => 'text', 'label' => 'PiecesJustificatives', 'enabled' => 1, 'position' => 1310, 'notnull' => 0, 'visible' => 1)
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Fusion des champs spécifiques à la retraite avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_retraite);
        
        // Valeurs par défaut spécifiques aux démarches de retraite
        $this->type_demarche_code = 'RETRAITE';  // Force le code de type de démarche
        if (!isset($this->statut_demande_code)) $this->statut_demande_code = 'PREPARATION'; // Statut par défaut
        if (!isset($this->carriere_longue)) $this->carriere_longue = 0; // Par défaut pas de carrière longue
        if (!isset($this->handicap_reconnu)) $this->handicap_reconnu = 0; // Par défaut pas de handicap reconnu
        if (!isset($this->organisme_principal_code)) $this->organisme_principal_code = 'CARSAT'; // Organisme par défaut
    }

    /**
     * Crée une démarche retraite dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à RETRAITE
        $this->type_demarche_code = 'RETRAITE';
        
        // Vérifications spécifiques aux démarches retraite
        if (empty($this->type_retraite_code)) {
            $this->error = 'TypeRetraiteIsMandatory';
            return -1;
        }
        
        if (empty($this->organisme_principal_code)) {
            $this->error = 'OrganismePrincipalIsMandatory';
            return -1;
        }
        
        // Vérification du format du numéro de sécurité sociale si fourni
        if (!empty($this->numero_secu) && !$this->isValidNumeroSecu($this->numero_secu)) {
            $this->error = 'InvalidNumeroSecuFormat';
            return -1;
        }
        
        // Génération automatique du libellé s'il n'est pas fourni
        if (empty($this->libelle)) {
            $this->libelle = $this->getTypeRetraiteLabel() . ' - ' . $this->getOrganismePrincipalLabel();
        }
        
        // Date d'échéance = date de départ prévue si fournie
        if (!empty($this->date_depart_prevue) && empty($this->date_echeance)) {
            $this->date_echeance = $this->date_depart_prevue;
        }
        
        // Initialisation des pièces justificatives
        if (empty($this->pieces_justificatives)) {
            $this->pieces_justificatives = $this->getPiecesJustificativesParDefaut();
        }
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'une démarche retraite
            $this->addToNotes($user, "Création d'une démarche retraite de type " . $this->getTypeRetraiteLabel() . " auprès de " . $this->getOrganismePrincipalLabel());
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
     * Met à jour une démarche retraite dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à RETRAITE
        $this->type_demarche_code = 'RETRAITE';
        
        // Vérifications spécifiques aux démarches retraite
        if (empty($this->type_retraite_code)) {
            $this->error = 'TypeRetraiteIsMandatory';
            return -1;
        }
        
        if (empty($this->organisme_principal_code)) {
            $this->error = 'OrganismePrincipalIsMandatory';
            return -1;
        }
        
        // Vérification du format du numéro de sécurité sociale si fourni
        if (!empty($this->numero_secu) && !$this->isValidNumeroSecu($this->numero_secu)) {
            $this->error = 'InvalidNumeroSecuFormat';
            return -1;
        }
        
        // Vérification cohérence des dates
        if (!empty($this->date_depart_prevue) && !empty($this->date_depart_effective) && $this->date_depart_effective < $this->date_depart_prevue) {
            $this->error = 'DateDepartEffectiveCantBeBeforeDateDepartPrevue';
            return -1;
        }
        
        if (!empty($this->date_depart_effective) && !empty($this->date_debut_versement) && $this->date_debut_versement < $this->date_depart_effective) {
            $this->error = 'DateDebutVersementCantBeBeforeDateDepartEffective';
            return -1;
        }
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le statut de la demande de retraite
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
            case 'EN_COURS_CONSTITUTION':
                if ($this->statut_demarche_code == 'A_FAIRE') {
                    $this->statut_demarche_code = 'EN_COURS';
                    $this->progression = 30; // 30% de progression
                }
                break;
                
            case 'DEPOSEE':
                if (empty($this->date_depot_demande)) {
                    $this->date_depot_demande = date('Y-m-d');
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 50; // 50% de progression
                break;
                
            case 'EN_INSTRUCTION':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 70; // 70% de progression
                break;
                
            case 'ACCORDEE':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'REFUSEE':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'VERSEMENT_EN_COURS':
                if (empty($this->date_debut_versement)) {
                    $this->date_debut_versement = date('Y-m-d');
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
        $this->ajouterActionHistorique($user, 'CHANGEMENT_STATUT_DEMANDE', $ancien_statut . ' → ' . $statut_code, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CHANGEMENT_STATUT_RETRAITE';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutDemandeOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche retraite "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_retraite',
                    $this->id,
                    $message,
                    array('statut_demande_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour les dates importantes de la retraite
     *
     * @param User   $user                 Utilisateur effectuant l'action
     * @param string $date_depart_prevue   Date prévue de départ (YYYY-MM-DD)
     * @param string $date_depart_effective Date effective de départ (YYYY-MM-DD)
     * @param string $date_debut_versement  Date de début de versement de la pension (YYYY-MM-DD)
     * @param string $commentaire          Commentaire optionnel
     * @return int                         <0 si erreur, >0 si OK
     */
    public function configurerDates($user, $date_depart_prevue, $date_depart_effective = null, $date_debut_versement = null, $commentaire = '')
    {
        // Vérification des dates
        if (empty($date_depart_prevue)) {
            $this->error = 'DateDepartPrevueObligatoire';
            return -1;
        }
        
        // Vérification cohérence des dates
        if ($date_depart_effective && $date_depart_effective < $date_depart_prevue) {
            $this->error = 'DateDepartEffectiveNeDoitPasEtreAnterieureADateDepartPrevue';
            return -1;
        }
        
        if ($date_depart_effective && $date_debut_versement && $date_debut_versement < $date_depart_effective) {
            $this->error = 'DateDebutVersementNeDoitPasEtreAnterieureADateDepartEffective';
            return -1;
        }
        
        $anciennes_dates = array(
            'prevue' => $this->date_depart_prevue,
            'effective' => $this->date_depart_effective,
            'versement' => $this->date_debut_versement
        );
        
        $this->date_depart_prevue = $date_depart_prevue;
        
        if ($date_depart_effective !== null) {
            $this->date_depart_effective = $date_depart_effective;
        }
        
        if ($date_debut_versement !== null) {
            $this->date_debut_versement = $date_debut_versement;
        }
        
        // Mise à jour de l'échéance de la démarche
        if ($this->date_echeance != $date_depart_prevue) {
            $this->date_echeance = $date_depart_prevue;
        }
        
        // Mise à jour automatique du statut si nécessaire
        if (!empty($date_debut_versement) && $this->statut_demande_code != 'VERSEMENT_EN_COURS') {
            $this->statut_demande_code = 'VERSEMENT_EN_COURS';
        } else if (!empty($date_depart_effective) && $this->statut_demande_code == 'PREPARATION') {
            $this->statut_demande_code = 'EN_COURS_CONSTITUTION';
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Date prévue: ".($anciennes_dates['prevue'] ? dol_print_date($this->db->jdate($anciennes_dates['prevue']), 'day') : 'Non définie');
        $details .= " → ".dol_print_date($this->db->jdate($this->date_depart_prevue), 'day');
        
        if ($date_depart_effective !== null) {
            $details .= "; Date effective: ".($anciennes_dates['effective'] ? dol_print_date($this->db->jdate($anciennes_dates['effective']), 'day') : 'Non définie');
            $details .= " → ".($this->date_depart_effective ? dol_print_date($this->db->jdate($this->date_depart_effective), 'day') : 'Non définie');
        }
        
        if ($date_debut_versement !== null) {
            $details .= "; Date versement: ".($anciennes_dates['versement'] ? dol_print_date($this->db->jdate($anciennes_dates['versement']), 'day') : 'Non définie');
            $details .= " → ".($this->date_debut_versement ? dol_print_date($this->db->jdate($this->date_debut_versement), 'day') : 'Non définie');
        }
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_DATES', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_DATES_RETRAITE';
                
                $message = 'Configuration des dates de retraite pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_retraite',
                    $this->id,
                    $message,
                    array(
                        'date_depart_prevue' => array($anciennes_dates['prevue'], $this->date_depart_prevue)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour les informations de trimestres et calculs de pension
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param int    $nb_trimestres_cotises Nombre de trimestres cotisés
     * @param int    $nb_trimestres_valides Nombre de trimestres validés
     * @param int    $nb_trimestres_requis  Nombre de trimestres requis pour le taux plein
     * @param double $taux_decote           Taux de décote appliqué (optionnel)
     * @param double $taux_surcote          Taux de surcote appliqué (optionnel)
     * @param string $commentaire           Commentaire optionnel
     * @return int                          <0 si erreur, >0 si OK
     */
    public function updateTrimestres($user, $nb_trimestres_cotises, $nb_trimestres_valides, $nb_trimestres_requis, $taux_decote = null, $taux_surcote = null, $commentaire = '')
    {
        // Vérifications
        if ($nb_trimestres_cotises < 0 || $nb_trimestres_valides < 0 || $nb_trimestres_requis < 0) {
            $this->error = 'NombreTrimestresMustBePositive';
            return -1;
        }
        
        if (($taux_decote !== null && $taux_decote < 0) || ($taux_surcote !== null && $taux_surcote < 0)) {
            $this->error = 'TauxDecoteSurcoteMustBePositive';
            return -1;
        }
        
        if ($taux_decote !== null && $taux_surcote !== null && $taux_decote > 0 && $taux_surcote > 0) {
            $this->error = 'CantHaveBothDecoteAndSurcote';
            return -1;
        }
        
        $anciens_trimestres = array(
            'cotises' => $this->nb_trimestres_cotises,
            'valides' => $this->nb_trimestres_valides,
            'requis' => $this->nb_trimestres_requis,
            'decote' => $this->taux_decote,
            'surcote' => $this->taux_surcote
        );
        
        $this->nb_trimestres_cotises = $nb_trimestres_cotises;
        $this->nb_trimestres_valides = $nb_trimestres_valides;
        $this->nb_trimestres_requis = $nb_trimestres_requis;
        
        if ($taux_decote !== null) {
            $this->taux_decote = $taux_decote;
        }
        
        if ($taux_surcote !== null) {
            $this->taux_surcote = $taux_surcote;
        }
        
        // Détection automatique des statuts spécifiques
        if ($this->nb_trimestres_valides >= $this->nb_trimestres_requis && $this->carriere_longue == 0) {
            // Mise à jour automatique du statut carrière longue si les trimestres valident cette condition
            $this->carriere_longue = 1;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Trimestres cotisés: ".($anciens_trimestres['cotises'] ?: '0')." → ".$this->nb_trimestres_cotises;
        $details .= "; Trimestres validés: ".($anciens_trimestres['valides'] ?: '0')." → ".$this->nb_trimestres_valides;
        $details .= "; Trimestres requis: ".($anciens_trimestres['requis'] ?: '0')." → ".$this->nb_trimestres_requis;
        
        if ($taux_decote !== null) {
            $details .= "; Taux décote: ".($anciens_trimestres['decote'] !== null ? $anciens_trimestres['decote'].'%' : 'N/A');
            $details .= " → ".($this->taux_decote !== null ? $this->taux_decote.'%' : 'N/A');
        }
        
        if ($taux_surcote !== null) {
            $details .= "; Taux surcote: ".($anciens_trimestres['surcote'] !== null ? $anciens_trimestres['surcote'].'%' : 'N/A');
            $details .= " → ".($this->taux_surcote !== null ? $this->taux_surcote.'%' : 'N/A');
        }
        
        $this->ajouterActionHistorique($user, 'MISE_A_JOUR_TRIMESTRES', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_TRIMESTRES_RETRAITE';
                
                $message = 'Mise à jour des trimestres pour la démarche retraite "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_retraite',
                    $this->id,
                    $message,
                    array(
                        'nb_trimestres_cotises' => array($anciens_trimestres['cotises'], $this->nb_trimestres_cotises),
                        'nb_trimestres_valides' => array($anciens_trimestres['valides'], $this->nb_trimestres_valides),
                        'nb_trimestres_requis' => array($anciens_trimestres['requis'], $this->nb_trimestres_requis)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour les montants de pension
     *
     * @param User   $user                    Utilisateur effectuant l'action
     * @param double $montant_pension_estime   Montant estimé de la pension
     * @param double $montant_pension_reel     Montant réel de la pension (optionnel)
     * @param string $commentaire              Commentaire optionnel
     * @return int                             <0 si erreur, >0 si OK
     */
    public function updateMontantsPension($user, $montant_pension_estime, $montant_pension_reel = null, $commentaire = '')
    {
        $anciens_montants = array(
            'estime' => $this->montant_pension_estime,
            'reel' => $this->montant_pension_reel
        );
        
        $this->montant_pension_estime = $montant_pension_estime;
        
        if ($montant_pension_reel !== null) {
            $this->montant_pension_reel = $montant_pension_reel;
        }
        
        // Mise à jour automatique du statut si nécessaire
        if ($montant_pension_reel !== null && $montant_pension_reel > 0 && $this->statut_demande_code != 'ACCORDEE' && $this->statut_demande_code != 'VERSEMENT_EN_COURS') {
            $this->statut_demande_code = 'ACCORDEE';
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Pension estimée: ".price($anciens_montants['estime'])." → ".price($this->montant_pension_estime);
        
        if ($montant_pension_reel !== null) {
            $details .= "; Pension réelle: ".price($anciens_montants['reel'])." → ".price($this->montant_pension_reel);
        }
        
        $this->ajouterActionHistorique($user, 'MISE_A_JOUR_MONTANTS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_MONTANTS_PENSION';
                
                $message = 'Mise à jour des montants de pension pour la démarche retraite "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_retraite',
                    $this->id,
                    $message,
                    array(
                        'montant_pension_estime' => array($anciens_montants['estime'], $this->montant_pension_estime)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour les organismes complémentaires concernés par la retraite
     *
     * @param User   $user                     Utilisateur effectuant l'action
     * @param string $regimes_complementaires  Liste des codes de régimes séparés par des virgules
     * @param array  $organismes               Tableau des organismes concernés
     * @param string $commentaire              Commentaire optionnel
     * @return int                             <0 si erreur, >0 si OK
     */
    public function updateOrganismes($user, $regimes_complementaires, $organismes = array(), $commentaire = '')
    {
        $anciens_regimes = $this->regimes_complementaires;
        $anciens_organismes = $this->organismes_concernes;
        
        $this->regimes_complementaires = $regimes_complementaires;
        
        if (!empty($organismes)) {
            $this->organismes_concernes = json_encode($organismes);
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Régimes complémentaires: ".($anciens_regimes ?: 'Non définis')." → ".($this->regimes_complementaires ?: 'Non définis');
        
        if (!empty($organismes)) {
            $details .= "; Organismes mis à jour";
        }
        
        $this->ajouterActionHistorique($user, 'MISE_A_JOUR_ORGANISMES', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_ORGANISMES_RETRAITE';
                
                $message = 'Mise à jour des organismes de retraite pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_retraite',
                    $this->id,
                    $message,
                    array('regimes_complementaires' => array($anciens_regimes, $this->regimes_complementaires))
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
                $action = 'MAJ_PIECES_RETRAITE';
                
                $message = 'Mise à jour des pièces justificatives pour la démarche retraite "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_retraite',
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
            array('libelle' => 'Carte d\'identité', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Relevé de carrière', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'RIB', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Livret de famille', 'obligatoire' => 0, 'fourni' => 0)
        );
        
        // Ajouter des pièces spécifiques selon le type de retraite
        if ($this->type_retraite_code == 'RETRAITE_ANTICIPEE') {
            $pieces[] = array('libelle' => 'Justificatif de situation d\'inaptitude', 'obligatoire' => 1, 'fourni' => 0);
        } else if ($this->type_retraite_code == 'REVERSION') {
            $pieces[] = array('libelle' => 'Acte de décès du conjoint', 'obligatoire' => 1, 'fourni' => 0);
            $pieces[] = array('libelle' => 'Acte de mariage', 'obligatoire' => 1, 'fourni' => 0);
        }
        
        // Si handicap reconnu
        if ($this->handicap_reconnu) {
            $pieces[] = array('libelle' => 'Justificatif MDPH', 'obligatoire' => 1, 'fourni' => 0);
        }
        
        return json_encode($pieces);
    }

    /**
     * Ajoute une entrée à l'historique des actions spécifiques de la démarche retraite
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
     * Ajoute une action à l'historique spécifique de la démarche retraite
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
                        case 'MISE_A_JOUR_MONTANTS':
                            $class = 'bg-success';
                            break;
                        case 'MISE_A_JOUR_TRIMESTRES':
                            $class = 'bg-warning';
                            break;
                        case 'CONFIGURATION_DATES':
                            $class = 'bg-purple';
                            break;
                        case 'MISE_A_JOUR_ORGANISMES':
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
     * Obtient le libellé du type de retraite
     * 
     * @return string Libellé du type de retraite
     */
    public function getTypeRetraiteLabel()
    {
        $types = self::getTypeRetraiteOptions($this->langs);
        return isset($types[$this->type_retraite_code]) ? $types[$this->type_retraite_code] : $this->type_retraite_code;
    }
    
    /**
     * Obtient le libellé de l'organisme principal
     * 
     * @return string Libellé de l'organisme
     */
    public function getOrganismePrincipalLabel()
    {
        $organismes = self::getOrganismePrincipalOptions($this->langs);
        return isset($organismes[$this->organisme_principal_code]) ? $organismes[$this->organisme_principal_code] : $this->organisme_principal_code;
    }
    
    /**
     * Liste des statuts de demande valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsDemandeValides()
    {
        return array(
            'PREPARATION',          // Préparation de la demande
            'EN_COURS_CONSTITUTION', // Constitution du dossier en cours
            'DEPOSEE',              // Demande déposée
            'EN_INSTRUCTION',       // Demande en cours d'instruction
            'INCOMPLETE',           // Dossier incomplet
            'ACCORDEE',             // Retraite accordée
            'REFUSEE',              // Retraite refusée
            'VERSEMENT_EN_COURS',   // Versement en cours
            'SUSPENDUE',            // Retraite suspendue
            'RECALCUL_EN_COURS'     // Recalcul en cours
        );
    }
    
    /**
     * Liste des types de retraite valides
     *
     * @return array Codes des types de retraite valides
     */
    public static function getTypesRetraiteValides()
    {
        return array(
            'RETRAITE_BASE',        // Retraite de base
            'RETRAITE_COMPLEMENTAIRE', // Retraite complémentaire
            'RETRAITE_ANTICIPEE',   // Retraite anticipée
            'REVERSION',            // Pension de réversion
            'INVALIDITE',           // Pension d'invalidité
            'ASPA',                 // Allocation de Solidarité aux Personnes Âgées
            'CUMUL_EMPLOI_RETRAITE', // Cumul emploi-retraite
            'SURCOTE',              // Demande de surcote
            'RACHAT_TRIMESTRES',    // Rachat de trimestres
            'REGULARISATION',       // Régularisation de carrière
            'AUTRE'                 // Autre type de démarche retraite
        );
    }
    
    /**
     * Liste des organismes principaux valides
     *
     * @return array Codes des organismes valides
     */
    public static function getOrganismesPrincipauxValides()
    {
        return array(
            'CARSAT',       // Caisse d'Assurance Retraite et de la Santé au Travail
            'CNAV',         // Caisse Nationale d'Assurance Vieillesse
            'MSA',          // Mutualité Sociale Agricole
            'SSI',          // Sécurité Sociale des Indépendants (ex-RSI)
            'CNRACL',       // Caisse Nationale de Retraites des Agents des Collectivités Locales
            'AGIRC_ARRCO',  // AGIRC-ARRCO
            'IRCANTEC',     // Institution de Retraite Complémentaire des Agents Non Titulaires de l'État
            'CNAVPL',       // Caisse Nationale d'Assurance Vieillesse des Professions Libérales
            'CNIEG',        // Caisse Nationale des Industries Électriques et Gazières
            'CRPCEN',       // Caisse de Retraite et de Prévoyance des Clercs et Employés de Notaires
            'RAFP',         // Retraite Additionnelle de la Fonction Publique
            'SRE',          // Service des Retraites de l'État
            'AUTRE'         // Autre organisme
        );
    }
    
    /**
     * Récupère les options du dictionnaire des types de retraite
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeRetraiteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'retraite_type_retraite', $usekeys, $show_empty);
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
        return self::getOptionsFromDictionary($langs, 'retraite_statut_demande', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des organismes principaux
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getOrganismePrincipalOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'retraite_organisme_principal', $usekeys, $show_empty);
    }
    
    /**
     * Vérifie si cette démarche spécifique est une démarche de retraite
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isRetraite()
    {
        return true;
    }
    
    /**
     * Récupère le contact conseiller retraite associé à la démarche
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
    const ACTION_MISE_A_JOUR_MONTANTS = 'MISE_A_JOUR_MONTANTS';
    const ACTION_MISE_A_JOUR_TRIMESTRES = 'MISE_A_JOUR_TRIMESTRES';
    const ACTION_CONFIGURATION_DATES = 'CONFIGURATION_DATES';
    const ACTION_MISE_A_JOUR_ORGANISMES = 'MISE_A_JOUR_ORGANISMES';
    const ACTION_MISE_A_JOUR_PIECES = 'MISE_A_JOUR_PIECES';
    const ACTION_CONTACT_CONSEILLER = 'CONTACT_CONSEILLER';
    const ACTION_ENVOI_DOSSIER = 'ENVOI_DOSSIER';
    const ACTION_RECEPTION_NOTIFICATION = 'RECEPTION_NOTIFICATION';
    const ACTION_NOTE_INTERNE = 'NOTE_INTERNE';
}

} // Fin de la condition if !class_exists
