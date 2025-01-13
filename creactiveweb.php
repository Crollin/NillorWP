<?php
/**
 * Plugin Name: Creactiveweb
 * Plugin URI: https://creactiveweb.com
 * Description: Plugin personnalisé pour Nillor - Recherche SKU, Personnalisation B2BKing, Onglets "Mon compte", etc.
 * Version: 2.4.0
 * Author: Creactive
 * Author URI: https://creactiveweb.com
 * License: GPL2
 * Text Domain: creactiveweb
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Sécurité : empêche l'accès direct aux fichiers
}

// -----------------------------------------------------------------------------
// 1. Définition des constantes principales du plugin
// -----------------------------------------------------------------------------
define( 'CREACTIVEWEB_VERSION', '2.4.0' );
define( 'CREACTIVEWEB_PLUGIN_FILE', __FILE__ );
define( 'CREACTIVEWEB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CREACTIVEWEB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// -----------------------------------------------------------------------------
// 2. Inclusion des fichiers de classes
// -----------------------------------------------------------------------------
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-activator.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-desactivator.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-admin-settings.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-sku-search.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-b2bking-pdf-custom.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-myaccount-info.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-myaccount-tabs.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-shortcodes.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-variation-price.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-nous-consulter.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-custom-admin-user-edit.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-dashboard-widget.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-renamer.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-init.php';
require_once CREACTIVEWEB_PLUGIN_PATH . 'inc/class-pvt-customization.php';

// -----------------------------------------------------------------------------
// 3. Hooks d’activation et de désactivation
// -----------------------------------------------------------------------------
function creactiveweb_activate_plugin() {
    \CreactiveWeb\Activator::activate();
}
register_activation_hook( __FILE__, 'creactiveweb_activate_plugin' );

function creactiveweb_deactivate_plugin() {
    \CreactiveWeb\Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'creactiveweb_deactivate_plugin' );

// -----------------------------------------------------------------------------
// 4. Lancement du plugin
// -----------------------------------------------------------------------------
function creactiveweb_run() {
    // On instancie la classe Init qui se charge d'initialiser toutes les features
    $plugin_init = new \CreactiveWeb\Init();
    $plugin_init->run();
}
add_action('plugins_loaded', 'creactiveweb_run');