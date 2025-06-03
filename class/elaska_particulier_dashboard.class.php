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

/**
 * Classe de gestion du tableau de bord administratif pour les particuliers
 * 
 * @package    Elaska
 * @subpackage Particuliers
 */
class ElaskaParticulierDashboard
{
    /**
     * ID du particulier concerné
     * @var int
     */
    private int $particulier_id;
    
    /**
     * Période d'analyse (en jours)
     * @var int
     */
    private int $periode = 30;
    
    /**
     * Types de documents à inclure (null = tous)
     * @var array|null
     */
    private ?array $types_documents = null;
    
    /**
     * Types de démarches à inclure (null = tous)
     * @var array|null
     */
    private ?array $types_demarches = null;
    
    /**
     * Types d'événements à inclure (null = tous)
     * @var array|null
     */
    private ?array $types_evenements = null;
    
    /**
     * Date de référence pour les calculs (aujourd'hui par défaut)
     * @var DateTime
     */
    private DateTime $date_reference;
    
    /**
     * Cache des données récupérées
     * @var array
     */
    private array $cache = [];
    
    /**
     * Constructeur
     * @param int $particulier_id ID du particulier
     */
    public function __construct(int $particulier_id)
    {
        $this->particulier_id = $particulier_id;
        $this->date_reference = new DateTime();
    }
    
    /**
     * Définit la période d'analyse
     * @param int $jours Nombre de jours
     * @return self
     */
    public function setPeriode(int $jours): self
    {
        $this->periode = max(1, $jours);
        $this->cache = []; // Vider le cache
        return $this;
    }
    
    /**
     * Définit les types de documents à inclure
     * @param array|null $types Liste des types ou null pour tous
     * @return self
     */
    public function setTypesDocuments(?array $types): self
    {
        $this->types_documents = $types;
        $this->cache = []; // Vider le cache
        return $this;
    }
    
    /**
     * Définit les types de démarches à inclure
     * @param array|null $types Liste des types ou null pour tous
     * @return self
     */
    public function setTypesDemarches(?array $types): self
    {
        $this->types_demarches = $types;
        $this->cache = []; // Vider le cache
        return $this;
    }
    
    /**
     * Définit les types d'événements à inclure
     * @param array|null $types Liste des types ou null pour tous
     * @return self
     */
    public function setTypesEvenements(?array $types): self
    {
        $this->types_evenements = $types;
        $this->cache = []; // Vider le cache
        return $this;
    }
    
    /**
     * Définit la date de référence
     * @param DateTime $date Date de référence
     * @return self
     */
    public function setDateReference(DateTime $date): self
    {
        $this->date_reference = clone $date;
        $this->cache = []; // Vider le cache
        return $this;
    }
    
    /**
     * Récupère toutes les données du tableau de bord
     * @return array Données du tableau de bord
     */
    public function getDashboardData(): array
    {
        if (isset($this->cache['dashboard_data'])) {
            return $this->cache['dashboard_data'];
        }
        
        $data = [
            'documents' => $this->getDocuments(),
            'documents_a_traiter' => $this->getDocumentsATraiter(),
            'documents_recents' => $this->getDocumentsRecents(),
            'demarches_actives' => $this->getDemarchesActives(),
            'demarches_recentes' => $this->getDemarchesRecentes(),
            'notifications' => $this->getNotifications(),
            'echeances' => $this->getEcheances(),
            'evenements' => $this->getEvenements(),
            'statistiques' => $this->getStatistiques()
        ];
        
        $this->cache['dashboard_data'] = $data;
        return $data;
    }
    
    /**
     * Récupère les documents du particulier
     * @return array Documents
     */
    public function getDocuments(): array
    {
        if (isset($this->cache['documents'])) {
            return $this->cache['documents'];
        }
        
        $criteres = [
            'particulier_id' => $this->particulier_id,
            'order_by' => 'date_creation DESC'
        ];
        
        if ($this->types_documents !== null) {
            $criteres['type'] = $this->types_documents;
        }
        
        $documents = ElaskaDocument::findAllBy($criteres);
        $this->cache['documents'] = $documents;
        
        return $documents;
    }
    
