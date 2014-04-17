<?php
/**
 * Solvingmagento_GroupedRedirect observer class
 *
 * PHP version 5.3
 *
 * @category Solvingmagento
 * @package Solvingmagento_GroupedRedirect
 * @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @copyright 2014 Oleg Ishenko
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version GIT: <0.1.0>
 * @link http://www.solvingmagento.com/
 *
 */

/** Solvingmagento_GroupedRedirect_Model_Observer
 *
 * @category Solvingmagento
 * @package Solvingmagento_GroupedRedirect
 *
 * @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version Release: <package_version>
 * @link http://www.solvingmagento.com/
 */
class Solvingmagento_GroupedRedirect_Model_Observer
{
    public function redirectGrouped(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $groupedTypeInstance = Mage::getModel('catalog/product_type_grouped');
        $parentIds = $groupedTypeInstance->getParentIdsByChild($product->getId());

        foreach ($parentIds as $parentId) {
            $parent = Mage::getModel('catalog/product')->load($parentId);
            if ($parent
                && $parent instanceof Mage_Catalog_Model_Product
                && $parent->getTypeId() == 'grouped'
            ) {
                $redirect = Mage::getStoreConfig(
                    'catalog/grouped_options/redirect_enabled',
                    Mage::app()->getStore()->getId()
                );
                if ($redirect) {
                    Mage::app()->getResponse()->setRedirect($parent->getProductUrl());
                }
                break;
            }
        }
    }
}