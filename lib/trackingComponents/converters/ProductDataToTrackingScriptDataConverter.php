<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

namespace LivepaymentsWootracker\TrackingComponents\Converters {
    use WC_Product;
    use WC_Product_Grouped;
    use WC_Product_Simple;
	use LivepaymentsWootracker\Helpers\WcProductHelpers;
    use WC_Product_Variable;
    use WC_Product_Variation;

class ProductDataToTrackingScriptDataConverter {
        private $_productToConvert;

        public function __construct(WC_Product $product) {
            $this->_productToConvert = $product;
        }

        public static function forProduct($product) {
            return new self(wc_get_product($product));
        }

        public function getProductTrackingData() {
            $trackingData = new \stdClass();
            $itemsTrackingData = array();

            if ($this->_productToConvert instanceof WC_Product_Grouped) {
               $childrenIds = $this->_productToConvert->get_children();
               foreach ($childrenIds as $childId) {
                   $groupItemProduct = wc_get_product($childId);
                   $itemsTrackingData[] = $this->_getProductTrackingData($groupItemProduct);
               }
            } else {
               $itemsTrackingData[] = $this->_getProductTrackingData($this->_productToConvert);
            }

			$trackingData->items = $itemsTrackingData;
           	return $trackingData;
        }

		public function getProductTrackingSupportData() {
			$supportData = new \stdClass();
			$supportData->variationMapping = array();

			if ($this->_productToConvert instanceof WC_Product_Variable) {
				$childrenIds = $this->_productToConvert->get_children();
               foreach ($childrenIds as $childId) {
                   $variationProduct = wc_get_product($childId);
				   $supportData->variationMapping[$variationProduct->get_id()] = array(
					   'id' => WcProductHelpers::getProductIdForTracking($variationProduct),
					   'price' => WcProductHelpers::getProductPriceForTracking($variationProduct)
				   );
               }
			}

			return $supportData;
		}

        private function _getProductTrackingData(WC_Product $product) {
            $productTrackingData = new \stdClass();

            $productTrackingData->id = $product->get_sku();
            if (empty($productTrackingData->id)) {
                $productTrackingData->id = $product->get_id();
            }

            $productTrackingData->name = WcProductHelpers::getProductNameForTracking($product);
            $productTrackingData->category = WcProductHelpers::getProductCategoryNameForTracking($product);
            $productTrackingData->variant = WcProductHelpers::getProductVariantNameforTracking($product);

            $productTrackingData->quantity = 1;
			$productTrackingData->price = WcProductHelpers::getProductPriceForTracking($product);

            return $productTrackingData;
        }
    }
}