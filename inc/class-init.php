<?php

namespace CreactiveWeb;

if (!defined('ABSPATH')) exit;

class Init
{
    public function run()
    {
        // Charge la page Admin
        AdminSettings::register();

        $options = get_option('creactive_settings');

        // SKU Search
        if (! empty($options['enable_feature_sku_search'])) {
            SkuSearch::init();
        }
        // B2BKing PDF
        if (!empty($options['enable_feature_b2bking_pdf'])) {
            B2bkingPdfCustom::init();
        }
        // Variation Price
        if (!empty($options['enable_feature_variation_price'])) {
            VariationPrice::init();
        }
        // Nous Consulter
        if (!empty($options['enable_feature_nous_consulter'])) {
            NousConsulter::init();
        }
        // Shortcodes
        if (!empty($options['enable_feature_shortcodes'])) {
            Shortcodes::init();
        }
        // Dashboard widget
        if (!empty($options['enable_feature_dashboard_widget'])) {
            DashboardWidget::init();
        }
        // MyAccount Info
        if (!empty($options['enable_feature_myaccount_info'])) {
            MyAccountInfo::init();
        }
        // MyAccount Tabs
        if (!empty($options['enable_feature_myaccount_tabs'])) {
            MyAccountTabs::init();
        }
        // Custom admin user-edit
        if (!empty($options['enable_feature_custom_admin'])) {
            CustomAdminUserEdit::init();
        }
        add_filter('pvt_column_headers', function ($headers) {
            $headers['quantity'] = 'Quantité'; // Exemple de traduction
            $headers['price'] = 'Prix';
            $headers['add_to_cart'] = 'Ajouter au panier';

            return $headers;
        });
    }
}
