<?php
/**
 * Contrôleur du tableau de bord administratif pour les particuliers
 * 
 * @package Elaska
 * @subpackage Particuliers
 * @author Elaska Dev Team
 * @version 4.5.0
 */
class DashboardController extends ElaskaController {
    /**
     * Affiche le tableau de bord principal
     * @param int $particulier_id ID du particulier
     * @return ElaskaView Vue à afficher
     */
    public function index(int $particulier_id) {
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_dashboard', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($particulier_id);
        
        if (!$particulier) {
            return $this->notFound("Particulier #$particulier_id non trouvé");
        }
        
        // Créer le tableau de bord
        $dashboard = new ElaskaParticulierDashboard($particulier_id);
        
        // Configurer le tableau de bord selon les paramètres de la requête
        $periode = $this->request->get('periode', 30);
        $dashboard->setPeriode($periode);
        
        // Filtres optionnels
        $types_documents = $this->request->get('types_documents');
        if ($types_documents) {
            $dashboard->setTypesDocuments(explode(',', $types_documents));
        }
        
        $types_demarches = $this->request->get('types_demarches');
        if ($types_demarches) {
            $dashboard->setTypesDemarches(explode(',', $types_demarches));
        }
        
        // Récupérer les données du tableau de bord
        $dashboard_data = $dashboard->getDashboardData();
        $resume_taches = $dashboard->genererResumeTaches();
        
        // Récupérer les statistiques des archives
        $stats_archives = ElaskaDocumentArchive::getStatistiquesArchives($particulier_id);
        
        // Préparer les données pour la vue
        $data = [
            'particulier' => $particulier,
            'dashboard' => $dashboard_data,
            'resume_taches' => $resume_taches,
            'stats_archives' => $stats_archives,
            'periode' => $periode
        ];
        
        return $this->view('particuliers/dashboard/index', $data);
    }
    
    /**
     * Affiche les échéances
     * @param int $particulier_id ID du particulier
     * @return ElaskaView Vue à afficher
     */
    public function echeances(int $particulier_id) {
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_dashboard', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($particulier_id);
        
        if (!$particulier) {
            return $this->notFound("Particulier #$particulier_id non trouvé");
        }
        
        // Créer le tableau de bord
        $dashboard = new ElaskaParticulierDashboard($particulier_id);
        
        // Récupérer les échéances pour différentes périodes
        $echeances_7j = $dashboard->getEcheances(7);
        $echeances_30j = $dashboard->getEcheances(30);
        $echeances_90j = $dashboard->getEcheances(90);
        
        // Préparer les données pour la vue
        $data = [
            'particulier' => $particulier,
            'echeances_7j' => $echeances_7j,
            'echeances_30j' => $echeances_30j,
            'echeances_90j' => $echeances_90j
        ];
        
        return $this->view('particuliers/dashboard/echeances', $data);
    }
    
    /**
     * Affiche les tâches à réaliser
     * @param int $particulier_id ID du particulier
     * @return ElaskaView Vue à afficher
     */
    public function taches(int $particulier_id) {
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_dashboard', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($particulier_id);
        
        if (!$particulier) {
            return $this->notFound("Particulier #$particulier_id non trouvé");
        }
        
        // Créer le tableau de bord
        $dashboard = new ElaskaParticulierDashboard($particulier_id);
        
        // Récupérer les tâches
        $resume_taches = $dashboard->genererResumeTaches();
        
        // Préparer les données pour la vue
        $data = [
            'particulier' => $particulier,
            'resume_taches' => $resume_taches
        ];
        
        return $this->view('particuliers/dashboard/taches', $data);
    }
    
