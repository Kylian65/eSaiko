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

/**
 * Classe de liaison entre objectifs et démarches administratives
 * 
 * @package    Elaska
 * @subpackage Particuliers
 */
class ElaskaParticulierObjectifDemarche extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */
    public $element = 'elaska_particulier_objectif_demarche';
    
    /**
     * @var string Name of table without prefix where object is stored
     */
    public $table_element = 'elaska_particulier_objectif_demarche';
    
    /**
     * @var int    ID
     */
    public $id;
    
    /**
     * @var int    Objective ID
     */
    public $fk_objectif;
    
    /**
     * @var int    Administrative procedure ID
     */
    public $fk_demarche;
    
    /**
     * @var string Creation date
     */
    public $date_creation;
    
    /**
     * @var int    Creator user ID
     */
    public $fk_user_creat;
    
    /**
     * @var string Comments
     */
    public $commentaire;
    
    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->date_creation = dol_now();
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
        if (empty($this->fk_objectif) || empty($this->fk_demarche)) {
            $this->errors[] = $langs->trans('ErrorFieldRequired', $langs->transnoentities('ObjectifDemarche'));
            $error++;
        }
        
        if ($error) {
            dol_syslog(__METHOD__ . ' ' . $this->errorsToString(), LOG_ERR);
            return -1;
        }
        
        $this->fk_user_creat = $user->id;
        
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
        return $this->fetchCommon($id, $ref);
    }
    
    /**
     * Load object from database based on objectif and demarche IDs
     *
     * @param int $objectif_id  Objective ID
     * @param int $demarche_id  Administrative procedure ID
     * @return int              <0 if KO, 0 if not found, >0 if OK
     */
    public function fetchByObjectifDemarche($objectif_id, $demarche_id)
    {
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE fk_objectif = ".(int) $objectif_id;
        $sql.= " AND fk_demarche = ".(int) $demarche_id;
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = "Error ".$this->db->lasterror();
            dol_syslog(__METHOD__." ".$this->error, LOG_ERR);
            return -1;
        }
        
        if ($this->db->num_rows($resql) == 0) {
            return 0;
        }
        
        $obj = $this->db->fetch_object($resql);
        
        return $this->fetch($obj->rowid);
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
     * Delete all links for a given objective
     *
     * @param  int  $objectif_id Objective ID
     * @param  User $user        User that deletes
     * @return int               <0 if KO, >0 if OK
     */
    public function deleteByObjectif($objectif_id, User $user)
    {
        $sql = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE fk_objectif = ".(int) $objectif_id;
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = "Error ".$this->db->lasterror();
            dol_syslog(__METHOD__." ".$this->error, LOG_ERR);
            return -1;
        }
        
        return 1;
    }
    
    /**
     * Delete all links for a given administrative procedure
     *
     * @param  int  $demarche_id Administrative procedure ID
     * @param  User $user        User that deletes
     * @return int               <0 if KO, >0 if OK
     */
    public function deleteByDemarche($demarche_id, User $user)
    {
        $sql = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE fk_demarche = ".(int) $demarche_id;
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = "Error ".$this->db->lasterror();
            dol_syslog(__METHOD__." ".$this->error, LOG_ERR);
            return -1;
        }
        
        return 1;
    }
    
    /**
     * Update comments
     *
     * @param  string $commentaire New comment
     * @param  User   $user        User that updates
     * @return int                 <0 if KO, >0 if OK
     */
    public function updateCommentaire($commentaire, User $user)
    {
        $this->commentaire = $commentaire;
        
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " SET commentaire = '".$this->db->escape($commentaire)."'";
        $sql.= " WHERE rowid = ".(int) $this->id;
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = "Error ".$this->db->lasterror();
            dol_syslog(__METHOD__." ".$this->error, LOG_ERR);
            return -1;
        }
        
        return 1;
    }
    
    /**
     * Get linked objectives for a given administrative procedure
     *
     * @param  int    $demarche_id Administrative procedure ID
     * @return array               Array of links
     */
    public function getByDemarche($demarche_id)
    {
        return $this->fetchAll('', '', 0, 0, array('customsql' => 'fk_demarche = '.(int) $demarche_id));
    }
    
    /**
     * Get linked administrative procedures for a given objective
     *
     * @param  int    $objectif_id Objective ID
     * @return array               Array of links
     */
    public function getByObjectif($objectif_id)
    {
        return $this->fetchAll('', '', 0, 0, array('customsql' => 'fk_objectif = '.(int) $objectif_id));
    }
}
