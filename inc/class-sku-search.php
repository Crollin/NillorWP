<?php
namespace CreactiveWeb;

if ( ! defined('ABSPATH') ) {
    exit; // Sécurité
}

class SkuSearch {

    public static function init() {
        // On se branche sur 'posts_search' avec une priorité élevée (999).
        add_filter('posts_search', [ __CLASS__, 'searchBySku' ], 999, 2);
    }

    /**
     * Intercepte la requête de recherche WordPress.
     * Injecte dans la requête les IDs de produits correspondants au SKU cherché.
     */
    public static function searchBySku($search, $wp_query) {
        global $wpdb;

        // 1) Vérifier s'il y a bien une recherche en cours
        if ( ! isset($wp_query->query['s']) || empty($wp_query->query['s']) ) {
            return $search;
        }

        // 2) Vérifier si la fonctionnalité SKU Search est activée
        $options = get_option('creactive_settings');
        if ( empty($options['enable_feature_sku_search']) ) {
            return $search;
        }

        // 3) Récupérer l’opérateur (LIKE ou =). Par défaut: LIKE
        $compare_operator = ! empty($options['sku_search_compare_operator']) 
            ? $options['sku_search_compare_operator'] 
            : 'LIKE';

        // 4) La valeur tapée par l'utilisateur
        $search_value = $wp_query->query['s'];

        // 5) Récupérer tous les products + variations dont le _sku matche
        //    On ne dépend pas de la requête principale, on fait un get_posts
        $matching_ids = get_posts([
            'post_type'      => ['product', 'product_variation'],
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'     => '_sku',
                    'value'   => $search_value,
                    'compare' => $compare_operator
                ]
            ]
        ]);

        // S’il n’y a **aucun** produit correspondant, on ne modifie pas la requête
        if ( empty($matching_ids) ) {
            return $search;
        }

        // 6) Construction d’un OR ID IN(...) pour injecter nos IDs
        $ids_string = implode(',', $matching_ids);

        // Petit log de debug si besoin
        // error_log('*** searchBySku was called *** IDs found: ' . $ids_string);

        // 7) Injecter nos IDs dans $search
        //    - On ESSAIE d’insérer juste après 'AND (((', si on le trouve.
        //    - Sinon, on ajoute un simple "OR wp_posts.ID IN(...)" en fin de la clause WHERE.
        if ( strpos($search, 'AND (((') !== false ) {
            // Cas où la requête contient "AND ((("
            // On insère un "OR wp_posts.ID IN(...)"
            $search = str_replace(
                'AND (((',
                "AND ((({$wpdb->posts}.ID IN ($ids_string)) OR (",
                $search
            );
        } else {
            // Si la structure 'AND (((' n’apparaît pas,
            // on ajoute un OR ... en fin du $search
            $search .= $wpdb->prepare(
                " OR {$wpdb->posts}.ID IN ($ids_string)",
                ''
            );
        }

        return $search;
    }
}