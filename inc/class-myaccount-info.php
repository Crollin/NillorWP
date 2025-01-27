<?php
namespace CreactiveWeb;

if (!defined('ABSPATH')) exit;

class MyAccountInfo {
    public static function init() {
        // On affiche les infos sur le dashboard "Mon compte"
        add_action('woocommerce_account_dashboard', [ __CLASS__, 'afficherInfosClient' ]);
    }

    public static function afficherInfosClient() {
        $options = get_option('creactive_settings');

        $user_id       = get_current_user_id();
        $numero_client = get_field('CDCLI', 'user_'.$user_id); // ou autre meta

        $title        = $options['client_info_title']        ?? 'Informations Client';
        $number_label = $options['client_info_number_label'] ?? 'Numéro Client';

        echo '<div class="myaccount-info-block">';
        echo '<h3>' . esc_html($title) . '</h3>';
        echo '<p><strong>' . esc_html($number_label) . ' :</strong> ' . esc_html($numero_client) . '</p>';
        echo '</div>';
    }
}