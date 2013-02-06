<?php
/**
 * Solvingmagento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  Solvingmagento
 * @package   Solvingmagento
 * @author    Oleg Ishenko <oleg.ishenko@solvingamegnto.com>
 * @copyright 2013 Oleg Ishenko
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.solvingmagento.com
 * 
 */

class Solvingmagento_OrderExport_Model_Export
{
    
    /**
     * Generates an XML file from the order data and places it into
     * the var/export directory
     * 
     * @param Mage_Sales_Model_Order $order order object
     * 
     * @return boolean
     */
    public function exportOrder($order) 
    {
        $dirPath = Mage::getBaseDir('var') . DS . 'export';
        
        //if the export directory does not exist, create it
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        
        $data = $order->getData();
        
        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($data, array ($xml, 'addChild'));
        
        file_put_contents(
            $dirPath. DS .$order->getIncrementId().'xml', 
            $xml->asXML()
        );
        
        return true;
    }
}