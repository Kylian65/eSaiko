<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches MDPH des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 * Dernière modification: 2025-06-03 16:57:35 UTC
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarcheMDPH', false)) {

class ElaskaParticulierDemarcheMDPH extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_mdph';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_mdph';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AUX DÉMARCHES MDPH
    //
    
    /**
     * @var string Numéro de dossier MDPH
     */
    public $numero_dossier_mdph;
    
    /**
     * @var string Département de la MDPH
     */
    public $departement_mdph;
    
    /**
     * @var string Numéro de sécurité sociale du bénéficiaire
     */
    public $numero_secu;
    
    /**
     * @var string Code du type de demande MDPH (dictionnaire)
     */
    public $type_demande_mdph_code;
    
    /**
     * @var string Code du statut de la demande (dictionnaire)
     */
    public $statut_demande_mdph_code;
    
    /**
     * @var string Date du dépôt de la demande (format YYYY-MM-DD)
     */
    public $date_depot_demande;
    
    /**
     * @var string Date de réception de l'accusé (format YYYY-MM-DD)
     */
    public $date_accuse_reception;
    
    /**
     * @var string Date de passage en CDAPH (format YYYY-MM-DD)
     */
    public $date_passage_cdaph;
    
    /**
     * @var string Date de décision (format YYYY-MM-DD)
     */
    public $date_decision;
    
    /**
     * @var string Date de notification de la décision (format YYYY-MM-DD)
     */
    public $date_notification;
    
    /**
     * @var string Date de début de validité des droits (format YYYY-MM-DD)
     */
    public $date_debut_droits;
    
    /**
     * @var string Date de fin de validité des droits (format YYYY-MM-DD)
     */
    public $date_fin_droits;
    
    /**
     * @var int Taux d'incapacité attribué (en pourcentage)
     */
    public $taux_incapacite;
    
    /**
     * @var string Catégorie d'incapacité (1, 2, 3, etc.)
     */
    public $categorie_incapacite;
    
    /**
     * @var string Code du type de handicap principal (dictionnaire)
     */
    public $type_handicap_code;
    
    /**
     * @var double Montant de l'allocation mensuelle (AAH, AEEH, etc.)
     */
    public $montant_allocation;
    
    /**
     * @var string Type d'allocation (AAH, AEEH, PCH, etc.)
     */
    public $type_allocation;
    
    /**
     * @var string Prestation(s) accordée(s) (format JSON)
     */
    public $prestations_accordees;
    
    /**
     * @var string Orientation décidée (établissement, service, etc.)
     */
    public $orientation;
    
    /**
     * @var string Code de l'établissement d'orientation (dictionnaire)
     */
    public $etablissement_orientation_code;
    
    /**
     * @var string Carte(s) attribuée(s) (invalidité, stationnement, priorité, etc.)
     */
    public $cartes_attribuees;
    
    /**
     * @var int Reconnaissance de la qualité de travailleur handicapé (0=non, 1=oui)
     */
    public $rqth;
    
    /**
     * @var int Demande de recours (0=non, 1=oui)
     */
    public $demande_recours;
    
    /**
     * @var string Type de recours (gracieux, contentieux)
     */
    public $type_recours;
    
    /**
     * @var string Date de dépôt du recours (format YYYY-MM-DD)
     */
    public $date_depot_recours;
    
    /**
     * @var string Décision du recours
     */
    public $decision_recours;
    
    /**
     * @var int ID du contact référent MDPH
     */
    public $fk_contact_referent;
    
    /**
     * @var string Commentaires du médecin (confidentiels)
     */
    public $commentaires_medicaux;
    
    /**
     * @var string Projet de vie (texte détaillant le projet)
     */
    public $projet_vie;
    
    /**
     * @var string Besoins d'aménagement (logement, véhicule, etc.)
     */
    public $besoins_amenagement;
    
    /**
     * @var string Historique des actions spécifiques à la démarche MDPH
     */
    public $historique_actions;
    
    /**
     * @var string Pièces justificatives à fournir (format JSON)
     */
    public $pieces_justificatives;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_mdph = array(
        'numero_dossier_mdph' => array('type' => 'varchar(50)', 'label' => 'NumeroDossierMDPH', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'departement_mdph' => array('type' => 'varchar(3)', 'label' => 'DepartementMDPH', 'enabled' => 1, 'position' => 1110, 'notnull' => 1, 'visible' => 1),
        'numero_secu' => array('type' => 'varchar(15)', 'label' => 'NumeroSecu', 'enabled' => 1, 'position' => 1120, 'notnull' => 0, 'visible' => 1),
        'type_demande_mdph_code' => array('type' => 'varchar(50)', 'label' => 'TypeDemandeMDPH', 'enabled' => 1, 'position' => 1130, 'notnull' => 1, 'visible' => 1),
        'statut_demande_mdph_code' => array('type' => 'varchar(50)', 'label' => 'StatutDemandeMDPH', 'enabled' => 1, 'position' => 1140, 'notnull' => 1, 'visible' => 1),
        'date_depot_demande' => array('type' => 'date', 'label' => 'DateDepotDemande', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'date_accuse_reception' => array('type' => 'date', 'label' => 'DateAccuseReception', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'date_passage_cdaph' => array('type' => 'date', 'label' => 'DatePassageCDAPH', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'date_decision' => array('type' => 'date', 'label' => 'DateDecision', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'date_notification' => array('type' => 'date', 'label' => 'DateNotification', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'date_debut_droits' => array('type' => 'date', 'label' => 'DateDebutDroits', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'date_fin_droits' => array('type' => 'date', 'label' => 'DateFinDroits', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'taux_incapacite' => array('type' => 'integer', 'label' => 'TauxIncapacite', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'categorie_incapacite' => array('type' => 'varchar(10)', 'label' => 'CategorieIncapacite', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'type_handicap_code' => array('type' => 'varchar(50)', 'label' => 'TypeHandicap', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'montant_allocation' => array('type' => 'double(24,8)', 'label' => 'MontantAllocation', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'type_allocation' => array('type' => 'varchar(50)', 'label' => 'TypeAllocation', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1),
        'prestations_accordees' => array('type' => 'text', 'label' => 'PrestationsAccordees', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'orientation' => array('type' => 'varchar(255)', 'label' => 'Orientation', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'etablissement_orientation_code' => array('type' => 'varchar(50)', 'label' => 'EtablissementOrientation', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1),
        'cartes_attribuees' => array('type' => 'varchar(255)', 'label' => 'CartesAttribuees', 'enabled' => 1, 'position' => 1300, 'notnull' => 0, 'visible' => 1),
        'rqth' => array('type' => 'boolean', 'label' => 'RQTH', 'enabled' => 1, 'position' => 1310, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'demande_recours' => array('type' => 'boolean', 'label' => 'DemandeRecours', 'enabled' => 1, 'position' => 1320, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'type_recours' => array('type' => 'varchar(50)', 'label' => 'TypeRecours', 'enabled' => 1, 'position' => 1330, 'notnull' => 0, 'visible' => 1),
        'date_depot_recours' => array('type' => 'date', 'label' => 'DateDepotRecours', 'enabled' => 1, 'position' => 1340, 'notnull' => 0, 'visible' => 1),
        'decision_recours' => array('type' => 'text', 'label' => 'DecisionRecours', 'enabled' => 1, 'position' => 1350, 'notnull' => 0, 'visible' => 1),
        'fk_contact_referent' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactReferent', 'enabled' => 1, 'position' => 1360, 'notnull' => 0, 'visible' => 1),
        'commentaires_medicaux' => array('type' => 'text', 'label' => 'CommentairesMedicaux', 'enabled' => 1, 'position' => 1370, 'notnull' => 0, 'visible' => 1, 'alwayseditable' => 0),
        'projet_vie' => array('type' => 'text', 'label' => 'ProjetVie', 'enabled' => 1, 'position' => 1380, 'notnull' => 0, 'visible' => 1),
        'besoins_amenagement' => array('type' => 'text', 'label' => 'BesoinsAmenagement', 'enabled' => 1, 'position' => 1390, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 1400, 'notnull' => 0, 'visible' => 1),
        'pieces_justificatives' => array('type' => 'text', 'label' => 'PiecesJustificatives', 'enabled' => 1, 'position' => 1410, 'notnull' => 0, 'visible' => 1)
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Fusion des champs spécifiques MDPH avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_mdph);
        
        // Valeurs par défaut spécifiques aux démarches MDPH
        $this->type_demarche_code = 'MDPH';  // Force le code de type de démarche
        if (!isset($this->statut_demande_mdph_code)) $this->statut_demande_mdph_code = 'A_CONSTITUER'; // Statut par défaut
        if (!isset($this->rqth)) $this->rqth = 0; // Par défaut pas de RQTH
        if (!isset($this->demande_recours)) $this->demande_recours = 0; // Par défaut pas de demande de recours
    }

    /**
     * Crée une démarche MDPH dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à MDPH
        $this->type_demarche_code = 'MDPH';
        
        // Vérifications spécifiques aux démarches MDPH
        if (empty($this->type_demande_mdph_code)) {
            $this->error = 'TypeDemandeMDPHIsMandatory';
            return -1;
        }
        
        if (empty($this->departement_mdph)) {
            $this->error = 'DepartementMDPHIsMandatory';
            return -1;
        }
        
        // Vérification du format du numéro de sécurité sociale si fourni
        if (!empty($this->numero_secu) && !$this->isValidNumeroSecu($this->numero_secu)) {
            $this->error = 'InvalidNumeroSecuFormat';
            return -1;
        }
        
        // Génération automatique du libellé s'il n'est pas fourni
        if (empty($this->libelle)) {
            $this->libelle = $this->getTypeDemandeMDPHLabel();
            if (!empty($this->numero_dossier_mdph)) {
                $this->libelle .= ' - n° ' . $this->numero_dossier_mdph;
            }
            $this->libelle .= ' (Dpt ' . $this->departement_mdph . ')';
        }
        
        // Initialisation des pièces justificatives
        if (empty($this->pieces_justificatives)) {
            $this->pieces_justificatives = $this->getPiecesJustificativesParDefaut();
        }
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'une démarche MDPH
            $note = "Création d'une démarche MDPH de type " . $this->getTypeDemandeMDPHLabel();
            $note .= " dans le département " . $this->departement_mdph;
            if (!empty($this->numero_dossier_mdph)) {
                $note .= " (dossier n°" . $this->numero_dossier_mdph . ")";
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
     * Met à jour une démarche MDPH dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à MDPH
        $this->type_demarche_code = 'MDPH';
        
        // Vérifications spécifiques aux démarches MDPH
        if (empty($this->type_demande_mdph_code)) {
            $this->error = 'TypeDemandeMDPHIsMandatory';
            return -1;
        }
        
        if (empty($this->departement_mdph)) {
            $this->error = 'DepartementMDPHIsMandatory';
            return -1;
        }
        
        // Vérification du format du numéro de sécurité sociale si fourni
        if (!empty($this->numero_secu) && !$this->isValidNumeroSecu($this->numero_secu)) {
            $this->error = 'InvalidNumeroSecuFormat';
            return -1;
        }
        
        // Vérification de la cohérence des dates
        if (!empty($this->date_debut_droits) && !empty($this->date_fin_droits) && $this->date_fin_droits < $this->date_debut_droits) {
            $this->error = 'DateFinDroitsCantBeBeforeDateDebutDroits';
            return -1;
        }
        
        // Vérification du taux d'incapacité s'il est fourni
        if (!empty($this->taux_incapacite) && ($this->taux_incapacite < 0 || $this->taux_incapacite > 100)) {
            $this->error = 'TauxIncapaciteMustBeBetween0And100';
            return -1;
        }
        
        // Si demande de recours cochée mais sans type de recours
        if ($this->demande_recours && empty($this->type_recours)) {
            $this->error = 'TypeRecoursRequiredWhenDemandeRecours';
            return -1;
        }
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le statut de la démarche MDPH
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau code de statut
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function changerStatutDemandeMDPH($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsDemandeMDPHValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatutDemandeMDPHCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_demande_mdph_code;
        $this->statut_demande_mdph_code = $statut_code;
        
        // Mises à jour automatiques selon le statut
        switch ($statut_code) {
            case 'DOSSIER_CONSTITUE':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 30; // 30% de progression
                break;
                
            case 'DEPOSE':
                if (empty($this->date_depot_demande)) {
                    $this->date_depot_demande = date('Y-m-d');
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 40; // 40% de progression
                break;
                
            case 'ACCUSE_RECU':
                if (empty($this->date_accuse_reception)) {
                    $this->date_accuse_reception = date('Y-m-d');
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 50; // 50% de progression
                break;
                
            case 'EN_INSTRUCTION':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 60; // 60% de progression
                break;
                
            case 'PASSAGE_CDAPH':
                if (empty($this->date_passage_cdaph)) {
                    $this->date_passage_cdaph = date('Y-m-d');
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 70; // 70% de progression
                break;
                
            case 'DECISION_PRISE':
                if (empty($this->date_decision)) {
                    $this->date_decision = date('Y-m-d');
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 80; // 80% de progression
                break;
                
            case 'NOTIFICATION_ENVOYEE':
                if (empty($this->date_notification)) {
                    $this->date_notification = date('Y-m-d');
                }
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 90; // 90% de progression
                break;
                
            case 'DROITS_OUVERTS':
                if (empty($this->date_debut_droits)) {
                    $this->date_debut_droits = date('Y-m-d');
                }
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'REJET':
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'RECOURS_EN_COURS':
                $this->statut_demarche_code = 'EN_COURS';
                $this->progression = 70; // Retour à 70% de progression
                $this->demande_recours = 1; // Active la demande de recours
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
                $action = 'CHANGEMENT_STATUT_MDPH';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutDemandeMDPHOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche MDPH "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
                    $this->id,
                    $message,
                    array('statut_demande_mdph_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un dépôt de demande MDPH
     *
     * @param User   $user                  Utilisateur effectuant l'action
     * @param string $date_depot_demande    Date de dépôt (YYYY-MM-DD)
     * @param string $numero_dossier_mdph   Numéro de dossier attribué
     * @param string $commentaire          Commentaire optionnel
     * @return int                          <0 si erreur, >0 si OK
     */
    public function enregistrerDepot($user, $date_depot_demande, $numero_dossier_mdph = '', $commentaire = '')
    {
        // Vérifications
        if (empty($date_depot_demande)) {
            $this->error = 'DateDepotDemandeObligatoire';
            return -1;
        }
        
        $ancienne_date_depot = $this->date_depot_demande;
        $ancien_numero_dossier = $this->numero_dossier_mdph;
        
        $this->date_depot_demande = $date_depot_demande;
        
        if (!empty($numero_dossier_mdph)) {
            $this->numero_dossier_mdph = $numero_dossier_mdph;
        }
        
        // Mise à jour du statut si ce n'est pas déjà au moins DEPOSE
        if ($this->statut_demande_mdph_code == 'A_CONSTITUER' || $this->statut_demande_mdph_code == 'DOSSIER_CONSTITUE') {
            $this->statut_demande_mdph_code = 'DEPOSE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 40;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Dépôt de la demande le " . dol_print_date($this->db->jdate($date_depot_demande), 'day');
        
        if (!empty($numero_dossier_mdph) && $numero_dossier_mdph != $ancien_numero_dossier) {
            $details .= "; N° dossier: ".($ancien_numero_dossier ?: 'Non défini')." → ".$this->numero_dossier_mdph;
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_DEPOT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DEPOT_DEMANDE_MDPH';
                
                $message = 'Dépôt de la demande MDPH "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
                    $this->id,
                    $message,
                    array('date_depot_demande' => array($ancienne_date_depot, $this->date_depot_demande))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une notification de décision
     *
     * @param User   $user                       Utilisateur effectuant l'action
     * @param string $date_notification          Date de notification (YYYY-MM-DD)
     * @param string $date_debut_droits          Date de début des droits (YYYY-MM-DD)
     * @param string $date_fin_droits            Date de fin des droits (YYYY-MM-DD)
     * @param int    $taux_incapacite            Taux d'incapacité attribué (optionnel)
     * @param string $commentaire                Commentaire optionnel
     * @return int                               <0 si erreur, >0 si OK
     */
    public function enregistrerNotification($user, $date_notification, $date_debut_droits, $date_fin_droits, $taux_incapacite = null, $commentaire = '')
    {
        // Vérifications
        if (empty($date_notification)) {
            $this->error = 'DateNotificationObligatoire';
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
        
        if ($taux_incapacite !== null && ($taux_incapacite < 0 || $taux_incapacite > 100)) {
            $this->error = 'TauxIncapaciteMustBeBetween0And100';
            return -1;
        }
        
        $ancienne_date_notification = $this->date_notification;
        $ancienne_date_debut = $this->date_debut_droits;
        $ancienne_date_fin = $this->date_fin_droits;
        $ancien_taux = $this->taux_incapacite;
        
        $this->date_notification = $date_notification;
        $this->date_debut_droits = $date_debut_droits;
        $this->date_fin_droits = $date_fin_droits;
        
        if ($taux_incapacite !== null) {
            $this->taux_incapacite = $taux_incapacite;
        }
        
        // Mise à jour du statut
        if ($this->statut_demande_mdph_code != 'NOTIFICATION_ENVOYEE' && $this->statut_demande_mdph_code != 'DROITS_OUVERTS') {
            $this->statut_demande_mdph_code = 'NOTIFICATION_ENVOYEE';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 90;
        }
        
        // Si la date de début des droits est aujourd'hui ou une date passée, passage au statut DROITS_OUVERTS
        $today = date('Y-m-d');
        if ($this->date_debut_droits <= $today && $this->statut_demande_mdph_code != 'DROITS_OUVERTS') {
            $this->statut_demande_mdph_code = 'DROITS_OUVERTS';
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
        $details = "Notification reçue le " . dol_print_date($this->db->jdate($date_notification), 'day');
        $details .= "; Période de validité: ";
        $details .= dol_print_date($this->db->jdate($date_debut_droits), 'day') . " → " . dol_print_date($this->db->jdate($date_fin_droits), 'day');
        
        if ($taux_incapacite !== null) {
            $details .= "; Taux d'incapacité: ".($ancien_taux !== null ? $ancien_taux.'%' : 'Non défini')." → ".$this->taux_incapacite.'%';
        }
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_NOTIFICATION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'NOTIFICATION_MDPH';
                
                $message = 'Notification de la décision MDPH pour "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
                    $this->id,
                    $message,
                    array(
                        'date_notification' => array($ancienne_date_notification, $this->date_notification),
                        'date_debut_droits' => array($ancienne_date_debut, $this->date_debut_droits),
                        'date_fin_droits' => array($ancienne_date_fin, $this->date_fin_droits)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure les prestations accordées
     *
     * @param User   $user                   Utilisateur effectuant l'action
     * @param array  $prestations            Tableau des prestations accordées
     * @param string $type_allocation        Type d'allocation accordée (optionnel)
     * @param double $montant_allocation     Montant de l'allocation (optionnel)
     * @param string $commentaire           Commentaire optionnel
     * @return int                           <0 si erreur, >0 si OK
     */
    public function configurerPrestations($user, $prestations, $type_allocation = '', $montant_allocation = null, $commentaire = '')
    {
        // Vérifications
        if (empty($prestations) || !is_array($prestations)) {
            $this->error = 'PrestationsObligatoires';
            return -1;
        }
        
        // Si montant allocation fourni, type allocation obligatoire
        if ($montant_allocation !== null && empty($type_allocation)) {
            $this->error = 'TypeAllocationRequiredWithMontant';
            return -1;
        }
        
        $anciennes_prestations = $this->prestations_accordees;
        $ancien_type_allocation = $this->type_allocation;
        $ancien_montant = $this->montant_allocation;
        
        $this->prestations_accordees = json_encode($prestations);
        
        if (!empty($type_allocation)) {
            $this->type_allocation = $type_allocation;
        }
        
        if ($montant_allocation !== null) {
            $this->montant_allocation = $montant_allocation;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Configuration des prestations accordées";
        
        if (!empty($type_allocation) && $type_allocation != $ancien_type_allocation) {
            $details .= "; Type d'allocation: ".($ancien_type_allocation ?: 'Non défini')." → ".$this->type_allocation;
        }
        
        if ($montant_allocation !== null) {
            $details .= "; Montant mensuel: ".price($ancien_montant)." → ".price($this->montant_allocation);
        }
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_PRESTATIONS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_PRESTATIONS_MDPH';
                
                $message = 'Configuration des prestations MDPH pour "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure les cartes et la RQTH
     *
     * @param User   $user             Utilisateur effectuant l'action
     * @param string $cartes_attribuees Cartes attribuées (séparées par des virgules)
     * @param int    $rqth              RQTH accordée (0=non, 1=oui)
     * @param string $commentaire      Commentaire optionnel
     * @return int                      <0 si erreur, >0 si OK
     */
    public function configurerCartesEtRQTH($user, $cartes_attribuees, $rqth, $commentaire = '')
    {
        $anciennes_cartes = $this->cartes_attribuees;
        $ancien_rqth = $this->rqth;
        
        $this->cartes_attribuees = $cartes_attribuees;
        $this->rqth = $rqth ? 1 : 0;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Cartes: ".($anciennes_cartes ?: 'Aucune')." → ".($this->cartes_attribuees ?: 'Aucune');
        $details .= "; RQTH: ".($ancien_rqth ? 'Oui' : 'Non')." → ".($this->rqth ? 'Oui' : 'Non');
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_CARTES_RQTH', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_CARTES_RQTH_MDPH';
                
                $message = 'Configuration des cartes et RQTH pour la démarche MDPH "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
                    $this->id,
                    $message,
                    array(
                        'rqth' => array($ancien_rqth, $this->rqth)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure l'orientation proposée
     *
     * @param User   $user                       Utilisateur effectuant l'action
     * @param string $orientation                Description de l'orientation
     * @param string $etablissement_orientation_code Code de l'établissement d'orientation (optionnel)
     * @param string $commentaire                Commentaire optionnel
     * @return int                               <0 si erreur, >0 si OK
     */
    public function configurerOrientation($user, $orientation, $etablissement_orientation_code = '', $commentaire = '')
    {
        $ancienne_orientation = $this->orientation;
        $ancien_etablissement = $this->etablissement_orientation_code;
        
        $this->orientation = $orientation;
        
        if (!empty($etablissement_orientation_code)) {
            $this->etablissement_orientation_code = $etablissement_orientation_code;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Orientation: ".($ancienne_orientation ?: 'Non définie')." → ".$this->orientation;
        
        if (!empty($etablissement_orientation_code) && $etablissement_orientation_code != $ancien_etablissement) {
            $details .= "; Établissement: ".($ancien_etablissement ?: 'Non défini')." → ".$this->etablissement_orientation_code;
        }
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_ORIENTATION', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_ORIENTATION_MDPH';
                
                $message = 'Configuration de l\'orientation MDPH pour "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
                    $this->id,
                    $message,
                    array('orientation' => array($ancienne_orientation, $this->orientation))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un recours
     *
     * @param User   $user                Utilisateur effectuant l'action
     * @param string $type_recours        Type de recours (gracieux, contentieux)
     * @param string $date_depot_recours  Date de dépôt du recours (YYYY-MM-DD)
     * @param string $commentaire         Commentaire optionnel
     * @return int                        <0 si erreur, >0 si OK
     */
    public function enregistrerRecours($user, $type_recours, $date_depot_recours, $commentaire = '')
    {
        // Vérifications
        if (empty($type_recours)) {
            $this->error = 'TypeRecoursObligatoire';
            return -1;
        }
        
        if (empty($date_depot_recours)) {
            $this->error = 'DateDepotRecoursObligatoire';
            return -1;
        }
        
        $ancien_type_recours = $this->type_recours;
        $ancienne_date_recours = $this->date_depot_recours;
        $ancienne_demande_recours = $this->demande_recours;
        
        $this->demande_recours = 1;
        $this->type_recours = $type_recours;
        $this->date_depot_recours = $date_depot_recours;
        
        // Mise à jour du statut
        if ($this->statut_demande_mdph_code != 'RECOURS_EN_COURS') {
            $this->statut_demande_mdph_code = 'RECOURS_EN_COURS';
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 70; // Retour à 70% de progression
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Recours " . $this->type_recours . " déposé le " . dol_print_date($this->db->jdate($date_depot_recours), 'day');
        
        $this->ajouterActionHistorique($user, 'ENREGISTREMENT_RECOURS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'RECOURS_MDPH';
                
                $message = 'Enregistrement d\'un recours pour la démarche MDPH "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
                    $this->id,
                    $message,
                    array(
                        'demande_recours' => array($ancienne_demande_recours, $this->demande_recours),
                        'type_recours' => array($ancien_type_recours, $this->type_recours)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre une décision de recours
     *
     * @param User   $user              Utilisateur effectuant l'action
     * @param string $decision_recours   Texte de la décision suite au recours
     * @param string $commentaire       Commentaire optionnel
     * @return int                       <0 si erreur, >0 si OK
     */
    public function enregistrerDecisionRecours($user, $decision_recours, $commentaire = '')
    {
        // Vérifications
        if (empty($decision_recours)) {
            $this->error = 'DecisionRecoursObligatoire';
            return -1;
        }
        
        if (!$this->demande_recours) {
            $this->error = 'AucunRecoursEnCours';
            return -1;
        }
        
        $ancienne_decision = $this->decision_recours;
        $this->decision_recours = $decision_recours;
        
        // Mise à jour du statut
        if ($this->statut_demande_mdph_code == 'RECOURS_EN_COURS') {
            // On reprend le statut DECISION_PRISE
            $this->statut_demande_mdph_code = 'DECISION_PRISE';
            if (empty($this->date_decision)) {
                $this->date_decision = date('Y-m-d');
            }
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 80;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Décision suite au recours : " . substr($this->decision_recours, 0, 50) . (strlen($this->decision_recours) > 50 ? '...' : '');
        
        $this->ajouterActionHistorique($user, 'DECISION_RECOURS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'DECISION_RECOURS_MDPH';
                
                $message = 'Enregistrement de la décision suite au recours pour la démarche MDPH "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }

    /**
     * Met à jour le projet de vie
     * 
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $projet_vie  Texte du projet de vie
     * @param string $commentaire Commentaire optionnel
     * @return int                <0 si erreur, >0 si OK
     */
    public function updateProjetVie($user, $projet_vie, $commentaire = '')
    {
        $ancien_projet = $this->projet_vie;
        $this->projet_vie = $projet_vie;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Mise à jour du projet de vie";
        
        $this->ajouterActionHistorique($user, 'MAJ_PROJET_VIE', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_PROJET_VIE_MDPH';
                
                $message = 'Mise à jour du projet de vie pour la démarche MDPH "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }

    /**
     * Met à jour les besoins d'aménagement
     * 
     * @param User   $user                Utilisateur effectuant l'action
     * @param string $besoins_amenagement Texte des besoins d'aménagement
     * @param string $commentaire         Commentaire optionnel
     * @return int                        <0 si erreur, >0 si OK
     */
    public function updateBesoinsAmenagement($user, $besoins_amenagement, $commentaire = '')
    {
        $anciens_besoins = $this->besoins_amenagement;
        $this->besoins_amenagement = $besoins_amenagement;
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Mise à jour des besoins d'aménagement";
        
        $this->ajouterActionHistorique($user, 'MAJ_BESOINS_AMENAGEMENT', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_BESOINS_AMENAGEMENT_MDPH';
                
                $message = 'Mise à jour des besoins d\'aménagement pour la démarche MDPH "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
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
                $action = 'MAJ_PIECES_MDPH';
                
                $message = 'Mise à jour des pièces justificatives pour la démarche MDPH "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_mdph',
                    $this->id,
                    $message
                );
            }
        }
        
        return $result;
    }
    
        /**
     * Récupère les prestations accordées
     * 
     * @return array Tableau des prestations accordées
     */
    public function getPrestationsAccordees()
    {
        if (empty($this->prestations_accordees)) {
            return array();
        }
        
        $prestations = json_decode($this->prestations_accordees, true);
        
        if (!is_array($prestations)) {
            return array();
        }
        
        return $prestations;
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
            array('libelle' => 'Formulaire de demande MDPH', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Certificat médical de moins de 6 mois', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Pièce d\'identité', 'obligatoire' => 1, 'fourni' => 0),
            array('libelle' => 'Justificatif de domicile', 'obligatoire' => 1, 'fourni' => 0)
        );
        
        // Ajouter des pièces spécifiques selon le type de demande MDPH
        switch ($this->type_demande_mdph_code) {
            case 'AAH':
                $pieces[] = array('libelle' => 'Justificatifs de ressources des 3 derniers mois', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Dernier avis d\'imposition', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'PCH':
                $pieces[] = array('libelle' => 'Justificatifs des aides techniques demandées', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Devis des aménagements souhaités', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'AEEH':
                $pieces[] = array('libelle' => 'Certificat de scolarité', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Justificatifs de frais supplémentaires', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'CARTE_MOBILITE':
                $pieces[] = array('libelle' => 'Photo d\'identité', 'obligatoire' => 1, 'fourni' => 0);
                break;
                
            case 'ORIENTATION_PRO':
                $pieces[] = array('libelle' => 'CV', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Diplômes et formations', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Rapport d\'évaluation professionnelle', 'obligatoire' => 0, 'fourni' => 0);
                break;
                
            case 'ORIENTATION_ESMS':
                $pieces[] = array('libelle' => 'Rapports éducatifs', 'obligatoire' => 1, 'fourni' => 0);
                $pieces[] = array('libelle' => 'Bilans psychologiques', 'obligatoire' => 0, 'fourni' => 0);
                break;
        }
        
        return json_encode($pieces);
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
     * Ajoute une entrée à l'historique des actions spécifiques de la démarche MDPH
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
     * Ajoute une action à l'historique spécifique de la démarche MDPH
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
                        case 'ENREGISTREMENT_DEPOT':
                            $class = 'bg-success';
                            break;
                        case 'ENREGISTREMENT_NOTIFICATION':
                            $class = 'bg-warning';
                            break;
                        case 'CONFIGURATION_PRESTATIONS':
                            $class = 'bg-primary';
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
     * Obtient le libellé du type de demande MDPH
     * 
     * @return string Libellé du type de demande
     */
    public function getTypeDemandeMDPHLabel()
    {
        $types = self::getTypeDemandeMDPHOptions($this->langs);
        return isset($types[$this->type_demande_mdph_code]) ? $types[$this->type_demande_mdph_code] : $this->type_demande_mdph_code;
    }
    
    /**
     * Liste des statuts de demande MDPH valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsDemandeMDPHValides()
    {
        return array(
            'A_CONSTITUER',        // Dossier à constituer
            'DOSSIER_CONSTITUE',   // Dossier constitué
            'DEPOSE',              // Dossier déposé
            'ACCUSE_RECU',         // Accusé de réception reçu
            'EN_INSTRUCTION',      // Dossier en cours d'instruction
            'PASSAGE_CDAPH',       // Passage en commission CDAPH
            'DECISION_PRISE',      // Décision prise
            'NOTIFICATION_ENVOYEE', // Notification envoyée
            'DROITS_OUVERTS',      // Droits ouverts
            'REJET',               // Demande rejetée
            'RECOURS_EN_COURS',    // Recours en cours
            'RENOUVELLEMENT_A_PREVOIR' // Renouvellement à prévoir
        );
    }
    
    /**
     * Liste des types de demande MDPH valides
     *
     * @return array Codes des types de demande valides
     */
    public static function getTypesDemandeMDPHValides()
    {
        return array(
            'AAH',               // Allocation aux Adultes Handicapés
            'PCH',               // Prestation de Compensation du Handicap
            'AEEH',              // Allocation d'Éducation de l'Enfant Handicapé
            'RQTH',              // Reconnaissance de la Qualité de Travailleur Handicapé
            'CARTE_MOBILITE',    // Carte Mobilité Inclusion
            'CARTE_INVALIDITE',  // Carte d'invalidité
            'CARTE_STATIONNEMENT', // Carte de stationnement
            'ORIENTATION_PRO',   // Orientation professionnelle
            'ORIENTATION_ESMS',  // Orientation établissement médico-social
            'RENOUVELLEMENT_DROITS', // Renouvellement de droits existants
            'REVISION_DROITS',   // Révision de droits existants
            'AUTRE'              // Autre type de demande
        );
    }
    
    /**
     * Liste des types de handicap valides
     *
     * @return array Codes des types de handicap valides
     */
    public static function getTypesHandicapValides()
    {
        return array(
            'MOTEUR',            // Handicap moteur
            'VISUEL',            // Handicap visuel
            'AUDITIF',           // Handicap auditif
            'PSYCHIQUE',         // Handicap psychique
            'MENTAL',            // Handicap mental
            'COGNITIF',          // Handicap cognitif
            'POLYHANDICAP',      // Polyhandicap
            'AUTISME',           // Troubles du spectre autistique
            'MALADIES_RARES',    // Maladies rares
            'TRAUMATISME_CRANIEN', // Traumatisme crânien
            'AUTRE'              // Autre type de handicap
        );
    }
    
    /**
     * Liste des types de recours valides
     *
     * @return array Codes des types de recours valides
     */
    public static function getTypesRecoursValides()
    {
        return array(
            'GRACIEUX',          // Recours gracieux auprès de la MDPH
            'RAPO',              // Recours administratif préalable obligatoire
            'CONTENTIEUX',       // Recours contentieux
            'CONCILIATION'       // Conciliation
        );
    }
    
    /**
     * Récupère les options du dictionnaire des types de demande MDPH
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeDemandeMDPHOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'mdph_type_demande', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts de demande MDPH
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutDemandeMDPHOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'mdph_statut_demande', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de handicap
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeHandicapOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'mdph_type_handicap', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de recours
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeRecoursOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'mdph_type_recours', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des établissements d'orientation
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getEtablissementOrientationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'mdph_etablissement', $usekeys, $show_empty);
    }
    
    /**
     * Vérifie si cette démarche spécifique est une démarche MDPH
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isMDPH()
    {
        return true;
    }
    
    /**
     * Récupère le contact référent MDPH associé à la démarche
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
    const ACTION_CHANGEMENT_STATUT_DEMANDE = 'CHANGEMENT_STATUT_DEMANDE';
    const ACTION_ENREGISTREMENT_DEPOT = 'ENREGISTREMENT_DEPOT';
    const ACTION_ENREGISTREMENT_NOTIFICATION = 'ENREGISTREMENT_NOTIFICATION';
    const ACTION_CONFIGURATION_PRESTATIONS = 'CONFIGURATION_PRESTATIONS';
    const ACTION_CONFIGURATION_CARTES_RQTH = 'CONFIGURATION_CARTES_RQTH';
    const ACTION_CONFIGURATION_ORIENTATION = 'CONFIGURATION_ORIENTATION';
    const ACTION_ENREGISTREMENT_RECOURS = 'ENREGISTREMENT_RECOURS';
    const ACTION_DECISION_RECOURS = 'DECISION_RECOURS';
    const ACTION_MAJ_PROJET_VIE = 'MAJ_PROJET_VIE';
    const ACTION_MAJ_BESOINS_AMENAGEMENT = 'MAJ_BESOINS_AMENAGEMENT';
    const ACTION_MISE_A_JOUR_PIECES = 'MISE_A_JOUR_PIECES';
    const ACTION_CONTACT_REFERENT = 'CONTACT_REFERENT';
    const ACTION_NOTE_INTERNE = 'NOTE_INTERNE';
}

} // Fin de la condition if !class_exists

// Mise à jour des informations de version
// Dernière modification: 2025-06-03 17:05:10 UTC par Kylian65
