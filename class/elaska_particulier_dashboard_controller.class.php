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
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
dol_include_once('/elaska/class/elaska_particulier_dashboard.class.php');
dol_include_once('/elaska/class/elaska_document_archive.class.php');

/**
 * Contrôleur du tableau de bord administratif pour les particuliers
 * 
 * @package    Elaska
 * @subpackage Particuliers
 */
class ElaskaParticulierDashboardController
{
    /**
     * @var DoliDB    Database handler
     */
    public $db;
    
    /**
     * @var string    Error message
     */
    public $error;
    
    /**
     * @var array     Error messages
     */
    public $errors = array();
    
    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Affiche le tableau de bord principal
     * 
     * @param   int     $particulier_id     ID du particulier
     * @param   array   $params             Paramètres supplémentaires
     * @return  array                       Données pour la vue
     */
    public function index($particulier_id, $params = array())
    {
        global $user;
        
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_dashboard', $particulier_id)) {
            return array('error' => 'NotAllowed');
        }
        
        // Récupérer le particulier
        require_once DOL_DOCUMENT_ROOT.'/elaska/class/elaska_particulier.class.php';
        $particulier = new ElaskaParticulier($this->db);
        $result = $particulier->fetch($particulier_id);
        
        if ($result < 0) {
            return array('error' => 'NotFound', 'message' => "Particulier #$particulier_id non trouvé");
        }
        
        // Créer le tableau de bord
        $dashboard = new ElaskaParticulierDashboard($particulier_id);
        
        // Configurer le tableau de bord selon les paramètres de la requête
        $periode = isset($params['periode']) ? intval($params['periode']) : 30;
        $dashboard->setPeriode($periode);
        
        // Filtres optionnels
        if (isset($params['types_documents']) && !empty($params['types_documents'])) {
            $dashboard->setTypesDocuments(explode(',', $params['types_documents']));
        }
        
        if (isset($params['types_demarches']) && !empty($params['types_demarches'])) {
            $dashboard->setTypesDemarches(explode(',', $params['types_demarches']));
        }
        
        // Récupérer les données du tableau de bord
        $dashboard_data = $dashboard->getDashboardData();
        $resume_taches = $dashboard->genererResumeTaches();
        
        // Récupérer les statistiques des archives
        $stats_archives = ElaskaDocumentArchive::getStatistiquesArchives($particulier_id);
        
        // Préparer les données pour la vue
        $data = array(
            'particulier' => $particulier,
            'dashboard' => $dashboard_data,
            'resume_taches' => $resume_taches,
            'stats_archives' => $stats_archives,
            'periode' => $periode
        );
        
