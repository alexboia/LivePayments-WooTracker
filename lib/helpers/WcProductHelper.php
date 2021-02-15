<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\Helpers {
    use WC_Product;

    class WcProductHelpers {
        public static function getProductVariantNameforTracking(WC_Product $product) {
            $variant = '';
            if ($product instanceof WC_Product_Variation) {
                $variant = wc_get_formatted_variation($product, true, true, true);
            }

            return $variant;
        }

        public static function getProductCategoryNameForTracking(WC_Product $product) {
            $name = '';
            $categoryIds = $product->get_category_ids();
            
            if (!empty($categoryIds) && is_array($categoryIds)) {
                $name = self::getProductCategoryName($categoryIds[0]);
            }

            return $name;
        }

        public static function getProductCategoryName($categoryId) {
            $term = get_term($categoryId, 'product_cat', OBJECT, 'raw');
            return !empty($term) 
                ? $term->name 
                : '';
        }
    }
}