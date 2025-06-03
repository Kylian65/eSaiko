<?php
/**
 * Gestion améliorée de l'archivage des documents
 * 
 * @package Elaska
 * @subpackage Particuliers
 * @author Elaska Dev Team
 * @version 4.5.0
 */
class ElaskaDocumentArchive extends ElaskaModel {
    /**
     * Nom de la table en base de données
     * @var string
     */
    protected static $table_name = 'elaska_document_archive';
    
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
        'id', 'document_id', 'particulier_id', 'titre_original', 
        'description', 'type', 'sous_type', 'meta_donnees',
        'chemin_fichier', 'taille_fichier', 'date_archivage',
        'raison_archivage', 'duree_conservation', 'date_expiration',
        'niveau_confidentialite', 'est_restaurable', 'checksum'
    ];
    
    /**
     * Types de raisons d'archivage
     */
    const RAISON_MANUEL = 'manuel';
    const RAISON_AUTOMATIQUE = 'automatique';
    const RAISON_PERIME = 'perime';
    const RAISON_REMPLACE = 'remplace';
    
    /**
     * ID unique de l'archive
     * @var int
     */
    private int $id;
    
    /**
     * ID du document archivé
     * @var int
     */
    private int $document_id;
    
    /**
     * ID du particulier
     * @var int
     */
    private int $particulier_id;
    
    /**
     * Titre original du document
     * @var string
     */
    private string $titre_original;
    
    /**
     * Description du document
     * @var string
     */
    private string $description;
    
    /**
     * Type de document
     * @var string
     */
    private string $type;
    
    /**
     * Sous-type de document
     * @var string
     */
    private string $sous_type;
    
    /**
     * Métadonnées du document (JSON)
     * @var string
     */
    private string $meta_donnees;
    
    /**
     * Chemin du fichier archivé
     * @var string
     */
    private string $chemin_fichier;
    
    /**
     * Taille du fichier en octets
     * @var int
     */
    private int $taille_fichier;
    
    /**
     * Date d'archivage
     * @var DateTime
     */
    private DateTime $date_archivage;
    
    /**
     * Raison de l'archivage
     * @var string
     */
    private string $raison_archivage;
    
    /**
     * Durée de conservation en mois
     * @var int
     */
    private int $duree_conservation;
    
    /**
     * Date d'expiration de l'archive
     * @var DateTime|null
     */
    private ?DateTime $date_expiration;
    
    /**
     * Niveau de confidentialité (1-5)
     * @var int
     */
    private int $niveau_confidentialite;
    
    /**
     * Indique si le document peut être restauré
     * @var bool
     */
    private bool $est_restaurable;
    
    /**
     * Checksum du fichier pour vérifier l'intégrité
     * @var string
     */
    private string $checksum;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->date_archivage = new DateTime();
        $this->raison_archivage = self::RAISON_MANUEL;
        $this->duree_conservation = 60; // 5 ans par défaut
        $this->niveau_confidentialite = 3; // Moyen par défaut
        $this->est_restaurable = true;
        
        // Date d'expiration par défaut (5 ans)
        $this->date_expiration = clone $this->date_archivage;
        $this->date_expiration->modify("+{$this->duree_conservation} months");
    }
    
    /**
     * Sauvegarde l'objet en base de données
     * @return bool Succès de l'opération
     */
    public function save(): bool {
        // Vérifications
        if (empty($this->document_id) || empty($this->particulier_id) || empty($this->titre_original)) {
            ElaskaLog::error("Tentative de sauvegarde d'une archive de document incomplète");
            return false;
        }
        
        // Vérifier que le chemin du fichier est valide
        if (!file_exists($this->chemin_fichier)) {
            ElaskaLog::error("Le fichier à archiver n'existe pas: {$this->chemin_fichier}");
            return false;
        }
        
        // JSON encodage des métadonnées si nécessaire
        if (is_array($this->meta_donnees)) {
            $this->meta_donnees = json_encode($this->meta_donnees);
        }
        
        // Calcul du checksum si vide
        if (empty($this->checksum)) {
            $this->checksum = hash_file('sha256', $this->chemin_fichier);
        }
        
        return parent::save();
    }
    
    /**
     * Archive un document existant
     * @param ElaskaDocument $document Document à archiver
     * @param string $raison Raison de l'archivage
     * @param bool $supprimer_original Si true, marque le document original comme archivé
     * @return ElaskaDocumentArchive|null Archive créée ou null en cas d'erreur
     */
    public static function archiverDocument(ElaskaDocument $document, string $raison = self::RAISON_MANUEL, bool $supprimer_original = true): ?ElaskaDocumentArchive {
        // Chemin du fichier
        $chemin_fichier = $document->getCheminFichier();
        
        // Vérifier que le fichier existe
        if (!file_exists($chemin_fichier)) {
            ElaskaLog::error("Le fichier à archiver n'existe pas: {$chemin_fichier}");
            return null;
        }
        
        // Créer le dossier d'archives si nécessaire
        $dossier_archive = ELASKA_STORAGE_PATH . '/archives/' . date('Y/m');
        if (!is_dir($dossier_archive)) {
            mkdir($dossier_archive, 0755, true);
        }
        
        // Nom du fichier archivé
        $nom_fichier = 'archive_' . $document->getId() . '_' . time() . '_' . basename($chemin_fichier);
        $chemin_archive = $dossier_archive . '/' . $nom_fichier;
        
        // Copier le fichier dans les archives
        if (!copy($chemin_fichier, $chemin_archive)) {
            ElaskaLog::error("Échec de la copie du fichier vers les archives: {$chemin_archive}");
            return null;
        }
        
        // Créer l'archive
        $archive = new ElaskaDocumentArchive();
        $archive->setDocumentId($document->getId());
        $archive->setParticulierId($document->getParticulierId());
        $archive->setTitreOriginal($document->getTitre());
        $archive->setDescription($document->getDescription());
        $archive->setType($document->getType());
        $archive->setSousType($document->getSousType());
        $archive->setMetaDonnees($document->getMetaDonnees());
        $archive->setCheminFichier($chemin_archive);
        $archive->setTailleFichier(filesize($chemin_archive));
        $archive->setRaisonArchivage($raison);
        
        // Durée de conservation selon le type de document
        $durees_conservation = self::getDureesConservationParDefaut();
        if (isset($durees_conservation[$document->getType()])) {
            $archive->setDureeConservation($durees_conservation[$document->getType()]);
        }
        
        // Calcul de la date d'expiration
        $date_expiration = clone $archive->getDateArchivage();
        $date_expiration->modify("+{$archive->getDureeConservation()} months");
        $archive->setDateExpiration($date_expiration);
        
        // Niveau de confidentialité
        $archive->setNiveauConfidentialite($document->getNiveauConfidentialite());
        
        // Checksum pour vérification d'intégrité
        $archive->setChecksum(hash_file('sha256', $chemin_archive));
        
        // Sauvegarder l'archive
        if ($archive->save()) {
            ElaskaLog::info("Document #{$document->getId()} archivé avec succès");
            
            // Marquer le document original comme archivé si demandé
            if ($supprimer_original) {
                $document->setEstArchive(true);
                $document->save();
            }
            
            return $archive;
        }
        
        // En cas d'erreur, supprimer le fichier copié
        @unlink($chemin_archive);
        ElaskaLog::error("Échec de la création de l'archive pour le document #{$document->getId()}");
        
        return null;
    }
    
    /**
     * Restaure un document archivé
     * @return ElaskaDocument|null Document restauré ou null en cas d'erreur
     */
    public function restaurerDocument(): ?ElaskaDocument {
        // Vérifier si l'archive est restaurable
        if (!$this->est_restaurable) {
            ElaskaLog::error("Tentative de restauration d'une archive non restaurable: #{$this->id}");
            return null;
        }
        
        // Vérifier l'existence du fichier archivé
        if (!file_exists($this->chemin_fichier)) {
            ElaskaLog::error("Le fichier archivé n'existe pas: {$this->chemin_fichier}");
            return null;
        }
        
        // Vérifier l'intégrité du fichier
        $checksum_actuel = hash_file('sha256', $this->chemin_fichier);
        if ($checksum_actuel !== $this->checksum) {
            ElaskaLog::error("L'intégrité du fichier archivé est compromise: {$this->chemin_fichier}");
            return null;
        }
        
        // Vérifier si le document original existe toujours
        $document_original = ElaskaDocument::findById($this->document_id);
        
        if ($document_original) {
            // Le document existe, le mettre à jour
            $document_original->setEstArchive(false);
            $document_original->setDateModification(new DateTime());
            
            // Si le fichier original n'existe plus, le restaurer
            if (!file_exists($document_original->getCheminFichier())) {
                // Créer le dossier si nécessaire
                $dossier_destination = dirname($document_original->getCheminFichier());
                if (!is_dir($dossier_destination)) {
                    mkdir($dossier_destination, 0755, true);
                }
                
                // Copier le fichier archivé
                copy($this->chemin_fichier, $document_original->getCheminFichier());
            }
            
            if ($document_original->save()) {
                ElaskaLog::info("Document #{$this->document_id} restauré depuis l'archive #{$this->id}");
                return $document_original;
            }
        } else {
            // Le document n'existe plus, en créer un nouveau
            $document = new ElaskaDocument();
            $document->setParticulierId($this->particulier_id);
            $document->setTitre($this->titre_original);
            $document->setDescription($this->description);
            $document->setType($this->type);
            $document->setSousType($this->sous_type);
            
            // Métadonnées
            if (is_string($this->meta_donnees)) {
                $document->setMetaDonnees(json_decode($this->meta_donnees, true) ?? []);
            } else {
                $document->setMetaDonnees($this->meta_donnees);
            }
            
            // Créer le dossier pour le nouveau fichier
            $dossier_documents = ELASKA_STORAGE_PATH . '/documents/' . date('Y/m');
            if (!is_dir($dossier_documents)) {
                mkdir($dossier_documents, 0755, true);
            }
            
            // Nom du fichier restauré
            $nom_fichier = 'restaure_' . time() . '_' . basename($this->chemin_fichier);
            $chemin_document = $dossier_documents . '/' . $nom_fichier;
            
            // Copier le fichier
            if (copy($this->chemin_fichier, $chemin_document)) {
                $document->setCheminFichier($chemin_document);
                $document->setTailleFichier($this->taille_fichier);
                $document->setNiveauConfidentialite($this->niveau_confidentialite);
                $document->setDateCreation(new DateTime());
                
                if ($document->save()) {
                    ElaskaLog::info("Nouveau document créé depuis l'archive #{$this->id}: Document #{$document->getId()}");
                    return $document;
                }
            } else {
                ElaskaLog::error("Échec de la copie du fichier archivé lors de la restauration: {$this->chemin_fichier}");
            }
        }
        
        return null;
    }
    
    /**
     * Archive automatiquement les documents selon les critères
     * @param array $criteres Critères de sélection des documents
     * @return int Nombre de documents archivés
     */
    public static function archivageAutomatique(array $criteres = []): int {
        $criteres_defaut = [
            'est_archive' => false,
            'est_valide' => true,
            'date_creation_before' => (new DateTime('-2 years'))->format('Y-m-d')
        ];
        
        $criteres = array_merge($criteres_defaut, $criteres);
        
        // Récupérer les documents à archiver
        $documents = ElaskaDocument::findAllBy($criteres);
        
        if (empty($documents)) {
            ElaskaLog::info("Aucun document à archiver automatiquement");
            return 0;
        }
        
        $count = 0;
        foreach ($documents as $document) {
            if (self::archiverDocument($document, self::RAISON_AUTOMATIQUE, true)) {
                $count++;
            }
        }
        
        ElaskaLog::info("Archivage automatique: $count documents archivés");
        return $count;
    }
    
    /**
     * Archive les documents périmés
     * @return int Nombre de documents archivés
     */
    public static function archiverDocumentsPerimes(): int {
        $criteres = [
            'est_archive' => false,
            'date_echeance_before' => (new DateTime())->format('Y-m-d'),
            'date_echeance_not_null' => true
        ];
        
        // Récupérer les documents périmés
        $documents = ElaskaDocument::findAllBy($criteres);
        
        if (empty($documents)) {
            ElaskaLog::info("Aucun document périmé à archiver");
            return 0;
        }
        
        $count = 0;
        foreach ($documents as $document) {
            if (self::archiverDocument($document, self::RAISON_PERIME, true)) {
                $count++;
            }
        }
        
        ElaskaLog::info("Archivage des documents périmés: $count documents archivés");
        return $count;
    }
    
    /**
     * Supprime définitivement les archives expirées
     * @return int Nombre d'archives supprimées
     */
    public static function supprimerArchivesExpirees(): int {
        $criteres = [
            'date_expiration_before' => (new DateTime())->format('Y-m-d'),
            'date_expiration_not_null' => true
        ];
        
        // Récupérer les archives expirées
        $archives = self::findAllBy($criteres);
        
        if (empty($archives)) {
            ElaskaLog::info("Aucune archive expirée à supprimer");
            return 0;
        }
        
        $count = 0;
        foreach ($archives as $archive) {
            // Supprimer le fichier
            if (file_exists($archive->getCheminFichier())) {
                @unlink($archive->getCheminFichier());
            }
            
            // Supprimer l'archive de la base de données
            if ($archive->delete()) {
                $count++;
            }
        }
        
        ElaskaLog::info("Suppression des archives expirées: $count archives supprimées");
        return $count;
    }
    
    /**
     * Vérifie l'intégrité de toutes les archives
     * @return array Résultats de la vérification
     */
    public static function verifierIntegriteArchives(): array {
        $archives = self::findAll();
        
        $resultats = [
            'total' => count($archives),
            'valides' => 0,
            'corrompues' => 0,
            'manquantes' => 0,
            'details' => []
        ];
        
        foreach ($archives as $archive) {
            $chemin = $archive->getCheminFichier();
            $id = $archive->getId();
            
            if (!file_exists($chemin)) {
                $resultats['manquantes']++;
                $resultats['details'][] = [
                    'id' => $id,
                    'statut' => 'manquant',
                    'message' => "Le fichier n'existe pas: $chemin"
                ];
                continue;
            }
            
            $checksum_actuel = hash_file('sha256', $chemin);
            if ($checksum_actuel !== $archive->getChecksum()) {
                $resultats['corrompues']++;
                $resultats['details'][] = [
                    'id' => $id,
                    'statut' => 'corrompu',
                    'message' => "Checksum différent pour l'archive #$id"
                ];
                continue;
            }
            
            $resultats['valides']++;
            $resultats['details'][] = [
                'id' => $id,
                'statut' => 'valide',
                'message' => "Archive #$id intègre"
            ];
        }
        
        return $resultats;
    }
    
    /**
     * Génère des statistiques sur les archives
     * @param int|null $particulier_id ID du particulier ou null pour tous
     * @return array Statistiques
     */
    public static function getStatistiquesArchives(?int $particulier_id = null): array {
        $criteres = [];
        if ($particulier_id !== null) {
            $criteres['particulier_id'] = $particulier_id;
        }
        
        $archives = self::findAllBy($criteres);
        
        $stats = [
            'total' => count($archives),
            'par_type' => [],
            'par_raison' => [],
            'par_annee' => [],
            'taille_totale' => 0,
            'duree_conservation_moyenne' => 0,
            'niveau_confidentialite_moyen' => 0
        ];
        
        if ($stats['total'] === 0) {
            return $stats;
        }
        
        $duree_totale = 0;
        $confidentialite_totale = 0;
        
        foreach ($archives as $archive) {
            // Statistiques par type
            $type = $archive->getType();
            if (!isset($stats['par_type'][$type])) {
                $stats['par_type'][$type] = 0;
            }
            $stats['par_type'][$type]++;
            
            // Statistiques par raison
            $raison = $archive->getRaisonArchivage();
            if (!isset($stats['par_raison'][$raison])) {
                $stats['par_raison'][$raison] = 0;
            }
            $stats['par_raison'][$raison]++;
            
            // Statistiques par année
            $annee = $archive->getDateArchivage()->format('Y');
            if (!isset($stats['par_annee'][$annee])) {
                $stats['par_annee'][$annee] = 0;
            }
            $stats['par_annee'][$annee]++;
            
            // Taille totale
            $stats['taille_totale'] += $archive->getTailleFichier();
            
            // Durée totale
            $duree_totale += $archive->getDureeConservation();
            
            // Confidentialité totale
            $confidentialite_totale += $archive->getNiveauConfidentialite();
        }
        
        // Moyennes
        $stats['duree_conservation_moyenne'] = $duree_totale / $stats['total'];
        $stats['niveau_confidentialite_moyen'] = $confidentialite_totale / $stats['total'];
        
        // Taille totale formatée
        $stats['taille_totale_formatee'] = self::formatTailleFichier($stats['taille_totale']);
        
        return $stats;
    }
    
    /**
     * Retourne les durées de conservation par défaut selon le type de document
     * @return array Durées de conservation en mois
     */
    public static function getDureesConservationParDefaut(): array {
        return [
            'facture' => 120, // 10 ans
            'contrat' => 120, // 10 ans
            'bulletin_salaire' => 600, // 50 ans
            'declaration_fiscale' => 72, // 6 ans
            'piece_identite' => 120, // 10 ans
            'assurance' => 36, // 3 ans après expiration
            'sante' => 120, // 10 ans
            'correspondance' => 36, // 3 ans
            'divers' => 60, // 5 ans
        ];
    }
    
    /**
     * Formate une taille de fichier en octets en format lisible
     * @param int $taille Taille en octets
     * @return string Taille formatée
     */
    private static function formatTailleFichier(int $taille): string {
        $unites = ['o', 'Ko', 'Mo', 'Go', 'To'];
        $i = 0;
        while ($taille >= 1024 && $i < count($unites) - 1) {
            $taille /= 1024;
            $i++;
        }
        
        return round($taille, 2) . ' ' . $unites[$i];
    }
    
    // Getters et setters
    
    public function getId(): int {
        return $this->id;
    }
    
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }
    
    public function getDocumentId(): int {
        return $this->document_id;
    }
    
    public function setDocumentId(int $document_id): self {
        $this->document_id = $document_id;
        return $this;
    }
    
    public function getParticulierId(): int {
        return $this->particulier_id;
    }
    
    public function setParticulierId(int $particulier_id): self {
        $this->particulier_id = $particulier_id;
        return $this;
    }
    
    public function getTitreOriginal(): string {
        return $this->titre_original;
    }
    
    public function setTitreOriginal(string $titre_original): self {
        $this->titre_original = $titre_original;
        return $this;
    }
    
    public function getDescription(): string {
        return $this->description;
    }
    
    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }
    
    public function getType(): string {
        return $this->type;
    }
    
    public function setType(string $type): self {
        $this->type = $type;
        return $this;
    }
    
    public function getSousType(): string {
        return $this->sous_type;
    }
    
    public function setSousType(string $sous_type): self {
        $this->sous_type = $sous_type;
        return $this;
    }
    
    public function getMetaDonnees() {
        // Si chaîne JSON, la décoder
        if (is_string($this->meta_donnees)) {
            return json_decode($this->meta_donnees, true) ?? [];
        }
        return $this->meta_donnees;
    }
    
    public function setMetaDonnees($meta_donnees): self {
        $this->meta_donnees = $meta_donnees;
        return $this;
    }
    
    public function getCheminFichier(): string {
        return $this->chemin_fichier;
    }
    
    public function setCheminFichier(string $chemin_fichier): self {
        $this->chemin_fichier = $chemin_fichier;
        return $this;
    }
    
    public function getTailleFichier(): int {
        return $this->taille_fichier;
    }
    
    public function setTailleFichier(int $taille_fichier): self {
        $this->taille_fichier = $taille_fichier;
        return $this;
    }
    
    public function getDateArchivage(): DateTime {
        return $this->date_archivage;
    }
    
    public function setDateArchivage(DateTime $date_archivage): self {
        $this->date_archivage = $date_archivage;
        return $this;
    }
    
    public function getRaisonArchivage(): string {
        return $this->raison_archivage;
    }
    
    public function setRaisonArchivage(string $raison_archivage): self {
        $this->raison_archivage = $raison_archivage;
        return $this;
    }
    
    public function getDureeConservation(): int {
        return $this->duree_conservation;
    }
    
    public function setDureeConservation(int $duree_conservation): self {
        $this->duree_conservation = $duree_conservation;
        
        // Mettre à jour la date d'expiration
        if ($this->date_archivage) {
            $this->date_expiration = clone $this->date_archivage;
            $this->date_expiration->modify("+{$duree_conservation} months");
        }
        
        return $this;
    }
    
    public function getDateExpiration(): ?DateTime {
        return $this->date_expiration;
    }
    
    public function setDateExpiration(?DateTime $date_expiration): self {
        $this->date_expiration = $date_expiration;
        return $this;
    }
    
    public function getNiveauConfidentialite(): int {
        return $this->niveau_confidentialite;
    }
    
    public function setNiveauConfidentialite(int $niveau_confidentialite): self {
        $this->niveau_confidentialite = max(1, min(5, $niveau_confidentialite));
        return $this;
    }
    
    public function estRestaurable(): bool {
        return $this->est_restaurable;
    }
    
    public function setEstRestaurable(bool $est_restaurable): self {
        $this->est_restaurable = $est_restaurable;
        return $this;
    }
    
    public function getChecksum(): string {
        return $this->checksum;
    }
    
    public function setChecksum(string $checksum): self {
        $this->checksum = $checksum;
        return $this;
    }
    
    /**
     * Marque une archive comme non restaurable
     * @return bool Succès de l'opération
     */
    public function marquerNonRestaurable(): bool {
        $this->est_restaurable = false;
        return $this->save();
    }
    
    /**
     * Prolonge la durée de conservation d'une archive
     * @param int $duree_supplementaire Durée supplémentaire en mois
     * @return bool Succès de l'opération
     */
    public function prolongerConservation(int $duree_supplementaire): bool {
        if ($duree_supplementaire <= 0) {
            return false;
        }
        
        $this->duree_conservation += $duree_supplementaire;
        
        // Mise à jour de la date d'expiration
        if ($this->date_expiration) {
            $this->date_expiration->modify("+{$duree_supplementaire} months");
        }
        
        return $this->save();
    }
    
    /**
     * Exporte les métadonnées de l'archive au format JSON
     * @return string JSON des métadonnées
     */
    public function exporterMetadonnees(): string {
        $metadata = [
            'id' => $this->id,
            'document_id' => $this->document_id,
            'particulier_id' => $this->particulier_id,
            'titre' => $this->titre_original,
            'description' => $this->description,
            'type' => $this->type,
            'sous_type' => $this->sous_type,
            'date_archivage' => $this->date_archivage->format('Y-m-d H:i:s'),
            'raison_archivage' => $this->raison_archivage,
            'duree_conservation' => $this->duree_conservation,
            'date_expiration' => $this->date_expiration ? $this->date_expiration->format('Y-m-d H:i:s') : null,
            'niveau_confidentialite' => $this->niveau_confidentialite,
            'est_restaurable' => $this->est_restaurable,
            'taille_fichier' => $this->taille_fichier,
            'taille_formatee' => self::formatTailleFichier($this->taille_fichier),
            'checksum' => $this->checksum
        ];
        
        // Ajouter les métadonnées spécifiques
        if (is_string($this->meta_donnees)) {
            $meta = json_decode($this->meta_donnees, true) ?? [];
            $metadata['meta_donnees'] = $meta;
        } else {
            $metadata['meta_donnees'] = $this->meta_donnees;
        }
        
        return json_encode($metadata, JSON_PRETTY_PRINT);
    }
}
