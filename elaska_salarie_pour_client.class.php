<?php
/**
 * eLaska - Classe pour gérer les salariés travaillant pour les clients entreprises
 * Date: 2025-05-30
 * Version: 1.1
 * Auteur: Kylian65 / IA Collaboration
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

if (!class_exists('ElaskaSalariePourClient', false)) {

class ElaskaSalariePourClient extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_salarie_pour_client';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_salarie_pour_client';
    
    /**
     * @var string Icône utilisée
     */
    public $picto = 'user@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var string Référence unique du salarié
     */
    public $ref;
    
    /**
     * @var int ID de l'entreprise cliente
     */
    public $fk_elaska_tiers;
    
    /**
     * @var int ID du dossier associé (optionnel)
     */
    public $fk_elaska_dossier;
    
    /**
     * @var int ID du mandat associé (optionnel)
     */
    public $fk_elaska_mandat;
    
    /**
     * @var int ID du contrat de service associé (optionnel)
     */
    public $fk_elaska_contrat_service;
    
    /**
     * @var string Code de civilité (M./Mme/etc.)
     */
    public $civilite_code;
    
    /**
     * @var string Nom du salarié
     */
    public $nom;
    
    /**
     * @var string Prénom du salarié
     */
    public $prenom;
    
    /**
     * @var string Fonction/Poste occupé
     */
    public $fonction;
    
    /**
     * @var string Email du salarié
     */
    public $email;
    
    /**
     * @var string Téléphone du salarié
     */
    public $telephone;
    
    /**
     * @var string Date de début de mission (YYYY-MM-DD)
     */
    public $date_debut_mission;
    
    /**
     * @var string Date de fin prévue (YYYY-MM-DD)
     */
    public $date_fin_prevue;
    
    /**
     * @var string Date de fin réelle (YYYY-MM-DD)
     */
    public $date_fin_reelle;
    
    /**
     * @var string Code du type de contrat
     */
    public $type_contrat_code;
    
    /**
     * @var string Code du temps de travail
     */
    public $temps_travail_code;
    
    /**
     * @var float Nombre d'heures de travail par semaine
     */
    public $temps_travail_heures;
    
    /**
     * @var float Salaire brut mensuel
     */
    public $salaire_brut_mensuel;
    
    /**
     * @var float Taux de facturation horaire au client
     */
    public $taux_facturation;
    
    /**
     * @var string Code du statut du salarié
     */
    public $statut_salarie_code;
    
    /**
     * @var string Code du niveau de compétence
     */
    public $niveau_competence_code;
    
    /**
     * @var string Matricule interne du salarié chez le client
     */
    public $matricule_interne_client;
    
    /**
     * @var string Notes confidentielles (visibles uniquement par eLaska)
     */
    public $notes_confidentielles;
    
    /**
     * @var string Commentaires visibles par le client
     */
    public $commentaires_publics;
    
    // Champs techniques standard
    public $rowid;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $entity;
    public $status;
    
    /**
     * @var array Tableau de compétences du salarié
     */
    public $competences = array();
    
    /**
     * @var array Tableau de documents du salarié
     */
    public $documents = array();
    
    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'ref' => array('type' => 'varchar(128)', 'label' => 'RefSalarie', 'enabled' => 1, 'position' => 5, 'notnull' => 1, 'visible' => 1, 'unique' => 1),
        'fk_elaska_tiers' => array('type' => 'integer:ElaskaTiers:custom/elaska/class/elaska_tiers.class.php', 'label' => 'EntrepriseCliente', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1),
        'fk_elaska_dossier' => array('type' => 'integer:ElaskaDossier:custom/elaska/class/elaska_dossier.class.php', 'label' => 'DossierAssocie', 'enabled' => 1, 'position' => 15, 'notnull' => 0, 'visible' => 1),
        'fk_elaska_mandat' => array('type' => 'integer:ElaskaMandat:custom/elaska/class/elaska_mandat.class.php', 'label' => 'MandatAssocie', 'enabled' => 1, 'position' => 20, 'notnull' => 0, 'visible' => 1),
        'fk_elaska_contrat_service' => array('type' => 'integer:ElaskaContratService:custom/elaska/class/elaska_contrat_service.class.php', 'label' => 'ContratServiceAssocie', 'enabled' => 1, 'position' => 25, 'notnull' => 0, 'visible' => 1),
        'civilite_code' => array('type' => 'varchar(6)', 'label' => 'Civilite', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'nom' => array('type' => 'varchar(128)', 'label' => 'Nom', 'enabled' => 1, 'position' => 35, 'notnull' => 1, 'visible' => 1),
        'prenom' => array('type' => 'varchar(128)', 'label' => 'Prenom', 'enabled' => 1, 'position' => 40, 'notnull' => 1, 'visible' => 1),
        'fonction' => array('type' => 'varchar(128)', 'label' => 'Fonction', 'enabled' => 1, 'position' => 45, 'notnull' => 0, 'visible' => 1),
        'email' => array('type' => 'varchar(128)', 'label' => 'Email', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'telephone' => array('type' => 'varchar(30)', 'label' => 'Telephone', 'enabled' => 1, 'position' => 55, 'notnull' => 0, 'visible' => 1),
        'date_debut_mission' => array('type' => 'date', 'label' => 'DateDebutMission', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'date_fin_prevue' => array('type' => 'date', 'label' => 'DateFinPrevue', 'enabled' => 1, 'position' => 65, 'notnull' => 0, 'visible' => 1),
        'date_fin_reelle' => array('type' => 'date', 'label' => 'DateFinReelle', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'type_contrat_code' => array('type' => 'varchar(30)', 'label' => 'TypeContrat', 'enabled' => 1, 'position' => 75, 'notnull' => 0, 'visible' => 1),
        'temps_travail_code' => array('type' => 'varchar(30)', 'label' => 'TempsTravail', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'temps_travail_heures' => array('type' => 'double(8,2)', 'label' => 'NbHeuresHebdo', 'enabled' => 1, 'position' => 85, 'notnull' => 0, 'visible' => 1),
        'salaire_brut_mensuel' => array('type' => 'double(24,8)', 'label' => 'SalaireBrutMensuel', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'taux_facturation' => array('type' => 'double(24,8)', 'label' => 'TauxFacturation', 'enabled' => 1, 'position' => 95, 'notnull' => 0, 'visible' => 1),
        'statut_salarie_code' => array('type' => 'varchar(30)', 'label' => 'StatutSalarie', 'enabled' => 1, 'position' => 100, 'notnull' => 1, 'visible' => 1, 'default' => 'PROJET'),
        'niveau_competence_code' => array('type' => 'varchar(30)', 'label' => 'NiveauCompetence', 'enabled' => 1, 'position' => 105, 'notnull' => 0, 'visible' => 1),
        'matricule_interne_client' => array('type' => 'varchar(50)', 'label' => 'MatriculeInterneClient', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'notes_confidentielles' => array('type' => 'text', 'label' => 'NotesConfidentielles', 'enabled' => 1, 'position' => 115, 'notnull' => 0, 'visible' => 0),
        'commentaires_publics' => array('type' => 'text', 'label' => 'CommentairesPublics', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 1, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => -2),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => 0, 'visible' => -2),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => -2),
        'status' => array('type' => 'integer', 'label' => 'Status', 'enabled' => 1, 'position' => 1002, 'notnull' => 1, 'visible' => -2, 'default' => '1'),
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'default' => 1, 'enabled' => 1, 'visible' => -2, 'notnull' => 1, 'position' => 1003),
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Initialisation des valeurs par défaut
        if (empty($this->statut_salarie_code)) $this->statut_salarie_code = 'PROJET';
        if (!isset($this->status)) $this->status = 1;
    }

    /**
     * Crée un salarié pour client dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Référence temporaire si non fournie
        if (empty($this->ref)) {
            $this->ref = 'tmp-salarie-'.dol_print_date(dol_now(), '%y%m%d-%H%M%S');
        }
        
        $result = $this->createCommon($user, $notrigger);
        
        // Si la création a réussi, générer la référence définitive avec ElaskaNumero
        if ($result > 0) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';
            
            $params = array();
            // Ajouter des paramètres spécifiques pour le masque
            if (!empty($this->fk_elaska_tiers)) {
                $params['ID_TIERS'] = $this->fk_elaska_tiers;
            }
            
            $final_ref = ElaskaNumero::generateAndRecord(
                $this->db,
                'elaska',
                $this->element,
                $this->id,
                '', // Utilise le modèle actif par défaut
                $params
            );
            
            if ($final_ref !== false && !empty($final_ref)) {
                $this->ref = $final_ref;
                $this->updateRef($user);
            } else {
                dol_syslog(get_class($this)."::create Error generating ElaskaNumero reference", LOG_ERR);
                // La création reste valide même si la référence n'est pas générée correctement
            }
            
            // Sauvegarder les compétences si définies
            if (!empty($this->competences) && is_array($this->competences)) {
                foreach ($this->competences as $competence) {
                    $this->addCompetence(
                        $competence['competence'],
                        $competence['niveau'],
                        isset($competence['annees_experience']) ? $competence['annees_experience'] : null,
                        $user
                    );
                }
            }
        }
        
        return $result;
    }

    /**
     * Charge un salarié pour client de la base de données
     *
     * @param int    $id    Id du salarié
     * @param string $ref   Référence du salarié
     * @return int          <0 si KO, >0 si OK
     */
    public function fetch($id, $ref = null)
    {
        $result = $this->fetchCommon($id, $ref);
        
        if ($result > 0) {
            // Charger les compétences associées
            $this->fetchCompetences();
            
            // Charger les documents associés
            $this->fetchDocuments();
        }
        
        return $result;
    }

    /**
     * Met à jour un salarié pour client dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        return $this->updateCommon($user, $notrigger);
    }

    /**
     * Met à jour uniquement la référence d'un salarié après génération via ElaskaNumero
     *
     * @param User $user Utilisateur qui effectue la mise à jour
     * @return int       <0 si erreur, >0 si OK
     */
    public function updateRef($user)
    {
        if (empty($this->id) || empty($this->ref)) return -1;
        
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
        $sql .= " SET ref = '".$this->db->escape($this->ref)."'";
        $sql .= " WHERE rowid = ".(int)$this->id;
        
        dol_syslog(get_class($this)."::updateRef", LOG_DEBUG);
        $resql = $this->db->query($sql);
        
        if ($resql) {
            return 1;
        } else {
            $this->error = $this->db->lasterror();
            dol_syslog(get_class($this)."::updateRef ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /**
     * Supprime un salarié pour client de la base de données
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        $this->db->begin();
        
        // Les compétences et documents sont supprimés automatiquement grâce à la contrainte CASCADE dans SQL
        
        // Supprimer les documents physiques (fichiers) liés à ce salarié
        $this->deleteAllFiles();
        
        // Suppression du salarié lui-même
        $result = $this->deleteCommon($user, $notrigger);
        if ($result < 0) {
            $this->db->rollback();
            return -1;
        }
        
        $this->db->commit();
        return 1;
    }

    /**
     * Charge les compétences associées au salarié
     * 
     * @return int <0 si KO, nombre de compétences si OK
     */
    public function fetchCompetences()
    {
        $this->competences = array();
        
        $sql = "SELECT rowid, competence, niveau, annees_experience, date_creation, fk_user_creat";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_salarie_client_competence";
        $sql .= " WHERE fk_salarie_pour_client = " . (int) $this->id;
        $sql .= " ORDER BY rowid ASC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            $num_rows = $this->db->num_rows($resql);
            while ($obj = $this->db->fetch_object($resql)) {
                $competence = array(
                    'rowid' => $obj->rowid,
                    'competence' => $obj->competence,
                    'niveau' => $obj->niveau,
                    'annees_experience' => $obj->annees_experience,
                    'date_creation' => $this->db->jdate($obj->date_creation),
                    'fk_user_creat' => $obj->fk_user_creat
                );
                $this->competences[] = $competence;
            }
            $this->db->free($resql);
            return $num_rows;
        } else {
            $this->error = $this->db->lasterror();
            return -1;
        }
    }
    
    /**
     * Ajoute une compétence au salarié
     * 
     * @param string $competence         Nom de la compétence
     * @param int    $niveau             Niveau de la compétence (1-10)
     * @param int    $annees_experience  Nombre d'années d'expérience (optionnel)
     * @param User   $user               Utilisateur qui effectue l'action
     * @return int                       <0 si erreur, ID de la compétence si OK
     */
    public function addCompetence($competence, $niveau, $annees_experience = null, $user)
    {
        if (empty($this->id)) return -1;
        
        // Valider les paramètres
        if (empty($competence)) {
            $this->error = "Le nom de la compétence est obligatoire";
            return -2;
        }
        
        // Assurer que le niveau est entre 1 et 10
        $niveau = max(1, min(10, (int) $niveau));
        
        $this->db->begin();
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_salarie_client_competence";
        $sql .= " (fk_salarie_pour_client, competence, niveau, annees_experience, date_creation, fk_user_creat, entity)";
        $sql .= " VALUES (";
        $sql .= (int) $this->id . ", ";
        $sql .= "'" . $this->db->escape($competence) . "', ";
        $sql .= (int) $niveau . ", ";
        $sql .= ($annees_experience !== null ? (int) $annees_experience : "NULL") . ", ";
        $sql .= "'" . $this->db->idate(dol_now()) . "', ";
        $sql .= (int) $user->id . ", ";
        $sql .= (int) $this->entity;
        $sql .= ")";
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            $this->db->rollback();
            return -3;
        }
        
        $competence_id = $this->db->last_insert_id(MAIN_DB_PREFIX."elaska_salarie_client_competence");
        
        $this->db->commit();
        
        // Recharger les compétences pour mettre à jour $this->competences
        $this->fetchCompetences();
        
        return $competence_id;
    }
    
    /**
     * Supprime une compétence du salarié
     * 
     * @param int  $competence_id ID de la compétence à supprimer
     * @return int                <0 si erreur, 1 si OK
     */
    public function deleteCompetence($competence_id)
    {
        if (empty($this->id) || empty($competence_id)) return -1;
        
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_salarie_client_competence";
        $sql .= " WHERE rowid = " . (int) $competence_id;
        $sql .= " AND fk_salarie_pour_client = " . (int) $this->id;
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            return -2;
        }
        
        // Recharger les compétences pour mettre à jour $this->competences
        $this->fetchCompetences();
        
        return 1;
    }

    /**
     * Charge les documents associés au salarié
     * 
     * @return int <0 si KO, nombre de documents si OK
     */
    public function fetchDocuments()
    {
        $this->documents = array();
        
        $sql = "SELECT rowid, type_document_code, titre, description, filename, filepath, filesize, date_document, date_upload, fk_user_upload";
        $sql .= " FROM ".MAIN_DB_PREFIX."elaska_salarie_client_document";
        $sql .= " WHERE fk_salarie_pour_client = " . (int) $this->id;
        $sql .= " ORDER BY date_document DESC, rowid DESC";
        
        $resql = $this->db->query($sql);
        if ($resql) {
            $num_rows = $this->db->num_rows($resql);
            while ($obj = $this->db->fetch_object($resql)) {
                $document = array(
                    'rowid' => $obj->rowid,
                    'type_document_code' => $obj->type_document_code,
                    'titre' => $obj->titre,
                    'description' => $obj->description,
                    'filename' => $obj->filename,
                    'filepath' => $obj->filepath,
                    'filesize' => $obj->filesize,
                    'date_document' => $this->db->jdate($obj->date_document),
                    'date_upload' => $this->db->jdate($obj->date_upload),
                    'fk_user_upload' => $obj->fk_user_upload
                );
                $this->documents[] = $document;
            }
            $this->db->free($resql);
            return $num_rows;
        } else {
            $this->error = $this->db->lasterror();
            return -1;
        }
    }
    
    /**
     * Ajoute un document au salarié
     * 
     * @param string $type_document_code Code du type de document
     * @param string $titre              Titre du document
     * @param string $description        Description du document (optionnel)
     * @param string $filename           Nom du fichier
     * @param string $filepath           Chemin du fichier
     * @param int    $filesize           Taille du fichier en octets
     * @param string $date_document      Date du document (YYYY-MM-DD)
     * @param User   $user               Utilisateur qui effectue l'action
     * @return int                       <0 si erreur, ID du document si OK
     */
    public function addDocument($type_document_code, $titre, $description, $filename, $filepath, $filesize, $date_document, $user)
    {
        if (empty($this->id)) return -1;
        
        // Valider les paramètres
        if (empty($type_document_code) || empty($titre) || empty($filename) || empty($filepath)) {
            $this->error = "Paramètres manquants pour l'ajout d'un document";
            return -2;
        }
        
        $this->db->begin();
        
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."elaska_salarie_client_document";
        $sql .= " (fk_salarie_pour_client, type_document_code, titre, description, filename, filepath, filesize, date_document, date_upload, fk_user_upload, entity)";
        $sql .= " VALUES (";
        $sql .= (int) $this->id . ", ";
        $sql .= "'" . $this->db->escape($type_document_code) . "', ";
        $sql .= "'" . $this->db->escape($titre) . "', ";
        $sql .= "'" . $this->db->escape($description) . "', ";
        $sql .= "'" . $this->db->escape($filename) . "', ";
        $sql .= "'" . $this->db->escape($filepath) . "', ";
        $sql .= (int) $filesize . ", ";
        $sql .= "'" . $this->db->idate(strtotime($date_document)) . "', ";
        $sql .= "'" . $this->db->idate(dol_now()) . "', ";
        $sql .= (int) $user->id . ", ";
        $sql .= (int) $this->entity;
        $sql .= ")";
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            $this->db->rollback();
            return -3;
        }
        
        $document_id = $this->db->last_insert_id(MAIN_DB_PREFIX."elaska_salarie_client_document");
        
        $this->db->commit();
        
        // Recharger les documents pour mettre à jour $this->documents
        $this->fetchDocuments();
        
        return $document_id;
    }
    
    /**
     * Supprime un document du salarié
     * 
     * @param int  $document_id ID du document à supprimer
     * @param bool $delete_file Supprimer également le fichier physique
     * @return int              <0 si erreur, 1 si OK
     */
    public function deleteDocument($document_id, $delete_file = true)
    {
        if (empty($this->id) || empty($document_id)) return -1;
        
        // Récupérer les informations du document pour supprimer le fichier
        $document_info = null;
        if ($delete_file) {
            $sql = "SELECT filepath, filename FROM ".MAIN_DB_PREFIX."elaska_salarie_client_document";
            $sql .= " WHERE rowid = " . (int) $document_id;
            $sql .= " AND fk_salarie_pour_client = " . (int) $this->id;
            
            $resql = $this->db->query($sql);
            if ($resql && $this->db->num_rows($resql) > 0) {
                $obj = $this->db->fetch_object($resql);
                $document_info = array(
                    'filepath' => $obj->filepath,
                    'filename' => $obj->filename
                );
                $this->db->free($resql);
            }
        }
        
        // Supprimer l'entrée en base de données
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."elaska_salarie_client_document";
        $sql .= " WHERE rowid = " . (int) $document_id;
        $sql .= " AND fk_salarie_pour_client = " . (int) $this->id;
        
        $this->db->begin();
        
        $resql = $this->db->query($sql);
        if (!$resql) {
            $this->error = $this->db->lasterror();
            $this->db->rollback();
            return -2;
        }
        
        // Supprimer le fichier physique si demandé et si les informations sont disponibles
        if ($delete_file && $document_info) {
            $file_path = DOL_DATA_ROOT . '/' . $document_info['filepath'] . '/' . $document_info['filename'];
            if (file_exists($file_path) && !@unlink($file_path)) {
                // Échec de la suppression du fichier, mais on ne bloque pas la transaction
                dol_syslog(get_class($this)."::deleteDocument Failed to delete file: ".$file_path, LOG_WARNING);
            }
        }
        
        $this->db->commit();
        
        // Recharger les documents pour mettre à jour $this->documents
        $this->fetchDocuments();
        
        return 1;
    }
    
    /**
     * Supprime tous les fichiers physiques associés aux documents du salarié
     * Utile lors de la suppression complète du salarié
     * 
     * @return int <0 si erreur, nombre de fichiers supprimés si OK
     */
    public function deleteAllFiles()
    {
        if (empty($this->id)) return -1;
        
        // Charger les documents si ce n'est pas déjà fait
        if (empty($this->documents)) {
            $this->fetchDocuments();
        }
        
        $count = 0;
        foreach ($this->documents as $document) {
            if (!empty($document['filepath']) && !empty($document['filename'])) {
                $file_path = DOL_DATA_ROOT . '/' . $document['filepath'] . '/' . $document['filename'];
                if (file_exists($file_path) && @unlink($file_path)) {
                    $count++;
                }
            }
        }
        
        return $count;
    }

    /**
     * Change le statut d'un salarié pour client
     * 
     * @param User   $user           Utilisateur qui effectue l'action
     * @param string $nouveau_statut Code du nouveau statut
     * @return int                   <0 si erreur, >0 si OK
     */
    public function setStatut($user, $nouveau_statut)
    {
        if (empty($this->id)) return -1;
        
        // Vérifier que le statut existe dans le dictionnaire
        $statut_options = self::getStatutSalarieOptions($this->db, true);
        
        if (!array_key_exists($nouveau_statut, $statut_options)) {
            $this->error = "Code statut invalide: " . $nouveau_statut;
            return -2;
        }
        
        // Actions spécifiques selon le changement de statut
        switch ($nouveau_statut) {
            case 'EN_POSTE':
                // Si passage en poste et pas de date de début, définir à aujourd'hui
                if (empty($this->date_debut_mission)) {
                    $this->date_debut_mission = dol_print_date(dol_now(), '%Y-%m-%d');
                }
                break;
                
            case 'FIN_CONTRAT':
                // Si fin de contrat et pas de date de fin réelle, définir à aujourd'hui
                if (empty($this->date_fin_reelle)) {
                    $this->date_fin_reelle = dol_print_date(dol_now(), '%Y-%m-%d');
                }
                break;
        }
        
        $this->statut_salarie_code = $nouveau_statut;
        return $this->update($user);
    }

    // --- Méthodes statiques pour lire les dictionnaires ---
    
    /**
     * Méthode générique pour récupérer les options depuis un dictionnaire
     *
     * @param DoliDB $db                         Base de données
     * @param string $dictionary_table_suffix    Suffixe court du nom de la table dictionnaire
     * @param bool   $usekeys                    True pour retourner tableau associatif code=>label
     * @param bool   $show_empty                 True pour ajouter une option vide
     * @return array                             Tableau d'options
     */
    private static function getOptionsFromDictionary($db, $dictionary_table_suffix, $usekeys = true, $show_empty = false)
    {
        global $langs;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_salarie_client_".$db->escape($dictionary_table_suffix);
        $sql .= " WHERE active = 1";
        $sql .= " ORDER BY position ASC, label ASC";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                if ($usekeys) {
                    $options[$obj->code] = $langs->trans($obj->label);
                } else {
                    $obj_option = new stdClass();
                    $obj_option->code = $obj->code;
                    $obj_option->label = $obj->label;
                    $obj_option->label_translated = $langs->trans($obj->label);
                    $options[] = $obj_option;
                }
            }
            $db->free($resql);
        }
        
        return $options;
    }
    
    /**
     * Récupère les options de statuts de salarié pour client
     * 
     * @param DoliDB $db          Base de données
     * @param bool   $usekeys     True pour retourner tableau associatif code=>label
     * @param bool   $show_empty  True pour ajouter une option vide
     * @return array              Tableau d'options
     */
    public static function getStatutSalarieOptions($db, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($db, 'statut', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options de types de contrat
     * 
     * @param DoliDB $db          Base de données
     * @param bool   $usekeys     True pour retourner tableau associatif code=>label
     * @param bool   $show_empty  True pour ajouter une option vide
     * @return array              Tableau d'options
     */
    public static function getTypeContratOptions($db, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($db, 'type_contrat', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options de temps de travail
     * 
     * @param DoliDB $db          Base de données
     * @param bool   $usekeys     True pour retourner tableau associatif code=>label
     * @param bool   $show_empty  True pour ajouter une option vide
     * @return array              Tableau d'options
     */
    public static function getTempsTravailOptions($db, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($db, 'temps_travail', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options de niveau de compétence
     * 
     * @param DoliDB $db          Base de données
     * @param bool   $usekeys     True pour retourner tableau associatif code=>label
     * @param bool   $show_empty  True pour ajouter une option vide
     * @return array              Tableau d'options
     */
    public static function getNiveauCompetenceOptions($db, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($db, 'niveau_competence', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options de type de document
     * 
     * @param DoliDB $db          Base de données
     * @param bool   $usekeys     True pour retourner tableau associatif code=>label
     * @param bool   $show_empty  True pour ajouter une option vide
     * @return array              Tableau d'options
     */
    public static function getTypeDocumentOptions($db, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($db, 'type_document', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options de civilité depuis le dictionnaire standard Dolibarr
     * 
     * @param DoliDB $db          Base de données
     * @param bool   $usekeys     True pour retourner tableau associatif code=>label
     * @param bool   $show_empty  True pour ajouter une option vide
     * @return array              Tableau d'options
     */
    public static function getCiviliteOptions($db, $usekeys = true, $show_empty = false)
    {
        global $langs;
        
        require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $form = new Form($db);
        $civilite_array = $form->select_civility('', 'ABC', 1); // Mode spécial pour récupérer le tableau
        
        foreach ($civilite_array as $code => $label) {
            if ($code == '-1') continue; // Ignorer l'option vide
            
            if ($usekeys) {
                $options[$code] = $label;
            } else {
                $obj_option = new stdClass();
                $obj_option->code = $code;
                $obj_option->label = $code; // Pour civilité, le code est souvent utilisé comme clé de traduction
                $obj_option->label_translated = $label;
                $options[] = $obj_option;
            }
        }
        
        return $options;
    }
    
    /**
     * Récupère les salariés associés à une entreprise cliente
     * 
     * @param DoliDB $db           Base de données
     * @param int    $fk_tiers     ID de l'entreprise cliente
     * @param string $statut_code  Code du statut pour filtrer (optionnel)
     * @param int    $limit        Nombre maximum de résultats (0 = pas de limite)
     * @return array|int           Tableau d'objets ElaskaSalariePourClient ou <0 si erreur
     */
    public static function getSalariesByTiers($db, $fk_tiers, $statut_code = '', $limit = 0)
    {
        $salaries = array();
        
        if (empty($fk_tiers)) return -1;
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."elaska_salarie_pour_client";
        $sql.= " WHERE fk_elaska_tiers = ".(int)$fk_tiers;
        if (!empty($statut_code)) {
            $sql.= " AND statut_salarie_code = '".$db->escape($statut_code)."'";
        }
        $sql.= " ORDER BY date_creation DESC";
        if ($limit > 0) $sql.= " LIMIT ".(int)$limit;
        
        $resql = $db->query($sql);
        if (!$resql) {
            dol_syslog("ElaskaSalariePourClient::getSalariesByTiers Error ".$db->lasterror(), LOG_ERR);
            return -1;
        }
        
        while ($obj = $db->fetch_object($resql)) {
            $salarie = new ElaskaSalariePourClient($db);
            if ($salarie->fetch($obj->rowid) > 0) {
                $salaries[] = $salarie;
            }
        }
        
        $db->free($resql);
        return $salaries;
    }
}
}
?>