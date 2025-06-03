<?php
/**
 * eLaska - Classe pour gérer les données spécifiques aux clients associations
 * Date: 2025-05-30
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaAssociation', false)) {

class ElaskaAssociation extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_association';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_association';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'group@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var int ID du tiers eLaska parent
     */
    public $fk_elaska_tiers;
    
    //
    // IDENTIFICATION ET INFORMATIONS GÉNÉRALES
    //
    
    /**
     * @var string Numéro RNA (Répertoire National des Associations)
     */
    public $rna;
    
    /**
     * @var string Numéro SIRET de l'association (si applicable)
     */
    public $siret;
    
    /**
     * @var string Sigle de l'association
     */
    public $sigle_association;
    
    /**
     * @var string Date de création de l'association (format YYYY-MM-DD)
     */
    public $date_creation_asso;
    
    /**
     * @var string Date de publication au Journal Officiel (format YYYY-MM-DD)
     */
    public $date_publication_jo;
    
    /**
     * @var string Numéro de parution au JOAFE
     */
    public $numero_publication_jo;
    
    /**
     * @var string Objet social résumé de l'association
     */
    public $objet_social;
    
    /**
     * @var string Objet social détaillé
     */
    public $objet_social_detaille_text;
    
    /**
     * @var string Code de la catégorie d'association (dictionnaire)
     */
    public $categorie_association_code;
    
    /**
     * @var string Code de la sous-catégorie d'association (dictionnaire)
     */
    public $sous_categorie_code;
    
    /**
     * @var string Code du rayonnement de l'association (dictionnaire)
     */
    public $rayonnement_code;
    
    /**
     * @var string Nom de la fédération ou union d'appartenance
     */
    public $federation_affiliation_nom;
    
    /**
     * @var string Numéro d'affiliation à la fédération
     */
    public $federation_affiliation_numero;
    
    /**
     * @var int Reconnue d'utilité publique (0=non, 1=oui)
     */
    public $statut_utilite_publique;
    
    /**
     * @var string Date de reconnaissance d'utilité publique (format YYYY-MM-DD)
     */
    public $date_reconnaissance_utilite_publique;
    
    /**
     * @var int Agrément Jeunesse et Sports (0=non, 1=oui)
     */
    public $agrement_jeunesse_sport;
    
    /**
     * @var string Autres agréments (texte libre)
     */
    public $agrement_autre_texte;
    
    /**
     * @var string Code du secteur d'activité principal (dictionnaire)
     */
    public $secteur_activite_principal_asso_code;
    
    /**
     * @var string Code du public cible principal (dictionnaire)
     */
    public $public_cible_principal_code;
    
    /**
     * @var string Valeurs portées par l'association
     */
    public $valeurs_association;

    //
    // GOUVERNANCE ET ORGANISATION INTERNE
    //
    
    /**
     * @var string Code du mode de gouvernance (dictionnaire)
     */
    public $mode_gouvernance_code;
    
    /**
     * @var string Nom du Président(e)
     */
    public $nom_president;
    
    /**
     * @var string Nom du Trésorier(e)
     */
    public $nom_tresorier;
    
    /**
     * @var string Nom du Secrétaire
     */
    public $nom_secretaire;
    
    /**
     * @var int Nombre de membres au CA
     */
    public $nombre_administrateurs;
    
    /**
     * @var int Durée du mandat des administrateurs (années)
     */
    public $duree_mandat_administrateurs_ans;
    
    /**
     * @var int Existence de commissions de travail (0=non, 1=oui)
     */
    public $existence_commissions_travail;
    
    /**
     * @var string Description des commissions
     */
    public $commissions_travail_description_text;
    
    /**
     * @var int ID du document règlement intérieur (lien vers ElaskaDocument)
     */
    public $reglement_interieur_document_id;
    
    /**
     * @var int ID du document statuts (lien vers ElaskaDocument)
     */
    public $statuts_association_document_id;
    
    /**
     * @var string Date de la dernière Assemblée Générale (format YYYY-MM-DD)
     */
    public $date_derniere_ag;
    
    /**
     * @var string Date de la dernière modification des statuts (format YYYY-MM-DD)
     */
    public $date_modification_statuts;
    
    /**
     * @var string Date du prochain Conseil d'Administration (format YYYY-MM-DD)
     */
    public $date_prochain_ca;
    
    /**
     * @var string Date de la prochaine Assemblée Générale (format YYYY-MM-DD)
     */
    public $date_prochaine_ag;
    
    /**
     * @var string Code de périodicité des réunions du CA (dictionnaire)
     */
    public $periodicite_reunion_ca_code;
    
    /**
     * @var string Code de périodicité des réunions du Bureau (dictionnaire)
     */
    public $periodicite_reunion_bureau_code;

    //
    // MEMBRES, ADHÉRENTS, BÉNÉVOLES
    //
    
    /**
     * @var int Nombre total de membres actifs
     */
    public $nombre_membres;
    
    /**
     * @var int Nombre d'adhérents à jour de cotisation
     */
    public $nombre_adherents;
    
    /**
     * @var int Nombre de bénévoles actifs
     */
    public $nombre_benevoles;
    
    /**
     * @var float Nombre de salariés en Équivalent Temps Plein
     */
    public $nombre_salaries_etp;
    
    /**
     * @var float Montant de la cotisation annuelle moyenne
     */
    public $montant_cotisation_annuelle_membre;
    
    /**
     * @var float Montant moyen d'un don
     */
    public $montant_moyen_don;
    
    /**
     * @var string Description du profil type des bénévoles
     */
    public $profil_type_benevole_text;
    
    /**
     * @var string Actions de formation pour les bénévoles
     */
    public $actions_formation_benevoles_text;

    //
    // FINANCES ET RESSOURCES
    //
    
    /**
     * @var float Budget annuel total de l'association
     */
    public $budget_annuel_total;
    
    /**
     * @var float Part des subventions dans le budget en %
     */
    public $part_subventions_budget_pct;
    
    /**
     * @var string Code de l'origine principale des ressources (dictionnaire)
     */
    public $origine_principale_ressources_code;
    
    /**
     * @var float Montant des subventions publiques N-1
     */
    public $montant_subventions_publiques_n1;
    
    /**
     * @var float Montant des dons et mécénat N-1
     */
    public $montant_dons_mecenat_n1;
    
    /**
     * @var float Montant des cotisations N-1
     */
    public $montant_cotisations_n1;
    
    /**
     * @var float Résultat de l'exercice N-1
     */
    public $resultat_exercice_n1;
    
    /**
     * @var float Montant des fonds propres
     */
    public $fonds_propres;
    
    /**
     * @var float Montant de la trésorerie actuelle
     */
    public $tresorerie_actuelle;
    
    /**
     * @var string Code du régime fiscal de l'association (dictionnaire)
     */
    public $regime_fiscal_asso_code;
    
    /**
     * @var int Association assujettie à la TVA (0=non, 1=oui)
     */
    public $assujettissement_tva;
    
    /**
     * @var string Code du mode d'organisation comptable (dictionnaire)
     */
    public $mode_organisation_comptable_code;
    
    /**
     * @var string Nom du cabinet de l'expert-comptable (si externe)
     */
    public $expert_comptable_asso_nom_cabinet;
    
    /**
     * @var string Nom du cabinet du CAC (si applicable)
     */
    public $commissaire_aux_comptes_asso_nom_cabinet;
    
    /**
     * @var int ID du document plan de trésorerie (lien vers ElaskaDocument)
     */
    public $plan_tresorerie_previsionnel_document_id;
    
    /**
     * @var int ID du document budget prévisionnel (lien vers ElaskaDocument)
     */
    public $budget_previsionnel_document_id;
    
    /**
     * @var int ID du document dernier rapport d'activité (lien vers ElaskaDocument)
     */
    public $rapport_activite_dernier_document_id;

    //
    // LOCAUX
    //
    
    /**
     * @var string Description des locaux utilisés
     */
    public $locaux_description;
    
    /**
     * @var string Code du statut d'occupation des locaux (dictionnaire)
     */
    public $statut_occupation_locaux_asso_code;

    //
    // PROJETS ET ACTIVITÉS
    //
    
    /**
     * @var string Description des projets phares de l'association
     */
    public $projets_phares;
    
    /**
     * @var int Nombre de projets actuellement en cours
     */
    public $nombre_projets_en_cours;
    
    /**
     * @var string Description de la mesure de l'impact social
     */
    public $impact_social_mesure_text;
    
    /**
     * @var string Principaux partenaires opérationnels
     */
    public $partenaires_operationnels_principaux_text;
    
    /**
     * @var string Appartenance à d'autres réseaux
     */
    public $reseaux_appartenance_text;

    //
    // COMMUNICATION (ASSOCIATION)
    //
    
    /**
     * @var string Site web de l'association (si différent du tiers)
     */
    public $site_web_association;
    
    /**
     * @var string Supports de communication utilisés
     */
    public $support_communication_utilises_text;
    
    /**
     * @var string Nom du responsable communication
     */
    public $personne_chargee_communication_nom;

    //
    // BESOINS ET NOTES
    //
    
    /**
     * @var string Besoins spécifiques d'accompagnement
     */
    public $besoins_accompagnement_specifiques_asso;
    
    /**
     * @var string Notes internes réservées à eLaska
     */
    public $notes_internes_asso;

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
        
        // IDENTIFICATION ET INFORMATIONS GÉNÉRALES
        'rna' => array('type' => 'varchar(20)', 'label' => 'RNA', 'enabled' => 1, 'position' => 20, 'notnull' => 0, 'visible' => 1),
        'siret' => array('type' => 'varchar(14)', 'label' => 'SIRET', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'sigle_association' => array('type' => 'varchar(50)', 'label' => 'SigleAssociation', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'date_creation_asso' => array('type' => 'date', 'label' => 'DateCreationAsso', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'date_publication_jo' => array('type' => 'date', 'label' => 'DatePublicationJO', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'numero_publication_jo' => array('type' => 'varchar(50)', 'label' => 'NumeroPublicationJO', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'objet_social' => array('type' => 'text', 'label' => 'ObjetSocial', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'objet_social_detaille_text' => array('type' => 'text', 'label' => 'ObjetSocialDetaille', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'categorie_association_code' => array('type' => 'varchar(50)', 'label' => 'CategorieAssociationCode', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'sous_categorie_code' => array('type' => 'varchar(50)', 'label' => 'SousCategorieCode', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'rayonnement_code' => array('type' => 'varchar(30)', 'label' => 'RayonnementCode', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'federation_affiliation_nom' => array('type' => 'varchar(255)', 'label' => 'FederationAffiliationNom', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'federation_affiliation_numero' => array('type' => 'varchar(50)', 'label' => 'FederationAffiliationNumero', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'statut_utilite_publique' => array('type' => 'boolean', 'label' => 'StatutUtilitePublique', 'enabled' => 1, 'position' => 150, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'date_reconnaissance_utilite_publique' => array('type' => 'date', 'label' => 'DateReconnaissanceUtilitePublique', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        'agrement_jeunesse_sport' => array('type' => 'boolean', 'label' => 'AgrementJeunesseSport', 'enabled' => 1, 'position' => 170, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'agrement_autre_texte' => array('type' => 'text', 'label' => 'AgrementAutre', 'enabled' => 1, 'position' => 180, 'notnull' => 0, 'visible' => 1),
        'secteur_activite_principal_asso_code' => array('type' => 'varchar(50)', 'label' => 'SecteurActivitePrincipalAssoCode', 'enabled' => 1, 'position' => 190, 'notnull' => 0, 'visible' => 1),
        'public_cible_principal_code' => array('type' => 'varchar(30)', 'label' => 'PublicCiblePrincipalCode', 'enabled' => 1, 'position' => 200, 'notnull' => 0, 'visible' => 1),
        'valeurs_association' => array('type' => 'text', 'label' => 'ValeursAssociation', 'enabled' => 1, 'position' => 210, 'notnull' => 0, 'visible' => 1),
        
        // GOUVERNANCE ET ORGANISATION INTERNE
        'mode_gouvernance_code' => array('type' => 'varchar(30)', 'label' => 'ModeGouvernanceCode', 'enabled' => 1, 'position' => 300, 'notnull' => 0, 'visible' => 1),
        'nom_president' => array('type' => 'varchar(255)', 'label' => 'NomPresident', 'enabled' => 1, 'position' => 310, 'notnull' => 0, 'visible' => 1),
        'nom_tresorier' => array('type' => 'varchar(255)', 'label' => 'NomTresorier', 'enabled' => 1, 'position' => 320, 'notnull' => 0, 'visible' => 1),
        'nom_secretaire' => array('type' => 'varchar(255)', 'label' => 'NomSecretaire', 'enabled' => 1, 'position' => 330, 'notnull' => 0, 'visible' => 1),
        'nombre_administrateurs' => array('type' => 'integer', 'label' => 'NombreAdministrateurs', 'enabled' => 1, 'position' => 340, 'notnull' => 0, 'visible' => 1),
        'duree_mandat_administrateurs_ans' => array('type' => 'integer', 'label' => 'DureeMandatAdministrateursAns', 'enabled' => 1, 'position' => 350, 'notnull' => 0, 'visible' => 1),
        'existence_commissions_travail' => array('type' => 'boolean', 'label' => 'ExistenceCommissionsTravail', 'enabled' => 1, 'position' => 360, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'commissions_travail_description_text' => array('type' => 'text', 'label' => 'CommissionsTravailDescription', 'enabled' => 1, 'position' => 370, 'notnull' => 0, 'visible' => 1),
        'reglement_interieur_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'ReglementInterieurDocumentID', 'enabled' => 1, 'position' => 380, 'notnull' => 0, 'visible' => 1),
        'statuts_association_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'StatutsAssociationDocumentID', 'enabled' => 1, 'position' => 390, 'notnull' => 0, 'visible' => 1),
        'date_derniere_ag' => array('type' => 'date', 'label' => 'DateDerniereAG', 'enabled' => 1, 'position' => 400, 'notnull' => 0, 'visible' => 1),
        'date_modification_statuts' => array('type' => 'date', 'label' => 'DateModificationStatuts', 'enabled' => 1, 'position' => 410, 'notnull' => 0, 'visible' => 1),
        'date_prochain_ca' => array('type' => 'date', 'label' => 'DateProchainCA', 'enabled' => 1, 'position' => 420, 'notnull' => 0, 'visible' => 1),
        'date_prochaine_ag' => array('type' => 'date', 'label' => 'DateProchaineAG', 'enabled' => 1, 'position' => 430, 'notnull' => 0, 'visible' => 1),
        'periodicite_reunion_ca_code' => array('type' => 'varchar(30)', 'label' => 'PeriodiciteReunionCACode', 'enabled' => 1, 'position' => 440, 'notnull' => 0, 'visible' => 1),
        'periodicite_reunion_bureau_code' => array('type' => 'varchar(30)', 'label' => 'PeriodiciteReunionBureauCode', 'enabled' => 1, 'position' => 450, 'notnull' => 0, 'visible' => 1),
        
        // MEMBRES, ADHÉRENTS, BÉNÉVOLES
        'nombre_membres' => array('type' => 'integer', 'label' => 'NombreMembres', 'enabled' => 1, 'position' => 500, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'nombre_adherents' => array('type' => 'integer', 'label' => 'NombreAdherents', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'nombre_benevoles' => array('type' => 'integer', 'label' => 'NombreBenevoles', 'enabled' => 1, 'position' => 520, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'nombre_salaries_etp' => array('type' => 'double(10,2)', 'label' => 'NombreSalariesETP', 'enabled' => 1, 'position' => 530, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'montant_cotisation_annuelle_membre' => array('type' => 'double(10,2)', 'label' => 'MontantCotisationAnnuelleMembre', 'enabled' => 1, 'position' => 540, 'notnull' => 0, 'visible' => 1),
        'montant_moyen_don' => array('type' => 'double(10,2)', 'label' => 'MontantMoyenDon', 'enabled' => 1, 'position' => 550, 'notnull' => 0, 'visible' => 1),
        'profil_type_benevole_text' => array('type' => 'text', 'label' => 'ProfilTypeBenevole', 'enabled' => 1, 'position' => 560, 'notnull' => 0, 'visible' => 1),
        'actions_formation_benevoles_text' => array('type' => 'text', 'label' => 'ActionsFormationBenevoles', 'enabled' => 1, 'position' => 570, 'notnull' => 0, 'visible' => 1),
        
        // FINANCES ET RESSOURCES
        'budget_annuel_total' => array('type' => 'double(24,8)', 'label' => 'BudgetAnnuelTotal', 'enabled' => 1, 'position' => 600, 'notnull' => 0, 'visible' => 1),
        'part_subventions_budget_pct' => array('type' => 'double(5,2)', 'label' => 'PartSubventionsBudgetPct', 'enabled' => 1, 'position' => 610, 'notnull' => 0, 'visible' => 1),
        'origine_principale_ressources_code' => array('type' => 'varchar(50)', 'label' => 'OriginePrincipaleRessourcesCode', 'enabled' => 1, 'position' => 620, 'notnull' => 0, 'visible' => 1),
        'montant_subventions_publiques_n1' => array('type' => 'double(24,8)', 'label' => 'MontantSubventionsPubliquesN1', 'enabled' => 1, 'position' => 630, 'notnull' => 0, 'visible' => 1),
        'montant_dons_mecenat_n1' => array('type' => 'double(24,8)', 'label' => 'MontantDonsMecenatN1', 'enabled' => 1, 'position' => 640, 'notnull' => 0, 'visible' => 1),
        'montant_cotisations_n1' => array('type' => 'double(24,8)', 'label' => 'MontantCotisationsN1', 'enabled' => 1, 'position' => 650, 'notnull' => 0, 'visible' => 1),
        'resultat_exercice_n1' => array('type' => 'double(24,8)', 'label' => 'ResultatExerciceN1', 'enabled' => 1, 'position' => 660, 'notnull' => 0, 'visible' => 1),
        'fonds_propres' => array('type' => 'double(24,8)', 'label' => 'FondsPropres', 'enabled' => 1, 'position' => 670, 'notnull' => 0, 'visible' => 1),
        'tresorerie_actuelle' => array('type' => 'double(24,8)', 'label' => 'TresorerieActuelle', 'enabled' => 1, 'position' => 680, 'notnull' => 0, 'visible' => 1),
        'regime_fiscal_asso_code' => array('type' => 'varchar(50)', 'label' => 'RegimeFiscalAssoCode', 'enabled' => 1, 'position' => 690, 'notnull' => 0, 'visible' => 1),
        'assujettissement_tva' => array('type' => 'boolean', 'label' => 'AssujettissementTVA', 'enabled' => 1, 'position' => 700, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'mode_organisation_comptable_code' => array('type' => 'varchar(50)', 'label' => 'ModeOrganisationComptableCode', 'enabled' => 1, 'position' => 710, 'notnull' => 0, 'visible' => 1),
        'expert_comptable_asso_nom_cabinet' => array('type' => 'varchar(255)', 'label' => 'ExpertComptableAssoNomCabinet', 'enabled' => 1, 'position' => 720, 'notnull' => 0, 'visible' => 1),
        'commissaire_aux_comptes_asso_nom_cabinet' => array('type' => 'varchar(255)', 'label' => 'CommissaireAuxComptesAssoNomCabinet', 'enabled' => 1, 'position' => 730, 'notnull' => 0, 'visible' => 1),
        'plan_tresorerie_previsionnel_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'PlanTresoreriePrevDocumentID', 'enabled' => 1, 'position' => 740, 'notnull' => 0, 'visible' => 1),
        'budget_previsionnel_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'BudgetPrevDocumentID', 'enabled' => 1, 'position' => 750, 'notnull' => 0, 'visible' => 1),
        'rapport_activite_dernier_document_id' => array('type' => 'integer:ElaskaDocument:custom/elaska/class/elaska_document.class.php', 'label' => 'RapportActiviteDocumentID', 'enabled' => 1, 'position' => 760, 'notnull' => 0, 'visible' => 1),
        
        // LOCAUX
        'locaux_description' => array('type' => 'text', 'label' => 'LocauxDescription', 'enabled' => 1, 'position' => 800, 'notnull' => 0, 'visible' => 1),
        'statut_occupation_locaux_asso_code' => array('type' => 'varchar(50)', 'label' => 'StatutOccupationLocauxAssoCode', 'enabled' => 1, 'position' => 810, 'notnull' => 0, 'visible' => 1),
        
        // PROJETS ET ACTIVITÉS
        'projets_phares' => array('type' => 'text', 'label' => 'ProjetsPhares', 'enabled' => 1, 'position' => 900, 'notnull' => 0, 'visible' => 1),
        'nombre_projets_en_cours' => array('type' => 'integer', 'label' => 'NombreProjetsEnCours', 'enabled' => 1, 'position' => 910, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'impact_social_mesure_text' => array('type' => 'text', 'label' => 'ImpactSocialMesure', 'enabled' => 1, 'position' => 920, 'notnull' => 0, 'visible' => 1),
        'partenaires_operationnels_principaux_text' => array('type' => 'text', 'label' => 'PartenairesOperationnelsPrincipaux', 'enabled' => 1, 'position' => 930, 'notnull' => 0, 'visible' => 1),
        'reseaux_appartenance_text' => array('type' => 'text', 'label' => 'ReseauxAppartenance', 'enabled' => 1, 'position' => 940, 'notnull' => 0, 'visible' => 1),
        
        // COMMUNICATION (ASSOCIATION)
        'site_web_association' => array('type' => 'varchar(255)', 'label' => 'SiteWebAssociation', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => 1),
        'support_communication_utilises_text' => array('type' => 'text', 'label' => 'SupportCommunicationUtilises', 'enabled' => 1, 'position' => 1010, 'notnull' => 0, 'visible' => 1),
        'personne_chargee_communication_nom' => array('type' => 'varchar(255)', 'label' => 'PersonneChargeeCommunicationNom', 'enabled' => 1, 'position' => 1020, 'notnull' => 0, 'visible' => 1),
        
        // BESOINS ET NOTES
        'besoins_accompagnement_specifiques_asso' => array('type' => 'text', 'label' => 'BesoinsAccompagnementSpecifiquesAsso', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'notes_internes_asso' => array('type' => 'text', 'label' => 'NotesInternesAsso', 'enabled' => 1, 'position' => 1110, 'notnull' => 0, 'visible' => 1),
        
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
        
        // Par défaut, l'association est active
        if (!isset($this->status)) $this->status = 1;
        
        // Valeurs par défaut pour les champs booléens
        $this->statut_utilite_publique = isset($this->statut_utilite_publique) ? $this->statut_utilite_publique : 0;
        $this->agrement_jeunesse_sport = isset($this->agrement_jeunesse_sport) ? $this->agrement_jeunesse_sport : 0;
        $this->existence_commissions_travail = isset($this->existence_commissions_travail) ? $this->existence_commissions_travail : 0;
        $this->assujettissement_tva = isset($this->assujettissement_tva) ? $this->assujettissement_tva : 0;
        
        // Valeurs par défaut pour les champs numériques
        $this->nombre_membres = isset($this->nombre_membres) ? $this->nombre_membres : 0;
        $this->nombre_adherents = isset($this->nombre_adherents) ? $this->nombre_adherents : 0;
        $this->nombre_benevoles = isset($this->nombre_benevoles) ? $this->nombre_benevoles : 0;
        $this->nombre_salaries_etp = isset($this->nombre_salaries_etp) ? $this->nombre_salaries_etp : 0;
        $this->nombre_projets_en_cours = isset($this->nombre_projets_en_cours) ? $this->nombre_projets_en_cours : 0;
    }

    /**
     * Crée une association dans la base de données
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
     * Charge une association depuis la base de données par son ID
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
     * Charge une association depuis la base de données par l'ID du tiers associé
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
     * Charge une association depuis la base de données par son numéro RNA
     *
     * @param string $rna      Numéro RNA à rechercher
     * @return int             <0 si erreur, 0 si non trouvé, >0 si OK
     */
    public function fetchByRNA($rna)
    {
        if (empty($rna)) return -1;
        
        $sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE rna = '".$this->db->escape($rna)."'";
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
     * Met à jour une association dans la base de données
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
     * Supprime une association de la base de données
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
     * Vérifie si un numéro RNA est valide
     *
     * @param string $rna RNA à valider (ex: W123456789)
     * @return bool       true si valide, false sinon
     */
    public static function isRNAValid($rna)
    {
        // Un RNA valide commence par W suivi de 9 chiffres
        return (bool) preg_match('/^W\d{9}$/', $rna);
    }
    
    /**
     * Calcule l'âge de l'association en années
     * 
     * @return int        Age en années, -1 si la date de création n'est pas définie
     */
    public function getAge()
    {
        if (empty($this->date_creation_asso)) {
            return -1;
        }
        
        try {
            $creation_date = new DateTime($this->date_creation_asso);
            $today = new DateTime();
            $age = $today->diff($creation_date);
            
            return $age->y;
        } catch (Exception $e) {
            return -1;
        }
    }
    
    /**
     * Méthode factorisée pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                     Objet de traduction
     * @param string    $dictionary_table_suffix_short Suffixe court du nom de la table dictionnaire (ex: 'categorie' pour llx_c_elaska_asso_categorie)
     * @param bool      $usekeys                   True pour retourner tableau associatif code=>label
     * @param bool      $show_empty                True pour ajouter une option vide
     * @return array                               Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix_short, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_asso_".$dictionary_table_suffix_short;
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
    
    /**
     * Récupère les options du dictionnaire des catégories d'association
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getCategorieAssociationOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'categorie', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des sous-catégories d'association
     *
     * @param Translate $langs          Objet de traduction
     * @param string    $parent_code    Code de la catégorie parente pour filtrer
     * @param bool      $usekeys        True pour retourner tableau associatif code=>label
     * @param bool      $show_empty     True pour ajouter une option vide
     * @return array                    Tableau d'options
     */
    public static function getSousCategorieOptions($langs, $parent_code = '', $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_asso_sous_categorie";
        $sql.= " WHERE active = 1";
        if (!empty($parent_code)) {
            $sql.= " AND fk_code_cat_parent = '".$db->escape($parent_code)."'";
        }
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
    
    /**
     * Récupère les options du dictionnaire des rayonnements d'association
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getRayonnementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'rayonnement', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des régimes fiscaux d'association
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getRegimeFiscalAssoOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'regime_fiscal', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des organisations comptables d'association
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getOrgaComptaOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'orga_compta', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des statuts d'occupation des locaux d'association
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutOccupationLocauxAssoOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_occupation_locaux', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des secteurs d'activité d'association
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSecteurActiviteAssoOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'secteur_activite', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des publics cibles d'association
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getPublicCibleOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'public_cible', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des modes de gouvernance d'association
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getModeGouvernanceOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'mode_gouvernance', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des périodicités de réunion
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getPeriodiciteReunionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'periodicite_reunion', $usekeys, $show_empty);
    }
    
    /**
     * Récupère les options du dictionnaire des origines de ressources d'association
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getOrigineRessourcesOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'origine_ressources', $usekeys, $show_empty);
    }
}

} // Fin de la condition if !class_exists
?>
