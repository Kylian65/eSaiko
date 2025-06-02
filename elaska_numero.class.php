<?php
/**
 * eLaska - Classe pour gérer la numérotation personnalisée des documents et objets
 * Date: 2025-05-30
 * Version: 3.0 (Intégration admin, historique et raffinements)
 * Auteur: Kylian65 / Contribution Github Copilot & IA
 */

if (!class_exists('ElaskaNumero', false)) {

class ElaskaNumero
{
    /** @var DoliDB Base de données Dolibarr */
    private $db;
    
    /** @var string Module concerné par la numérotation ('elaska', etc.) */
    public $module;
    
    /** @var string Type d'objet concerné ('elaska_dossier', 'elaska_document', etc.) */
    public $object_type;
    
    /** @var string Code du modèle de numérotation à utiliser (facultatif) */
    public $model_code;
    
    /** @var array Paramètres spécifiques pour la substitution dans le masque */
    public $params = array();
    
    /** @var string Message d'erreur en cas d'échec */
    public $error = '';
    
    /** @var int ID de l'objet pour lequel le numéro est généré (utile pour recordUsedNumber) */
    public $fk_object;

    /**
     * Constructeur
     *
     * @param DoliDB $db         Base de données
     * @param string $module     Module concerné
     * @param string $object_type Type d'objet
     * @param string $model_code  Code du modèle (facultatif)
     */
    public function __construct($db, $module, $object_type, $model_code = '')
    {
        $this->db = $db;
        $this->module = $module;
        $this->object_type = $object_type;
        $this->model_code = $model_code;
    }

    /**
     * Ajoute un paramètre pour la substitution dans le masque
     *
     * @param string $name  Nom du paramètre
     * @param mixed  $value Valeur du paramètre
     * @return ElaskaNumero $this Pour chaînage
     */
    public function addParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Ajoute plusieurs paramètres pour la substitution dans le masque
     *
     * @param array $params Tableau associatif des paramètres
     * @return ElaskaNumero $this Pour chaînage
     */
    public function addParams($params)
    {
        if (is_array($params)) {
            $this->params = array_merge($this->params, $params);
        }
        return $this;
    }

    /**
     * Définit l'ID de l'objet pour lequel le numéro est généré (pour l'historique)
     *
     * @param int $fk_object ID de l'objet eLaska
     * @return ElaskaNumero $this Pour chaînage
     */
    public function setFkObject($fk_object)
    {
        $this->fk_object = (int) $fk_object;
        return $this;
    }

    /**
     * Génère le prochain numéro selon le modèle actif ou utilise un fallback
     *
     * @param int|null $entity Entité Dolibarr (null = entité courante)
     * @return string|int      Numéro généré ou -1 si erreur
     */
    public function getNextNumber($entity = null)
    {
        global $conf, $user; // $user pour recordUsedNumber

        if (is_null($entity)) {
            $entity = $conf->entity;
        }

        $model_info = $this->getActiveModel($entity);
        $numero_genere = '';

        if (empty($model_info)) {
            dol_syslog("ElaskaNumero::getNextNumber: No active model found for module '".$this->module."', object_type '".$this->object_type."', model_code '".$this->model_code."'. Falling back to default numbering.", LOG_WARNING);
            $numero_genere = $this->generateDefaultNumber($entity);
        } else {
            $numero_genere = $this->generateNumberFromModel($model_info, $entity);
        }

        if (!empty($numero_genere) && $numero_genere !== -1 && !empty($this->fk_object)) {
            self::recordUsedNumber($this->db, $numero_genere, $this->module, $this->object_type, $this->fk_object, $entity);
        } elseif ($numero_genere === -1) { // Erreur de génération
            $this->error = "Numero generation failed from model, and fallback also failed or was not triggered.";
            dol_syslog("ElaskaNumero::getNextNumber: ".$this->error, LOG_CRIT);
            return -1; // Indiquer une erreur critique
        }

        return $numero_genere;
    }

    /**
     * Récupère le modèle de numérotation actif
     *
     * @param int $entity Entité Dolibarr
     * @return object|false Information sur le modèle ou false si non trouvé
     */
    private function getActiveModel($entity)
    {
        $sql = "SELECT *";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_numbering_models";
        $sql .= " WHERE module = '".$this->db->escape($this->module)."'";
        $sql .= " AND object_type = '".$this->db->escape($this->object_type)."'";
        $sql .= " AND entity = ".(int) $entity;
        $sql .= " AND active = 1";
        
        if (!empty($this->model_code)) {
            $sql .= " AND code = '".$this->db->escape($this->model_code)."'";
        }
        
        $sql .= " ORDER BY priority DESC, rowid ASC LIMIT 1";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($this->db->num_rows($resql) > 0) {
                $result = $this->db->fetch_object($resql);
                $this->db->free($resql);
                return $result;
            }
            $this->db->free($resql);
        } else {
            $this->error = "SQL Error in getActiveModel: ".$this->db->lasterror();
            dol_syslog("ElaskaNumero::getActiveModel: ".$this->error, LOG_ERR);
        }
        return false;
    }
    
    /**
     * Génère un numéro selon le modèle spécifié
     *
     * @param object $model_info Information sur le modèle
     * @param int    $entity     Entité Dolibarr
     * @return string|int        Numéro généré ou -1 si erreur
     */
    private function generateNumberFromModel($model_info, $entity)
    {
        $mask = $model_info->mask;
        $start_number = (int) $model_info->start_number;
        $padding_digits = (int) $model_info->padding_digits > 0 ? (int) $model_info->padding_digits : 4;
        
        // Obtenir toutes les substitutions standard et spécifiques
        $substitutions = $this->getStandardSubstitutions();
        
        // Ajouter les paramètres spécifiques fournis
        foreach ($this->params as $key => $value) {
            $substitutions['{'.$key.'}'] = $value;
        }
        
        // Appliquer les substitutions de base au masque
        $mask_substituted_vars = str_replace(array_keys($substitutions), array_values($substitutions), $mask);
        
        // Traitement du compteur {N+x} si présent dans le masque
        if (preg_match('/{N\+(\d+)?}/', $mask_substituted_vars, $matches)) {
            $counter_pattern = $matches[0];
            $increment_value = isset($matches[1]) && is_numeric($matches[1]) ? (int)$matches[1] : 1;
            
            // Isoler le préfixe du masque pour le compteur
            $mask_prefix_for_counter = str_replace($counter_pattern, '', $mask_substituted_vars);
            // Déterminer l'identifiant de période selon la fréquence de reset
            $period_identifier = $this->getPeriodIdentifierForFrequency($model_info->reset_frequency);

            $this->db->begin();
            
            // Obtenir la dernière valeur de compteur utilisée
            $last_counter = $this->getLastUsedCounter($model_info->rowid, $mask_prefix_for_counter, $period_identifier, $entity);
            $next_counter_value = max($last_counter + $increment_value, $start_number);
            
            // Sauvegarder la nouvelle valeur du compteur
            if ($this->saveUsedCounter($model_info->rowid, $mask_prefix_for_counter, $next_counter_value, $period_identifier, $entity)) {
                $this->db->commit();
                $padded_number = str_pad($next_counter_value, $padding_digits, '0', STR_PAD_LEFT);
                return str_replace($counter_pattern, $padded_number, $mask_substituted_vars);
            } else {
                $this->db->rollback();
                $this->error = "Failed to save new counter value for model.";
                dol_syslog("ElaskaNumero::generateNumberFromModel: ".$this->error, LOG_ERR);
                return -1; // Erreur spécifique
            }
        }
        
        // Si le masque ne contient pas de compteur {N+}, on retourne simplement le masque avec les substitutions
        return $mask_substituted_vars;
    }
    
    /**
     * Récupère la dernière valeur de compteur utilisée
     *
     * @param int    $model_id          ID du modèle
     * @param string $mask_prefix       Préfixe du masque
     * @param string $period_identifier Identifiant de période
     * @param int    $entity            Entité Dolibarr
     * @return int                      Dernière valeur de compteur ou 0 si aucune
     */
    private function getLastUsedCounter($model_id, $mask_prefix, $period_identifier, $entity)
    {
        $sql = "SELECT counter_value";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_numbering_counters";
        $sql .= " WHERE fk_model = ".(int) $model_id;
        $sql .= " AND mask_prefix = '".$this->db->escape($mask_prefix)."'";
        $sql .= " AND period_identifier = '".$this->db->escape($period_identifier)."'";
        $sql .= " AND entity = ".(int) $entity;
        // Pour la robustesse en cas de concurrence, on pourrait ajouter un FOR UPDATE ici si la DB le supporte bien
        // $sql .= " FOR UPDATE"; 

        $resql = $this->db->query($sql);
        if ($resql && $this->db->num_rows($resql) > 0) {
            $obj = $this->db->fetch_object($resql);
            $this->db->free($resql);
            return (int) $obj->counter_value;
        }
        if ($resql) {
            $this->db->free($resql);
        }
        return 0; 
    }
    
    /**
     * Sauvegarde la valeur du compteur utilisée
     *
     * @param int    $model_id          ID du modèle
     * @param string $mask_prefix       Préfixe du masque
     * @param int    $counter           Valeur du compteur
     * @param string $period_identifier Identifiant de période
     * @param int    $entity            Entité Dolibarr
     * @return bool                     True si succès, false sinon
     */
    private function saveUsedCounter($model_id, $mask_prefix, $counter, $period_identifier, $entity)
    {
        $now_sql = "'".$this->db->idate(dol_now())."'";

        $sql_check = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_numbering_counters";
        $sql_check .= " WHERE fk_model = ".(int) $model_id;
        $sql_check .= " AND mask_prefix = '".$this->db->escape($mask_prefix)."'";
        $sql_check .= " AND period_identifier = '".$this->db->escape($period_identifier)."'";
        $sql_check .= " AND entity = ".(int) $entity;
        
        $res_check = $this->db->query($sql_check);
        if ($res_check && $this->db->num_rows($res_check) > 0) {
            $obj_check = $this->db->fetch_object($res_check);
            $this->db->free($res_check);
            $sql = "UPDATE ".MAIN_DB_PREFIX."elaska_numbering_counters";
            $sql .= " SET counter_value = ".(int) $counter;
            // date_creation n'est pas mis à jour ici, c'est la date de création du compteur pour cette période.
            $sql .= " WHERE rowid = ".(int) $obj_check->rowid;
        } else {
            if ($res_check) {
                $this->db->free($res_check);
            }
            $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_numbering_counters";
            $sql .= " (fk_model, mask_prefix, counter_value, period_identifier, date_creation, entity)";
            $sql .= " VALUES (";
            $sql .= (int) $model_id.",";
            $sql .= "'".$this->db->escape($mask_prefix)."',";
            $sql .= (int) $counter.",";
            $sql .= "'".$this->db->escape($period_identifier)."',";
            $sql .= $now_sql.",";
            $sql .= (int) $entity;
            $sql .= ")";
        }
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = "Error saving counter: ".$this->db->lasterror();
            dol_syslog("ElaskaNumero::saveUsedCounter: SQL Error ".$this->error. " SQL: ".$sql, LOG_ERR);
            return false;
        }
        return true;
    }
    
    /**
     * Déterminer l'identifiant de période selon la fréquence de réinitialisation
     *
     * @param string $reset_freq Fréquence de réinitialisation
     * @return string            Identifiant de période
     */
    private function getPeriodIdentifierForFrequency($reset_freq)
    {
        $now = dol_now();
        switch (strtoupper($reset_freq)) {
            case 'YEARLY':
                return date('Y', $now);
            case 'MONTHLY':
                return date('Y-m', $now);
            case 'DAILY':
                return date('Y-m-d', $now);
            case 'NONE':
            default:
                return 'ALLTIME';
        }
    }
    
    /**
     * Obtient les substitutions standard pour le masque
     *
     * @return array Tableau des substitutions
     */
    private function getStandardSubstitutions()
    {
        $substitutions = array();
        $now = dol_now();
        $year = date('Y', $now);
        $month = date('m', $now);
        $day = date('d', $now);
        
        $substitutions['{yyyy}'] = $year;
        $substitutions['{yy}'] = substr($year, 2, 2);
        $substitutions['{MM}'] = $month;
        $substitutions['{mm}'] = $month; 
        $substitutions['{DD}'] = $day;
        $substitutions['{dd}'] = $day;   
        
        // Les substitutions spécifiques à l'objet (ex: {TYPE_DOSSIER_CODE})
        // sont gérées par $this->params
        return $substitutions;
    }
    
    /**
     * Génère un numéro par défaut si aucun modèle n'est trouvé.
     * Utilise la table llx_elaska_numero_history pour une séquence simple par prefix-yyyy-mm.
     *
     * @param int $entity Entité Dolibarr
     * @return string|int Numéro généré par défaut ou -1 en cas d'erreur grave
     */
    private function generateDefaultNumber($entity)
    {
        global $conf; // $user est global, utilisé dans recordUsedNumber appelé par getNextNumber
        $prefix = strtoupper(substr($this->object_type, 0, 3));
        $year = date('Y');
        $month = date('m');
        $start_number_default = 1;

        $this->db->begin();

        // Attention : cette requête pour le fallback est simplifiée et pourrait avoir des problèmes de concurrence.
        // Elle cherche le max dans l'historique complet pour ce format, pas dans une table de compteur dédiée au fallback.
        $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(numero, '-', -1), '-', 1) AS SIGNED)) as last_num";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_numero_history"; // Note: Utilise la table d'historique
        $sql .= " WHERE module = '".$this->db->escape($this->module)."'";
        $sql .= " AND object_type = '".$this->db->escape($this->object_type)."'";
        $sql .= " AND numero LIKE '".$this->db->escape($prefix)."-".$year.$month."-%'"; // Format Prefix-YYYYMM-NNNN
        $sql .= " AND entity = ".(int)$entity;

        $resql = $this->db->query($sql);
        $last_num = 0;
        if ($resql && ($obj = $this->db->fetch_object($resql)) && !is_null($obj->last_num)) {
            $last_num = (int) $obj->last_num;
            $this->db->free($resql);
        } elseif (!$resql) {
            $this->error = "Error fetching last default number from history: ".$this->db->lasterror();
            dol_syslog("ElaskaNumero::generateDefaultNumber: ".$this->error, LOG_ERR);
            $this->db->rollback();
            return -1; // Erreur grave
        } else {
            $this->db->free($resql);
        }
        
        $next_num = $last_num + $start_number_default;
        $formatted_num = $prefix.'-'.$year.$month.'-'.sprintf('%04d', $next_num);
        
        // Il n'est pas nécessaire de ré-enregistrer ici avec recordUsedNumber car
        // getNextNumber() s'en chargera si fk_object est défini.
        // Cependant, si cette méthode est appelée directement SANS passer par getNextNumber
        // ou si fk_object n'est pas encore connu, il faudrait un mécanisme.
        // Pour l'instant, on assume que getNextNumber est le point d'entrée principal.
        
        $this->db->commit(); // Commit ici car c'est une génération de fallback réussie
        return $formatted_num;
    }
    
    // --- MÉTHODES STATIQUES POUR L'ADMINISTRATION DES MODÈLES ---

    /**
     * Récupère les modèles disponibles pour un module et un type d'objet
     *
     * @param DoliDB $db         Base de données
     * @param string $module     Module concerné
     * @param string $object_type Type d'objet
     * @param int    $entity     Entité Dolibarr (null = entité courante)
     * @return array             Tableau d'objets modèles
     */
    public static function getAvailableModels($db, $module, $object_type, $entity = null)
    {
        global $conf;
        if (is_null($entity)) $entity = $conf->entity;
        $models = array();
        
        $sql = "SELECT rowid, code, label, mask, active, priority, ";
        $sql .= "reset_frequency, start_number, padding_digits, notes, ";
        $sql .= "date_creation, fk_user_creat, tms, fk_user_modif ";
        $sql .= "FROM ".MAIN_DB_PREFIX."elaska_numbering_models";
        $sql .= " WHERE module = '".$db->escape($module)."'";
        $sql .= " AND object_type = '".$db->escape($object_type)."'";
        $sql .= " AND entity = ".(int) $entity;
        $sql .= " ORDER BY priority DESC, label ASC";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                // On ne convertit pas les dates ici, le code appelant le fera si besoin (ex: dol_print_date)
                $models[] = $obj; 
            }
            $db->free($resql);
        } else {
            dol_syslog("ElaskaNumero::getAvailableModels Error ".$db->lasterror(), LOG_ERR);
        }
        return $models;
    }
    
    /**
     * Active ou désactive un modèle de numérotation
     *
     * @param DoliDB $db       Base de données
     * @param int    $model_id ID du modèle
     * @param bool   $active   État d'activation
     * @return bool            True si succès, false sinon
     */
    public static function setModelActive($db, $model_id, $active)
    {
        global $user;
        $now_sql = "'".$db->idate(dol_now())."'";
        
        $sql = "UPDATE ".MAIN_DB_PREFIX."elaska_numbering_models";
        $sql .= " SET active = ".($active ? 1 : 0);
        $sql .= ", tms = ".$now_sql;
        $sql .= ", fk_user_modif = ".(isset($user->id) ? (int) $user->id : "NULL");
        $sql .= " WHERE rowid = ".(int) $model_id;
        
        $resql = $db->query($sql);
        if (!$resql) {
            dol_syslog("ElaskaNumero::setModelActive Error ".$db->lasterror(), LOG_ERR);
            return false;
        }
        return true;
    }
    
    /**
     * Crée un nouveau modèle de numérotation
     *
     * @param DoliDB $db         Base de données
     * @param array  $model_data Données du modèle
     * @return int|bool          ID du modèle créé ou false si erreur
     */
    public static function createModel($db, $model_data)
    {
        global $conf, $user;
        
        if (empty($model_data['module']) || empty($model_data['object_type']) || 
            empty($model_data['code']) || empty($model_data['mask'])) {
            dol_syslog("ElaskaNumero::createModel Error: Missing required fields (module, object_type, code, mask)", LOG_ERR);
            return false;
        }
        
        $now_sql = "'".$db->idate(dol_now())."'";
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_numbering_models";
        $sql .= " (module, object_type, code, label, mask, start_number, padding_digits,";
        $sql .= " reset_frequency, active, priority, notes, entity, date_creation, fk_user_creat)";
        $sql .= " VALUES (";
        $sql .= "'".$db->escape($model_data['module'])."',";
        $sql .= "'".$db->escape($model_data['object_type'])."',";
        $sql .= "'".$db->escape($model_data['code'])."',";
        $sql .= "'".$db->escape(isset($model_data['label']) ? $model_data['label'] : $model_data['code'])."',";
        $sql .= "'".$db->escape($model_data['mask'])."',";
        $sql .= (isset($model_data['start_number']) && is_numeric($model_data['start_number']) ? (int) $model_data['start_number'] : 1).",";
        $sql .= (isset($model_data['padding_digits']) && is_numeric($model_data['padding_digits']) ? (int) $model_data['padding_digits'] : 4).",";
        $sql .= "'".$db->escape(isset($model_data['reset_frequency']) ? $model_data['reset_frequency'] : 'NONE')."',";
        $sql .= (isset($model_data['active']) ? (int) $model_data['active'] : 0).",";
        $sql .= (isset($model_data['priority']) && is_numeric($model_data['priority']) ? (int) $model_data['priority'] : 10).",";
        $sql .= "'".$db->escape(isset($model_data['notes']) ? $model_data['notes'] : '')."',";
        $sql .= (isset($model_data['entity']) ? (int) $model_data['entity'] : $conf->entity).",";
        $sql .= $now_sql.",";
        $sql .= (isset($user->id) ? (int) $user->id : "NULL");
        $sql .= ")";
        
        $resql = $db->query($sql);
        if ($resql) {
            return $db->last_insert_id(MAIN_DB_PREFIX."elaska_numbering_models");
        } else {
            dol_syslog("ElaskaNumero::createModel Error ".$db->lasterror()." SQL:".$sql, LOG_ERR);
            return false;
        }
    }
    
    /**
     * Met à jour un modèle de numérotation existant
     *
     * @param DoliDB $db          Base de données
     * @param int    $model_id    ID du modèle
     * @param array  $model_data  Données du modèle à mettre à jour
     * @return bool               True si succès, false sinon
     */
    public static function updateModel($db, $model_id, $model_data)
    {
        global $user;
        if (empty($model_id)) return false;

        $fields_to_update_sql = array();
        $now_sql = "'".$db->idate(dol_now())."'";
        
        if (isset($model_data['label'])) $fields_to_update_sql[] = "label = '".$db->escape($model_data['label'])."'";
        if (isset($model_data['mask'])) $fields_to_update_sql[] = "mask = '".$db->escape($model_data['mask'])."'";
        if (isset($model_data['start_number']) && is_numeric($model_data['start_number'])) $fields_to_update_sql[] = "start_number = ".(int) $model_data['start_number'];
        if (isset($model_data['padding_digits']) && is_numeric($model_data['padding_digits'])) $fields_to_update_sql[] = "padding_digits = ".(int) $model_data['padding_digits'];
        if (isset($model_data['reset_frequency'])) $fields_to_update_sql[] = "reset_frequency = '".$db->escape($model_data['reset_frequency'])."'";
        if (isset($model_data['active'])) $fields_to_update_sql[] = "active = ".(int) $model_data['active'];
        if (isset($model_data['priority']) && is_numeric($model_data['priority'])) $fields_to_update_sql[] = "priority = ".(int) $model_data['priority'];
        if (isset($model_data['notes'])) $fields_to_update_sql[] = "notes = '".$db->escape($model_data['notes'])."'";
        
        if (empty($fields_to_update_sql)) return true; // Rien à mettre à jour
        
        $fields_to_update_sql[] = "tms = ".$now_sql;
        $fields_to_update_sql[] = "fk_user_modif = ".(isset($user->id) ? (int) $user->id : "NULL");
        
        $sql = "UPDATE ".MAIN_DB_PREFIX."elaska_numbering_models SET ";
        $sql .= implode(', ', $fields_to_update_sql);
        $sql .= " WHERE rowid = ".(int) $model_id;
        
        $resql = $db->query($sql);
        if (!$resql) {
            dol_syslog("ElaskaNumero::updateModel Error ".$db->lasterror()." SQL:".$sql, LOG_ERR);
            return false;
        }
        return true;
    }
    
    /**
     * Supprime un modèle de numérotation et ses compteurs associés
     *
     * @param DoliDB $db       Base de données
     * @param int    $model_id ID du modèle
     * @return bool            True si succès, false sinon
     */
    public static function deleteModel($db, $model_id)
    {
        if (empty($model_id)) return false;
        $db->begin();
        
        $sql_check = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_numbering_models WHERE rowid = ".(int) $model_id;
        $res_check = $db->query($sql_check);
        if (!$res_check || $db->num_rows($res_check) == 0) {
            dol_syslog("ElaskaNumero::deleteModel Error: Model ID ".$model_id." not found", LOG_ERR);
            if ($res_check) {
                $db->free($res_check);
            }
            $db->rollback();
            return false;
        }
        $db->free($res_check);
        
        $sql_del_counters = "DELETE FROM ".MAIN_DB_PREFIX."elaska_numbering_counters WHERE fk_model = ".(int) $model_id;
        if (!$db->query($sql_del_counters)) {
            dol_syslog("ElaskaNumero::deleteModel Error deleting counters for model ID ".$model_id.": ".$db->lasterror(), LOG_ERR);
            $db->rollback();
            return false;
        }
        
        $sql_del_model = "DELETE FROM ".MAIN_DB_PREFIX."elaska_numbering_models WHERE rowid = ".(int) $model_id;
        if (!$db->query($sql_del_model)) {
            dol_syslog("ElaskaNumero::deleteModel Error deleting model ID ".$model_id.": ".$db->lasterror(), LOG_ERR);
            $db->rollback();
            return false;
        }
        
        $db->commit();
        return true;
    }

    /**
     * Enregistre un numéro utilisé dans l'historique
     *
     * @param DoliDB $db          Base de données
     * @param string $numero      Numéro attribué
     * @param string $module      Module concerné
     * @param string $object_type Type d'objet
     * @param int    $fk_object   ID de l'objet
     * @param int    $entity      Entité Dolibarr (null = entité courante)
     * @return bool               True si succès, false sinon
     */
    public static function recordUsedNumber($db, $numero, $module, $object_type, $fk_object, $entity = null)
    {
        global $conf, $user;
        if (is_null($entity)) $entity = $conf->entity;
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_numero_history";
        $sql .= " (numero, module, object_type, fk_object, date_attribution, fk_user, entity)";
        $sql .= " VALUES (";
        $sql .= "'".$db->escape($numero)."',";
        $sql .= "'".$db->escape($module)."',";
        $sql .= "'".$db->escape($object_type)."',";
        $sql .= (int) $fk_object.",";
        $sql .= "'".$db->idate(dol_now())."',";
        $sql .= (isset($user->id) ? (int) $user->id : "NULL").",";
        $sql .= (int) $entity;
        $sql .= ")";
        
        $resql = $db->query($sql);
        if (!$resql) {
            dol_syslog("ElaskaNumero::recordUsedNumber Error ".$db->lasterror()." SQL: ".$sql, LOG_ERR);
            return false;
        }
        return true;
    }
    
    /**
     * Vérifie si un numéro existe déjà dans l'historique
     *
     * @param DoliDB $db                Base de données
     * @param string $numero            Numéro à vérifier
     * @param string $module            Module concerné
     * @param string $object_type       Type d'objet
     * @param int    $fk_object_to_exclude ID de l'objet à exclure de la vérification (0 = aucun)
     * @param int    $entity            Entité Dolibarr (null = entité courante)
     * @return bool                     True si le numéro existe déjà, false sinon
     */
    public static function checkNumberExists($db, $numero, $module, $object_type, $fk_object_to_exclude = 0, $entity = null)
    {
        global $conf;
        if (is_null($entity)) $entity = $conf->entity;
        
        $sql = "SELECT COUNT(*) as nb FROM ".MAIN_DB_PREFIX."elaska_numero_history";
        $sql .= " WHERE numero = '".$db->escape($numero)."'";
        $sql .= " AND module = '".$db->escape($module)."'";
        $sql .= " AND object_type = '".$db->escape($object_type)."'";
        $sql .= " AND entity = ".(int) $entity;
        if ($fk_object_to_exclude > 0) {
            $sql .= " AND fk_object != ".(int) $fk_object_to_exclude;
        }
        
        $resql = $db->query($sql);
        if ($resql) {
            $obj = $db->fetch_object($resql);
            $db->free($resql);
            return ($obj->nb > 0);
        }
        dol_syslog("ElaskaNumero::checkNumberExists Error ".$db->lasterror(), LOG_ERR);
        return true; // Par précaution, considérer qu'il existe en cas d'erreur pour éviter les doublons.
    }
	
    /**
     * Méthode utilitaire pour générer et enregistrer en une seule étape un numéro pour un objet existant
     *
     * @param DoliDB $db         Base de données
     * @param string $module     Module concerné
     * @param string $object_type Type d'objet
     * @param int    $fk_object   ID de l'objet
     * @param string $model_code  Code du modèle (facultatif)
     * @param array  $params      Paramètres additionnels pour la génération
     * @return string|false      Numéro généré ou false si erreur
     */
    public static function generateAndRecord($db, $module, $object_type, $fk_object, $model_code = '', $params = array())
    {
        $numHelper = new self($db, $module, $object_type, $model_code);
        
        // Ajouter les paramètres supplémentaires
        if (!empty($params) && is_array($params)) {
            $numHelper->addParams($params);
        }
        
        // Générer le numéro et l'enregistrer automatiquement
        $numero = $numHelper->setFkObject($fk_object)->getNextNumber();
        
        if ($numero === -1 || empty($numero)) {
            dol_syslog("ElaskaNumero::generateAndRecord Error generating number for $module/$object_type/$fk_object", LOG_ERR);
            return false;
        }
        
        return $numero;
    }
}
}
?>