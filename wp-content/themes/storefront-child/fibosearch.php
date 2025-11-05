<?php
/**
 * 1) Capture product IDs from TNT results and store them for the duration of the current request.
 */
add_filter('dgwt/wcas/tnt/search_results/ids', function ($ids) {
    if (empty($ids)) {
        return $ids;
    }
    // Remember product IDs in a global (request-scoped)
    $GLOBALS['fibo_last_search_product_ids'] = array_values(array_filter(array_map('absint', (array) $ids)));
    return $ids;
}, 10, 1);

/**
 * Helper: return term_id values from product_cat and product_brand for a list of product IDs.
 *
 * @param int[] $product_ids
 * @return int[] Unique term IDs
 */
function fibo_terms_from_products(array $product_ids): array {
    global $wpdb;

    $product_ids = array_values(array_filter(array_map('absint', $product_ids)));
    if (empty($product_ids)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($product_ids), '%d'));
    $tax_cat   = 'product_cat';
    $tax_brand = 'product_brand';

    $sql = "
        SELECT DISTINCT tt.term_id
        FROM {$wpdb->term_relationships} tr
        INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
        WHERE tr.object_id IN ($placeholders)
          AND tt.taxonomy IN (%s, %s)
    ";

    $params = array_merge($product_ids, [$tax_cat, $tax_brand]);
    $term_ids = $wpdb->get_col($wpdb->prepare($sql, $params));

    // Return unique, reindexed integers
    return array_values(array_unique(array_map('intval', (array) $term_ids)));
}

/**
 * 2) Expand the provided term_ids with terms derived from the product IDs captured above.
 */
add_filter('dgwt/wcas/search_results/term_ids', function ($ids, $phrase) {
    // Normalize incoming term IDs
    $ids = array_values(array_filter(array_map('absint', (array) $ids)));

    // If we have product IDs from the current search request, use them
    $product_ids = $GLOBALS['fibo_last_search_product_ids'] ?? [];
    if (!empty($product_ids)) {
        $add_ids = fibo_terms_from_products($product_ids);
        if (!empty($add_ids)) {
            // Merge and deduplicate
            $ids = array_values(array_unique(array_merge($ids, $add_ids)));
        }
    }

    return $ids;
}, 10, 2);