    /**
     * Récupère les documents à traiter (non validés)
     * @return array Documents à traiter
     */
    public function getDocumentsATraiter(): array
    {
        if (isset($this->cache['documents_a_traiter'])) {
            return $this->cache['documents_a_traiter'];
        }
        
        $criteres = [
            'particulier_id' => $this->particulier_id,
            'est_valide' => false,
            'est_archive' => false,
            'order_by' => 'priorite DESC, date_creation ASC'
        ];
        
        if ($this->types_documents !== null) {
            $criteres['type'] = $this->types_documents;
        }
        
        $documents = ElaskaDocument::findAllBy($criteres);
        $this->cache['documents_a_traiter'] = $documents;
        
        return $documents;
    }
    
    /**
     * Récupère les documents récents (créés dans la période)
     * @return array Documents récents
     */
    public function getDocumentsRecents(): array
    {
        if (isset($this->cache['documents_recents'])) {
            return $this->cache['documents_recents'];
        }
        
        $date_debut = clone $this->date_reference;
        $date_debut->modify("-{$this->periode} days");
        
        $criteres = [
            'particulier_id' => $this->particulier_id,
            'date_creation_after' => $date_debut->format('Y-m-d H:i:s'),
            'order_by' => 'date_creation DESC'
        ];
        
        if ($this->types_documents !== null) {
            $criteres['type'] = $this->types_documents;
        }
        
        $documents = ElaskaDocument::findAllBy($criteres);
        $this->cache['documents_recents'] = $documents;
        
        return $documents;
    }
    
    /**
     * Récupère les démarches actives
     * @return array Démarches actives
     */
    public function getDemarchesActives(): array
    {
        if (isset($this->cache['demarches_actives'])) {
            return $this->cache['demarches_actives'];
        }
        
        $criteres = [
            'particulier_id' => $this->particulier_id,
            'statut' => ['en_cours', 'en_attente'],
            'order_by' => 'priorite DESC, date_echeance ASC'
        ];
        
        if ($this->types_demarches !== null) {
            $criteres['type'] = $this->types_demarches;
        }
        
        $demarches = ElaskaParticulierDemarche::findAllBy($criteres);
        $this->cache['demarches_actives'] = $demarches;
        
        return $demarches;
    }
    
    /**
     * Récupère les démarches récentes (créées dans la période)
     * @return array Démarches récentes
     */
    public function getDemarchesRecentes(): array
    {
        if (isset($this->cache['demarches_recentes'])) {
            return $this->cache['demarches_recentes'];
        }
        
        $date_debut = clone $this->date_reference;
        $date_debut->modify("-{$this->periode} days");
        
        $criteres = [
            'particulier_id' => $this->particulier_id,
            'date_creation_after' => $date_debut->format('Y-m-d H:i:s'),
            'order_by' => 'date_creation DESC'
        ];
        
        if ($this->types_demarches !== null) {
            $criteres['type'] = $this->types_demarches;
        }
        
        $demarches = ElaskaParticulierDemarche::findAllBy($criteres);
        $this->cache['demarches_recentes'] = $demarches;
        
        return $demarches;
    }
    
    /**
     * Récupère les notifications actives
     * @return array Notifications
     */
    public function getNotifications(): array
    {
        if (isset($this->cache['notifications'])) {
            return $this->cache['notifications'];
        }
        
        $criteres = [
            'destinataire' => $this->particulier_id,
            'est_lue' => false,
            'order_by' => 'date_creation DESC'
        ];
        
        $notifications = ElaskaNotification::findAllBy($criteres);
        $this->cache['notifications'] = $notifications;
        
        return $notifications;
    }
    
