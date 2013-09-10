<?php
/**
 * A Mage_Catalog_Helper_Product_Compare helper extension
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

/** Solvingmagento_AffiliateProduct_Helper_Compare
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
class Solvingmagento_AffiliateProduct_Helper_Compare extends Mage_Catalog_Helper_Product_Compare
{
    /**
     * Retrieve add to cart url
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToCartUrl($product)
    {
        if ($product->getTypeId() === Solvingmagento_AffiliateProduct_Model_Product_Type::TYPE_AFFILIATE) {
            return Mage::helper('solvingmagento_affiliateproduct')->getRedirectUrl($product);
        }

        $beforeCompareUrl = Mage::getSingleton('catalog/session')->getBeforeCompareUrl();
        $params = array(
            'product'=>$product->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl)
        );

        return $this->_getUrl('checkout/cart/add', $params);
    }
}
