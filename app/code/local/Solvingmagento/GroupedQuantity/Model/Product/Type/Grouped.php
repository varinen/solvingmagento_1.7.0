<?php
/**
* Solvingmagento_GroupedQuantity grouped product type override class
*
* PHP version 5.3
*
* @category Solvingmagento
* @package Solvingmagento_GroupedQuantity
* @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
* @copyright 2013 Oleg Ishenko
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @version GIT: <0.1.0>
* @link http://www.solvingmagento.com/
*
*/

/** Solvingmagento_GroupedQuantity_Model_Product_Type_Grouped
*
* @category Solvingmagento
* @package Solvingmagento_GroupedQuantity
*
* @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @version Release: <package_version>
* @link http://www.solvingmagento.com/
*/
class Solvingmagento_GroupedQuantity_Model_Product_Type_Grouped 
    extends Mage_Catalog_Model_Product_Type_Grouped
{
    /**
     * Sets the super_group property generated from the items selected by
     * a customer and quantity data read from grouped products default
     * quantities.
     * 
     * @param Varien_Object              $buyRequest request object
     * @param Mage_Catalog_Model_Product $product    grouped product object
     * 
     * @return void
     */
    protected function setSuperGroup(Varien_Object $buyRequest, $product) 
    {
        $selection = array_keys($buyRequest->getSuperGroupSelection());
        $associatedProducts = $this->getAssociatedProductCollection($product)
            ->load();
        $superGroup = array();
        if (is_array($selection)) {
            foreach($selection as $selected) {
                $item = $associatedProducts->getItemById($selected);
                if ($item && ($item->getQty() > 0)) {
                    $superGroup[$selected] = $item->getQty();
                }
            }
            $buyRequest->setSuperGroup($superGroup);
        }
    }
    
    
    /**
     * Extends the parent function by adding a call to a method that builds
     * the super_group property of the buyRequest from other post parameters.
     * 
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and add logic specific to Grouped product type.
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $product = $this->getProduct($product);
        $this->setSuperGroup($buyRequest, $product);
        $productsInfo = $buyRequest->getSuperGroup();
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

        if (!$isStrictProcessMode || (!empty($productsInfo) && is_array($productsInfo))) {
            $products = array();
            $associatedProductsInfo = array();
            $associatedProducts = $this->getAssociatedProducts($product);
            if ($associatedProducts || !$isStrictProcessMode) {
                foreach ($associatedProducts as $subProduct) {
                    $subProductId = $subProduct->getId();
                    if(isset($productsInfo[$subProductId])) {
                        $qty = $productsInfo[$subProductId];
                        if (!empty($qty) && is_numeric($qty)) {

                            $_result = $subProduct->getTypeInstance(true)
                                ->_prepareProduct($buyRequest, $subProduct, $processMode);
                            if (is_string($_result) && !is_array($_result)) {
                                return $_result;
                            }

                            if (!isset($_result[0])) {
                                return Mage::helper('checkout')->__('Cannot process the item.');
                            }

                            if ($isStrictProcessMode) {
                                $_result[0]->setCartQty($qty);
                                $_result[0]->addCustomOption('product_type', self::TYPE_CODE, $product);
                                $_result[0]->addCustomOption('info_buyRequest',
                                    serialize(array(
                                        'super_product_config' => array(
                                            'product_type'  => self::TYPE_CODE,
                                            'product_id'    => $product->getId()
                                        )
                                    ))
                                );
                                $products[] = $_result[0];
                            } else {
                                $associatedProductsInfo[] = array($subProductId => $qty);
                                $product->addCustomOption('associated_product_' . $subProductId, $qty);
                            }
                        }
                    }
                }
            }

            if (!$isStrictProcessMode || count($associatedProductsInfo)) {
                $product->addCustomOption('product_type', self::TYPE_CODE, $product);
                $product->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));

                $products[] = $product;
            }

            if (count($products)) {
                return $products;
            }
        }

        return Mage::helper('catalog')->__('Please specify the quantity of product(s).');
    }
}
