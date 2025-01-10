<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MyAccountTabs {

    public static function init() {
        add_filter('woocommerce_account_menu_items', [ __CLASS__, 'ajouterMenuMonCompte' ]);
        add_action('init', [ __CLASS__, 'ajouterEndpoint' ]);
        add_action('woocommerce_account_{mes_tarifs}_endpoint', [ __CLASS__, 'afficherContenuTarifs' ]);
        add_action('woocommerce_account_{mes_demandes}_endpoint', [ __CLASS__, 'afficherContenuDevis' ]);
    }

    // Filtre principal pour injecter nos items
    public static function ajouterMenuMonCompte($items) {
        $options = get_option('creactive_settings');

        $tab_1_slug  = $options['my_account_tab_1_slug']  ?? 'mes-tarifs';
        $tab_1_label = $options['my_account_tab_1_label'] ?? 'Mes Tarifs';
        $tab_2_slug  = $options['my_account_tab_2_slug']  ?? 'mes-demandes-de-devis';
        $tab_2_label = $options['my_account_tab_2_label'] ?? 'Demande de devis';

        $nouveaux_items = [
            $tab_1_slug => $tab_1_label,
            $tab_2_slug => $tab_2_label
        ];

        // On insère nos onglets juste après le premier item ("dashboard")
        $items = array_slice($items, 0, 1, true) + $nouveaux_items + array_slice($items, 1, null, true);

        // Supprimer l'onglet Téléchargements par exemple
        if (isset($items['downloads'])) {
            unset($items['downloads']);
        }

        return $items;
    }

    public static function ajouterEndpoint() {
        $options = get_option('creactive_settings');
        $tab_1_slug = $options['my_account_tab_1_slug'] ?? 'mes-tarifs';
        $tab_2_slug = $options['my_account_tab_2_slug'] ?? 'mes-demandes-de-devis';

        add_rewrite_endpoint($tab_1_slug, EP_ROOT | EP_PAGES);
        add_rewrite_endpoint($tab_2_slug, EP_ROOT | EP_PAGES);
    }

    public static function afficherContenuTarifs() {
        $user_id = get_current_user_id();
        $tarif_url = get_field('tarifs_preferentiels', 'user_' . $user_id);

        if ($tarif_url) {
            echo '<h3>Mes Tarifs Préférentiels</h3>';
            echo '<p>Téléchargez votre fichier de tarifs préférentiels en cliquant sur le lien ci-dessous :</p>';
            echo '<a href="' . esc_url($tarif_url) . '" class="button">Télécharger mes tarifs</a>';
        } else {
            echo '<p>Aucun tarif n\'est associé à votre compte pour le moment.</p>';
        }
    }

    public static function afficherContenuDevis() {
        if (is_user_logged_in()) {
            $options = get_option('creactive_settings');
            $form_id = $options['my_account_tab_2_form_id'] ?? '8635';
            echo '<h3>Mes Demandes de Devis</h3>';
            echo do_shortcode('[forminator_form id="' . intval($form_id) . '"]');
        }
    }
}
