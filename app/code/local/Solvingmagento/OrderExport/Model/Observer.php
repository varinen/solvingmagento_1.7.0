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

class Solvingmagento_OrderExport_Model_Observer
{
    
    /**
     * Exports an order after it is placed
     * 
     * @param Varien_Event_Observer $observer observer object 
     * 
     * @return boolean
     */
    public function exportOrder(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        
        Mage::getModel('solvingmagento_orderexport/export')
            ->exportOrder($order);
        
        return true;
        
    }
    
    /**
     * Sends a welcome email to the customer after he places his first order
     * online
     * 
     * @param Varien_Event_Observer $observer observer object
     * 
     * @return boolean
     */
    public function welcomeCustomer(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        
        if (!$order->getCustomerId()) {
            //send welcome message only to registered customers
            return;
        }
        
        $customer = $order->getCustomer();
        
        $customerOrders = Mage::getModel('sales/order')
                ->getCollection()
                ->addAttributToFilter('customer_id', $customer->getId());
        if (count($customerOrders) > 1) {
            // send welcome message only after the first order
            return;
        }
        
        return Mage::getModel('solvingmagento_orderexport/welcome')
            ->welcomeCustomer($customer, $order);
        
    }
}