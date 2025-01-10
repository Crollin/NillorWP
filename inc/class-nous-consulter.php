<?php
namespace CreactiveWeb;

if ( ! defined('ABSPATH') ) {
    exit;
}

class NousConsulter {
    public static function init() {
        add_filter('woocommerce_get_price_html', [ __CLASS__, 'customPriceMessage' ], 100, 2);
    }

    public static function customPriceMessage($price, $product) {
        if ( (float) $product->get_price() === 0.01 ) {
            $options = get_option('creactive_settings');
            $text = $options['nous_consulter_text'] ?? 'Nous consulter';
            $url  = $options['nous_consulter_url']  ?? '#';
            return '<a href="' . esc_url($url) . '" class="price-nous-consulter">' . esc_html($text) . '</a>';
        }
        return $price;
    }
}