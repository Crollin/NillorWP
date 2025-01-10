<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AdminSettings {

    public static function register() {
        add_action('admin_menu', [ __CLASS__, 'addMenuPage' ]);
        add_action('admin_init', [ __CLASS__, 'initSettings' ]);

        // Pour styliser la page (CSS) & color picker
        add_action('admin_enqueue_scripts', [ __CLASS__, 'adminScripts' ]);
    }

    public static function addMenuPage() {
        add_menu_page(
            'Creactive',
            'Creactive',
            'manage_options',
            'creactive-settings',
            [ __CLASS__, 'renderSettingsPage' ],
            'dashicons-admin-generic',
            81
        );
    }

    public static function initSettings() {
        // On enregistre l'option principale
        register_setting('creactive_settings_group', 'creactive_settings');

        // --- SECTION "FEATURE TOGGLES" --- //
        add_settings_section(
            'feature_toggles_section',
            'Activation / Désactivation des Fonctionnalités',
            function() {
                echo '<p>Cochez les fonctionnalités que vous souhaitez activer.</p>';
            },
            'creactive-settings'
        );

        // 1. SKU Search
        add_settings_field(
            'enable_feature_sku_search',
            'Recherche par SKU',
            [ __CLASS__, 'field_enable_feature_sku_search' ],
            'creactive-settings',
            'feature_toggles_section'
        );

        // 2. B2BKing PDF
        add_settings_field(
            'enable_feature_b2bking_pdf',
            'Personnalisation PDF B2BKing',
            [ __CLASS__, 'field_enable_feature_b2bking_pdf' ],
            'creactive-settings',
            'feature_toggles_section'
        );

        // 3. Variation Price
        add_settings_field(
            'enable_feature_variation_price',
            'Personnalisation du prix variable',
            [ __CLASS__, 'field_enable_feature_variation_price' ],
            'creactive-settings',
            'feature_toggles_section'
        );

        // 4. “Nous Consulter”
        add_settings_field(
            'enable_feature_nous_consulter',
            'Lien "Nous consulter" si prix 0.01',
            [ __CLASS__, 'field_enable_feature_nous_consulter' ],
            'creactive-settings',
            'feature_toggles_section'
        );

        // 5. Shortcodes connexion
        add_settings_field(
            'enable_feature_shortcodes',
            'Shortcodes (is_logged_in, etc.)',
            [ __CLASS__, 'field_enable_feature_shortcodes' ],
            'creactive-settings',
            'feature_toggles_section'
        );

        // 6. Dashboard widget
        add_settings_field(
            'enable_feature_dashboard_widget',
            'Widget Tableau de bord personnalisé',
            [ __CLASS__, 'field_enable_feature_dashboard_widget' ],
            'creactive-settings',
            'feature_toggles_section'
        );

        // 7. MyAccount Info
        add_settings_field(
            'enable_feature_myaccount_info',
            'Infos client sur "Mon compte"',
            [ __CLASS__, 'field_enable_feature_myaccount_info' ],
            'creactive-settings',
            'feature_toggles_section'
        );

        // 8. MyAccount Tabs
        add_settings_field(
            'enable_feature_myaccount_tabs',
            'Onglets personnalisés "Mon compte"',
            [ __CLASS__, 'field_enable_feature_myaccount_tabs' ],
            'creactive-settings',
            'feature_toggles_section'
        );

        // 9. Custom Admin user-edit
        add_settings_field(
            'enable_feature_custom_admin',
            'Personnalisation user-edit back-office',
            [ __CLASS__, 'field_enable_feature_custom_admin' ],
            'creactive-settings',
            'feature_toggles_section'
        );

        // --------------------------------------------------------------------------------
        // Exemples de champs de configuration supplémentaires pour la recherche SKU :
        // --------------------------------------------------------------------------------
        add_settings_section(
            'sku_search_section',
            'Paramètres de la Recherche SKU',
            null,
            'creactive-settings'
        );
        add_settings_field(
            'sku_search_compare_operator',
            'Opérateur de comparaison',
            [ __CLASS__, 'field_sku_search_compare_operator' ],
            'creactive-settings',
            'sku_search_section'
        );

        // etc. Si tu veux personnaliser d'autres fonctionnalités...
    }

    // --- Affichage de la page ---
    public static function renderSettingsPage() {
        ?>
        <div class="wrap creactive-admin-wrap">
            <h1>Creactive - Paramètres Personnalisés</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('creactive_settings_group');
                do_settings_sections('creactive-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // --- Callbacks pour les champs "feature toggles" ---
    public static function field_enable_feature_sku_search() {
        self::renderCheckbox('enable_feature_sku_search');
    }
    public static function field_enable_feature_b2bking_pdf() {
        self::renderCheckbox('enable_feature_b2bking_pdf');
    }
    public static function field_enable_feature_variation_price() {
        self::renderCheckbox('enable_feature_variation_price');
    }
    public static function field_enable_feature_nous_consulter() {
        self::renderCheckbox('enable_feature_nous_consulter');
    }
    public static function field_enable_feature_shortcodes() {
        self::renderCheckbox('enable_feature_shortcodes');
    }
    public static function field_enable_feature_dashboard_widget() {
        self::renderCheckbox('enable_feature_dashboard_widget');
    }
    public static function field_enable_feature_myaccount_info() {
        self::renderCheckbox('enable_feature_myaccount_info');
    }
    public static function field_enable_feature_myaccount_tabs() {
        self::renderCheckbox('enable_feature_myaccount_tabs');
    }
    public static function field_enable_feature_custom_admin() {
        self::renderCheckbox('enable_feature_custom_admin');
    }

    // Petite fonction helper pour éviter de répéter le code
    private static function renderCheckbox($option_name) {
        $options = get_option('creactive_settings');
        $checked = !empty($options[$option_name]) && $options[$option_name] == '1';
        ?>
        <label>
            <input type="checkbox" name="creactive_settings[<?php echo esc_attr($option_name); ?>]"
                   value="1" <?php checked($checked); ?>>
            Activer
        </label>
        <?php
    }

    // --- Exemples de champs config pour la Recherche SKU ---
    public static function field_sku_search_compare_operator() {
        $options = get_option('creactive_settings');
        $operator = $options['sku_search_compare_operator'] ?? 'LIKE';
        ?>
        <select name="creactive_settings[sku_search_compare_operator]">
            <option value="LIKE" <?php selected($operator, 'LIKE'); ?>>Contient (LIKE)</option>
            <option value="=" <?php selected($operator, '='); ?>>Exact (=)</option>
        </select>
        <?php
    }

    // --- Scripts & Styles ---
    public static function adminScripts($hook) {
        if ($hook !== 'toplevel_page_creactive-settings') {
            return;
        }
        // Exemple : color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        // Ton CSS perso
        wp_enqueue_style(
            'creactive_admin_css',
            CREACTIVEWEB_PLUGIN_URL . 'inc/admin-style.css',
            [],
            CREACTIVEWEB_VERSION
        );
    }

}