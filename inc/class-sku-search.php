<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SkuSearch {

    public static function init() {
        add_filter('posts_search', [ __CLASS__, 'searchBySku' ], PHP_INT_MAX, 2);
    }

    public static function searchBySku($search, $query_vars) {
        global $wpdb;

        // Récupérer les options
        $options = get_option('creactive_settings');

        // Vérifier si la recherche par SKU est activée
        if (empty($options['enable_feature_sku_search']) || $options['enable_feature_sku_search'] !== '1') {
            return $search;
        }

        // Obtenir l'opérateur de comparaison (par défaut 'LIKE')
        $compare_operator = $options['sku_search_compare_operator'] ?? 'LIKE';

        // Vérifier si les variations doivent être incluses
        $include_variations = isset($options['include_variations_in_sku_search']) && $options['include_variations_in_sku_search'] == 1;

        if (isset($query_vars->query['s']) && !empty($query_vars->query['s'])) {
            // Recherche dans les produits simples
            $args = [
                'posts_per_page' => -1,
                'post_type'      => 'product',
                'meta_query'     => [
                    [
                        'key'     => '_sku',
                        'value'   => $query_vars->query['s'],
                        'compare' => $compare_operator
                    ]
                ]
            ];

            $posts = get_posts($args);
            $get_post_ids = [];

            foreach ($posts as $post) {
                $get_post_ids[] = $post->ID;
            }

            // Si les variations doivent être incluses
            if ($include_variations) {
                // Recherche dans les variations
                $args = [
                    'posts_per_page' => -1,
                    'post_type'      => 'product_variation',
                    'meta_query'     => [
                        [
                            'key'     => '_sku',
                            'value'   => $query_vars->query['s'],
                            'compare' => $compare_operator
                        ]
                    ]
                ];

                $posts_variation = get_posts($args);

                foreach ($posts_variation as $post) {
                    $get_post_ids[] = $post->post_parent;
                }
            }

            // Si aucun produit n'a été trouvé, retourner la recherche initiale
            if (empty($get_post_ids)) {
                return $search;
            }

            // Modifier la requête de recherche pour inclure les produits trouvés
            $search = str_replace(
                'AND (((',
                "AND ((({$wpdb->posts}.ID IN (" . implode(',', array_unique($get_post_ids)) . ")) OR (",
                $search
            );
        }

        return $search;
    }
}
