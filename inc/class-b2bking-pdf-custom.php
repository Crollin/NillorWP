<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class B2bkingPdfCustom {

    public static function init() {
        add_filter('b2bking_custom_content_offer_pdf_left_2', [ __CLASS__, 'customContent' ], 10, 1);
    }

    public static function customContent($text) {
        $options = get_option('creactive_settings');
        $custom_text = $options['b2bking_pdf_custom_text'] ?? 'Your custom content and test here';
        return $custom_text;
    }
}
