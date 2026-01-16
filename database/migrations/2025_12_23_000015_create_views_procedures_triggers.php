<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateViewsProceduresTriggers extends Migration
{
    public function up()
    {
        // Create views (portable SQL where possible)
        $driver = DB::getDriverName();

        $view1 = <<<SQL
        CREATE VIEW IF NOT EXISTS vue_vendeurs_actifs AS
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
        LEFT JOIN plats p ON v.id_vendeur = p.id_vendeur AND p.disponible = 1
        WHERE v.actif = 1
            AND v.statut_verification = 'verifie'
            AND u.statut_compte = 'actif'
        GROUP BY
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
            u.telephone;
        SQL;

        // `vue_commandes_jour` uses different date functions per driver
        if ($driver === 'mysql') {
            $view2 = <<<SQL
            CREATE VIEW IF NOT EXISTS vue_commandes_jour AS
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
            SQL;
        } else {
            $view2 = <<<SQL
            CREATE VIEW IF NOT EXISTS vue_commandes_jour AS
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
            WHERE DATE(c.date_commande) = DATE('now');
            SQL;
        }
        DB::statement($view1);
        DB::statement($view2);

        // Only create procedures/triggers on MySQL
        if ($driver === 'mysql') {
            $sql = <<<'SQL'
            -- PROCEDURES
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
            END;

            CREATE PROCEDURE generer_numero_commande(OUT p_numero VARCHAR(20))
            BEGIN
                SET p_numero = CONCAT(
                    'CMD',
                    DATE_FORMAT(NOW(), '%Y%m%d'),
                    LPAD(FLOOR(RAND() * 10000), 4, '0')
                );
            END;

            -- TRIGGERS
            CREATE TRIGGER after_commande_insert
            AFTER INSERT ON commandes
            FOR EACH ROW
            BEGIN
                UPDATE vendeurs
                SET 
                    nombre_commandes_total = nombre_commandes_total + 1,
                    nombre_commandes_mois = nombre_commandes_mois + 1
                WHERE id_vendeur = NEW.id_vendeur;
            END;

            CREATE TRIGGER after_ligne_commande_insert
            AFTER INSERT ON lignes_commande
            FOR EACH ROW
            BEGIN
                UPDATE plats
                SET nombre_commandes = nombre_commandes + NEW.quantite
                WHERE id_plat = NEW.id_plat;
            END;
            SQL;

            DB::unprepared($sql);
        }
    }

    public function down()
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $drop = <<<'SQL'
            DROP TRIGGER IF EXISTS after_ligne_commande_insert;
            DROP TRIGGER IF EXISTS after_commande_insert;
            DROP PROCEDURE IF EXISTS calculer_note_vendeur;
            DROP PROCEDURE IF EXISTS generer_numero_commande;
            DROP VIEW IF EXISTS vue_commandes_jour;
            DROP VIEW IF EXISTS vue_vendeurs_actifs;
            SQL;

            DB::unprepared($drop);
            return;
        }

        // For other drivers (sqlite, pgsql) drop known objects safely one-by-one
        try {
            DB::statement('DROP TRIGGER IF EXISTS after_ligne_commande_insert');
        } catch (\Exception $e) {
            // ignore
        }

        try {
            DB::statement('DROP TRIGGER IF EXISTS after_commande_insert');
        } catch (\Exception $e) {
            // ignore
        }

        try {
            DB::statement('DROP VIEW IF EXISTS vue_commandes_jour');
        } catch (\Exception $e) {
            // ignore
        }

        try {
            DB::statement('DROP VIEW IF EXISTS vue_vendeurs_actifs');
        } catch (\Exception $e) {
            // ignore
        }
    }
}
