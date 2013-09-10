<?php
/**
 * Solvingmagento_AffiliateProduct product type class
 *
 * PHP version 5.3
 *
 * @category  Solvingmagento
 * @package   Solvingmagento_AffiliateProduct
 * @author    Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @copyright 2013 Oleg Ishenko
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: <0.1.0>
 * @link      http://www.solvingmagento.com/
 *
 */

/** Solvingmagento_AffiliateProduct_Model_Product_Type
 *
 * @category Solvingmagento
 * @package  Solvingmagento_AffiliateProduct
 *
 * @author  Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version Release: <package_version>
 * @link    http://www.solvingmagento.com/
 */
class Solvingmagento_AffiliateProduct_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Virtual
{
    const TYPE_AFFILIATE          = 'affiliate';
    const XML_PATH_AUTHENTICATION = 'catalog/affiliate/authentication';

    /**
     * Processes the product and its options before adding it to a quote or a wishlist
     *
     * @param Varien_Object              $buyRequest  request object
     * @param Mage_Catalog_Model_Product $product     product ibject
     * @param string                     $processMode process mode: strict for cart, lite for wishlist
     *
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        if ($this->_isStrictProcessMode($processMode)) {
            return Mage::helper('solvingmagento_affiliateproduct')->__(
                'Affiliate product %s cannot be added to cart. ' .
                ' On the product detail page click the "Go to parent site" button to access the product.',
                $product->getName()
            );
        }
        return parent::_prepareProduct($buyRequest, $product, $processMode);
    }
}
