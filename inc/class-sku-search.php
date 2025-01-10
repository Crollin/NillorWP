<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SkuSearch {

    public static function init() {
        add_filter('posts_search', [ __CLASS__, 'searchBySku' ], 999, 2);
    }

    public static function searchBySku($search, $query_vars) {
        global $wpdb;
        $options = get_option('creactive_settings');

        // Vérifier si la recherche par SKU est activée
        if (!isset($options['enable_sku_search']) || $options['enable_sku_search'] != 1) {
            return $search;
        }

        // Operator
        $compare_operator = $options['sku_search_compare_operator'] ?? 'LIKE';
        $include_variations = isset($options['include_variations_in_sku_search']) && $options['include_variations_in_sku_search'] == 1;

        if (isset($query_vars->query['s']) && !empty($query_vars->query['s'])) {
            // Recherche dans les produits simples
            $posts = get_posts([
                'posts_per_page' => -1,
                'post_type'      => 'product',
                'meta_query'     => [
                    [
                        'key'     => '_sku',
                        'value'   => $query_vars->query['s'],
                        'compare' => $compare_operator
                    ]
                ]
            ]);

            $get_post_ids = [];
            foreach ($posts as $post) {
                $get_post_ids[] = $post->ID;
            }

            // Recherche dans les variations
            if ($include_variations) {
                $posts_variation = get_posts([
                    'posts_per_page' => -1,
                    'post_type'      => 'product_variation',
                    'meta_query'     => [
                        [
                            'key'     => '_sku',
                            'value'   => $query_vars->query['s'],
                            'compare' => $compare_operator
                        ]
                    ]
                ]);
                foreach ($posts_variation as $post) {
                    $get_post_ids[] = $post->post_parent;
                }
            }

            if (empty($get_post_ids)) {
                return $search;
            }

            // Injecter nos IDs dans la requête de recherche
            $search = str_replace(
                'AND (((',
                "AND ((({$wpdb->posts}.ID IN (" . implode(',', array_unique($get_post_ids)) . ")) OR (",
                $search
            );
        }

        return $search;
    }
}