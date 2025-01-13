<?php
namespace CreactiveWeb;

if (!defined('ABSPATH')) exit;

class PVTCustomization {

    public static function init() {
        // Ajouter le style CSS pour cacher les colonnes pour les non-connectés
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueCustomStyles']);

        // Traduire les en-têtes de colonnes de PVT
        add_filter('gettext', [__CLASS__, 'translateTableHeaders'], 20, 3);
    }

    /**
     * Chargement du CSS pour cacher les colonnes non désirées pour les utilisateurs non connectés
     */
    public static function enqueueCustomStyles() {
        wp_enqueue_style(
            'pvt-custom-styles',
            CREACTIVEWEB_PLUGIN_URL . 'assets/css/pvt-custom.css', // Chemin vers le fichier CSS
            [],
            '1.0.0'
        );
    }

    /**
     * Traduire les en-têtes dynamiques de PVT
     */
    public static function translateTableHeaders($translated_text, $text, $domain) {
        // Vérifie si le texte est dans le domaine de PVT
        if ($domain === 'pvt') {
            switch ($text) {
                case 'Price':
                    $translated_text = 'Prix';
                    break;
                case 'Quantity':
                    $translated_text = 'Quantité';
                    break;
                case 'Add to cart':
                    $translated_text = 'Ajouter au panier';
                    break;
                case 'Availability':
                    $translated_text = 'Disponibilité';
                    break;
                default:
                    break;
            }
        }
        return $translated_text;
    }
}