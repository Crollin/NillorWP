<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Init {

    public function run() {
        // 1. Enregistrer les réglages (page d'options)
        AdminSettings::register();

        // 2. Mise en place du filtre de recherche par SKU
        SkuSearch::init();

        // 3. Personnalisation PDF B2BKing
        B2bkingPdfCustom::init();

        // 4. Informations client sur la page de compte
        MyAccountInfo::init();

        // 5. Onglets personnalisés sur "Mon compte"
        MyAccountTabs::init();

        // 6. Shortcodes de connexion
        Shortcodes::init();

        // 7. Variation Table / formatage du prix
        VariationPrice::init();

        // 8. Affichage du "Nous consulter"
        NousConsulter::init();

        // 9. Personnalisation de la page user-edit (back-office)
        CustomAdminUserEdit::init();

        // 10. Widget & panneau de dashboard
        DashboardWidget::init();

        // 11. Renommer les fichiers uploadés (accents)
        Renamer::init();
    }
}
