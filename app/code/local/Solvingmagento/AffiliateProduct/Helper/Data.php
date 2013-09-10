<?php
/**
 * Solvingmagento_AffiliateProduct helper class
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

/** Solvingmagento_AffiliateProduct_Helper_Data
 *
 * @category Solvingmagento
 * @package  Solvingmagento_AffiliateProduct
 *
 * @author  Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version Release: <package_version>
 * @link    http://www.solvingmagento.com/
 */
class Solvingmagento_AffiliateProduct_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getRedirectUrl(Mage_Catalog_Model_Product $product)
    {
        return Mage::getUrl('affiliate/redirect/product', array('id' => $product->getId()));
    }
}
