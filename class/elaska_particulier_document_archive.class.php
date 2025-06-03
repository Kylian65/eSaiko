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
dol_include_once('/elaska/class/elaska_document.class.php');

/**
 * Gestion améliorée de l'archivage des documents
 * 
 * @package    Elaska
 * @subpackage Particuliers
 */
class ElaskaDocumentArchive extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */
    public $element = 'elaska_document_archive';
    
    /**
     * @var string Name of table without prefix where object is stored
     */
    public $table_element = 'elaska_document_archive';
    
    /**
     * @var string    Name of subtable line
     */
    public $table_element_line = '';
    
    /**
     * @var string Field with ID of parent key if this field has a parent
     */
    public $fk_element = '';
    
    /**
     * @var string String with name of icon for elaska_document_archive
     */
    public $picto = 'archive';
    
    /**
     * Types de raisons d'archivage
     */
    const RAISON_MANUEL = 'manuel';
    const RAISON_AUTOMATIQUE = 'automatique';
    const RAISON_PERIME = 'perime';
    const RAISON_REMPLACE = 'remplace';
    
    /**
     * @var int    ID
     */
    public $id;
    
    /**
     * @var int    Document ID
     */
    public $document_id;
    
    /**
     * @var int    Particulier ID
     */
    public $particulier_id;
    
    /**
     * @var string Original title
     */
    public $titre_original;
    
    /**
     * @var string Description
     */
    public $description;
    
    /**
     * @var string Document type
     */
    public $type;
    
    /**
     * @var string Document sub-type
     */
    public $sous_type;
    
    /**
     * @var string Metadata (JSON)
     */
    public $meta_donnees;
    
    /**
     * @var string File path
     */
    public $chemin_fichier;
    
    /**
     * @var int    File size in bytes
     */
    public $taille_fichier;
    
    /**
     * @var string Archive date
     */
    public $date_archivage;
    
    /**
     * @var string Archive reason
     */
    public $raison_archivage;
    
    /**
     * @var int    Retention period in months
     */
    public $duree_conservation;
    
    /**
     * @var string Expiration date
     */
    public $date_expiration;
    
    /**
     * @var int    Confidentiality level (1-5)
     */
    public $niveau_confidentialite;
    
    /**
     * @var int    Is restorable
     */
    public $est_restaurable;
    
    /**
     * @var string Checksum for integrity verification
     */
    public $checksum;
    
    /**
     * @var array   List of child tables
     */
    protected $childtables = array();
    
    /**
     * @var array   List of child tables (key is the name of the child table)
     */
    protected $childtablesoncascade = array();
    
    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->date_archivage = dol_now();
        $this->raison_archivage = self::RAISON_MANUEL;
        $this->duree_conservation = 60; // 5 ans par défaut
        $this->niveau_confidentialite = 3; // Moyen par défaut
        $this->est_restaurable = 1;
        
        // Date d'expiration par défaut (5 ans)
        $this->date_expiration = dol_time_plus_duree($this->date_archivage, $this->duree_conservation, 'm');
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
        
        // Vérifications
        if (empty($this->document_id) || empty($this->particulier_id) || empty($this->titre_original)) {
            $this->error = $langs->trans('ArchiveIncomplete');
            $this->errors[] = $this->error;
            dol_syslog(__METHOD__ . ' ' . $this->error, LOG_ERR);
            return -1;
        }
        
        // Vérifier que le chemin du fichier est valide
        if (!file_exists($this->chemin_fichier)) {
            $this->error = $langs->trans('FileNotExists', $this->chemin_fichier);
            $this->errors[] = $this->error;
            dol_syslog(__METHOD__ . ' ' . $this->error, LOG_ERR);
            return -2;
        }
        
        // JSON encodage des métadonnées si nécessaire
        if (is_array($this->meta_donnees)) {
            $this->meta_donnees = json_encode($this->meta_donnees);
        }
        
        // Calcul du checksum si vide
        if (empty($this->checksum)) {
            $this->checksum = hash_file('sha256', $this->chemin_fichier);
        }
        
        return $this->createCommon($user, $notrigger);
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
        
        if ($result > 0 && !empty($this->meta_donnees)) {
            // Decoder les métadonnées JSON
            $decoded = json_decode($this->meta_donnees, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->meta_donnees = $decoded;
            }
        }
        
        return $result;
    }
    
    /**
     * Archive un document existant
     * 
     * @param ElaskaDocument $document          Document à archiver
     * @param string         $raison            Raison de l'archivage
     * @param bool           $supprimer_original Si true, marque le document original comme archivé
     * @return ElaskaDocumentArchive|null       Archive créée ou null en cas d'erreur
     */
    public static function archiverDocument(ElaskaDocument $document, $raison = self::RAISON_MANUEL, $supprimer_original = true)
    {
        global $db, $user, $conf, $langs;
        
        // Chemin du fichier
        $chemin_fichier = $document->chemin_fichier;
        
        // Vérifier que le fichier existe
        if (!file_exists($chemin_fichier)) {
            dol_syslog("Le fichier à archiver n'existe pas: " . $chemin_fichier, LOG_ERR);
            return null;
        }
        
        // Créer le dossier d'archives si nécessaire
        $dossier_archive = $conf->elaska->dir_output . '/archives/' . date('Y/m', dol_now());
        if (!dol_mkdir($dossier_archive)) {
            dol_syslog("Échec de création du dossier d'archives: " . $dossier_archive, LOG_ERR);
            return null;
        }
        
        // Nom du fichier archivé
        $nom_fichier = 'archive_' . $document->id . '_' . dol_print_date(dol_now(), 'dayhourlog') . '_' . basename($chemin_fichier);
        $chemin_archive = $dossier_archive . '/' . $nom_fichier;
        
        // Copier le fichier dans les archives
        if (!dol_copy($chemin_fichier, $chemin_archive)) {
            dol_syslog("Échec de la copie du fichier vers les archives: " . $chemin_archive, LOG_ERR);
            return null;
        }
        
        // Créer l'archive
        $archive = new ElaskaDocumentArchive($db);
        $archive->document_id = $document->id;
        $archive->particulier_id = $document->particulier_id;
        $archive->titre_original = $document->titre;
        $archive->description = $document->description;
        $archive->type = $document->type;
        $archive->sous_type = $document->sous_type;
        $archive->meta_donnees = $document->meta_donnees;
        $archive->chemin_fichier = $chemin_archive;
        $archive->taille_fichier = filesize($chemin_archive);
        $archive->raison_archivage = $raison;
        
        // Durée de conservation selon le type de document
        $durees_conservation = self::getDureesConservationParDefaut();
        if (isset($durees_conservation[$document->type])) {
            $archive->duree_conservation = $durees_conservation[$document->type];
        }
        
        // Calcul de la date d'expiration
        $archive->date_expiration = dol_time_plus_duree(dol_now(), $archive->duree_conservation, 'm');
        
        // Niveau de confidentialité
        $archive->niveau_confidentialite = $document->niveau_confidentialite;
        
        // Checksum pour vérification d'intégrité
        $archive->checksum = hash_file('sha256', $chemin_archive);
        
        // Sauvegarder l'archive
        $result = $archive->create($user);
        if ($result > 0) {
            dol_syslog("Document #" . $document->id . " archivé avec succès");
            
            // Marquer le document original comme archivé si demandé
            if ($supprimer_original) {
                $document->est_archive = 1;
                $document->update($user);
            }
            
            return $archive;
        }
        
        // En cas d'erreur, supprimer le fichier copié
        @unlink($chemin_archive);
        dol_syslog("Échec de la création de l'archive pour le document #" . $document->id, LOG_ERR);
        
        return null;
    }
    
    /**
     * Restaure un document archivé
     * 
     * @return ElaskaDocument|null Document restauré ou null en cas d'erreur
     */
    public function restaurerDocument()
    {
        global $db, $user, $conf, $langs;
        
        // Vérifier si l'archive est restaurable
        if (!$this->est_restaurable) {
            dol_syslog("Tentative de restauration d'une archive non restaurable: #" . $this->id, LOG_ERR);
            return null;
        }
        
        // Vérifier l'existence du fichier archivé
        if (!file_exists($this->chemin_fichier)) {
            dol_syslog("Le fichier archivé n'existe pas: " . $this->chemin_fichier, LOG_ERR);
            return null;
        }
        
        // Vérifier l'intégrité du fichier
        $checksum_actuel = hash_file('sha256', $this->chemin_fichier);
        if ($checksum_actuel !== $this->checksum) {
            dol_syslog("L'intégrité du fichier archivé est compromise: " . $this->chemin_fichier, LOG_ERR);
            return null;
        }
        
        // Vérifier si le document original existe toujours
        $document_original = new ElaskaDocument($db);
        $result = $document_original->fetch($this->document_id);
        
        if ($result > 0) {
            // Le document existe, le mettre à jour
            $document_original->est_archive = 0;
            $document_original->date_modification = dol_now();
            
            // Si le fichier original n'existe plus, le restaurer
            if (!file_exists($document_original->chemin_fichier)) {
                // Créer le dossier si nécessaire
                $dossier_destination = dirname($document_original->chemin_fichier);
                if (!dol_mkdir($dossier_destination)) {
                    dol_syslog("Échec de création du dossier: " . $dossier_destination, LOG_ERR);
                    return null;
                }
                
                // Copier le fichier archivé
                dol_copy($this->chemin_fichier, $document_original->chemin_fichier);
            }
            
            if ($document_original->update($user) > 0) {
                dol_syslog("Document #" . $this->document_id . " restauré depuis l'archive #" . $this->id);
                return $document_original;
            }
        } else {
            // Le document n'existe plus, en créer un nouveau
            $document = new ElaskaDocument($db);
            $document->particulier_id = $this->particulier_id;
            $document->titre = $this->titre_original;
            $document->description = $this->description;
            $document->type = $this->type;
            $document->sous_type = $this->sous_type;
            
            // Métadonnées
            if (is_string($this->meta_donnees)) {
                $document->meta_donnees = json_decode($this->meta_donnees, true) ?? array();
            } else {
                $document->meta_donnees = $this->meta_donnees;
            }
            
            // Créer le dossier pour le nouveau fichier
            $dossier_documents = $conf->elaska->dir_output . '/documents/' . date('Y/m', dol_now());
            if (!dol_mkdir($dossier_documents)) {
                dol_syslog("Échec de création du dossier: " . $dossier_documents, LOG_ERR);
                return null;
            }
            
            // Nom du fichier restauré
            $nom_fichier = 'restaure_' . dol_print_date(dol_now(), 'dayhourlog') . '_' . basename($this->chemin_fichier);
            $chemin_document = $dossier_documents . '/' . $nom_fichier;
            
            // Copier le fichier
            if (dol_copy($this->chemin_fichier, $chemin_document)) {
                $document->chemin_fichier = $chemin_document;
                $document->taille_fichier = $this->taille_fichier;
                $document->niveau_confidentialite = $this->niveau_confidentialite;
                $document->date_creation = dol_now();
                
                $result = $document->create($user);
                if ($result > 0) {
                    dol_syslog("Nouveau document créé depuis l'archive #" . $this->id . ": Document #" . $document->id);
                    return $document;
                }
            } else {
                dol_syslog("Échec de la copie du fichier archivé lors de la restauration: " . $this->chemin_fichier, LOG_ERR);
            }
        }
        
        return null;
    }
    
    /**
     * Archive automatiquement les documents selon les critères
     * 
     * @param array $criteres Critères de sélection des documents
     * @return int Nombre de documents archivés
     */
    public static function archivageAutomatique($criteres = array())
    {
        global $db, $user, $conf, $langs;
        
        $criteres_defaut = array(
            'est_archive' => false,
            'est_valide' => true,
            'date_creation_before' => dol_time_plus_duree(dol_now(), -2, 'y')
        );
        
        $criteres = array_merge($criteres_defaut, $criteres);
        
        // Récupérer les documents à archiver
        $document = new ElaskaDocument($db);
        $documents = $document->fetchAll('', '', 0, 0, $criteres);
        
        if (empty($documents) || !is_array($documents)) {
            dol_syslog("Aucun document à archiver automatiquement", LOG_INFO);
            return 0;
        }
        
        $count = 0;
        foreach ($documents as $doc) {
            if (self::archiverDocument($doc, self::RAISON_AUTOMATIQUE, true)) {
                $count++;
            }
        }
        
        dol_syslog("Archivage automatique: $count documents archivés");
        return $count;
    }
    
    /**
     * Archive les documents périmés
     * 
     * @return int Nombre de documents archivés
     */
    public static function archiverDocumentsPerimes()
    {
        global $db, $user, $conf, $langs;
        
        $criteres = array(
            'est_archive' => false,
            'date_echeance_before' => dol_print_date(dol_now(), 'dayrfc'),
            'date_echeance_not_null' => true
        );
        
        // Récupérer les documents périmés
        $document = new ElaskaDocument($db);
        $documents = $document->fetchAll('', '', 0, 0, $criteres);
        
        if (empty($documents) || !is_array($documents)) {
            dol_syslog("Aucun document périmé à archiver", LOG_INFO);
            return 0;
        }
        
        $count = 0;
        foreach ($documents as $doc) {
            if (self::archiverDocument($doc, self::RAISON_PERIME, true)) {
                $count++;
            }
        }
        
        dol_syslog("Archivage des documents périmés: $count documents archivés");
        return $count;
    }
    
    /**
     * Supprime définitivement les archives expirées
     * 
     * @return int Nombre d'archives supprimées
     */
    public static function supprimerArchivesExpirees()
    {
        global $db, $user, $conf, $langs;
        
        $criteres = array(
            'date_expiration_before' => dol_print_date(dol_now(), 'dayrfc'),
            'date_expiration_not_null' => true
        );
        
        // Récupérer les archives expirées
        $archive = new ElaskaDocumentArchive($db);
        $archives = $archive->fetchAll('', '', 0, 0, $criteres);
        
        if (empty($archives) || !is_array($archives)) {
            dol_syslog("Aucune archive expirée à supprimer", LOG_INFO);
            return 0;
        }
        
        $count = 0;
        foreach ($archives as $archive) {
            // Supprimer le fichier
            if (file_exists($archive->chemin_fichier)) {
                @unlink($archive->chemin_fichier);
            }
            
            // Supprimer l'archive de la base de données
            if ($archive->delete($user) > 0) {
                $count++;
            }
        }
        
        dol_syslog("Suppression des archives expirées: $count archives supprimées");
        return $count;
    }
    
    /**
     * Vérifie l'intégrité de toutes les archives
     * 
     * @return array Résultats de la vérification
     */
    public static function verifierIntegriteArchives()
    {
        global $db, $user, $conf, $langs;
        
        $archive = new ElaskaDocumentArchive($db);
        $archives = $archive->fetchAll();
        
        $resultats = array(
            'total' => is_array($archives) ? count($archives) : 0,
            'valides' => 0,
            'corrompues' => 0,
            'manquantes' => 0,
            'details' => array()
        );
        
        if (!is_array($archives) || empty($archives)) {
            return $resultats;
        }
        
        foreach ($archives as $archive) {
            $chemin = $archive->chemin_fichier;
            $id = $archive->id;
            
            if (!file_exists($chemin)) {
                $resultats['manquantes']++;
                $resultats['details'][] = array(
                    'id' => $id,
                    'statut' => 'manquant',
                    'message' => "Le fichier n'existe pas: $chemin"
                );
                continue;
            }
            
            $checksum_actuel = hash_file('sha256', $chemin);
            if ($checksum_actuel !== $archive->checksum) {
                $resultats['corrompues']++;
                $resultats['details'][] = array(
                    'id' => $id,
                    'statut' => 'corrompu',
                    'message' => "Checksum différent pour l'archive #$id"
                );
                continue;
            }
            
            $resultats['valides']++;
            $resultats['details'][] = array(
                'id' => $id,
                'statut' => 'valide',
                'message' => "Archive #$id intègre"
            );
        }
        
        return $resultats;
    }
    
    /**
     * Génère des statistiques sur les archives
     * 
     * @param int|null $particulier_id ID du particulier ou null pour tous
     * @return array Statistiques
     */
    public static function getStatistiquesArchives($particulier_id = null)
    {
        global $db, $user, $conf, $langs;
        
        $criteres = array();
        if ($particulier_id !== null) {
            $criteres['particulier_id'] = $particulier_id;
        }
        
        $archive = new ElaskaDocumentArchive($db);
        $archives = $archive->fetchAll('', '', 0, 0, $criteres);
        
        $stats = array(
            'total' => is_array($archives) ? count($archives) : 0,
            'par_type' => array(),
            'par_raison' => array(),
            'par_annee' => array(),
            'taille_totale' => 0,
            'duree_conservation_moyenne' => 0,
            'niveau_confidentialite_moyen' => 0
        );
        
        if ($stats['total'] === 0) {
            return $stats;
        }
        
        $duree_totale = 0;
        $confidentialite_totale = 0;
        
        foreach ($archives as $archive) {
            // Statistiques par type
            $type = $archive->type;
            if (!isset($stats['par_type'][$type])) {
                $stats['par_type'][$type] = 0;
            }
            $stats['par_type'][$type]++;
            
            // Statistiques par raison
            $raison = $archive->raison_archivage;
            if (!isset($stats['par_raison'][$raison])) {
                $stats['par_raison'][$raison] = 0;
            }
            $stats['par_raison'][$raison]++;
            
            // Statistiques par année
            $annee = dol_print_date($archive->date_archivage, '%Y');
            if (!isset($stats['par_annee'][$annee])) {
                $stats['par_annee'][$annee] = 0;
            }
            $stats['par_annee'][$annee]++;
            
            // Taille totale
            $stats['taille_totale'] += $archive->taille_fichier;
            
            // Durée totale
            $duree_totale += $archive->duree_conservation;
            
            // Confidentialité totale
            $confidentialite_totale += $archive->niveau_confidentialite;
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
     * 
     * @return array Durées de conservation en mois
     */
    public static function getDureesConservationParDefaut()
    {
        return array(
            'facture' => 120, // 10 ans
            'contrat' => 120, // 10 ans
            'bulletin_salaire' => 600, // 50 ans
            'declaration_fiscale' => 72, // 6 ans
            'piece_identite' => 120, // 10 ans
            'assurance' => 36, // 3 ans après expiration
            'sante' => 120, // 10 ans
            'correspondance' => 36, // 3 ans
            'divers' => 60, // 5 ans
        );
    }
    
    /**
     * Formate une taille de fichier en octets en format lisible
     * 
     * @param int $taille Taille en octets
     * @return string Taille formatée
     */
    private static function formatTailleFichier($taille)
    {
        return dol_print_size($taille);
    }
    
    /**
     * Marque une archive comme non restaurable
     * 
     * @return bool Succès de l'opération
     */
    public function marquerNonRestaurable()
    {
        global $user;
        
        $this->est_restaurable = 0;
        return $this->update($user) > 0;
    }
    
    /**
     * Prolonge la durée de conservation d'une archive
     * 
     * @param int $duree_supplementaire Durée supplémentaire en mois
     * @return bool Succès de l'opération
     */
    public function prolongerConservation($duree_supplementaire)
    {
        global $user;
        
        if ($duree_supplementaire <= 0) {
            return false;
        }
        
        $this->duree_conservation += $duree_supplementaire;
        
        // Mise à jour de la date d'expiration
        if (!empty($this->date_expiration)) {
            $this->date_expiration = dol_time_plus_duree($this->date_expiration, $duree_supplementaire, 'm');
        }
        
        return $this->update($user) > 0;
    }
    
    /**
     * Exporte les métadonnées de l'archive au format JSON
     * 
     * @return string JSON des métadonnées
     */
    public function exporterMetadonnees()
    {
        $metadata = array(
            'id' => $this->id,
            'document_id' => $this->document_id,
            'particulier_id' => $this->particulier_id,
            'titre' => $this->titre_original,
            'description' => $this->description,
            'type' => $this->type,
            'sous_type' => $this->sous_type,
            'date_archivage' => dol_print_date($this->date_archivage, 'dayhourtext'),
            'raison_archivage' => $this->raison_archivage,
            'duree_conservation' => $this->duree_conservation,
            'date_expiration' => !empty($this->date_expiration) ? dol_print_date($this->date_expiration, 'dayhourtext') : null,
            'niveau_confidentialite' => $this->niveau_confidentialite,
            'est_restaurable' => $this->est_restaurable,
            'taille_fichier' => $this->taille_fichier,
            'taille_formatee' => self::formatTailleFichier($this->taille_fichier),
            'checksum' => $this->checksum
        );
        
        // Ajouter les métadonnées spécifiques
        if (is_string($this->meta_donnees)) {
            $meta = json_decode($this->meta_donnees, true) ?? array();
            $metadata['meta_donnees'] = $meta;
        } else {
            $metadata['meta_donnees'] = $this->meta_donnees;
        }
        
        return json_encode($metadata, JSON_PRETTY_PRINT);
    }
}
