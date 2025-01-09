<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VariationPrice {

    public static function init() {
        add_filter('woocommerce_variable_sale_price_html', [ __CLASS__, 'variationPriceFormat' ], 10, 2);
        add_filter('woocommerce_variable_price_html',      [ __CLASS__, 'variationPriceFormat' ], 10, 2);
    }

    public static function variationPriceFormat($price, $product) {
        $min_price = $product->get_variation_price('min', true);
        $max_price = $product->get_variation_price('max', true);

        if ($min_price != $max_price) {
            // À partir de
            $price = sprintf(__('À partir de %1$s', 'woocommerce'), wc_price($min_price));
        } else {
            // Prix unique
            $price = sprintf(__('%1$s', 'woocommerce'), wc_price($min_price));
        }

        return $price;
    }
}
