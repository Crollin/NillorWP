<?php
namespace CreactiveWeb;

if (!defined('ABSPATH')) exit;

class DashboardWidget {

    public static function init() {
        // On ne crée le widget que si la case "enable_feature_dashboard_widget" est cochée
        add_action('wp_dashboard_setup', [ __CLASS__, 'addDashboardWidget' ]);
    }

    /**
     * Ajoute le widget sur le tableau de bord
     */
    public static function addDashboardWidget() {
        wp_add_dashboard_widget(
            'creactive_dashboard_widget',  // ID
            'Mon Widget Creactive',        // Titre par défaut
            [ __CLASS__, 'renderWidget']   // Callback d’affichage
        );
    }

    /**
     * Affiche le widget (lit les champs "dashboard_widget_title", etc.)
     */
    public static function renderWidget() {
        $options = get_option('creactive_settings');

        $title    = !empty($options['dashboard_widget_title'])
                    ? $options['dashboard_widget_title']
                    : 'Mon Widget';
        $subtitle = !empty($options['dashboard_widget_subtitle'])
                    ? $options['dashboard_widget_subtitle']
                    : '';
        $message  = !empty($options['dashboard_widget_message'])
                    ? $options['dashboard_widget_message']
                    : 'Bienvenue sur le tableau de bord !';

        echo '<div class="creactive-dashboard-widget">';
        echo '<h2 style="color: #0071bc;">' . esc_html($title) . '</h2>';
        if ($subtitle) {
            echo '<h4 style="margin-top: -10px; color: #444;">' . esc_html($subtitle) . '</h4>';
        }
        echo '<p style="margin-top: 1em;">' . nl2br(esc_html($message)) . '</p>';
        echo '</div>';
    }
}