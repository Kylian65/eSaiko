<?php
/* Copyright (C) 2025 Kylian65
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
 *
 * Date dernière modification: 2025-05-29 // Fichier complet et final généré par l'IA
 * Par: Kylian65 // IA Assistance
 */

/**
 * Description and activation file for module eLaska
 */
include_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';

/**
 * Description and activation class for module eLaska
 */
class modElaska extends DolibarrModules
{
    /**
     * Constructor. Define names, constants, directories, boxes, permissions
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        global $langs, $conf;
        $this->db = $db;

        // Module ID
        $this->numero = 900000;
        // Module family
        $this->family = "crm";
        // Module position in the family
        $this->module_position = 500;
        // Module name
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        // Module description
        $this->description = "Module complet de gestion d'accompagnement administratif, financier et de conseil";
        // Keywords
        $this->keywords = array('crm', 'conseil', 'administratif', 'patrimoine', 'accompagnement', 'elaska');
        // Url
        $this->url = 'https://github.com/Kylian65/elaska';
        // Author information
        $this->editor_name = 'Kylian65';
        $this->editor_url = 'https://github.com/Kylian65';
        // Version
        $this->version = '1.0.6'; // Version incrémentée
        // Key used in llx_const table to save module status
        $this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
        // Name of image file used for this module
        $this->picto = 'elaska@elaska';
        // Dependencies
        $this->depends = array('modProjet', 'modAgenda', 'modSociete');
        // Data directories to create when module is enabled
        $this->dirs = array(
            '/elaska/temp',
            '/elaska/documents',
            '/elaska/documents/dossiers',
            '/elaska/documents/patrimoine',
            '/elaska/documents/clients',
            '/elaska/documents/kb',
            '/elaska/documents/templates',
        );
        // Config pages
        $this->config_page_url = array("setup.php@elaska");
        
        // Constants
        $this->const = array(
            0 => array('ELASKA_USE_CUSTOM_MENU', 'chaine', '1', 'Utiliser le menu personnalisé eLaska', 0, 'current', 1),
            1 => array('ELASKA_MENU_ICONS_ENABLED', 'chaine', '1', 'Activer les icônes dans le menu', 0, 'current', 1),
            2 => array('ELASKA_MENU_ROLE_BASED', 'chaine', '1', 'Activer menu basé sur les rôles', 0, 'current', 1),
            3 => array('ELASKA_REAL_TIME_DATA', 'chaine', '1', 'Afficher les données en temps réel', 0, 'current', 1),
            4 => array('ELASKA_DASHBOARD_REFRESH_RATE', 'chaine', '60', 'Taux de rafraîchissement du tableau de bord (secondes)', 0, 'current', 1),
        );

        // Dictionaries
        $this->dictionaries = array(
            'langs' => 'elaska@elaska',
            'tabname' => array(
                MAIN_DB_PREFIX . "c_elaska_dossier_type",             // 0
                MAIN_DB_PREFIX . "c_elaska_prestations",              // 1
                MAIN_DB_PREFIX . "c_elaska_timeline_etapes",          // 2
                MAIN_DB_PREFIX . "c_elaska_intervenant_type",         // 3
                MAIN_DB_PREFIX . "c_elaska_notification_type",        // 4
                MAIN_DB_PREFIX . "c_elaska_opportunity_status",       // 5
                MAIN_DB_PREFIX . "c_elaska_satisfaction_question",    // 6
                MAIN_DB_PREFIX . "c_elaska_situation_client",         // 7 ElaskaTiers
                MAIN_DB_PREFIX . "c_elaska_type_client",              // 8 ElaskaTiers
                MAIN_DB_PREFIX . "c_elaska_part_genre",               // 9 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_sit_fam",             // 10 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_reg_mat",             // 11 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_stat_pro",            // 12 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_reg_secu",            // 13 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_permis",              // 14 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_prot_jur",            // 15 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_hab_type",            // 16 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_hab_statut",          // 17 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_pref_comm",           // 18 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_reg_fis",             // 19 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_ret_stat",            // 20 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_profil_inv",          // 21 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_avers_risque",        // 22 Particulier
                MAIN_DB_PREFIX . "c_elaska_part_horiz_plac",          // 23 Particulier
                MAIN_DB_PREFIX . "c_elaska_asso_cat",                 // 24 Association
                MAIN_DB_PREFIX . "c_elaska_asso_rayon",               // 25 Association
                MAIN_DB_PREFIX . "c_elaska_asso_occ_loc",             // 26 Association
                MAIN_DB_PREFIX . "c_elaska_asso_reg_fis",             // 27 Association
                MAIN_DB_PREFIX . "c_elaska_asso_gest_paie",           // 28 Association
                MAIN_DB_PREFIX . "c_elaska_creat_profil",             // 29 Createur
                MAIN_DB_PREFIX . "c_elaska_creat_stat_act",           // 30 Createur
                MAIN_DB_PREFIX . "c_elaska_creat_phase",              // 31 Createur
                MAIN_DB_PREFIX . "c_elaska_creat_form_jur",           // 32 Createur
                MAIN_DB_PREFIX . "c_elaska_creat_innov_type",         // 33 Createur
                MAIN_DB_PREFIX . "c_elaska_creat_stat_proj",          // 34 Createur
                MAIN_DB_PREFIX . "c_elaska_entr_form_jur",            // 35 Entreprise
                MAIN_DB_PREFIX . "c_elaska_entr_reg_tva",             // 36 Entreprise
                MAIN_DB_PREFIX . "c_elaska_entr_loc_stat_occ",        // 37 Entreprise
                MAIN_DB_PREFIX . "c_elaska_interv_form_exer",         // 38 Intervenant
                MAIN_DB_PREFIX . "c_elaska_interv_mode_fact",         // 39 Intervenant
                MAIN_DB_PREFIX . "c_elaska_org_type",                 // 40 Organisme
                MAIN_DB_PREFIX . "c_elaska_org_echelon",              // 41 Organisme
                MAIN_DB_PREFIX . "c_elaska_org_nomen_compta",         // 42 Organisme
                MAIN_DB_PREFIX . "c_elaska_org_demat_niv",            // 43 Organisme
                MAIN_DB_PREFIX . "c_elaska_org_nat_jur",              // 44 Organisme
                MAIN_DB_PREFIX . "c_elaska_org_reg_fis"               // 45 Organisme
            ),
            'tablib' => array(
                "Types de dossier eLaska", "Prestations eLaska", "Types d'Étapes de timeline eLaska",
                "Types d'intervenants externes eLaska", "Types de notification eLaska",
                "Statuts d'opportunité eLaska", "Questions d'enquête de satisfaction eLaska",
                "Situations Client (eLaska)", "Types de Client (eLaska)",
                "Genres (Particuliers eLaska)", "Situations Familiales (Particuliers eLaska)",
                "Régimes Matrimoniaux (Particuliers eLaska)", "Statuts Professionnels (Particuliers eLaska)",
                "Régimes Sécurité Sociale (Particuliers eLaska)", "Types de Permis (Particuliers eLaska)",
                "Protections Juridiques (Particuliers eLaska)", "Types d'Habitation (Particuliers eLaska)",
                "Statuts Occupation Habitation (Particuliers eLaska)", "Préférences Communication (Particuliers eLaska)",
                "Régimes Fiscaux (Particuliers eLaska)", "Statuts Dossier Retraite (Particuliers eLaska)",
                "Profils Investisseur (Particuliers eLaska)", "Aversions au Risque (Particuliers eLaska)",
                "Horizons de Placement (Particuliers eLaska)",
                "Catégories d'Association (eLaska)", "Rayonnements d'Association (eLaska)",
                "Statuts Occupation Locaux (Associations eLaska)", "Régimes Fiscaux (Associations eLaska)",
                "Modes Gestion Paie (Associations eLaska)",
                "Profils de Créateur (eLaska)", "Statuts Actuels (Créateurs eLaska)",
                "Phases de Projet (Créateurs eLaska)", "Formes Juridiques Envisagées (Créateurs eLaska)",
                "Types d'Innovation (Créateurs eLaska)", "Statuts de Projet (Créateurs eLaska)",
                "Formes Juridiques (Entreprises eLaska)", "Régimes de TVA (Entreprises eLaska)",
                "Statuts Occupation Locaux (Entreprises eLaska)",
                "Formes d'Exercice (Intervenants eLaska)", "Modes de Facturation (Intervenants eLaska)",
                "Types d'Organisme (eLaska)", "Échelons Territoriaux (Organismes eLaska)",
                "Nomenclatures Comptables (Organismes eLaska)", "Niveaux de Dématérialisation (Organismes eLaska)",
                "Natures Juridiques (Organismes eLaska)", "Régimes Fiscaux (Organismes eLaska)"
            ),
            'tabsql' => array(
                'SELECT rowid, code, label, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_dossier_type', // 0
                'SELECT rowid, code, label, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_prestations', // 1
                'SELECT rowid, code, label, description, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_timeline_etapes', // 2
                'SELECT rowid, code, label, description, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_intervenant_type', // 3
                'SELECT rowid, code, label, description, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_notification_type', // 4
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_opportunity_status', // 5
                'SELECT rowid, code, label, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_satisfaction_question', // 6
                'SELECT rowid, code, label, description, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_situation_client', // 7
                'SELECT rowid, code, label, description, picto, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_type_client', // 8
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_genre', // 9
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_sit_fam', // 10
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_reg_mat', // 11
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_stat_pro', // 12
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_reg_secu', // 13
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_permis', // 14
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_prot_jur', // 15
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_hab_type', // 16
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_hab_statut', // 17
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_pref_comm', // 18
                'SELECT rowid, code, label, description, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_reg_fis', // 19
                'SELECT rowid, code, label, description, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_ret_stat', // 20
                'SELECT rowid, code, label, description, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_profil_inv', // 21
                'SELECT rowid, code, label, description, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_avers_risque', // 22
                'SELECT rowid, code, label, description, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_part_horiz_plac',  // 23
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_asso_cat',                 // 24
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_asso_rayon',               // 25
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_asso_occ_loc',             // 26
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_asso_reg_fis',             // 27
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_asso_gest_paie',           // 28
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_creat_profil',             // 29
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_creat_stat_act',           // 30
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_creat_phase',              // 31
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_creat_form_jur',           // 32
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_creat_innov_type',         // 33
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_creat_stat_proj',          // 34
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_entr_form_jur',            // 35
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_entr_reg_tva',             // 36
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_entr_loc_stat_occ',        // 37
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_interv_form_exer',         // 38
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_interv_mode_fact',         // 39
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_org_type',                 // 40
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_org_echelon',              // 41
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_org_nomen_compta',         // 42
                'SELECT rowid, code, label, description, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_org_demat_niv',// 43
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_org_nat_jur',              // 44
                'SELECT rowid, code, label, position, active FROM ' . MAIN_DB_PREFIX . 'c_elaska_org_reg_fis'               // 45
            ),
            'tabsqlsort' => array(
                'label ASC', 'label ASC', 'position ASC, label ASC', 'position ASC, label ASC', 'label ASC', 'position ASC, label ASC', 'label ASC',
                'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC',
                'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC',
                'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC',
                'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC',
                'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC',
                'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC',
                'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC',
                'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC',
                'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC', 'position ASC, label ASC',
                'position ASC, label ASC', 'position ASC, label ASC'
            ),
            'tabfield' => array( 
                'code,label', 'code,label', 'code,label,description,position', 'code,label,description,position', 
                'code,label,description', 'code,label,position', 'code,label',
                'code,label,description,position', 'code,label,description,picto,position', // ElaskaTiers
                // Particulier (15)
                'code,label,position', 'code,label,position', 'code,label,position', 'code,label,position', 'code,label,position',
                'code,label,position', 'code,label,position', 'code,label,position', 'code,label,position', 'code,label,position',
                'code,label,description,position', 'code,label,description,position', 'code,label,description,position',
                'code,label,description,position', 'code,label,description,position',
                // Association (5)
                'code,label,position', 'code,label,position', 'code,label,position', 'code,label,position', 'code,label,position',
                // Createur (6)
                'code,label,position', 'code,label,position', 'code,label,position', 'code,label,position', 'code,label,position',
                'code,label,position',
                // Entreprise (3)
                'code,label,position', 'code,label,position', 'code,label,position',
                // Intervenant (2)
                'code,label,position', 'code,label,position',
                // Organisme (6)
                'code,label,position', 'code,label,position', 'code,label,position', 'code,label,description,position',
                'code,label,position', 'code,label,position'
            ),
            'tabfieldvalue' => array( /* Initialisé dynamiquement ci-dessous */ ),
            'tabfieldinsert' => array( /* Initialisé dynamiquement ci-dessous */ ),
            'tabrowid' => array( /* Initialisé dynamiquement ci-dessous */ ),
            'tabcond' => array( /* Initialisé dynamiquement ci-dessous */ )
        );
        $numDictsTotal = count($this->dictionaries['tabname']);
        $this->dictionaries['tabfieldvalue'] = $this->dictionaries['tabfield']; 
        $this->dictionaries['tabfieldinsert'] = $this->dictionaries['tabfield']; 
        $this->dictionaries['tabrowid'] = array_fill(0, $numDictsTotal, 'rowid');
        $this->dictionaries['tabcond'] = array_fill(0, $numDictsTotal, '$conf->global->MAIN_MODULE_ELASKA');

        $this->boxes = array(
            0 => array('file' => 'box_elaska_dossiers.php@elaska', 'note' => 'Widget affichant les dossiers actifs', 'enabledbydefaulton' => 'Home'),
            1 => array('file' => 'box_elaska_tasks.php@elaska', 'note' => 'Widget affichant les tâches du jour', 'enabledbydefaulton' => 'Home'),
            2 => array('file' => 'box_elaska_rdv.php@elaska', 'note' => 'Widget affichant les prochains rendez-vous', 'enabledbydefaulton' => 'Home'),
            3 => array('file' => 'box_elaska_notifications.php@elaska', 'note' => 'Widget affichant les notifications non lues', 'enabledbydefaulton' => 'Home'),
            4 => array('file' => 'box_elaska_opportunities.php@elaska', 'note' => 'Widget affichant les opportunités en cours', 'enabledbydefaulton' => 'Home')
        );

        $this->rights = array();
        $this->rights_class = 'elaska';
        $r = 0;
        $this->rights[$r][0] = 9000; $this->rights[$r][1] = 'Accès au module eLaska'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'lire'; $this->rights[$r][5] = ''; $r++;
        $this->rights[$r][0] = 9001; $this->rights[$r][1] = 'Tableau de bord eLaska'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'dashboard'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9002; $this->rights[$r][1] = 'Consulter prospection & opportunités'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'prospection'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9003; $this->rights[$r][1] = 'Créer/modifier prospection & opportunités'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'prospection'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9004; $this->rights[$r][1] = 'Consulter les tiers eLaska'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'tiers'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9005; $this->rights[$r][1] = 'Créer/modifier les tiers eLaska'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'tiers'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 90051; $this->rights[$r][1] = 'Supprimer les tiers eLaska'; $this->rights[$r][2] = 'd'; $this->rights[$r][3] = 0; $this->rights[$r][4] = 'tiers'; $this->rights[$r][5] = 'delete'; $r++;
        $this->rights[$r][0] = 9006; $this->rights[$r][1] = 'Consulter les prestations'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'prestations'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9007; $this->rights[$r][1] = 'Créer/modifier les prestations'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'prestations'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9008; $this->rights[$r][1] = 'Consulter les dossiers'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'dossiers'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9009; $this->rights[$r][1] = 'Créer/modifier les dossiers'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'dossiers'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9010; $this->rights[$r][1] = 'Consulter patrimoine & finance'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'patrimoine'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9011; $this->rights[$r][1] = 'Créer/modifier patrimoine & finance'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'patrimoine'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9012; $this->rights[$r][1] = 'Consulter accompagnement spécifique'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'accompagnement'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9013; $this->rights[$r][1] = 'Créer/modifier accompagnement spécifique'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'accompagnement'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9014; $this->rights[$r][1] = 'Consulter tâches & suivi'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'tasks'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9015; $this->rights[$r][1] = 'Créer/modifier tâches & suivi'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'tasks'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9016; $this->rights[$r][1] = 'Consulter abonnements clients'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'abonnements'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9017; $this->rights[$r][1] = 'Créer/modifier abonnements clients'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'abonnements'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9018; $this->rights[$r][1] = 'Consulter communications'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'communications'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9019; $this->rights[$r][1] = 'Créer/modifier communications'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'communications'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9020; $this->rights[$r][1] = 'Consulter base de connaissances'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'kb'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9021; $this->rights[$r][1] = 'Créer/modifier base de connaissances'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'kb'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9022; $this->rights[$r][1] = 'Consulter partenariats'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'partenariats'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9023; $this->rights[$r][1] = 'Créer/modifier partenariats'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'partenariats'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9024; $this->rights[$r][1] = 'Consulter satisfaction client'; $this->rights[$r][2] = 'r'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'satisfaction'; $this->rights[$r][5] = 'read'; $r++;
        $this->rights[$r][0] = 9025; $this->rights[$r][1] = 'Créer/modifier satisfaction client'; $this->rights[$r][2] = 'w'; $this->rights[$r][3] = 1; $this->rights[$r][4] = 'satisfaction'; $this->rights[$r][5] = 'write'; $r++;
        $this->rights[$r][0] = 9099; $this->rights[$r][1] = 'Administrer le module eLaska'; $this->rights[$r][2] = 'a'; $this->rights[$r][3] = 0; $this->rights[$r][4] = 'admin'; $this->rights[$r][5] = ''; $r++;

        // Menu principal (copié de votre fichier original)
        $this->menu = array();
        $r = 0;
        $this->menu[$r++] = array('fk_menu' => '', 'type' => 'top', 'titre' => 'eLaska', 'prefix' => '<i class="fas fa-chart-line"></i>', 'mainmenu' => 'elaska', 'leftmenu' => '', 'url' => '/custom/elaska/index.php', 'langs' => 'elaska@elaska', 'position' => 100, 'enabled' => '$conf->global->MAIN_MODULE_ELASKA', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Tableau de bord', 'prefix' => '<i class="fas fa-tachometer-alt pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'dashboard', 'url' => '/custom/elaska/index.php', 'langs' => 'elaska@elaska', 'position' => 101, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=dashboard', 'type' => 'left', 'titre' => 'Mes tâches du jour', 'mainmenu' => 'elaska', 'leftmenu' => 'dashboard_tasks_today', 'url' => '/custom/elaska/tasks/today.php', 'langs' => 'elaska@elaska', 'position' => 102, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=dashboard', 'type' => 'left', 'titre' => 'Alertes & notifications', 'mainmenu' => 'elaska', 'leftmenu' => 'dashboard_alerts', 'url' => '/custom/elaska/alerts/list.php', 'langs' => 'elaska@elaska', 'position' => 103, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=dashboard', 'type' => 'left', 'titre' => 'Tâches & suivi', 'mainmenu' => 'elaska', 'leftmenu' => 'dashboard_tasks_all', 'url' => '/custom/elaska/tasks/list.php', 'langs' => 'elaska@elaska', 'position' => 104, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Tiers', 'prefix' => '<i class="fas fa-users pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'tiers', 'url' => '/custom/elaska/tiers/list.php', 'langs' => 'elaska@elaska', 'position' => 110, 'enabled' => '1', 'perms' => '$user->rights->elaska->tiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=tiers', 'type' => 'left', 'titre' => 'Nouveau tiers', 'mainmenu' => 'elaska', 'leftmenu' => 'tiers_new', 'url' => '/custom/elaska/tiers/new.php', 'langs' => 'elaska@elaska', 'position' => 111, 'enabled' => '1', 'perms' => '$user->rights->elaska->tiers->write', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=tiers', 'type' => 'left', 'titre' => 'Particuliers', 'mainmenu' => 'elaska', 'leftmenu' => 'tiers_particuliers', 'url' => '/custom/elaska/tiers/list.php?type=PARTICULIER', 'langs' => 'elaska@elaska', 'position' => 112, 'enabled' => '1', 'perms' => '$user->rights->elaska->tiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=tiers', 'type' => 'left', 'titre' => 'Associations', 'mainmenu' => 'elaska', 'leftmenu' => 'tiers_associations', 'url' => '/custom/elaska/tiers/list.php?type=ASSOCIATION', 'langs' => 'elaska@elaska', 'position' => 113, 'enabled' => '1', 'perms' => '$user->rights->elaska->tiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=tiers', 'type' => 'left', 'titre' => 'Créateurs d\'entreprises', 'mainmenu' => 'elaska', 'leftmenu' => 'tiers_createurs', 'url' => '/custom/elaska/tiers/list.php?type=CREATEUR', 'langs' => 'elaska@elaska', 'position' => 114, 'enabled' => '1', 'perms' => '$user->rights->elaska->tiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=tiers', 'type' => 'left', 'titre' => 'TPE/PME', 'mainmenu' => 'elaska', 'leftmenu' => 'tiers_entreprises', 'url' => '/custom/elaska/tiers/list.php?type=ENTREPRISE', 'langs' => 'elaska@elaska', 'position' => 115, 'enabled' => '1', 'perms' => '$user->rights->elaska->tiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=tiers', 'type' => 'left', 'titre' => 'Intervenants externes', 'mainmenu' => 'elaska', 'leftmenu' => 'tiers_intervenants', 'url' => '/custom/elaska/tiers/list.php?type=INTERVENANT', 'langs' => 'elaska@elaska', 'position' => 116, 'enabled' => '1', 'perms' => '$user->rights->elaska->tiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=tiers', 'type' => 'left', 'titre' => 'Organismes locaux', 'mainmenu' => 'elaska', 'leftmenu' => 'tiers_organismes', 'url' => '/custom/elaska/tiers/list.php?type=ORGANISME', 'langs' => 'elaska@elaska', 'position' => 117, 'enabled' => '1', 'perms' => '$user->rights->elaska->tiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Dossiers', 'prefix' => '<i class="fas fa-folder-open pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'dossiers', 'url' => '/custom/elaska/dossiers/list.php', 'langs' => 'elaska@elaska', 'position' => 120, 'enabled' => '1', 'perms' => '$user->rights->elaska->dossiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=dossiers', 'type' => 'left', 'titre' => 'Nouveau dossier', 'mainmenu' => 'elaska', 'leftmenu' => 'elaska_dossiers_new', 'url' => '/custom/elaska/dossiers/card.php?action=create', 'langs' => 'elaska@elaska', 'position' => 121, 'enabled' => '1', 'perms' => '$user->rights->elaska->dossiers->write', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=dossiers', 'type' => 'left', 'titre' => 'Tous les dossiers', 'mainmenu' => 'elaska', 'leftmenu' => 'elaska_dossiers_all', 'url' => '/custom/elaska/dossiers/list.php', 'langs' => 'elaska@elaska', 'position' => 122, 'enabled' => '1', 'perms' => '$user->rights->elaska->dossiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=dossiers', 'type' => 'left', 'titre' => 'Particuliers', 'mainmenu' => 'elaska', 'leftmenu' => 'elaska_dossiers_particuliers', 'url' => '/custom/elaska/dossiers/list.php?type=PARTICULAR', 'langs' => 'elaska@elaska', 'position' => 123, 'enabled' => '1', 'perms' => '$user->rights->elaska->dossiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=dossiers', 'type' => 'left', 'titre' => 'Associations', 'mainmenu' => 'elaska', 'leftmenu' => 'elaska_dossiers_associations', 'url' => '/custom/elaska/dossiers/list.php?type=ASSOCIATION', 'langs' => 'elaska@elaska', 'position' => 124, 'enabled' => '1', 'perms' => '$user->rights->elaska->dossiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=dossiers', 'type' => 'left', 'titre' => 'Créations d\'entreprises', 'mainmenu' => 'elaska', 'leftmenu' => 'elaska_dossiers_creations', 'url' => '/custom/elaska/dossiers/list.php?type=CREATION', 'langs' => 'elaska@elaska', 'position' => 125, 'enabled' => '1', 'perms' => '$user->rights->elaska->dossiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=dossiers', 'type' => 'left', 'titre' => 'TPE/PME', 'mainmenu' => 'elaska', 'leftmenu' => 'elaska_dossiers_tpe', 'url' => '/custom/elaska/dossiers/list.php?type=TPE_PME', 'langs' => 'elaska@elaska', 'position' => 126, 'enabled' => '1', 'perms' => '$user->rights->elaska->dossiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=dossiers', 'type' => 'left', 'titre' => 'Recouvrements', 'mainmenu' => 'elaska', 'leftmenu' => 'elaska_dossiers_recouvrement', 'url' => '/custom/elaska/dossiers/list.php?type=RECOVERY', 'langs' => 'elaska@elaska', 'position' => 127, 'enabled' => '1', 'perms' => '$user->rights->elaska->dossiers->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Communications', 'prefix' => '<i class="fas fa-comments pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'communications', 'url' => '/custom/elaska/communications/index.php', 'langs' => 'elaska@elaska', 'position' => 130, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=communications', 'type' => 'left', 'titre' => 'Nouvelle communication', 'mainmenu' => 'elaska', 'leftmenu' => 'communications_new', 'url' => '/custom/elaska/communications/card.php?action=create', 'langs' => 'elaska@elaska', 'position' => 131, 'enabled' => '1', 'perms' => '$user->rights->elaska->write', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=communications', 'type' => 'left', 'titre' => 'Liste des communications', 'mainmenu' => 'elaska', 'leftmenu' => 'communications_list', 'url' => '/custom/elaska/communications/list.php', 'langs' => 'elaska@elaska', 'position' => 132, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Prestations', 'prefix' => '<i class="fas fa-briefcase pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'prestations', 'url' => '/custom/elaska/prestations/index.php', 'langs' => 'elaska@elaska', 'position' => 140, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=prestations', 'type' => 'left', 'titre' => 'Nouvelle prestation', 'mainmenu' => 'elaska', 'leftmenu' => 'prestations_new', 'url' => '/custom/elaska/prestations/card.php?action=create', 'langs' => 'elaska@elaska', 'position' => 141, 'enabled' => '1', 'perms' => '$user->rights->elaska->write', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=prestations', 'type' => 'left', 'titre' => 'Liste des prestations', 'mainmenu' => 'elaska', 'leftmenu' => 'prestations_list', 'url' => '/custom/elaska/prestations/list.php', 'langs' => 'elaska@elaska', 'position' => 142, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Patrimoine & Finances', 'prefix' => '<i class="fas fa-landmark pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'patrimoine', 'url' => '/custom/elaska/patrimoine/index.php', 'langs' => 'elaska@elaska', 'position' => 150, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=patrimoine', 'type' => 'left', 'titre' => 'Nouveau dossier patrimonial', 'mainmenu' => 'elaska', 'leftmenu' => 'patrimoine_new', 'url' => '/custom/elaska/patrimoine/card.php?action=create', 'langs' => 'elaska@elaska', 'position' => 151, 'enabled' => '1', 'perms' => '$user->rights->elaska->write', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=patrimoine', 'type' => 'left', 'titre' => 'Dossiers patrimoniaux', 'mainmenu' => 'elaska', 'leftmenu' => 'patrimoine_list', 'url' => '/custom/elaska/patrimoine/list.php', 'langs' => 'elaska@elaska', 'position' => 152, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=patrimoine', 'type' => 'left', 'titre' => 'Simulateurs financiers', 'mainmenu' => 'elaska', 'leftmenu' => 'patrimoine_simulateurs', 'url' => '/custom/elaska/patrimoine/simulateurs.php', 'langs' => 'elaska@elaska', 'position' => 153, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Assurances', 'prefix' => '<i class="fas fa-shield-alt pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'assurances', 'url' => '/custom/elaska/assurances/index.php', 'langs' => 'elaska@elaska', 'position' => 154, 'enabled' => '$conf->elaska->enabled', 'perms' => '$user->rights->elaska->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=assurances', 'type' => 'left', 'titre' => 'Nouveau contrat', 'mainmenu' => 'elaska', 'leftmenu' => 'assurances_new', 'url' => '/custom/elaska/assurances/card.php?action=create', 'langs' => 'elaska@elaska', 'position' => 155, 'enabled' => '$conf->elaska->enabled', 'perms' => '$user->rights->elaska->write', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=assurances', 'type' => 'left', 'titre' => 'Liste des contrats', 'mainmenu' => 'elaska', 'leftmenu' => 'assurances_list', 'url' => '/custom/elaska/assurances/list.php', 'langs' => 'elaska@elaska', 'position' => 156, 'enabled' => '$conf->elaska->enabled', 'perms' => '$user->rights->elaska->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Sinistres', 'prefix' => '<i class="fas fa-exclamation-triangle pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'sinistres', 'url' => '/custom/elaska/sinistres/index.php', 'langs' => 'elaska@elaska', 'position' => 157, 'enabled' => '$conf->elaska->enabled', 'perms' => '$user->rights->elaska->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=sinistres', 'type' => 'left', 'titre' => 'Nouveau sinistre', 'mainmenu' => 'elaska', 'leftmenu' => 'sinistres_new', 'url' => '/custom/elaska/sinistres/card.php?action=create', 'langs' => 'elaska@elaska', 'position' => 158, 'enabled' => '$conf->elaska->enabled', 'perms' => '$user->rights->elaska->write', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=sinistres', 'type' => 'left', 'titre' => 'Liste des sinistres', 'mainmenu' => 'elaska', 'leftmenu' => 'sinistres_list', 'url' => '/custom/elaska/sinistres/list.php', 'langs' => 'elaska@elaska', 'position' => 159, 'enabled' => '$conf->elaska->enabled', 'perms' => '$user->rights->elaska->read', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Accompagnement spécifique', 'prefix' => '<i class="fas fa-handshake pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'accompagnement', 'url' => '/custom/elaska/accompagnement/index.php', 'langs' => 'elaska@elaska', 'position' => 160, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=accompagnement', 'type' => 'left', 'titre' => 'Nouvel accompagnement', 'mainmenu' => 'elaska', 'leftmenu' => 'accompagnement_new', 'url' => '/custom/elaska/accompagnement/card.php?action=create', 'langs' => 'elaska@elaska', 'position' => 161, 'enabled' => '1', 'perms' => '$user->rights->elaska->write', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=accompagnement', 'type' => 'left', 'titre' => 'Liste des accompagnements', 'mainmenu' => 'elaska', 'leftmenu' => 'accompagnement_list', 'url' => '/custom/elaska/accompagnement/list.php', 'langs' => 'elaska@elaska', 'position' => 162, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Gestion des partenariats', 'prefix' => '<i class="fas fa-handshake pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'partenariats', 'url' => '/custom/elaska/partenariats/index.php', 'langs' => 'elaska@elaska', 'position' => 170, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=partenariats', 'type' => 'left', 'titre' => 'Nouveau partenariat', 'mainmenu' => 'elaska', 'leftmenu' => 'partenariats_new', 'url' => '/custom/elaska/partenariats/card.php?action=create', 'langs' => 'elaska@elaska', 'position' => 171, 'enabled' => '1', 'perms' => '$user->rights->elaska->write', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=partenariats', 'type' => 'left', 'titre' => 'Liste des partenariats', 'mainmenu' => 'elaska', 'leftmenu' => 'partenariats_list', 'url' => '/custom/elaska/partenariats/list.php', 'langs' => 'elaska@elaska', 'position' => 172, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Abonnements clients', 'prefix' => '<i class="fas fa-sync-alt pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'abonnements', 'url' => '/custom/elaska/abonnements/index.php', 'langs' => 'elaska@elaska', 'position' => 180, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=abonnements', 'type' => 'left', 'titre' => 'Nouvel abonnement', 'mainmenu' => 'elaska', 'leftmenu' => 'abonnements_new', 'url' => '/custom/elaska/abonnements/card.php?action=create', 'langs' => 'elaska@elaska', 'position' => 181, 'enabled' => '1', 'perms' => '$user->rights->elaska->write', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=abonnements', 'type' => 'left', 'titre' => 'Liste des abonnements', 'mainmenu' => 'elaska', 'leftmenu' => 'abonnements_list', 'url' => '/custom/elaska/abonnements/list.php', 'langs' => 'elaska@elaska', 'position' => 182, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska,fk_leftmenu=abonnements', 'type' => 'left', 'titre' => 'Modèles d\'abonnements', 'mainmenu' => 'elaska', 'leftmenu' => 'abonnements_modeles', 'url' => '/custom/elaska/abonnements/modeles.php', 'langs' => 'elaska@elaska', 'position' => 183, 'enabled' => '1', 'perms' => '$user->rights->elaska->lire', 'target' => '', 'user' => 2 );
        $this->menu[$r++] = array('fk_menu' => 'fk_mainmenu=elaska', 'type' => 'left', 'titre' => 'Administration', 'prefix' => '<i class="fas fa-cogs pictofixedwidth"></i>', 'mainmenu' => 'elaska', 'leftmenu' => 'admin', 'url' => '/custom/elaska/admin/setup.php', 'langs' => 'elaska@elaska', 'position' => 190, 'enabled' => '1', 'perms' => '$user->rights->elaska->admin', 'target' => '', 'user' => 2 );
    }

    public function init($options = '')
    {
        global $conf, $langs;
        $sql_for_parent_init = array();

        $result_load_tables = $this->_load_tables('/elaska/sql/');
        if ($result_load_tables < 0) {
            $this->error = "ECHEC: Impossible de charger les tables du module depuis /elaska/sql/. Vérifiez les fichiers SQL et les permissions de la base de données. Code d'erreur: " . $result_load_tables;
            dol_syslog($this->error, LOG_ERR);
            return 0;
        }

        $result_parent_init = $this->_init($sql_for_parent_init, $options);
        if (! $result_parent_init) {
            $error_msg = isset($this->error) && !empty($this->error) ? $this->error : "Erreur inconnue lors de _init()";
            dol_syslog("ECHEC: La fonction _init() parente a échoué pour le module " . $this->name . ". Erreur: " . $error_msg, LOG_ERR);
            $this->error = $error_msg;
            return 0;
        }
        
        if (!empty($conf->global->MAIN_MODULE_ELASKA)) {
             $this->initDictionaries();
        } else {
            dol_syslog("AVERTISSEMENT: Module eLaska non considéré comme pleinement actif (MAIN_MODULE_ELASKA non défini), initialisation des dictionnaires ignorée.", LOG_WARNING);
        }
        
        $this->setUpPermissions();
        
        dolibarr_set_const($this->db, 'ELASKA_INSTALL_DATE', dol_now(), 'chaine', 0, '', $conf->entity);
        
        return 1;
    }
    
    private function initDictionaries() 
    {
        global $conf, $langs;
        
        // --- Dictionnaires de base du module (originaux) ---
        $types_dossier = array(
            array('code' => 'PARTICULAR', 'label' => 'DossierTypeParticulier', 'description' => 'DossierTypeDescParticulier', 'position' => 10),
            array('code' => 'ASSOCIATION', 'label' => 'DossierTypeAssociation', 'description' => 'DossierTypeDescAssociation', 'position' => 20),
            array('code' => 'CREATION', 'label' => 'DossierTypeCreationEnt', 'description' => 'DossierTypeDescCreationEnt', 'position' => 30),
            array('code' => 'TPE_PME', 'label' => 'DossierTypeTPEPME', 'description' => 'DossierTypeDescTPEPME', 'position' => 40),
            array('code' => 'RECOUVREMENT', 'label' => 'DossierTypeRecouvrement', 'description' => 'DossierTypeDescRecouvrement', 'position' => 50)
        );
        if ($this->isTableExists(MAIN_DB_PREFIX . 'c_elaska_dossier_type')) $this->insertDictionaryEntries('c_elaska_dossier_type', $types_dossier);
        
        $types_prestations = array(
             array('code' => 'CONSULT_ADMIN', 'label' => 'PrestationConseilAdministratif', 'description' => 'PrestationDescConseilAdministratif', 'position' => 10),
             array('code' => 'ACCOMP_PATRI', 'label' => 'PrestationAccompagnementPatrimoine', 'description' => 'PrestationDescAccompagnementPatrimoine', 'position' => 20)
        );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_prestations')) $this->insertDictionaryEntries('c_elaska_prestations', $types_prestations);

        $timeline_etapes_entries = array(
            array('code' => 'ETAPE_INIT', 'label' => 'TimelineEtapeInitiale', 'description' => 'Première étape générique d\'une timeline', 'position' => 10),
            array('code' => 'ETAPE_VALID', 'label' => 'TimelineEtapeValidation', 'description' => 'Étape de validation par le client ou un responsable', 'position' => 20),
            array('code' => 'ETAPE_RDV', 'label' => 'TimelineEtapeRDV', 'description' => 'Étape correspondant à un rendez-vous planifié', 'position' => 30)
        );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_timeline_etapes')) $this->insertDictionaryEntries('c_elaska_timeline_etapes', $timeline_etapes_entries);

        $types_intervenants = array(
            array('code' => 'AVOCAT', 'label' => 'IntervenantTypeAvocat', 'description' => 'Professionnel du droit (avocat, conseil juridique)', 'position' => 10),
            array('code' => 'EXPERT_COMPTABLE', 'label' => 'IntervenantTypeExpertComptable', 'description' => 'Professionnel de la comptabilité et de la finance', 'position' => 20),
            array('code' => 'ASSISTANTE_SOCIALE', 'label' => 'IntervenantTypeAssistanteSociale', 'description' => 'Professionnel de l\'action sociale et de l\'accompagnement', 'position' => 30),
            array('code' => 'NOTAIRE', 'label' => 'IntervenantTypeNotaire', 'description' => 'Officier public pour actes et contrats', 'position' => 40),
            array('code' => 'HUJISSIER', 'label' => 'IntervenantTypeHuissier', 'description' => 'Huissier de justice', 'position' => 50)
        );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_intervenant_type')) $this->insertDictionaryEntries('c_elaska_intervenant_type', $types_intervenants);
        
        $notification_type_entries = array(
            array('code' => 'EMAIL_RAPPEL', 'label' => 'NotificationTypeEmailRappel', 'description' => 'Notification par email pour un rappel d\'échéance ou de tâche.'),
            array('code' => 'PORTAIL_ALERTE', 'label' => 'NotificationTypePortailAlerte', 'description' => 'Alerte affichée sur le portail client concernant une information importante.'),
            array('code' => 'SMS_INFO', 'label' => 'NotificationTypeSMSInfo', 'description' => 'Notification par SMS pour une information brève.')
        );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_notification_type')) $this->insertDictionaryEntries('c_elaska_notification_type', $notification_type_entries);
        
        $opportunity_status_entries = array(
            array('code' => 'PISTE', 'label' => 'OpportunityStatusPiste', 'description' => 'Premier contact ou lead identifié.', 'position' => 10),
            array('code' => 'QUALIFICATION', 'label' => 'OpportunityStatusQualification', 'description' => 'Opportunité en cours de qualification (besoins, budget).', 'position' => 20),
            array('code' => 'PROPOSITION', 'label' => 'OpportunityStatusProposition', 'description' => 'Proposition commerciale envoyée.', 'position' => 30),
            array('code' => 'NEGOCIATION', 'label' => 'OpportunityStatusNegociation', 'description' => 'En phase de négociation.', 'position' => 35),
            array('code' => 'GAGNEE', 'label' => 'OpportunityStatusGagnee', 'description' => 'Opportunité remportée.', 'position' => 40),
            array('code' => 'PERDUE', 'label' => 'OpportunityStatusPerdue', 'description' => 'Opportunité perdue.', 'position' => 50),
            array('code' => 'ANNULEE', 'label' => 'OpportunityStatusAnnulee', 'description' => 'Opportunité annulée par le prospect/client.', 'position' => 60)
        );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_opportunity_status')) $this->insertDictionaryEntries('c_elaska_opportunity_status', $opportunity_status_entries);
        
        $satisfaction_questions_entries = array(
            array('code' => 'SATIS_GENERALE', 'label' => 'SatisfactionQuestionGenerale', 'description' => 'Quel est votre niveau de satisfaction globale concernant nos services ?'),
            array('code' => 'SATIS_ACCUEIL', 'label' => 'SatisfactionQuestionAccueil', 'description' => 'Comment évaluez-vous la qualité de notre accueil (téléphonique et physique) ?'),
            array('code' => 'SATIS_ECOUTE', 'label' => 'SatisfactionQuestionEcoute', 'description' => 'Avez-vous le sentiment d\'avoir été bien écouté et compris ?'),
            array('code' => 'SATIS_REACTIVITE', 'label' => 'SatisfactionQuestionReactivite', 'description' => 'Jugez-vous notre réactivité satisfaisante ?'),
            array('code' => 'SATIS_COMPETENCE', 'label' => 'SatisfactionQuestionCompetence', 'description' => 'Comment évaluez-vous la compétence de nos conseillers ?')
        );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_satisfaction_question')) $this->insertDictionaryEntries('c_elaska_satisfaction_question', $satisfaction_questions_entries);
        
        // --- Dictionnaires pour ElaskaTiers ---
        $situations_client = array(
            array('code' => 'PROSPECT_CHAUD', 'label' => 'SituationProspectChaud', 'description' => 'SituationDescProspectChaud', 'position' => 10),
            array('code' => 'PROSPECT_FROID', 'label' => 'SituationProspectFroid', 'description' => 'SituationDescProspectFroid', 'position' => 20),
            array('code' => 'CLIENT_STANDARD', 'label' => 'SituationClientStandard', 'description' => 'SituationDescClientStandard', 'position' => 30),
            array('code' => 'CLIENT_VIP', 'label' => 'SituationClientVIP', 'description' => 'SituationDescClientVIP', 'position' => 40),
            array('code' => 'CLIENT_CONTRAT_BRONZE', 'label' => 'SituationClientContratBronze', 'description' => 'SituationDescClientContratBronze', 'position' => 50),
            array('code' => 'CLIENT_CONTRAT_ARGENT', 'label' => 'SituationClientContratArgent', 'description' => 'SituationDescClientContratArgent', 'position' => 60),
            array('code' => 'CLIENT_CONTRAT_OR', 'label' => 'SituationClientContratOr', 'description' => 'SituationDescClientContratOr', 'position' => 70),
            array('code' => 'CLIENT_OCCASIONNEL', 'label' => 'SituationClientOccasionnel', 'description' => 'SituationDescClientOccasionnel', 'position' => 80),
            array('code' => 'CLIENT_INACTIF', 'label' => 'SituationClientInactif', 'description' => 'SituationDescClientInactif', 'position' => 90),
            array('code' => 'ANCIEN_CLIENT', 'label' => 'SituationAncienClient', 'description' => 'SituationDescAncienClient', 'position' => 100)
        );
        if ($this->isTableExists(MAIN_DB_PREFIX . 'c_elaska_situation_client')) $this->insertDictionaryEntries('c_elaska_situation_client', $situations_client);

        $types_client_elaska = array(
            array('code' => 'PARTICULIER', 'label' => 'TypeClientParticulier', 'picto' => 'fas fa-user', 'position' => 10, 'description' => 'TypeClientDescParticulier'),
            array('code' => 'ASSOCIATION', 'label' => 'TypeClientAssociation', 'picto' => 'fas fa-sitemap', 'position' => 20, 'description' => 'TypeClientDescAssociation'),
            array('code' => 'ENTREPRISE',  'label' => 'TypeClientEntreprise',  'picto' => 'fas fa-building', 'position' => 30, 'description' => 'TypeClientDescEntreprise'),
            array('code' => 'CREATEUR',    'label' => 'TypeClientCreateur',    'picto' => 'fas fa-lightbulb', 'position' => 40, 'description' => 'TypeClientDescCreateur'),
            array('code' => 'INTERVENANT', 'label' => 'TypeClientIntervenant', 'picto' => 'fas fa-handshake', 'position' => 50, 'description' => 'TypeClientDescIntervenant'),
            array('code' => 'ORGANISME',   'label' => 'TypeClientOrganisme',   'picto' => 'fas fa-university', 'position' => 60, 'description' => 'TypeClientDescOrganisme')
        );
        if ($this->isTableExists(MAIN_DB_PREFIX . 'c_elaska_type_client')) $this->insertDictionaryEntries('c_elaska_type_client', $types_client_elaska);
        
        // --- Dictionnaires pour ElaskaParticulier (15) ---
        $part_genres = array( array('code' => 'HOMME', 'label' => 'GenreHomme', 'position' => 10), array('code' => 'FEMME', 'label' => 'GenreFemme', 'position' => 20), array('code' => 'AUTRE_GENRE', 'label' => 'GenreAutre', 'position' => 30));
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_genre')) $this->insertDictionaryEntries('c_elaska_part_genre', $part_genres);
        
        $part_sit_fam = array( array('code' => 'CELIBATAIRE', 'label' => 'SituationFamCelibataire', 'position' => 10), array('code' => 'MARIE', 'label' => 'SituationFamMarie', 'position' => 20), array('code' => 'PACSE', 'label' => 'SituationFamPacse', 'position' => 30), array('code' => 'UNION_LIBRE', 'label' => 'SituationFamUnionLibre', 'position' => 40), array('code' => 'DIVORCE', 'label' => 'SituationFamDivorce', 'position' => 50), array('code' => 'SEPARE', 'label' => 'SituationFamSepare', 'position' => 60), array('code' => 'VEUF', 'label' => 'SituationFamVeuf', 'position' => 70) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_sit_fam')) $this->insertDictionaryEntries('c_elaska_part_sit_fam', $part_sit_fam);

        $part_reg_mat = array( array('code' => 'COMMUNAUTE_LEGALE', 'label' => 'RegimeMatCommunauteLegale', 'position' => 10), array('code' => 'COMMUNAUTE_UNIVERSELLE', 'label' => 'RegimeMatCommunauteUniverselle', 'position' => 20), array('code' => 'SEPARATION_BIENS', 'label' => 'RegimeMatSeparationBiens', 'position' => 30), array('code' => 'PARTICIPATION_ACQUETS', 'label' => 'RegimeMatParticipationAcquets', 'position' => 40) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_reg_mat')) $this->insertDictionaryEntries('c_elaska_part_reg_mat', $part_reg_mat);
        
        $part_stat_pro = array( array('code' => 'SALARIE_CDI', 'label' => 'StatutProSalarieCDI', 'position' => 10), array('code' => 'SALARIE_CDD', 'label' => 'StatutProSalarieCDD', 'position' => 20), array('code' => 'FONCTIONNAIRE', 'label' => 'StatutProFonctionnaire', 'position' => 30), array('code' => 'INDEPENDANT', 'label' => 'StatutProIndependant', 'position' => 40), array('code' => 'CHEF_ENTREPRISE', 'label' => 'StatutProChefEntreprise', 'position' => 50), array('code' => 'RETRAITE', 'label' => 'StatutProRetraite', 'position' => 60), array('code' => 'SANS_EMPLOI', 'label' => 'StatutProSansEmploi', 'position' => 70), array('code' => 'ETUDIANT', 'label' => 'StatutProEtudiant', 'position' => 80));
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_stat_pro')) $this->insertDictionaryEntries('c_elaska_part_stat_pro', $part_stat_pro);

        $part_reg_secu = array( array('code' => 'GENERAL', 'label' => 'RegimeSecuGeneral', 'position' => 10), array('code' => 'MSA', 'label' => 'RegimeSecuMSA', 'position' => 20), array('code' => 'SSI', 'label' => 'RegimeSecuSSI', 'position' => 30), array('code' => 'AUTRE_REG', 'label' => 'RegimeSecuAutre', 'position' => 40) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_reg_secu')) $this->insertDictionaryEntries('c_elaska_part_reg_secu', $part_reg_secu);

        $part_permis = array( array('code' => 'AUCUN', 'label' => 'PermisAucun', 'position' => 10), array('code' => 'AM_BSR', 'label' => 'PermisAMBSR', 'position' => 20), array('code' => 'A1', 'label' => 'PermisA1', 'position' => 30), array('code' => 'A2', 'label' => 'PermisA2', 'position' => 40), array('code' => 'A', 'label' => 'PermisA', 'position' => 50), array('code' => 'B1', 'label' => 'PermisB1', 'position' => 60), array('code' => 'B', 'label' => 'PermisB', 'position' => 70), array('code' => 'BE', 'label' => 'PermisBE', 'position' => 80) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_permis')) $this->insertDictionaryEntries('c_elaska_part_permis', $part_permis);
        
        $part_prot_jur = array( array('code' => 'AUCUNE', 'label' => 'ProtectionJuridiqueAucune', 'position' => 10), array('code' => 'SAUVEGARDE_JUSTICE', 'label' => 'ProtectionJuridiqueSauvegardeJustice', 'position' => 20), array('code' => 'CURATELLE_SIMPLE', 'label' => 'ProtectionJuridiqueCuratelleSimple', 'position' => 30), array('code' => 'CURATELLE_RENFORCEE', 'label' => 'ProtectionJuridiqueCuratelleRenforcee', 'position' => 40), array('code' => 'TUTELLE', 'label' => 'ProtectionJuridiqueTutelle', 'position' => 50), array('code' => 'HABILITATION_FAMILIALE', 'label' => 'ProtectionJuridiqueHabilitationFamiliale', 'position' => 60) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_prot_jur')) $this->insertDictionaryEntries('c_elaska_part_prot_jur', $part_prot_jur);

        $part_hab_type = array( array('code' => 'APPARTEMENT', 'label' => 'HabitationTypeAppartement', 'position' => 10), array('code' => 'MAISON', 'label' => 'HabitationTypeMaison', 'position' => 20), array('code' => 'STUDIO', 'label' => 'HabitationTypeStudio', 'position' => 30), array('code' => 'AUTRE_HAB_TYPE', 'label' => 'HabitationTypeAutre', 'position' => 40) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_hab_type')) $this->insertDictionaryEntries('c_elaska_part_hab_type', $part_hab_type);
        
        $part_hab_statut = array( array('code' => 'PROPRIETAIRE', 'label' => 'HabitationStatutProprietaire', 'position' => 10), array('code' => 'LOCATAIRE', 'label' => 'HabitationStatutLocataire', 'position' => 20), array('code' => 'HEBERGE_GRATUIT', 'label' => 'HabitationStatutHebergeGratuit', 'position' => 30), array('code' => 'HEBERGE_PAYANT', 'label' => 'HabitationStatutHebergePayant', 'position' => 40), array('code' => 'USUFRUITIER', 'label' => 'HabitationStatutUsufruitier', 'position' => 50) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_hab_statut')) $this->insertDictionaryEntries('c_elaska_part_hab_statut', $part_hab_statut);

        $part_pref_comm = array( array('code' => 'EMAIL', 'label' => 'CommunicationPreferenceEmail', 'position' => 10), array('code' => 'TELEPHONE', 'label' => 'CommunicationPreferenceTelephone', 'position' => 20), array('code' => 'COURRIER', 'label' => 'CommunicationPreferenceCourrier', 'position' => 30), array('code' => 'SMS', 'label' => 'CommunicationPreferenceSMS', 'position' => 40), array('code' => 'PORTAIL', 'label' => 'CommunicationPreferencePortail', 'position' => 50) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_pref_comm')) $this->insertDictionaryEntries('c_elaska_part_pref_comm', $part_pref_comm);

        $part_reg_fis = array( array('code' => 'IR_TS', 'label' => 'RegFisIRTraitementsSalaires', 'description' => 'Imposition sur le revenu, catégorie Traitements et Salaires', 'position' => 10), array('code' => 'IR_BIC', 'label' => 'RegFisIRBIC', 'description' => 'Impôt sur le Revenu - Bénéfices Industriels et Commerciaux', 'position' => 20), array('code' => 'IR_BNC', 'label' => 'RegFisIRBNC', 'description' => 'Impôt sur le Revenu - Bénéfices Non Commerciaux', 'position' => 30), array('code' => 'NON_IMPOSABLE', 'label' => 'RegFisNonImposable', 'description' => 'Non imposable à l\'impôt sur le revenu', 'position' => 40) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_reg_fis')) $this->insertDictionaryEntries('c_elaska_part_reg_fis', $part_reg_fis);

        $part_ret_stat = array( array('code' => 'NON_INITIE', 'label' => 'RetStatNonInitie', 'description' => 'Dossier retraite non encore démarré', 'position' => 10), array('code' => 'ETUDE_PREL', 'label' => 'RetStatEtudePreliminaire', 'description' => 'Étude préliminaire des droits à la retraite', 'position' => 20), array('code' => 'CONSTITUTION', 'label' => 'RetStatConstitutionDossier', 'description' => 'Constitution du dossier de demande de retraite', 'position' => 30), array('code' => 'CLOS_OK', 'label' => 'RetStatClosOK', 'description' => 'Dossier retraite liquidé et clos', 'position' => 40) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_ret_stat')) $this->insertDictionaryEntries('c_elaska_part_ret_stat', $part_ret_stat);

        $part_profil_inv = array( array('code' => 'PRUDENT', 'label' => 'ProfilInvPrudent', 'description' => 'Recherche la sécurité du capital avant tout.', 'position' => 10), array('code' => 'EQUILIBRE', 'label' => 'ProfilInvEquilibre', 'description' => 'Recherche un équilibre entre rendement et sécurité.', 'position' => 20), array('code' => 'DYNAMIQUE', 'label' => 'ProfilInvDynamique', 'description' => 'Recherche une performance élevée, accepte une prise de risque.', 'position' => 30) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_profil_inv')) $this->insertDictionaryEntries('c_elaska_part_profil_inv', $part_profil_inv);
        
        $part_avers_risque = array( array('code' => 'FAIBLE', 'label' => 'AversionRisqueFaible', 'description' => 'Tolérance au risque faible.', 'position' => 10), array('code' => 'MODEREE', 'label' => 'AversionRisqueModeree', 'description' => 'Tolérance au risque modérée.', 'position' => 20), array('code' => 'ELEVEE', 'label' => 'AversionRisqueElevee', 'description' => 'Forte tolérance au risque.', 'position' => 30) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_avers_risque')) $this->insertDictionaryEntries('c_elaska_part_avers_risque', $part_avers_risque);

        $part_horiz_plac = array( array('code' => 'COURT_TERME', 'label' => 'HorizonPlacCourtTerme', 'description' => 'Moins de 2 ans.', 'position' => 10), array('code' => 'MOYEN_TERME', 'label' => 'HorizonPlacMoyenTerme', 'description' => 'Entre 2 et 5 ans.', 'position' => 20), array('code' => 'LONG_TERME', 'label' => 'HorizonPlacLongTerme', 'description' => 'Plus de 5 ans.', 'position' => 30) );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_part_horiz_plac')) $this->insertDictionaryEntries('c_elaska_part_horiz_plac', $part_horiz_plac);

        // --- Dictionnaires pour ElaskaAssociation (5) ---
        $asso_categories = array( array('code' => 'CULTURELLE', 'label' => 'AssoCatCulturelle', 'position' => 10), array('code' => 'SPORTIVE', 'label' => 'AssoCatSportive', 'position' => 20), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_asso_cat')) $this->insertDictionaryEntries('c_elaska_asso_cat', $asso_categories);
        $asso_rayon = array( array('code' => 'LOCAL', 'label' => 'AssoRayonLocal', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_asso_rayon')) $this->insertDictionaryEntries('c_elaska_asso_rayon', $asso_rayon);
        $asso_occ_loc = array( array('code' => 'PROPRIETAIRE', 'label' => 'AssoOccLocProprietaire', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_asso_occ_loc')) $this->insertDictionaryEntries('c_elaska_asso_occ_loc', $asso_occ_loc);
        $asso_reg_fis = array( array('code' => 'NON_LUCRATIF', 'label' => 'AssoRegFisNonLucratif', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_asso_reg_fis')) $this->insertDictionaryEntries('c_elaska_asso_reg_fis', $asso_reg_fis);
        $asso_gest_paie = array( array('code' => 'INTERNE', 'label' => 'AssoGestPaieInterne', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_asso_gest_paie')) $this->insertDictionaryEntries('c_elaska_asso_gest_paie', $asso_gest_paie);

        // --- Dictionnaires pour ElaskaCreateur (6) ---
        $creat_profil = array( array('code' => 'ENTREPRENEUR', 'label' => 'CreatProfilEntrepreneur', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_creat_profil')) $this->insertDictionaryEntries('c_elaska_creat_profil', $creat_profil);
        $creat_stat_act = array( array('code' => 'SALARIE', 'label' => 'CreatStatActSalarie', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_creat_stat_act')) $this->insertDictionaryEntries('c_elaska_creat_stat_act', $creat_stat_act);
        $creat_phase = array( array('code' => 'IDEATION', 'label' => 'CreatPhaseIdeation', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_creat_phase')) $this->insertDictionaryEntries('c_elaska_creat_phase', $creat_phase);
        $creat_form_jur = array( array('code' => 'EI', 'label' => 'FormeJuridiqueEI', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_creat_form_jur')) $this->insertDictionaryEntries('c_elaska_creat_form_jur', $creat_form_jur);
        $creat_innov_type = array( array('code' => 'TECHNOLOGIQUE', 'label' => 'CreatInnovTechno', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_creat_innov_type')) $this->insertDictionaryEntries('c_elaska_creat_innov_type', $creat_innov_type);
        $creat_stat_proj = array( array('code' => 'EN_COURS', 'label' => 'CreatStatProjEnCours', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_creat_stat_proj')) $this->insertDictionaryEntries('c_elaska_creat_stat_proj', $creat_stat_proj);

        // --- Dictionnaires pour ElaskaEntreprise (3) ---
        $entr_form_jur = array( array('code' => 'EI', 'label' => 'EntrFormeJuridiqueEI', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_entr_form_jur')) $this->insertDictionaryEntries('c_elaska_entr_form_jur', $entr_form_jur);
        $entr_reg_tva = array( array('code' => 'REEL_NORMAL', 'label' => 'EntrRegimeTVAReelNormal', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_entr_reg_tva')) $this->insertDictionaryEntries('c_elaska_entr_reg_tva', $entr_reg_tva);
        $entr_loc_stat_occ = array( array('code' => 'PROPRIETAIRE', 'label' => 'EntrLocStatOccProprietaire', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_entr_loc_stat_occ')) $this->insertDictionaryEntries('c_elaska_entr_loc_stat_occ', $entr_loc_stat_occ);

        // --- Dictionnaires pour ElaskaIntervenant (2 nouveaux) ---
        $interv_form_exer = array( array('code' => 'INDEPENDANT', 'label' => 'IntervFormExerIndependant', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_interv_form_exer')) $this->insertDictionaryEntries('c_elaska_interv_form_exer', $interv_form_exer);
        $interv_mode_fact = array( array('code' => 'FORFAIT', 'label' => 'IntervModeFactForfait', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_interv_mode_fact')) $this->insertDictionaryEntries('c_elaska_interv_mode_fact', $interv_mode_fact);
        
        // --- Dictionnaires pour ElaskaOrganisme (6) ---
        $org_type = array( array('code' => 'COMMUNE', 'label' => 'OrgTypeCommune', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_org_type')) $this->insertDictionaryEntries('c_elaska_org_type', $org_type);
        $org_echelon = array( array('code' => 'COMMUNAL', 'label' => 'OrgEchelonCommunal', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_org_echelon')) $this->insertDictionaryEntries('c_elaska_org_echelon', $org_echelon);
        $org_nomen_compta = array( array('code' => 'M14', 'label' => 'OrgNomenComptaM14', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_org_nomen_compta')) $this->insertDictionaryEntries('c_elaska_org_nomen_compta', $org_nomen_compta);
        $org_demat_niv = array( array('code' => 'NIVEAU_0', 'label' => 'OrgDematNiv0', 'description' => 'OrgDematNiv0Desc', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_org_demat_niv')) $this->insertDictionaryEntries('c_elaska_org_demat_niv', $org_demat_niv);
        $org_nat_jur = array( array('code' => 'COLLEC_TERRIT', 'label' => 'OrgNatJurCollecTerrit', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_org_nat_jur')) $this->insertDictionaryEntries('c_elaska_org_nat_jur', $org_nat_jur);
        $org_reg_fis = array( array('code' => 'NON_FISCALISE', 'label' => 'OrgRegFisNonFiscalise', 'position' => 10), /* ... */ );
        if ($this->isTableExists(MAIN_DB_PREFIX.'c_elaska_org_reg_fis')) $this->insertDictionaryEntries('c_elaska_org_reg_fis', $org_reg_fis);
    }
    
    private function isTableExists($tableName)
    { 
        if (empty($this->db) || !is_object($this->db)) { dol_syslog("Erreur: DB non dispo dans isTableExists pour ".$tableName, LOG_ERR); return false; }
        $resql = $this->db->query("SHOW TABLES LIKE '".$this->db->escape($tableName)."'");
        if ($resql) { if ($this->db->num_rows($resql) > 0) { $this->db->free($resql); return true; } $this->db->free($resql); }
        else { dol_syslog("Erreur vérification table ".$tableName.": ".$this->db->error(), LOG_ERR); }
        return false;
    }

    private function insertDictionaryEntries($table, $entries)
    {
        global $conf; 
        $table_name = MAIN_DB_PREFIX . $table;
        foreach ($entries as $entry) {
            if (empty($entry['code']) || empty($entry['label'])) {
                dol_syslog("AVERTISSEMENT: Entrée dico invalide (code/label manquant) table " . $table_name . ". Entrée: " . var_export($entry, true), LOG_WARNING);
                continue;
            }
            $current_entity = isset($conf->entity) ? $conf->entity : 1;
            $sql_check = "SELECT count(*) as nb FROM " . $table_name . " WHERE code = '" . $this->db->escape($entry['code']) . "' AND entity = " . $current_entity;
            $res_check = $this->db->query($sql_check);
            if ($res_check) {
                $obj_check = $this->db->fetch_object($res_check);
                if ($obj_check && $obj_check->nb == 0) {
                    $cols = array(); $vals = array();
                    $processed_entry = $entry;
                    if (!isset($processed_entry['entity'])) $processed_entry['entity'] = $current_entity;
                    if (!isset($processed_entry['active'])) $processed_entry['active'] = 1; 
                    foreach ($processed_entry as $key => $value) {
                        $cols[] = $this->db->escape($key); 
                        if (is_int($value) || is_float($value) || is_bool($value)) { $vals[] = (int) $value; }
                        elseif (is_null($value)) { $vals[] = "NULL"; }
                        else { $vals[] = "'" . $this->db->escape((string) $value) . "'"; }
                    }
                    if (!empty($cols)) {
                        $sql_insert = "INSERT INTO " . $table_name . " (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ")";
                        if (!$this->db->query($sql_insert)) dol_syslog("ECHEC: Insert dico " . $table_name . ": code=" . $entry['code'] . ". Erreur: " . $this->db->error() . " SQL=" . $sql_insert, LOG_ERR);
                    } else { dol_syslog("AVERTISSEMENT: Pas de cols/vals dico " . $table_name . ": code=" . $entry['code'], LOG_WARNING); }
                }
                $this->db->free($res_check);
            } else { dol_syslog("ECHEC: Check dico " . $table_name . ": code=" . $entry['code'] . ". Erreur: " . $this->db->error(), LOG_ERR); }
        }
    }
    
    private function setUpPermissions()
    { 
        global $conf;
        $admin_user_id = 1;
        if (!empty($conf->global->MAIN_FEATURES_LEVEL)) {
            foreach ($this->rights as $right_def) {
                $permission_id = $right_def[0];
                $sql_check_perm = "SELECT rowid FROM ".MAIN_DB_PREFIX."user_rights WHERE fk_user = ".$admin_user_id." AND fk_id = ".$permission_id." AND entity = ".$conf->entity;
                $res_check_perm = $this->db->query($sql_check_perm);
                if ($res_check_perm) {
                    if ($this->db->num_rows($res_check_perm) == 0) {
                        $sql_grant = "INSERT INTO ".MAIN_DB_PREFIX."user_rights (fk_user, fk_id, entity) VALUES (".$admin_user_id.", ".$permission_id.", ".$conf->entity.")";
                        if (!$this->db->query($sql_grant)) {
                            dol_syslog("ECHEC: Impossible d'attribuer le droit ID ".$permission_id." à l'admin (ID ".$admin_user_id.") pour le module ".$this->name.". Erreur: ".$this->db->error(), LOG_ERR);
                        }
                    }
                    $this->db->free($res_check_perm);
                } else {
                    dol_syslog("ECHEC: Impossible de vérifier les droits pour l'admin (ID ".$admin_user_id.") pour le module ".$this->name." (droit ID ".$permission_id."). Erreur: ".$this->db->error(), LOG_ERR);
                }
            }
        }
    }

    public function remove($options = '')
    { 
        $sql = array();
        return $this->_remove($sql, $options);
    }
}
?>
