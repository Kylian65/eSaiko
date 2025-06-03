<?php
/**
 * Gestion des objectifs des particuliers
 * 
 * @package Elaska
 * @subpackage Particuliers
 * @author Elaska Dev Team
 * @version 4.5.0
 */
class ElaskaParticulierObjectif extends ElaskaModel {
    // Code existant...
    
    /**
     * Retourne toutes les démarches administratives liées à cet objectif
     * @return array Liste des démarches associées
     */
    public function getDemarchesLiees(): array {
        $liaisons = ElaskaParticulierObjectifDemarche::findAllBy([
            'objectif_id' => $this->id
        ]);
        
        if (empty($liaisons)) {
            return [];
        }
        
        $demarches = [];
        foreach ($liaisons as $liaison) {
            $demarche = $liaison->getDemarche();
            if ($demarche) {
                $demarche->typeLiaison = $liaison->getTypeLiaison();
                $demarche->poidsImpact = $liaison->getPoidsImpact();
                $demarches[] = $demarche;
            }
        }
        
        return $demarches;
    }
    
    /**
     * Vérifie si des démarches administratives bloquent la progression de l'objectif
     * @return array Liste des démarches bloquantes
     */
    public function getDemarchesBloquantes(): array {
        $liaisons = ElaskaParticulierObjectifDemarche::findAllBy([
            'objectif_id' => $this->id,
            'type_liaison' => 'bloquant'
        ]);
        
        if (empty($liaisons)) {
            return [];
        }
        
        $demarches_bloquantes = [];
        foreach ($liaisons as $liaison) {
            if ($liaison->estBloquant()) {
                $demarche = $liaison->getDemarche();
                if ($demarche) {
                    $demarches_bloquantes[] = $demarche;
                }
            }
        }
        
        return $demarches_bloquantes;
    }
    
    /**
     * Recalcule le taux de progression en tenant compte des démarches liées
     * @return float Taux de progression ajusté (0-100)
     */
    public function calculerProgressionAvecDemarches(): float {
        // Si l'objectif est déjà complété ou abandonné, utiliser la progression actuelle
        if ($this->statut == 'complete') {
            return 100;
        }
        
        if ($this->statut == 'abandonne') {
            return $this->progression;
        }
        
        // Vérifier les démarches bloquantes
        $demarches_bloquantes = $this->getDemarchesBloquantes();
        if (!empty($demarches_bloquantes)) {
            // Il y a des démarches bloquantes, ajuster la progression en conséquence
            return $this->progression * 0.8; // Réduction de 20% de la progression
        }
        
        // Récupérer toutes les démarches liées
        $liaisons = ElaskaParticulierObjectifDemarche::findAllBy([
            'objectif_id' => $this->id
        ]);
        
        if (empty($liaisons)) {
            // Pas de démarches liées, utiliser la progression actuelle
            return $this->progression;
        }
        
        // Calculer l'impact des démarches sur la progression
        $progression_base = $this->progression;
        $impact_total = 0;
        $facteur_total = 0;
        
        foreach ($liaisons as $liaison) {
            $impact = $liaison->evaluerImpact();
            $poids = $liaison->getPoidsImpact();
            $impact_total += $impact * $poids;
            $facteur_total += $poids;
        }
        
        // Si aucun facteur d'impact, utiliser la progression actuelle
        if ($facteur_total == 0) {
            return $progression_base;
        }
        
        // Combiner la progression de base (70%) et l'impact des démarches (30%)
        $progression_ajustee = ($progression_base * 0.7) + (($impact_total / $facteur_total) * 100 * 0.3);
        
        // Limiter entre 0 et 100
        return max(0, min(100, $progression_ajustee));
    }
    
    /**
     * Met à jour la progression de l'objectif en tenant compte des démarches liées
     * @return bool Succès de l'opération
     */
    public function updateProgression(): bool {
        // Recalculer la progression
        $nouvelle_progression = $this->calculerProgressionAvecDemarches();
        
        // Si la progression a changé de manière significative, mettre à jour
        if (abs($nouvelle_progression - $this->progression) >= 1) {
            $this->progression = $nouvelle_progression;
            $this->date_modification = new DateTime();
            
            // Si la progression atteint 100%, marquer comme complété
            if ($this->progression >= 100 && $this->statut == 'actif') {
                $this->statut = 'complete';
                $this->date_completion = new DateTime();
            }
            
            return $this->save();
        }
        
        return true;
    }
    
    /**
     * Lie une démarche administrative à cet objectif
     * @param int $demarche_id ID de la démarche à lier
     * @param string $type_liaison Type de liaison (prerequis, contributif, bloquant)
     * @param float $poids_impact Poids d'impact (0-1)
     * @param string $description Description optionnelle de la liaison
     * @return bool Succès de l'opération
     */
    public function lierDemarche(int $demarche_id, string $type_liaison = 'contributif', 
                                float $poids_impact = 0.5, string $description = ''): bool {
        // Vérifier si la démarche existe
        $demarche = ElaskaParticulierDemarche::findById($demarche_id);
        if (!$demarche) {
            ElaskaLog::error("Tentative de liaison avec une démarche inexistante #$demarche_id");
            return false;
        }
        
        // Vérifier si la liaison existe déjà
        $liaison_existante = ElaskaParticulierObjectifDemarche::findOneBy([
            'objectif_id' => $this->id,
            'demarche_id' => $demarche_id
        ]);
        
        if ($liaison_existante) {
            // Mettre à jour la liaison existante
            $liaison_existante->setTypeLiaison($type_liaison);
            $liaison_existante->setPoidsImpact($poids_impact);
            $liaison_existante->setDescription($description);
            return $liaison_existante->save();
        }
        
        // Créer une nouvelle liaison
        $liaison = new ElaskaParticulierObjectifDemarche();
        $liaison->setObjectifId($this->id);
        $liaison->setDemarcheId($demarche_id);
        $liaison->setTypeLiaison($type_liaison);
        $liaison->setPoidsImpact($poids_impact);
        $liaison->setDescription($description);
        
        if ($liaison->save()) {
            // Mettre à jour la progression après la liaison
            $this->updateProgression();
            return true;
        }
        
        return false;
    }
    
    /**
     * Supprime la liaison avec une démarche
     * @param int $demarche_id ID de la démarche à délier
     * @return bool Succès de l'opération
     */
    public function delierDemarche(int $demarche_id): bool {
        $liaison = ElaskaParticulierObjectifDemarche::findOneBy([
            'objectif_id' => $this->id,
            'demarche_id' => $demarche_id
        ]);
        
        if ($liaison && $liaison->delete()) {
            // Mettre à jour la progression après la suppression
            $this->updateProgression();
            return true;
        }
        
        return false;
    }
}
