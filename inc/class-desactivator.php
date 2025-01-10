<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Deactivator {
    public static function deactivate() {
        // Opérations à réaliser lors de la désactivation du plugin
        flush_rewrite_rules();
    }
}
