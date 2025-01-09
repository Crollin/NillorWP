<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Activator {
    public static function activate() {
        // Ici, tu peux ajouter les opérations à effectuer lors de l’activation du plugin
        // (par exemple création de tables, flush des permaliens, etc.)
        flush_rewrite_rules();
    }
}
