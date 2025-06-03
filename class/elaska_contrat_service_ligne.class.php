<?php
/**
 * eLaska - Classe pour gérer les lignes d'un contrat de service
 * Date: 2025-05-30
 * Version: 2.0 (Alignée sur SQL v3)
 * Auteur: Kylian65 / IA Collaboration
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

if (!class_exists('ElaskaContratServiceLigne', false)) {

class ElaskaContratServiceLigne extends CommonObject
{
    /**
     * @var string Nom de l'élément
     */
    public $element = 'elaska_contrat_service_ligne';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_contrat_service_ligne';
    
    /**
     * @var string Icône utilisée
     */
    public $picto = 'elaska@elaska';

    /**
     * @var int Référence au contrat parent
     */
    public $fk_contrat_service;
    
    /**
     * @var int Rang d'affichage dans la liste
     */
    public $rang;
    
    /**
     * @var string Description détaillée du service
     */
    public $description_ligne;
    
    /**
     * @var int ID du produit/service Dolibarr associé
     */
    public $fk_produit_service;
    
    /**
     * @var float Quantité
     */
    public $quantite;
    
    /**
     * @var float Prix unitaire HT
     */
    public $prix_unitaire_ht;
    
    /**
     * @var float Taux de TVA pour cette ligne
     */
    public $tva_tx_ligne;
    
    /**
     * @var float Pourcentage de remise pour cette ligne
     */
    public $remise_percent_ligne;
    
    /**
     * @var float Montant total HT de la ligne
     */
    public $montant_ligne_ht;
    
    /**
     * @var float Montant total TTC de la ligne
     */
    public $montant_ligne_ttc;
    
    /**
     * @var string Date de début spécifique pour cette ligne (YYYY-MM-DD)
     */
    public $date_debut_service_ligne;
    
    /**
     * @var string Date de fin spécifique pour cette ligne (YYYY-MM-DD)
     */
    public $date_fin_service_ligne;
    
    /**
     * @var string Code du statut de la ligne
     */
    public $statut_ligne_code;
    
    /**
     * @var string Notes spécifiques à cette ligne
     */
    public $notes_ligne;
    
    // Champs techniques standard
    public $rowid;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $entity;

    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'fk_contrat_service' => array('type' => 'integer:ElaskaContratService:custom/elaska/class/elaska_contrat_service.class.php', 'label' => 'ContratParentID', 'enabled' => 1, 'position' => 5, 'notnull' => 1, 'visible' => 0),
        'rang' => array('type' => 'integer', 'label' => 'RangLigne', 'enabled' => 1, 'position' => 10, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'description_ligne' => array('type' => 'text', 'label' => 'DescriptionLigneService', 'enabled' => 1, 'position' => 20, 'notnull' => 1, 'visible' => 1, 'textarea_html' => 1),
        'fk_produit_service' => array('type' => 'integer:Product:product/class/product.class.php', 'label' => 'ProduitServiceLie', 'enabled' => 1, 'position' => 25, 'notnull' => 0, 'visible' => 1),
        'quantite' => array('type' => 'double(24,8)', 'label' => 'QuantiteLigne', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1, 'default' => 1),
        'prix_unitaire_ht' => array('type' => 'double(24,8)', 'label' => 'PULigneHT', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'tva_tx_ligne' => array('type' => 'double(6,3)', 'label' => 'TauxTVALigne', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'remise_percent_ligne' => array('type' => 'double(6,3)', 'label' => 'RemisePctLigne', 'enabled' => 1, 'position' => 55, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'montant_ligne_ht' => array('type' => 'double(24,8)', 'label' => 'MontantLigneHT', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'montant_ligne_ttc' => array('type' => 'double(24,8)', 'label' => 'MontantLigneTTC', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1, 'default' => 0),
        'date_debut_service_ligne' => array('type' => 'date', 'label' => 'DateDebutServiceLigne', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'date_fin_service_ligne' => array('type' => 'date', 'label' => 'DateFinServiceLigne', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'statut_ligne_code' => array('type' => 'varchar(30)', 'label' => 'StatutLigneService', 'enabled' => 1, 'position' => 95, 'notnull' => 0, 'visible' => 1, 'default' => 'BROUILLON'),
        'notes_ligne' => array('type' => 'text', 'label' => 'NotesLigneService', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => 1, 'position' => 501, 'notnull' => 1, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => -2),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => 1, 'position' => 511, 'notnull' => 0, 'visible' => -2),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => -2),
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'default' => 1, 'enabled' => 1, 'visible' => -2, 'notnull' => 1, 'position' => 1002),
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
        if (!isset($this->quantite)) $this->quantite = 1;
        if (!isset($this->tva_tx_ligne)) $this->tva_tx_ligne = 0;
        if (!isset($this->remise_percent_ligne)) $this->remise_percent_ligne = 0;
        if (empty($this->statut_ligne_code)) $this->statut_ligne_code = 'BROUILLON';
    }

    /**
     * Calcule les montants HT et TTC de la ligne
     */
    public function calculerMontants()
    {
        // S'assurer que les valeurs numériques sont traitées comme des nombres
        $quantite = (float)$this->quantite;
        $prix_unitaire_ht = (float)$this->prix_unitaire_ht;
        $remise_percent = (float)$this->remise_percent_ligne;
        $tva_tx = (float)$this->tva_tx_ligne;
        
        // Calcul du montant HT avec remise
        $this->montant_ligne_ht = price2num($quantite * $prix_unitaire_ht * (1 - $remise_percent/100), 'MT');
        
        // Calcul du montant TTC
        if ($tva_tx > 0) {
            $this->montant_ligne_ttc = price2num($this->montant_ligne_ht * (1 + ($tva_tx/100)), 'MT');
        } else {
            $this->montant_ligne_ttc = $this->montant_ligne_ht;
        }
    }

    /**
     * Crée une ligne de contrat de service dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID de la ligne si OK
     */
    public function create($user, $notrigger = 0)
    {
        if (empty($this->fk_contrat_service)) {
            $this->error = "Impossible de créer une ligne sans contrat parent associé";
            return -1;
        }
        
        $this->calculerMontants();
        return $this->createCommon($user, $notrigger);
    }

    /**
     * Charge une ligne de contrat de la base de données
     *
     * @param int    $id    Id de la ligne
     * @param string $ref   Référence de la ligne (non utilisé)
     * @return int          <0 si KO, >0 si OK
     */
    public function fetch($id, $ref = null)
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Met à jour une ligne de contrat dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        $this->calculerMontants();
        
        $result = $this->updateCommon($user, $notrigger);
        
        // Mettre à jour les totaux du contrat parent
        if ($result > 0 && !empty($this->fk_contrat_service)) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_contrat_service.class.php';
            $contrat = new ElaskaContratService($this->db);
            if ($contrat->fetch($this->fk_contrat_service) > 0) {
                $contrat->updateTotalAmount($user, 1);
            }
        }
        
        return $result;
    }

    /**
     * Supprime une ligne de contrat de la base de données
     *
     * @param User $user       Utilisateur qui supprime
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        $parent_contract_id = $this->fk_contrat_service;
        
        $result = $this->deleteCommon($user, $notrigger);
        
        // Mettre à jour les totaux du contrat parent
        if ($result > 0 && !empty($parent_contract_id)) {
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_contrat_service.class.php';
            $contrat = new ElaskaContratService($this->db);
            if ($contrat->fetch($parent_contract_id) > 0) {
                $contrat->updateTotalAmount($user, 1);
            }
        }
        
        return $result;
    }

    /**
     * Change le statut d'une ligne de contrat
     *
     * @param User   $user           Utilisateur qui effectue l'action
     * @param string $nouveau_statut Nouveau statut à appliquer
     * @return int                   <0 si erreur, >0 si OK
     */
    public function setStatut($user, $nouveau_statut)
    {
        global $langs;
        
        // Vérifier que le statut existe dans le dictionnaire
        require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_contrat_service.class.php';
        $statut_options = ElaskaContratService::getStatutLigneOptions($langs, true);
        
        if (!array_key_exists($nouveau_statut, $statut_options)) {
            $this->error = "Code statut invalide: " . $nouveau_statut;
            return -1;
        }
        
        $this->statut_ligne_code = $nouveau_statut;
        return $this->update($user, 1); // 1 = pas de triggers
    }

    /**
     * Charge les informations du produit lié et met à jour les attributs de la ligne
     * 
     * @param bool $force_price Forcer la mise à jour du prix depuis le produit
     * @return int              <0 si erreur, >0 si OK
     */
    public function chargerInfosProduit($force_price = false)
    {
        if (empty($this->fk_produit_service)) return 0;
        
        require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
        $product = new Product($this->db);
        $result = $product->fetch($this->fk_produit_service);
        
        if ($result > 0) {
            // Mettre à jour la description si vide
            if (empty($this->description_ligne)) {
                if (!empty($product->description)) {
                    $this->description_ligne = $product->description;
                } else {
                    $this->description_ligne = $product->label;
                }
            }
            
            // Mettre à jour le prix si demandé ou si vide
            if ($force_price || empty($this->prix_unitaire_ht)) {
                $this->prix_unitaire_ht = $product->price;
            }
            
            // Mettre à jour le taux de TVA si vide
            if (empty($this->tva_tx_ligne)) {
                $this->tva_tx_ligne = $product->tva_tx;
            }
            
            return 1;
        } else {
            $this->error = $product->error;
            return -1;
        }
    }
}
}
?>
