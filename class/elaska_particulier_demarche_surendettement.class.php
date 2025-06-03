<?php
/**
 * eLaska - Classe spécialisée pour gérer les démarches de surendettement des particuliers
 * Date: 2025-06-03
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier_demarche.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulierDemarcheSurendettement', false)) {

class ElaskaParticulierDemarcheSurendettement extends ElaskaParticulierDemarche
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier_demarche_surendettement';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_demarche_surendettement';

    //
    // PROPRIÉTÉS SPÉCIFIQUES AU SURENDETTEMENT
    //
    
    /**
     * @var string Code du type de procédure de surendettement (dictionnaire)
     */
    public $type_procedure_code;
    
    /**
     * @var string Code du stade de la procédure (dictionnaire)
     */
    public $stade_procedure_code;
    
    /**
     * @var string Numéro de dossier à la Banque de France
     */
    public $numero_dossier_bdf;
    
    /**
     * @var double Montant total de l'endettement
     */
    public $montant_total_dette;
    
    /**
     * @var double Montant de la capacité de remboursement mensuelle
     */
    public $capacite_remboursement;
    
    /**
     * @var string Date de dépôt du dossier (format YYYY-MM-DD)
     */
    public $date_depot_dossier;
    
    /**
     * @var string Date de recevabilité du dossier (format YYYY-MM-DD)
     */
    public $date_recevabilite;
    
    /**
     * @var string Date de début du plan de remboursement (format YYYY-MM-DD)
     */
    public $date_debut_plan;
    
    /**
     * @var string Date de fin du plan de remboursement (format YYYY-MM-DD)
     */
    public $date_fin_plan;
    
    /**
     * @var int Durée du plan en mois
     */
    public $duree_plan_mois;
    
    /**
     * @var string Décision de la commission (texte)
     */
    public $decision_commission;
    
    /**
     * @var string Date de la décision (format YYYY-MM-DD)
     */
    public $date_decision;
    
    /**
     * @var int Nombre de créanciers dans le dossier
     */
    public $nombre_creanciers;
    
    /**
     * @var string Observations spécifiques au dossier de surendettement
     */
    public $observations_surendettement;
    
    /**
     * @var int ID du conseiller Banque de France
     */
    public $fk_contact_bdf;
    
    /**
     * @var string Historique des actions spécifiques au dossier de surendettement
     */
    public $historique_actions;
    
    /**
     * @var int Flag indiquant si le dossier inclut des dettes professionnelles (0=non, 1=oui)
     */
    public $inclut_dettes_pro;
    
    /**
     * @var double Pourcentage d'effacement de la dette
     */
    public $pourcentage_effacement;
    
    /**
     * @var string Code du niveau de suivi (dictionnaire)
     */
    public $niveau_suivi_code;

    /**
     * @var array Définition des champs additionnels pour le gestionnaire d'objets
     */
    public $fields_surendettement = array(
        'type_procedure_code' => array('type' => 'varchar(50)', 'label' => 'TypeProcedure', 'enabled' => 1, 'position' => 1100, 'notnull' => 1, 'visible' => 1),
        'stade_procedure_code' => array('type' => 'varchar(50)', 'label' => 'StadeProcedure', 'enabled' => 1, 'position' => 1110, 'notnull' => 1, 'visible' => 1),
        'numero_dossier_bdf' => array('type' => 'varchar(50)', 'label' => 'NumeroDossierBDF', 'enabled' => 1, 'position' => 1120, 'notnull' => 0, 'visible' => 1),
        'montant_total_dette' => array('type' => 'double(24,8)', 'label' => 'MontantDette', 'enabled' => 1, 'position' => 1130, 'notnull' => 0, 'visible' => 1),
        'capacite_remboursement' => array('type' => 'double(24,8)', 'label' => 'CapaciteRemboursement', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'date_depot_dossier' => array('type' => 'date', 'label' => 'DateDepotDossier', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'date_recevabilite' => array('type' => 'date', 'label' => 'DateRecevabilite', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'date_debut_plan' => array('type' => 'date', 'label' => 'DateDebutPlan', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'date_fin_plan' => array('type' => 'date', 'label' => 'DateFinPlan', 'enabled' => 1, 'position' => 1180, 'notnull' => 0, 'visible' => 1),
        'duree_plan_mois' => array('type' => 'integer', 'label' => 'DureePlanMois', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),
        'decision_commission' => array('type' => 'text', 'label' => 'DecisionCommission', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'date_decision' => array('type' => 'date', 'label' => 'DateDecision', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'nombre_creanciers' => array('type' => 'integer', 'label' => 'NombreCreanciers', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'observations_surendettement' => array('type' => 'text', 'label' => 'ObservationsSpecifiques', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'fk_contact_bdf' => array('type' => 'integer:Contact:contact/class/contact.class.php', 'label' => 'ContactBDF', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'inclut_dettes_pro' => array('type' => 'boolean', 'label' => 'InclutDettesPro', 'enabled' => 1, 'position' => 1260, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'pourcentage_effacement' => array('type' => 'double(8,2)', 'label' => 'PourcentageEffacement', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'niveau_suivi_code' => array('type' => 'varchar(50)', 'label' => 'NiveauSuivi', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1)
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Fusion des champs spécifiques au surendettement avec les champs de la classe parente
        $this->fields = array_merge($this->fields, $this->fields_surendettement);
        
        // Valeurs par défaut spécifiques au surendettement
        $this->type_demarche_code = 'SURENDETTEMENT';  // Force le code de type de démarche
        if (!isset($this->type_procedure_code)) $this->type_procedure_code = 'PRP'; // Procédure de rétablissement personnel par défaut
        if (!isset($this->stade_procedure_code)) $this->stade_procedure_code = 'DEPOT_DOSSIER'; // Stade initial par défaut
        if (!isset($this->niveau_suivi_code)) $this->niveau_suivi_code = 'NORMAL'; // Niveau de suivi par défaut
        if (!isset($this->inclut_dettes_pro)) $this->inclut_dettes_pro = 0; // Par défaut pas de dettes pro
    }

    /**
     * Crée une démarche de surendettement dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Force le type de démarche à SURENDETTEMENT
        $this->type_demarche_code = 'SURENDETTEMENT';
        
        // Appel de la méthode parente pour les vérifications standard
        $result = parent::create($user, $notrigger);
        
        if ($result > 0) {
            // Ajout d'une note spécifique indiquant qu'il s'agit d'un dossier de surendettement
            $this->addToNotes($user, "Création d'un dossier de surendettement");
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
     * Met à jour une démarche de surendettement dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Force le type de démarche à SURENDETTEMENT
        $this->type_demarche_code = 'SURENDETTEMENT';
        
        // Vérifications spécifiques au surendettement
        if (empty($this->type_procedure_code)) {
            $this->error = 'TypeProcedureIsMandatory';
            return -1;
        }
        
        if (empty($this->stade_procedure_code)) {
            $this->error = 'StadeProcedureIsMandatory';
            return -1;
        }
        
        // Si les dates de début et fin de plan sont renseignées, calculer automatiquement la durée
        if (!empty($this->date_debut_plan) && !empty($this->date_fin_plan) && empty($this->duree_plan_mois)) {
            $this->duree_plan_mois = $this->calculerDureePlan();
        }
        
        // Appel de la méthode parente
        return parent::update($user, $notrigger);
    }

    /**
     * Change le stade de la procédure de surendettement
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $stade_code  Nouveau code de stade
     * @param string $commentaire Commentaire optionnel sur le changement
     * @return int               <0 si erreur, >0 si OK
     */
    public function changerStadeProcedure($user, $stade_code, $commentaire = '')
    {
        $stades_valides = self::getStadesValides();
        
        if (!in_array($stade_code, $stades_valides)) {
            $this->error = 'InvalidStadeProcedureCode';
            return -1;
        }
        
        $ancien_stade = $this->stade_procedure_code;
        $this->stade_procedure_code = $stade_code;
        
        // Mises à jour automatiques selon le stade
        switch ($stade_code) {
            case 'DEPOT_DOSSIER':
                if (empty($this->date_depot_dossier)) {
                    $this->date_depot_dossier = date('Y-m-d');
                }
                break;
                
            case 'RECEVABILITE':
                if (empty($this->date_recevabilite)) {
                    $this->date_recevabilite = date('Y-m-d');
                }
                break;
                
            case 'PLAN_CONVENTIONNEL':
            case 'MESURES_IMPOSEES':
            case 'PRP_SANS_LJ':
            case 'PRP_AVEC_LJ':
                if (empty($this->date_decision)) {
                    $this->date_decision = date('Y-m-d');
                }
                
                // Si c'est un stade final, mettre à jour le statut de la démarche
                $this->statut_demarche_code = 'TERMINEE';
                $this->progression = 100;
                $this->date_cloture = dol_now();
                $this->fk_user_cloture = $user->id;
                break;
            
            case 'PLAN_EN_COURS':
                if (empty($this->date_debut_plan)) {
                    $this->date_debut_plan = date('Y-m-d');
                }
                break;
            
            case 'PLAN_TERMINE':
                if (empty($this->date_fin_plan)) {
                    $this->date_fin_plan = date('Y-m-d');
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
        $this->ajouterActionHistorique($user, 'CHANGEMENT_STADE', $ancien_stade . ' → ' . $stade_code, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CHANGEMENT_STADE_SURENDETTEMENT';
                
                // Obtenir les libellés traduits des stades
                $stade_options = self::getStadeProcedureOptions($particulier->langs);
                $ancien_stade_libelle = isset($stade_options[$ancien_stade]) ? $stade_options[$ancien_stade] : $ancien_stade;
                $nouveau_stade_libelle = isset($stade_options[$stade_code]) ? $stade_options[$stade_code] : $stade_code;
                
                $message = 'Changement de stade du dossier de surendettement "'.$this->libelle.'" (Réf: '.$this->ref.'): ' .
                           $ancien_stade_libelle.' → '.$nouveau_stade_libelle;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_surendettement',
                    $this->id,
                    $message,
                    array('stade_procedure_code' => array($ancien_stade, $stade_code))
                );
            }
        }
        
        return $result;
    }

    /**
     * Met à jour les montants du dossier de surendettement
     *
     * @param User   $user                     Utilisateur effectuant l'action
     * @param double $montant_dette            Montant total de la dette
     * @param double $capacite_remboursement   Capacité de remboursement mensuelle
     * @param int    $nombre_creanciers        Nombre de créanciers
     * @param double $pourcentage_effacement   Pourcentage d'effacement de la dette (0-100)
     * @param string $commentaire              Commentaire optionnel
     * @return int                             <0 si erreur, >0 si OK
     */
    public function updateMontants($user, $montant_dette, $capacite_remboursement, $nombre_creanciers = null, $pourcentage_effacement = null, $commentaire = '')
    {
        $anciens_montants = array(
            'dette' => $this->montant_total_dette,
            'capacite' => $this->capacite_remboursement,
            'creanciers' => $this->nombre_creanciers,
            'effacement' => $this->pourcentage_effacement
        );
        
        $this->montant_total_dette = $montant_dette;
        $this->capacite_remboursement = $capacite_remboursement;
        
        if ($nombre_creanciers !== null) {
            $this->nombre_creanciers = $nombre_creanciers;
        }
        
        if ($pourcentage_effacement !== null) {
            if ($pourcentage_effacement < 0 || $pourcentage_effacement > 100) {
                $this->error = 'PourcentageEffacementMustBeBetween0And100';
                return -1;
            }
            $this->pourcentage_effacement = $pourcentage_effacement;
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Dette: ".price($anciens_montants['dette'])." → ".price($this->montant_total_dette)."; ";
        $details .= "Capacité mensuelle: ".price($anciens_montants['capacite'])." → ".price($this->capacite_remboursement);
        
        if ($nombre_creanciers !== null) {
            $details .= "; Créanciers: ".$anciens_montants['creanciers']." → ".$this->nombre_creanciers;
        }
        
        if ($pourcentage_effacement !== null) {
            $details .= "; Effacement: ".($anciens_montants['effacement'] !== null ? $anciens_montants['effacement'].'%' : 'N/A')." → ".$this->pourcentage_effacement."%";
        }
        
        $this->ajouterActionHistorique($user, 'MISE_A_JOUR_MONTANTS', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'MAJ_MONTANTS_SURENDETTEMENT';
                
                $message = 'Mise à jour des montants du dossier de surendettement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_surendettement',
                    $this->id,
                    $message,
                    array(
                        'montant_total_dette' => array($anciens_montants['dette'], $this->montant_total_dette),
                        'capacite_remboursement' => array($anciens_montants['capacite'], $this->capacite_remboursement)
                    )
                );
            }
        }
        
        return $result;
    }
    /**
     * Ajoute une entrée à l'historique des actions spécifiques du dossier de surendettement
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
                        case 'CHANGEMENT_STADE':
                            $class = 'bg-info';
                            break;
                        case 'MISE_A_JOUR_MONTANTS':
                            $class = 'bg-success';
                            break;
                        case 'CONFIGURATION_PLAN':
                            $class = 'bg-warning';
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
     * Définit des constantes pour les types d'actions standards dans l'historique
     */
    const ACTION_CHANGEMENT_STADE = 'CHANGEMENT_STADE';
    const ACTION_MISE_A_JOUR_MONTANTS = 'MISE_A_JOUR_MONTANTS';
    const ACTION_CONFIGURATION_PLAN = 'CONFIGURATION_PLAN';
    const ACTION_AJOUT_DOCUMENT = 'AJOUT_DOCUMENT';
    const ACTION_CONTACT_CONSEILLER = 'CONTACT_CONSEILLER';
    const ACTION_PAIEMENT_MENSUALITE = 'PAIEMENT_MENSUALITE';
    const ACTION_REUNION_CLIENT = 'REUNION_CLIENT';
    const ACTION_NOTE_INTERNE = 'NOTE_INTERNE';


    
    /**
     * Configure le plan de remboursement
     *
     * @param User   $user              Utilisateur effectuant l'action
     * @param string $date_debut_plan   Date de début du plan (YYYY-MM-DD)
     * @param string $date_fin_plan     Date de fin du plan (YYYY-MM-DD)
     * @param int    $duree_plan_mois   Durée du plan en mois (optionnel, calculée sinon)
     * @param string $commentaire       Commentaire optionnel
     * @return int                      <0 si erreur, >0 si OK
     */
    public function configurerPlan($user, $date_debut_plan, $date_fin_plan, $duree_plan_mois = null, $commentaire = '')
    {
        // Vérification des dates
        if (empty($date_debut_plan) || empty($date_fin_plan)) {
            $this->error = 'DebutEtFinPlanObligatoires';
            return -1;
        }
        
        if ($date_debut_plan > $date_fin_plan) {
            $this->error = 'DateDebutPlanNeDoitPasEtreSuperieureADateFinPlan';
            return -1;
        }
        
        $anciens_params = array(
            'debut' => $this->date_debut_plan,
            'fin' => $this->date_fin_plan,
            'duree' => $this->duree_plan_mois
        );
        
        $this->date_debut_plan = $date_debut_plan;
        $this->date_fin_plan = $date_fin_plan;
        
        // Calculer ou utiliser la durée fournie
        if ($duree_plan_mois === null) {
            $this->duree_plan_mois = $this->calculerDureePlan();
        } else {
            $this->duree_plan_mois = $duree_plan_mois;
        }
        
        // Mise à jour du stade si pas encore en cours
        if ($this->stade_procedure_code != 'PLAN_EN_COURS' && $this->stade_procedure_code != 'PLAN_TERMINE') {
            $this->stade_procedure_code = 'PLAN_EN_COURS';
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $details = "Début: ".($anciens_params['debut'] ? dol_print_date($this->db->jdate($anciens_params['debut']), 'day') : 'Non défini');
        $details .= " → ".dol_print_date($this->db->jdate($this->date_debut_plan), 'day')."; ";
        $details .= "Fin: ".($anciens_params['fin'] ? dol_print_date($this->db->jdate($anciens_params['fin']), 'day') : 'Non défini');
        $details .= " → ".dol_print_date($this->db->jdate($this->date_fin_plan), 'day')."; ";
        $details .= "Durée: ".($anciens_params['duree'] ? $anciens_params['duree'].' mois' : 'Non défini');
        $details .= " → ".$this->duree_plan_mois." mois";
        
        $this->ajouterActionHistorique($user, 'CONFIGURATION_PLAN', $details, $commentaire);
        
        $result = parent::update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Récupération du particulier pour l'historique
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $action = 'CONFIG_PLAN_SURENDETTEMENT';
                
                $message = 'Configuration du plan de remboursement du dossier de surendettement "'.$this->libelle.'" (Réf: '.$this->ref.') : ' . $details;
                
                if (!empty($commentaire)) {
                    $message .= ' avec le commentaire : '.$commentaire;
                }
                
                $particulier->addHistorique(
                    $user,
                    $action,
                    'demarche_surendettement',
                    $this->id,
                    $message,
                    array(
                        'date_debut_plan' => array($anciens_params['debut'], $this->date_debut_plan),
                        'date_fin_plan' => array($anciens_params['fin'], $this->date_fin_plan),
                        'duree_plan_mois' => array($anciens_params['duree'], $this->duree_plan_mois)
                    )
                );
            }
        }
        
        return $result;
    }

    /**
     * Calcule la durée du plan en mois d'après les dates de début et fin
     *
     * @return int Nombre de mois
     */
    public function calculerDureePlan()
    {
        if (empty($this->date_debut_plan) || empty($this->date_fin_plan)) {
            return 0;
        }
        
        try {
            $date_debut = new DateTime($this->date_debut_plan);
            $date_fin = new DateTime($this->date_fin_plan);
            
            $interval = $date_debut->diff($date_fin);
            $mois = $interval->y * 12 + $interval->m;
            
            // Si on a des jours en plus, on ajoute un mois
            if ($interval->d > 0) {
                $mois++;
            }
            
            return $mois;
        } catch (Exception $e) {
            dol_syslog('Error in ElaskaParticulierDemarcheSurendettement::calculerDureePlan: '.$e->getMessage(), LOG_ERR);
            return 0;
        }
    }

    /**
     * Calcule la mensualité théorique du plan de remboursement
     *
     * @param double $taux_interet Taux d'intérêt annuel (optionnel, en pourcentage)
     * @return double Montant de la mensualité
     */
    public function calculerMensualite($taux_interet = 0)
    {
        // Si les montants ne sont pas renseignés, impossible de calculer
        if (empty($this->montant_total_dette) || empty($this->duree_plan_mois)) {
            return 0;
        }
        
        // Si effacement partiel, prendre en compte le pourcentage
        $montant_a_rembourser = $this->montant_total_dette;
        if (!empty($this->pourcentage_effacement) && $this->pourcentage_effacement > 0) {
            $montant_a_rembourser = $this->montant_total_dette * (1 - ($this->pourcentage_effacement / 100));
        }
        
        // Sans intérêts, simple division
        if ($taux_interet <= 0) {
            return $montant_a_rembourser / $this->duree_plan_mois;
        }
        
        // Avec intérêts, formule de calcul d'un crédit amortissable
        $taux_mensuel = $taux_interet / 100 / 12;
        $numerateur = $montant_a_rembourser * $taux_mensuel * pow(1 + $taux_mensuel, $this->duree_plan_mois);
        $denominateur = pow(1 + $taux_mensuel, $this->duree_plan_mois) - 1;
        
        return $numerateur / $denominateur;
    }

    /**
     * Ajoute une action à l'historique spécifique du dossier de surendettement
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
     * Liste des stades valides pour les procédures de surendettement
     *
     * @return array Codes des stades valides
     */
    public static function getStadesValides()
    {
        return array(
            'DEPOT_DOSSIER',
            'DOSSIER_INCOMPLET',
            'RECEVABILITE',
            'IRRECEVABILITE',
            'ORIENTATION', 
            'NEGOCIATION',
            'PLAN_CONVENTIONNEL',
            'ECHEC_NEGOCIATION',
            'MESURES_IMPOSEES',
            'PRP_SANS_LJ',
            'PRP_AVEC_LJ',
            'PLAN_EN_COURS',
            'PLAN_TERMINE',
            'CADUCITE',
            'DOSSIER_CLASSE',
            'APPEL_COMMISSION'
        );
    }

    /**
     * Liste des types de procédure valides
     *
     * @return array Codes des types de procédure valides
     */
    public static function getTypesProcedureValides()
    {
        return array(
            'PRP',              // Procédure de Rétablissement Personnel
            'REECHELONNEMENT',  // Plan de rééchelonnement de dettes
            'MORATOIRE',        // Moratoire
            'EFFACEMENT'        // Effacement partiel de dettes
        );
    }

    /**
     * Récupère les options du dictionnaire des stades de procédure
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStadeProcedureOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'surendettement_stade', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des types de procédure
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeProcedureOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'surendettement_type_procedure', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des niveaux de suivi
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getNiveauSuiviOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'surendettement_niveau_suivi', $usekeys, $show_empty);
    }

    /**
     * Vérifie si cette démarche spécifique est un dossier de surendettement
     * (Toujours vrai pour cette classe, sert à la cohérence avec la classe parente)
     * 
     * @return bool true (toujours pour cette classe)
     */
    public function isSurendettement()
    {
        return true;
    }
    
    /**
     * Récupère le conseiller BDF associé au dossier
     * 
     * @return Contact|null Contact ou null si non trouvé
     */
    public function getContactBDF()
    {
        if (empty($this->fk_contact_bdf)) {
            return null;
        }
        
        require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
        $contact = new Contact($this->db);
        
        if ($contact->fetch($this->fk_contact_bdf) > 0) {
            return $contact;
        }
        
        return null;
    }

/**
 * Associe un contact BDF au dossier
 *
 * @param User    $user           Utilisateur effectuant l'action
 * @param integer $id_contact_bdf ID du contact BDF à associer
 * @return integer                <0 si erreur, >0 si OK
 */
public function setContactBDF($user, $id_contact_bdf)
{
    require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
    $contact = new Contact($this->db);
    
    // Vérifier que le contact existe
    if ($contact->fetch($id_contact_bdf) <= 0) {
        $this->error = 'ContactBDFDoesNotExist';
        return -1;
    }
    
    $old_contact_id = $this->fk_contact_bdf;
    $this->fk_contact_bdf = $id_contact_bdf;
    
    // Mise à jour de la base
    $result = $this->update($user, 1);
    
    if ($result > 0) {
        $this->ajouterActionHistorique(
            $user, 
            'CHANGEMENT_CONTACT_BDF',
            'Contact BDF changé: ' . $contact->getFullName($this->langs)
        );
    }
    
    return $result;
}
    
}

} // Fin de la condition if !class_exists
