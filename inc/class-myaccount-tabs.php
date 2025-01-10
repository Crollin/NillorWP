<?php
namespace CreactiveWeb;

if (!defined('ABSPATH')) exit;

class MyAccountTabs {
    public static function init() {
        add_filter('woocommerce_account_menu_items', [ __CLASS__, 'ajouterMenuMonCompte' ]);
        add_action('init', [ __CLASS__, 'ajouterEndpoints' ]);

        // On attache l’affichage du contenu à l’endpoint
        $options = get_option('creactive_settings');
        $tab1 = $options['my_account_tab_1_slug'] ?? 'mes-tarifs';
        $tab2 = $options['my_account_tab_2_slug'] ?? 'mes-demandes-de-devis';

        add_action("woocommerce_account_{$tab1}_endpoint", [ __CLASS__, 'contenuTarifs' ]);
        add_action("woocommerce_account_{$tab2}_endpoint", [ __CLASS__, 'contenuDevis' ]);
    }

    public static function ajouterMenuMonCompte($items) {
        $options = get_option('creactive_settings');
        $tab1_slug  = $options['my_account_tab_1_slug']  ?? 'mes-tarifs';
        $tab1_label = $options['my_account_tab_1_label'] ?? 'Mes Tarifs';
        $tab2_slug  = $options['my_account_tab_2_slug']  ?? 'mes-demandes-de-devis';
        $tab2_label = $options['my_account_tab_2_label'] ?? 'Demande de devis';

        $nouveaux = [
            $tab1_slug => $tab1_label,
            $tab2_slug => $tab2_label
        ];

        // Insérer juste après "Dashboard"
        $items = array_slice($items, 0, 1, true)
               + $nouveaux
               + array_slice($items, 1, null, true);

        // On peut enlever "Téléchargements" par exemple
        if (isset($items['downloads'])) unset($items['downloads']);

        return $items;
    }

    public static function ajouterEndpoints() {
        $options = get_option('creactive_settings');
        $tab1_slug = $options['my_account_tab_1_slug'] ?? 'mes-tarifs';
        $tab2_slug = $options['my_account_tab_2_slug'] ?? 'mes-demandes-de-devis';

        add_rewrite_endpoint($tab1_slug, EP_ROOT | EP_PAGES);
        add_rewrite_endpoint($tab2_slug, EP_ROOT | EP_PAGES);
    }

    public static function contenuTarifs() {
        $user_id = get_current_user_id();
        // ex. ACF get_field
        $tarif_url = get_field('tarifs_preferentiels', 'user_'.$user_id);

        echo '<h3>Mes Tarifs Préférentiels</h3>';
        if ($tarif_url) {
            echo '<p>Vous pouvez télécharger vos tarifs ici :</p>';
            echo '<a class="button" href="' . esc_url($tarif_url) . '">Télécharger</a>';
        } else {
            echo '<p>Aucun tarif n’est disponible.</p>';
        }
    }

    public static function contenuDevis() {
        $options = get_option('creactive_settings');
        $form_id = $options['my_account_tab_2_form_id'] ?? '';

        echo '<h3>Mes Demandes de Devis</h3>';
        if ($form_id) {
            echo do_shortcode('[forminator_form id="' . intval($form_id) . '"]');
        } else {
            echo '<p>Aucun formulaire n’est configuré.</p>';
        }
    }
}