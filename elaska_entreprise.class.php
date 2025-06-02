<?php
/**
 * eLaska - Classe pour gérer les données spécifiques aux clients entreprises
 * Date: 2025-05-30
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaEntreprise', false)) {

class ElaskaEntreprise extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_entreprise';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_entreprise';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'building@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var int ID du tiers eLaska parent
     */
    public $fk_elaska_tiers;
    
    //
    // INFORMATIONS GÉNÉRALES ET IDENTIFICATION
    //
    
    /**
     * @var string Raison sociale
     */
    public $raison_sociale;
    
    /**
     * @var string Enseigne commerciale
     */
    public $enseigne_commerciale;
    
    /**
     * @var string Slogan
     */
    public $slogan;
    
    /**
     * @var string Nom du fichier logo
     */
    public $logo_filename;
    
    /**
     * @var string Chemin du logo
     */
    public $logo_filepath;
    
    /**
     * @var string Site web principal
     */
    public $site_web_principal;
    
    /**
     * @var string Code de la forme juridique (dictionnaire)
     */
    public $forme_juridique_code;
    
    /**
     * @var float Capital social
     */
    public $capital_social;
    
    /**
     * @var string Monnaie du capital (ex: EUR)
     */
    public $monnaie_capital;
    
    /**
     * @var string Date de création de l'entreprise (format YYYY-MM-DD)
     */
    public $date_creation_entreprise;
    
    /**
     * @var string Date d'immatriculation RCS (format YYYY-MM-DD)
     */
    public $date_immatriculation_rcs;
    
    /**
     * @var string Lieu d'immatriculation RCS
     */
    public $lieu_immatriculation_rcs;
    
    /**
     * @var string SIREN (9 chiffres)
     */
    public $siren;
    
    /**
     * @var string NIC du siège (5 chiffres pour compléter le SIRET)
     */
    public $nic_siege;
    
    /**
     * @var string Numéro de TVA intracommunautaire
     */
    public $numero_tva_intracommunautaire;
    
    /**
     * @var string Code du secteur d'activité (dictionnaire)
     */
    public $secteur_activite_code;
    
    /**
     * @var string Code APE/NAF principal
     */
    public $code_ape_naf_principal;
    
    /**
     * @var string Description des activités secondaires
     */
    public $activites_secondaires_texte;
    
    /**
     * @var string Code du statut juridique particulier (dictionnaire)
     */
    public $statut_juridique_particulier_code;
    
    /**
     * @var string Nom du groupe d'appartenance
     */
    public $groupe_appartenance_nom;
    
    /**
     * @var string SIREN du groupe d'appartenance
     */
    public $groupe_appartenance_siren;
    
    /**
     * @var int Société côtée en bourse (0=non, 1=oui)
     */
    public $est_societe_cotee;
    
    /**
     * @var string Code du pays du siège social (dictionnaire standard)
     */
    public $pays_siege_social_code;
    
    /**
     * @var string Code de la langue de communication avec l'entreprise (dictionnaire standard)
     */
    public $langue_communication_entreprise_code;
    
    //
    // ORGANISATION ET STRUCTURE
    //
    
    /**
     * @var int ID du document organigramme (lien vers ElaskaDocument)
     */
    public $organigramme_document_id;
    
    /**
     * @var int Nombre d'établissements
     */
    public $nombre_etablissements;
    
    /**
     * @var int Nombre de sites d'exploitation
     */
    public $nombre_sites_exploitation;
    
    /**
     * @var int Effectif total
     */
    public $effectif_total;
    
    /**
     * @var int Effectif cadres
     */
    public $effectif_cadres;
    
    /**
     * @var string Code de la taille de l'entreprise (dictionnaire)
     */
    public $taille_entreprise_code;
    
    /**
     * @var string Code du statut d'activité de l'entreprise (dictionnaire)
     */
    public $statut_entreprise_code;
    
    /**
     * @var string Description des principaux actionnaires
     */
    public $principaux_actionnaires_text;
    
    /**
     * @var string Nom du dirigeant principal
     */
    public $dirigeant_principal_nom;
    
    /**
     * @var string Fonction du dirigeant principal
     */
    public $dirigeant_principal_fonction;
    
    /**
     * @var string Email du dirigeant principal
     */
    public $dirigeant_principal_email;
    
    /**
     * @var string Téléphone du dirigeant principal
     */
    public $dirigeant_principal_telephone;
    
    /**
     * @var string Historique des changements clés
     */
    public $historique_changements_cles_text;
    
    //
    // FINANCES ET COMPTABILITÉ
    //
    
    /**
     * @var string Date de début d'exercice (format YYYY-MM-DD)
     */
    public $date_exercice_debut;
    
    /**
     * @var string Date de clôture de l'exercice N (format YYYY-MM-DD)
     */
    public $date_cloture_exercice_n;
    
    /**
     * @var float Chiffre d'affaires N-2
     */
    public $chiffre_affaires_n_moins_2;
    
    /**
     * @var float Résultat net N-2
     */
    public $resultat_net_n_moins_2;
    
    /**
     * @var float Chiffre d'affaires N-1
     */
    public $chiffre_affaires_n_moins_1;
    
    /**
     * @var float Résultat net N-1
     */
    public $resultat_net_n_moins_1;
    
    /**
     * @var float Chiffre d'affaires N
     */
    public $chiffre_affaires_n;
    
    /**
     * @var float Résultat net N
     */
    public $resultat_net_n;
    
    /**
     * @var string Code du régime fiscal d'imposition (dictionnaire)
     */
    public $regime_fiscal_imposition_code;
    
    /**
     * @var string Code du régime TVA (dictionnaire)
     */
    public $regime_tva_code;
    
    /**
     * @var string Code de l'organisation comptable (dictionnaire)
     */
    public $organisation_comptable_code;
    
    /**
     * @var string Nom de la banque principale
     */
    public $banque_principale_nom;
    
    /**
     * @var string Contact de la banque principale
     */
    public $banque_principale_contact;
    
    /**
     * @var string Nom du cabinet d'expert comptable
     */
    public $expert_comptable_nom_cabinet;
    
    /**
     * @var string Contact de l'expert comptable
     */
    public $expert_comptable_contact;
    
    /**
     * @var string Nom du cabinet de commissaire aux comptes
     */
    public $commissaire_aux_comptes_nom_cabinet;
    
    /**
     * @var string Contact du commissaire aux comptes
     */
    public $commissaire_aux_comptes_contact;
    
    /**
     * @var string Description des aides et subventions obtenues
     */
    public $aides_subventions_obtenues_text;
    
    /**
     * @var float Endettement financier global
     */
    public $endettement_financier_global;
    
    /**
     * @var float Capacité d'autofinancement
     */
    public $capacite_autofinancement;
    
    /**
     * @var string Notation financière
     */
    public $notation_financiere;
    
    /**
     * @var string Principaux indicateurs de performance
     */
    public $principaux_indicateurs_performance_text;
    
    //
    // RESSOURCES HUMAINES
    //
    
    /**
     * @var string Code du mode de gestion de la paie (dictionnaire)
     */
    public $mode_gestion_paie_code;
    
    /**
     * @var string Code de la convention collective (dictionnaire)
     */
    public $convention_collective_code;
    
    /**
     * @var float Taux de turnover annuel en pourcentage
     */
    public $turnover_annuel_pct;
    
    /**
     * @var float Âge moyen des salariés
     */
    public $age_moyen_salaries;
    
    /**
     * @var float Pourcentage de femmes dans l'effectif
     */
    public $pourcentage_femmes_effectif;
    
    /**
     * @var float Pourcentage de cadres dans l'effectif
     */
    public $pourcentage_cadres_effectif;
    
    /**
     * @var string Description de la politique RH
     */
    public $politique_rh_description_text;
    
    /**
     * @var int Existence d'un CSE (0=non, 1=oui)
     */
    public $existence_cse;
    
    /**
     * @var string Description des accords d'entreprise signés
     */
    public $accords_entreprise_signes_text;
    
    /**
     * @var string Description des besoins de recrutement actuels
     */
    public $besoins_recrutement_actuels_text;
    
    /**
     * @var string Nom de l'organisme de formation principal
     */
    public $organisme_formation_principal_nom;
    
    //
    // LOCAUX ET MOYENS
    //
    
    /**
     * @var string Code du statut d'occupation des locaux (dictionnaire)
     */
    public $statut_occupation_locaux_code;
    
    /**
     * @var string Code du type de locaux (dictionnaire)
     */
    public $type_locaux_code;
    
    /**
     * @var int Surface totale des locaux en m²
     */
    public $surface_totale_locaux_m2;
    
    /**
     * @var int Locaux aux normes ERP/PMR (0=non, 1=oui)
     */
    public $locaux_normes_erp_pmr;
    
    /**
     * @var string Police d'assurance des locaux professionnels
     */
    public $assurances_locaux_professionnels_police;
    
    /**
     * @var string Police d'assurance RC Pro
     */
    public $assurances_rc_pro_police;
    
    /**
     * @var string Description des principaux équipements de production
     */
    public $principaux_equipements_production_text;
    
    /**
     * @var string Description du parc informatique
     */
    public $parc_informatique_description_text;
    
    //
    // ACTIVITÉ COMMERCIALE ET MARKETING
    //
    
    /**
     * @var string Code des types de clients principaux (dictionnaire)
     */
    public $clients_principaux_types_code;
    
    /**
     * @var string Description des principaux secteurs clients
     */
    public $clients_principaux_secteurs_text;
    
    /**
     * @var string Code de la zone de chalandise principale (dictionnaire)
     */
    public $zone_chalandise_principale_code;
    
    /**
     * @var string Code des principaux canaux de distribution (dictionnaire)
     */
    public $canaux_distribution_principaux_code;
    
    /**
     * @var string Description de la stratégie de prix
     */
    public $strategie_prix_description_text;
    
    /**
     * @var string Description de l'avantage concurrentiel principal
     */
    public $avantage_concurrentiel_principal_text;
    
    /**
     * @var string Description des principaux concurrents
     */
    public $concurrents_principaux_text;
    
    /**
     * @var float Budget annuel marketing et communication
     */
    public $budget_marketing_communication_annuel;
    
    //
    // DIGITAL ET INNOVATION
    //
    
    /**
     * @var string Code du niveau de maturité digitale (dictionnaire)
     */
    public $niveau_maturite_digitale_code;
    
    /**
     * @var string Code de la stratégie de communication (dictionnaire)
     */
    public $strategie_communication_code;
    
    /**
     * @var string Code de la présence sur les réseaux sociaux (dictionnaire)
     */
    public $presence_reseaux_sociaux_code;
    
    /**
     * @var string Description des outils de gestion utilisés
     */
    public $outils_gestion_utilises_text;
    
    /**
     * @var string Description des logiciels métier spécifiques
     */
    public $logiciels_metier_specifiques_text;
    
    /**
     * @var int Utilisation d'un CRM (0=non, 1=oui)
     */
    public $utilise_crm;
    
    /**
     * @var string Nom du CRM utilisé
     */
    public $nom_crm_utilise;
    
    /**
     * @var int Utilisation d'un ERP (0=non, 1=oui)
     */
    public $utilise_erp;
    
    /**
     * @var string Nom de l'ERP utilisé
     */
    public $nom_erp_utilise;
    
    /**
     * @var string Description de la politique d'innovation
     */
    public $politique_innovation_description_text;
    
    /**
     * @var int Nombre de brevets déposés
     */
    public $brevets_deposes_nombre;
    
    /**
     * @var int Mise en place d'une veille technologique/stratégique (0=non, 1=oui)
     */
    public $veille_technologique_strategique_mise_en_place;
    
    //
    // QUALITÉ, SÉCURITÉ, ENVIRONNEMENT (QSE)
    //
    
    /**
     * @var string Description des certifications qualité obtenues
     */
    public $certifications_qualite_obtenues_text;
    
    /**
     * @var int Démarche RSE active (0=non, 1=oui)
     */
    public $demarche_rse_active;
    
    /**
     * @var string Description des actions RSE menées
     */
    public $actions_rse_menees_text;
    
    /**
     * @var string Description de la politique de gestion des déchets
     */
    public $politique_gestion_dechets_text;
    
    /**
     * @var string Date d'évaluation des risques professionnels (format YYYY-MM-DD)
     */
    public $evaluation_risques_professionnels_document_unique_date;
    
    //
    // ACCOMPAGNEMENT ET BESOINS
    //
    
    /**
     * @var string Potentiel de développement
     */
    public $potentiel_developpement;
    
    /**
     * @var string Points forts de l'entreprise
     */
    public $points_forts_entreprise;
    
    /**
     * @var string Points faibles de l'entreprise
     */
    public $points_faibles_entreprise;
    
    /**
     * @var string Besoins d'accompagnement spécifiques
     */
    public $besoins_accompagnement_specifiques;
    
    /**
     * @var string Code du type d'accompagnement recherché (dictionnaire)
     */
    public $type_accompagnement_recherche_code;

    // Champs techniques standard
    public $rowid;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $entity;
    public $status;

    /**
     * @var array Définition des champs pour le gestionnaire d'objets
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'fk_elaska_tiers' => array('type' => 'integer:ElaskaTiers:custom/elaska/class/elaska_tiers.class.php', 'label' => 'ElaskaTiersID', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1, 'foreignkey' => 'elaska_tiers.rowid'),
        
        // INFORMATIONS GÉNÉRALES ET IDENTIFICATION
        'raison_sociale' => array('type' => 'varchar(255)', 'label' => 'RaisonSociale', 'enabled' => 1, 'position' => 20, 'notnull' => 0, 'visible' => 1),
        'enseigne_commerciale' => array('type' => 'varchar(255)', 'label' => 'EnseigneCommerciale', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'slogan' => array('type' => 'varchar(255)', 'label' => 'Slogan', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'logo_filename' => array('type' => 'varchar(255)', 'label' => 'LogoFilename', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'logo_filepath' => array('type' => 'varchar(255)', 'label' => 'LogoFilepath', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'site_web_principal' => array('type' => 'varchar(255)', 'label' => 'SiteWebPrincipal', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'forme_juridique_code' => array('type' => 'varchar(50)', 'label' => 'FormeJuridiqueCode', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'capital_social' => array('type' => 'double(24,2)', 'label' => 'CapitalSocial', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'monnaie_capital' => array('type' => 'varchar(3)', 'label' => 'MonnaieCapital', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1, 'default' => 'EUR'),
        'date_creation_entreprise' => array('type' => 'date', 'label' => 'DateCreationEntreprise', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'date_immatriculation_rcs' => array('type' => 'date', 'label' => 'DateImmatriculationRCS', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'lieu_immatriculation_rcs' => array('type' => 'varchar(100)', 'label' => 'LieuImmatriculationRCS', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'siren' => array('type' => 'varchar(9)', 'label' => 'SIREN', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'nic_siege' => array('type' => 'varchar(5)', 'label' => 'NICSiege', 'enabled' => 1, 'position' => 150, 'notnull' => 0, 'visible' => 1),
        'numero_tva_intracommunautaire' => array('type' => 'varchar(20)', 'label' => 'NumTVAIntra', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        'secteur_activite_code' => array('type' => 'varchar(50)', 'label' => 'SecteurActiviteCode', 'enabled' => 1, 'position' => 170, 'notnull' => 0, 'visible' => 1),
        'code_ape_naf_principal' => array('type' => 'varchar(10)', 'label' => 'CodeAPENAF', 'enabled' => 1, 'position' => 180, 'notnull' => 0, 'visible' => 1),
        'activites_secondaires_texte' => array('type' => 'text', 'label' => 'ActivitesSecondaires', 'enabled' => 1, 'position' => 190, 'notnull' => 0, 'visible' => 1),
        'statut_juridique_particulier_code' => array('type' => 'varchar(50)', 'label' => 'StatutJuridiqueParticulierCode', 'enabled' => 1, 'position' => 200, 'notnull' => 0, 'visible' => 1),
        'groupe_appartenance_nom' => array('type' => 'varchar(255)', 'label' => 'GroupeAppartenanceNom', 'enabled' => 1, 'position' => 210, 'notnull' => 0, 'visible' => 1),
        'groupe_appartenance_siren' => array('type' => 'varchar(9)', 'label' => 'GroupeAppartenanceSIREN', 'enabled' => 1, 'position' => 220, 'notnull' => 0, 'visible' => 1),
        'est_societe_cotee' => array('type' => 'boolean', 'label' => 'SocieteCotee', 'enabled' => 1, 'position' => 230, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'pays_siege_social_code' => array('type' => 'varchar(3)', 'label' => 'PaysSiegeSocialCode', 'enabled' => 1, 'position' => 240, 'notnull' => 0, 'visible' => 1),
        'langue_communication_entreprise_code' => array('type' => 'varchar(5)', 'label' => 'LangueCommunicationEntrepriseCode', 'enabled' => 1, 'position' => 250, 'notnull' => 0, 'visible' => 1),
        
        // ORGANISATION ET STRUCTURE
        'organigramme_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'OrganigrammeDocumentID', 'enabled' => 1, 'position' => 300, 'notnull' => 0, 'visible' => 1),
        'nombre_etablissements' => array('type' => 'integer', 'label' => 'NombreEtablissements', 'enabled' => 1, 'position' => 310, 'notnull' => 0, 'visible' => 1, 'default' => '1'),
        'nombre_sites_exploitation' => array('type' => 'integer', 'label' => 'NombreSitesExploitation', 'enabled' => 1, 'position' => 320, 'notnull' => 0, 'visible' => 1, 'default' => '1'),
        'effectif_total' => array('type' => 'integer', 'label' => 'EffectifTotal', 'enabled' => 1, 'position' => 330, 'notnull' => 0, 'visible' => 1),
        'effectif_cadres' => array('type' => 'integer', 'label' => 'EffectifCadres', 'enabled' => 1, 'position' => 340, 'notnull' => 0, 'visible' => 1),
        'taille_entreprise_code' => array('type' => 'varchar(50)', 'label' => 'TailleEntrepriseCode', 'enabled' => 1, 'position' => 350, 'notnull' => 0, 'visible' => 1),
        'statut_entreprise_code' => array('type' => 'varchar(50)', 'label' => 'StatutEntrepriseCode', 'enabled' => 1, 'position' => 360, 'notnull' => 0, 'visible' => 1),
        'principaux_actionnaires_text' => array('type' => 'text', 'label' => 'PrincipauxActionnaires', 'enabled' => 1, 'position' => 370, 'notnull' => 0, 'visible' => 1),
        'dirigeant_principal_nom' => array('type' => 'varchar(255)', 'label' => 'DirigPrincipalNom', 'enabled' => 1, 'position' => 380, 'notnull' => 0, 'visible' => 1),
        'dirigeant_principal_fonction' => array('type' => 'varchar(255)', 'label' => 'DirigPrincipalFonction', 'enabled' => 1, 'position' => 390, 'notnull' => 0, 'visible' => 1),
        'dirigeant_principal_email' => array('type' => 'varchar(255)', 'label' => 'DirigPrincipalEmail', 'enabled' => 1, 'position' => 400, 'notnull' => 0, 'visible' => 1),
        'dirigeant_principal_telephone' => array('type' => 'varchar(30)', 'label' => 'DirigPrincipalTelephone', 'enabled' => 1, 'position' => 410, 'notnull' => 0, 'visible' => 1),
        'historique_changements_cles_text' => array('type' => 'text', 'label' => 'HistoriqueChangementsCles', 'enabled' => 1, 'position' => 420, 'notnull' => 0, 'visible' => 1),
        
        // FINANCES ET COMPTABILITÉ
        'date_exercice_debut' => array('type' => 'date', 'label' => 'DateExerciceDebut', 'enabled' => 1, 'position' => 500, 'notnull' => 0, 'visible' => 1),
        'date_cloture_exercice_n' => array('type' => 'date', 'label' => 'DateClotureExerciceN', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => 1),
        'chiffre_affaires_n_moins_2' => array('type' => 'double(24,2)', 'label' => 'CANMoins2', 'enabled' => 1, 'position' => 520, 'notnull' => 0, 'visible' => 1),
        'resultat_net_n_moins_2' => array('type' => 'double(24,2)', 'label' => 'ResultatNetNMoins2', 'enabled' => 1, 'position' => 530, 'notnull' => 0, 'visible' => 1),
        'chiffre_affaires_n_moins_1' => array('type' => 'double(24,2)', 'label' => 'CANMoins1', 'enabled' => 1, 'position' => 540, 'notnull' => 0, 'visible' => 1),
        'resultat_net_n_moins_1' => array('type' => 'double(24,2)', 'label' => 'ResultatNetNMoins1', 'enabled' => 1, 'position' => 550, 'notnull' => 0, 'visible' => 1),
        'chiffre_affaires_n' => array('type' => 'double(24,2)', 'label' => 'CAN', 'enabled' => 1, 'position' => 560, 'notnull' => 0, 'visible' => 1),
        'resultat_net_n' => array('type' => 'double(24,2)', 'label' => 'ResultatNetN', 'enabled' => 1, 'position' => 570, 'notnull' => 0, 'visible' => 1),
        'regime_fiscal_imposition_code' => array('type' => 'varchar(50)', 'label' => 'RegimeFiscalImpositionCode', 'enabled' => 1, 'position' => 580, 'notnull' => 0, 'visible' => 1),
        'regime_tva_code' => array('type' => 'varchar(50)', 'label' => 'RegimeTVACode', 'enabled' => 1, 'position' => 590, 'notnull' => 0, 'visible' => 1),
        'organisation_comptable_code' => array('type' => 'varchar(50)', 'label' => 'OrganisationComptableCode', 'enabled' => 1, 'position' => 600, 'notnull' => 0, 'visible' => 1),
        'banque_principale_nom' => array('type' => 'varchar(255)', 'label' => 'BanquePrincipaleNom', 'enabled' => 1, 'position' => 610, 'notnull' => 0, 'visible' => 1),
        'banque_principale_contact' => array('type' => 'text', 'label' => 'BanquePrincipaleContact', 'enabled' => 1, 'position' => 620, 'notnull' => 0, 'visible' => 1),
        'expert_comptable_nom_cabinet' => array('type' => 'varchar(255)', 'label' => 'ExpertComptableNomCabinet', 'enabled' => 1, 'position' => 630, 'notnull' => 0, 'visible' => 1),
        'expert_comptable_contact' => array('type' => 'text', 'label' => 'ExpertComptableContact', 'enabled' => 1, 'position' => 640, 'notnull' => 0, 'visible' => 1),
        'commissaire_aux_comptes_nom_cabinet' => array('type' => 'varchar(255)', 'label' => 'CAComptesNomCabinet', 'enabled' => 1, 'position' => 650, 'notnull' => 0, 'visible' => 1),
        'commissaire_aux_comptes_contact' => array('type' => 'text', 'label' => 'CAComptesContact', 'enabled' => 1, 'position' => 660, 'notnull' => 0, 'visible' => 1),
        'aides_subventions_obtenues_text' => array('type' => 'text', 'label' => 'AidesSubventionsObtenues', 'enabled' => 1, 'position' => 670, 'notnull' => 0, 'visible' => 1),
        'endettement_financier_global' => array('type' => 'double(24,2)', 'label' => 'EndettementFinancierGlobal', 'enabled' => 1, 'position' => 680, 'notnull' => 0, 'visible' => 1),
        'capacite_autofinancement' => array('type' => 'double(24,2)', 'label' => 'CapaciteAutofinancement', 'enabled' => 1, 'position' => 690, 'notnull' => 0, 'visible' => 1),
        'notation_financiere' => array('type' => 'varchar(50)', 'label' => 'NotationFinanciere', 'enabled' => 1, 'position' => 700, 'notnull' => 0, 'visible' => 1),
        'principaux_indicateurs_performance_text' => array('type' => 'text', 'label' => 'PrincipauxIndicateursPerformance', 'enabled' => 1, 'position' => 710, 'notnull' => 0, 'visible' => 1),
        
        // RESSOURCES HUMAINES
        'mode_gestion_paie_code' => array('type' => 'varchar(50)', 'label' => 'ModeGestionPaieCode', 'enabled' => 1, 'position' => 800, 'notnull' => 0, 'visible' => 1),
        'convention_collective_code' => array('type' => 'varchar(50)', 'label' => 'ConventionCollectiveCode', 'enabled' => 1, 'position' => 810, 'notnull' => 0, 'visible' => 1),
        'turnover_annuel_pct' => array('type' => 'double(5,2)', 'label' => 'TurnoverAnnuelPct', 'enabled' => 1, 'position' => 820, 'notnull' => 0, 'visible' => 1),
        'age_moyen_salaries' => array('type' => 'double(5,1)', 'label' => 'AgeMoyenSalaries', 'enabled' => 1, 'position' => 830, 'notnull' => 0, 'visible' => 1),
        'pourcentage_femmes_effectif' => array('type' => 'double(5,2)', 'label' => 'PourcentageFemmesEffectif', 'enabled' => 1, 'position' => 840, 'notnull' => 0, 'visible' => 1),
        'pourcentage_cadres_effectif' => array('type' => 'double(5,2)', 'label' => 'PourcentageCadresEffectif', 'enabled' => 1, 'position' => 850, 'notnull' => 0, 'visible' => 1),
        'politique_rh_description_text' => array('type' => 'text', 'label' => 'PolitiqueRHDescription', 'enabled' => 1, 'position' => 860, 'notnull' => 0, 'visible' => 1),
        'existence_cse' => array('type' => 'boolean', 'label' => 'ExistenceCSE', 'enabled' => 1, 'position' => 870, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'accords_entreprise_signes_text' => array('type' => 'text', 'label' => 'AccordsEntrepriseSigne', 'enabled' => 1, 'position' => 880, 'notnull' => 0, 'visible' => 1),
        'besoins_recrutement_actuels_text' => array('type' => 'text', 'label' => 'BesoinsRecrutementActuels', 'enabled' => 1, 'position' => 890, 'notnull' => 0, 'visible' => 1),
        'organisme_formation_principal_nom' => array('type' => 'varchar(255)', 'label' => 'OrganismeFormationPrincipalNom', 'enabled' => 1, 'position' => 900, 'notnull' => 0, 'visible' => 1),
        
        // LOCAUX ET MOYENS
        'statut_occupation_locaux_code' => array('type' => 'varchar(50)', 'label' => 'StatutOccupationLocauxCode', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => 1),
        'type_locaux_code' => array('type' => 'varchar(50)', 'label' => 'TypeLocauxCode', 'enabled' => 1, 'position' => 1010, 'notnull' => 0, 'visible' => 1),
        'surface_totale_locaux_m2' => array('type' => 'integer', 'label' => 'SurfaceTotaleLocauxM2', 'enabled' => 1, 'position' => 1020, 'notnull' => 0, 'visible' => 1),
        'locaux_normes_erp_pmr' => array('type' => 'boolean', 'label' => 'LocauxNormesERPPMR', 'enabled' => 1, 'position' => 1030, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'assurances_locaux_professionnels_police' => array('type' => 'varchar(100)', 'label' => 'AssurancesLocauxProPolice', 'enabled' => 1, 'position' => 1040, 'notnull' => 0, 'visible' => 1),
        'assurances_rc_pro_police' => array('type' => 'varchar(100)', 'label' => 'AssurancesRCProPolice', 'enabled' => 1, 'position' => 1050, 'notnull' => 0, 'visible' => 1),
        'principaux_equipements_production_text' => array('type' => 'text', 'label' => 'PrincipauxEquipementsProduction', 'enabled' => 1, 'position' => 1060, 'notnull' => 0, 'visible' => 1),
        'parc_informatique_description_text' => array('type' => 'text', 'label' => 'ParcInformatiqueDescription', 'enabled' => 1, 'position' => 1070, 'notnull' => 0, 'visible' => 1),
        
        // ACTIVITÉ COMMERCIALE ET MARKETING
        'clients_principaux_types_code' => array('type' => 'varchar(50)', 'label' => 'ClientsPrincipauxTypesCode', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'clients_principaux_secteurs_text' => array('type' => 'text', 'label' => 'ClientsPrincipauxSecteurs', 'enabled' => 1, 'position' => 1110, 'notnull' => 0, 'visible' => 1),
        'zone_chalandise_principale_code' => array('type' => 'varchar(50)', 'label' => 'ZoneChalandisePrincipaleCode', 'enabled' => 1, 'position' => 1120, 'notnull' => 0, 'visible' => 1),
        'canaux_distribution_principaux_code' => array('type' => 'varchar(50)', 'label' => 'CanauxDistributionPrincipauxCode', 'enabled' => 1, 'position' => 1130, 'notnull' => 0, 'visible' => 1),
        'strategie_prix_description_text' => array('type' => 'text', 'label' => 'StrategiePrixDescription', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'avantage_concurrentiel_principal_text' => array('type' => 'text', 'label' => 'AvantageConcurrentielPrincipal', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'concurrents_principaux_text' => array('type' => 'text', 'label' => 'ConcurrentsPrincipaux', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'budget_marketing_communication_annuel' => array('type' => 'double(24,2)', 'label' => 'BudgetMarketingCommAnnuel', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        
        // DIGITAL ET INNOVATION
        'niveau_maturite_digitale_code' => array('type' => 'varchar(50)', 'label' => 'NiveauMaturiteDigitaleCode', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'strategie_communication_code' => array('type' => 'varchar(50)', 'label' => 'StrategieCommunicationCode', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'presence_reseaux_sociaux_code' => array('type' => 'varchar(50)', 'label' => 'PresenceReseauxSociauxCode', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'outils_gestion_utilises_text' => array('type' => 'text', 'label' => 'OutilsGestionUtilises', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'logiciels_metier_specifiques_text' => array('type' => 'text', 'label' => 'LogicielsMetierSpecifiques', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'utilise_crm' => array('type' => 'boolean', 'label' => 'UtiliseCRM', 'enabled' => 1, 'position' => 1250, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'nom_crm_utilise' => array('type' => 'varchar(255)', 'label' => 'NomCRMUtilise', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1),
        'utilise_erp' => array('type' => 'boolean', 'label' => 'UtiliseERP', 'enabled' => 1, 'position' => 1270, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'nom_erp_utilise' => array('type' => 'varchar(255)', 'label' => 'NomERPUtilise', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'politique_innovation_description_text' => array('type' => 'text', 'label' => 'PolitiqueInnovationDescription', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1),
        'brevets_deposes_nombre' => array('type' => 'integer', 'label' => 'BrevetsDeposesNombre', 'enabled' => 1, 'position' => 1300, 'notnull' => 0, 'visible' => 1),
        'veille_technologique_strategique_mise_en_place' => array('type' => 'boolean', 'label' => 'VeilleTechnoStrategique', 'enabled' => 1, 'position' => 1310, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        
        // QUALITÉ, SÉCURITÉ, ENVIRONNEMENT (QSE)
        'certifications_qualite_obtenues_text' => array('type' => 'text', 'label' => 'CertificationsQualiteObtenues', 'enabled' => 1, 'position' => 1400, 'notnull' => 0, 'visible' => 1),
        'demarche_rse_active' => array('type' => 'boolean', 'label' => 'DemarcheRSEActive', 'enabled' => 1, 'position' => 1410, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'actions_rse_menees_text' => array('type' => 'text', 'label' => 'ActionsRSEMenees', 'enabled' => 1, 'position' => 1420, 'notnull' => 0, 'visible' => 1),
        'politique_gestion_dechets_text' => array('type' => 'text', 'label' => 'PolitiqueGestionDechets', 'enabled' => 1, 'position' => 1430, 'notnull' => 0, 'visible' => 1),
        'evaluation_risques_professionnels_document_unique_date' => array('type' => 'date', 'label' => 'EvalRisquesProfDocUniqueDate', 'enabled' => 1, 'position' => 1440, 'notnull' => 0, 'visible' => 1),
        
        // ACCOMPAGNEMENT ET BESOINS
        'potentiel_developpement' => array('type' => 'text', 'label' => 'PotentielDeveloppement', 'enabled' => 1, 'position' => 1500, 'notnull' => 0, 'visible' => 1),
        'points_forts_entreprise' => array('type' => 'text', 'label' => 'PointsFortsEntreprise', 'enabled' => 1, 'position' => 1510, 'notnull' => 0, 'visible' => 1),
        'points_faibles_entreprise' => array('type' => 'text', 'label' => 'PointsFaiblesEntreprise', 'enabled' => 1, 'position' => 1520, 'notnull' => 0, 'visible' => 1),
        'besoins_accompagnement_specifiques' => array('type' => 'text', 'label' => 'BesoinsAccompagnementSpecifiques', 'enabled' => 1, 'position' => 1530, 'notnull' => 0, 'visible' => 1),
        'type_accompagnement_recherche_code' => array('type' => 'varchar(50)', 'label' => 'TypeAccompagnementRechercheCode', 'enabled' => 1, 'position' => 1540, 'notnull' => 0, 'visible' => 1),

        // CHAMPS TECHNIQUES
        'entity' => array('type' => 'integer', 'label' => 'Entity', 'visible' => 0, 'enabled' => 1, 'position' => 1900, 'notnull' => 1, 'default' => '1'),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'visible' => 0, 'enabled' => 1, 'position' => 1910, 'notnull' => 1),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'visible' => 0, 'enabled' => 1, 'position' => 1920, 'notnull' => 1),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'visible' => 0, 'enabled' => 1, 'position' => 1930, 'notnull' => 1),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'visible' => 0, 'enabled' => 1, 'position' => 1940, 'notnull' => 0),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'visible' => 0, 'enabled' => 1, 'position' => 1950, 'notnull' => 0),
        'status' => array('type' => 'integer', 'label' => 'Status', 'visible' => 1, 'enabled' => 1, 'position' => 2000, 'notnull' => 1, 'default' => '1', 'arrayofkeyval' => array('0' => 'Inactive', '1' => 'Active'))
    );

    /**
     * Constructeur
     *
     * @param DoliDB $db Base de données
     */
    public function __construct($db)
    {
        global $conf;
        
        parent::__construct($db);
        
        // Par défaut, l'entreprise est active
        if (!isset($this->status)) $this->status = 1;
        
        // Valeurs par défaut pour les champs booléens
        $this->est_societe_cotee = isset($this->est_societe_cotee) ? $this->est_societe_cotee : 0;
        $this->existence_cse = isset($this->existence_cse) ? $this->existence_cse : 0;
        $this->locaux_normes_erp_pmr = isset($this->locaux_normes_erp_pmr) ? $this->locaux_normes_erp_pmr : 0;
        $this->utilise_crm = isset($this->utilise_crm) ? $this->utilise_crm : 0;
        $this->utilise_erp = isset($this->utilise_erp) ? $this->utilise_erp : 0;
        $this->veille_technologique_strategique_mise_en_place = isset($this->veille_technologique_strategique_mise_en_place) ? $this->veille_technologique_strategique_mise_en_place : 0;
        $this->demarche_rse_active = isset($this->demarche_rse_active) ? $this->demarche_rse_active : 0;
        
        // Valeurs par défaut pour les champs numériques
        $this->nombre_etablissements = isset($this->nombre_etablissements) ? $this->nombre_etablissements : 1;
        $this->nombre_sites_exploitation = isset($this->nombre_sites_exploitation) ? $this->nombre_sites_exploitation : 1;
    }

    /**
     * Crée une entreprise dans la base de données
     *
     * @param User $user      Utilisateur qui crée
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, ID si OK
     */
    public function create($user, $notrigger = 0)
    {
        // Vérifier que le lien vers ElaskaTiers est défini
        if (empty($this->fk_elaska_tiers)) {
            $this->error = 'ElaskaTiersIDIsMandatory';
            return -1;
        }
        
        // Valeurs par défaut
        if (empty($this->status)) $this->status = 1;
        
        $this->fk_user_creat = $user->id;
        
        return $this->createCommon($user, $notrigger);
    }

    /**
     * Charge une entreprise depuis la base de données par son ID
     *
     * @param int $id         ID de l'enregistrement à charger
     * @param string $ref     Référence (non utilisée mais requise par héritage)
     * @return int            <0 si erreur, >0 si OK
     */
    public function fetch($id, $ref = '')
    {
        return $this->fetchCommon($id, $ref);
    }

    /**
     * Charge une entreprise depuis la base de données par l'ID du tiers associé
     *
     * @param int $fk_elaska_tiers ID du tiers eLaska lié
     * @return int                 <0 si erreur, 0 si non trouvé, >0 si OK
     */
    public function fetchByTiersId($fk_elaska_tiers)
    {
        if (empty($fk_elaska_tiers)) return -1;
        
        $sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.$this->table_element;
        $sql.= ' WHERE fk_elaska_tiers = '.(int) $fk_elaska_tiers;
        $sql.= ' AND entity IN ('.getEntity($this->element).')';
        
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($obj = $this->db->fetch_object($resql)) {
                return $this->fetch($obj->rowid);
            }
            return 0; // Aucun enregistrement trouvé (pas forcément une erreur dans ce cas)
        }
        
        $this->error = $this->db->lasterror();
        return -1; // Erreur
    }

    /**
     * Met à jour une entreprise dans la base de données
     *
     * @param User $user      Utilisateur qui modifie
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function update($user, $notrigger = 0)
    {
        $this->fk_user_modif = $user->id;
        
        return $this->updateCommon($user, $notrigger);
    }

    /**
     * Supprime une entreprise de la base de données
     *
     * @param User $user      Utilisateur qui supprime
     * @param int  $notrigger 0=déclenche triggers, 1=ne déclenche pas
     * @return int            <0 si erreur, >0 si OK
     */
    public function delete($user, $notrigger = 0)
    {
        return $this->deleteCommon($user, $notrigger);
    }

    /**
     * Définit les propriétés à partir des données du formulaire
     * 
     * @param array   $keyArray          Liste des propriétés à définir
     * @param string  $prefix            Préfixe pour les noms de variables POST
     * @param boolean $excludeUndefined  Ne pas définir les propriétés non présentes dans keyArray
     * @return int                       1 si OK, 0 si aucune propriété à définir, -1 si erreur
     */
    public function setPropsFromPost($keyArray, $prefix = '', $excludeUndefined = false)
    {
        if (!is_array($keyArray) || empty($keyArray)) return 0;
        
        $updated = false;
        
        foreach ($keyArray as $key) {
            // Ne mettre à jour que les champs qui ont un paramètre POST correspondant
            $postName = empty($prefix) ? $key : $prefix.$key;
            if (GETPOSTISSET($postName)) {
                // Traitement spécifique par type de champ
                if (isset($this->fields[$key])) {
                    $type = $this->fields[$key]['type'];
                    
                    if (strpos($type, 'date') === 0) {
                        // Traitement des dates (date, datetime, timestamp)
                        $this->$key = GETPOST($postName.'_year', 'int').'-'.GETPOST($postName.'_month', 'int').'-'.GETPOST($postName.'_day', 'int');
                        if (strpos($type, 'datetime') === 0) {
                            $this->$key .= ' '.GETPOST($postName.'_hour', 'int').':'.GETPOST($postName.'_min', 'int').':00';
                        }
                    } elseif (strpos($type, 'double') === 0) {
                        // Traitement des nombres décimaux
                        $this->$key = price2num(GETPOST($postName, 'alphanohtml'));
                    } elseif (strpos($type, 'integer') === 0) {
                        // Traitement des entiers
                        $this->$key = GETPOST($postName, 'int');
                    } elseif (strpos($type, 'text') === 0) {
                        // Traitement du texte riche
                        $this->$key = GETPOST($postName, 'restricthtml');
                    } else {
                        // Traitement par défaut
                        $this->$key = GETPOST($postName, 'alphanohtml');
                    }
                    $updated = true;
                } elseif (!$excludeUndefined) {
                    // Pour les champs non définis dans $fields mais qui sont dans l'objet
                    $this->$key = GETPOST($postName, 'alphanohtml');
                    $updated = true;
                }
            }
        }
        
        return $updated ? 1 : 0;
    }
    
    /**
     * Calcule et met à jour le code de taille d'entreprise en fonction de l'effectif total
     *
     * @param User $user       Utilisateur effectuant l'action
     * @param int  $notrigger  0=déclenche triggers, 1=ne déclenche pas
     * @return int             <0 si erreur, >0 si OK
     */
        public function updateTailleEntrepriseCode($user, $notrigger = 0)
    {
        if (!isset($this->effectif_total)) return -1;
        
        $old_code = $this->taille_entreprise_code;
        
        // Attribution du code en fonction de l'effectif
        if ($this->effectif_total < 10) {
            $this->taille_entreprise_code = 'TPE';
        } elseif ($this->effectif_total >= 10 && $this->effectif_total < 250) {
            $this->taille_entreprise_code = 'PME';
        } elseif ($this->effectif_total >= 250 && $this->effectif_total < 5000) {
            $this->taille_entreprise_code = 'ETI';
        } else { // 5000 et plus
            $this->taille_entreprise_code = 'GE';
        }
        
        // Si le code a changé, mettre à jour la base de données
        if ($old_code != $this->taille_entreprise_code) {
            return $this->update($user, $notrigger);
        }
        
        return 1; // Pas de changement nécessaire
    }

    /**
     * Calcule le SIRET complet à partir du SIREN et du NIC
     *
     * @return string SIRET complet ou chaîne vide si données manquantes
     */
    public function getSiret()
    {
        if (empty($this->siren) || empty($this->nic_siege)) {
            return '';
        }
        
        return $this->siren.$this->nic_siege;
    }
    
    /**
     * Vérifie si un SIREN est valide (algorithme de Luhn)
     *
     * @param string $siren SIREN à vérifier
     * @return bool         true si valide, false sinon
     */
    public static function isSirenValid($siren)
    {
        // Le SIREN doit avoir 9 chiffres
        if (!preg_match('/^\d{9}$/', $siren)) {
            return false;
        }
        
        // Algorithme de Luhn
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $digit = (int) $siren[$i];
            if ($i % 2 == 1) { // Positions impaires (en partant de 0)
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }
        
        return $sum % 10 == 0;
    }
    
    /**
     * Vérifie si un SIRET est valide (algorithme de Luhn)
     *
     * @param string $siret SIRET à vérifier
     * @return bool         true si valide, false sinon
     */
    public static function isSiretValid($siret)
    {
        // Le SIRET doit avoir 14 chiffres
        if (!preg_match('/^\d{14}$/', $siret)) {
            return false;
        }
        
        // Vérifier que le SIREN (les 9 premiers chiffres) est valide
        if (!self::isSirenValid(substr($siret, 0, 9))) {
            return false;
        }
        
        // Algorithme de Luhn pour le SIRET entier
        $sum = 0;
        for ($i = 0; $i < 14; $i++) {
            $digit = (int) $siret[$i];
            if ($i % 2 == 0) { // Positions paires (en partant de 0)
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }
        
        return $sum % 10 == 0;
    }
    
    /**
     * Méthode factorisée pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                     Objet de traduction
     * @param string    $dictionary_table_suffix_short Suffixe court du nom de la table dictionnaire (ex: 'forme_juridique' pour llx_c_elaska_entr_forme_juridique)
     * @param bool      $usekeys                   True pour retourner tableau associatif code=>label
     * @param bool      $show_empty                True pour ajouter une option vide
     * @return array                               Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix_short, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_entr_".$dictionary_table_suffix_short;
        $sql.= " WHERE active = 1";
        $sql.= " ORDER BY position ASC, label ASC";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                if ($usekeys) {
                    $options[$obj->code] = $langs->trans($obj->label);
                } else {
                    $obj_option = new stdClass();
                    $obj_option->code = $obj->code;
                    $obj_option->label = $obj->label;
                    $obj_option->label_translated = $langs->trans($obj->label);
                    $options[] = $obj_option;
                }
            }
            $db->free($resql);
        } else {
            dol_print_error($db);
        }
        
        return $options;
    }
    
    //
    // Méthodes pour récupérer les options des dictionnaires
    //
    
    /**
     * Récupère les options du dictionnaire des formes juridiques
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getFormeJuridiqueOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'forme_juridique', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des secteurs d'activité
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSecteurActiviteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'secteur_activite', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des régimes fiscaux d'imposition
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getRegimeFiscalOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'regime_fiscal', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des régimes TVA
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getRegimeTvaOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'regime_tva', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts d'occupation des locaux
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutOccupationLocauxOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_occupation_locaux', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de locaux
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeLocauxOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_locaux', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des conventions collectives
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getConventionCollectiveOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'convention_collective', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des organisations comptables
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getOrganisationComptableOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'orga_compta', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des modes de gestion de la paie
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getModeGestionPaieOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'mode_gestion_paie', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des niveaux de maturité digitale
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getNiveauMaturiteDigitaleOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'maturite_digitale', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des stratégies de communication
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStrategieCommunicationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'strategie_comm', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des présences sur les réseaux sociaux
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getPresenceReseauxSociauxOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'presence_reseaux', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types d'accompagnement
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeAccompagnementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_accompagnement', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts juridiques particuliers
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutJuridiqueParticulierOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_juridique_particulier', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des tailles d'entreprise
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTailleEntrepriseOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'taille', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts d'activité d'entreprise
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutEntrepriseOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_activite', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des types de clients principaux
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getClientsPrincipauxTypesOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'client_type', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des zones de chalandise
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getZoneChalandiseOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'zone_chalandise', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des canaux de distribution
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getCanauxDistributionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'canal_distribution', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les pays pour les nationalités et pays de siège social
     * Utilise directement le dictionnaire standard Dolibarr
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getPaysOptions($langs, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_country";
        $sql.= " WHERE active = 1";
        $sql.= " ORDER BY code";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                if ($usekeys) {
                    $options[$obj->code] = $langs->trans($obj->label);
                } else {
                    $obj_option = new stdClass();
                    $obj_option->code = $obj->code;
                    $obj_option->label = $obj->label;
                    $obj_option->label_translated = $langs->trans($obj->label);
                    $options[] = $obj_option;
                }
            }
            $db->free($resql);
        } else {
            dol_print_error($db);
        }
        
        return $options;
    }
    
    /**
     * Récupère les options du dictionnaire des langues
     * Utilise directement le dictionnaire standard Dolibarr
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getLangueOptions($langs, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_languages";
        $sql.= " WHERE active = 1";
        $sql.= " ORDER BY code";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                if ($usekeys) {
                    $options[$obj->code] = $langs->trans($obj->label);
                } else {
                    $obj_option = new stdClass();
                    $obj_option->code = $obj->code;
                    $obj_option->label = $obj->label;
                    $obj_option->label_translated = $langs->trans($obj->label);
                    $options[] = $obj_option;
                }
            }
            $db->free($resql);
        } else {
            dol_print_error($db);
        }
        
        return $options;
    }
}

} // Fin de la condition if !class_exists
?>