    /**
     * Récupère les échéances à venir
     * @param int $jours_futur Nombre de jours dans le futur (30 par défaut)
     * @return array Échéances
     */
    public function getEcheances(int $jours_futur = 30): array
    {
        $cache_key = 'echeances_' . $jours_futur;
        if (isset($this->cache[$cache_key])) {
            return $this->cache[$cache_key];
        }
        
        $date_fin = clone $this->date_reference;
        $date_fin->modify("+{$jours_futur} days");
        
        // Récupérer les échéances des démarches
        $criteres_demarches = [
            'particulier_id' => $this->particulier_id,
            'statut' => ['en_cours', 'en_attente'],
            'date_echeance_before' => $date_fin->format('Y-m-d H:i:s'),
            'date_echeance_after' => $this->date_reference->format('Y-m-d H:i:s'),
            'order_by' => 'date_echeance ASC'
        ];
        
        if ($this->types_demarches !== null) {
            $criteres_demarches['type'] = $this->types_demarches;
        }
        
        $demarches = ElaskaParticulierDemarche::findAllBy($criteres_demarches);
        
        // Récupérer les échéances des documents
        $criteres_documents = [
            'particulier_id' => $this->particulier_id,
            'est_archive' => false,
            'date_echeance_before' => $date_fin->format('Y-m-d H:i:s'),
            'date_echeance_after' => $this->date_reference->format('Y-m-d H:i:s'),
            'order_by' => 'date_echeance ASC'
        ];
        
        if ($this->types_documents !== null) {
            $criteres_documents['type'] = $this->types_documents;
        }
        
        $documents = ElaskaDocument::findAllBy($criteres_documents);
        
        // Fusionner les échéances
        $echeances = [];
        
        foreach ($demarches as $demarche) {
            $echeances[] = [
                'type' => 'demarche',
                'id' => $demarche->getId(),
                'titre' => $demarche->getTitre(),
                'description' => $demarche->getDescription(),
                'date_echeance' => $demarche->getDateEcheance(),
                'url' => "/particulier/{$this->particulier_id}/demarches/view/{$demarche->getId()}",
                'priorite' => $demarche->getPriorite(),
                'categorie' => $demarche->getType()
            ];
        }
        
        foreach ($documents as $document) {
            $echeances[] = [
                'type' => 'document',
                'id' => $document->getId(),
                'titre' => $document->getTitre(),
                'description' => $document->getDescription(),
                'date_echeance' => $document->getDateEcheance(),
                'url' => "/particulier/{$this->particulier_id}/documents/view/{$document->getId()}",
                'priorite' => $document->getPriorite(),
                'categorie' => $document->getType()
            ];
        }
        
        // Trier par date d'échéance
        usort($echeances, function($a, $b) {
            return $a['date_echeance']->getTimestamp() - $b['date_echeance']->getTimestamp();
        });
        
        $this->cache[$cache_key] = $echeances;
        return $echeances;
    }
    
    /**
     * Récupère les événements récents ou à venir
     * @return array Événements
     */
    public function getEvenements(): array
    {
        if (isset($this->cache['evenements'])) {
            return $this->cache['evenements'];
        }
        
        $date_debut = clone $this->date_reference;
        $date_debut->modify("-{$this->periode} days");
        
        $date_fin = clone $this->date_reference;
        $date_fin->modify("+{$this->periode} days");
        
        $criteres = [
            'particulier_id' => $this->particulier_id,
            'date_after' => $date_debut->format('Y-m-d H:i:s'),
            'date_before' => $date_fin->format('Y-m-d H:i:s'),
            'order_by' => 'date ASC'
        ];
        
        if ($this->types_evenements !== null) {
            $criteres['type'] = $this->types_evenements;
        }
        
        $evenements = ElaskaParticulierEvenementVie::findAllBy($criteres);
        $this->cache['evenements'] = $evenements;
        
        return $evenements;
    }
    
