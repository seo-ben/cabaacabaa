-- ============================================================================
-- BASE DE DONNÉES - PLATEFORME DE MISE EN RELATION VENDEURS/CLIENTS
-- Système de commande de nourriture locale avec géolocalisation
-- ============================================================================

-- Table 1: USERS
-- Gère tous les comptes (clients, vendeurs, admins)
CREATE TABLE users (
    id_user BIGINT PRIMARY KEY AUTO_INCREMENT,
    nom_complet VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('client', 'vendeur', 'admin') NOT NULL DEFAULT 'client',
    statut_compte ENUM('actif', 'suspendu', 'en_attente', 'supprime') NOT NULL DEFAULT 'actif',
    photo_profil VARCHAR(255),
    langue_preferee VARCHAR(5) DEFAULT 'fr',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_derniere_connexion TIMESTAMP NULL,
    derniere_ip VARCHAR(45),
    INDEX idx_telephone (telephone),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_statut (statut_compte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 2: ZONES_GEOGRAPHIQUES
-- Structure la recherche et le ciblage géographique
CREATE TABLE zones_geographiques (
    id_zone INT PRIMARY KEY AUTO_INCREMENT,
    nom_zone VARCHAR(100) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    quartier VARCHAR(100),
    latitude_centre DECIMAL(10, 8) NOT NULL,
    longitude_centre DECIMAL(11, 8) NOT NULL,
    rayon_km DECIMAL(5, 2) DEFAULT 2.0,
    population_estimee INT,
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ville (ville),
    INDEX idx_coord (latitude_centre, longitude_centre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 3: VENDEURS
-- Profil commercial des vendeurs de nourriture
CREATE TABLE vendeurs (
    id_vendeur BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_user BIGINT UNIQUE NOT NULL,
    id_zone INT,
    nom_commercial VARCHAR(150) NOT NULL,
    description TEXT,
    type_vendeur ENUM('restaurant', 'cantine', 'fast_food', 'vendeur_independant', 'patisserie', 'autre') NOT NULL,
    adresse_complete TEXT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    
    -- Horaires d'ouverture (format JSON ou texte structuré)
    horaires_ouverture JSON,
    
    -- Contact
    telephone_commercial VARCHAR(20),
    
    -- Vérification et qualité
    statut_verification ENUM('non_verifie', 'en_cours', 'verifie', 'rejete') DEFAULT 'non_verifie',
    date_verification TIMESTAMP NULL,
    note_moyenne DECIMAL(3, 2) DEFAULT 0.00,
    nombre_avis INT DEFAULT 0,
    
    -- Statistiques
    nombre_commandes_total INT DEFAULT 0,
    nombre_commandes_mois INT DEFAULT 0,
    
    -- Images
    image_principale VARCHAR(255),
    images_galerie JSON,
    
    -- Métadonnées
    actif BOOLEAN DEFAULT TRUE,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_derniere_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_zone) REFERENCES zones_geographiques(id_zone) ON DELETE SET NULL,
    INDEX idx_zone (id_zone),
    INDEX idx_localisation (latitude, longitude),
    INDEX idx_type (type_vendeur),
    INDEX idx_verification (statut_verification),
    INDEX idx_note (note_moyenne),
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 4: CATEGORIES_PLATS
-- Classification des plats pour faciliter la recherche
CREATE TABLE categories_plats (
    id_categorie INT PRIMARY KEY AUTO_INCREMENT,
    nom_categorie VARCHAR(50) NOT NULL,
    description VARCHAR(255),
    icone VARCHAR(100),
    ordre_affichage INT DEFAULT 0,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_ordre (ordre_affichage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 5: PLATS
-- Menu et offres des vendeurs
CREATE TABLE plats (
    id_plat BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_vendeur BIGINT NOT NULL,
    id_categorie INT,
    nom_plat VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    devise VARCHAR(3) DEFAULT 'XOF',
    
    -- Disponibilité
    disponible BOOLEAN DEFAULT TRUE,
    stock_limite BOOLEAN DEFAULT FALSE,
    quantite_disponible INT,
    
    -- Préparation
    temps_preparation_min INT DEFAULT 15,
    
    -- Médias
    image_principale VARCHAR(255),
    images_supplementaires JSON,
    
    -- Popularité
    nombre_commandes INT DEFAULT 0,
    nombre_vues INT DEFAULT 0,
    
    -- Promotions
    en_promotion BOOLEAN DEFAULT FALSE,
    prix_promotion DECIMAL(10, 2),
    date_debut_promotion TIMESTAMP NULL,
    date_fin_promotion TIMESTAMP NULL,
    
    -- Métadonnées
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_vendeur) REFERENCES vendeurs(id_vendeur) ON DELETE CASCADE,
    FOREIGN KEY (id_categorie) REFERENCES categories_plats(id_categorie) ON DELETE SET NULL,
    INDEX idx_vendeur (id_vendeur),
    INDEX idx_categorie (id_categorie),
    INDEX idx_disponible (disponible),
    INDEX idx_popularite (nombre_commandes),
    INDEX idx_promotion (en_promotion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 6: COMMANDES
-- Transactions entre clients et vendeurs
CREATE TABLE commandes (
    id_commande BIGINT PRIMARY KEY AUTO_INCREMENT,
    numero_commande VARCHAR(20) UNIQUE NOT NULL,
    
    -- Acteurs
    id_client BIGINT NOT NULL,
    id_vendeur BIGINT NOT NULL,
    
    -- Statut et workflow
    statut ENUM(
        'en_attente',
        'confirmee',
        'en_preparation',
        'prete',
        'recuperee',
        'livree',
        'annulee_client',
        'annulee_vendeur',
        'litige'
    ) NOT NULL DEFAULT 'en_attente',
    
    -- Type et modalités
    type_recuperation ENUM('emporter', 'sur_place', 'livraison') NOT NULL DEFAULT 'emporter',
    mode_paiement_prevu ENUM('espece', 'qr_code', 'mobile_money', 'carte') NOT NULL DEFAULT 'espece',
    paiement_effectue BOOLEAN DEFAULT FALSE,
    
    -- Montants
    montant_plats DECIMAL(10, 2) NOT NULL,
    frais_service DECIMAL(10, 2) DEFAULT 0.00,
    montant_total DECIMAL(10, 2) NOT NULL,
    
    -- Timing
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    heure_recuperation_souhaitee TIMESTAMP,
    heure_preparation_debut TIMESTAMP NULL,
    heure_prete TIMESTAMP NULL,
    heure_recuperation_effective TIMESTAMP NULL,
    
    -- Informations complémentaires
    instructions_speciales TEXT,
    
    -- Métadonnées
    date_annulation TIMESTAMP NULL,
    raison_annulation TEXT,
    
    FOREIGN KEY (id_client) REFERENCES users(id_user) ON DELETE RESTRICT,
    FOREIGN KEY (id_vendeur) REFERENCES vendeurs(id_vendeur) ON DELETE RESTRICT,
    INDEX idx_client (id_client),
    INDEX idx_vendeur (id_vendeur),
    INDEX idx_statut (statut),
    INDEX idx_date (date_commande),
    INDEX idx_numero (numero_commande)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 7: LIGNES_COMMANDE
-- Détail des plats commandés
CREATE TABLE lignes_commande (
    id_ligne BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_commande BIGINT NOT NULL,
    id_plat BIGINT NOT NULL,
    nom_plat_snapshot VARCHAR(150) NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    sous_total DECIMAL(10, 2) NOT NULL,
    notes TEXT,
    
    FOREIGN KEY (id_commande) REFERENCES commandes(id_commande) ON DELETE CASCADE,
    FOREIGN KEY (id_plat) REFERENCES plats(id_plat) ON DELETE RESTRICT,
    INDEX idx_commande (id_commande),
    INDEX idx_plat (id_plat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 8: AVIS_EVALUATIONS
-- Système de notation et commentaires
CREATE TABLE avis_evaluations (
    id_avis BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_client BIGINT NOT NULL,
    id_vendeur BIGINT NOT NULL,
    id_commande BIGINT,
    
    -- Notation
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    
    -- Critères détaillés (optionnel)
    note_qualite INT CHECK (note_qualite BETWEEN 1 AND 5),
    note_rapidite INT CHECK (note_rapidite BETWEEN 1 AND 5),
    note_rapport_qualite_prix INT CHECK (note_rapport_qualite_prix BETWEEN 1 AND 5),
    
    -- Modération
    statut_avis ENUM('visible', 'masque', 'en_attente_moderation', 'signale') DEFAULT 'visible',
    date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP NULL,
    
    -- Réponse du vendeur
    reponse_vendeur TEXT,
    date_reponse TIMESTAMP NULL,
    
    FOREIGN KEY (id_client) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_vendeur) REFERENCES vendeurs(id_vendeur) ON DELETE CASCADE,
    FOREIGN KEY (id_commande) REFERENCES commandes(id_commande) ON DELETE SET NULL,
    UNIQUE KEY unique_avis_par_client_vendeur (id_client, id_vendeur),
    INDEX idx_vendeur (id_vendeur),
    INDEX idx_note (note),
    INDEX idx_statut (statut_avis)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 9: ABONNEMENTS_TARIFICATION
-- Gestion commerciale plateforme/vendeur
CREATE TABLE abonnements_tarification (
    id_abonnement BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_vendeur BIGINT NOT NULL,
    
    -- Type de tarification
    type_tarification ENUM('pourcentage_commande', 'forfait_journalier', 'forfait_mensuel', 'gratuit') NOT NULL,
    valeur_tarif DECIMAL(10, 2),
    pourcentage DECIMAL(5, 2),
    
    -- Période
    date_debut TIMESTAMP NOT NULL,
    date_fin TIMESTAMP,
    duree_jours INT,
    
    -- Statut
    statut ENUM('actif', 'expire', 'suspendu', 'annule') NOT NULL DEFAULT 'actif',
    periode_essai BOOLEAN DEFAULT FALSE,
    
    -- Facturation
    montant_a_payer DECIMAL(10, 2) DEFAULT 0.00,
    montant_paye DECIMAL(10, 2) DEFAULT 0.00,
    date_dernier_paiement TIMESTAMP NULL,
    
    -- Métadonnées
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    
    FOREIGN KEY (id_vendeur) REFERENCES vendeurs(id_vendeur) ON DELETE CASCADE,
    INDEX idx_vendeur (id_vendeur),
    INDEX idx_statut (statut),
    INDEX idx_dates (date_debut, date_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 10: MISE_EN_AVANT
-- Gestion de la visibilité algorithmique
CREATE TABLE mise_en_avant (
    id_mise_en_avant BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_vendeur BIGINT NOT NULL,
    
    -- Type de mise en avant
    type_promotion ENUM('nouveau_vendeur', 'performance', 'sponsorise', 'promotion_speciale') NOT NULL,
    priorite INT DEFAULT 1,
    
    -- Période
    date_debut TIMESTAMP NOT NULL,
    date_fin TIMESTAMP NOT NULL,
    
    -- Ciblage
    id_zone INT,
    zones_ciblees JSON,
    
    -- Statut
    actif BOOLEAN DEFAULT TRUE,
    
    -- Métadonnées
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description VARCHAR(255),
    
    FOREIGN KEY (id_vendeur) REFERENCES vendeurs(id_vendeur) ON DELETE CASCADE,
    FOREIGN KEY (id_zone) REFERENCES zones_geographiques(id_zone) ON DELETE SET NULL,
    INDEX idx_vendeur (id_vendeur),
    INDEX idx_dates (date_debut, date_fin),
    INDEX idx_actif (actif),
    INDEX idx_priorite (priorite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 11: FAVORIS_CLIENTS
-- Liste des vendeurs favoris des clients
CREATE TABLE favoris_clients (
    id_favori BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_client BIGINT NOT NULL,
    id_vendeur BIGINT NOT NULL,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_client) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_vendeur) REFERENCES vendeurs(id_vendeur) ON DELETE CASCADE,
    UNIQUE KEY unique_favori (id_client, id_vendeur),
    INDEX idx_client (id_client)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 12: NOTIFICATIONS
-- Système de notifications push/email/SMS
CREATE TABLE notifications (
    id_notification BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_user BIGINT NOT NULL,
    type_notification ENUM('commande', 'promotion', 'avis', 'system', 'paiement') NOT NULL,
    titre VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    
    -- Références
    id_commande BIGINT,
    id_vendeur BIGINT,
    
    -- Statut
    lue BOOLEAN DEFAULT FALSE,
    date_lecture TIMESTAMP NULL,
    
    -- Métadonnées
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_commande) REFERENCES commandes(id_commande) ON DELETE SET NULL,
    FOREIGN KEY (id_vendeur) REFERENCES vendeurs(id_vendeur) ON DELETE SET NULL,
    INDEX idx_user (id_user),
    INDEX idx_lue (lue),
    INDEX idx_date (date_creation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 13: TRANSACTIONS_FINANCIERES
-- Suivi des flux financiers (pour comptabilité interne)
CREATE TABLE transactions_financieres (
    id_transaction BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_commande BIGINT,
    id_vendeur BIGINT NOT NULL,
    id_abonnement BIGINT,
    
    type_transaction ENUM('commission_commande', 'abonnement', 'remboursement', 'ajustement') NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    devise VARCHAR(3) DEFAULT 'XOF',
    
    statut ENUM('en_attente', 'complete', 'echec', 'annule') DEFAULT 'en_attente',
    
    date_transaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reference_paiement VARCHAR(100),
    notes TEXT,
    
    FOREIGN KEY (id_commande) REFERENCES commandes(id_commande) ON DELETE SET NULL,
    FOREIGN KEY (id_vendeur) REFERENCES vendeurs(id_vendeur) ON DELETE RESTRICT,
    FOREIGN KEY (id_abonnement) REFERENCES abonnements_tarification(id_abonnement) ON DELETE SET NULL,
    INDEX idx_vendeur (id_vendeur),
    INDEX idx_date (date_transaction),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 14: LOGS_ACTIVITE
-- Journalisation pour audit et sécurité
CREATE TABLE logs_activite (
    id_log BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_user BIGINT,
    type_action VARCHAR(50) NOT NULL,
    table_cible VARCHAR(50),
    id_enregistrement BIGINT,
    
    details_action JSON,
    adresse_ip VARCHAR(45),
    user_agent TEXT,
    
    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user (id_user),
    INDEX idx_date (date_action),
    INDEX idx_type (type_action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- VUES UTILES
-- ============================================================================

-- Vue: Vendeurs actifs avec leurs statistiques
CREATE VIEW vue_vendeurs_actifs AS
SELECT 
    v.id_vendeur,
    v.nom_commercial,
    v.type_vendeur,
    v.latitude,
    v.longitude,
    v.note_moyenne,
    v.nombre_avis,
    v.nombre_commandes_total,
    z.nom_zone,
    z.ville,
    u.telephone,
    COUNT(DISTINCT p.id_plat) as nombre_plats_disponibles
FROM vendeurs v
JOIN users u ON v.id_user = u.id_user
LEFT JOIN zones_geographiques z ON v.id_zone = z.id_zone
LEFT JOIN plats p ON v.id_vendeur = p.id_vendeur AND p.disponible = TRUE
WHERE v.actif = TRUE 
    AND v.statut_verification = 'verifie'
    AND u.statut_compte = 'actif'
GROUP BY v.id_vendeur;

-- Vue: Commandes du jour
CREATE VIEW vue_commandes_jour AS
SELECT 
    c.id_commande,
    c.numero_commande,
    c.statut,
    v.nom_commercial as vendeur,
    u.nom_complet as client,
    c.montant_total,
    c.date_commande,
    c.type_recuperation
FROM commandes c
JOIN vendeurs v ON c.id_vendeur = v.id_vendeur
JOIN users u ON c.id_client = u.id_user
WHERE DATE(c.date_commande) = CURDATE();

-- ============================================================================
-- PROCÉDURES STOCKÉES ESSENTIELLES
-- ============================================================================

-- Procédure: Calculer la note moyenne d'un vendeur
DELIMITER //
CREATE PROCEDURE calculer_note_vendeur(IN p_id_vendeur BIGINT)
BEGIN
    UPDATE vendeurs v
    SET 
        note_moyenne = (
            SELECT COALESCE(AVG(note), 0)
            FROM avis_evaluations
            WHERE id_vendeur = p_id_vendeur 
                AND statut_avis = 'visible'
        ),
        nombre_avis = (
            SELECT COUNT(*)
            FROM avis_evaluations
            WHERE id_vendeur = p_id_vendeur 
                AND statut_avis = 'visible'
        )
    WHERE id_vendeur = p_id_vendeur;
END //
DELIMITER ;

-- Procédure: Générer un numéro de commande unique
DELIMITER //
CREATE PROCEDURE generer_numero_commande(OUT p_numero VARCHAR(20))
BEGIN
    SET p_numero = CONCAT(
        'CMD',
        DATE_FORMAT(NOW(), '%Y%m%d'),
        LPAD(FLOOR(RAND() * 10000), 4, '0')
    );
END //
DELIMITER ;

-- ============================================================================
-- TRIGGERS
-- ============================================================================

-- Trigger: Mettre à jour les statistiques du vendeur après une commande
DELIMITER //
CREATE TRIGGER after_commande_insert
AFTER INSERT ON commandes
FOR EACH ROW
BEGIN
    UPDATE vendeurs
    SET 
        nombre_commandes_total = nombre_commandes_total + 1,
        nombre_commandes_mois = nombre_commandes_mois + 1
    WHERE id_vendeur = NEW.id_vendeur;
END //
DELIMITER ;

-- Trigger: Mettre à jour la popularité d'un plat
DELIMITER //
CREATE TRIGGER after_ligne_commande_insert
AFTER INSERT ON lignes_commande
FOR EACH ROW
BEGIN
    UPDATE plats
    SET nombre_commandes = nombre_commandes + NEW.quantite
    WHERE id_plat = NEW.id_plat;
END //
DELIMITER ;

-- ============================================================================
-- INDEX COMPOSITES POUR OPTIMISATION DES REQUÊTES FRÉQUENTES
-- ============================================================================

-- Recherche de vendeurs par zone et type
CREATE INDEX idx_vendeurs_zone_type ON vendeurs(id_zone, type_vendeur, actif);

-- Recherche de commandes par client et statut
CREATE INDEX idx_commandes_client_statut ON commandes(id_client, statut, date_commande);

-- Recherche de plats disponibles par vendeur
CREATE INDEX idx_plats_vendeur_dispo ON plats(id_vendeur, disponible, nombre_commandes);

-- ============================================================================
-- DONNÉES D'INITIALISATION
-- ============================================================================

-- Catégories de plats de base
INSERT INTO categories_plats (nom_categorie, description, ordre_affichage) VALUES
('Plats principaux', 'Repas complets et plats du jour', 1),
('Fast Food', 'Burgers, sandwichs, pizzas', 2),
('Petit-déjeuner', 'Viennoiseries, café, omelettes', 3),
('Desserts', 'Pâtisseries, glaces, fruits', 4),
('Boissons', 'Boissons chaudes et fraîches', 5),
('Snacks', 'Collations et en-cas', 6);

-- Zones géographiques exemples (à adapter selon votre ville)
INSERT INTO zones_geographiques (nom_zone, ville, latitude_centre, longitude_centre, rayon_km) VALUES
('Centre-ville', 'Lomé', 6.1256, 1.2251, 2.5),
('Zone universitaire', 'Lomé', 6.1683, 1.2064, 1.5),
('Zone industrielle', 'Lomé', 6.1425, 1.2485, 3.0);

-- ============================================================================
-- COMMENTAIRES ET DOCUMENTATION
-- ============================================================================

/*
NOTES IMPORTANTES:

1. SÉCURITÉ:
   - Les mots de passe doivent être hashés avec bcrypt ou argon2
   - Utiliser des requêtes préparées (prepared statements) pour éviter SQL injection
   - Implémenter rate limiting sur les API

2. PERFORMANCE:
   - Index créés sur toutes les colonnes fréquemment recherchées
   - Utiliser la pagination pour les grandes listes
   - Cache Redis recommandé pour les requêtes géographiques fréquentes

3. SCALABILITÉ:
   - Architecture prête pour sharding par zone géographique
   - Les JSON fields permettent flexibilité sans modification de schéma
   - Logs séparés peuvent être archivés périodiquement

4. RGPD / CONFIDENTIALITÉ:
   - Soft delete recommandé (flag supprime) plutôt que suppression physique
   - Anonymisation possible des données après X mois
   - Consentement utilisateur à tracker dans une table dédiée si nécessaire

5. ÉVOLUTION FUTURE:
   - Livraison: ajouter table coursiers/livreurs
   - Chat: ajouter table messages vendeur/client
   - Programme fidélité: ajouter table points/récompenses
   - Multi-devises: extension colonne devise + table taux_change
*/