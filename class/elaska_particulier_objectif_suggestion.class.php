<?php
/* Copyright (C) 2025 Elaska Dev Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
dol_include_once('/elaska/class/elaska_particulier.class.php');

/**
 * Classe de gestion des suggestions d'objectifs pour particuliers
 * 
 * @package    Elaska
 * @subpackage Particuliers
 */
class ElaskaParticulierObjectifSuggestion extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */
    public $element = 'elaska_particulier_objectif_suggestion';
    
    /**
     * @var string Name of table without prefix where object is stored
     */
    public $table_element = 'elaska_particulier_objectif_suggestion';
    
    /**
     * @var string String with name of icon for objectif suggestion
     */
    public $picto = 'star';
    
    /**
     * @var int    ID
     */
    public $id;
    
    /**
     * @var int    Client ID
     */
    public $fk_particulier;
    
    /**
     * @var string Title
     */
    public $titre;
    
    /**
     * @var string Description
     */
    public $description;
    
    /**
     * @var string Category
     */
    public $categorie;
    
    /**
     * @var int    Estimated duration in months
     */
    public $duree_estimee = 3;
    
    /**
     * @var int    Score (0-100)
     */
    public $score = 0;
    
    /**
     * @var int    Budget estimation
     */
    public $budget_estime = 0;
    
    /**
     * @var string Creation date
     */
    public $date_creation;
    
    /**
     * @var string Expiration date
     */
    public $date_expiration;
    
    /**
     * @var string Suggestion status
     */
    public $statut;
    
    /**
     * @var int    Creator user ID
     */
    public $fk_user_creat;
    
    /**
     * @var string Source of the suggestion
     */
    public $source;
    
    /**
     * @var string Additional data (JSON)
     */
    public $donnees_additionnelles;
    
    /**
     * @var bool   Processed flag
     */
    public $est_traite = 0;
    
    /**
     * @var string Default steps (JSON)
     */
    public $etapes_defaut;
    
    /**
     * Statuts possibles
     */
    const STATUS_NEW = 'nouveau';
    const STATUS_SHOWN = 'affiche';
    const STATUS_ACCEPTED = 'accepte';
    const STATUS_REFUSED = 'refuse';
    const STATUS_EXPIRED = 'expire';
    
    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->date_creation = dol_now();
        $this->statut = self::STATUS_NEW;
        
        // Calcul par défaut de la date d'expiration (3 mois)
        $this->date_expiration = dol_time_plus_duree(dol_now(), 3, 'm');
    }
    
    /**
     * Create object into database
     *
     * @param  User $user      User that creates
     * @param  bool $notrigger false=launch triggers after, true=disable triggers
     * @return int             <0 if KO, Id of created object if OK
     */
    public function create(User $user, $notrigger = false)
    {
        global $conf, $langs;
        
        $error = 0;
        
        // Verification
        if (empty($this->titre)) {
            $this->errors[] = $langs->trans('ErrorFieldRequired', $langs->transnoentities('Title'));
            $error++;
        }
        
        if (empty($this->fk_particulier)) {
            $this->errors[] = $langs->trans('ErrorFieldRequired', $langs->transnoentities('Client'));
            $error++;
        }
        
        if ($error) {
            dol_syslog(__METHOD__ . ' ' . $this->errorsToString(), LOG_ERR);
            return -1;
        }
        
        $this->fk_user_creat = $user->id;
        
        // JSON encodage des données si nécessaire
        if (is_array($this->donnees_additionnelles)) {
            $this->donnees_additionnelles = json_encode($this->donnees_additionnelles);
        }
        
        if (is_array($this->etapes_defaut)) {
            $this->etapes_defaut = json_encode($this->etapes_defaut);
        }
        
        // Insert
        $result = $this->createCommon($user, $notrigger);
        
        if ($result <= 0) {
            $this->error = $this->db->lasterror();
            $this->errors[] = $this->error;
            dol_syslog(__METHOD__ . ' ' . $this->errorsToString(), LOG_ERR);
            return -1;
        }
        
        return $this->id;
    }
    
    /**
     * Load object in memory from the database
     *
     * @param int    $id   Id object
     * @param string $ref  Ref
     * @return int         <0 if KO, 0 if not found, >0 if OK
     */
    public function fetch($id, $ref = null)
    {
        $result = $this->fetchCommon($id, $ref);
        
        if ($result > 0) {
            // Decoder JSON data
            if (!empty($this->donnees_additionnelles)) {
                $decoded = json_decode($this->donnees_additionnelles, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->donnees_additionnelles = $decoded;
                }
            }
            
            if (!empty($this->etapes_defaut)) {
                $decoded = json_decode($this->etapes_defaut, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->etapes_defaut = $decoded;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Update object into database
     *
     * @param  User $user      User that modifies
     * @param  bool $notrigger false=launch triggers after, true=disable triggers
     * @return int             <0 if KO, >0 if OK
     */
    public function update(User $user, $notrigger = false)
    {
        global $conf, $langs;
        
        $error = 0;
        
        // Verification
        if (empty($this->titre)) {
            $this->errors[] = $langs->trans('ErrorFieldRequired', $langs->transnoentities('Title'));
            $error++;
        }
        
        if ($error) {
            dol_syslog(__METHOD__ . ' ' . $this->errorsToString(), LOG_ERR);
            return -1;
        }
        
        // JSON encodage des données si nécessaire
        if (is_array($this->donnees_additionnelles)) {
            $this->donnees_additionnelles = json_encode($this->donnees_additionnelles);
        }
        
        if (is_array($this->etapes_defaut)) {
            $this->etapes_defaut = json_encode($this->etapes_defaut);
        }
        
        return $this->updateCommon($user, $notrigger);
    }
    
    /**
     * Delete object in database
     *
     * @param  User $user      User that deletes
     * @param  bool $notrigger false=launch triggers after, true=disable triggers
     * @return int             <0 if KO, >0 if OK
     */
    public function delete(User $user, $notrigger = false)
    {
        return $this->deleteCommon($user, $notrigger);
    }
    
    /**
     * Mark suggestion as shown to the client
     * 
     * @param  User $user      User that updates
     * @return int             <0 if KO, >0 if OK
     */
    public function marquerCommeAffiche(User $user)
    {
        if ($this->statut != self::STATUS_NEW) {
            $this->error = "La suggestion n'est pas dans l'état approprié";
            return -1;
        }
        
        $this->statut = self::STATUS_SHOWN;
        
        return $this->update($user);
    }
    
    /**
     * Mark suggestion as accepted
     * 
     * @param  User $user      User that updates
     * @return int             <0 if KO, >0 if OK
     */
    public function accepter(User $user)
    {
        if ($this->statut != self::STATUS_NEW && $this->statut != self::STATUS_SHOWN) {
            $this->error = "La suggestion n'est pas dans l'état approprié";
            return -1;
        }
        
        $this->statut = self::STATUS_ACCEPTED;
        $this->est_traite = 1;
        
        return $this->update($user);
    }
    
    /**
     * Mark suggestion as refused
     * 
     * @param  User $user      User that updates
     * @param  string $motif   Reason for refusal
     * @return int             <0 if KO, >0 if OK
     */
    public function refuser(User $user, $motif = '')
    {
        if ($this->statut != self::STATUS_NEW && $this->statut != self::STATUS_SHOWN) {
            $this->error = "La suggestion n'est pas dans l'état approprié";
            return -1;
        }
        
        $this->statut = self::STATUS_REFUSED;
        $this->est_traite = 1;
        
        // Ajouter le motif aux données additionnelles
        if (!empty($motif)) {
            $donnees = $this->donnees_additionnelles;
            if (!is_array($donnees)) {
                $donnees = array();
            }
            $donnees['motif_refus'] = $motif;
            $this->donnees_additionnelles = $donnees;
        }
        
        return $this->update($user);
    }
    
    /**
     * Check if suggestions are expired and update their status
     * 
     * @param  User $user      User for updates
     * @return int             Number of updated suggestions
     */
    public static function verifierSuggestionsExpirees(User $user)
    {
        global $db;
        
        $now = dol_now();
        
        $suggestion = new ElaskaParticulierObjectifSuggestion($db);
        $suggestions = $suggestion->fetchAll('', '', 0, 0, array(
            'customsql' => "date_expiration < '".$db->idate($now)."' AND statut IN ('nouveau', 'affiche')"
        ));
        
        if (!is_array($suggestions)) {
            return 0;
        }
        
        $count = 0;
        foreach ($suggestions as $sugg) {
            $sugg->statut = self::STATUS_EXPIRED;
            $sugg->est_traite = 1;
            
            if ($sugg->update($user) > 0) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Generate suggestions for all clients
     * 
     * @param  User $user      User for creations
     * @param  array $options  Options for generation
     * @return int             Number of suggestions created
     */
    public static function genererSuggestionsTousClients(User $user, $options = array())
    {
        global $db, $conf;
        
        // Charger le gestionnaire de suggestions
        dol_include_once('/elaska/class/elaska_particulier_objectif_suggestion_manager.class.php');
        $manager = new ElaskaParticulierObjectifSuggestionManager($db);
        
        // Récupérer tous les particuliers
        dol_include_once('/elaska/class/elaska_particulier.class.php');
        $particulier = new ElaskaParticulier($db);
        $particuliers = $particulier->fetchAll();
        
        if (!is_array($particuliers)) {
            return 0;
        }
        
        // Options par défaut
        $options = array_merge(array(
            'max_par_client' => 3,
            'score_min' => 40,
            'categories' => array()
        ), $options);
        
        $count = 0;
        foreach ($particuliers as $part) {
            // Générer les suggestions pour ce particulier
            $suggestions = $manager->genererSuggestions($part->id, array(
                'max_suggestions' => $options['max_par_client'],
                'categories' => $options['categories']
            ));
            
            // Créer les suggestions qui ont un score suffisant
            foreach ($suggestions as $sugg) {
                if ($sugg['score'] < $options['score_min']) {
                    continue;
                }
                
                $suggestion = new ElaskaParticulierObjectifSuggestion($db);
                $suggestion->fk_particulier = $part->id;
                $suggestion->titre = $sugg['titre'];
                $suggestion->description = $sugg['description'];
                $suggestion->categorie = $sugg['categorie'];
                $suggestion->duree_estimee = $sugg['duree_estimee'];
                $suggestion->score = $sugg['score'];
                $suggestion->source = 'automatique';
                $suggestion->etapes_defaut = $sugg['etapes_defaut'];
                
                // Données additionnelles
                $suggestion->donnees_additionnelles = array(
                    'score_details' => isset($sugg['score_details']) ? $sugg['score_details'] : array(),
                    'declencheurs' => isset($sugg['declencheurs']) ? $sugg['declencheurs'] : array()
                );
                
                if ($suggestion->create($user) > 0) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Create a manual suggestion for a client
     * 
     * @param  int    $particulier_id Client ID
     * @param  array  $donnees        Suggestion data
     * @param  User   $user           User that creates
     * @return int                    <0 if KO, ID if OK
     */
    public static function creerSuggestionManuelle($particulier_id, $donnees, User $user)
    {
        global $db, $langs;
        
        if (empty($particulier_id) || empty($donnees['titre'])) {
            return -1;
        }
        
        // Vérifier que le particulier existe
        dol_include_once('/elaska/class/elaska_particulier.class.php');
        $particulier = new ElaskaParticulier($db);
        $result = $particulier->fetch($particulier_id);
        
        if ($result <= 0) {
            return -2;
        }
        
        $suggestion = new ElaskaParticulierObjectifSuggestion($db);
        $suggestion->fk_particulier = $particulier_id;
        $suggestion->titre = $donnees['titre'];
        $suggestion->description = $donnees['description'] ?? '';
        $suggestion->categorie = $donnees['categorie'] ?? '';
        $suggestion->duree_estimee = $donnees['duree_estimee'] ?? 3;
        $suggestion->budget_estime = $donnees['budget_estime'] ?? 0;
        $suggestion->source = 'manuel';
        
        // Étapes par défaut
        if (!empty($donnees['etapes'])) {
            $suggestion->etapes_defaut = $donnees['etapes'];
        }
        
        // Score arbitraire pour les suggestions manuelles
        $suggestion->score = 80;
        
        return $suggestion->create($user);
    }
    
    /**
     * Get status label
     * 
     * @param  int    $mode       0=long label, 1=short label, 2=css class
     * @return string             Status label
     */
    public function getStatutLabel($mode = 0)
    {
        global $langs;
        
        $langs->load('elaska@elaska');
        
        $statusLongLabels = array(
            self::STATUS_NEW => $langs->trans('StatusNew'),
            self::STATUS_SHOWN => $langs->trans('StatusShown'),
            self::STATUS_ACCEPTED => $langs->trans('StatusAccepted'),
            self::STATUS_REFUSED => $langs->trans('StatusRefused'),
            self::STATUS_EXPIRED => $langs->trans('StatusExpired')
        );
        
        $statusShortLabels = array(
            self::STATUS_NEW => $langs->trans('StatusNewShort'),
            self::STATUS_SHOWN => $langs->trans('StatusShownShort'),
            self::STATUS_ACCEPTED => $langs->trans('StatusAcceptedShort'),
            self::STATUS_REFUSED => $langs->trans('StatusRefusedShort'),
            self::STATUS_EXPIRED => $langs->trans('StatusExpiredShort')
        );
        
        $statusCssClasses = array(
            self::STATUS_NEW => 'status0',
            self::STATUS_SHOWN => 'status1',
            self::STATUS_ACCEPTED => 'status4',
            self::STATUS_REFUSED => 'status9',
            self::STATUS_EXPIRED => 'status8'
        );
        
        if ($mode === 0) {
            return $statusLongLabels[$this->statut] ?? $langs->trans('Unknown');
        } elseif ($mode === 1) {
            return $statusShortLabels[$this->statut] ?? $langs->trans('Unknown');
        } elseif ($mode === 2) {
            return $statusCssClasses[$this->statut] ?? '';
        }
        
        return '';
    }
    
    /**
     * Convert object to string
     * 
     * @return string String representation
     */
    public function __toString()
    {
        return $this->titre;
    }
}
