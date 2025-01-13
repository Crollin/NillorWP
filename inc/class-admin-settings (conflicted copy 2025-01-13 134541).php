<?php
namespace CreactiveWeb;

if (!defined('ABSPATH')) exit;

class AdminSettings {
    
    public static function register() {
        add_action('admin_menu', [ __CLASS__, 'addMenuPage' ]);
        add_action('admin_init', [ __CLASS__, 'initSettings' ]);
        add_action('admin_enqueue_scripts', [ __CLASS__, 'adminScripts' ]);
    }

    /**
     * Ajoute la page "Creactive" au menu d’admin
     */
    public static function addMenuPage() {
        // On peut renommer le menu "Creactive" en quelque chose de plus explicite.
        add_menu_page(
            'Creactive - Paramètres',       // Titre de la page
            'Creactive',                    // Label du menu
            'manage_options',               // Capability
            'creactive-settings',           // Slug
            [ __CLASS__, 'renderSettingsPage' ],
            'dashicons-admin-generic',      // Icône
            80
        );
    }

    public static function initSettings() {
        register_setting('creactive_settings_group', 'creactive_settings');

        // --- SECTION 1 : Feature Toggles ---
        add_settings_section(
            'feature_toggles_section',
            'Activation / Désactivation des Fonctionnalités',
            function() {
                echo '<p>Cochez les fonctionnalités que vous souhaitez activer.</p>';
            },
            'creactive-settings'
        );

        self::addFeatureField('enable_feature_sku_search',         'Recherche par SKU');
        self::addFeatureField('enable_feature_b2bking_pdf',        'Personnalisation PDF B2BKing');
        self::addFeatureField('enable_feature_variation_price',    'Personnalisation du prix variable');
        self::addFeatureField('enable_feature_nous_consulter',     'Lien "Nous consulter" si prix 0.01');
        self::addFeatureField('enable_feature_shortcodes',         'Shortcodes (is_logged_in, etc.)');
        self::addFeatureField('enable_feature_dashboard_widget',   'Widget Tableau de bord personnalisé');
        self::addFeatureField('enable_feature_myaccount_info',     'Infos client sur "Mon compte"');
        self::addFeatureField('enable_feature_myaccount_tabs',     'Onglets personnalisés "Mon compte"');
        self::addFeatureField('enable_feature_custom_admin',       'Personnalisation user-edit back-office');
        self::addFeatureField('enable_feature_pvt',                'Personnalisation PVT');

        
        // --- SECTION 2 : Personnalisation “Recherche SKU” ---
        add_settings_section(
            'sku_search_section',
            'Paramètres de la Recherche par SKU',
            function() {
                echo '<p>Configurez le comportement de la recherche par SKU.</p>';
            },
            'creactive-settings'
        );

        // a) Opérateur de comparaison
        add_settings_field(
            'sku_search_compare_operator',
            'Opérateur de comparaison SKU',
            [__CLASS__, 'field_sku_search_compare_operator'],
            'creactive-settings',
            'sku_search_section'
        );

        // b) Inclure les variations
        add_settings_field(
            'include_variations_in_sku_search',
            'Inclure les variations dans la recherche par SKU',
            [__CLASS__, 'field_include_variations_in_sku_search'],
            'creactive-settings',
            'sku_search_section'
        );
    

        // --- SECTION 3 : Personnalisation “Infos Mon compte” ---
        add_settings_section(
            'myaccount_info_section',
            'Paramètres "Infos Mon Compte"',
            function() {
                echo '<p>Personnalisez le titre et le label affichés sur la page "Mon compte".</p>';
            },
            'creactive-settings'
        );

        add_settings_field(
            'client_info_title',
            'Titre pour les infos client',
            [__CLASS__, 'field_client_info_title'],
            'creactive-settings',
            'myaccount_info_section'
        );
        add_settings_field(
            'client_info_number_label',
            'Label du numéro client',
            [__CLASS__, 'field_client_info_number_label'],
            'creactive-settings',
            'myaccount_info_section'
        );

        // --- SECTION 4 : Personnalisation “Onglets Mon compte” ---
        add_settings_section(
            'myaccount_tabs_section',
            'Paramètres "Onglets Mon Compte"',
            function() {
                echo '<p>Personnalisez les slugs / labels de vos onglets personnalisés dans "Mon compte".</p>';
            },
            'creactive-settings'
        );

        add_settings_field(
            'my_account_tab_1_slug',
            'Slug de l’onglet 1',
            [__CLASS__, 'field_my_account_tab_1_slug'],
            'creactive-settings',
            'myaccount_tabs_section'
        );
        add_settings_field(
            'my_account_tab_1_label',
            'Label de l’onglet 1',
            [__CLASS__, 'field_my_account_tab_1_label'],
            'creactive-settings',
            'myaccount_tabs_section'
        );
        add_settings_field(
            'my_account_tab_2_slug',
            'Slug de l’onglet 2',
            [__CLASS__, 'field_my_account_tab_2_slug'],
            'creactive-settings',
            'myaccount_tabs_section'
        );
        add_settings_field(
            'my_account_tab_2_label',
            'Label de l’onglet 2',
            [__CLASS__, 'field_my_account_tab_2_label'],
            'creactive-settings',
            'myaccount_tabs_section'
        );
        add_settings_field(
            'my_account_tab_2_form_id',
            'Form ID pour l’onglet 2',
            [__CLASS__, 'field_my_account_tab_2_form_id'],
            'creactive-settings',
            'myaccount_tabs_section'
        );
        add_settings_field(
            'my_account_tab_3_slug',
            'Slug de l’onglet 3',
            [__CLASS__, 'field_my_account_tab_3_slug'],
            'creactive-settings',
            'myaccount_tabs_section'
        );
        add_settings_field(
            'my_account_tab_3_label',
            'Label de l’onglet 3',
            [__CLASS__, 'field_my_account_tab_3_label'],
            'creactive-settings',
            'myaccount_tabs_section'
        );

    }