        return $data;
    }
    
    /**
     * Affiche les échéances
     * 
     * @param   int     $particulier_id     ID du particulier
     * @param   array   $params             Paramètres supplémentaires
     * @return  array                       Données pour la vue
     */
    public function echeances($particulier_id, $params = array())
    {
        global $user;
        
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_dashboard', $particulier_id)) {
            return array('error' => 'NotAllowed');
        }
        
        // Récupérer le particulier
        require_once DOL_DOCUMENT_ROOT.'/elaska/class/elaska_particulier.class.php';
        $particulier = new ElaskaParticulier($this->db);
        $result = $particulier->fetch($particulier_id);
        
        if ($result < 0) {
            return array('error' => 'NotFound', 'message' => "Particulier #$particulier_id non trouvé");
        }
        
        // Créer le tableau de bord
        $dashboard = new ElaskaParticulierDashboard($particulier_id);
        
        // Récupérer les échéances pour différentes périodes
        $echeances_7j = $dashboard->getEcheances(7);
        $echeances_30j = $dashboard->getEcheances(30);
        $echeances_90j = $dashboard->getEcheances(90);
        
        // Préparer les données pour la vue
        $data = array(
            'particulier' => $particulier,
            'echeances_7j' => $echeances_7j,
            'echeances_30j' => $echeances_30j,
            'echeances_90j' => $echeances_90j
        );
        
        return $data;
    }
    
    /**
     * Affiche les tâches à réaliser
     * 
     * @param   int     $particulier_id     ID du particulier
     * @param   array   $params             Paramètres supplémentaires
     * @return  array                       Données pour la vue
     */
    public function taches($particulier_id, $params = array())
    {
        global $user;
        
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_dashboard', $particulier_id)) {
            return array('error' => 'NotAllowed');
        }
        
        // Récupérer le particulier
        require_once DOL_DOCUMENT_ROOT.'/elaska/class/elaska_particulier.class.php';
        $particulier = new ElaskaParticulier($this->db);
        $result = $particulier->fetch($particulier_id);
        
        if ($result < 0) {
            return array('error' => 'NotFound', 'message' => "Particulier #$particulier_id non trouvé");
        }
        
        // Créer le tableau de bord
        $dashboard = new ElaskaParticulierDashboard($particulier_id);
        
        // Récupérer les tâches
        $resume_taches = $dashboard->genererResumeTaches();
        
        // Préparer les données pour la vue
        $data = array(
            'particulier' => $particulier,
            'resume_taches' => $resume_taches
        );
        
        return $data;
    }
    
    /**
     * Affiche les statistiques du tableau de bord
     * 
     * @param   int     $particulier_id     ID du particulier
     * @param   array   $params             Paramètres supplémentaires
     * @return  array                       Données pour la vue
     */
    public function statistiques($particulier_id, $params = array())
    {
        global $user;
        
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_dashboard', $particulier_id)) {
            return array('error' => 'NotAllowed');
        }
        
        // Récupérer le particulier
        require_once DOL_DOCUMENT_ROOT.'/elaska/class/elaska_particulier.class.php';
        $particulier = new ElaskaParticulier($this->db);
        $result = $particulier->fetch($particulier_id);
        
        if ($result < 0) {
            return array('error' => 'NotFound', 'message' => "Particulier #$particulier_id non trouvé");
        }
        
        // Créer le tableau de bord
        $dashboard = new ElaskaParticulierDashboard($particulier_id);
        
        // Statistiques générales
        $statistiques = $dashboard->getStatistiques();
        
        // Statistiques des archives
        $stats_archives = ElaskaDocumentArchive::getStatistiquesArchives($particulier_id);
        
        // Statistiques des démarches
        require_once DOL_DOCUMENT_ROOT.'/elaska/class/elaska_particulier_demarche.class.php';
        $demarche = new ElaskaParticulierDemarche($this->db);
        $demarches = $demarche->fetchAll('', '', 0, 0, array('customsql' => 'fk_particulier = '.$particulier_id));
        
        $stats_demarches = array();
        $stats_demarches['total'] = is_array($demarches) ? count($demarches) : 0;
        $stats_demarches['par_statut'] = array();
        $stats_demarches['par_type'] = array();
        
        if (is_array($demarches) && $stats_demarches['total'] > 0) {
            foreach ($demarches as $d) {
                $statut = $d->statut;
                $type = $d->type;
                
                if (!isset($stats_demarches['par_statut'][$statut])) {
                    $stats_demarches['par_statut'][$statut] = 0;
                }
                
                if (!isset($stats_demarches['par_type'][$type])) {
                    $stats_demarches['par_type'][$type] = 0;
                }
                
                $stats_demarches['par_statut'][$statut]++;
                $stats_demarches['par_type'][$type]++;
            }
        }
        
        // Préparer les données pour la vue
        $data = array(
            'particulier' => $particulier,
            'statistiques' => $statistiques,
            'stats_archives' => $stats_archives,
            'stats_demarches' => $stats_demarches
        );
        
        return $data;
    }
    
    /**
     * Exporte les données du tableau de bord au format PDF
     * 
     * @param   int     $particulier_id     ID du particulier
     * @param   array   $params             Paramètres supplémentaires
     * @return  string                      Chemin vers le fichier PDF généré
     */
    public function exportPDF($particulier_id, $params = array())
    {
        global $user, $conf, $langs;
        
        // Vérification des permissions
        if (!$this->checkPermission('export_particulier_dashboard', $particulier_id)) {
            $this->error = $langs->trans('NotAllowed');
            return -1;
        }
        
        // Récupérer le particulier
        require_once DOL_DOCUMENT_ROOT.'/elaska/class/elaska_particulier.class.php';
        $particulier = new ElaskaParticulier($this->db);
        $result = $particulier->fetch($particulier_id);
        
        if ($result < 0) {
            $this->error = $langs->trans('RecordNotFound');
            return -2;
        }
        
        // Créer le tableau de bord
        $dashboard = new ElaskaParticulierDashboard($particulier_id);
        $dashboard_data = $dashboard->getDashboardData();
        $resume_taches = $dashboard->genererResumeTaches();
        
        // Générer le PDF
        require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
        require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
        
        // Créer répertoire temporaire si nécessaire
        $dir = $conf->elaska->dir_temp;
        dol_mkdir($dir);
        
        // Nom du fichier
        $filename = 'dashboard_'.$particulier_id.'_'.dol_print_date(dol_now(), 'dayhourlog').'.pdf';
        $file = $dir.'/'.$filename;
        
        // Instancier la classe PDF
        $pdf = pdf_getInstance();
        $pdf->SetTitle($langs->trans('Dashboard').' - '.$particulier->nom.' '.$particulier->prenom);
        $pdf->SetAuthor($conf->global->MAIN_INFO_SOCIETE_NOM);
        $pdf->SetCreator($conf->global->MAIN_INFO_SOCIETE_NOM);
        
        if (!$pdf->Error()) {
            $pdf->AddPage();
            
            // En-tête
            $pdf->SetFont('','B', 16);
            $pdf->Cell(0, 10, $langs->trans('AdministrativeDashboard'), 0, 1, 'C');
            $pdf->SetFont('','', 12);
            $pdf->Cell(0, 10, $langs->trans('Client').': '.$particulier->getNomComplet(), 0, 1, 'C');
            $pdf->Cell(0, 10, $langs->trans('Date').': '.dol_print_date(dol_now(), 'day'), 0, 1, 'C');
            $pdf->Ln(10);
            
            // Résumé statistique
            $pdf->SetFont('','B', 14);
            $pdf->Cell(0, 10, $langs->trans('StatisticalSummary'), 0, 1);
            $pdf->SetFont('','', 10);
            
            $stats = $dashboard_data['statistiques'];
            $pdf->Cell(100, 8, $langs->trans('DocumentsToProcess').':', 0);
            $pdf->Cell(0, 8, $stats['documents_a_traiter'].' / '.$stats['documents_total'], 0, 1);
            
            $pdf->Cell(100, 8, $langs->trans('ActiveProcedures').':', 0);
            $pdf->Cell(0, 8, $stats['demarches_actives'], 0, 1);
            
            $pdf->Cell(100, 8, $langs->trans('UrgentDeadlines').':', 0);
            $pdf->Cell(0, 8, $stats['echeances_urgentes'], 0, 1);
            
            $pdf->Cell(100, 8, $langs->trans('CompletionRate').':', 0);
            $pdf->Cell(0, 8, sprintf("%.1f%%", $stats['taux_completion']), 0, 1);
            $pdf->Ln(5);
            
            // Tâches urgentes
            if (count($resume_taches['urgentes']) > 0) {
                $pdf->SetFont('','B', 14);
                $pdf->Cell(0, 10, $langs->trans('UrgentTasks'), 0, 1);
                $pdf->SetFont('','', 10);
                
                foreach ($resume_taches['urgentes'] as $tache) {
                    $pdf->Cell(0, 8, "• ".$tache['titre']." - ".$tache['description'], 0, 1);
                }
                $pdf->Ln(5);
            }
            
            // Échéances à venir
            $echeances = $dashboard_data['echeances'];
            if (count($echeances) > 0) {
                $pdf->SetFont('','B', 14);
                $pdf->Cell(0, 10, $langs->trans('UpcomingDeadlines'), 0, 1);
                $pdf->SetFont('','', 10);
                
                foreach (array_slice($echeances, 0, 10) as $echeance) {
                    $date = $echeance['date_echeance']->format('d/m/Y');
                    $pdf->Cell(0, 8, "• ".$date." - ".$echeance['titre'], 0, 1);
                }
            }
            
            // Finaliser le PDF
            $pdf->Output($file, 'F');
            
            if (!empty($conf->global->MAIN_UMASK)) {
                @chmod($file, octdec($conf->global->MAIN_UMASK));
            }
            
            return $file;
        }
        
        $this->error = $langs->trans('ErrorGeneratingPDF');
        return -3;
    }
    
    /**
     * Vérifie les permissions pour une action donnée
     * 
     * @param   string  $action         Action à vérifier
     * @param   int     $particulier_id ID du particulier
     * @return  bool                    True si l'utilisateur a les droits, False sinon
     */
    private function checkPermission($action, $particulier_id)
    {
        global $user;
        
        // Vérifier si l'utilisateur est admin
        if ($user->admin) {
            return true;
        }
        
        // TODO: Vérifications spécifiques selon les droits de l'utilisateur
        // Exemple:
        // if ($action == 'view_particulier_dashboard') {
        //     return $user->rights->elaska->particulier->read;
        // } elseif ($action == 'export_particulier_dashboard') {
        //     return $user->rights->elaska->particulier->export;
        // }
        
        return true; // À adapter selon le système de droits
    }
}
