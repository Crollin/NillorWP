<?php
namespace CreactiveWeb;

if ( ! defined('ABSPATH') ) {
    exit;
}

class Shortcodes {
    public static function init() {
        add_shortcode('connected_user', [ __CLASS__, 'shortcode_connected_user' ]);
        add_shortcode('guest_user', [ __CLASS__, 'shortcode_guest_user' ]);
        add_shortcode('is_logged_in', [ __CLASS__, 'shortcode_is_logged_in' ]);
        add_shortcode('is_not_logged_in', [ __CLASS__, 'shortcode_is_not_logged_in' ]);
    }

    public static function shortcode_connected_user($atts, $content = null) {
        return is_user_logged_in() ? $content : '';
    }
    public static function shortcode_guest_user($atts, $content = null) {
        return !is_user_logged_in() ? $content : '';
    }
    public static function shortcode_is_logged_in($atts, $content = null) {
        return is_user_logged_in() ? do_shortcode($content) : '';
    }
    public static function shortcode_is_not_logged_in($atts, $content = null) {
        return !is_user_logged_in() ? do_shortcode($content) : '';
    }
}