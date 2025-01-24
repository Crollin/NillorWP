<?php

namespace CreactiveWeb;

if (! defined('ABSPATH')) {
    exit;
}

class DashboardWidget
{

    public static function init()
    {
        add_action('admin_footer', [__CLASS__, 'customAdminDashboardWelcomePanel']);
        add_action('admin_notices', [__CLASS__, 'showCustomWelcomePanel']);
        add_filter('contextual_help', [__CLASS__, 'removeHelpTabs'], 999, 3);
        add_action('admin_head', [__CLASS__, 'customWelcomePanelStyles']);
    }

    // Déplacer le panneau avant #dashboard-widgets-wrap
    public static function customAdminDashboardWelcomePanel()
    {
        $screen = get_current_screen();
        if ($screen->base !== 'dashboard') {
            return;
        }

        echo '
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $("#dashboard-widgets-wrap").before($("#custom-welcome-panel").show());
        });
        </script>';
    }

    public static function showCustomWelcomePanel()
    {
        // Vérifier si on est sur le Dashboard principal
        $screen = get_current_screen();
        if ($screen->base !== 'dashboard') {
            return;
        }

        // Vérifie si WooCommerce est actif
        if (!class_exists('WooCommerce')) {
            return;
        }

        $current_user = wp_get_current_user();
        $user_name = $current_user->display_name;

        $options = get_option('creactive_settings');
        $logo_url = $options['custom_logo'] ?? 'https://nillor.eu/wp-content/uploads/2024/04/nillor.png';

        // On récupère les données WC
        $data = self::customDashboardWooCommerceData();

        // On récupère les actions rapides
        $quick_actions = $options['dashboard_quick_actions'] ?? '';
        $quick_actions_array = [];
        if ($quick_actions) {
            $lines = explode("\n", $quick_actions);
            foreach ($lines as $line) {
                $parts = explode('|', $line);
                if (count($parts) >= 2) {
                    $label = trim($parts[0]);
                    $url   = trim($parts[1]);
                    $button_type = isset($parts[2]) ? trim($parts[2]) : 'primary';
                    $quick_actions_array[] = [
                        'label' => $label,
                        'url'   => $url,
                        'type'  => $button_type
                    ];
                }
            }
        }

        echo '
        <div id="custom-welcome-panel" class="welcome-panel postbox" style="display: none; margin-bottom: 20px;">
            <div class="welcome-panel-content">
                <div class="custom-panel-container welcome-panel-column-container" style="display: flex; justify-content: space-between; align-items: center;">
                    
                    <!-- Colonne 1 : Logo et Bienvenue -->
                    <div class="welcome-panel-column" style="flex: 1; text-align: center; margin-right: 20px;">
                        <img src="' . esc_url($logo_url) . '" alt="Logo" style="width: 200px; height: auto; margin-bottom: 50px;">
                        <h2 class="titre_principale">Bonjour <a href="profile.php" style="color: #0071BC;">' . esc_html($user_name) . '</a> !</h2>
                        <p style="font-size: 20px; color: #555;">Bienvenue dans votre espace d\'administration.</p>
                    </div>

                    <!-- Colonne 2 : Actions Rapides -->
                    <div class="welcome-panel-column" style="flex: 1; padding: 20px; background: #f9f9f9; border-radius: 10px;">
                        <h3>Actions rapides</h3>
                        <ul style="list-style: none; padding-left: 0; margin: 0; display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">';

        foreach ($quick_actions_array as $action) {
            echo '<li>
                                <a href="' . esc_url(admin_url($action['url'])) . '" class="button button-' . esc_attr($action['type']) . '" style="width: 100%;">' . esc_html($action['label']) . '</a>
                            </li>';
        }

        echo '</ul>
                    </div>

                    <!-- Colonne 3 : État de WooCommerce -->
                    <div class="welcome-panel-column" style="flex: 1; text-align: center;">
                        <h3 class="titre_secondaire">Informations Ventes</h3>
                        <div class="dashboard-stats">
                            <div class="stat-box sales-box">
                                <p style="font-weight: 600;">Ventes ce mois-ci</p>
                                <p class="stat-number">' . esc_html($data['sales']) . '</p>
                            </div>
                            <div class="stat-box processing-box">
                                <p style="font-weight: 600;">Commandes en cours</p>
                                <p class="stat-number">' . esc_html($data['orders_processing']) . '</p>
                            </div>
                            <div class="stat-box pending-box">
                                <p style="font-weight: 600;">Commandes en attente</p>
                                <p class="stat-number">' . esc_html($data['orders_pending']) . '</p>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Ligne de texte en bas des colonnes -->
                <div style="text-align: center; margin-top: 20px; font-size: 14px; color: #777;">
                    <p>';

        $footer_text = $options['dashboard_footer_text'] ?? 'Ce site a été conçu par';
        $footer_link = $options['dashboard_footer_link'] ?? 'https://creactiveweb.com';

        echo esc_html($footer_text) . ' <a href="' . esc_url($footer_link) . '" target="_blank" style="color: #0071a1;">Creactiveweb</a>.';

        echo '</p>
                </div>
            </div>
        </div>';
    }

    // Récupérer les données WooCommerce
    public static function customDashboardWooCommerceData()
    {
        if (!class_exists('WooCommerce')) {
            return [
                'sales' => 'WooCommerce non disponible',w
                'orders_processing' => 'N/A',
                'orders_pending' => 'N/A'
            ];
        }
        $current_month = date('m');
        $current_year  = date('Y');

        // Commandes en cours et en attente
        $orders_processing = wc_orders_count('processing');
        $orders_pending    = wc_orders_count('pending');

        $options = get_option('creactive_settings');
        $consumer_key    = $options['api_consumer_key']    ?? '';
        $consumer_secret = $options['api_consumer_secret'] ?? '';

        if (empty($consumer_key) || empty($consumer_secret)) {
            return [
                'sales' => 'Clés API non définies',
                'orders_processing' => $orders_processing,
                'orders_pending'    => $orders_pending
            ];
        }

        $request_url = get_home_url() . "/wp-json/wc/v3/reports/sales?date_min=$current_year-$current_month-01&date_max="
            . date('Y-m-t', strtotime("$current_year-$current_month-01"));

        $response = wp_remote_get($request_url, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($consumer_key . ':' . $consumer_secret),
            ]
        ]);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
            $monthly_sales = 0;
        } else {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            $monthly_sales = isset($data[0]['total_sales']) ? floatval($data[0]['total_sales']) : 0;
        }

        $currency_symbol = get_woocommerce_currency_symbol();
        $formatted_sales = number_format($monthly_sales, 2, ',', ' ') . ' ' . $currency_symbol;

        return [
            'sales'             => $formatted_sales,
            'orders_processing' => $orders_processing,
            'orders_pending'    => $orders_pending
        ];
    }

    // Enlever les onglets d’aide
    public static function removeHelpTabs($old_help, $screen_id, $screen)
    {
        $screen->remove_help_tabs();
        return $old_help;
    }

    public static function customWelcomePanelStyles()
    {
        $options = get_option('creactive_settings');
        $custom_color = $options['custom_colors'] ?? '#0071bc';

        echo '
        <style>
        #custom-welcome-panel {
            width: calc(100% - 20px) !important;
            background: #fff;
            border: 1px solid #e5e5e5;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
            padding: 20px;
        }
        #custom-welcome-panel .welcome-panel h3,
        #custom-welcome-panel .titre_principale a {
            color: ' . esc_attr($custom_color) . ';
        }
        #custom-welcome-panel .welcome-panel-column-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        #custom-welcome-panel .welcome-panel-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 10px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            flex: 1;
        }
        #custom-welcome-panel .titre_principale {
            color: #333;
            padding-bottom: 30px;
            font-size : 35px;
        }
        .welcome-panel h3 {
            color: #0071bc;
            font-size: 25px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 15px;
        }
        #custom-welcome-panel .welcome-panel-column ul {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        #custom-welcome-panel .welcome-panel-column .button {
            margin-bottom: 10px;
            width: 100%;
            border-radius: 30px !important;
        }
        #custom-welcome-panel .dashboard-stats {
            display: flex;
            justify-content: space-around;
            width: 100%;
        }
        #custom-welcome-panel .stat-box {
            padding: 15px;
            border-radius: 8px;
            color: #fff;
            text-align: center;
            flex: 1;
            margin: 0 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .sales-box { background-color: #399E5A; }
        .processing-box { background-color: #38618C; }
        .pending-box { background-color: #0071BC; }

        @media (max-width: 1024px) {
            #custom-welcome-panel .welcome-panel-column-container {
                flex-direction: column;
                align-items: center;
            }
            #custom-welcome-panel .welcome-panel-column {
                width: 90%;
                margin: 15px 0;
            }
            #custom-welcome-panel .dashboard-stats {
                flex-direction: column;
                align-items: center;
            }
        }
        @media (max-width: 768px) {
            #custom-welcome-panel .welcome-panel-column ul {
                grid-template-columns: 1fr;
            }
            #custom-welcome-panel .stat-box {
                width: 90%;
                margin: 10px 0;
            }
        }
        @media (max-width: 480px) {
            #custom-welcome-panel .welcome-panel-column h3 {
                font-size: 20px;
            }
            #custom-welcome-panel .welcome-panel-column img {
                width: 150px;
            }
            #custom-welcome-panel .titre_principale {
                font-size : 25px;
            }
            #custom-welcome-panel .stat-number {
                font-size: 15px;
            }
        }
        </style>';
    }
}