    // -----------------------------------------------------
    // Helper pour ajouter un champ "toggle"
    // -----------------------------------------------------
    private static function addFeatureField($option_name, $label) {
        add_settings_field(
            $option_name,
            $label,
            function() use ($option_name) { 
                self::renderCheckbox($option_name);
            },
            'creactive-settings',
            'feature_toggles_section'
        );
    }

    private static function renderCheckbox($option_name) {
        $options = get_option('creactive_settings');
        $checked = !empty($options[$option_name]);
        ?>
        <label>
            <input type="checkbox" name="creactive_settings[<?php echo esc_attr($option_name); ?>]" 
                   value="1" <?php checked($checked); ?>>
            Activer
        </label>
        <?php
    }

    // -----------------------------------------------------
    // Section "Recherche SKU"
    // -----------------------------------------------------
    public static function field_enable_sku_search() {
        $options = get_option('creactive_settings');
        ?>
        <label>
            <input type="checkbox" name="creactive_settings[enable_sku_search]"
                   value="1" <?php checked(!empty($options['enable_sku_search'])); ?>>
            Activer la recherche par SKU
        </label>
        <?php
    }

    public static function field_sku_search_compare_operator() {
        $options = get_option('creactive_settings');
        $operator = isset($options['sku_search_compare_operator']) 
                    ? $options['sku_search_compare_operator'] 
                    : 'LIKE';
        ?>
        <select name="creactive_settings[sku_search_compare_operator]">
            <option value="LIKE" <?php selected($operator, 'LIKE'); ?>>
                Contient (LIKE)
            </option>
            <option value="=" <?php selected($operator, '='); ?>>
                Exact (=)
            </option>
        </select>
        <?php
    }

