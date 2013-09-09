<?php
/**
 * Solvingmagento_AffiliateProduct observer class
 *
 * PHP version 5.3
 *
 * @category Solvingmagento
 * @package Solvingmagento_AffiliateProduct
 * @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @copyright 2013 Oleg Ishenko
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version GIT: <0.1.0>
 * @link http://www.solvingmagento.com/
 *
 */

/** Solvingmagento_AffiliateProduct_Model_Observer
 *
 * @category Solvingmagento
 * @package Solvingmagento_AffiliateProduct
 *
 * @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version Release: <package_version>
 * @link http://www.solvingmagento.com/
 */
class Solvingmagento_AffiliateProduct_Model_Observer
{
    /**
     * Sets affiliate-specific block templates where necessary
     *
     * @param Varien_Event_Observer $observer observer object
     *
     * @return void
     */
    public function setTemplate(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Cart) {
            $product = $block->getItem()->getProduct();

            if ($product->gettypeId() != Solvingmagento_AffiliateProduct_Model_Product_Type::TYPE_AFFILIATE) {
                return;
            } else {
                $block->setTemplate('solvingmagento/affiliate/wishlist/item/column/cart.phtml');
            }
        }
    }
}