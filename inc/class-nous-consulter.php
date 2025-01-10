<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NousConsulter {

    public static function init() {
        add_filter('woocommerce_get_price_html', [ __CLASS__, 'customPriceMessage' ], 100, 2);
        add_action('woocommerce_before_lost_password_form', [ __CLASS__, 'customLostPasswordTitle' ]);
    }

    public static function customPriceMessage($price, $product) {
        if (floatval($product->get_price()) === floatval(0.01)) {
            $options = get_option('creactive_settings');
            $nous_consulter_text = $options['nous_consulter_text'] ?? 'Nous consulter';
            $nous_consulter_url  = $options['nous_consulter_url']  ?? 'https://nillor.eu/demande-renseignement-produit/';

            return '<a href="' . esc_url($nous_consulter_url) . '" class="price-nous-consulter">' 
                    . esc_html($nous_consulter_text) . '</a>';
        }
        return $price;
    }

    public static function customLostPasswordTitle() {
        $options = get_option('creactive_settings');
        $title = $options['lost_password_title'] ?? 'Réinitialisez votre mot de passe';

        echo '<h2 style="font-size: 30px; color: #0071bc;">' . esc_html($title) . '</h2>';
    }
}
