<?php
/**
 * A Mage_Checkout_Helper_Cart helper extension
 *
 * PHP version 5.3
 *
 * @category Solvingmagento
 * @package  Solvingmagento_AffiliateProduct
 * @author   Magento Core Team <core@magentocommerce.com>
 * @author   Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version  GIT: <0.1.0>
 * @link     http://www.solvingmagento.com/
 *
 */

/** Solvingmagento_AffiliateProduct_Helper_Cart
 *
 * @category Solvingmagento
 * @package  Solvingmagento_AffiliateProduct
 *
 * @author  Magento Core Team <core@magentocommerce.com>
 * @author  Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version Release: <package_version>
 * @link    http://www.solvingmagento.com/
 */
class Solvingmagento_AffiliateProduct_Helper_Cart extends Mage_Checkout_Helper_Cart
{
    /**
     * Retrieve url for add product to cart
     *
     * @param Mage_Catalog_Model_Product $product    product object
     * @param array                      $additional additional route parameters
     *
     * @return string
     */
    public function getAddUrl($product, $additional = array())
    {
        if ($product->getTypeId() === Solvingmagento_AffiliateProduct_Model_Product_Type::TYPE_AFFILIATE) {
            return Mage::helper('solvingmagento_affiliateproduct')->getRedirectUrl($product);
        }

        $continueUrl    = Mage::helper('core')->urlEncode($this->getCurrentUrl());
        $urlParamName   = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;

        $routeParams = array(
            $urlParamName   => $continueUrl,
            'product'       => $product->getEntityId()
        );

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_store_to_url'] = true;
        }

        if ($this->_getRequest()->getRouteName() == 'checkout'
            && $this->_getRequest()->getControllerName() == 'cart') {
            $routeParams['in_cart'] = 1;
        }

        return $this->_getUrl('checkout/cart/add', $routeParams);
    }
}
