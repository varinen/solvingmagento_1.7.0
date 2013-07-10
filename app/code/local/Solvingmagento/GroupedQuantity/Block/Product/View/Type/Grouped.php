<?php
/**
* Solvingmagento_GroupedQuantity block class
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

/** Solvingmagento_GroupedQuantity_Block_Product_View_Type_Grouped
*
* @category Solvingmagento
* @package Solvingmagento_GroupedQuantity
*
* @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @version Release: <package_version>
* @link http://www.solvingmagento.com/
*/

class Solvingmagento_GroupedQuantity_Block_Product_View_Type_Grouped 
    extends Mage_Catalog_Block_Product_View_Type_Grouped
{
    
    /**
     * Returns default quantity of a grouped item 
     * 
     * @param Mage_Catalog_Model_Product $item grouped item object
     * 
     * @return string
     */
    public function getItemQuantity(Mage_Catalog_Model_Product $item)
    {
        $qty = ceil($item->getQty());
        if (($item->getTypeInstance(true, $item)->canUseQtyDecimals()) 
            && ($item->getQty() != ceil($item->getQty()))
        ) {
           $qty = Zend_Locale_Format::toNumber(
                $item->getQty(), 
                array('locale' => Mage::app()->getLocale()->getLocale())
           );
        }
        
        return (string) $qty;
    }
}