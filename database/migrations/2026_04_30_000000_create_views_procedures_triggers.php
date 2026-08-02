<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // Drop existing views if any
        DB::statement('DROP VIEW IF EXISTS vue_vendeurs_actifs');
        DB::statement('DROP VIEW IF EXISTS vue_commandes_jour');

        // Create views (portable SQL where possible)
        $driver = DB::getDriverName();

        $view1 = <<<SQL
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
            SQL;
        } else {
            $view2 = <<<SQL
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
            WHERE DATE(c.date_commande) = DATE('now');
            SQL;
        }
        DB::statement($view1);
        DB::statement($view2);

        // Only create procedures/triggers on MySQL
        if ($driver === 'mysql') {
            $sql = <<<'SQL'
            -- PROCEDURES
            DROP PROCEDURE IF EXISTS calculer_note_vendeur;
            CREATE PROCEDURE calculer_note_vendeur(IN p_id_vendeur BIGINT)
            BEGIN
                UPDATE vendeurs v
                SET 
                    note_moyenne = (
                        SELECT COALESCE(AVG(note), 0)
                        FROM avis_evaluations
                        WHERE id_vendeur = p_id_vendeur AND statut_avis = 'publie'
                    ),
                    nombre_avis = (
                        SELECT COUNT(*)
                        FROM avis_evaluations
                        WHERE id_vendeur = p_id_vendeur AND statut_avis = 'publie'
                    )
                WHERE v.id_vendeur = p_id_vendeur;
            END;

            DROP PROCEDURE IF EXISTS maj_statistiques_vendeur;
            CREATE PROCEDURE maj_statistiques_vendeur(IN p_id_vendeur BIGINT)
            BEGIN
                UPDATE vendeurs v
                SET 
                    nombre_commandes_total = (
                        SELECT COUNT(*)
                        FROM commandes
                        WHERE id_vendeur = p_id_vendeur AND statut = 'livree'
                    ),
                    nombre_commandes_mois = (
                        SELECT COUNT(*)
                        FROM commandes
                        WHERE id_vendeur = p_id_vendeur 
                            AND statut = 'livree'
                            AND MONTH(date_commande) = MONTH(CURRENT_DATE())
                            AND YEAR(date_commande) = YEAR(CURRENT_DATE())
                    )
                WHERE v.id_vendeur = p_id_vendeur;
            END;

            -- TRIGGERS
            DROP TRIGGER IF EXISTS after_avis_insert;
            CREATE TRIGGER after_avis_insert
            AFTER INSERT ON avis_evaluations
            FOR EACH ROW
            BEGIN
                IF NEW.statut_avis = 'publie' THEN
                    CALL calculer_note_vendeur(NEW.id_vendeur);
                END IF;
            END;

            DROP TRIGGER IF EXISTS after_avis_update;
            CREATE TRIGGER after_avis_update
            AFTER UPDATE ON avis_evaluations
            FOR EACH ROW
            BEGIN
                IF NEW.statut_avis != OLD.statut_avis OR NEW.note != OLD.note THEN
                    CALL calculer_note_vendeur(NEW.id_vendeur);
                END IF;
            END;

            DROP TRIGGER IF EXISTS after_commande_update_stats;
            CREATE TRIGGER after_commande_update_stats
            AFTER UPDATE ON commandes
            FOR EACH ROW
            BEGIN
                IF NEW.statut = 'livree' AND OLD.statut != 'livree' THEN
                    CALL maj_statistiques_vendeur(NEW.id_vendeur);
                END IF;
            END;
            SQL;

            DB::unprepared($sql);
        }
    }

    public function down()
    {
        $driver = DB::getDriverName();

        DB::statement('DROP VIEW IF EXISTS vue_commandes_jour');
        DB::statement('DROP VIEW IF EXISTS vue_vendeurs_actifs');

        if ($driver === 'mysql') {
            $sql = <<<'SQL'
            DROP TRIGGER IF EXISTS after_commande_update_stats;
            DROP TRIGGER IF EXISTS after_avis_update;
            DROP TRIGGER IF EXISTS after_avis_insert;
            DROP PROCEDURE IF EXISTS maj_statistiques_vendeur;
            DROP PROCEDURE IF EXISTS calculer_note_vendeur;
            SQL;

            DB::unprepared($sql);
        }
    }
};
