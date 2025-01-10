<?php
namespace CreactiveWeb;

if (! defined('ABSPATH') ) {
    exit;
}

class MyAccountTabs {
    public static function init() {
        add_filter('woocommerce_account_menu_items', [ __CLASS__, 'ajouterMenuMonCompte' ]);
        add_action('init', [ __CLASS__, 'ajouterEndpoint' ]);

        // Récupérer les slugs depuis les options (si besoin)
        $options = get_option('creactive_settings');
        $tab_1_slug = $options['my_account_tab_1_slug'] ?? 'mes-tarifs';
        $tab_2_slug = $options['my_account_tab_2_slug'] ?? 'mes-demandes-de-devis';

        add_action("woocommerce_account_{$tab_1_slug}_endpoint", [ __CLASS__, 'contenuTarifs' ]);
        add_action("woocommerce_account_{$tab_2_slug}_endpoint", [ __CLASS__, 'contenuDevis' ]);
    }

    public static function ajouterMenuMonCompte($items) {
        $options = get_option('creactive_settings');
        $tab_1_slug  = $options['my_account_tab_1_slug']  ?? 'mes-tarifs';
        $tab_1_label = $options['my_account_tab_1_label'] ?? 'Mes Tarifs';
        $tab_2_slug  = $options['my_account_tab_2_slug']  ?? 'mes-demandes-de-devis';
        $tab_2_label = $options['my_account_tab_2_label'] ?? 'Demande de devis';

        $nouveaux = [
            $tab_1_slug => $tab_1_label,
            $tab_2_slug => $tab_2_label
        ];
        $items = array_slice($items, 0, 1, true) + $nouveaux + array_slice($items, 1, null, true);

        // Ex. suppr l’onglet “Téléchargements”
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

    public static function contenuTarifs() {
        $user_id = get_current_user_id();
        $tarif_url = get_field('tarifs_preferentiels', 'user_' . $user_id);

        if ($tarif_url) {
            echo '<h3>Mes Tarifs Préférentiels</h3>';
            echo '<p>Vous pouvez télécharger vos tarifs préférentiels ici :</p>';
            echo '<a class="button" href="' . esc_url($tarif_url) . '">Télécharger</a>';
        } else {
            echo '<p>Aucun tarif associé à votre compte.</p>';
        }
    }

    public static function contenuDevis() {
        $options = get_option('creactive_settings');
        $form_id = $options['my_account_tab_2_form_id'] ?? 0;

        echo '<h3>Mes Demandes de Devis</h3>';
        if ($form_id) {
            echo do_shortcode('[forminator_form id="' . intval($form_id) . '"]');
        } else {
            echo '<p>Formulaire non configuré.</p>';
        }
    }
}