<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches citoyennes et administratives des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 * Dernière modification: 2025-06-03 17:48:14 UTC
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarcheCitoyenne', false)) {

class ElaskaParticulierDemarcheCitoyenne extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_citoyenne';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_citoyenne';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AUX DÉMARCHES CITOYENNES
    //
    
    /**
     * @var string Numéro de dossier administratif
     */
    public $numero_dossier;
    
    /**
     * @var string Numéro de l'ancien titre (pour renouvellements)
     */
    public $numero_ancien_titre;
    
    /**
     * @var string Code du type de démarche citoyenne (dictionnaire)
     */
    public $type_demarche_citoyenne_code;
    
    /**
     * @var string Code du statut de la démarche (dictionnaire)
     */
    public $statut_demarche_citoyenne_code;
    
    /**
     * @var string Administration concernée (préfecture, mairie, etc.)
     */
    public $administration;
    
    /**
     * @var string Commune de la démarche
     */
    public $commune_demarche;
    
    /**
     * @var string Code postal de la commune
     */
    public $code_postal_demarche;
    
    /**
     * @var string Modalité de la démarche (sur place, en ligne, par courrier)
     */
    public $modalite_demarche;
    
    /**
     * @var string Date du dépôt de la demande (format YYYY-MM-DD)
     */
    public $date_depot_demande;
    
    /**
     * @var string Date du rendez-vous (format YYYY-MM-DD)
     */
    public $date_rendez_vous;
    
    /**
     * @var string Heure du rendez-vous (format HH:MM:SS)
     */
    public $heure_rendez_vous;
    
    /**
     * @var string Lieu du rendez-vous
     */
    public $lieu_rendez_vous;
    
    /**
     * @var string Date de délivrance du titre/document (format YYYY-MM-DD)
     */
    public $date_delivrance;
    
    /**
     * @var string Date d'expiration du titre/document (format YYYY-MM-DD)
     */
    public $date_expiration;
    
    /**
     * @var int Durée de validité en années
     */
    public $duree_validite;
    
    /**
     * @var string Date de retrait du titre/document (format YYYY-MM-DD)
     */
    public $date_retrait;
    
    /**
     * @var string Motif de la demande (première demande, renouvellement, perte, etc.)
     */
    public $motif_demande;
    
    /**
     * @var string Numéro du titre/document
     */
    public $numero_titre;
    
    /**
     * @var string Autorité de délivrance
     */
    public $autorite_delivrance;
    
    /**
     * @var string Timbre fiscal (référence)
     */
    public $timbre_fiscal;
    
    /**
     * @var double Montant du timbre fiscal ou des frais
     */
    public $montant_frais;
    
    /**
     * @var string Pays concerné (pour visa, naturalisation...)
     */
    public $pays_concerne;
    
    /**
     * @var int Pré-demande effectuée (0=non, 1=oui)
     */
    public $pre_demande_effectuee;
    
    /**
     * @var string Numéro de pré-demande
     */
    public $numero_pre_demande;
    
    /**
     * @var string Type de titre d'identité (CNI, passeport, etc.)
     */
    public $type_titre;
    
    /**
     * @var string Type de titre de séjour
     */
    public $type_titre_sejour;
    
    /**
     * @var string Raison d'un refus éventuel
     */
    public $raison_refus;
    
    /**
     * @var int Procuration (0=non, 1=oui)
     */
    public $procuration;
    
    /**
     * @var string Nom du mandataire pour procuration
     */
    public $nom_mandataire;
    
    /**
     * @var string Bureau de vote assigné
     */
    public $bureau_vote;
    
    /**
     * @var string Numéro électeur
     */
    public $numero_electeur;
    
    /**
     * @var string Historique des actions spécifiques à la démarche citoyenne
     */
    public $historique_actions;
    
    /**
     * @var string Pièces justificatives à fournir (format JSON)
     */
    public $pieces_justificatives;
    
    /**
     * @var string URL du site officiel pour la démarche
     */
    public $url_demarche;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_citoyenne = array(
        'numero_dossier' => array('type' => 'varchar(50)', 'label' => 'NumeroDossier', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'numero_ancien_titre' => array('type' => 'varchar(50)', 'label' => 'NumeroAncienTitre', 'enabled' => 1, 'position' => 1110, 'notnull' => 0, 'visible' => 1),
        'type_demarche_citoyenne_code' => array('type' => 'varchar(50)', 'label' => 'TypeDemarcheCitoyenne', 'enabled' => 1, 'position' => 1120, 'notnull' => 1, 'visible' => 1),
        'statut_demarche_citoyenne_code' => array('type' => 'varchar(50)', 'label' => 'StatutDemarcheCitoyenne', 'enabled' => 1, 'position' => 1130, 'notnull' => 1, 'visible' => 1),
        'administration' => array('type' => 'varchar(255)', 'label' => 'Administration', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'commune_demarche' => array('type' => 'varchar(255)', 'label' => 'Commune', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'code_postal_demarche' => array('type' => 'varchar(10)', 'label' => 'CodePostal', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'modalite_demarche' => array('type' => 'varchar(50)', 'label' => 'ModaliteDemarche', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'date_depot_demande' => array('type' => 'date', 'label' => 'DateDepotDemande', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'date_rendez_vous' => array('type' => 'date', 'label' => 'DateRendezVous', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'heure_rendez_vous' => array('type' => 'varchar(8)', 'label' => 'HeureRendezVous', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'lieu_rendez_vous' => array('type' => 'varchar(255)', 'label' => 'LieuRendezVous', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'date_delivrance' => array('type' => 'date', 'label' => 'DateDelivrance', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'date_expiration' => array('type' => 'date', 'label' => 'DateExpiration', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'duree_validite' => array('type' => 'integer', 'label' => 'DureeValidite', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'date_retrait' => array('type' => 'date', 'label' => 'DateRetrait', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'motif_demande' => array('type' => 'varchar(255)', 'label' => 'MotifDemande', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1),
        'numero_titre' => array('type' => 'varchar(50)', 'label' => 'NumeroTitre', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'autorite_delivrance' => array('type' => 'varchar(255)', 'label' => 'AutoriteDelivrance', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'timbre_fiscal' => array('type' => 'varchar(50)', 'label' => 'TimbreFiscal', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1),
        'montant_frais' => array('type' => 'double(24,8)', 'label' => 'MontantFrais', 'enabled' => 1, 'position' => 1300, 'notnull' => 0, 'visible' => 1),
        'pays_concerne' => array('type' => 'varchar(255)', 'label' => 'PaysConcerne', 'enabled' => 1, 'position' => 1310, 'notnull' => 0, 'visible' => 1),
        'pre_demande_effectuee' => array('type' => 'boolean', 'label' => 'PreDemandeEffectuee', 'enabled' => 1, 'position' => 1320, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'numero_pre_demande' => array('type' => 'varchar(50)', 'label' => 'NumeroPreDemande', 'enabled' => 1, 'position' => 1330, 'notnull' => 0, 'visible' => 1),
        'type_titre' => array('type' => 'varchar(50)', 'label' => 'TypeTitre', 'enabled' => 1, 'position' => 1340, 'notnull' => 0, 'visible' => 1),
        'type_titre_sejour' => array('type' => 'varchar(50)', 'label' => 'TypeTitreSejour', 'enabled' => 1, 'position' => 1350, 'notnull' => 0, 'visible' => 1),
        'raison_refus' => array('type' => 'text', 'label' => 'RaisonRefus', 'enabled' => 1, 'position' => 1360, 'notnull' => 0, 'visible' => 1),
        'procuration' => array('type' => 'boolean', 'label' => 'Procuration', 'enabled' => 1, 'position' => 1370, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'nom_mandataire' => array('type' => 'varchar(255)', 'label' => 'NomMandataire', 'enabled' => 1, 'position' => 1380, 'notnull' => 0, 'visible' => 1),
        'bureau_vote' => array('type' => 'varchar(50)', 'label' => 'BureauVote', 'enabled' => 1, 'position' => 1390, 'notnull' => 0, 'visible' => 1),
        'numero_electeur' => array('type' => 'varchar(50)', 'label' => 'NumeroElecteur', 'enabled' => 1, 'position' => 1400, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 1410, 'notnull' => 0, 'visible' => 1),
        'pieces_justificatives' => array('type' => 'text', 'label' => 'PiecesJustificatives', 'enabled' => 1, 'position' => 1420, 'notnull' => 0, 'visible' => 1),
        'url_demarche' => array('type' => 'varchar(255)', 'label' => 'URLDemarche', 'enabled' => 1, 'position' => 1430, 'notnull' => 0, 'visible' => 1)
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Fusion des champs spécifiques citoyenne avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_citoyenne);
        
        // Valeurs par défaut spécifiques aux démarches citoyennes
        $this->type_demarche_code = 'CITOYENNE';  // Force le code de type de démarche
        if (!isset($this->statut_demarche_citoyenne_code)) $this->statut_demarche_citoyenne_code = 'A_INITIER'; // Statut par défaut
        if (!isset($this->pre_demande_effectuee)) $this->pre_demande_effectuee = 0;
        if (!isset($this->procuration)) $this->procuration = 0;
    }

    /**
     * Crée une démarche citoyenne dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à CITOYENNE
        $this->type_demarche_code = 'CITOYENNE';
        
        // Vérifications spécifiques aux démarches citoyennes
        if (empty($this->type_demarche_citoyenne_code)) {
            $this->error = 'TypeDemarcheCitoyenneIsMandatory';
            return -1;
        }
        
        // Génération automatique du libellé s'il n'est pas fourni
        if (empty($this->libelle)) {
            $this->libelle = $this->getTypeDemarcheCitoyenneLabel();
            if (!empty($this->motif_demande)) {
                $this->libelle .= ' - ' . $this->motif_demande;
            }
            if (!empty($this->commune_demarche)) {
                $this->libelle .= ' à ' . $this->commune_demarche;
            }
        }
        
        // Initialisation des pièces justificatives
        if (empty($this->pieces_justificatives)) {
            $this->pieces_justificatives = $this->getPiecesJustificativesParDefaut();
        }
        
        // Si date de dépôt non renseignée, mettre par défaut aujourd'hui
        if (empty($this->date_depot_demande) && $this->statut_demarche_citoyenne_code != 'A_INITIER') {
            $this->date_depot_demande = date('Y-m-d');
        }
        
        // Si rendez-vous planifié, mettre à jour la date d'échéance
        if (!empty($this->date_rendez_vous) && (empty($this->date_echeance) || $this->date_rendez_vous < $this->date_echeance)) {
            $this->date_echeance = $this->date_rendez_vous;
        }
        
        // Définition de la date d'expiration automatique basée sur la durée de validité si fournie
        if (!empty($this->date_delivrance) && !empty($this->duree_validite) && empty($this->date_expiration)) {
            $this->date_expiration = date('Y-m-d', strtotime('+' . $this->duree_validite . ' years', strtotime($this->date_delivrance)));
        }
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'une démarche citoyenne
            $note = "Création d'une démarche citoyenne de type " . $this->getTypeDemarcheCitoyenneLabel();
            if (!empty($this->commune_demarche)) {
                $note .= " à " . $this->commune_demarche;
            }
            if (!empty($this->motif_demande)) {
                $note .= " (" . $this->motif_demande . ")";
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
     * Met à jour une démarche citoyenne dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à CITOYENNE
        $this->type_demarche_code = 'CITOYENNE';
        
        // Vérifications spécifiques aux démarches citoyennes
        if (empty($this->type_demarche_citoyenne_code)) {
            $this->error = 'TypeDemarcheCitoyenneIsMandatory';
            return -1;
        }
        
        // Vérification de l'heure au format HH:MM ou HH:MM:SS
        if (!empty($this->heure_rendez_vous) && !preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $this->heure_rendez_vous)) {
            $this->error = 'FormatHeureInvalide';
            return -1;
        }
        
        // Si procuration, vérifier qu'il y a un mandataire
        if ($this->procuration && empty($this->nom_mandataire)) {
            $this->error = 'NomMandataireRequiredWhenProcuration';
            return -1;
        }
        
        // Vérification de la cohérence des dates
        if (!empty($this->date_depot_demande) && !empty($this->date_delivrance) && $this->date_delivrance < $this->date_depot_demande) {
            $this->error = 'DateDelivranceCantBeBeforeDateDepot';
            return -1;
        }
        
        if (!empty($this->date_delivrance) && !empty($this->date_expiration) && $this->date_expiration < $this->date_delivrance) {
            $this->error = 'DateExpirationCantBeBeforeDateDelivrance';
            return -1;
        }
        
        if (!empty($this->date_delivrance) && !empty($this->date_retrait) && $this->date_retrait < $this->date_delivrance) {
            $this->error = 'DateRetraitCantBeBeforeDateDelivrance';
            return -1;
        }
        
        // Mise à jour de la date d'expiration automatique basée sur la durée de validité si modifiée
        if (!empty($this->date_delivrance) && !empty($this->duree_validite)) {
            $this->date_expiration = date('Y-m-d', strtotime('+' . $this->duree_validite . ' years', strtotime($this->date_delivrance)));
        }
        
        // Si rendez-vous planifié, mettre à jour la date d'échéance
        if (!empty($this->date_rendez_vous) && (empty($this->date_echeance) || $this->date_rendez_vous < $this->date_echeance)) {
            $this->date_echeance = $this->date_rendez_vous;
        }
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le statut de la démarche citoyenne
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau code de statut
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function changerStatutDemarcheCitoyenne($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsDemarcheCitoyenneValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatutDemarcheCitoyenneCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_demarche_citoyenne_code;
        $this->statut_demarche_citoyenne_code = $statut_code;
        
        // Mises à jour automatiques selon le statut
        switch ($statut_code) {
            case 'PRE_DEMANDE_EFFECTUEE':
                $this->pre_demande_effectuee = 1;
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 20; // 20% de progression
                break;
                
            case 'DOSSIER_DEPOSE':
                if (empty($this->date_depot_demande)) {
                    $this->date_depot_demande = date('Y-m-d');
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 40; // 40% de progression
                break;
                
            case 'RDV_PLANIFIE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 50; // 50% de progression
                break;
                
            case 'RDV_EFFECTUE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 60; // 60% de progression
                break;
                
            case 'EN_TRAITEMENT':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 70; // 70% de progression
                break;
                
            case 'DOCUMENT_DISPONIBLE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 90; // 90% de progression
                break;
                
            case 'DOCUMENT_RETIRE':
                if (empty($this->date_retrait)) {
                    $this->date_retrait = date('Y-m-d');
                }
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'DEMANDE_REFUSEE':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'DEMANDE_ANNULEE':
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
                $action = 'CHANGEMENT_STATUT_CITOYENNE';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutDemarcheCitoyenneOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
                    $this->id,
                    $message,
                    array('statut_demarche_citoyenne_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une pré-demande en ligne
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param string $date_pre_demande      Date de la pré-demande (YYYY-MM-DD)
     * @param string $numero_pre_demande    Numéro de pré-demande
     * @param string $url_demarche          URL de la démarche en ligne (optionnel)
     * @param string $commentaire          Commentaire optionnel
     * @return int                          <0 si erreur, >0 si OK
     */
    public function enregistrerPreDemande($user, $date_pre_demande, $numero_pre_demande, $url_demarche = '', $commentaire = '')
    {
        // Vérifications
        if (empty($date_pre_demande)) {
            $this->error = 'DatePreDemandeObligatoire';
            return -1;
        }
        
        if (empty($numero_pre_demande)) {
            $this->error = 'NumeroPreDemandeObligatoire';
            return -1;
        }
        
        $ancienne_pre_demande = $this->pre_demande_effectuee;
        $ancien_numero_pre_demande = $this->numero_pre_demande;
        
        $this->pre_demande_effectuee = 1;
        $this->numero_pre_demande = $numero_pre_demande;
        $this->date_depot_demande = $date_pre_demande;
        
        if (!empty($url_demarche)) {
            $this->url_demarche = $url_demarche;
        }
        
        // Mise à jour du statut si ce n'est pas déjà fait
        if ($this->statut_demarche_citoyenne_code == 'A_INITIER') {
            $this->statut_demarche_citoyenne_code = 'PRE_DEMANDE_EFFECTUEE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 20;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Pré-demande enregistrée le " . dol_print_date($this->db->jdate($date_pre_demande), 'day');
        $details .= "; Numéro pré-demande: " . $this->numero_pre_demande;
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_PRE_DEMANDE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'PRE_DEMANDE_CITOYENNE';
                
                $message = 'Enregistrement d\'une pré-demande en ligne pour la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
                    $this->id,
                    $message,
                    array(
                        'pre_demande_effectuee' => array($ancienne_pre_demande, $this->pre_demande_effectuee),
                        'numero_pre_demande' => array($ancien_numero_pre_demande, $this->numero_pre_demande)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre le dépôt de dossier
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param string $date_depot_demande    Date de dépôt (YYYY-MM-DD)
     * @param string $numero_dossier        Numéro du dossier (optionnel)
     * @param string $administration        Administration concernée (optionnel)
     * @param string $commentaire           Commentaire optionnel
     * @return int                          <0 si erreur, >0 si OK
     */
    public function enregistrerDepotDossier($user, $date_depot_demande, $numero_dossier = '', $administration = '', $commentaire = '')
    {
        // Vérifications
        if (empty($date_depot_demande)) {
            $this->error = 'DateDepotDemandeCitoyenneObligatoire';
            return -1;
        }
        
        $ancienne_date_depot = $this->date_depot_demande;
        $ancien_numero_dossier = $this->numero_dossier;
        $ancienne_administration = $this->administration;
        
        $this->date_depot_demande = $date_depot_demande;
        
        if (!empty($numero_dossier)) {
            $this->numero_dossier = $numero_dossier;
        }
        
        if (!empty($administration)) {
            $this->administration = $administration;
        }
        
        // Mise à jour du statut
        if ($this->statut_demarche_citoyenne_code == 'A_INITIER' || $this->statut_demarche_citoyenne_code == 'PRE_DEMANDE_EFFECTUEE') {
            $this->statut_demarche_citoyenne_code = 'DOSSIER_DEPOSE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 40;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Dossier déposé le " . dol_print_date($this->db->jdate($date_depot_demande), 'day');
        
        if (!empty($numero_dossier)) {
            $details .= "; Numéro dossier: " . $this->numero_dossier;
        }
        
        if (!empty($administration)) {
            $details .= "; Administration: " . $this->administration;
        }
        
        $this->ajouterActionHistorique($user, 'DEPOT_DOSSIER', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DEPOT_DOSSIER_CITOYENNE';
                
                $message = 'Dépôt de dossier pour la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
                    $this->id,
                    $message,
                    array('date_depot_demande' => array($ancienne_date_depot, $this->date_depot_demande))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Planifie un rendez-vous
     *
     * @param User   $user                Utilisateur effectuant l'action
     * @param string $date_rendez_vous     Date du rendez-vous (YYYY-MM-DD)
     * @param string $heure_rendez_vous    Heure du rendez-vous (HH:MM:SS)
     * @param string $lieu_rendez_vous     Lieu du rendez-vous
     * @param string $commentaire         Commentaire optionnel
     * @return int                         <0 si erreur, >0 si OK
     */
    public function planifierRendezVous($user, $date_rendez_vous, $heure_rendez_vous, $lieu_rendez_vous, $commentaire = '')
    {
        // Vérifications
        if (empty($date_rendez_vous)) {
            $this->error = 'DateRendezVousObligatoire';
            return -1;
        }
        
        if (empty($lieu_rendez_vous)) {
            $this->error = 'LieuRendezVousObligatoire';
            return -1;
        }
        
        // Vérification de l'heure au format HH:MM ou HH:MM:SS
        if (!empty($heure_rendez_vous) && !preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $heure_rendez_vous)) {
            $this->error = 'FormatHeureInvalide';
            return -1;
        }
        
        $ancienne_date_rdv = $this->date_rendez_vous;
        $ancienne_heure_rdv = $this->heure_rendez_vous;
        $ancien_lieu_rdv = $this->lieu_rendez_vous;
        
        $this->date_rendez_vous = $date_rendez_vous;
        $this->heure_rendez_vous = $heure_rendez_vous;
        $this->lieu_rendez_vous = $lieu_rendez_vous;
        
        // Mise à jour du statut
        if ($this->statut_demarche_citoyenne_code == 'A_INITIER' || 
            $this->statut_demarche_citoyenne_code == 'PRE_DEMANDE_EFFECTUEE' || 
            $this->statut_demarche_citoyenne_code == 'DOSSIER_DEPOSE') {
            $this->statut_demarche_citoyenne_code = 'RDV_PLANIFIE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 50;
        }
        
        // Mise à jour de la date d'échéance
        if (empty($this->date_echeance) || $this->date_rendez_vous < $this->date_echeance) {
            $this->date_echeance = $this->date_rendez_vous;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Rendez-vous planifié le " . dol_print_date($this->db->jdate($date_rendez_vous), 'day');
        $details .= " à " . $this->heure_rendez_vous;
        $details .= " - Lieu: " . $this->lieu_rendez_vous;
        
        $this->ajouterActionHistorique($user, 'PLANIFICATION_RENDEZ_VOUS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'PLANIFICATION_RDV_CITOYENNE';
                
                $message = 'Planification de rendez-vous pour la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
                    $this->id,
                    $message,
                    array(
                        'date_rendez_vous' => array($ancienne_date_rdv, $this->date_rendez_vous),
                        'lieu_rendez_vous' => array($ancien_lieu_rdv, $this->lieu_rendez_vous)
                    )
                );
            }
        }
        
        return $result;
    }

    /**
     * Enregistre un rendez-vous effectué
     * 
     * @param User   $user          Utilisateur effectuant l'action
     * @param string $date_rdv      Date du rendez-vous effectué (YYYY-MM-DD)
     * @param string $commentaire   Commentaire optionnel
     * @return int                  <0 si erreur, >0 si OK
     */
    public function confirmerRendezVousEffectue($user, $date_rdv, $commentaire = '')
    {
        // Vérifications
        if (empty($date_rdv)) {
            $this->error = 'DateRendezVousObligatoire';
            return -1;
        }
        
        // Mise à jour du statut
        if ($this->statut_demarche_citoyenne_code != 'RDV_EFFECTUE' && 
            $this->statut_demarche_citoyenne_code != 'EN_TRAITEMENT' &&
            $this->statut_demarche_citoyenne_code != 'DOCUMENT_DISPONIBLE' &&
            $this->statut_demarche_citoyenne_code != 'DOCUMENT_RETIRE') {
            $this->statut_demarche_citoyenne_code = 'RDV_EFFECTUE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 60;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Rendez-vous effectué le " . dol_print_date($this->db->jdate($date_rdv), 'day');
        
        $this->ajouterActionHistorique($user, 'RENDEZ_VOUS_EFFECTUE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'RDV_EFFECTUE_CITOYENNE';
                
                $message = 'Rendez-vous effectué pour la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre la délivrance d'un document
     *
     * @param User   $user                Utilisateur effectuant l'action
     * @param string $date_delivrance      Date de délivrance (YYYY-MM-DD)
     * @param string $numero_titre         Numéro du titre/document
     * @param int    $duree_validite       Durée de validité en années
     * @param string $autorite_delivrance  Autorité de délivrance (optionnel)
     * @param string $commentaire         Commentaire optionnel
     * @return int                         <0 si erreur, >0 si OK
     */
    public function enregistrerDelivranceDocument($user, $date_delivrance, $numero_titre, $duree_validite = 0, $autorite_delivrance = '', $commentaire = '')
    {
        // Vérifications
        if (empty($date_delivrance)) {
            $this->error = 'DateDelivranceObligatoire';
            return -1;
        }
        
        if (empty($numero_titre)) {
            $this->error = 'NumeroTitreObligatoire';
            return -1;
        }
        
        $ancienne_date_delivrance = $this->date_delivrance;
        $ancien_numero_titre = $this->numero_titre;
        $ancienne_duree_validite = $this->duree_validite;
        $ancienne_autorite = $this->autorite_delivrance;
        
        $this->date_delivrance = $date_delivrance;
        $this->numero_titre = $numero_titre;
        
        if ($duree_validite > 0) {
            $this->duree_validite = $duree_validite;
            // Calcul de la date d'expiration
            $this->date_expiration = date('Y-m-d', strtotime('+' . $duree_validite . ' years', strtotime($date_delivrance)));
        }
        
        if (!empty($autorite_delivrance)) {
            $this->autorite_delivrance = $autorite_delivrance;
        }
        
        // Mise à jour du statut
        if ($this->statut_demarche_citoyenne_code != 'DOCUMENT_DISPONIBLE' && 
            $this->statut_demarche_citoyenne_code != 'DOCUMENT_RETIRE') {
            $this->statut_demarche_citoyenne_code = 'DOCUMENT_DISPONIBLE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 90;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Document délivré le " . dol_print_date($this->db->jdate($date_delivrance), 'day');
        $details .= "; Numéro: " . $this->numero_titre;
        
        if ($duree_validite > 0) {
            $details .= "; Durée de validité: " . $this->duree_validite . " an(s)";
            $details .= "; Expiration: " . dol_print_date($this->db->jdate($this->date_expiration), 'day');
        }
        
        if (!empty($autorite_delivrance)) {
            $details .= "; Autorité: " . $this->autorite_delivrance;
        }
        
        $this->ajouterActionHistorique($user, 'DELIVRANCE_DOCUMENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DELIVRANCE_DOCUMENT_CITOYENNE';
                
                $message = 'Délivrance de document pour la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
                    $this->id,
                    $message,
                    array(
                        'date_delivrance' => array($ancienne_date_delivrance, $this->date_delivrance),
                        'numero_titre' => array($ancien_numero_titre, $this->numero_titre)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre le retrait du document
     *
     * @param User   $user           Utilisateur effectuant l'action
     * @param string $date_retrait    Date de retrait (YYYY-MM-DD)
     * @param string $commentaire    Commentaire optionnel
     * @return int                    <0 si erreur, >0 si OK
     */
    public function enregistrerRetraitDocument($user, $date_retrait, $commentaire = '')
    {
        // Vérifications
        if (empty($date_retrait)) {
            $this->error = 'DateRetraitObligatoire';
            return -1;
        }
        
        // Vérifie que le document est disponible
        if (empty($this->date_delivrance) || empty($this->numero_titre)) {
            $this->error = 'DocumentDoitEtreDelivre';
            return -1;
        }
        
        $ancienne_date_retrait = $this->date_retrait;
        
        $this->date_retrait = $date_retrait;
        
        // Mise à jour du statut
        if ($this->statut_demarche_citoyenne_code != 'DOCUMENT_RETIRE') {
            $this->statut_demarche_citoyenne_code = 'DOCUMENT_RETIRE';
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
        $details = "Document retiré le " . dol_print_date($this->db->jdate($date_retrait), 'day');
        
        $this->ajouterActionHistorique($user, 'RETRAIT_DOCUMENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'RETRAIT_DOCUMENT_CITOYENNE';
                
                $message = 'Retrait du document pour la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
                    $this->id,
                    $message,
                    array('date_retrait' => array($ancienne_date_retrait, $this->date_retrait))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un refus de demande
     *
     * @param User   $user           Utilisateur effectuant l'action
     * @param string $raison_refus    Raison du refus
     * @param string $commentaire    Commentaire optionnel
     * @return int                    <0 si erreur, >0 si OK
     */
    public function enregistrerRefusDemande($user, $raison_refus, $commentaire = '')
    {
        // Vérifications
        if (empty($raison_refus)) {
            $this->error = 'RaisonRefusObligatoire';
            return -1;
        }
        
        $ancienne_raison = $this->raison_refus;
        
        $this->raison_refus = $raison_refus;
        
        // Mise à jour du statut
        if ($this->statut_demarche_citoyenne_code != 'DEMANDE_REFUSEE') {
            $this->statut_demarche_citoyenne_code = 'DEMANDE_REFUSEE';
            $this->statut_demarche_code = 'TERMINEE';
            $this->progression = 100;
            $this->date_cloture = dol_now();
            $this->fk_user_cloture = $user->id;
        }
        
        // Mise à jour de la note
        $note = "Demande refusée. Motif : " . $raison_refus;
        
        if (!empty($commentaire)) {
            $note .= "\n" . $commentaire;
        }
        
        $this->addToNotes($user, $note);
        
        // Mise à jour de l'historique spécifique
        $details = "Demande refusée; Motif: " . $this->raison_refus;
        
        $this->ajouterActionHistorique($user, 'REFUS_DEMANDE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'REFUS_DEMANDE_CITOYENNE';
                
                $message = 'Refus de demande pour la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
                    $this->id,
                    $message,
                    array('raison_refus' => array($ancienne_raison, $this->raison_refus))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une procuration
     *
     * @param User   $user              Utilisateur effectuant l'action
     * @param string $nom_mandataire     Nom du mandataire
     * @param string $bureau_vote        Bureau de vote (optionnel)
     * @param string $commentaire       Commentaire optionnel
     * @return int                       <0 si erreur, >0 si OK
     */
    public function enregistrerProcuration($user, $nom_mandataire, $bureau_vote = '', $commentaire = '')
    {
        // Vérifications
        if (empty($nom_mandataire)) {
            $this->error = 'NomMandataireObligatoire';
            return -1;
        }
        
        // Vérifier que le type de démarche est bien électoral/vote
        if ($this->type_demarche_citoyenne_code != 'INSCRIPTION_LISTE_ELECTORALE' && $this->type_demarche_citoyenne_code != 'PROCURATION_VOTE') {
            $this->error = 'ProcurationPossibleSeulementPourDemarchesElectorales';
            return -1;
        }
        
        $ancienne_procuration = $this->procuration;
        $ancien_mandataire = $this->nom_mandataire;
        $ancien_bureau = $this->bureau_vote;
        
        $this->procuration = 1;
        $this->nom_mandataire = $nom_mandataire;
        
        if (!empty($bureau_vote)) {
            $this->bureau_vote = $bureau_vote;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Procuration enregistrée pour " . $this->nom_mandataire;
        
        if (!empty($bureau_vote)) {
            $details .= "; Bureau de vote: " . $this->bureau_vote;
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_PROCURATION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'PROCURATION_CITOYENNE';
                
                $message = 'Enregistrement d\'une procuration pour la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
                    $this->id,
                    $message,
                    array(
                        'procuration' => array($ancienne_procuration, $this->procuration),
                        'nom_mandataire' => array($ancien_mandataire, $this->nom_mandataire)
                    )
                );
            }
        }
        
        return $result;
    }

    /**
     * Enregistre l'achat de timbres fiscaux
     * 
     * @param User   $user           Utilisateur effectuant l'action
     * @param string $timbre_fiscal   Référence du timbre fiscal
     * @param double $montant_frais   Montant des frais/timbre
     * @param string $commentaire    Commentaire optionnel
     * @return int                    <0 si erreur, >0 si OK
     */
    public function enregistrerTimbreFiscal($user, $timbre_fiscal, $montant_frais, $commentaire = '')
    {
        // Vérifications
        if (empty($timbre_fiscal)) {
            $this->error = 'ReferenceTimbeFiscalObligatoire';
            return -1;
        }
        
                if ($montant_frais <= 0) {
            $this->error = 'MontantFraisDoitEtrePositif';
            return -1;
        }
        
        $ancien_timbre = $this->timbre_fiscal;
        $ancien_montant = $this->montant_frais;
        
        $this->timbre_fiscal = $timbre_fiscal;
        $this->montant_frais = $montant_frais;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Timbre fiscal enregistré: " . $this->timbre_fiscal;
        $details .= "; Montant: " . price($this->montant_frais);
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_TIMBRE_FISCAL', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'TIMBRE_FISCAL_CITOYENNE';
                
                $message = 'Enregistrement d\'un timbre fiscal pour la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
                    $this->id,
                    $message,
                    array(
                        'timbre_fiscal' => array($ancien_timbre, $this->timbre_fiscal),
                        'montant_frais' => array($ancien_montant, $this->montant_frais)
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
                $action = 'MAJ_PIECES_CITOYENNE';
                
                $message = 'Mise à jour des pièces justificatives pour la démarche citoyenne "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_citoyenne',
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
            array('libelle' => 'Justificatif de domicile de moins de 3 mois', 'obligatoire' => 1, 'fourni' => 0)
        );
        
        // Ajouter des pièces spécifiques selon le type de démarche citoyenne
        switch ($this->type_demarche_citoyenne_code) {
            case 'CNI':
                $pieces[] = array('libelle' => 'Photo d\'identité récente', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Acte de naissance de moins de 3 mois', 'obligatoire' => 0, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Timbre fiscal', 'obligatoire' => $this->exige_timbre_fiscal(), 'fourni' => 0);
                $pieces[] = array('libelle' => 'Ancienne carte d\'identité', 'obligatoire' => ($this->motif_demande == 'RENOUVELLEMENT'), 'fourni' => 0);
                break;
                
            case 'PASSEPORT':
                $pieces[] = array('libelle' => 'Photo d\'identité récente', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Timbre fiscal', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Acte de naissance de moins de 3 mois', 'obligatoire' => 0, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Ancien passeport', 'obligatoire' => ($this->motif_demande == 'RENOUVELLEMENT'), 'fourni' => 0);
                break;
                
            case 'TITRE_SEJOUR':
                $pieces[] = array('libelle' => 'Photos d\'identité récentes', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Passeport valide', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Acte de naissance traduit', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Justificatif d\'entrée régulière en France', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation d\'hébergement', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Justificatifs de ressources', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Ancien titre de séjour', 'obligatoire' => ($this->motif_demande == 'RENOUVELLEMENT'), 'fourni' => 0);
                break;
                
            case 'PERMIS_CONDUIRE':
                $pieces[] = array('libelle' => 'Photo d\'identité récente', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Justificatif d\'aptitude', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation de formation', 'obligatoire' => ($this->motif_demande != 'DUPLICATA'), 'fourni' => 0);
                $pieces[] = array('libelle' => 'Déclaration de perte ou de vol', 'obligatoire' => ($this->motif_demande == 'PERTE' || $this->motif_demande == 'VOL'), 'fourni' => 0);
                break;
                
            case 'INSCRIPTION_LISTE_ELECTORALE':
                $pieces[] = array('libelle' => 'Formulaire d\'inscription', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Attestation d\'inscription sur les listes consulaires', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'PROCURATION_VOTE':
                $pieces[] = array('libelle' => 'Formulaire de procuration', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Pièce d\'identité du mandataire', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'ACTE_NAISSANCE':
            case 'ACTE_MARIAGE':
            case 'ACTE_DECES':
                $pieces[] = array('libelle' => 'Formulaire de demande d\'acte', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Livret de famille', 'obligatoire' => 0, 'fourni' => 0);
                break;
        }
        
        return json_encode($pieces);
    }

    /**
     * Détermine si cette démarche nécessite un timbre fiscal
     *
     * @return bool True si un timbre fiscal est requis
     */
    protected function exige_timbre_fiscal()
    {
        // CNI : timbre fiscal uniquement en cas de perte ou vol
        if ($this->type_demarche_citoyenne_code == 'CNI') {
            return ($this->motif_demande == 'PERTE' || $this->motif_demande == 'VOL');
        }
        
        // PASSEPORT : toujours exige un timbre fiscal
        if ($this->type_demarche_citoyenne_code == 'PASSEPORT') {
            return true;
        }
        
        // TITRE_SEJOUR : toujours exige un timbre fiscal
        if ($this->type_demarche_citoyenne_code == 'TITRE_SEJOUR') {
            return true;
        }
        
        return false;
    }

    /**
     * Ajoute une entrée à l'historique des actions spécifiques de la démarche citoyenne
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
     * Ajoute une action à l'historique spécifique de la démarche citoyenne
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
                        case 'ENREGISTREMENT_PRE_DEMANDE':
                            $class = 'bg-primary';
                            break;
                        case 'DEPOT_DOSSIER':
                            $class = 'bg-success';
                            break;
                        case 'PLANIFICATION_RENDEZ_VOUS':
                            $class = 'bg-warning';
                            break;
                        case 'RENDEZ_VOUS_EFFECTUE':
                            $class = 'bg-warning';
                            break;
                        case 'DELIVRANCE_DOCUMENT':
                            $class = 'bg-success';
                            break;
                        case 'RETRAIT_DOCUMENT':
                            $class = 'bg-success';
                            break;
                        case 'REFUS_DEMANDE':
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
     * Obtient le libellé du type de démarche citoyenne
     * 
     * @return string Libellé du type de démarche
     */
    public function getTypeDemarcheCitoyenneLabel()
    {
        $types = self::getTypeDemarcheCitoyenneOptions($this->langs);
        return isset($types[$this->type_demarche_citoyenne_code]) ? $types[$this->type_demarche_citoyenne_code] : $this->type_demarche_citoyenne_code;
    }
    
    /**
     * Liste des statuts de démarche citoyenne valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsDemarcheCitoyenneValides()
    {
        return array(
            'A_INITIER',             // Démarche à initier
            'PRE_DEMANDE_EFFECTUEE', // Pré-demande en ligne effectuée
            'DOSSIER_DEPOSE',        // Dossier déposé
            'RDV_PLANIFIE',          // Rendez-vous planifié
            'RDV_EFFECTUE',          // Rendez-vous effectué
            'EN_TRAITEMENT',         // Dossier en traitement
            'DOCUMENT_DISPONIBLE',   // Document disponible
            'DOCUMENT_RETIRE',       // Document retiré par l'usager
            'DEMANDE_REFUSEE',       // Demande refusée
            'DEMANDE_ANNULEE'        // Demande annulée
        );
    }
    
    /**
     * Liste des types de démarche citoyenne valides
     *
     * @return array Codes des types de démarche valides
     */
    public static function getTypesDemarcheCitoyenneValides()
    {
        return array(
            'CNI',                        // Carte Nationale d'Identité
            'PASSEPORT',                  // Passeport
            'TITRE_SEJOUR',               // Titre de séjour
            'PERMIS_CONDUIRE',            // Permis de conduire
            'INSCRIPTION_LISTE_ELECTORALE', // Inscription sur les listes électorales
            'PROCURATION_VOTE',           // Procuration de vote
            'ACTE_NAISSANCE',             // Demande d'acte de naissance
            'ACTE_MARIAGE',               // Demande d'acte de mariage
            'ACTE_DECES',                 // Demande d'acte de décès
            'NATURALISATION',             // Demande de naturalisation
            'ATTESTATION_ACCUEIL',        // Attestation d'accueil
            'LIVRET_FAMILLE',             // Demande de livret de famille
            'CARTE_GRISE',                // Carte grise / certificat d'immatriculation
            'AUTRE'                       // Autre type de démarche
        );
    }
    
    /**
     * Liste des motifs de demande valides
     *
     * @return array Codes des motifs de demande valides
     */
    public static function getMotifsDemandeValides()
    {
        return array(
            'PREMIERE_DEMANDE',  // Première demande
            'RENOUVELLEMENT',    // Renouvellement
            'PERTE',             // Perte
            'VOL',               // Vol
            'MODIFICATION',      // Modification des informations
            'CHANGEMENT_ADRESSE', // Changement d'adresse
            'DEPLACEMENT_ETRANGER', // Déplacement à l'étranger
            'DETERIORATION',     // Détérioration
            'AUTRE'              // Autre motif
        );
    }
    
    /**
     * Liste des modalités de démarche valides
     *
     * @return array Codes des modalités valides
     */
    public static function getModalitesDemarcheValides()
    {
        return array(
            'EN_LIGNE',         // Démarche en ligne
            'SUR_PLACE',        // Démarche sur place
            'PAR_COURRIER',     // Démarche par courrier
            'PAR_TELESERVICE'   // Démarche par téléservice
        );
    }
    
    /**
     * Récupère les options du dictionnaire des types de démarche citoyenne
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeDemarcheCitoyenneOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'citoyenne_type_demarche', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts de démarche citoyenne
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutDemarcheCitoyenneOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'citoyenne_statut_demarche', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des motifs de demande
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getMotifDemandeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'citoyenne_motif_demande', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de titre de séjour
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeTitreSejour($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'citoyenne_type_titre_sejour', $usekeys, $show_empty);
    }
    
    /**
     * Vérifie si cette démarche spécifique est une démarche citoyenne
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isCitoyenne()
    {
        return true;
    }
    
    /**
     * Définit des constantes pour les types d'actions standards dans l'historique
     */
    const ACTION_CHANGEMENT_STATUT_DEMARCHE = 'CHANGEMENT_STATUT_DEMARCHE';
    const ACTION_ENREGISTREMENT_PRE_DEMANDE = 'ENREGISTREMENT_PRE_DEMANDE';
    const ACTION_DEPOT_DOSSIER = 'DEPOT_DOSSIER';
    const ACTION_PLANIFICATION_RENDEZ_VOUS = 'PLANIFICATION_RENDEZ_VOUS';
    const ACTION_RENDEZ_VOUS_EFFECTUE = 'RENDEZ_VOUS_EFFECTUE';
    const ACTION_DELIVRANCE_DOCUMENT = 'DELIVRANCE_DOCUMENT';
    const ACTION_RETRAIT_DOCUMENT = 'RETRAIT_DOCUMENT';
    const ACTION_REFUS_DEMANDE = 'REFUS_DEMANDE';
    const ACTION_ENREGISTREMENT_PROCURATION = 'ENREGISTREMENT_PROCURATION';
    const ACTION_ENREGISTREMENT_TIMBRE_FISCAL = 'ENREGISTREMENT_TIMBRE_FISCAL';
    const ACTION_MISE_A_JOUR_PIECES = 'MISE_A_JOUR_PIECES';
}

} // Fin de la condition if !class_exists

// Dernière modification: 2025-06-03 17:53:59 UTC par Kylian65
