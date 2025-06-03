<?php
/**
 * Contrôleur des objectifs de vie des particuliers
 * 
 * @package Elaska
 * @subpackage Particuliers
 * @author Elaska Dev Team
 * @version 4.5.0
 */
class ObjectifController extends ElaskaController {
    /**
     * Affiche la liste des objectifs d'un particulier
     * @param int $particulier_id ID du particulier
     * @return ElaskaView Vue à afficher
     */
    public function index(int $particulier_id) {
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_objectifs', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Récupération des objectifs
        $objectifs = ElaskaParticulierObjectif::findAllBy([
            'particulier_id' => $particulier_id,
            'order_by' => 'priorite DESC, date_echeance ASC'
        ]);
        
        // Générer les statistiques
        $stats = ElaskaParticulierObjectifStats::genererStatistiques($particulier_id);
        $tendances = ElaskaParticulierObjectifStats::analyserTendances($particulier_id);
        $recommandations = ElaskaParticulierObjectifStats::genererRecommandations($particulier_id);
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($particulier_id);
        
        // Préparer les données pour la vue
        $data = [
            'objectifs' => $objectifs,
            'particulier' => $particulier,
            'stats' => $stats,
            'tendances' => $tendances,
            'recommandations' => $recommandations
        ];
        
        return $this->view('particuliers/objectifs/liste', $data);
    }
    
    /**
     * Affiche le détail d'un objectif
     * @param int $objectif_id ID de l'objectif à afficher
     * @return ElaskaView Vue à afficher
     */
    public function view(int $objectif_id) {
        // Récupération de l'objectif
        $objectif = ElaskaParticulierObjectif::findById($objectif_id);
        
        if (!$objectif) {
            return $this->notFound("Objectif #$objectif_id non trouvé");
        }
        
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_objectifs', $objectif->getParticulierId())) {
            return $this->forbidden();
        }
        
        // Récupérer les étapes de l'objectif
        $etapes = ElaskaParticulierObjectifEtape::findAllBy([
            'objectif_id' => $objectif_id,
            'order_by' => 'ordre ASC'
        ]);
        
        // Récupérer les démarches liées
        $demarches = $objectif->getDemarchesLiees();
        $demarches_bloquantes = $objectif->getDemarchesBloquantes();
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($objectif->getParticulierId());
        
        // Préparer les données pour la vue
        $data = [
            'objectif' => $objectif,
            'etapes' => $etapes,
            'particulier' => $particulier,
            'demarches' => $demarches,
            'demarches_bloquantes' => $demarches_bloquantes
        ];
        
