<?php
/**
 * Gestion des suggestions automatisées d'objectifs
 * 
 * @package Elaska
 * @subpackage Particuliers
 * @author Elaska Dev Team
 * @version 4.5.0
 */
class ElaskaParticulierObjectifSuggestion extends ElaskaModel {
    /**
     * Nom de la table en base de données
     * @var string
     */
    protected static $table_name = 'elaska_particulier_objectif_suggestion';
    
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
        'id', 'particulier_id', 'titre', 'description', 'categorie',
        'priorite', 'meta_donnees', 'est_accepte', 'est_rejete',
        'date_creation', 'date_modification'
    ];
    
    /**
     * ID unique de la suggestion
     * @var int
     */
    private int $id;
    
    /**
     * ID du particulier concerné
     * @var int
     */
    private int $particulier_id;
    
    /**
     * Titre de la suggestion d'objectif
     * @var string
     */
    private string $titre;
    
    /**
     * Description détaillée
     * @var string
     */
    private string $description;
    
    /**
     * Catégorie de l'objectif suggéré
     * @var string
     */
    private string $categorie;
    
    /**
     * Niveau de priorité (1-5)
     * @var int
     */
    private int $priorite;
    
    /**
     * Métadonnées complémentaires (JSON)
     * @var array
     */
    private array $meta_donnees = [];
    
    /**
     * Indique si la suggestion a été acceptée
     * @var bool
     */
    private bool $est_accepte = false;
    
    /**
     * Indique si la suggestion a été rejetée
     * @var bool
     */
    private bool $est_rejete = false;
    
    /**
     * Date de création
     * @var DateTime
     */
    private DateTime $date_creation;
    
    /**
     * Date de dernière modification
     * @var DateTime
     */
    private DateTime $date_modification;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->date_creation = new DateTime();
        $this->date_modification = new DateTime();
        $this->priorite = 3; // Priorité moyenne par défaut
    }
    
    /**
     * Sauvegarde l'objet en base de données
     * @return bool Succès de l'opération
     */
    public function save(): bool {
        // Validation des données
        if (empty($this->titre) || empty($this->particulier_id)) {
            ElaskaLog::error("Tentative de sauvegarde d'une suggestion d'objectif sans titre ou particulier");
            return false;
        }
        
        $this->date_modification = new DateTime();
        
        // Encodage des métadonnées en JSON
        if (!empty($this->meta_donnees)) {
            $this->meta_donnees = json_encode($this->meta_donnees);
        } else {
            $this->meta_donnees = '{}';
        }
        
        $result = parent::save();
        
        // Restauration du tableau après sauvegarde
        if (is_string($this->meta_donnees)) {
            $this->meta_donnees = json_decode($this->meta_donnees, true) ?? [];
        }
        
        return $result;
    }
    
    /**
     * Convertit la suggestion en objectif réel
     * @return ElaskaParticulierObjectif|null L'objectif créé ou null en cas d'erreur
     */
    public function convertirEnObjectif(): ?ElaskaParticulierObjectif {
        if ($this->est_rejete) {
            ElaskaLog::warning("Tentative de conversion d'une suggestion rejetée en objectif");
            return null;
        }
        
        $objectif = new ElaskaParticulierObjectif();
        $objectif->setParticulierId($this->particulier_id);
        $objectif->setTitre($this->titre);
        $objectif->setDescription($this->description);
        $objectif->setCategorie($this->categorie);
        $objectif->setPriorite($this->priorite);
        
        // Date d'échéance: 3 mois par défaut
        $echeance = new DateTime();
        $echeance->modify('+3 months');
        $objectif->setDateEcheance($echeance);
        
        // Statut initial
        $objectif->setStatut('actif');
        $objectif->setProgression(0);
        
        if ($objectif->save()) {
            // Marquer la suggestion comme acceptée
            $this->est_accepte = true;
            $this->save();
            
            ElaskaLog::info("Suggestion d'objectif #{$this->id} convertie en objectif #{$objectif->getId()}");
            return $objectif;
        }
        
        ElaskaLog::error("Échec de la conversion de la suggestion d'objectif #{$this->id}");
        return null;
    }
    
    /**
     * Rejette définitivement la suggestion
     */
    public function rejetDefinitif(): void {
        $this->est_rejete = true;
        $this->save();
        ElaskaLog::info("Suggestion d'objectif #{$this->id} rejetée définitivement");
    }
    
    /**
     * Détermine si la suggestion est pertinente pour le particulier
     * @return bool True si pertinent
     */
    public function estPertinent(): bool {
        // Si déjà traité, pas pertinent
        if ($this->est_accepte || $this->est_rejete) {
            return false;
        }
        
        // Vérifier si un objectif similaire existe déjà
        $objectifs_existants = ElaskaParticulierObjectif::findAllBy([
            'particulier_id' => $this->particulier_id,
            'statut' => ['actif', 'en_pause']
        ]);
        
        foreach ($objectifs_existants as $objectif) {
            // Similarité de titre (utilisation de la fonction de similarité de texte)
            $similarite = ElaskaTextUtils::calculateSimilarity(
                $this->titre, 
                $objectif->getTitre()
            );
            
            if ($similarite > 0.8) {
                return false; // Trop similaire à un objectif existant
            }
        }
        
        return true;
    }
    
    /**
     * Calcule un score de pertinence pour la suggestion
     * @return float Score (0-1)
     */
    public function calculerScoreRelevance(): float {
        $score = 0.5; // Score de base
        
        // Ajustement selon la priorité
        $score += ($this->priorite - 3) * 0.1;
        
        // Ajustement selon les métadonnées
        if (isset($this->meta_donnees['source_confiance'])) {
            $score += $this->meta_donnees['source_confiance'] * 0.2;
        }
        
        if (isset($this->meta_donnees['pourcentage_match'])) {
            $score += $this->meta_donnees['pourcentage_match'] * 0.3;
        }
        
        // Limiter le score entre 0 et 1
        return max(0, min(1, $score));
    }
    
    // Getters et setters
    
    public function getId(): int {
        return $this->id;
    }
    
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }
    
    public function getParticulierId(): int {
        return $this->particulier_id;
    }
    
    public function setParticulierId(int $particulier_id): self {
        $this->particulier_id = $particulier_id;
        return $this;
    }
    
    public function getTitre(): string {
        return $this->titre;
    }
    
    public function setTitre(string $titre): self {
        $this->titre = $titre;
        return $this;
    }
    
    public function getDescription(): string {
        return $this->description;
    }
    
    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }
    
    public function getCategorie(): string {
        return $this->categorie;
    }
    
    public function setCategorie(string $categorie): self {
        $this->categorie = $categorie;
        return $this;
    }
    
    public function getPriorite(): int {
        return $this->priorite;
    }
    
    public function setPriorite(int $priorite): self {
        $this->priorite = max(1, min(5, $priorite));
        return $this;
    }
    
    public function getMetaDonnees(): array {
        // S'assurer que les métadonnées sont un tableau
        if (is_string($this->meta_donnees)) {
            return json_decode($this->meta_donnees, true) ?? [];
        }
        return $this->meta_donnees;
    }
    
    public function setMetaDonnees(array $meta_donnees): self {
        $this->meta_donnees = $meta_donnees;
        return $this;
    }
    
    public function estAccepte(): bool {
        return $this->est_accepte;
    }
    
    public function setEstAccepte(bool $est_accepte): self {
        $this->est_accepte = $est_accepte;
        return $this;
    }
    
    public function estRejete(): bool {
        return $this->est_rejete;
    }
    
    public function setEstRejete(bool $est_rejete): self {
        $this->est_rejete = $est_rejete;
        return $this;
    }
    
    public function getDateCreation(): DateTime {
        return $this->date_creation;
    }
    
    public function setDateCreation(DateTime $date_creation): self {
        $this->date_creation = $date_creation;
        return $this;
    }
    
    public function getDateModification(): DateTime {
        return $this->date_modification;
    }
    
    public function setDateModification(DateTime $date_modification): self {
        $this->date_modification = $date_modification;
        return $this;
    }
}