    /**
     * Récupère les statistiques du tableau de bord
     * @return array Statistiques
     */
    public function getStatistiques(): array
    {
        if (isset($this->cache['statistiques'])) {
            return $this->cache['statistiques'];
        }
        
        // Documents
        $documents = $this->getDocuments();
        $documents_a_traiter = $this->getDocumentsATraiter();
        
        // Démarches
        $demarches_actives = $this->getDemarchesActives();
        
        // Échéances urgentes (7 prochains jours)
        $echeances_urgentes = $this->getEcheances(7);
        
        // Notifications non lues
        $notifications = $this->getNotifications();
        
        // Statistiques
        $stats = [
            'documents_total' => count($documents),
            'documents_a_traiter' => count($documents_a_traiter),
            'demarches_actives' => count($demarches_actives),
            'echeances_urgentes' => count($echeances_urgentes),
            'notifications_non_lues' => count($notifications),
            'taux_completion' => $this->calculerTauxCompletion()
        ];
        
        $this->cache['statistiques'] = $stats;
        return $stats;
    }
    
    /**
     * Calcule le taux de complétion des documents et démarches
     * @return float Taux de complétion (0-100)
     */
    private function calculerTauxCompletion(): float
    {
        // Documents
        $documents_total = ElaskaDocument::countBy([
            'particulier_id' => $this->particulier_id,
            'est_archive' => false
        ]);
        
        $documents_valides = ElaskaDocument::countBy([
            'particulier_id' => $this->particulier_id,
            'est_valide' => true,
            'est_archive' => false
        ]);
        
        // Démarches
        $demarches_total = ElaskaParticulierDemarche::countBy([
            'particulier_id' => $this->particulier_id
        ]);
        
        $demarches_terminees = ElaskaParticulierDemarche::countBy([
            'particulier_id' => $this->particulier_id,
            'statut' => 'complete'
        ]);
        
        // Calcul du taux global
        $total = $documents_total + $demarches_total;
        
        if ($total === 0) {
            return 100; // Si aucun document/démarche, considérer comme 100% complet
        }
        
        $completes = $documents_valides + $demarches_terminees;
        
        return ($completes / $total) * 100;
    }
    
    /**
     * Génère un résumé des tâches administratives
     * @return array Résumé des tâches
     */
    public function genererResumeTaches(): array
    {
        $echeances = $this->getEcheances();
        $documents_a_traiter = $this->getDocumentsATraiter();
        
        // Identifier les tâches urgentes (échéance dans moins de 7 jours)
        $date_limite_urgente = clone $this->date_reference;
        $date_limite_urgente->modify('+7 days');
        
        $taches_urgentes = [];
        $taches_importantes = [];
        $taches_normales = [];
        
        // Classifier les échéances
        foreach ($echeances as $echeance) {
            $tache = [
                'titre' => $echeance['titre'],
                'description' => "Échéance le " . $echeance['date_echeance']->format('d/m/Y'),
                'type' => $echeance['type'],
                'id' => $echeance['id'],
                'url' => $echeance['url'],
                'priorite' => $echeance['priorite']
            ];
            
            if ($echeance['date_echeance'] < $date_limite_urgente) {
                $taches_urgentes[] = $tache;
            } else if ($echeance['priorite'] >= 4) {
                $taches_importantes[] = $tache;
            } else {
                $taches_normales[] = $tache;
            }
        }
        
        // Ajouter les documents à traiter
        foreach ($documents_a_traiter as $document) {
            $tache = [
                'titre' => $document->getTitre(),
                'description' => "Document à traiter",
                'type' => 'document',
                'id' => $document->getId(),
                'url' => "/particulier/{$this->particulier_id}/documents/view/{$document->getId()}",
                'priorite' => $document->getPriorite()
            ];
            
            if ($document->getPriorite() >= 5) {
                $taches_urgentes[] = $tache;
            } else if ($document->getPriorite() >= 3) {
                $taches_importantes[] = $tache;
            } else {
                $taches_normales[] = $tache;
            }
        }
        
        // Trier les tâches par priorité
        usort($taches_urgentes, function($a, $b) {
            return $b['priorite'] - $a['priorite'];
        });
        
        usort($taches_importantes, function($a, $b) {
            return $b['priorite'] - $a['priorite'];
        });
        
        return [
            'urgentes' => $taches_urgentes,
            'importantes' => $taches_importantes,
            'normales' => $taches_normales
        ];
    }
}
