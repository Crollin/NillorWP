<?php
namespace CreactiveWeb;

if (!defined('ABSPATH')) exit;

class PVTCustomization {
    public static function init() {
        // Traduction dynamique des colonnes
        add_filter('pvtfw_table_column_title_quantity', [__CLASS__, 'translateQuantity']);
        add_filter('pvtfw_table_column_title_price', [__CLASS__, 'translatePrice']);

        // Cacher les colonnes si l'utilisateur n'est pas connecté
        add_action('wp_enqueue_scripts', [__CLASS__, 'hideColumnsForGuests']);
    }

    // Traduction de "Quantity"
    public static function translateQuantity($title) {
        return __('Quantité', 'creactiveweb'); // Remplace "Quantity" par "Quantité"
    }

    // Traduction de "Price"
    public static function translatePrice($title) {
        return __('Prix', 'creactiveweb'); // Remplace "Price" par "Prix"
    }

    // Masquer les colonnes si l'utilisateur n'est pas connecté
    public static function hideColumnsForGuests() {
        if (!is_user_logged_in()) {
            wp_enqueue_style(
                'pvt-custom-styles',
                CREACTIVEWEB_PLUGIN_URL . 'inc/css/pvt-custom.css',
                [],
                '1.0'
            );
        }
    }
}