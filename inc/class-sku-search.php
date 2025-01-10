<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SkuSearch {
    public static function init() {
        add_filter('posts_search', [ __CLASS__, 'searchBySku' ], 999, 2);
    }

    public static function searchBySku($search, $query) {
        global $wpdb;

        $options = get_option('creactive_settings');
        if (!isset($options['enable_feature_sku_search']) || $options['enable_feature_sku_search'] != 1) {
            return $search; // Sécurité
        }

        $compare_operator = $options['sku_search_compare_operator'] ?? 'LIKE';

        if (isset($query->query['s']) && !empty($query->query['s'])) {
            $user_search = $query->query['s'];

            // Recherche produit simple
            $posts = get_posts([
                'posts_per_page' => -1,
                'post_type'      => 'product',
                'meta_query'     => [
                    [
                        'key'     => '_sku',
                        'value'   => $user_search,
                        'compare' => $compare_operator
                    ]
                ]
            ]);

            $post_ids = [];
            foreach($posts as $p) {
                $post_ids[] = $p->ID;
            }

            // Recherche variations ? Optionnel
            // ...

            if (!empty($post_ids)) {
                $search = str_replace(
                    'AND (((',
                    "AND ((({$wpdb->posts}.ID IN (" . implode(',', $post_ids) . ")) OR (",
                    $search
                );
            }
        }
        return $search;
    }
}