<?php
/**
 * Classe de liaison entre objectifs et démarches administratives
 * 
 * @package Elaska
 * @subpackage Particuliers
 * @author Elaska Dev Team
 * @version 4.5.0
 */
class ElaskaParticulierObjectifDemarche extends ElaskaModel {
    /**
     * Nom de la table en base de données
     * @var string
     */
    protected static $table_name = 'elaska_particulier_objectif_demarche';
    
    /**
     * Clé primaire
     * @var string
     */
    protected static $primary_key = 'id';
    
    /**
     * Liste des champs de la table
     * @var array
     */
    protected static $fields = [
        'id', 'objectif_id', 'demarche_id', 'type_liaison',
        'poids_impact', 'description', 'date_creation'
    ];
    
    /**
     * ID unique de la liaison
     * @var int
     */
    private int $id;
    
    /**
     * ID de l'objectif lié
     * @var int
     */
    private int $objectif_id;
    
    /**
     * ID de la démarche liée
     * @var int
     */
    private int $demarche_id;
    
    /**
     * Type de liaison (prerequis, contributif, bloquant)
     * @var string
     */
    private string $type_liaison;
    
    /**
     * Poids d'impact de la démarche sur l'objectif (0-1)
     * @var float
     */
    private float $poids_impact;
    
    /**
     * Description de la relation
     * @var string
     */
    private string $description;
    
    /**
     * Date de création de la liaison
     * @var DateTime
     */
    private DateTime $date_creation;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->date_creation = new DateTime();
        $this->poids_impact = 0.5; // Valeur par défaut
        $this->type_liaison = 'contributif'; // Valeur par défaut
    }
    
    /**
     * Sauvegarde l'objet en base de données
     * @return bool Succès de l'opération
     */
    public function save(): bool {
        // Vérifications de validité
        if (!$this->objectif_id || !$this->demarche_id) {
            ElaskaLog::error("Tentative de sauvegarde d'une liaison objectif-démarche sans identifiants valides");
            return false;
        }
        
        if (!in_array($this->type_liaison, ['prerequis', 'contributif', 'bloquant'])) {
            $this->type_liaison = 'contributif';
        }
        
        if ($this->poids_impact < 0 || $this->poids_impact > 1) {
            $this->poids_impact = max(0, min(1, $this->poids_impact));
        }
        
        return parent::save();
    }
    
    /**
     * Supprime l'objet de la base de données
     * @return bool Succès de l'opération
     */
    public function delete(): bool {
        // Journalisation avant suppression
        ElaskaLog::info("Suppression liaison objectif {$this->objectif_id} - démarche {$this->demarche_id}");
        
        return parent::delete();
    }
    
    /**
     * Évalue l'impact de la démarche sur l'objectif
     * @return float Score d'impact (0-1)
     */
    public function evaluerImpact(): float {
        $demarche = ElaskaParticulierDemarche::findById($this->demarche_id);
        if (!$demarche) {
            return 0;
        }
        
        $score_base = $this->poids_impact;
        
        // Calcul d'impact selon le statut de la démarche
        switch ($demarche->getStatut()) {
            case 'complete':
                return $score_base;
            case 'en_cours':
                return $score_base * $demarche->getPourcentageAvancement() / 100;
            case 'bloquee':
                return $this->type_liaison == 'bloquant' ? 0 : $score_base * 0.1;
            default:
                return 0;
        }
    }
    
    /**
     * Notifie le changement de statut d'une démarche à l'objectif parent
     */
    public function notifierChangementStatut(): void {
        $objectif = ElaskaParticulierObjectif::findById($this->objectif_id);
        $demarche = ElaskaParticulierDemarche::findById($this->demarche_id);
        
        if (!$objectif || !$demarche) {
            return;
        }
        
        // Création de notification
        $notification = new ElaskaNotification();
        $notification->setType('objectif_demarche_update');
        $notification->setDestinataire($objectif->getParticulierId());
        $notification->setTitre("Mise à jour d'une démarche liée à votre objectif");
        $notification->setContenu("La démarche \"{$demarche->getTitre()}\" liée à votre objectif \"{$objectif->getTitre()}\" a été mise à jour.");
        $notification->setLien("/particulier/objectifs/view/{$objectif->getId()}");
        $notification->save();
        
        // Mise à jour du taux de progression de l'objectif
        $objectif->updateProgression();
    }
    
    /**
     * Détermine si cette démarche est bloquante pour l'objectif
     * @return bool True si bloquant
     */
    public function estBloquant(): bool {
        if ($this->type_liaison != 'bloquant') {
            return false;
        }
        
        $demarche = ElaskaParticulierDemarche::findById($this->demarche_id);
        return $demarche && $demarche->getStatut() == 'bloquee';
    }
    
    // Getters et setters
    
    public function getId(): int {
        return $this->id;
    }
    
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }
    
    public function getObjectifId(): int {
        return $this->objectif_id;
    }
    
    public function setObjectifId(int $objectif_id): self {
        $this->objectif_id = $objectif_id;
        return $this;
    }
    
    public function getDemarcheId(): int {
        return $this->demarche_id;
    }
    
    public function setDemarcheId(int $demarche_id): self {
        $this->demarche_id = $demarche_id;
        return $this;
    }
    
    public function getTypeLiaison(): string {
        return $this->type_liaison;
    }
    
    public function setTypeLiaison(string $type_liaison): self {
        $this->type_liaison = $type_liaison;
        return $this;
    }
    
    public function getPoidsImpact(): float {
        return $this->poids_impact;
    }
    
    public function setPoidsImpact(float $poids_impact): self {
        $this->poids_impact = max(0, min(1, $poids_impact));
        return $this;
    }
    
    public function getDescription(): string {
        return $this->description;
    }
    
    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }
    
    public function getDateCreation(): DateTime {
        return $this->date_creation;
    }
    
    public function setDateCreation(DateTime $date_creation): self {
        $this->date_creation = $date_creation;
        return $this;
    }
    
    /**
     * Récupère l'instance de l'objectif associé
     * @return ElaskaParticulierObjectif|null
     */
    public function getObjectif(): ?ElaskaParticulierObjectif {
        return ElaskaParticulierObjectif::findById($this->objectif_id);
    }
    
    /**
     * Récupère l'instance de la démarche associée
     * @return ElaskaParticulierDemarche|null
     */
    public function getDemarche(): ?ElaskaParticulierDemarche {
        return ElaskaParticulierDemarche::findById($this->demarche_id);
    }
}
