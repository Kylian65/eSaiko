<?php
/**
 * eLaska - Classe pour gérer les données spécifiques aux clients particuliers
 * Date: 2025-05-31
 * Version: 4.0 (Version finale complète pour production)
 * Auteur: Kylian65
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

// Protection contre les inclusions multiples
if (!class_exists('ElaskaParticulier', false)) {

class ElaskaParticulier extends CommonObject
{
    /**
     * @var string Nom de l'élément (utilisé par les API et les hooks)
     */
    public $element = 'elaska_particulier';
    
    /**
     * @var string Nom de la table sans préfixe
     */
    public $table_element = 'elaska_particulier';
    
    /**
     * @var string Icône utilisée pour la classe
     */
    public $picto = 'user@elaska';
    
    /**
     * @var int Drapeau indiquant si l'objet est multi-entités
     */
    public $ismultientitymanaged = 1;

    /**
     * @var int ID du tiers eLaska parent
     */
    public $fk_elaska_tiers;
    
    //
    // IDENTITÉ ET ÉTAT CIVIL
    //
    
    /**
     * @var string Code du genre (dictionnaire)
     */
    public $genre_code;
    
    /**
     * @var string Nom d'usage si différent du nom de naissance
     */
    public $nom_usage;
    
    /**
     * @var string Tous les prénoms de l'état civil
     */
    public $prenoms_etat_civil;
    
    /**
     * @var string Date de naissance (format YYYY-MM-DD)
     */
    public $date_naissance;
    
    /**
     * @var string Lieu de naissance
     */
    public $lieu_naissance;
    
    /**
     * @var string Code pays de naissance (dictionnaire standard)
     */
    public $pays_naissance_code;
    
    /**
     * @var string Code département de naissance (dictionnaire standard)
     */
    public $departement_naissance_code;
    
    /**
     * @var string Code INSEE de la ville de naissance
     */
    public $ville_naissance_code_insee;
    
    /**
     * @var string Code ISO du pays de nationalité (dictionnaire standard)
     */
    public $nationalite_code;
    
    /**
     * @var string Code pays ancienne nationalité (dictionnaire standard)
     */
    public $ancienne_nationalite_code;
    
    /**
     * @var string Date de naturalisation (format YYYY-MM-DD)
     */
    public $date_naturalisation;
    
    /**
     * @var string Numéro de titre de séjour
     */
    public $titre_sejour_numero;
    
    /**
     * @var string Date d'expiration du titre de séjour (format YYYY-MM-DD)
     */
    public $titre_sejour_date_expiration;
    
    /**
     * @var string Nom de jeune fille de la mère
     */
    public $nom_jeune_fille_mere;
    
    /**
     * @var string Nom du père
     */
    public $nom_pere;
    
    /**
     * @var string Nom de la mère
     */
    public $nom_mere;

    //
    // COMPOSITION DU FOYER ET FAMILLE
    //
    
    /**
     * @var string Code de la situation familiale (dictionnaire)
     */
    public $situation_familiale_code;
    
    /**
     * @var int ID du conjoint si ElaskaParticulier
     */
    public $conjoint_fk_elaska_particulier;
    
    /**
     * @var string Nom et prénom du conjoint si externe
     */
    public $conjoint_nom_prenom_externe;
    
    /**
     * @var string Date de mariage/PACS (format YYYY-MM-DD)
     */
    public $date_mariage_pacse;
    
    /**
     * @var string Lieu de mariage/PACS
     */
    public $lieu_mariage_pacse;
    
    /**
     * @var string Date de divorce/séparation (format YYYY-MM-DD)
     */
    public $date_divorce_separation;
    
    /**
     * @var string Code du régime matrimonial (dictionnaire)
     */
    public $regime_matrimonial_code;
    
    /**
     * @var int Nombre d'enfants à charge
     */
    public $nombre_enfants_a_charge;
    
    /**
     * @var int Nombre total de personnes au foyer
     */
    public $nombre_personnes_foyer;
    
    /**
     * @var string Code du type de garde d'enfants (dictionnaire)
     */
    public $type_garde_enfants_code;
    
    /**
     * @var float Montant pension alimentaire versée
     */
    public $pension_alimentaire_versee;
    
    /**
     * @var float Montant pension alimentaire reçue
     */
    public $pension_alimentaire_recue;
    
    /**
     * @var int Nombre d'enfants en études supérieures
     */
    public $nombre_enfants_etudiants_sup;
    
    /**
     * @var string Description d'autres personnes à charge
     */
    public $personnes_a_charge_autres;
    
    //
    // LOGEMENT
    //
    
    /**
     * @var string Code du type d'habitation (dictionnaire)
     */
    public $type_logement_code;
    
    /**
     * @var string Code du statut d'occupation (dictionnaire)
     */
    public $statut_occupation_logement_code;
    
    /**
     * @var string Date d'emménagement (format YYYY-MM-DD)
     */
    public $date_emmenagement_logement_actuel;
    
    /**
     * @var float Loyer mensuel hors charges
     */
    public $loyer_mensuel_hc;
    
    /**
     * @var float Charges locatives mensuelles
     */
    public $charges_locatives_mensuelles;
    
    /**
     * @var string Nom du bailleur
     */
    public $nom_bailleur;
    
    /**
     * @var string Coordonnées du bailleur
     */
    public $coordonnees_bailleur;
    
    /**
     * @var int Surface habitable en m²
     */
    public $surface_habitable_m2;
    
    /**
     * @var int Nombre de pièces principales
     */
    public $nombre_pieces_principales;
    
    /**
     * @var int Logement en zone tendue (0=non, 1=oui)
     */
    public $logement_situe_zone_tendue;
    
    /**
     * @var int Logement conventionné APL (0=non, 1=oui)
     */
    public $logement_conventionne_apl;
    
    /**
     * @var int Consommation énergétique annuelle en kWh
     */
    public $consommation_energetique_annuelle_kwh;
    
    /**
     * @var string Code du type de chauffage (dictionnaire)
     */
    public $type_chauffage_code;
    
    /**
     * @var int Année de construction du logement (YYYY)
     */
    public $annee_construction_logement;
    
    //
    // EMPLOI ET REVENUS
    //
    
    /**
     * @var string Code de la profession (dictionnaire)
     */
    public $profession_code;
    
    /**
     * @var string Code du statut professionnel (dictionnaire)
     */
    public $statut_professionnel_code;
    
    /**
     * @var string Nom de l'employeur actuel
     */
    public $employeur_actuel;
    
    /**
     * @var string Code du secteur d'activité professionnelle (dictionnaire)
     */
    public $secteur_activite_pro_code;
    
    /**
     * @var string Date de début d'activité professionnelle (format YYYY-MM-DD)
     */
    public $date_debut_activite_pro;
    
    /**
     * @var string Code détail du type de contrat de travail (dictionnaire)
     */
    public $contrat_travail_type_detail_code;
    
    /**
     * @var string Nom de la convention collective
     */
    public $nom_convention_collective;
    
    /**
     * @var float Salaire net mensuel avant prélèvement à la source
     */
    public $salaire_net_mensuel_avant_pas;
    
    /**
     * @var float Taux de prélèvement à la source
     */
    public $taux_pas;
    
    /**
     * @var string Code de la tranche de revenus annuels nets du foyer (dictionnaire)
     */
    public $revenus_annuels_net_foyer_code;
    
    /**
     * @var float Revenus fonciers annuels
     */
    public $revenus_fonciers_annuels;
    
    /**
     * @var float Revenus de capitaux mobiliers annuels
     */
    public $revenus_capitaux_mobiliers_annuels;
    
    /**
     * @var string Description des autres revenus annuels
     */
    public $autres_revenus_annuels_description;
    
    /**
     * @var float Montant des autres revenus annuels
     */
    public $autres_revenus_annuels_montant;
    
    /**
     * @var int Éligibilité à la prime d'activité (0=non, 1=oui)
     */
    public $eligible_prime_activite;
    
    /**
     * @var string Historique d'emploi
     */
    public $historique_emploi;
    
    //
    // PATRIMOINE ET ENDETTEMENT
    //
    
    /**
     * @var string Code de l'estimation du patrimoine net (dictionnaire)
     */
    public $patrimoine_net_estime_code;
    
    /**
     * @var string Code du niveau d'endettement (dictionnaire)
     */
    public $niveau_endettement_code;
    
    //
    // SANTE ET DEPENDANCE
    //
    
    /**
     * @var string Numéro de sécurité sociale (données sensibles)
     */
    public $numero_securite_sociale;
    
    /**
     * @var string Caisse d'assurance maladie
     */
    public $caisse_assurance_maladie;
    
    /**
     * @var int Affection longue durée (0=non, 1=oui)
     */
    public $affection_longue_duree_ald;
    
    /**
     * @var string Description de l'ALD
     */
    public $description_ald;
    
    /**
     * @var int Taux d'invalidité MDPH en pourcentage
     */
    public $taux_invalidite_mdph;
    
    /**
     * @var int Bénéficiaire APA (0=non, 1=oui)
     */
    public $beneficiaire_apa;
    
    /**
     * @var string Code du groupe iso-ressources GIR (dictionnaire)
     */
    public $groupe_iso_ressources_gir_code;
    
    /**
     * @var string Description des besoins d'aide à domicile
     */
    public $besoins_aide_a_domicile_descr;
    
    /**
     * @var string Nom de la mutuelle
     */
    public $nom_mutuelle;
    
    /**
     * @var string Numéro de contrat mutuelle
     */
    public $numero_contrat_mutuelle;
    
    /**
     * @var string Nom du médecin traitant
     */
    public $medecin_traitant_nom;
    
    /**
     * @var string Coordonnées du médecin traitant
     */
    public $coordonnees_medecin_traitant;

    /**
     * @var string Personne à contacter en cas d'urgence (nom)
     */
    public $personne_contact_urgence_nom;
    
    /**
     * @var string Personne à contacter en cas d'urgence (téléphone)
     */
    public $personne_contact_urgence_tel;
    
    /**
     * @var string Code du lien de parenté pour contact d'urgence (dictionnaire)
     */
    public $personne_contact_urgence_lien_parente_code;
    
    /**
     * @var string Code de la situation handicap (dictionnaire)
     */
    public $situation_handicap_code;
    
    /**
     * @var int Allocataire CAF/AAH (0=non, 1=oui)
     */
    public $allocataire_caf_aah;
    
    /**
     * @var string Numéro d'allocataire CAF
     */
    public $numero_allocataire_caf;

    //
    // MOBILITE ET VEHICULES
    //
    
    /**
     * @var string Types de permis de conduire (A, B, etc., séparés par virgules)
     */
    public $permis_conduire_types;
    
    /**
     * @var int Nombre de véhicules au foyer
     */
    public $nombre_vehicules_foyer;
    
    /**
     * @var string Date d'obtention du permis B (format YYYY-MM-DD)
     */
    public $date_obtention_permis_b;
    
    /**
     * @var string Marque et modèle du véhicule principal
     */
    public $vehicule_principal_marque_modele;
    
    /**
     * @var int Année de mise en circulation du véhicule principal (YYYY)
     */
    public $vehicule_principal_annee_mise_circulation;
    
    /**
     * @var float Valeur estimée du véhicule principal
     */
    public $vehicule_principal_valeur_estimee;
    
    /**
     * @var string Compagnie d'assurance du véhicule principal
     */
    public $vehicule_principal_assurance_compagnie;
    
    /**
     * @var string Numéro de contrat d'assurance du véhicule principal
     */
    public $vehicule_principal_assurance_numero_contrat;
    
    /**
     * @var int Utilisation régulière des transports en commun (0=non, 1=oui)
     */
    public $utilise_transports_en_commun_regulierement;
    
    /**
     * @var string Type d'abonnement transport
     */
    public $abonnement_transport_type;
    
    //
    // FORMATION, OBJECTIFS ET PROJETS
    //
    
    /**
     * @var string Code du niveau d'études (dictionnaire)
     */
    public $niveau_etude_code;
    
    /**
     * @var string Diplômes obtenus
     */
    public $diplomes_obtenus;
    
    /**
     * @var string Compétences clés
     */
    public $competences_cles;
    
    /**
     * @var string Centres d'intérêt
     */
    public $centres_interet;
    
    /**
     * @var string Objectifs personnels
     */
    public $objectifs_personnels;
    
    /**
     * @var string Contraintes personnelles
     */
    public $contraintes_personnelles;
    
    /**
     * @var string Code de la situation judiciaire (dictionnaire)
     */
    public $situation_judiciaire_code;
    
    /**
     * @var string Code de l'horizon du projet d'achat immobilier (dictionnaire)
     */
    public $projet_achat_immobilier_horizon_code;
    
    /**
     * @var string Description du projet de voyage
     */
    public $projet_voyage_description;
    
    /**
     * @var string Description du projet de formation/reconversion
     */
    public $projet_formation_reconversion_description;
    
    /**
     * @var int Âge souhaité pour la retraite
     */
    public $projet_retraite_age_souhaite;
    
    /**
     * @var string Détail des attentes d'accompagnement eLaska
     */
    public $attentes_accompagnement_elaska_detail;
    
    /**
     * @var int Consentement RGPD (0=non, 1=oui)
     */
    public $consentement_rgpd;
    
    /**
     * @var string Date du consentement RGPD (format YYYY-MM-DD HH:MM:SS)
     */
    public $date_consentement_rgpd;
    
    /**
     * @var string Notes spécifiques au particulier
     */
    public $notes_specifiques_particulier;

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
        // Définition des champs pour le gestionnaire d'objets
        'rowid' => array('type' => 'integer', 'label' => 'ID', 'enabled' => 1, 'position' => 1, 'notnull' => 1, 'visible' => 0, 'primary' => 1),
        'fk_elaska_tiers' => array('type' => 'integer:ElaskaTiers:custom/elaska/class/elaska_tiers.class.php', 'label' => 'ElaskaTiersID', 'enabled' => 1, 'position' => 10, 'notnull' => 1, 'visible' => 1, 'foreignkey' => 'elaska_tiers.rowid'),
        
        // IDENTITÉ ET ÉTAT CIVIL
        'genre_code' => array('type' => 'varchar(20)', 'label' => 'GenreCode', 'enabled' => 1, 'position' => 20, 'notnull' => 0, 'visible' => 1),
        'nom_usage' => array('type' => 'varchar(100)', 'label' => 'NomUsage', 'enabled' => 1, 'position' => 30, 'notnull' => 0, 'visible' => 1),
        'prenoms_etat_civil' => array('type' => 'varchar(255)', 'label' => 'PrenomsEtatCivil', 'enabled' => 1, 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'date_naissance' => array('type' => 'date', 'label' => 'DateNaissance', 'enabled' => 1, 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'lieu_naissance' => array('type' => 'varchar(255)', 'label' => 'LieuNaissance', 'enabled' => 1, 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'pays_naissance_code' => array('type' => 'varchar(10)', 'label' => 'PaysNaissanceCode', 'enabled' => 1, 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'departement_naissance_code' => array('type' => 'varchar(10)', 'label' => 'DepartementNaissanceCode', 'enabled' => 1, 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'ville_naissance_code_insee' => array('type' => 'varchar(10)', 'label' => 'VilleNaissanceCodeInsee', 'enabled' => 1, 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'nationalite_code' => array('type' => 'varchar(10)', 'label' => 'NationaliteCode', 'enabled' => 1, 'position' => 100, 'notnull' => 0, 'visible' => 1),
        'ancienne_nationalite_code' => array('type' => 'varchar(10)', 'label' => 'AncienneNationaliteCode', 'enabled' => 1, 'position' => 110, 'notnull' => 0, 'visible' => 1),
        'date_naturalisation' => array('type' => 'date', 'label' => 'DateNaturalisation', 'enabled' => 1, 'position' => 120, 'notnull' => 0, 'visible' => 1),
        'titre_sejour_numero' => array('type' => 'varchar(50)', 'label' => 'TitreSejourNumero', 'enabled' => 1, 'position' => 130, 'notnull' => 0, 'visible' => 1),
        'titre_sejour_date_expiration' => array('type' => 'date', 'label' => 'TitreSejourDateExpiration', 'enabled' => 1, 'position' => 140, 'notnull' => 0, 'visible' => 1),
        'nom_jeune_fille_mere' => array('type' => 'varchar(100)', 'label' => 'NomJeuneFilleOfMere', 'enabled' => 1, 'position' => 150, 'notnull' => 0, 'visible' => 1),
        'nom_pere' => array('type' => 'varchar(100)', 'label' => 'NomPere', 'enabled' => 1, 'position' => 160, 'notnull' => 0, 'visible' => 1),
        'nom_mere' => array('type' => 'varchar(100)', 'label' => 'NomMere', 'enabled' => 1, 'position' => 170, 'notnull' => 0, 'visible' => 1),

        // COMPOSITION DU FOYER ET FAMILLE
        'situation_familiale_code' => array('type' => 'varchar(30)', 'label' => 'SituationFamilialeCode', 'enabled' => 1, 'position' => 200, 'notnull' => 0, 'visible' => 1),
        'conjoint_fk_elaska_particulier' => array('type' => 'integer:ElaskaParticulier:custom/elaska/class/elaska_particulier.class.php', 'label' => 'ConjointElaskaParticulierID', 'enabled' => 1, 'position' => 210, 'notnull' => 0, 'visible' => 1),
        'conjoint_nom_prenom_externe' => array('type' => 'varchar(255)', 'label' => 'ConjointNomPrenomExterne', 'enabled' => 1, 'position' => 220, 'notnull' => 0, 'visible' => 1),
        'date_mariage_pacse' => array('type' => 'date', 'label' => 'DateMariagePacse', 'enabled' => 1, 'position' => 230, 'notnull' => 0, 'visible' => 1),
        'lieu_mariage_pacse' => array('type' => 'varchar(255)', 'label' => 'LieuMariagePacse', 'enabled' => 1, 'position' => 240, 'notnull' => 0, 'visible' => 1),
        'date_divorce_separation' => array('type' => 'date', 'label' => 'DateDivorceSeparation', 'enabled' => 1, 'position' => 250, 'notnull' => 0, 'visible' => 1),
        'regime_matrimonial_code' => array('type' => 'varchar(50)', 'label' => 'RegimeMatrimonialCode', 'enabled' => 1, 'position' => 260, 'notnull' => 0, 'visible' => 1),
        'nombre_enfants_a_charge' => array('type' => 'integer', 'label' => 'NombreEnfantsACharge', 'enabled' => 1, 'position' => 270, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'nombre_personnes_foyer' => array('type' => 'integer', 'label' => 'NombrePersonnesFoyer', 'enabled' => 1, 'position' => 280, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'type_garde_enfants_code' => array('type' => 'varchar(30)', 'label' => 'TypeGardeEnfantsCode', 'enabled' => 1, 'position' => 290, 'notnull' => 0, 'visible' => 1),
        'pension_alimentaire_versee' => array('type' => 'double(10,2)', 'label' => 'PensionAlimentaireVersee', 'enabled' => 1, 'position' => 300, 'notnull' => 0, 'visible' => 1),
        'pension_alimentaire_recue' => array('type' => 'double(10,2)', 'label' => 'PensionAlimentaireRecue', 'enabled' => 1, 'position' => 310, 'notnull' => 0, 'visible' => 1),
        'nombre_enfants_etudiants_sup' => array('type' => 'integer', 'label' => 'NombreEnfantsEtudiantsSup', 'enabled' => 1, 'position' => 320, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'personnes_a_charge_autres' => array('type' => 'text', 'label' => 'PersonnesAChargeAutres', 'enabled' => 1, 'position' => 330, 'notnull' => 0, 'visible' => 1),

        // LOGEMENT
        'type_logement_code' => array('type' => 'varchar(50)', 'label' => 'TypeLogementCode', 'enabled' => 1, 'position' => 400, 'notnull' => 0, 'visible' => 1),
        'statut_occupation_logement_code' => array('type' => 'varchar(50)', 'label' => 'StatutOccupationLogementCode', 'enabled' => 1, 'position' => 410, 'notnull' => 0, 'visible' => 1),
        'date_emmenagement_logement_actuel' => array('type' => 'date', 'label' => 'DateEmmenagementActuel', 'enabled' => 1, 'position' => 420, 'notnull' => 0, 'visible' => 1),
        'loyer_mensuel_hc' => array('type' => 'double(10,2)', 'label' => 'LoyerMensuelHC', 'enabled' => 1, 'position' => 430, 'notnull' => 0, 'visible' => 1),
        'charges_locatives_mensuelles' => array('type' => 'double(10,2)', 'label' => 'ChargesLocativesMensuelles', 'enabled' => 1, 'position' => 440, 'notnull' => 0, 'visible' => 1),
        'nom_bailleur' => array('type' => 'text', 'label' => 'NomBailleur', 'enabled' => 1, 'position' => 450, 'notnull' => 0, 'visible' => 1),
        'coordonnees_bailleur' => array('type' => 'text', 'label' => 'CoordonneesBailleur', 'enabled' => 1, 'position' => 460, 'notnull' => 0, 'visible' => 1),
        'surface_habitable_m2' => array('type' => 'integer', 'label' => 'SurfaceHabitableM2', 'enabled' => 1, 'position' => 470, 'notnull' => 0, 'visible' => 1),
        'nombre_pieces_principales' => array('type' => 'integer', 'label' => 'NombrePiecesPrincipales', 'enabled' => 1, 'position' => 480, 'notnull' => 0, 'visible' => 1),
        'logement_situe_zone_tendue' => array('type' => 'boolean', 'label' => 'LogementZoneTendue', 'enabled' => 1, 'position' => 490, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'logement_conventionne_apl' => array('type' => 'boolean', 'label' => 'LogementConventionneAPL', 'enabled' => 1, 'position' => 500, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'consommation_energetique_annuelle_kwh' => array('type' => 'integer', 'label' => 'ConsommationEnergetiqueAnnuelleKWh', 'enabled' => 1, 'position' => 510, 'notnull' => 0, 'visible' => 1),
        'type_chauffage_code' => array('type' => 'varchar(30)', 'label' => 'TypeChauffageCode', 'enabled' => 1, 'position' => 520, 'notnull' => 0, 'visible' => 1),
        'annee_construction_logement' => array('type' => 'integer', 'label' => 'AnneeConstructionLogement', 'enabled' => 1, 'position' => 530, 'notnull' => 0, 'visible' => 1),

        // EMPLOI ET REVENUS
        'profession_code' => array('type' => 'varchar(50)', 'label' => 'ProfessionCode', 'enabled' => 1, 'position' => 600, 'notnull' => 0, 'visible' => 1),
        'statut_professionnel_code' => array('type' => 'varchar(50)', 'label' => 'StatutProfessionnelCode', 'enabled' => 1, 'position' => 610, 'notnull' => 0, 'visible' => 1),
        'employeur_actuel' => array('type' => 'varchar(255)', 'label' => 'EmployeurActuel', 'enabled' => 1, 'position' => 620, 'notnull' => 0, 'visible' => 1),
        'secteur_activite_pro_code' => array('type' => 'varchar(50)', 'label' => 'SecteurActiviteProCode', 'enabled' => 1, 'position' => 630, 'notnull' => 0, 'visible' => 1),
        'date_debut_activite_pro' => array('type' => 'date', 'label' => 'DateDebutActivitePro', 'enabled' => 1, 'position' => 640, 'notnull' => 0, 'visible' => 1),
        'contrat_travail_type_detail_code' => array('type' => 'varchar(50)', 'label' => 'ContratTravailTypeDetailCode', 'enabled' => 1, 'position' => 650, 'notnull' => 0, 'visible' => 1),
        'nom_convention_collective' => array('type' => 'varchar(255)', 'label' => 'NomConventionCollective', 'enabled' => 1, 'position' => 660, 'notnull' => 0, 'visible' => 1),
        'salaire_net_mensuel_avant_pas' => array('type' => 'double(10,2)', 'label' => 'SalaireNetMensuelAvantPAS', 'enabled' => 1, 'position' => 670, 'notnull' => 0, 'visible' => 1),
        'taux_pas' => array('type' => 'double(5,2)', 'label' => 'TauxPAS', 'enabled' => 1, 'position' => 680, 'notnull' => 0, 'visible' => 1),
        'revenus_annuels_net_foyer_code' => array('type' => 'varchar(50)', 'label' => 'RevenusAnnuelsNetFoyerCode', 'enabled' => 1, 'position' => 690, 'notnull' => 0, 'visible' => 1),
        'revenus_fonciers_annuels' => array('type' => 'double(12,2)', 'label' => 'RevenusFonciersAnnuels', 'enabled' => 1, 'position' => 700, 'notnull' => 0, 'visible' => 1),
        'revenus_capitaux_mobiliers_annuels' => array('type' => 'double(12,2)', 'label' => 'RevenusCapitauxMobiliersAnnuels', 'enabled' => 1, 'position' => 710, 'notnull' => 0, 'visible' => 1),
        'autres_revenus_annuels_description' => array('type' => 'text', 'label' => 'AutresRevenusAnnuelsDescription', 'enabled' => 1, 'position' => 720, 'notnull' => 0, 'visible' => 1),
        'autres_revenus_annuels_montant' => array('type' => 'double(12,2)', 'label' => 'AutresRevenusAnnuelsMontant', 'enabled' => 1, 'position' => 730, 'notnull' => 0, 'visible' => 1),
        'eligible_prime_activite' => array('type' => 'boolean', 'label' => 'EligiblePrimeActivite', 'enabled' => 1, 'position' => 740, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'historique_emploi' => array('type' => 'text', 'label' => 'HistoriqueEmploi', 'enabled' => 1, 'position' => 750, 'notnull' => 0, 'visible' => 1),

        // PATRIMOINE ET ENDETTEMENT
        'patrimoine_net_estime_code' => array('type' => 'varchar(50)', 'label' => 'PatrimoineNetEstimeCode', 'enabled' => 1, 'position' => 800, 'notnull' => 0, 'visible' => 1),
        'niveau_endettement_code' => array('type' => 'varchar(50)', 'label' => 'NiveauEndettementCode', 'enabled' => 1, 'position' => 810, 'notnull' => 0, 'visible' => 1),

        // SANTE ET DEPENDANCE
        'numero_securite_sociale' => array('type' => 'varchar(20)', 'label' => 'NumeroSecuriteSociale', 'enabled' => 1, 'position' => 900, 'notnull' => 0, 'visible' => 1),
        'caisse_assurance_maladie' => array('type' => 'varchar(100)', 'label' => 'CaisseAssuranceMaladie', 'enabled' => 1, 'position' => 910, 'notnull' => 0, 'visible' => 1),
        'affection_longue_duree_ald' => array('type' => 'boolean', 'label' => 'AffectionLongueALD', 'enabled' => 1, 'position' => 920, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'description_ald' => array('type' => 'text', 'label' => 'DescriptionALD', 'enabled' => 1, 'position' => 930, 'notnull' => 0, 'visible' => 1),
        'taux_invalidite_mdph' => array('type' => 'integer', 'label' => 'TauxInvaliditeMDPH', 'enabled' => 1, 'position' => 940, 'notnull' => 0, 'visible' => 1),
        'beneficiaire_apa' => array('type' => 'boolean', 'label' => 'BeneficiaireAPA', 'enabled' => 1, 'position' => 950, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'groupe_iso_ressources_gir_code' => array('type' => 'varchar(10)', 'label' => 'GroupeIsoRessourcesGIRCode', 'enabled' => 1, 'position' => 960, 'notnull' => 0, 'visible' => 1),
        'besoins_aide_a_domicile_descr' => array('type' => 'text', 'label' => 'BesoinsAideADomicileDescr', 'enabled' => 1, 'position' => 970, 'notnull' => 0, 'visible' => 1),
        'nom_mutuelle' => array('type' => 'varchar(255)', 'label' => 'NomMutuelle', 'enabled' => 1, 'position' => 980, 'notnull' => 0, 'visible' => 1),
        'numero_contrat_mutuelle' => array('type' => 'varchar(50)', 'label' => 'NumeroContratMutuelle', 'enabled' => 1, 'position' => 990, 'notnull' => 0, 'visible' => 1),
        'medecin_traitant_nom' => array('type' => 'varchar(255)', 'label' => 'MedecinTraitantNom', 'enabled' => 1, 'position' => 1000, 'notnull' => 0, 'visible' => 1),
        'coordonnees_medecin_traitant' => array('type' => 'text', 'label' => 'CoordonneesMedecinTraitant', 'enabled' => 1, 'position' => 1010, 'notnull' => 0, 'visible' => 1),
        'personne_contact_urgence_nom' => array('type' => 'varchar(255)', 'label' => 'PersonneContactUrgenceNom', 'enabled' => 1, 'position' => 1020, 'notnull' => 0, 'visible' => 1),
        'personne_contact_urgence_tel' => array('type' => 'varchar(30)', 'label' => 'PersonneContactUrgenceTel', 'enabled' => 1, 'position' => 1030, 'notnull' => 0, 'visible' => 1),
        'personne_contact_urgence_lien_parente_code' => array('type' => 'varchar(50)', 'label' => 'PersonneContactUrgenceLienParenteCode', 'enabled' => 1, 'position' => 1040, 'notnull' => 0, 'visible' => 1),
        'situation_handicap_code' => array('type' => 'varchar(50)', 'label' => 'SituationHandicapCode', 'enabled' => 1, 'position' => 1050, 'notnull' => 0, 'visible' => 1),
        'allocataire_caf_aah' => array('type' => 'boolean', 'label' => 'AllocataireCAFAAH', 'enabled' => 1, 'position' => 1060, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'numero_allocataire_caf' => array('type' => 'varchar(50)', 'label' => 'NumeroAllocataireCAF', 'enabled' => 1, 'position' => 1070, 'notnull' => 0, 'visible' => 1),

        // MOBILITE ET VEHICULES
        'permis_conduire_types' => array('type' => 'varchar(100)', 'label' => 'PermisConduireTypes', 'enabled' => 1, 'position' => 1100, 'notnull' => 0, 'visible' => 1),
        'nombre_vehicules_foyer' => array('type' => 'integer', 'label' => 'NombreVehiculesFoyer', 'enabled' => 1, 'position' => 1110, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'date_obtention_permis_b' => array('type' => 'date', 'label' => 'DateObtentionPermisB', 'enabled' => 1, 'position' => 1120, 'notnull' => 0, 'visible' => 1),
        'vehicule_principal_marque_modele' => array('type' => 'varchar(100)', 'label' => 'VehiculePrincipalMarqueModele', 'enabled' => 1, 'position' => 1130, 'notnull' => 0, 'visible' => 1),
        'vehicule_principal_annee_mise_circulation' => array('type' => 'integer', 'label' => 'VehiculePrincipalAnneeMiseCirculation', 'enabled' => 1, 'position' => 1140, 'notnull' => 0, 'visible' => 1),
        'vehicule_principal_valeur_estimee' => array('type' => 'double(10,2)', 'label' => 'VehiculePrincipalValeurEstimee', 'enabled' => 1, 'position' => 1150, 'notnull' => 0, 'visible' => 1),
        'vehicule_principal_assurance_compagnie' => array('type' => 'varchar(100)', 'label' => 'VehiculePrincipalAssuranceCompagnie', 'enabled' => 1, 'position' => 1160, 'notnull' => 0, 'visible' => 1),
        'vehicule_principal_assurance_numero_contrat' => array('type' => 'varchar(50)', 'label' => 'VehiculePrincipalAssuranceNumeroContrat', 'enabled' => 1, 'position' => 1170, 'notnull' => 0, 'visible' => 1),
        'utilise_transports_en_commun_regulierement' => array('type' => 'boolean', 'label' => 'UtiliseTransportsEnCommunRegulierement', 'enabled' => 1, 'position' => 1180, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'abonnement_transport_type' => array('type' => 'varchar(100)', 'label' => 'AbonnementTransportType', 'enabled' => 1, 'position' => 1190, 'notnull' => 0, 'visible' => 1),

        // FORMATION, OBJECTIFS ET PROJETS
        'niveau_etude_code' => array('type' => 'varchar(50)', 'label' => 'NiveauEtudeCode', 'enabled' => 1, 'position' => 1200, 'notnull' => 0, 'visible' => 1),
        'diplomes_obtenus' => array('type' => 'text', 'label' => 'DiplomesObtenus', 'enabled' => 1, 'position' => 1210, 'notnull' => 0, 'visible' => 1),
        'competences_cles' => array('type' => 'text', 'label' => 'CompetencesCles', 'enabled' => 1, 'position' => 1220, 'notnull' => 0, 'visible' => 1),
        'centres_interet' => array('type' => 'text', 'label' => 'CentresInteret', 'enabled' => 1, 'position' => 1230, 'notnull' => 0, 'visible' => 1),
        'objectifs_personnels' => array('type' => 'text', 'label' => 'ObjectifsPersonnels', 'enabled' => 1, 'position' => 1240, 'notnull' => 0, 'visible' => 1),
        'contraintes_personnelles' => array('type' => 'text', 'label' => 'ContraintesPersonnelles', 'enabled' => 1, 'position' => 1250, 'notnull' => 0, 'visible' => 1),
        'situation_judiciaire_code' => array('type' => 'varchar(50)', 'label' => 'SituationJudiciaireCode', 'enabled' => 1, 'position' => 1260, 'notnull' => 0, 'visible' => 1),
        'projet_achat_immobilier_horizon_code' => array('type' => 'varchar(30)', 'label' => 'ProjetAchatImmobilierHorizonCode', 'enabled' => 1, 'position' => 1270, 'notnull' => 0, 'visible' => 1),
        'projet_voyage_description' => array('type' => 'text', 'label' => 'ProjetVoyageDescription', 'enabled' => 1, 'position' => 1280, 'notnull' => 0, 'visible' => 1),
        'projet_formation_reconversion_description' => array('type' => 'text', 'label' => 'ProjetFormationReconversionDescription', 'enabled' => 1, 'position' => 1290, 'notnull' => 0, 'visible' => 1),
        'projet_retraite_age_souhaite' => array('type' => 'integer', 'label' => 'ProjetRetraiteAgeSouhaite', 'enabled' => 1, 'position' => 1300, 'notnull' => 0, 'visible' => 1),
        'attentes_accompagnement_elaska_detail' => array('type' => 'text', 'label' => 'AttentesAccompagnementElaskaDetail', 'enabled' => 1, 'position' => 1310, 'notnull' => 0, 'visible' => 1),
        'consentement_rgpd' => array('type' => 'boolean', 'label' => 'ConsentementRGPD', 'enabled' => 1, 'position' => 1320, 'notnull' => 1, 'visible' => 1, 'default' => '0'),
        'date_consentement_rgpd' => array('type' => 'datetime', 'label' => 'DateConsentementRGPD', 'enabled' => 1, 'position' => 1330, 'notnull' => 0, 'visible' => 1),
        'notes_specifiques_particulier' => array('type' => 'text', 'label' => 'NotesSpecifiquesParticulier', 'enabled' => 1, 'position' => 1340, 'notnull' => 0, 'visible' => 1),

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
        
        // Par défaut, le particulier est actif
        if (!isset($this->status)) $this->status = 1;
        
        // Valeurs par défaut pour les champs booléens
        $this->consentement_rgpd = isset($this->consentement_rgpd) ? $this->consentement_rgpd : 0;
        $this->allocataire_caf_aah = isset($this->allocataire_caf_aah) ? $this->allocataire_caf_aah : 0;
        $this->affection_longue_duree_ald = isset($this->affection_longue_duree_ald) ? $this->affection_longue_duree_ald : 0;
        $this->beneficiaire_apa = isset($this->beneficiaire_apa) ? $this->beneficiaire_apa : 0;
        $this->logement_situe_zone_tendue = isset($this->logement_situe_zone_tendue) ? $this->logement_situe_zone_tendue : 0;
        $this->logement_conventionne_apl = isset($this->logement_conventionne_apl) ? $this->logement_conventionne_apl : 0;
        $this->eligible_prime_activite = isset($this->eligible_prime_activite) ? $this->eligible_prime_activite : 0;
        $this->utilise_transports_en_commun_regulierement = isset($this->utilise_transports_en_commun_regulierement) ? $this->utilise_transports_en_commun_regulierement : 0;
    }

    /**
     * Crée un particulier dans la base de données
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
     * Charge un particulier depuis la base de données par son ID
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
     * Charge un particulier depuis la base de données par l'ID du tiers associé
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
     * Met à jour un particulier dans la base de données
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
     * Supprime un particulier de la base de données
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
     * Calcule l'âge du particulier à partir de sa date de naissance
     * 
     * @return int Age en années, -1 si la date de naissance n'est pas définie
     */
    public function getAge()
    {
        if (empty($this->date_naissance)) {
            return -1;
        }
        
        try {
            $birthdate = new DateTime($this->date_naissance);
            $today = new DateTime();
            $age = $today->diff($birthdate);
            
            return $age->y;
        } catch (Exception $e) {
            return -1;
        }
    }

    /**
     * Vérifie si le particulier est majeur
     * 
     * @param int $majority_age Âge de majorité (18 par défaut)
     * @return boolean          true si majeur, false sinon
     */
        public function isMajor($majority_age = 18)
    {
        $age = $this->getAge();
        if ($age < 0) {
            return false;  // Date de naissance non définie
        }
        
        return $age >= $majority_age;
    }

    /**
     * Met à jour le consentement RGPD
     * 
     * @param User   $user        Utilisateur effectuant l'action
     * @param int    $consentement 1=consenti, 0=non consenti
     * @param string $date_consent Date du consentement (format SQL), si null utilise la date courante
     * @return int                 <0 si erreur, >0 si OK
     */
    public function updateGDPRConsent($user, $consentement = 1, $date_consent = null)
    {
        $this->consentement_rgpd = $consentement ? 1 : 0;
        $this->date_consentement_rgpd = !empty($date_consent) ? $date_consent : dol_now();
        
        return $this->update($user, 1); // notrigger = 1
    }
    
    /**
     * Retourne le nom complet formaté du particulier
     * 
     * @param int $format 0=Prénom Nom, 1=Nom Prénom, 2=Nom (Prénom)
     * @return string Nom complet formaté
     */
    public function getFullName($format = 0)
    {
        global $langs;
        
        // Récupérer les infos du tiers parent si nécessaire
        $nom = $this->nom_usage;
        $prenom = $this->prenoms_etat_civil;
        
        if (empty($nom) || empty($prenom)) {
            // Charger le tiers parent pour récupérer les informations
            require_once DOL_DOCUMENT_ROOT.'/custom/elaska/class/elaska_tiers.class.php';
            $tiers = new ElaskaTiers($this->db);
            if ($this->fk_elaska_tiers > 0 && $tiers->fetch($this->fk_elaska_tiers) > 0) {
                if (empty($nom)) {
                    $nom = $tiers->nom;
                }
                // Le tiers n'a généralement pas la notion de prénom directement
            }
        }
        
        // Si toujours vide, utiliser des valeurs par défaut
        if (empty($nom)) $nom = $langs->trans('UnknownName');
        if (empty($prenom)) $prenom = '';
        
        // Formater selon le format demandé
        switch ($format) {
            case 1: // Nom Prénom
                return trim($nom.' '.$prenom);
            case 2: // Nom (Prénom)
                return trim($nom.(!empty($prenom) ? ' ('.$prenom.')' : ''));
            case 0: // Prénom Nom (défaut)
            default:
                return trim($prenom.' '.$nom);
        }
    }

    /**
     * Méthode factorisée pour récupérer les options depuis un dictionnaire
     *
     * @param Translate $langs                 Objet de traduction
     * @param string    $dictionary_table_suffix_short Suffixe court du nom de la table dictionnaire (ex: 'genre' pour llx_c_elaska_part_genre)
     * @param bool      $usekeys               True pour retourner tableau associatif code=>label
     * @param bool      $show_empty            True pour ajouter une option vide
     * @return array                           Tableau d'options
     */
    private static function getOptionsFromDictionary($langs, $dictionary_table_suffix_short, $usekeys = true, $show_empty = false)
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code, label FROM ".MAIN_DB_PREFIX."c_elaska_part_".$dictionary_table_suffix_short;
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
     * Récupère les options du dictionnaire des genres
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getGenreOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'genre', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des situations familiales
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSituationFamilialeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'situation_familiale', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des régimes matrimoniaux
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getRegimeMatrimonialOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'regime_matrimonial', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des professions
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getProfessionOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'profession', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts professionnels
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutProfessionnelOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_professionnel', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des secteurs d'activité professionnelle
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSecteurActiviteProOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'secteur_activite_pro', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des détails de contrat de travail
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getContratTravailDetailOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'contrat_detail', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des tranches de revenus annuels nets du foyer
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getRevenusAnnuelsNetFoyerOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'revenus_foyer', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des estimations de patrimoine net
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getPatrimoineNetEstimeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'patrimoine_net', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des niveaux d'endettement
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getNiveauEndettementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'niveau_endettement', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des groupes GIR
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getGroupeGirOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'gir', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des liens de parenté
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getLienParenteOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'lien_parente', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des situations de handicap
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSituationHandicapOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'situation_handicap', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des niveaux d'études
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getNiveauEtudeOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'niveau_etude', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des situations judiciaires
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getSituationJudiciaireOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'situation_judiciaire', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des horizons de projet
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getHorizonProjetOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'horizon_projet', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des types de logement
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeLogementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_logement', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des statuts d'occupation de logement
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getStatutOccupationLogementOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'statut_occupation_logement', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des types de chauffage
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeChauffageOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_chauffage', $usekeys, $show_empty);
    }

    /**
     * Récupère les options du dictionnaire des types de garde d'enfants
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @return array                Tableau d'options
     */
    public static function getTypeGardeEnfantsOptions($langs, $usekeys = true, $show_empty = false)
    {
        return self::getOptionsFromDictionary($langs, 'type_garde', $usekeys, $show_empty);
    }

    /**
     * Récupère les pays pour les nationalités et pays de naissance
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
     * Récupère les départements
     * Utilise directement le dictionnaire standard Dolibarr
     *
     * @param Translate $langs      Objet de traduction
     * @param bool      $usekeys    True pour retourner tableau associatif code=>label
     * @param bool      $show_empty True pour ajouter une option vide
     * @param string    $pays_code  Code du pays pour filtrer (FR par défaut)
     * @return array                Tableau d'options
     */
    public static function getDepartementOptions($langs, $usekeys = true, $show_empty = false, $pays_code = 'FR')
    {
        global $db;
        
        $options = array();
        if ($show_empty) $options[''] = $langs->trans("SelectAnOption");
        
        $sql = "SELECT code_departement as code, nom as label FROM ".MAIN_DB_PREFIX."c_departements";
        $sql.= " WHERE active = 1";
        if (!empty($pays_code)) {
            $sql.= " AND fk_region IN (SELECT rowid FROM ".MAIN_DB_PREFIX."c_regions WHERE fk_pays = ";
            $sql.= " (SELECT rowid FROM ".MAIN_DB_PREFIX."c_country WHERE code = '".$db->escape($pays_code)."')";
            $sql.= ")";
        }
        $sql.= " ORDER BY code_departement";
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                if ($usekeys) {
                    $options[$obj->code] = $obj->label;
                } else {
                    $obj_option = new stdClass();
                    $obj_option->code = $obj->code;
                    $obj_option->label = $obj->label;
                    $obj_option->label_translated = $obj->label; // Pas de traduction pour les noms de départements
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