        return $this->view('particuliers/objectifs/detail', $data);
    }
    
    /**
     * Affiche le formulaire de création d'un objectif
     * @param int $particulier_id ID du particulier
     * @return ElaskaView Vue à afficher
     */
    public function create(int $particulier_id) {
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($particulier_id);
        
        if (!$particulier) {
            return $this->notFound("Particulier #$particulier_id non trouvé");
        }
        
        // Préparer les données pour le formulaire
        $data = [
            'particulier' => $particulier,
            'objectif' => new ElaskaParticulierObjectif(),
            'categories' => ElaskaParticulierObjectifCategorie::getAll(),
            'mode' => 'create'
        ];
        
        return $this->view('particuliers/objectifs/form', $data);
    }
    
    /**
     * Traite le formulaire de création/édition d'un objectif
     * @return ElaskaResponse Redirection
     */
    public function save() {
        // Récupérer les données du formulaire
        $objectif_id = $this->request->post('objectif_id', 0);
        $particulier_id = $this->request->post('particulier_id', 0);
        
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Création ou récupération de l'objectif
        if ($objectif_id > 0) {
            $objectif = ElaskaParticulierObjectif::findById($objectif_id);
            if (!$objectif || $objectif->getParticulierId() != $particulier_id) {
                return $this->notFound("Objectif #$objectif_id non trouvé");
            }
        } else {
            $objectif = new ElaskaParticulierObjectif();
            $objectif->setParticulierId($particulier_id);
            $objectif->setDateCreation(new DateTime());
            $objectif->setProgression(0);
        }
        
        // Mise à jour des données
        $objectif->setTitre($this->request->post('titre'));
        $objectif->setDescription($this->request->post('description'));
        $objectif->setCategorie($this->request->post('categorie'));
        $objectif->setPriorite($this->request->post('priorite', 3));
        $objectif->setStatut($this->request->post('statut', 'actif'));
        
        // Date d'échéance
        $date_echeance = $this->request->post('date_echeance');
        if ($date_echeance) {
            $objectif->setDateEcheance(new DateTime($date_echeance));
        }
        
        $objectif->setDateModification(new DateTime());
        
        // Sauvegarde
        if ($objectif->save()) {
            // Journalisation
            ElaskaLog::info("Objectif #" . $objectif->getId() . " sauvegardé par " . $this->getCurrentUser()->getUsername());
            
            $this->setFlashMessage('success', "L'objectif a été sauvegardé avec succès");
            return $this->redirect("/particulier/{$particulier_id}/objectifs/view/" . $objectif->getId());
        } else {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la sauvegarde de l'objectif");
            return $this->redirect("/particulier/{$particulier_id}/objectifs");
        }
    }
    
    /**
     * Affiche le formulaire d'édition d'un objectif
     * @param int $objectif_id ID de l'objectif à éditer
     * @return ElaskaView Vue à afficher
     */
    public function edit(int $objectif_id) {
        // Récupération de l'objectif
        $objectif = ElaskaParticulierObjectif::findById($objectif_id);
        
        if (!$objectif) {
            return $this->notFound("Objectif #$objectif_id non trouvé");
        }
        
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $objectif->getParticulierId())) {
            return $this->forbidden();
        }
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($objectif->getParticulierId());
        
        // Préparer les données pour le formulaire
        $data = [
            'particulier' => $particulier,
            'objectif' => $objectif,
            'categories' => ElaskaParticulierObjectifCategorie::getAll(),
            'mode' => 'edit'
        ];
        
        return $this->view('particuliers/objectifs/form', $data);
    }
    
    /**
     * Supprime un objectif
     * @param int $objectif_id ID de l'objectif à supprimer
     * @return ElaskaResponse Redirection
     */
    public function delete(int $objectif_id) {
        // Récupération de l'objectif
        $objectif = ElaskaParticulierObjectif::findById($objectif_id);
        
        if (!$objectif) {
            return $this->notFound("Objectif #$objectif_id non trouvé");
        }
        
        $particulier_id = $objectif->getParticulierId();
        
        // Vérification des permissions
        if (!$this->checkPermission('delete_particulier_objectifs', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Suppression de l'objectif
        if ($objectif->delete()) {
            $this->setFlashMessage('success', "L'objectif a été supprimé avec succès");
        } else {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la suppression de l'objectif");
        }
        
        return $this->redirect("/particulier/{$particulier_id}/objectifs");
    }
    
    /**
     * Met à jour le statut d'un objectif
     * @param int $objectif_id ID de l'objectif
     * @param string $statut Nouveau statut
     * @return ElaskaResponse Redirection
     */
    public function updateStatut(int $objectif_id, string $statut) {
        // Récupération de l'objectif
        $objectif = ElaskaParticulierObjectif::findById($objectif_id);
        
        if (!$objectif) {
            return $this->notFound("Objectif #$objectif_id non trouvé");
        }
        
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $objectif->getParticulierId())) {
            return $this->forbidden();
        }
        
        // Vérification du statut
        $statuts_valides = ['actif', 'en_pause', 'complete', 'abandonne'];
        if (!in_array($statut, $statuts_valides)) {
            $this->setFlashMessage('error', "Le statut '$statut' n'est pas valide");
            return $this->redirect("/particulier/{$objectif->getParticulierId()}/objectifs/view/$objectif_id");
        }
        
        // Mise à jour du statut
        $objectif->setStatut($statut);
        $objectif->setDateModification(new DateTime());
        
        // Si l'objectif est marqué comme terminé, définir la date de complétion
        if ($statut == 'complete') {
            $objectif->setDateCompletion(new DateTime());
            $objectif->setProgression(100);
        }
        
        // Sauvegarde
        if ($objectif->save()) {
            $this->setFlashMessage('success', "Le statut de l'objectif a été mis à jour avec succès");
        } else {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la mise à jour du statut");
        }
        
        return $this->redirect("/particulier/{$objectif->getParticulierId()}/objectifs/view/$objectif_id");
    }
    
    /**
     * Met à jour la progression d'un objectif
     * @param int $objectif_id ID de l'objectif
     * @return ElaskaResponse Redirection
     */
    public function updateProgression(int $objectif_id) {
        // Récupération de l'objectif
        $objectif = ElaskaParticulierObjectif::findById($objectif_id);
        
        if (!$objectif) {
            return $this->notFound("Objectif #$objectif_id non trouvé");
        }
        
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $objectif->getParticulierId())) {
            return $this->forbidden();
        }
        
        // Récupérer la nouvelle progression
        $progression = $this->request->post('progression', -1);
        
        // Vérification de la progression
        if ($progression < 0 || $progression > 100) {
            $this->setFlashMessage('error', "La progression doit être comprise entre 0 et 100");
            return $this->redirect("/particulier/{$objectif->getParticulierId()}/objectifs/view/$objectif_id");
        }
        
        // Mise à jour de la progression
        $objectif->setProgression($progression);
        $objectif->setDateModification(new DateTime());
        
        // Si la progression atteint 100%, proposer de marquer comme complété
        if ($progression == 100 && $objectif->getStatut() == 'actif') {
            // On ne marque pas automatiquement comme complété, mais on le propose
            $this->setFlashMessage('info', "La progression est à 100%. Voulez-vous <a href='/particulier/{$objectif->getParticulierId()}/objectifs/statut/$objectif_id/complete'>marquer cet objectif comme terminé</a> ?");
        }
        
        // Sauvegarde
        if ($objectif->save()) {
            $this->setFlashMessage('success', "La progression de l'objectif a été mise à jour avec succès");
        } else {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la mise à jour de la progression");
        }
        
        return $this->redirect("/particulier/{$objectif->getParticulierId()}/objectifs/view/$objectif_id");
    }
    
    /**
     * Affiche la liste des suggestions d'objectifs
     * @param int $particulier_id ID du particulier
     * @return ElaskaView Vue à afficher
     */
    public function suggestionsList(int $particulier_id) {
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_objectifs', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($particulier_id);
        
        if (!$particulier) {
            return $this->notFound("Particulier #$particulier_id non trouvé");
        }
        
        // Récupérer les suggestions actives
        $suggestions = ElaskaParticulierObjectifSuggestionManager::getSuggestionsActives($particulier_id);
        
        // Préparer les données pour la vue
        $data = [
            'particulier' => $particulier,
            'suggestions' => $suggestions
        ];
        
        return $this->view('particuliers/objectifs/suggestions', $data);
    }
    
    /**
     * Accepte une suggestion d'objectif
     * @param int $suggestion_id ID de la suggestion
     * @return ElaskaResponse Redirection
     */
    public function suggestionAccept(int $suggestion_id) {
        // Récupération de la suggestion
        $suggestion = ElaskaParticulierObjectifSuggestion::findById($suggestion_id);
        
        if (!$suggestion) {
            return $this->notFound("Suggestion #$suggestion_id non trouvée");
        }
        
        $particulier_id = $suggestion->getParticulierId();
        
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Convertir la suggestion en objectif
        $objectif = $suggestion->convertirEnObjectif();
        
        if ($objectif) {
            $this->setFlashMessage('success', "La suggestion a été convertie en objectif avec succès");
            return $this->redirect("/particulier/{$particulier_id}/objectifs/view/" . $objectif->getId());
        } else {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la conversion de la suggestion");
            return $this->redirect("/particulier/{$particulier_id}/objectifs/suggestions");
        }
    }
    
    /**
     * Rejette une suggestion d'objectif
     * @param int $suggestion_id ID de la suggestion
     * @return ElaskaResponse Redirection
     */
    public function suggestionReject(int $suggestion_id) {
        // Récupération de la suggestion
        $suggestion = ElaskaParticulierObjectifSuggestion::findById($suggestion_id);
        
        if (!$suggestion) {
            return $this->notFound("Suggestion #$suggestion_id non trouvée");
        }
        
        $particulier_id = $suggestion->getParticulierId();
        
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Rejeter la suggestion
        $suggestion->rejetDefinitif();
        
        $this->setFlashMessage('success', "La suggestion a été rejetée avec succès");
        return $this->redirect("/particulier/{$particulier_id}/objectifs/suggestions");
    }
    
    /**
     * Génère des suggestions d'objectifs
     * @param int $particulier_id ID du particulier
     * @return ElaskaResponse Redirection
     */
    public function generateSuggestions(int $particulier_id) {
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Générer les suggestions
        $suggestions = ElaskaParticulierObjectifSuggestionManager::genererSuggestions($particulier_id);
        
        // Notifier le particulier
        ElaskaParticulierObjectifSuggestionManager::notifierNouvellesSuggestions($particulier_id, $suggestions);
        
        $this->setFlashMessage('success', count($suggestions) . " suggestions d'objectifs ont été générées");
        return $this->redirect("/particulier/{$particulier_id}/objectifs/suggestions");
    }
    
    /**
     * Affiche la gestion des démarches liées à un objectif
     * @param int $objectif_id ID de l'objectif
     * @return ElaskaView Vue à afficher
     */
    public function liensDemarches(int $objectif_id) {
        // Récupération de l'objectif
        $objectif = ElaskaParticulierObjectif::findById($objectif_id);
        
        if (!$objectif) {
            return $this->notFound("Objectif #$objectif_id non trouvé");
        }
        
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $objectif->getParticulierId())) {
            return $this->forbidden();
        }
        
        // Récupérer les démarches liées
        $demarches_liees = $objectif->getDemarchesLiees();
        
        // Récupérer toutes les démarches du particulier
        $particulier_id = $objectif->getParticulierId();
        $toutes_demarches = ElaskaParticulierDemarche::findAllBy([
            'particulier_id' => $particulier_id
        ]);
        
        // Filtrer pour exclure les démarches déjà liées
        $demarches_liees_ids = array_map(function($d) { return $d->getId(); }, $demarches_liees);
        $demarches_disponibles = array_filter($toutes_demarches, function($d) use ($demarches_liees_ids) {
            return !in_array($d->getId(), $demarches_liees_ids);
        });
        
        // Préparer les données pour la vue
        $data = [
            'objectif' => $objectif,
            'demarches_liees' => $demarches_liees,
            'demarches_disponibles' => $demarches_disponibles
        ];
        
        return $this->view('particuliers/objectifs/liens_demarches', $data);
    }
    
    /**
     * Ajoute une liaison entre un objectif et une démarche
     * @param int $objectif_id ID de l'objectif
     * @return ElaskaResponse Redirection
     */
    public function ajouterLienDemarche(int $objectif_id) {
        // Récupération de l'objectif
        $objectif = ElaskaParticulierObjectif::findById($objectif_id);
        
        if (!$objectif) {
            return $this->notFound("Objectif #$objectif_id non trouvé");
        }
        
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $objectif->getParticulierId())) {
            return $this->forbidden();
        }
        
        // Récupérer les données du formulaire
        $demarche_id = $this->request->post('demarche_id', 0);
        $type_liaison = $this->request->post('type_liaison', 'contributif');
        $poids_impact = $this->request->post('poids_impact', 0.5);
        $description = $this->request->post('description', '');
        
        // Vérifications
        if ($demarche_id <= 0) {
            $this->setFlashMessage('error', "Vous devez sélectionner une démarche");
            return $this->redirect("/particulier/{$objectif->getParticulierId()}/objectifs/liens-demarches/$objectif_id");
        }
        
        // Ajouter la liaison
        if ($objectif->lierDemarche($demarche_id, $type_liaison, $poids_impact, $description)) {
            $this->setFlashMessage('success', "La démarche a été liée à l'objectif avec succès");
        } else {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la liaison de la démarche");
        }
        
        return $this->redirect("/particulier/{$objectif->getParticulierId()}/objectifs/liens-demarches/$objectif_id");
    }
    
    /**
     * Supprime une liaison entre un objectif et une démarche
     * @param int $objectif_id ID de l'objectif
     * @param int $demarche_id ID de la démarche
     * @return ElaskaResponse Redirection
     */
    public function supprimerLienDemarche(int $objectif_id, int $demarche_id) {
        // Récupération de l'objectif
        $objectif = ElaskaParticulierObjectif::findById($objectif_id);
        
        if (!$objectif) {
            return $this->notFound("Objectif #$objectif_id non trouvé");
        }
        
        // Vérification des permissions
        if (!$this->checkPermission('edit_particulier_objectifs', $objectif->getParticulierId())) {
            return $this->forbidden();
        }
        
        // Supprimer la liaison
        if ($objectif->delierDemarche($demarche_id)) {
            $this->setFlashMessage('success', "La liaison avec la démarche a été supprimée avec succès");
        } else {
            $this->setFlashMessage('error', "Une erreur est survenue lors de la suppression de la liaison");
        }
        
        return $this->redirect("/particulier/{$objectif->getParticulierId()}/objectifs/liens-demarches/$objectif_id");
    }
    
    /**
     * Affiche les statistiques des objectifs d'un particulier
     * @param int $particulier_id ID du particulier
     * @return ElaskaView Vue à afficher
     */
    public function statistiques(int $particulier_id) {
        // Vérification des permissions
        if (!$this->checkPermission('view_particulier_objectifs', $particulier_id)) {
            return $this->forbidden();
        }
        
        // Récupérer le particulier
        $particulier = ElaskaParticulier::findById($particulier_id);
        
        if (!$particulier) {
            return $this->notFound("Particulier #$particulier_id non trouvé");
        }
        
        // Générer les statistiques
        $stats = ElaskaParticulierObjectifStats::genererStatistiques($particulier_id);
        $tendances = ElaskaParticulierObjectifStats::analyserTendances($particulier_id);
        $tendances_trimestre = ElaskaParticulierObjectifStats::analyserTendances($particulier_id, 'trimestre');
        $tendances_annee = ElaskaParticulierObjectifStats::analyserTendances($particulier_id, 'annee');
        $points_amelioration = ElaskaParticulierObjectifStats::identifierPointsAmelioration($particulier_id);
        $recommandations = ElaskaParticulierObjectifStats::genererRecommandations($particulier_id);
        $taux_reussite = ElaskaParticulierObjectifStats::calculerTauxReussite($particulier_id);
        
        // Préparer les données pour la vue
        $data = [
            'particulier' => $particulier,
            'stats' => $stats,
            'tendances' => $tendances,
            'tendances_trimestre' => $tendances_trimestre,
            'tendances_annee' => $tendances_annee,
            'points_amelioration' => $points_amelioration,
            'recommandations' => $recommandations,
            'taux_reussite' => $taux_reussite
        ];
        
        return $this->view('particuliers/objectifs/statistiques', $data);
    }
}