    public static function field_include_variations_in_sku_search() {
        $options = get_option('creactive_settings');
        ?>
        <label>
            <input type="checkbox" name="creactive_settings[include_variations_in_sku_search]"
                   value="1" <?php checked(!empty($options['include_variations_in_sku_search'])); ?>>
            Inclure les variations
        </label>
        <?php
    }

    // -----------------------------------------------------
    // Champs "Infos Mon compte"
    // -----------------------------------------------------
    public static function field_client_info_title() {
        $options = get_option('creactive_settings');
        $val = $options['client_info_title'] ?? 'Informations Client';
        echo '<input type="text" name="creactive_settings[client_info_title]" value="'.esc_attr($val).'" style="width: 300px;">';
    }
    public static function field_client_info_number_label() {
        $options = get_option('creactive_settings');
        $val = $options['client_info_number_label'] ?? 'Numéro Client';
        echo '<input type="text" name="creactive_settings[client_info_number_label]" value="'.esc_attr($val).'" style="width: 300px;">';
    }

    // -----------------------------------------------------
    // Champs "Onglets Mon compte"
    // -----------------------------------------------------
    public static function field_my_account_tab_1_slug() {
        $options = get_option('creactive_settings');
        $val = $options['my_account_tab_1_slug'] ?? 'mes-tarifs';
        echo '<input type="text" name="creactive_settings[my_account_tab_1_slug]" value="'.esc_attr($val).'" style="width: 300px;">';
    }
    public static function field_my_account_tab_1_label() {
        $options = get_option('creactive_settings');
        $val = $options['my_account_tab_1_label'] ?? 'Mes Tarifs';
        echo '<input type="text" name="creactive_settings[my_account_tab_1_label]" value="'.esc_attr($val).'" style="width: 300px;">';
    }
    public static function field_my_account_tab_2_slug() {
        $options = get_option('creactive_settings');
        $val = $options['my_account_tab_2_slug'] ?? 'mes-demandes-de-devis';
        echo '<input type="text" name="creactive_settings[my_account_tab_2_slug]" value="'.esc_attr($val).'" style="width: 300px;">';
    }
    public static function field_my_account_tab_2_label() {
        $options = get_option('creactive_settings');
        $val = $options['my_account_tab_2_label'] ?? 'Demande de devis';
        echo '<input type="text" name="creactive_settings[my_account_tab_2_label]" value="'.esc_attr($val).'" style="width: 300px;">';
    }
    public static function field_my_account_tab_2_form_id() {
        $options = get_option('creactive_settings');
        $val = $options['my_account_tab_2_form_id'] ?? '';
        echo '<input type="number" name="creactive_settings[my_account_tab_2_form_id]" value="'.esc_attr($val).'" style="width: 100px;">';
    }
    public static function field_my_account_tab_3_slug() {
        $options = get_option('creactive_settings');
        $val = $options['my_account_tab_3_slug'] ?? 'mes-factures';
        echo '<input type="text" name="creactive_settings[my_account_tab_3_slug]" value="'.esc_attr($val).'" style="width: 300px;">';
    }
    public static function field_my_account_tab_3_label() {
        $options = get_option('creactive_settings');
        $val = $options['my_account_tab_3_label'] ?? 'Mes factures';
        echo '<input type="text" name="creactive_settings[my_account_tab_3_label]" value="'.esc_attr($val).'" style="width: 300px;">';
    }

    // -------------------------------------------
    //  Page d’options (mise en page)
    // -------------------------------------------
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

    // -------------------------------------------
    //  Scripts/CSS
    // -------------------------------------------
    public static function adminScripts($hook) {
        if ($hook !== 'toplevel_page_creactive-settings') {
            return;
        }
        wp_enqueue_style(
            'creactive_admin_style', 
            CREACTIVEWEB_PLUGIN_URL . 'inc/css/style-admin.css',
            [],
            '1.0'
        );
    }
}