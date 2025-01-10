<?php
namespace CreactiveWeb;

if ( ! defined('ABSPATH') ) {
    exit;
}

class SkuSearch {

    public static function init() {
        add_filter('posts_search', [ __CLASS__, 'searchBySku' ], 999, 2);
    }

    public static function searchBySku($search, $query_vars) {
        global $wpdb;

        // 1) Récupération des options
        $options = get_option('creactive_settings');
        if ( empty($options['enable_sku_search']) ) {
            // Si la recherche par SKU n'est pas cochée, on sort
            return $search;
        }

        // 2) Vérifier qu'on est bien en train de faire une recherche (paramètre 's')
        if ( ! isset($query_vars->query['s']) || empty($query_vars->query['s']) ) {
            return $search;
        }
        $search_term = $query_vars->query['s'];

        // 3) Déterminer l'opérateur (LIKE ou =)
        $compare_operator = ! empty($options['sku_search_compare_operator'])
            ? $options['sku_search_compare_operator']
            : 'LIKE';  // par défaut

        // 4) Vérifier si on inclut les variations
        $include_variations = ! empty($options['include_variations_in_sku_search']);

        // 5) Construire la requête get_posts pour trouver les IDs qui matchent
        $post_types = ['product'];
        if ($include_variations) {
            $post_types[] = 'product_variation';
        }

        $args = [
            'post_type'      => $post_types,
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'     => '_sku',
                    'value'   => $search_term,
                    'compare' => $compare_operator
                ]
            ]
        ];
        $found_posts = get_posts($args);

        if ( empty($found_posts) ) {
            // Si aucun produit (ou variation) ne correspond au SKU cherché,
            // on ne modifie pas la requête (la recherche habituelle s’applique).
            return $search;
        }

        // Convertir en string
        $ids_string = implode(',', $found_posts);

        // 6) Injecter un OR {wpdb->posts}.ID IN(...) dans la requête
        // On cherche si "AND (((" est présent. Sinon on concatène un OR à la fin.
        if ( strpos($search, 'AND (((') !== false ) {
            // On injecte dans la parenthèse
            $search = str_replace(
                'AND (((',
                "AND ((({$wpdb->posts}.ID IN ($ids_string)) OR (",
                $search
            );
        } else {
            // Fallback : on ajoute un OR en fin
            $search .= $wpdb->prepare(
                " OR {$wpdb->posts}.ID IN ($ids_string)",
                ''
            );
        }

        return $search;
    }

}