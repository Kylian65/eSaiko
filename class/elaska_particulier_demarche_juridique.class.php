<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches juridiques des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 * Dernière modification: 2025-06-03 17:38:07 UTC
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarcheJuridique', false)) {

class ElaskaParticulierDemarcheJuridique extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_juridique';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_juridique';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AUX DÉMARCHES JURIDIQUES
    //
    
    /**
     * @var string Numéro de dossier juridique
     */
    public $numero_dossier;
    
    /**
     * @var string Code du type de démarche juridique (dictionnaire)
     */
    public $type_demarche_juridique_code;
    
    /**
     * @var string Code du statut de la démarche (dictionnaire)
     */
    public $statut_demarche_juridique_code;
    
    /**
     * @var string Juridiction concernée
     */
    public $juridiction;
    
    /**
     * @var string Type de procédure 
     */
    public $type_procedure;
    
    /**
     * @var string Date de début de la procédure (format YYYY-MM-DD)
     */
    public $date_debut_procedure;
    
    /**
     * @var string Date de la prochaine audience (format YYYY-MM-DD)
     */
    public $date_audience;
    
    /**
     * @var string Heure de la prochaine audience (format HH:MM:SS)
     */
    public $heure_audience;
    
    /**
     * @var string Lieu de l'audience
     */
    public $lieu_audience;
    
    /**
     * @var string Date du jugement (format YYYY-MM-DD)
     */
    public $date_jugement;
    
    /**
     * @var string Décision de justice
     */
    public $decision_justice;
    
    /**
     * @var string Identité de la partie adverse
     */
    public $partie_adverse;
    
    /**
     * @var string Nom de l'avocat
     */
    public $nom_avocat;
    
    /**
     * @var string Coordonnées de l'avocat
     */
    public $coordonnees_avocat;
    
    /**
     * @var string Date de la désignation de l'avocat (format YYYY-MM-DD)
     */
    public $date_designation_avocat;
    
    /**
     * @var int ID du contact avocat
     */
    public $fk_contact_avocat;
    
    /**
     * @var string Référence BAJ (Bureau d'Aide Juridictionnelle)
     */
    public $reference_baj;
    
    /**
     * @var string Date de la demande d'aide juridictionnelle (format YYYY-MM-DD)
     */
    public $date_demande_aj;
    
    /**
     * @var string Date de décision d'aide juridictionnelle (format YYYY-MM-DD)
     */
    public $date_decision_aj;
    
    /**
     * @var string Type d'aide juridictionnelle (totale, partielle)
     */
    public $type_aide_juridictionnelle;
    
    /**
     * @var string Pourcentage d'aide juridictionnelle
     */
    public $pourcentage_aj;
    
    /**
     * @var int Recours ou appel (0=non, 1=oui)
     */
    public$recours_appel;
    
    /**
     * @var string Date du recours ou appel (format YYYY-MM-DD)
     */
    public $date_recours;
    
    /**
     * @var string Parquet compétent
     */
    public $parquet_competent;
    
    /**
     * @var string Juge en charge du dossier
     */
    public $juge_en_charge;
    
    /**
     * @var string Numéro de rôle
     */
    public $numero_role;
    
    /**
     * @var string Référence de l'huissier
     */
    public $reference_huissier;
    
    /**
     * @var string Nom de l'huissier
     */
    public $nom_huissier;
    
    /**
     * @var int ID du contact huissier
     */
    public $fk_contact_huissier;
    
    /**
     * @var string Actes de procédure (format JSON)
     */
    public $actes_procedure;
    
    /**
     * @var double Montant des frais engagés
     */
    public $montant_frais;
    
    /**
     * @var double Montant des indemnités
     */
    public $montant_indemnites;
    
    /**
     * @var double Montant des dommages et intérêts
     */
    public $montant_dommages_interets;
    
    /**
     * @var string Modalités d'exécution du jugement
     */
    public $modalites_execution;
    
    /**
     * @var string Historique des actions spécifiques à la démarche juridique
     */
    public $historique_actions;
    
    /**
     * @var string Pièces justificatives à fournir (format JSON)
     */
    public $pieces_justificatives;
    
    /**
     * @var int Médiation tentée (0=non, 1=oui)
     */
    public $mediation_tentee;
    
    /**
     * @var string Date de la médiation (format YYYY-MM-DD)
     */
    public $date_mediation;
    
    /**
     * @var string Résultat de la médiation
     */
    public $resultat_mediation;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_juridique = array(
        'numero_dossier' => array('type' => 'varchar(50)', 'label' => 'NumeroDossier', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'type_demarche_juridique_code' => array('type' => 'varchar(50)', 'label' => 'TypeDemarcheJuridique', 'enabled' => 1, 'position' => 1110, 'notnull' => 1, 'visible' => 1),
        'statut_demarche_juridique_code' => array('type' => 'varchar(50)', 'label' => 'StatutDemarcheJuridique', 'enabled' => 1, 'position' => 1120, 'notnull' => 1, 'visible' => 1),
        'juridiction' => array('type' => 'varchar(255)', 'label' => 'Juridiction', 'enabled' => 1, 'position' => 1130, 'notnull' => 0, 'visible' => 1),
        'type_procedure' => array('type' => 'varchar(50)', 'label' => 'TypeProcedure', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'date_debut_procedure' => array('type' => 'date', 'label' => 'DateDebutProcedure', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'date_audience' => array('type' => 'date', 'label' => 'DateAudience', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'heure_audience' => array('type' => 'varchar(8)', 'label' => 'HeureAudience', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'lieu_audience' => array('type' => 'varchar(255)', 'label' => 'LieuAudience', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'date_jugement' => array('type' => 'date', 'label' => 'DateJugement', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'decision_justice' => array('type' => 'text', 'label' => 'DecisionJustice', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'partie_adverse' => array('type' => 'varchar(255)', 'label' => 'PartieAdverse', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'nom_avocat' => array('type' => 'varchar(255)', 'label' => 'NomAvocat', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'coordonnees_avocat' => array('type' => 'varchar(255)', 'label' => 'CoordonneesAvocat', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'date_designation_avocat' => array('type' => 'date', 'label' => 'DateDesignationAvocat', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'fk_contact_avocat' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactAvocat', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'reference_baj' => array('type' => 'varchar(50)', 'label' => 'ReferenceBAJ', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1),
        'date_demande_aj' => array('type' => 'date', 'label' => 'DateDemandeAJ', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'date_decision_aj' => array('type' => 'date', 'label' => 'DateDecisionAJ', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'type_aide_juridictionnelle' => array('type' => 'varchar(20)', 'label' => 'TypeAideJuridictionnelle', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1),
        'pourcentage_aj' => array('type' => 'varchar(10)', 'label' => 'PourcentageAJ', 'enabled' => 1, 'position' => 1300, 'notnull' => 0, 'visible' => 1),
        'recours_appel' => array('type' => 'boolean', 'label' => 'RecoursAppel', 'enabled' => 1, 'position' => 1310, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'date_recours' => array('type' => 'date', 'label' => 'DateRecours', 'enabled' => 1, 'position' => 1320, 'notnull' => 0, 'visible' => 1),
        'parquet_competent' => array('type' => 'varchar(255)', 'label' => 'ParquetCompetent', 'enabled' => 1, 'position' => 1330, 'notnull' => 0, 'visible' => 1),
        'juge_en_charge' => array('type' => 'varchar(255)', 'label' => 'JugeEnCharge', 'enabled' => 1, 'position' => 1340, 'notnull' => 0, 'visible' => 1),
        'numero_role' => array('type' => 'varchar(50)', 'label' => 'NumeroRole', 'enabled' => 1, 'position' => 1350, 'notnull' => 0, 'visible' => 1),
        'reference_huissier' => array('type' => 'varchar(50)', 'label' => 'ReferenceHuissier', 'enabled' => 1, 'position' => 1360, 'notnull' => 0, 'visible' => 1),
        'nom_huissier' => array('type' => 'varchar(255)', 'label' => 'NomHuissier', 'enabled' => 1, 'position' => 1370, 'notnull' => 0, 'visible' => 1),
        'fk_contact_huissier' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactHuissier', 'enabled' => 1, 'position' => 1380, 'notnull' => 0, 'visible' => 1),
        'actes_procedure' => array('type' => 'text', 'label' => 'ActesProcedure', 'enabled' => 1, 'position' => 1390, 'notnull' => 0, 'visible' => 1),
        'montant_frais' => array('type' => 'double(24,8)', 'label' => 'MontantFrais', 'enabled' => 1, 'position' => 1400, 'notnull' => 0, 'visible' => 1),
        'montant_indemnites' => array('type' => 'double(24,8)', 'label' => 'MontantIndemnites', 'enabled' => 1, 'position' => 1410, 'notnull' => 0, 'visible' => 1),
        'montant_dommages_interets' => array('type' => 'double(24,8)', 'label' => 'MontantDommagesInterets', 'enabled' => 1, 'position' => 1420, 'notnull' => 0, 'visible' => 1),
        'modalites_execution' => array('type' => 'text', 'label' => 'ModalitesExecution', 'enabled' => 1, 'position' => 1430, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 1440, 'notnull' => 0, 'visible' => 1),
        'pieces_justificatives' => array('type' => 'text', 'label' => 'PiecesJustificatives', 'enabled' => 1, 'position' => 1450, 'notnull' => 0, 'visible' => 1),
        'mediation_tentee' => array('type' => 'boolean', 'label' => 'MediationTentee', 'enabled' => 1, 'position' => 1460, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'date_mediation' => array('type' => 'date', 'label' => 'DateMediation', 'enabled' => 1, 'position' => 1470, 'notnull' => 0, 'visible' => 1),
        'resultat_mediation' => array('type' => 'text', 'label' => 'ResultatMediation', 'enabled' => 1, 'position' => 1480, 'notnull' => 0, 'visible' => 1)
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Fusion des champs spécifiques juridiques avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_juridique);
        
        // Valeurs par défaut spécifiques aux démarches juridiques
        $this->type_demarche_code = 'JURIDIQUE';  // Force le code de type de démarche
        if (!isset($this->statut_demarche_juridique_code)) $this->statut_demarche_juridique_code = 'PREPARATION'; // Statut par défaut
        if (!isset($this->recours_appel)) $this->recours_appel = 0;
        if (!isset($this->mediation_tentee)) $this->mediation_tentee = 0;
    }

    /**
     * Crée une démarche juridique dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à JURIDIQUE
        $this->type_demarche_code = 'JURIDIQUE';
        
        // Vérifications spécifiques aux démarches juridiques
        if (empty($this->type_demarche_juridique_code)) {
            $this->error = 'TypeDemarcheJuridiqueIsMandatory';
            return -1;
        }
        
        // Génération automatique du libellé s'il n'est pas fourni
        if (empty($this->libelle)) {
            $this->libelle = $this->getTypeDemarcheJuridiqueLabel();
            if (!empty($this->numero_dossier)) {
                $this->libelle .= ' n°' . $this->numero_dossier;
            }
            if (!empty($this->juridiction)) {
                $this->libelle .= ' - ' . $this->juridiction;
            }
        }
        
        // Initialisation des pièces justificatives
        if (empty($this->pieces_justificatives)) {
            $this->pieces_justificatives = $this->getPiecesJustificativesParDefaut();
        }
        
        // Initialisation des actes de procédure
        if (empty($this->actes_procedure)) {
            $this->actes_procedure = json_encode(array());
        }
        
        // Si date de début de procédure non renseignée, mettre par défaut aujourd'hui
        if (empty($this->date_debut_procedure)) {
            $this->date_debut_procedure = date('Y-m-d');
        }
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'une démarche juridique
            $note = "Création d'une démarche juridique de type " . $this->getTypeDemarcheJuridiqueLabel();
            if (!empty($this->juridiction)) {
                $note .= " concernant la juridiction " . $this->juridiction;
            }
            if (!empty($this->numero_dossier)) {
                $note .= " (dossier n°" . $this->numero_dossier . ")";
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
     * Met à jour une démarche juridique dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à JURIDIQUE
        $this->type_demarche_code = 'JURIDIQUE';
        
        // Vérifications spécifiques aux démarches juridiques
        if (empty($this->type_demarche_juridique_code)) {
            $this->error = 'TypeDemarcheJuridiqueIsMandatory';
            return -1;
        }
        
        // Si appel/recours est indiqué, vérifier qu'il y a une date
        if ($this->recours_appel && empty($this->date_recours)) {
            $this->error = 'DateRecoursRequiredWhenRecoursAppel';
            return -1;
        }
        
        // Si médiation tentée, vérifier qu'il y a une date
        if ($this->mediation_tentee && empty($this->date_mediation)) {
            $this->error = 'DateMediationRequiredWhenMediationTentee';
            return -1;
        }
        
        // Vérification de la cohérence des dates
        if (!empty($this->date_debut_procedure) && !empty($this->date_audience) && $this->date_audience < $this->date_debut_procedure) {
            $this->error = 'DateAudienceCantBeBeforeDateDebutProcedure';
            return -1;
        }
        
        if (!empty($this->date_audience) && !empty($this->date_jugement) && $this->date_jugement < $this->date_audience) {
            $this->error = 'DateJugementCantBeBeforeDateAudience';
            return -1;
        }
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le statut de la démarche juridique
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau code de statut
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function changerStatutDemarcheJuridique($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsDemarcheJuridiqueValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatutDemarcheJuridiqueCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_demarche_juridique_code;
        $this->statut_demarche_juridique_code = $statut_code;
        
        // Mises à jour automatiques selon le statut
        switch ($statut_code) {
            case 'CONSULTATION':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 20; // 20% de progression
                break;
                
            case 'SAISINE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 30; // 30% de progression
                break;
                
            case 'INSTRUCTION':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 40; // 40% de progression
                break;
                
            case 'AUDIENCE_PROGRAMMEE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 60; // 60% de progression
                break;
                
            case 'DELIBERE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 80; // 80% de progression
                break;
                
            case 'JUGEMENT_RENDU':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 90; // 90% de progression
                if (empty($this->date_jugement)) {
                    $this->date_jugement = date('Y-m-d');
                }
                break;
                
            case 'EXECUTION':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 95; // 95% de progression
                break;
                
            case 'CLOTURE':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'RECOURS_APPEL':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 70; // Retour à 70% de progression
                $this->recours_appel = 1; // Active l'indicateur de recours/appel
                if (empty($this->date_recours)) {
                    $this->date_recours = date('Y-m-d');
                }
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
                $action = 'CHANGEMENT_STATUT_JURIDIQUE';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutDemarcheJuridiqueOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message,
                    array('statut_demarche_juridique_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre des informations sur la procédure juridique
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param string $juridiction           Juridiction concernée
     * @param string $type_procedure        Type de procédure
     * @param string $parquet_competent     Parquet compétent (optionnel)
     * @param string $numero_role           Numéro de rôle (optionnel)
     * @param string $commentaire          Commentaire optionnel
     * @return int                          <0 si erreur, >0 si OK
     */
    public function enregistrerProcedure($user, $juridiction, $type_procedure, $parquet_competent = '', $numero_role = '', $commentaire = '')
    {
        // Vérifications
        if (empty($juridiction)) {
            $this->error = 'JuridictionObligatoire';
            return -1;
        }
        
        if (empty($type_procedure)) {
            $this->error = 'TypeProcedureObligatoire';
            return -1;
        }
        
        $ancienne_juridiction = $this->juridiction;
        $ancien_type_procedure = $this->type_procedure;
        $ancien_parquet = $this->parquet_competent;
        $ancien_numero_role = $this->numero_role;
        
        $this->juridiction = $juridiction;
        $this->type_procedure = $type_procedure;
        
        if (!empty($parquet_competent)) {
            $this->parquet_competent = $parquet_competent;
        }
        
        if (!empty($numero_role)) {
            $this->numero_role = $numero_role;
        }
        
        // Mise à jour du statut
        if ($this->statut_demarche_juridique_code == 'PREPARATION') {
            $this->statut_demarche_juridique_code = 'SAISINE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 30;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Juridiction: ".($ancienne_juridiction ?: 'Non définie')." → ".$this->juridiction;
        $details .= "; Type de procédure: ".($ancien_type_procedure ?: 'Non défini')." → ".$this->type_procedure;
        
        if (!empty($parquet_competent)) {
            $details .= "; Parquet compétent: ".($ancien_parquet ?: 'Non défini')." → ".$this->parquet_competent;
        }
        
        if (!empty($numero_role)) {
            $details .= "; Numéro de rôle: ".($ancien_numero_role ?: 'Non défini')." → ".$this->numero_role;
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_PROCEDURE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_PROCEDURE_JURIDIQUE';
                
                $message = 'Enregistrement des informations de procédure pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message,
                    array(
                        'juridiction' => array($ancienne_juridiction, $this->juridiction),
                        'type_procedure' => array($ancien_type_procedure, $this->type_procedure)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une audience
     *
     * @param User   $user               Utilisateur effectuant l'action
     * @param string $date_audience       Date de l'audience (YYYY-MM-DD)
     * @param string $heure_audience      Heure de l'audience (HH:MM:SS)
     * @param string $lieu_audience       Lieu de l'audience
     * @param string $juge_en_charge      Juge en charge du dossier (optionnel)
     * @param string $commentaire        Commentaire optionnel
     * @return int                        <0 si erreur, >0 si OK
     */
    public function enregistrerAudience($user, $date_audience, $heure_audience, $lieu_audience, $juge_en_charge = '', $commentaire = '')
    {
        // Vérifications
        if (empty($date_audience)) {
            $this->error = 'DateAudienceObligatoire';
            return -1;
        }
        
        if (empty($lieu_audience)) {
            $this->error = 'LieuAudienceObligatoire';
            return -1;
        }
        
        // Vérification de l'heure au format HH:MM ou HH:MM:SS
        if (!empty($heure_audience) && !preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $heure_audience)) {
            $this->error = 'FormatHeureInvalide';
            return -1;
        }
        
        $ancienne_date_audience = $this->date_audience;
        $ancienne_heure_audience = $this->heure_audience;
        $ancien_lieu_audience = $this->lieu_audience;
        $ancien_juge = $this->juge_en_charge;
        
        $this->date_audience = $date_audience;
        $this->heure_audience = $heure_audience;
        $this->lieu_audience = $lieu_audience;
        
        if (!empty($juge_en_charge)) {
            $this->juge_en_charge = $juge_en_charge;
        }
        
        // Mise à jour du statut
        if ($this->statut_demarche_juridique_code != 'AUDIENCE_PROGRAMMEE' && 
            $this->statut_demarche_juridique_code != 'DELIBERE' && 
            $this->statut_demarche_juridique_code != 'JUGEMENT_RENDU' && 
            $this->statut_demarche_juridique_code != 'EXECUTION' &&
            $this->statut_demarche_juridique_code != 'CLOTURE') {
            $this->statut_demarche_juridique_code = 'AUDIENCE_PROGRAMMEE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 60;
        }
        
        // Mise à jour de la date d'échéance sur la démarche principale si nécessaire
        if ($this->date_audience > $this->date_echeance || empty($this->date_echeance)) {
            $this->date_echeance = $this->date_audience;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Audience programmée le " . dol_print_date($this->db->jdate($date_audience), 'day');
        $details .= " à " . $this->heure_audience;
        $details .= " - Lieu: " . $this->lieu_audience;
        
        if (!empty($juge_en_charge)) {
            $details .= "; Juge: ".($ancien_juge ?: 'Non défini')." → ".$this->juge_en_charge;
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_AUDIENCE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_AUDIENCE_JURIDIQUE';
                
                $message = 'Enregistrement d\'audience pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message,
                    array(
                        'date_audience' => array($ancienne_date_audience, $this->date_audience),
                        'lieu_audience' => array($ancien_lieu_audience, $this->lieu_audience)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre les coordonnées d'un avocat
     *
     * @param User   $user                     Utilisateur effectuant l'action
     * @param string $nom_avocat                Nom de l'avocat
     * @param string $coordonnees_avocat        Coordonnées de l'avocat
     * @param string $date_designation_avocat   Date de désignation de l'avocat (YYYY-MM-DD)
     * @param int    $fk_contact_avocat         ID du contact avocat dans Dolibarr (optionnel)
     * @param string $commentaire              Commentaire optionnel
     * @return int                              <0 si erreur, >0 si OK
     */
    public function enregistrerAvocat($user, $nom_avocat, $coordonnees_avocat, $date_designation_avocat, $fk_contact_avocat = 0, $commentaire = '')
    {
        // Vérifications
        if (empty($nom_avocat)) {
            $this->error = 'NomAvocatObligatoire';
            return -1;
        }
        
        $ancien_nom_avocat = $this->nom_avocat;
        $anciennes_coordonnees = $this->coordonnees_avocat;
        $ancienne_date_designation = $this->date_designation_avocat;
        $ancien_fk_contact = $this->fk_contact_avocat;
        
        $this->nom_avocat = $nom_avocat;
        $this->coordonnees_avocat = $coordonnees_avocat;
        $this->date_designation_avocat = $date_designation_avocat;
        
        if ($fk_contact_avocat > 0) {
            $this->fk_contact_avocat = $fk_contact_avocat;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Avocat désigné: ".($ancien_nom_avocat ?: 'Non défini')." → ".$this->nom_avocat;
        $details .= "; Date de désignation: " . dol_print_date($this->db->jdate($date_designation_avocat), 'day');
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_AVOCAT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_AVOCAT_JURIDIQUE';
                
                $message = 'Enregistrement des coordonnées d\'avocat pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message,
                    array(
                        'nom_avocat' => array($ancien_nom_avocat, $this->nom_avocat),
                        'date_designation_avocat' => array($ancienne_date_designation, $this->date_designation_avocat)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une demande d'aide juridictionnelle
     *
     * @param User   $user                 Utilisateur effectuant l'action
     * @param string $date_demande_aj       Date de la demande d'aide juridictionnelle (YYYY-MM-DD)
     * @param string $reference_baj         Référence du Bureau d'Aide Juridictionnelle
     * @param string $commentaire          Commentaire optionnel
     * @return int                          <0 si erreur, >0 si OK
     */
    public function enregistrerDemandeAJ($user, $date_demande_aj, $reference_baj = '', $commentaire = '')
    {
        // Vérifications
        if (empty($date_demande_aj)) {
            $this->error = 'DateDemandeAJObligatoire';
            return -1;
        }
        
        $ancienne_date_demande = $this->date_demande_aj;
        $ancienne_reference_baj = $this->reference_baj;
        
        $this->date_demande_aj = $date_demande_aj;
        
        if (!empty($reference_baj)) {
            $this->reference_baj = $reference_baj;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Demande d'aide juridictionnelle déposée le " . dol_print_date($this->db->jdate($date_demande_aj), 'day');
        
        if (!empty($reference_baj)) {
            $details .= "; Référence BAJ: " . $this->reference_baj;
        }
        
        $this->ajouterActionHistorique($user, 'DEMANDE_AIDE_JURIDICTIONNELLE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DEMANDE_AJ_JURIDIQUE';
                
                $message = 'Enregistrement d\'une demande d\'aide juridictionnelle pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message,
                    array('date_demande_aj' => array($ancienne_date_demande, $this->date_demande_aj))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une décision d'aide juridictionnelle
     *
     * @param User   $user                   Utilisateur effectuant l'action
     * @param string $date_decision_aj        Date de la décision d'aide juridictionnelle (YYYY-MM-DD)
     * @param string $type_aide_juridictionnelle Type d'aide juridictionnelle (totale, partielle)
     * @param string $pourcentage_aj          Pourcentage d'aide juridictionnelle (pour aide partielle)
     * @param string $commentaire            Commentaire optionnel
     * @return int                            <0 si erreur, >0 si OK
     */
    public function enregistrerDecisionAJ($user, $date_decision_aj, $type_aide_juridictionnelle, $pourcentage_aj = '', $commentaire = '')
    {
        // Vérifications
        if (empty($date_decision_aj)) {
            $this->error = 'DateDecisionAJObligatoire';
            return -1;
        }
        
        if (empty($type_aide_juridictionnelle)) {
            $this->error = 'TypeAideJuridictionnelleObligatoire';
            return -1;
        }
        
        // Si type aide partielle, pourcentage obligatoire
        if ($type_aide_juridictionnelle == 'PARTIELLE' && empty($pourcentage_aj)) {
            $this->error = 'PourcentageAJObligatoirePourAidePartielle';
            return -1;
        }
        
        $ancienne_date_decision = $this->date_decision_aj;
        $ancien_type_aide = $this->type_aide_juridictionnelle;
        $ancien_pourcentage = $this->pourcentage_aj;
        
        $this->date_decision_aj = $date_decision_aj;
        $this->type_aide_juridictionnelle = $type_aide_juridictionnelle;
        
        if ($type_aide_juridictionnelle == 'PARTIELLE' && !empty($pourcentage_aj)) {
            $this->pourcentage_aj = $pourcentage_aj;
        } else if ($type_aide_juridictionnelle == 'TOTALE') {
            $this->pourcentage_aj = '100%';
        } else if ($type_aide_juridictionnelle == 'REJET') {
            $this->pourcentage_aj = '0%';
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Décision d'aide juridictionnelle reçue le " . dol_print_date($this->db->jdate($date_decision_aj), 'day');
        $details .= "; Type: " . $this->type_aide_juridictionnelle;
        
        if ($type_aide_juridictionnelle == 'PARTIELLE' && !empty($pourcentage_aj)) {
            $details .= "; Pourcentage: " . $this->pourcentage_aj;
        }
        
        $this->ajouterActionHistorique($user, 'DECISION_AIDE_JURIDICTIONNELLE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DECISION_AJ_JURIDIQUE';
                
                $message = 'Enregistrement de la décision d\'aide juridictionnelle pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message,
                    array(
                        'date_decision_aj' => array($ancienne_date_decision, $this->date_decision_aj),
                        'type_aide_juridictionnelle' => array($ancien_type_aide, $this->type_aide_juridictionnelle)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un jugement
     *
     * @param User   $user               Utilisateur effectuant l'action
     * @param string $date_jugement       Date du jugement (YYYY-MM-DD)
     * @param string $decision_justice    Décision de justice
     * @param double $montant_indemnites  Montant des indemnités (optionnel)
     * @param double $montant_dommages_interets Montant des dommages et intérêts (optionnel)
     * @param string $commentaire        Commentaire optionnel
     * @return int                        <0 si erreur, >0 si OK
     */
    public function enregistrerJugement($user, $date_jugement, $decision_justice, $montant_indemnites = 0, $montant_dommages_interets = 0, $commentaire = '')
    {
        // Vérifications
        if (empty($date_jugement)) {
            $this->error = 'DateJugementObligatoire';
            return -1;
        }
        
        if (empty($decision_justice)) {
            $this->error = 'DecisionJusticeObligatoire';
            return -1;
        }
        
        $ancienne_date_jugement = $this->date_jugement;
        $ancienne_decision = $this->decision_justice;
        $anciennes_indemnites = $this->montant_indemnites;
        $anciens_dommages = $this->montant_dommages_interets;
        
        $this->date_jugement = $date_jugement;
        $this->decision_justice = $decision_justice;
        
        if ($montant_indemnites > 0) {
            $this->montant_indemnites = $montant_indemnites;
        }
        
        if ($montant_dommages_interets > 0) {
            $this->montant_dommages_interets = $montant_dommages_interets;
        }
        
        // Mise à jour du statut si ce n'est pas déjà fait
        if ($this->statut_demarche_juridique_code != 'JUGEMENT_RENDU' && 
            $this->statut_demarche_juridique_code != 'EXECUTION' && 
            $this->statut_demarche_juridique_code != 'CLOTURE' &&
            $this->statut_demarche_juridique_code != 'RECOURS_APPEL') {
            $this->statut_demarche_juridique_code = 'JUGEMENT_RENDU';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 90;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Jugement rendu le " . dol_print_date($this->db->jdate($date_jugement), 'day');
        $details .= "; Décision: " . substr($this->decision_justice, 0, 100) . (strlen($this->decision_justice) > 100 ? '...' : '');
        
        if ($montant_indemnites > 0) {
            $details .= "; Indemnités: " . price($this->montant_indemnites);
        }
        
        if ($montant_dommages_interets > 0) {
            $details .= "; Dommages et intérêts: " . price($this->montant_dommages_interets);
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_JUGEMENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_JUGEMENT_JURIDIQUE';
                
                $message = 'Enregistrement d\'un jugement pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message,
                    array(
                        'date_jugement' => array($ancienne_date_jugement, $this->date_jugement)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre les modalités d'exécution du jugement
     *
     * @param User   $user                 Utilisateur effectuant l'action
     * @param string $modalites_execution   Modalités d'exécution du jugement
     * @param string $reference_huissier    Référence de l'huissier (optionnel)
     * @param string $nom_huissier          Nom de l'huissier (optionnel)
     * @param int    $fk_contact_huissier   ID du contact huissier (optionnel)
     * @param string $commentaire          Commentaire optionnel
     * @return int                          <0 si erreur, >0 si OK
     */
    public function enregistrerExecution($user, $modalites_execution, $reference_huissier = '', $nom_huissier = '', $fk_contact_huissier = 0, $commentaire = '')
    {
        // Vérifications
        if (empty($modalites_execution)) {
            $this->error = 'ModalitesExecutionObligatoires';
            return -1;
        }
        
        $anciennes_modalites = $this->modalites_execution;
        $ancienne_reference_huissier = $this->reference_huissier;
        $ancien_nom_huissier = $this->nom_huissier;
        
        $this->modalites_execution = $modalites_execution;
        
        if (!empty($reference_huissier)) {
            $this->reference_huissier = $reference_huissier;
        }
        
        if (!empty($nom_huissier)) {
            $this->nom_huissier = $nom_huissier;
        }
        
        if ($fk_contact_huissier > 0) {
            $this->fk_contact_huissier = $fk_contact_huissier;
        }
        
        // Mise à jour du statut si ce n'est pas déjà fait
        if ($this->statut_demarche_juridique_code != 'EXECUTION' && 
            $this->statut_demarche_juridique_code != 'CLOTURE' &&
            $this->statut_demarche_juridique_code != 'RECOURS_APPEL') {
            $this->statut_demarche_juridique_code = 'EXECUTION';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 95;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Modalités d'exécution enregistrées";
        
        if (!empty($nom_huissier)) {
            $details .= "; Huissier: " . $this->nom_huissier;
        }
        
        if (!empty($reference_huissier)) {
            $details .= "; Référence huissier: " . $this->reference_huissier;
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_EXECUTION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_EXECUTION_JURIDIQUE';
                
                $message = 'Enregistrement des modalités d\'exécution pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }

    /**
     * Enregistre une tentative de médiation
     * 
     * @param User   $user               Utilisateur effectuant l'action
     * @param string $date_mediation      Date de la médiation (YYYY-MM-DD)
     * @param string $resultat_mediation  Résultat de la médiation
     * @param string $commentaire        Commentaire optionnel
     * @return int                        <0 si erreur, >0 si OK
     */
    public function enregistrerMediation($user, $date_mediation, $resultat_mediation, $commentaire = '')
    {
        // Vérifications
        if (empty($date_mediation)) {
            $this->error = 'DateMediationObligatoire';
            return -1;
        }
        
        if (empty($resultat_mediation)) {
            $this->error = 'ResultatMediationObligatoire';
            return -1;
        }
        
        $ancienne_mediation_tentee = $this->mediation_tentee;
        $ancienne_date_mediation = $this->date_mediation;
        $ancien_resultat_mediation = $this->resultat_mediation;
        
        $this->mediation_tentee = 1;
        $this->date_mediation = $date_mediation;
        $this->resultat_mediation = $resultat_mediation;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
                // Mise à jour de l'historique spécifique
        $details = "Médiation tentée le " . dol_print_date($this->db->jdate($date_mediation), 'day');
        $details .= "; Résultat: " . substr($this->resultat_mediation, 0, 100) . (strlen($this->resultat_mediation) > 100 ? '...' : '');
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_MEDIATION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_MEDIATION_JURIDIQUE';
                
                $message = 'Enregistrement d\'une médiation pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message,
                    array(
                        'mediation_tentee' => array($ancienne_mediation_tentee, $this->mediation_tentee),
                        'date_mediation' => array($ancienne_date_mediation, $this->date_mediation)
                    )
                );
            }
        }
        
        return $result;
    }

    /**
     * Enregistre un recours ou appel
     * 
     * @param User   $user          Utilisateur effectuant l'action
     * @param string $date_recours   Date du recours (YYYY-MM-DD)
     * @param string $motif_recours  Motif du recours
     * @param string $commentaire   Commentaire optionnel
     * @return int                   <0 si erreur, >0 si OK
     */
    public function enregistrerRecours($user, $date_recours, $motif_recours, $commentaire = '')
    {
        // Vérifications
        if (empty($date_recours)) {
            $this->error = 'DateRecoursObligatoire';
            return -1;
        }
        
        if (empty($motif_recours)) {
            $this->error = 'MotifRecoursObligatoire';
            return -1;
        }
        
        $ancien_recours_appel = $this->recours_appel;
        $ancienne_date_recours = $this->date_recours;
        
        $this->recours_appel = 1;
        $this->date_recours = $date_recours;
        
        // Mise à jour du statut
        if ($this->statut_demarche_juridique_code != 'RECOURS_APPEL') {
            $this->statut_demarche_juridique_code = 'RECOURS_APPEL';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 70; // On revient à un niveau intermédiaire de progression
        }
        
        // Mise à jour de la note
        $note = "Recours/appel enregistré en date du " . dol_print_date($this->db->jdate($date_recours), 'day');
        $note .= "\nMotif : " . $motif_recours;
        
        if (!empty($commentaire)) {
            $note .= "\n" . $commentaire;
        }
        
        $this->addToNotes($user, $note);
        
        // Mise à jour de l'historique spécifique
        $details = "Recours/appel déposé le " . dol_print_date($this->db->jdate($date_recours), 'day');
        $details .= "; Motif: " . substr($motif_recours, 0, 100) . (strlen($motif_recours) > 100 ? '...' : '');
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_RECOURS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_RECOURS_JURIDIQUE';
                
                $message = 'Enregistrement d\'un recours pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message,
                    array(
                        'recours_appel' => array($ancien_recours_appel, $this->recours_appel),
                        'date_recours' => array($ancienne_date_recours, $this->date_recours)
                    )
                );
            }
        }
        
        return $result;
    }

    /**
     * Ajoute un acte de procédure
     * 
     * @param User   $user           Utilisateur effectuant l'action
     * @param string $type_acte      Type d'acte de procédure
     * @param string $date_acte      Date de l'acte (YYYY-MM-DD)
     * @param string $description    Description de l'acte
     * @param string $commentaire    Commentaire optionnel
     * @return int                   <0 si erreur, >0 si OK
     */
    public function ajouterActeProcedure($user, $type_acte, $date_acte, $description, $commentaire = '')
    {
        // Vérifications
        if (empty($type_acte)) {
            $this->error = 'TypeActeObligatoire';
            return -1;
        }
        
        if (empty($date_acte)) {
            $this->error = 'DateActeObligatoire';
            return -1;
        }
        
        if (empty($description)) {
            $this->error = 'DescriptionActeObligatoire';
            return -1;
        }
        
        // Récupérer les actes déjà enregistrés
        $actes = $this->getActesProcedure();
        
        // Ajouter le nouvel acte
        $actes[] = array(
            'id' => uniqid(),
            'type_acte' => $type_acte,
            'date_acte' => $date_acte,
            'description' => $description,
            'date_ajout' => date('Y-m-d')
        );
        
        // Mettre à jour le JSON
        $this->actes_procedure = json_encode($actes);
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Acte de procédure ajouté: " . $type_acte;
        $details .= " du " . dol_print_date($this->db->jdate($date_acte), 'day');
        $details .= "; Description: " . substr($description, 0, 50) . (strlen($description) > 50 ? '...' : '');
        
        $this->ajouterActionHistorique($user, 'AJOUT_ACTE_PROCEDURE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'AJOUT_ACTE_PROCEDURE_JURIDIQUE';
                
                $message = 'Ajout d\'un acte de procédure pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre les frais de procédure
     * 
     * @param User   $user           Utilisateur effectuant l'action
     * @param double $montant_frais  Montant des frais
     * @param string $description    Description des frais
     * @param string $commentaire    Commentaire optionnel
     * @return int                   <0 si erreur, >0 si OK
     */
    public function enregistrerFrais($user, $montant_frais, $description, $commentaire = '')
    {
        // Vérifications
        if ($montant_frais <= 0) {
            $this->error = 'MontantFraisDoitEtrePositif';
            return -1;
        }
        
        if (empty($description)) {
            $this->error = 'DescriptionFraisObligatoire';
            return -1;
        }
        
        $anciens_frais = $this->montant_frais;
        
        // Si déjà des frais, on additionne
        if ($this->montant_frais > 0) {
            $this->montant_frais += $montant_frais;
        } else {
            $this->montant_frais = $montant_frais;
        }
        
        // Mise à jour de la note
        $note = "Frais de procédure enregistrés : " . price($montant_frais);
        $note .= "\nDescription : " . $description;
        $note .= "\nMontant total des frais : " . price($this->montant_frais);
        
        if (!empty($commentaire)) {
            $note .= "\n" . $commentaire;
        }
        
        $this->addToNotes($user, $note);
        
        // Mise à jour de l'historique spécifique
        $details = "Frais de procédure : " . price($montant_frais);
        $details .= "; Total cumulé : " . price($this->montant_frais);
        $details .= "; Description : " . substr($description, 0, 50) . (strlen($description) > 50 ? '...' : '');
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_FRAIS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'ENREGISTREMENT_FRAIS_JURIDIQUE';
                
                $message = 'Enregistrement de frais pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message,
                    array('montant_frais' => array($anciens_frais, $this->montant_frais))
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
                $action = 'MAJ_PIECES_JURIDIQUE';
                
                $message = 'Mise à jour des pièces justificatives pour la démarche juridique "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_juridique',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }

    /**
     * Récupère les actes de procédure
     * 
     * @return array Tableau des actes de procédure
     */
    public function getActesProcedure()
    {
        if (empty($this->actes_procedure)) {
            return array();
        }
        
        $actes = json_decode($this->actes_procedure, true);
        
        if (!is_array($actes)) {
            return array();
        }
        
        return $actes;
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
            array('libelle' => 'Justificatif de domicile', 'obligatoire' => 1, 'fourni' => 0)
        );
        
        // Ajouter des pièces spécifiques selon le type de démarche juridique
        switch ($this->type_demarche_juridique_code) {
            case 'AIDE_JURIDICTIONNELLE':
                $pieces[] = array('libelle' => 'Formulaire CERFA demande d\'aide juridictionnelle', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Avis d\'imposition ou de non-imposition', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Relevés de prestations sociales', 'obligatoire' => 0, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Bulletins de salaire des 3 derniers mois', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'PROCEDURE_CIVILE':
                $pieces[] = array('libelle' => 'Assignation ou requête', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Pièces à l\'appui de la demande', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Conclusions', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'PROCEDURE_PENALE':
                $pieces[] = array('libelle' => 'Procès-verbal', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Plainte', 'obligatoire' => 0, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Convocation', 'obligatoire' => 0, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Certificat médical', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'PROCEDURE_ADMINISTRATIVE':
                $pieces[] = array('libelle' => 'Décision administrative contestée', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Recours gracieux', 'obligatoire' => 0, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Accusé de réception', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'PROCEDURE_PRUD_HOMALE':
                $pieces[] = array('libelle' => 'Contrat de travail', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Bulletins de salaire', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Lettre de licenciement', 'obligatoire' => 0, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation Pôle Emploi', 'obligatoire' => 0, 'fourni' => 0);
                break;
        }
        
        return json_encode($pieces);
    }

    /**
     * Ajoute une entrée à l'historique des actions spécifiques de la démarche juridique
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
     * Ajoute une action à l'historique spécifique de la démarche juridique
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
                        case 'ENREGISTREMENT_PROCEDURE':
                            $class = 'bg-primary';
                            break;
                        case 'ENREGISTREMENT_AUDIENCE':
                            $class = 'bg-warning';
                            break;
                        case 'ENREGISTREMENT_JUGEMENT':
                            $class = 'bg-success';
                            break;
                        case 'ENREGISTREMENT_RECOURS':
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
     * Obtient le libellé du type de démarche juridique
     * 
     * @return string Libellé du type de démarche
     */
    public function getTypeDemarcheJuridiqueLabel()
    {
        $types = self::getTypeDemarcheJuridiqueOptions($this->langs);
        return isset($types[$this->type_demarche_juridique_code]) ? $types[$this->type_demarche_juridique_code] : $this->type_demarche_juridique_code;
    }
    
    /**
     * Liste des statuts de démarche juridique valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsDemarcheJuridiqueValides()
    {
        return array(
            'PREPARATION',            // Préparation et constitution du dossier
            'CONSULTATION',           // Consultation juridique
            'SAISINE',                // Saisine de la juridiction
            'INSTRUCTION',            // Phase d'instruction
            'AUDIENCE_PROGRAMMEE',    // Audience programmée
            'DELIBERE',               // Délibéré
            'JUGEMENT_RENDU',         // Jugement rendu
            'EXECUTION',              // Phase d'exécution
            'CLOTURE',                // Procédure clôturée
            'RECOURS_APPEL'           // Recours ou appel
        );
    }
    
    /**
     * Liste des types de démarche juridique valides
     *
     * @return array Codes des types de démarche valides
     */
    public static function getTypesDemarcheJuridiqueValides()
    {
        return array(
            'AIDE_JURIDICTIONNELLE',     // Demande d'aide juridictionnelle
            'PROCEDURE_CIVILE',          // Procédure civile
            'PROCEDURE_PENALE',          // Procédure pénale
            'PROCEDURE_ADMINISTRATIVE',  // Procédure administrative
            'PROCEDURE_PRUD_HOMALE',     // Procédure prud'homale
            'PROCEDURE_COMMERCIALE',     // Procédure commerciale
            'MEDIATION',                 // Médiation/conciliation
            'SUCCESSION',                // Succession
            'DIVORCE',                   // Divorce
            'TUTELLE_CURATELLE',         // Tutelle ou curatelle
            'CONTENTIEUX_LOCATIF',       // Contentieux locatif
            'CONTENTIEUX_SANTE',         // Contentieux santé
            'CONTENTIEUX_CONSO',         // Contentieux consommation
            'CONTENTIEUX_ETRANGER',      // Contentieux droit des étrangers
            'PROCEDURE_EXECUTION',       // Procédures d'exécution
            'RECOURS_ADMINISTRATIF',     // Recours administratif
            'TRANSACTION',               // Transaction
            'AUTRE'                      // Autre type de démarche
        );
    }
    
    /**
     * Récupère les options du dictionnaire des types de démarche juridique
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeDemarcheJuridiqueOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'juridique_type_demarche', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts de démarche juridique
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutDemarcheJuridiqueOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'juridique_statut_demarche', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types d'actes de procédure
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeActeProcedureOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'juridique_type_acte', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des juridictions
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getJuridictionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'juridique_juridiction', $usekeys, $show_empty);
    }
    
    /**
     * Vérifie si cette démarche spécifique est une démarche juridique
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isJuridique()
    {
        return true;
    }
    
    /**
     * Récupère le contact avocat associé à la démarche
     * 
     * @return Contact|null Contact ou null si non trouvé
     */
    public function getContactAvocat()
    {
        if (empty($this->fk_contact_avocat)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
        $contact = new Contact($this->db);
        
        if ($contact->fetch($this->fk_contact_avocat) > 0) {
            return $contact;
        }
        
        return null;
    }
    
    /**
     * Récupère le contact huissier associé à la démarche
     * 
     * @return Contact|null Contact ou null si non trouvé
     */
    public function getContactHuissier()
    {
        if (empty($this->fk_contact_huissier)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
        $contact = new Contact($this->db);
        
        if ($contact->fetch($this->fk_contact_huissier) > 0) {
            return $contact;
        }
        
        return null;
    }
    
    /**
     * Définit des constantes pour les types d'actions standards dans l'historique
     */
    const ACTION_CHANGEMENT_STATUT_DEMARCHE = 'CHANGEMENT_STATUT_DEMARCHE';
    const ACTION_ENREGISTREMENT_PROCEDURE = 'ENREGISTREMENT_PROCEDURE';
    const ACTION_ENREGISTREMENT_AUDIENCE = 'ENREGISTREMENT_AUDIENCE';
    const ACTION_ENREGISTREMENT_AVOCAT = 'ENREGISTREMENT_AVOCAT';
    const ACTION_DEMANDE_AIDE_JURIDICTIONNELLE = 'DEMANDE_AIDE_JURIDICTIONNELLE';
    const ACTION_DECISION_AIDE_JURIDICTIONNELLE = 'DECISION_AIDE_JURIDICTIONNELLE';
    const ACTION_ENREGISTREMENT_JUGEMENT = 'ENREGISTREMENT_JUGEMENT';
    const ACTION_ENREGISTREMENT_EXECUTION = 'ENREGISTREMENT_EXECUTION';
    const ACTION_ENREGISTREMENT_MEDIATION = 'ENREGISTREMENT_MEDIATION';
    const ACTION_ENREGISTREMENT_RECOURS = 'ENREGISTREMENT_RECOURS';
    const ACTION_AJOUT_ACTE_PROCEDURE = 'AJOUT_ACTE_PROCEDURE';
    const ACTION_ENREGISTREMENT_FRAIS = 'ENREGISTREMENT_FRAIS';
    const ACTION_MISE_A_JOUR_PIECES = 'MISE_A_JOUR_PIECES';
}

} // Fin de la condition if !class_exists

// Dernière modification: 2025-06-03 17:44:04 UTC par Kylian65