    /**
     * Affiche les statistiques du tableau de bord
     * @param int $particulier_id ID du particulier
     * @return ElaskaView Vue à afficher
     */
    public function statistiques(int $particulier_id) {
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_dashboard', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($particulier_id);
        
        if (!$particulier) {
            return $this->notFound("Particulier #$particulier_id non trouvé");
        }
        
        // Créer le tableau de bord
        $dashboard = new ElaskaParticulierDashboard($particulier_id);
        
        // Statistiques générales
        $statistiques = $dashboard->getStatistiques();
        
        // Statistiques des archives
        $stats_archives = ElaskaDocumentArchive::getStatistiquesArchives($particulier_id);
        
        // Statistiques des démarches
        $stats_demarches = [];
        
        $demarches = ElaskaParticulierDemarche::findAllBy([
            'particulier_id' => $particulier_id
        ]);
        
        $stats_demarches['total'] = count($demarches);
        $stats_demarches['par_statut'] = [];
        $stats_demarches['par_type'] = [];
        
        foreach ($demarches as $demarche) {
            $statut = $demarche->getStatut();
            $type = $demarche->getType();
            
            if (!isset($stats_demarches['par_statut'][$statut])) {
                $stats_demarches['par_statut'][$statut] = 0;
            }
            
            if (!isset($stats_demarches['par_type'][$type])) {
                $stats_demarches['par_type'][$type] = 0;
            }
            
            $stats_demarches['par_statut'][$statut]++;
            $stats_demarches['par_type'][$type]++;
        }
        
        // Préparer les données pour la vue
        $data = [
            'particulier' => $particulier,
            'statistiques' => $statistiques,
            'stats_archives' => $stats_archives,
            'stats_demarches' => $stats_demarches
        ];
        
        return $this->view('particuliers/dashboard/statistiques', $data);
    }
    
    /**
     * Exporte les données du tableau de bord au format PDF
     * @param int $particulier_id ID du particulier
     * @return ElaskaResponse Fichier PDF
     */
    public function exportPDF(int $particulier_id) {
        // Vérification des permissions
        if (!$this->checkPermission('export_particulier_dashboard', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($particulier_id);
        
        if (!$particulier) {
            return $this->notFound("Particulier #$particulier_id non trouvé");
        }
        
        // Créer le tableau de bord
        $dashboard = new ElaskaParticulierDashboard($particulier_id);
        $dashboard_data = $dashboard->getDashboardData();
        $resume_taches = $dashboard->genererResumeTaches();
        
        // Générer le PDF
        $pdf = new ElaskaPDF('P', 'mm', 'A4');
        $pdf->setTitle("Tableau de bord - {$particulier->getNomComplet()}");
        $pdf->setAuthor("Elaska");
        $pdf->setCreator("Elaska v4.5");
        
        $pdf->AddPage();
        
        // En-tête
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, "Tableau de bord administratif", 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, "Client: {$particulier->getNomComplet()}", 0, 1, 'C');
        $pdf->Cell(0, 10, "Date: " . date('d/m/Y'), 0, 1, 'C');
        $pdf->Ln(10);
        
        // Résumé statistique
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, "Résumé statistique", 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        
        $stats = $dashboard_data['statistiques'];
        $pdf->Cell(100, 8, "Documents à traiter:", 0);
        $pdf->Cell(0, 8, "{$stats['documents_a_traiter']} / {$stats['documents_total']}", 0, 1);
        
        $pdf->Cell(100, 8, "Démarches actives:", 0);
        $pdf->Cell(0, 8, "{$stats['demarches_actives']}", 0, 1);
        
        $pdf->Cell(100, 8, "Échéances urgentes:", 0);
        $pdf->Cell(0, 8, "{$stats['echeances_urgentes']}", 0, 1);
        
        $pdf->Cell(100, 8, "Taux de complétion:", 0);
        $pdf->Cell(0, 8, sprintf("%.1f%%", $stats['taux_completion']), 0, 1);
        $pdf->Ln(5);
        
        // Tâches urgentes
        if (count($resume_taches['urgentes']) > 0) {
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, "Tâches urgentes", 0, 1);
            $pdf->SetFont('helvetica', '', 10);
            
            foreach ($resume_taches['urgentes'] as $tache) {
                $pdf->Cell(0, 8, "• {$tache['titre']} - {$tache['description']}", 0, 1);
            }
            $pdf->Ln(5);
        }
        
        // Échéances à venir
        $echeances = $dashboard_data['echeances'];
        if (count($echeances) > 0) {
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, "Échéances à venir", 0, 1);
            $pdf->SetFont('helvetica', '', 10);
            
            foreach (array_slice($echeances, 0, 10) as $echeance) {
                $date = $echeance['date_echeance']->format('d/m/Y');
                $pdf->Cell(0, 8, "• {$date} - {$echeance['titre']}", 0, 1);
            }
        }
        
        // Générer le fichier
        $filename = "dashboard_{$particulier_id}_" . date('Ymd') . ".pdf";
        
        return $this->file($pdf->Output('S'), $filename, 'application/pdf');
    }
}
