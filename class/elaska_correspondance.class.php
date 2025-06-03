<?php
/**
 * eLaska - Classe pour gérer les correspondances des particuliers
 * Date: 2025-06-03
 * Version: 3.0 (Version enrichie pour gestion complète des contrats et portail client)
 * Auteur: Gemini
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_particulier.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_numero.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_document.class.php'; // Pour lier les documents générés/reçus
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_notification.class.php'; // Pour les notifications aux conseillers
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_contrat_service.class.php'; // Pour vérifier les contrats d'assistance
require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_modele_document.class.php'; // Pour générer des documents à partir de modèles

if (!class_exists('ElaskaParticulierCorrespondance', false)) {

class ElaskaParticulierCorrespondance extends CommonObject
{
    /**
     * @var string Nom de l'élément
     */
    public $element = 'elaska_particulier_correspondance';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier_correspondance';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'mail@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;
    
    //
    // PROPRIÉTÉS DE LA CORRESPONDANCE
    //
    
    /**
     * @var string Référence unique de la correspondance
     */
    public $ref;
    
    /**
     * @var int ID du particulier lié à cette correspondance
     */
    public $fk_particulier;
    
    /**
     * @var string Type de correspondance (dictionnaire: ENTRANT, SORTANT, INTERNE)
     */
    public $type_correspondance_code;
    
    /**
     * @var string Méthode d'envoi/réception (dictionnaire: COURRIER, EMAIL, RECOMMANDE, SMS, PORTAIL)
     */
    public $methode_envoi_code;
    
    /**
     * @var string Objet de la correspondance
     */
    public $objet;
    
    /**
     * @var string Contenu de la correspondance (texte brut ou HTML si email)
     */
    public $contenu;
    
    /**
     * @var string Date d'envoi (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_envoi;
    
    /**
     * @var string Date de réception (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_reception;

    /**
     * @var string Date de réception de l'accusé de réception (pour les recommandés)
     */
    public $date_accuse_reception_recu;
    
    /**
     * @var string Numéro de suivi postal (recommandé, colis, etc.)
     */
    public $numero_suivi_postal;
    
    /**
     * @var string Statut de la correspondance (dictionnaire: BROUILLON, A_ENVOYER, ENVOYE, EN_TRANSIT, AVISE, RECU, NON_RECLAME, REFUSE, ARCHIVE, ERREUR_ENVOI)
     */
    public $statut_correspondance_code;
    
    /**
     * @var int ID du contact/tiers destinataire ou émetteur (si externe)
     */
    public $fk_contact_externe;
    
    /**
     * @var string Type de contact externe (dictionnaire: CONTACT, ORGANISME, TIERS)
     */
    public $type_contact_externe_code;
    
    /**
     * @var int ID de la démarche liée (si applicable)
     */
    public $fk_demarche;
    
    /**
     * @var string Type de l'élément de la démarche liée (ex: 'elaska_particulier_demarche_impot')
     */
    public $element_type_demarche;

    /**
     * @var int ID du document ElaskaDocument généré ou lié à cette correspondance
     */
    public $fk_document;

    /**
     * @var double Coût d'envoi de la correspondance (ex: affranchissement recommandé)
     */
    public $cout_envoi_ttc;
    
    /**
     * @var string Notes internes additionnelles (non visibles par le client)
     */
    public $notes_internes;

    /**
     * @var string Notes visibles par le client sur le portail (HTML/Markdown)
     */
    public $notes_client_portal;

    /**
     * @var string Date à laquelle le client a marqué la correspondance comme lue sur le portail
     */
    public $date_lecture_client;

    /**
     * @var string Historique des actions spécifiques à cette correspondance (format JSON)
     */
    public $historique_actions;

    // Champs techniques standard
    public $rowid;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $entity;
    public $status; // État de l'enregistrement (0=inactif, 1=actif)

    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'ref' => array('type' => 'varchar(30)', 'label' => 'Ref', 'enabled' => 1, 'position' => 5, 'notnull' => 1, 'visible' => 1, 'index' => 1),
        'fk_particulier' => array('type' => 'integer:ElaskaParticulier:custom/elaska/class/elaska_particulier.class.php', 'label' => 'Particulier', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1),
        'type_correspondance_code' => array('type' => 'varchar(50)', 'label' => 'TypeCorrespondance', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1),
        'methode_envoi_code' => array('type' => 'varchar(50)', 'label' => 'MethodeEnvoi', 'enabled' => 1, 'position' => 30, 'notnull' => 1, 'visible' => 1),
        'objet' => array('type' => 'varchar(255)', 'label' => 'Objet', 'enabled' => 1, 'position' => 40, 'notnull' => 1, 'visible' => 1),
        'contenu' => array('type' => 'mediumtext', 'label' => 'Contenu', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 0), // Peut être masqué si document lié
        'date_envoi' => array('type' => 'datetime', 'label' => 'DateEnvoi', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'date_reception' => array('type' => 'datetime', 'label' => 'DateReception', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'date_accuse_reception_recu' => array('type' => 'datetime', 'label' => 'DateAccuseReceptionRecu', 'enabled' => 1, 'position' => 75, 'notnull' => 0, 'visible' => 1),
        'numero_suivi_postal' => array('type' => 'varchar(50)', 'label' => 'NumeroSuiviPostal', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'statut_correspondance_code' => array('type' => 'varchar(50)', 'label' => 'StatutCorrespondance', 'enabled' => 1, 'position' => 90, 'notnull' => 1, 'visible' => 1, 'default' => 'BROUILLON'),
        'fk_contact_externe' => array('type' => 'integer', 'label' => 'ContactExterne', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1), // Peut être un contact Dolibarr ou un tiers
        'type_contact_externe_code' => array('type' => 'varchar(50)', 'label' => 'TypeContactExterne', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'fk_demarche' => array('type' => 'integer', 'label' => 'DemarcheLiee', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'element_type_demarche' => array('type' => 'varchar(50)', 'label' => 'ElementTypeDemarche', 'enabled' => 1, 'position' => 125, 'notnull' => 0, 'visible' => 1),
        'fk_document' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'DocumentLie', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'cout_envoi_ttc' => array('type' => 'double(24,8)', 'label' => 'CoutEnvoiTTC', 'enabled' => 1, 'position' => 135, 'notnull' => 0, 'visible' => 1),
        'notes_internes' => array('type' => 'text', 'label' => 'NotesInternes', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 0),
        'notes_client_portal' => array('type' => 'text', 'label' => 'NotesClientPortal', 'enabled' => 1, 'position' => 150, 'notnull' => 0, 'visible' => 1),
        'date_lecture_client' => array('type' => 'datetime', 'label' => 'DateLectureClient', 'enabled' => 1, 'position' => 155, 'notnull' => 0, 'visible' => 1),
        'historique_actions' => array('type' => 'text', 'label' => 'HistoriqueActions', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        
        // CHAMPS TECHNIQUES
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'enabled' => 1, 'position' => 900, 'notnull' => 1, 'visible' => 0, 'default' => '1'),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 910, 'notnull' => 1, 'visible' => 0),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 920, 'notnull' => 1, 'visible' => 0),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 930, 'notnull' => 1, 'visible' => 0),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 940, 'notnull' => 0, 'visible' => 0),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 950, 'notnull' => 0, 'visible' => 0),
        'status' => array('type' => 'integer', 'label' => 'Status', 'enabled' => 1, 'position' => 1000, 'notnull' => 1, 'visible' => 1, 'default' => '1'),
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        parent::__construct($db);
        
        // Valeurs par défaut
        if (!isset($this->status)) $this->status = 1;
        if (empty($this->statut_correspondance_code)) $this->statut_correspondance_code = self::STATUT_BROUILLON;
        if (empty($this->type_correspondance_code)) $this->type_correspondance_code = self::TYPE_SORTANT; // Par défaut, une correspondance est sortante
        if (!isset($this->cout_envoi_ttc)) $this->cout_envoi_ttc = 0;
    }

    /**
     * Crée une correspondance dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Vérification des champs obligatoires
        if (empty($this->fk_particulier)) {
            $this->error = 'ParticulierIDIsMandatory';
            return -1;
        }
        if (empty($this->objet)) {
            $this->error = 'ObjetIsMandatory';
            return -1;
        }
        if (empty($this->type_correspondance_code)) {
            $this->error = 'TypeCorrespondanceIsMandatory';
            return -1;
        }
        if (empty($this->methode_envoi_code)) {
            $this->error = 'MethodeEnvoiIsMandatory';
            return -1;
        }
        
        // Vérification que le particulier existe
        $particulier = new ElaskaParticulier($this->db);
        if ($particulier->fetch($this->fk_particulier) <= 0) {
            $this->error = 'ParticulierDoesNotExist';
            return -1;
        }

        // Génération de la référence unique
        if (empty($this->ref)) {
            $params = array();
            $params['TYPE_CORR'] = $this->type_correspondance_code;
            $params['METH_ENVOI'] = $this->methode_envoi_code;
            $reference = ElaskaNumero::generateAndRecord($this->db, 'CORR', $this->element, 0, '', $params);
            if (empty($reference)) {
                $this->error = 'ErrorGeneratingReference';
                return -1;
            }
            $this->ref = $reference;
        }
        
        // Si c'est un envoi, la date d'envoi est la date de création par défaut
        if ($this->type_correspondance_code == self::TYPE_SORTANT && empty($this->date_envoi)) {
            $this->date_envoi = dol_now();
        }
        // Si c'est une réception, le statut par défaut est A_TRAITER ou RECU si date de réception
        if ($this->type_correspondance_code == self::TYPE_ENTRANT && empty($this->statut_correspondance_code)) {
            $this->statut_correspondance_code = empty($this->date_reception) ? self::STATUT_A_TRAITER : self::STATUT_RECU;
        }

        $result = $this->createCommon($user, $notrigger);
        
        if ($result > 0) {
            // Mettre à jour l'ID de l'objet dans ElaskaNumero
            ElaskaNumero::recordUsedNumber($this->db, $this->ref, 'CORR', $this->element, $this->id, $this->entity);

            // Ajouter à l'historique du particulier
            $particulier->addHistorique(
                $user,
                self::ACTION_CREATE,
                'correspondance',
                $this->id,
                'Création de la correspondance : '.$this->objet.' (Réf: '.$this->ref.')'
            );
        }
        
        return $result;
    }

    /**
     * Charge une correspondance depuis la base de données
     *
     * @param int    $id      ID de la correspondance
     * @param string $ref     Référence de la correspondance
     * @return int            <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = '')
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Met à jour une correspondance dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        // Vérifications de base
        if (empty($this->objet)) {
            $this->error = 'ObjetIsMandatory';
            return -1;
        }
        if (empty($this->type_correspondance_code)) {
            $this->error = 'TypeCorrespondanceIsMandatory';
            return -1;
        }
        if (empty($this->methode_envoi_code)) {
            $this->error = 'MethodeEnvoiIsMandatory';
            return -1;
        }
        
        $this->fk_user_modif = $user->id;
        $result = $this->updateCommon($user, $notrigger);
        
        if ($result > 0 && !$notrigger) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    self::ACTION_UPDATE,
                    'correspondance',
                    $this->id,
                    'Mise à jour de la correspondance : '.$this->objet.' (Réf: '.$this->ref.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Supprime une correspondance de la base de données
     *
     * @param User $user      Utilisateur qui supprime
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        // Sauvegarde d'informations avant suppression pour l'historique
        $objetCorrespondance = $this->objet;
        $refCorrespondance = $this->ref;
        $idParticulier = $this->fk_particulier;
        
        $result = $this->deleteCommon($user, $notrigger);
        
        if ($result > 0 && !$notrigger && $idParticulier > 0) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($idParticulier) > 0) {
                $particulier->addHistorique(
                    $user,
                    self::ACTION_DELETE,
                    'correspondance',
                    0, // ID à 0 car la correspondance est supprimée
                    'Suppression de la correspondance : '.$objetCorrespondance.' (Réf: '.$refCorrespondance.')'
                );
            }
        }
        
        return $result;
    }

    /**
     * Change le statut d'une correspondance
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $statut_code Nouveau statut
     * @param string $commentaire Commentaire optionnel
     * @return int                <0 si erreur, >0 si OK
     */
    public function setStatut($user, $statut_code, $commentaire = '')
    {
        $statuts_valides = self::getStatutsValides();
        
        if (!in_array($statut_code, $statuts_valides)) {
            $this->error = 'InvalidStatutCode';
            return -1;
        }
        
        $ancien_statut = $this->statut_correspondance_code;
        $this->statut_correspondance_code = $statut_code;
        
        // Mises à jour automatiques des dates selon le statut
        if ($statut_code == self::STATUT_ENVOYE && empty($this->date_envoi)) {
            $this->date_envoi = dol_now();
        } elseif ($statut_code == self::STATUT_RECU && empty($this->date_reception)) {
            $this->date_reception = dol_now();
        }
        
        // Mise à jour de la note si un commentaire est fourni
        if (!empty($commentaire)) {
            $this->addToNotes($user, $commentaire);
        }
        
        // Mise à jour de l'historique spécifique
        $this->ajouterActionHistorique($user, self::ACTION_CHANGEMENT_STATUT, $ancien_statut . ' → ' . $statut_code, $commentaire);
        
        $result = $this->update($user, 1); // notrigger = 1 pour éviter un double enregistrement
        
        if ($result > 0) {
            // Ajouter à l'historique du particulier
            $particulier = new ElaskaParticulier($this->db);
            if ($particulier->fetch($this->fk_particulier) > 0) {
                $message = 'Changement de statut pour la correspondance "'.$this->objet.'" (Réf: '.$this->ref.'): '.$ancien_statut.' → '.$statut_code;
                if (!empty($commentaire)) {
                    $message .= ' ('.$commentaire.')';
                }
                $particulier->addHistorique(
                    $user,
                    self::ACTION_CHANGEMENT_STATUT,
                    'correspondance',
                    $this->id,
                    $message,
                    array('statut_correspondance_code' => array($ancien_statut, $statut_code))
                );
            }
        }
        
        return $result;
    }

    /**
     * Récupère toutes les correspondances d'un particulier
     *
     * @param int    $fk_particulier ID du particulier
     * @param string $type_filtre    Filtre optionnel par type (ENTRANT, SORTANT, INTERNE)
     * @param string $statut_filtre  Filtre optionnel par statut (BROUILLON, ENVOYE, RECU, etc.)
     * @param string $orderby        Colonnes pour ORDER BY
     * @param int    $limit          Limite de résultats
     * @param int    $offset         Décalage pour pagination
     * @return array                 Tableau d'objets ElaskaParticulierCorrespondance
     */
    public function fetchAllByParticulier($fk_particulier, $type_filtre = '', $statut_filtre = '', $orderby = 'date_creation DESC', $limit = 0, $offset = 0)
    {
        $correspondances = array();
        
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE fk_particulier = ".(int) $fk_particulier;
        $sql.= " AND entity IN (".getEntity($this->element).")";
        
        if (!empty($type_filtre)) {
            $sql.= " AND type_correspondance_code = '".$this->db->escape($type_filtre)."'";
        }
        if (!empty($statut_filtre)) {
            $sql.= " AND statut_correspondance_code = '".$this->db->escape($statut_filtre)."'";
        }
        
        $sql.= " ORDER BY ".$orderby;
        
        if ($limit) {
            if ($offset) {
                $sql.= " LIMIT ".(int) $offset.",".(int) $limit;
            } else {
                $sql.= " LIMIT ".(int) $limit;
            }
        }
        
        $resql = $this->db->query($sql);
        if ($resql) {
            $num = $this->db->num_rows($resql);
            if ($num) {
                $i = 0;
                while ($i < $num) {
                    $obj = $this->db->fetch_object($resql);
                    $correspondanceTemp = new ElaskaParticulierCorrespondance($this->db);
                    $correspondanceTemp->fetch($obj->rowid);
                    $correspondances[] = $correspondanceTemp;
                    $i++;
                }
            }
            $this->db->free($resql);
        } else {
            dol_syslog(get_class($this)."::fetchAllByParticulier Error ".$this->db->lasterror(), LOG_ERR);
        }
        
        return $correspondances;
    }

    /**
     * Enregistrer la réception d'une correspondance
     *
     * @param User   $user           Utilisateur effectuant l'action
     * @param string $date_reception Date de réception (format YYYY-MM-DD HH:MM:SS)
     * @param string $commentaire    Commentaire optionnel
     * @return int                   <0 si erreur, >0 si OK
     */
    public function enregistrerReception($user, $date_reception = '', $commentaire = '')
    {
        if (empty($date_reception)) {
            $date_reception = dol_now();
        }
        
        $this->date_reception = $date_reception;
        
        // Mettre à jour le statut à RECU
        return $this->setStatut($user, self::STATUT_RECU, 'Réception enregistrée: '.$commentaire);
    }

    /**
     * Envoie une correspondance (met à jour le statut à ENVOYE et la date d'envoi)
     *
     * @param User   $user           Utilisateur effectuant l'action
     * @param string $date_envoi     Date d'envoi (format YYYY-MM-DD HH:MM:SS)
     * @param string $numero_suivi   Numéro de suivi postal (optionnel)
     * @param double $cout_envoi     Coût de l'envoi (optionnel)
     * @param string $commentaire    Commentaire optionnel
     * @return int                   <0 si erreur, >0 si OK
     */
    public function envoyerCorrespondance($user, $date_envoi = '', $numero_suivi = '', $cout_envoi = 0, $commentaire = '')
    {
        if (empty($date_envoi)) {
            $date_envoi = dol_now();
        }
        
        $this->date_envoi = $date_envoi;
        $this->numero_suivi_postal = $numero_suivi;
        $this->cout_envoi_ttc = $cout_envoi;
        
        // Mettre à jour le statut à ENVOYE
        return $this->setStatut($user, self::STATUT_ENVOYE, 'Correspondance envoyée: '.$commentaire);
    }

    /**
     * Met à jour le statut de suivi postal (via API La Poste ou manuellement)
     *
     * @param User   $user        Utilisateur effectuant l'action
     * @param string $nouveau_statut_postal Nouveau statut de suivi (EN_TRANSIT, AVISE, NON_RECLAME, REFUSE, RECU)
     * @param string $commentaire Commentaire optionnel
     * @return int               <0 si erreur, >0 si OK
     */
    public function updateStatutSuiviPostal($user, $nouveau_statut_postal, $commentaire = '')
    {
        $statuts_suivi_valides = array(self::STATUT_EN_TRANSIT, self::STATUT_AVISE, self::STATUT_NON_RECLAME, self::STATUT_REFUSE, self::STATUT_RECU);
        
        if (!in_array($nouveau_statut_postal, $statuts_suivi_valides)) {
            $this->error = 'InvalidStatutSuiviPostalCode';
            return -1;
        }

        // Si le statut est RECU, mettre à jour la date de réception
        if ($nouveau_statut_postal == self::STATUT_RECU && empty($this->date_reception)) {
            $this->date_reception = dol_now();
        }
        // Si c'est un accusé de réception, mettre à jour la date spécifique
        if ($nouveau_statut_postal == self::STATUT_RECU && $this->methode_envoi_code == self::METHODE_RECOMMANDE && empty($this->date_accuse_reception_recu)) {
            $this->date_accuse_reception_recu = dol_now();
        }
        
        // Mettre à jour le statut de la correspondance principale
        $result = $this->setStatut($user, $nouveau_statut_postal, $commentaire);

        if ($result > 0) {
            $this->ajouterActionHistorique($user, self::ACTION_MISE_A_JOUR_SUIVI, 'Statut suivi postal: ' . $nouveau_statut_postal, $commentaire);
        }
        return $result;
    }

    /**
     * Génère les statistiques des correspondances
     * * @param int $fk_particulier ID du particulier (optionnel)
     * @return array Statistiques des correspondances
     */
    public static function getStatistiques($db, $fk_particulier = 0)
    {
        $stats = array(
            'total' => 0,
            'entrant' => 0,
            'sortant' => 0,
            'par_statut' => array(),
            'par_mois' => array()
        );
        
        $sql = "SELECT COUNT(*) as total, type_correspondance_code, statut_correspondance_code FROM ".MAIN_DB_PREFIX."elaska_particulier_correspondance";
        $sql.= " WHERE entity IN (".getEntity('elaska_particulier_correspondance').")";
        
        if ($fk_particulier > 0) {
            $sql.= " AND fk_particulier = ".(int) $fk_particulier;
        }
        
        $sql.= " GROUP BY type_correspondance_code, statut_correspondance_code";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                $stats['total'] += $obj->total;
                
                if ($obj->type_correspondance_code == self::TYPE_ENTRANT) {
                    $stats['entrant'] += $obj->total;
                } elseif ($obj->type_correspondance_code == self::TYPE_SORTANT) {
                    $stats['sortant'] += $obj->total;
                }
                
                if (!isset($stats['par_statut'][$obj->statut_correspondance_code])) {
                    $stats['par_statut'][$obj->statut_correspondance_code] = 0;
                }
                $stats['par_statut'][$obj->statut_correspondance_code] += $obj->total;
            }
            $db->free($resql);
        }
        
        // Statistiques par mois (12 derniers mois)
        $sql = "SELECT COUNT(*) as total, MONTH(date_creation) as mois, YEAR(date_creation) as annee 
                FROM ".MAIN_DB_PREFIX."elaska_particulier_correspondance";
        $sql.= " WHERE entity IN (".getEntity('elaska_particulier_correspondance').")";
        $sql.= " AND date_creation >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        
        if ($fk_particulier > 0) {
            $sql.= " AND fk_particulier = ".(int) $fk_particulier;
        }
        
        $sql.= " GROUP BY YEAR(date_creation), MONTH(date_creation)";
        $sql.= " ORDER BY YEAR(date_creation) ASC, MONTH(date_creation) ASC";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                $moisAnnee = $obj->annee.'-'.sprintf('%02d', $obj->mois);
                $stats['par_mois'][$moisAnnee] = $obj->total;
            }
            $db->free($resql);
        }
        
        return $stats;
    }

    /**
     * Génère un document à partir d'un modèle et le lie à la correspondance
     *
     * @param User   $user          Utilisateur effectuant l'action
     * @param string $modele_ref    Référence du modèle de document (ElaskaModeleDocument)
     * @param array  $variables     Tableau des variables à substituer dans le modèle
     * @param string $commentaire   Commentaire optionnel
     * @return int                  <0 si erreur, ID du document généré si OK
     */
    public function genererDocumentFromTemplate($user, $modele_ref, $variables = array(), $commentaire = '')
    {
        global $langs;

        if (empty($modele_ref)) {
            $this->error = $langs->trans('ModelReferenceIsMandatory');
            return -1;
        }

        $modele = new ElaskaModeleDocument($this->db);
        if ($modele->fetch(0, $modele_ref) <= 0) {
            $this->error = $langs->trans('ModelNotFound', $modele_ref);
            return -1;
        }

        // Ajouter les variables par défaut de la correspondance
        $default_vars = array(
            'CORR_REF' => $this->ref,
            'CORR_OBJET' => $this->objet,
            'CORR_DATE_ENVOI' => dol_print_date($this->date_envoi, 'day'),
            // Ajouter d'autres variables de la correspondance si nécessaire
        );
        $final_vars = array_merge($default_vars, $variables);

        // Générer le document, en le liant directement à cette correspondance
        $document_id = $modele->genererDocument($this, null, $final_vars); // Le 2ème param est $outputlangs, on met null pour défaut

        if ($document_id > 0) {
            $this->fk_document = $document_id;
            $result = $this->update($user, 1); // Mise à jour silencieuse de la correspondance

            if ($result > 0) {
                $this->ajouterActionHistorique($user, self::ACTION_GENERATION_DOCUMENT, 'Document généré à partir du modèle: ' . $modele_ref, $commentaire);
            }
            return $document_id;
        } else {
            $this->error = $modele->error; // Récupérer l'erreur du modèle de document
            return -1;
        }
    }

    /**
     * Marque la correspondance comme lue par le client sur le portail
     *
     * @param User $user_client Utilisateur client qui marque comme lu
     * @return int               <0 si erreur, >0 si OK
     */
    public function markAsReadByClient($user_client)
    {
        if ($this->date_lecture_client) { // Déjà lue
            return 1;
        }

        $this->date_lecture_client = dol_now();
        $result = $this->update($user_client, 1); // Mise à jour silencieuse

        if ($result > 0) {
            $this->ajouterActionHistorique($user_client, self::ACTION_LECTURE_CLIENT, 'Correspondance lue par le client sur le portail.');
        }
        return $result;
    }

    /**
     * Permet au client de demander des précisions sur la correspondance via le portail
     *
     * @param User   $user_client Utilisateur client effectuant la demande
     * @param string $question    Question ou demande de précision du client
     * @return int               <0 si erreur, >0 si OK
     */
    public function demanderPrecisionClient($user_client, $question)
    {
        global $langs, $conf;

        if (empty($question)) {
            $this->error = $langs->trans('QuestionIsMandatory');
            return -1;
        }

        // Ajouter à l'historique de la correspondance
        $this->ajouterActionHistorique(
            $user_client,
            self::ACTION_DEMANDE_PRECISION_CLIENT,
            $langs->trans('ClientDemandePrecision'),
            $question
        );

        // Créer une notification pour le conseiller responsable
        if (class_exists('ElaskaNotification')) {
            $particulier = new ElaskaParticulier($this->db);
            $particulier->fetch($this->fk_particulier);

            ElaskaNotification::createSystemNotification(
                $this->db,
                $user_client, // L'utilisateur qui déclenche l'action (le client)
                'CORRESPONDANCE_DEMANDE_PRECISION',
                $langs->trans('DemandePrecisionCorrespondanceTitre', $this->ref),
                $langs->trans('DemandePrecisionCorrespondanceMessage', $this->objet, $this->ref, $user_client->getFullName($langs), $question),
                $this->id,
                $this->element,
                $particulier->fk_user_conseiller_referent ?: $conf->global->MAIN_INFO_ADMIN_EMAIL, // Notifier le conseiller référent ou l'admin
                '/custom/elaska/particulier/correspondance/card.php?id='.$this->id,
                'HIGH' // Priorité élevée
            );
        }
        return 1;
    }

    /**
     * Ajoute du contenu aux notes internes avec date et séparateur
     *
     * @param User   $user      Utilisateur effectuant l'action
     * @param string $note      Texte à ajouter
     * @return int              <0 si erreur, >0 si OK
     */
    private function addToNotes($user, $note)
    {
        if (!empty($this->notes_internes)) {
            $this->notes_internes .= "\n\n" . date('Y-m-d H:i') . " - " . $note;
        } else {
            $this->notes_internes = date('Y-m-d H:i') . " - " . $note;
        }
        
        return $this->update($user, 1); // Mise à jour silencieuse
    }

    /**
     * Ajoute une action à l'historique spécifique de la correspondance
     *
     * @param User   $user      Utilisateur effectuant l'action
     * @param string $type      Type d'action (libre ou utiliser les constantes de la classe)
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
        
        $entries = explode("\n\n", $this->historique_actions);
        
        if (!empty($filter)) {
            $filtered_entries = array();
            foreach ($entries as $entry) {
                if (strpos($entry, ' - ' . $filter . ' - ') !== false) {
                    $filtered_entries[] = $entry;
                }
            }
            $entries = $filtered_entries;
        }
        
        if ($limit > 0 && count($entries) > $limit) {
            $entries = array_slice($entries, 0, $limit);
        }
        
        if (!$html && is_array($entries)) {
            $structured_entries = array();
            foreach ($entries as $entry) {
                $parts = explode(' - ', $entry, 4); 
                if (count($parts) >= 3) {
                    $structured_entry = array(
                        'datetime' => $parts[0],
                        'user' => $parts[1],
                        'type' => $parts[2],
                        'details' => isset($parts[3]) ? $parts[3] : ''
                    );
                    $comment_pos = isset($parts[3]) ? strpos($parts[3], ' - Commentaire: ') : false;
                    if ($comment_pos !== false) {
                        $structured_entry['details'] = substr($parts[3], 0, $comment_pos);
                        $structured_entry['comment'] = substr(html_entity_decode($parts[3], ENT_QUOTES), $comment_pos + 15); // Decode HTML entities for comment
                    }
                    $structured_entries[] = $structured_entry;
                }
            }
            return $structured_entries;
        }
        
        if ($html) {
            $html_output = '<div class="historique-actions">';
            foreach ($entries as $entry) {
                $parts = explode(' - ', $entry, 4);
                if (count($parts) >= 3) {
                    $datetime = $parts[0];
                    $user_name = $parts[1];
                    $type = $parts[2];
                    $details = isset($parts[3]) ? $parts[3] : '';
                    
                    $class = '';
                    switch ($type) {
                        case self::ACTION_CHANGEMENT_STATUT: $class = 'bg-info'; break;
                        case self::ACTION_ENVOI_CORRESPONDANCE: $class = 'bg-primary'; break;
                        case self::ACTION_RECEPTION_CORRESPONDANCE: $class = 'bg-success'; break;
                        case self::ACTION_MISE_A_JOUR_SUIVI: $class = 'bg-warning'; break;
                        case self::ACTION_DEMANDE_RESILIATION: $class = 'bg-danger'; break; // Utiliser bg-danger pour les demandes client de résiliation
                        case self::ACTION_LECTURE_CLIENT: $class = 'bg-secondary'; break;
                        case self::ACTION_DEMANDE_PRECISION_CLIENT: $class = 'bg-purple'; break;
                        case self::ACTION_GENERATION_DOCUMENT: $class = 'bg-info'; break;
                        default: $class = '';
                    }
                    
                    $comment_html = '';
                    $comment_pos = strpos($details, ' - Commentaire: ');
                    if ($comment_pos !== false) {
                        $comment = substr($details, $comment_pos + 15);
                        $details = substr($details, 0, $comment_pos);
                        $comment_html = '<div class="historique-comment"><em>' . dol_htmlentities($comment) . '</em></div>';
                    }
                    
                    $html_output .= '<div class="historique-entry '.$class.'">';
                    $html_output .= '<div class="historique-header">';
                    $html_output .= '<span class="historique-date">' . dol_htmlentities($datetime) . '</span> - ';
                    $html_output .= '<span class="historique-user">' . dol_htmlentities($user_name) . '</span> - ';
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
        
        return implode("\n\n", $entries);
    }

    /**
     * Liste des statuts de correspondance valides
     *
     * @return array Codes des statuts valides
     */
    public static function getStatutsValides()
    {
        return array(
            self::STATUT_BROUILLON,
            self::STATUT_A_ENVOYER,
            self::STATUT_ENVOYE,
            self::STATUT_EN_TRANSIT,
            self::STATUT_AVISE,
            self::STATUT_RECU,
            self::STATUT_NON_RECLAME,
            self::STATUT_REFUSE,
            self::STATUT_ARCHIVE,
            self::STATUT_ERREUR_ENVOI,
            self::STATUT_A_TRAITER // Nouveau statut
        );
    }

    /**
     * Récupère les options du dictionnaire des types de correspondance
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeCorrespondanceOptions($langs, $usekeys = true, $show_empty = false)
    {
        return ElaskaDictionaryHelper::getDictionaryOptions($langs->db, 'c_elaska_part_correspondance_type', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des méthodes d'envoi
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getMethodeEnvoiOptions($langs, $usekeys = true, $show_empty = false)
    {
        return ElaskaDictionaryHelper::getDictionaryOptions($langs->db, 'c_elaska_part_correspondance_methode', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts de correspondance
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutCorrespondanceOptions($langs, $usekeys = true, $show_empty = false)
    {
        return ElaskaDictionaryHelper::getDictionaryOptions($langs->db, 'c_elaska_part_correspondance_statut', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des types de contact externe
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeContactExterneOptions($langs, $usekeys = true, $show_empty = false)
    {
        return ElaskaDictionaryHelper::getDictionaryOptions($langs->db, 'c_elaska_part_correspondance_contact_type', $usekeys, $show_empty);
    }
    
    /**
     * Récupère le document lié à cette correspondance
     * @return ElaskaDocument|null
     */
    public function getDocument()
    {
        if (empty($this->fk_document)) {
            return null;
        }
        $doc = new ElaskaDocument($this->db);
        if ($doc->fetch($this->fk_document) > 0) {
            return $doc;
        }
        return null;
    }

    /**
     * Définit des constantes pour les types de correspondance
     */
    const TYPE_ENTRANT = 'ENTRANT';
    const TYPE_SORTANT = 'SORTANT';
    const TYPE_INTERNE = 'INTERNE'; // Pour les notes internes qui sont des correspondances

    /**
     * Définit des constantes pour les méthodes d'envoi
     */
    const METHODE_COURRIER = 'COURRIER';
    const METHODE_EMAIL = 'EMAIL';
    const METHODE_RECOMMANDE = 'RECOMMANDE';
    const METHODE_SMS = 'SMS';
    const METHODE_PORTAIL = 'PORTAIL'; // Message via le portail client

    /**
     * Définit des constantes pour les statuts de correspondance
     */
    const STATUT_BROUILLON = 'BROUILLON';
    const STATUT_A_ENVOYER = 'A_ENVOYER';
    const STATUT_ENVOYE = 'ENVOYE';
    const STATUT_EN_TRANSIT = 'EN_TRANSIT'; // Pour suivi postal
    const STATUT_AVISE = 'AVISE';         // Pour suivi postal
    const STATUT_RECU = 'RECU';
    const STATUT_NON_RECLAME = 'NON_RECLAME'; // Pour suivi postal
    const STATUT_REFUSE = 'REFUSE';       // Pour suivi postal
    const STATUT_ARCHIVE = 'ARCHIVE';
    const STATUT_ERREUR_ENVOI = 'ERREUR_ENVOI';
    const STATUT_A_TRAITER = 'A_TRAITER'; // Pour les correspondances entrantes non traitées

    /**
     * Définit des constantes pour les types d'actions dans l'historique
     */
    const ACTION_CREATE = 'CREATE';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';
    const ACTION_CHANGEMENT_STATUT = 'CHANGEMENT_STATUT';
    const ACTION_ENVOI_CORRESPONDANCE = 'ENVOI_CORRESPONDANCE';
    const ACTION_RECEPTION_CORRESPONDANCE = 'RECEPTION_CORRESPONDANCE';
    const ACTION_MISE_A_JOUR_SUIVI = 'MISE_A_JOUR_SUIVI'; // Pour les mises à jour de suivi postal
    const ACTION_DEMANDE_RESILIATION = 'DEMANDE_RESILIATION'; // Demande du client via portail
    const ACTION_GENERATION_DOCUMENT = 'GENERATION_DOCUMENT'; // Document généré lié à la correspondance
    const ACTION_LECTURE_CLIENT = 'LECTURE_CLIENT'; // Correspondance lue par le client
    const ACTION_DEMANDE_PRECISION_CLIENT = 'DEMANDE_PRECISION_CLIENT'; // Demande de précision du client
}

} // Fin de la condition if !class_exists
