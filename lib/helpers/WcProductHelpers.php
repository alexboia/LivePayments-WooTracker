<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\Helpers {
    use WC_Product;
    use WC_Product_Variation;

    class WcProductHelpers {
        public static function getProductNameForTracking(WC_Product $product) {
            return $product->get_title();
        }

        public static function getProductIdForTracking(WC_Product $product) {
            $id = $product->get_sku();
            if (empty($id)) {
                $id = $product->get_id();
            }
            return $id;
        }

        public static function getProductPriceForTracking(WC_Product $product) {
            $price = 0;
            if ($product->get_sale_price() > 0) {
                $price = floatval($product->get_sale_price());
            } else {
                $price = floatval($product->get_price());
            }
            return $price;
        }

        public static function getProductVariantNameforTracking(WC_Product $product) {
            $variant = '';
            if ($product instanceof WC_Product_Variation) {
                $variant = wc_get_formatted_variation($product, true, false, false);
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