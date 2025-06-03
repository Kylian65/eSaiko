<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches CAF des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarcheCAF', false)) {

class ElaskaParticulierDemarcheCAF extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_caf';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_caf';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AUX DÉMARCHES CAF
    //
    
    /**
     * @var string Numéro allocataire CAF
     */
    public $numero_allocataire;
    
    /**
     * @var string Code du type de prestation CAF (dictionnaire)
     */
    public $type_prestation_code;
    
    /**
     * @var string Code du statut de la demande CAF (dictionnaire)
     */
    public $statut_demande_code;
    
    /**
     * @var string Date de début des droits (format YYYY-MM-DD)
     */
    public $date_debut_droits;
    
    /**
     * @var string Date de fin des droits (format YYYY-MM-DD)
     */
    public $date_fin_droits;
    
    /**
     * @var double Montant de l'allocation
     */
    public $montant_allocation;
    
    /**
     * @var double Montant estimé de l'allocation (avant décision)
     */
    public $montant_estimation;
    
    /**
     * @var string Date de la dernière révision (format YYYY-MM-DD)
     */
    public $date_revision;
    
    /**
     * @var string Date de la prochaine révision prévue (format YYYY-MM-DD)
     */
    public $date_prochaine_revision;
    
    /**
     * @var string Motif de la démarche CAF
     */
    public $motif_demarche;
    
    /**
     * @var int ID du conseiller CAF
     */
    public $fk_contact_caf;
    
    /**
     * @var int Nombre d'enfants concernés par la prestation
     */
    public $nombre_enfants_concernes;
    
    /**
     * @var string Historique des actions spécifiques à la démarche CAF
     */
    public $historique_actions;
    
    /**
     * @var string Observations spécifiques à la démarche CAF
     */
    public $observations_caf;
    
    /**
     * @var int Flag indiquant si la démarche nécessite un contrôle (0=non, 1=oui)
     */
    public $controle_necessaire;
    
    /**
     * @var string Date du dernier contrôle (format YYYY-MM-DD)
     */
    public $date_dernier_controle;
    
    /**
     * @var string Résultat du dernier contrôle
     */
    public $resultat_controle;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_caf = array(
        'numero_allocataire' => array('type' => 'varchar(20)', 'label' => 'NumeroAllocataire', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'type_prestation_code' => array('type' => 'varchar(50)', 'label' => 'TypePrestation', 'enabled' => 1, 'position' => 1110, 'notnull' => 1, 'visible' => 1),
        'statut_demande_code' => array('type' => 'varchar(50)', 'label' => 'StatutDemande', 'enabled' => 1, 'position' => 1120, 'notnull' => 1, 'visible' => 1),
        'date_debut_droits' => array('type' => 'date', 'label' => 'DateDebutDroits', 'enabled' => 1, 'position' => 1130, 'notnull' => 0, 'visible' => 1),
        'date_fin_droits' => array('type' => 'date', 'label' => 'DateFinDroits', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'montant_allocation' => array('type' => 'double(24,8)', 'label' => 'MontantAllocation', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'montant_estimation' => array('type' => 'double(24,8)', 'label' => 'MontantEstimation', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'date_revision' => array('type' => 'date', 'label' => 'DateRevision', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'date_prochaine_revision' => array('type' => 'date', 'label' => 'DateProchaineRevision', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'motif_demarche' => array('type' => 'text', 'label' => 'MotifDemarche', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'fk_contact_caf' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactCAF', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'nombre_enfants_concernes' => array('type' => 'integer', 'label' => 'NombreEnfantsConcernes', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'observations_caf' => array('type' => 'text', 'label' => 'ObservationsCAF', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'controle_necessaire' => array('type' => 'boolean', 'label' => 'ControleNecessaire', 'enabled' => 1, 'position' => 1240, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'date_dernier_controle' => array('type' => 'date', 'label' => 'DateDernierControle', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'resultat_controle' => array('type' => 'text', 'label' => 'ResultatControle', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1)
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Fusion des champs spécifiques CAF avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_caf);
        
        // Valeurs par défaut spécifiques aux démarches CAF
        $this->type_demarche_code = 'CAF';  // Force le code de type de démarche
        if (!isset($this->statut_demande_code)) $this->statut_demande_code = 'A_CONSTITUER'; // Statut par défaut
        if (!isset($this->controle_necessaire)) $this->controle_necessaire = 0; // Par défaut pas de contrôle nécessaire
    }

    /**
     * Crée une démarche CAF dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à CAF
        $this->type_demarche_code = 'CAF';
        
        // Vérifications spécifiques CAF
        if (empty($this->type_prestation_code)) {
            $this->error = 'TypePrestationIsMandatory';
            return -1;
        }
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'une démarche CAF
            $this->addToNotes($user, "Création d'une démarche CAF pour " . $this->getTypePrestationLabel());
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
     * Met à jour une démarche CAF dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à CAF
        $this->type_demarche_code = 'CAF';
        
        // Vérifications spécifiques CAF
        if (empty($this->type_prestation_code)) {
            $this->error = 'TypePrestationIsMandatory';
            return -1;
        }
        
        if (empty($this->statut_demande_code)) {
            $this->error = 'StatutDemandeIsMandatory';
            return -1;
        }
        
        // Si la demande est accordée, assurez-vous que le montant de l'allocation est défini
        if ($this->statut_demande_code == 'ACCORDEE' && empty($this->montant_allocation) && $this->montant_allocation !== 0) {
            $this->error = 'MontantAllocationRequiredForAccordedStatus';
            return -1;
        }
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le statut de la demande CAF
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
            case 'DEPOSEE':
                // Mettre à jour le statut général de la démarche
                if ($this->statut_demarche_code == 'A_FAIRE') {
                    $this->statut_demarche_code = 'EN_COURS';
                }
                break;
                
            case 'ACCORDEE':
                // Si accordée mais pas de date de début des droits
                if (empty($this->date_debut_droits)) {
                    $this->date_debut_droits = date('Y-m-d');
                }
                
                // Si la démarche est terminée
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'REFUSEE':
                // Si refusée, clôturer la démarche
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
                
            case 'EN_REVISION':
                // Si en révision, réinitialiser certains champs
                if ($this->statut_demarche_code != 'EN_COURS') {
                    $this->statut_demarche_code = 'EN_COURS';
                }
                $this->date_revision = date('Y-m-d');
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
                $action = 'CHANGEMENT_STATUT_CAF';
                
                // Obtenir les libellés traduits des statuts
                $statut_options = self::getStatutDemandeOptions($particulier->langs);
                $ancien_statut_libelle = isset($statut_options[$ancien_statut]) ? $statut_options[$ancien_statut] : $ancien_statut;
                $nouveau_statut_libelle = isset($statut_options[$statut_code]) ? $statut_options[$statut_code] : $statut_code;
                
                $message = 'Changement de statut de la démarche CAF "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_statut_libelle.' → '.$nouveau_statut_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_caf',
                    $this->id,
                    $message,
                    array('statut_demande_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Met à jour les montants d'allocation
     *
     * @param User   $user                   Utilisateur effectuant l'action
     * @param double $montant_allocation     Montant de l'allocation
     * @param double $montant_estimation     Montant estimé de l'allocation (optionnel)
     * @param string $commentaire            Commentaire optionnel
     * @return int                           <0 si erreur, >0 si OK
     */
    public function updateMontants($user, $montant_allocation, $montant_estimation = null, $commentaire = '')
    {
        $ancien_montant = $this->montant_allocation;
        $ancienne_estimation = $this->montant_estimation;
        
        $this->montant_allocation = $montant_allocation;
        
        if ($montant_estimation !== null) {
            $this->montant_estimation = $montant_estimation;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Allocation: ".price($ancien_montant)." → ".price($this->montant_allocation);
        
        if ($montant_estimation !== null) {
            $details .= "; Estimation: ".price($ancienne_estimation)." → ".price($this->montant_estimation);
        }
        
        $this->ajouterActionHistorique($user, 'MISE_A_JOUR_MONTANTS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_MONTANTS_CAF';
                
                $message = 'Mise à jour des montants de la démarche CAF "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_caf',
                    $this->id,
                    $message,
                    array(
                        'montant_allocation' => array($ancien_montant, $this->montant_allocation)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Configure les dates de droits (début et fin)
     *
     * @param User   $user               Utilisateur effectuant l'action
     * @param string $date_debut_droits  Date de début des droits (YYYY-MM-DD)
     * @param string $date_fin_droits    Date de fin des droits (YYYY-MM-DD, optionnel)
     * @param string $commentaire        Commentaire optionnel
     * @return int                       <0 si erreur, >0 si OK
     */
    public function configurerDates($user, $date_debut_droits, $date_fin_droits = '', $commentaire = '')
    {
        // Vérification des dates
        if (empty($date_debut_droits)) {
            $this->error = 'DateDebutDroitsObligatoire';
            return -1;
        }
        
        // Si date de fin fournie, vérifier qu'elle est postérieure à la date de début
        if (!empty($date_fin_droits) && $date_debut_droits > $date_fin_droits) {
            $this->error = 'DateDebutDroitsNeDoitPasEtreSuperieureADateFinDroits';
            return -1;
        }
        
        $anciennes_dates = array(
            'debut' => $this->date_debut_droits,
            'fin' => $this->date_fin_droits
        );
        
        $this->date_debut_droits = $date_debut_droits;
        $this->date_fin_droits = $date_fin_droits;
        
        // Calculer la prochaine date de révision si nécessaire
        if (!empty($date_fin_droits)) {
            // Par défaut, on prévoit la révision 1 mois avant la fin des droits
            $date_fin = new DateTime($date_fin_droits);
            $date_fin->modify('-1 month');
            $this->date_prochaine_revision = $date_fin->format('Y-m-d');
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Début des droits: ".($anciennes_dates['debut'] ? dol_print_date($this->db->jdate($anciennes_dates['debut']), 'day') : 'Non défini');
        $details .= " → ".dol_print_date($this->db->jdate($this->date_debut_droits), 'day');
        
        if (!empty($this->date_fin_droits)) {
            $details .= "; Fin des droits: ".($anciennes_dates['fin'] ? dol_print_date($this->db->jdate($anciennes_dates['fin']), 'day') : 'Non défini');
            $details .= " → ".dol_print_date($this->db->jdate($this->date_fin_droits), 'day');
        }
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_DATES', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_DATES_CAF';
                
                $message = 'Configuration des dates de droits pour la démarche CAF "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_caf',
                    $this->id,
                    $message,
                    array(
                        'date_debut_droits' => array($anciennes_dates['debut'], $this->date_debut_droits),
                        'date_fin_droits' => array($anciennes_dates['fin'], $this->date_fin_droits)
                    )
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Enregistre un contrôle CAF
     *
     * @param User   $user                    Utilisateur effectuant l'action
     * @param string $date_controle           Date du contrôle (YYYY-MM-DD)
     * @param string $resultat_controle       Résultat du contrôle
     * @param int    $controle_necessaire     Drapeau indiquant si un nouveau contrôle est nécessaire
     * @param string $commentaire             Commentaire optionnel
     * @return int                            <0 si erreur, >0 si OK
     */
    public function enregistrerControle($user, $date_controle, $resultat_controle, $controle_necessaire = 0, $commentaire = '')
    {
        if (empty($date_controle) || empty($resultat_controle)) {
            $this->error = 'DateEtResultatControleObligatoires';
            return -1;
        }
        
        $ancien_date_controle = $this->date_dernier_controle;
        $ancien_resultat = $this->resultat_controle;
        $ancien_controle_necessaire = $this->controle_necessaire;
        
        $this->date_dernier_controle = $date_controle;
        $this->resultat_controle = $resultat_controle;
        $this->controle_necessaire = $controle_necessaire ? 1 : 0;
        
        // Mise à jour du statut de la démarche si besoin
        if ($this->statut_demarche_code == 'TERMINEE' && $controle_necessaire) {
            $this->statut_demarche_code = 'EN_COURS';
            $this->progression = 80; // Réouverture mais avancée
            $this->date_cloture = null;
            $this->fk_user_cloture = null;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Contrôle CAF le ".dol_print_date($this->db->jdate($date_controle), 'day');
        $details .= "; Résultat: ".$resultat_controle;
        $details .= "; Nouveau contrôle nécessaire: ".($controle_necessaire ? 'Oui' : 'Non');
        
        $this->ajouterActionHistorique($user, 'CONTROLE_CAF', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONTROLE_CAF';
                
                $message = 'Enregistrement d\'un contrôle CAF pour la démarche "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_caf',
                    $this->id,
                    $message,
                    array(
                        'date_dernier_controle' => array($ancien_date_controle, $this->date_dernier_controle),
                        'controle_necessaire' => array($ancien_controle_necessaire, $this->controle_necessaire)
                    )
                );
            }
        }
        
        return $result;
    }

    /**
     * Ajoute une entrée à l'historique des actions spécifiques de la démarche CAF
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
     * Ajoute une action à l'historique spécifique de la démarche CAF
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
                        case 'CONFIGURATION_DATES':
                            $class = 'bg-warning';
                            break;
                        case 'CONTROLE_CAF':
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
     * Obtient le libellé du type de prestation
     * 
     * @return string Libellé du type de prestation
     */
    public function getTypePrestationLabel()
    {
        $types = self::getTypePrestationOptions($this->langs);
        return isset($types[$this->type_prestation_code]) ? $types[$this->type_prestation_code] : $this->type_prestation_code;
    }
    
    /**
     * Liste des statuts de demande valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsDemandeValides()
    {
        return array(
            'A_CONSTITUER',    // Dossier à constituer
            'CONSTITUEE',      // Demande constituée mais pas encore déposée
            'DEPOSEE',         // Demande déposée à la CAF
            'EN_INSTRUCTION',  // Demande en cours d'instruction
            'INCOMPLETE',      // Demande incomplète, pièces manquantes
            'EN_REVISION',     // Demande en révision (après un contrôle)
            'ACCORDEE',        // Prestation accordée
            'REFUSEE',         // Prestation refusée
            'SUSPENDUE',       // Prestation suspendue
            'TERMINEE'         // Dossier terminé (fin de droits)
        );
    }
    
    /**
     * Liste des types de prestation CAF valides
     *
     * @return array Codes des types de prestation valides
     */
    public static function getTypesPrestationValides()
    {
        return array(
            'RSA',                    // Revenu de Solidarité Active
            'APL',                    // Aide Personnalisée au Logement
            'AAH',                    // Allocation aux Adultes Handicapés
            'PPA',                    // Prime d'Activité
            'CF',                     // Complément Familial
            'PAJE',                   // Prestation d'Accueil du Jeune Enfant
            'ASF',                    // Allocation de Soutien Familial
            'AF',                     // Allocations Familiales
            'AEEH',                   // Allocation d'Éducation de l'Enfant Handicapé
            'AJPP',                   // Allocation Journalière de Présence Parentale
            'PREPARE',                // Prestation Partagée d'Éducation de l'Enfant
            'ALF',                    // Allocation de Logement Familiale
            'ALS',                    // Allocation de Logement Sociale
            'ARCE',                   // Aide à la Reprise ou à la Création d'Entreprise
            'PRIME_DEMENAGEMENT',     // Prime de Déménagement
            'AIDE_VACANCES',          // Aide aux Vacances
            'AUTRE'                   // Autre prestation
        );
    }
    
    /**
     * Récupère les options du dictionnaire des types de prestation
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypePrestationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'caf_type_prestation', $usekeys, $show_empty);
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
        return self::getOptionsFromDictionary($langs, 'caf_statut_demande', $usekeys, $show_empty);
    }
    
    /**
     * Vérifie si cette démarche spécifique est une démarche CAF
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isCAF()
    {
        return true;
    }
    
    /**
     * Récupère le contact CAF associé à la démarche
     * 
     * @return Contact|null Contact ou null si non trouvé
     */
    public function getContactCAF()
    {
        if (empty($this->fk_contact_caf)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
        $contact = new Contact($this->db);
        
        if ($contact->fetch($this->fk_contact_caf) > 0) {
            return $contact;
        }
        
        return null;
    }
    
    /**
     * Définit des constantes pour les types d'actions standards dans l'historique
     */
    const ACTION_CHANGEMENT_STATUT_DEMANDE = 'CHANGEMENT_STATUT_DEMANDE';
    const ACTION_MISE_A_JOUR_MONTANTS = 'MISE_A_JOUR_MONTANTS';
    const ACTION_CONFIGURATION_DATES = 'CONFIGURATION_DATES';
    const ACTION_CONTROLE_CAF = 'CONTROLE_CAF';
    const ACTION_CONTACT_CONSEILLER = 'CONTACT_CONSEILLER';
    const ACTION_AJOUT_DOCUMENT = 'AJOUT_DOCUMENT';
    const ACTION_DEPOT_DEMANDE = 'DEPOT_DEMANDE';
    const ACTION_REVISION = 'REVISION';
    const ACTION_NOTE_INTERNE = 'NOTE_INTERNE';
}

} // Fin de la condition if !class_exists
