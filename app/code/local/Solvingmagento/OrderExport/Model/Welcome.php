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

class Solvingmagento_OrderExport_Model_Welcome 
{
    /**
     * Sends a welcome message to the new customers
     * 
     * @param Mage_Customer_Model_Customer $customer customer object
     * @param Mage_Sales_Model_Order       $order    order object
     * 
     * @return boolean
     */
    public function welcomeCustomer($customer, $order)
    {
        try {
            $storeId = $order->getStoreId();

            $templateId = Mage::getStoreConfig(
                'sales_email/order/welcome_template', 
                $storeId
            );

            $mailer = Mage::getModel('core/email_template_mailer');
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($customer->getEmail(), $customer->getName());

            $mailer->addEmailInfo($emailInfo);

            // Set all required params and send emails
            $mailer->setSender(
                Mage::getStoreConfig(
                    Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, 
                    $storeId
                )
            );
            $mailer->setStoreId($storeId);
            $mailer->setTemplateId($templateId);
            $mailer->setTemplateParams(
                array(
                    'customer'  => $customer
                )
            );
            $mailer->send();
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        } 
        
        return true;

    }
}