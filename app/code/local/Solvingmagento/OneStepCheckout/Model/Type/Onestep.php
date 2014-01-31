<?php
/**
 * Solvingmagento_OneStepCheckout helper class
 *
 * PHP version 5.3
 *
 * @category  Solvingmagento
 * @package   Solvingmagento_OneStepCheckout
 * @author    Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @copyright 2014 Oleg Ishenko
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: <0.1.0>
 * @link      http://www.solvingmagento.com/
 *
 */

/** Solvingmagento_OneStepCheckout_Helper_Data
 *
 * @category Solvingmagento
 * @package  Solvingmagento_OneStepCheckout
 *
 * @author  Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version Release: <package_version>
 * @link    http://www.solvingmagento.com/
 */
class Solvingmagento_OneStepCheckout_Model_Type_Onestep extends Mage_Checkout_Model_Type_Onepage
{
    /**
     * Error message of "customer already exists"
     *
     * @var string
     */
    private $_customerEmailExistsMessage = '';

    /**
     * Class constructor
     * Set customer already exists message
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('slvmto_onestepc');
        $this->_customerEmailExistsMessage = Mage::helper('checkout')->__(
            'There is already a customer registered using this email address. ' .
            'Please login using this email address or enter a different email ' .
            'address to register your account.'
        );
        $this->_checkoutSession = Mage::getSingleton('checkout/session');
        $this->_customerSession = Mage::getSingleton('customer/session');
    }

    /**
     * Initialize quote state to be valid for one page checkout
     *
     * @return Solvingmagento_OneStepCheckout_Model_Type_Onestep
     */
    public function initCheckout()
    {
        $checkout        = $this->getCheckout();
        $customerSession = $this->getCustomerSession();
       /**
         * Reset multishipping flag before any manipulations with quote address
         * addAddress method for quote object related on this flag
         */
        if ($this->getQuote()->getIsMultiShipping()) {
            $this->getQuote()->setIsMultiShipping(false);
            $this->getQuote()->save();
        }

        /*
        * Load the correct customer information by assigning to address
        * instead of just loading from sales/quote_address
        */
        $customer = $customerSession->getCustomer();
        if ($customer) {
            $this->getQuote()->assignCustomer($customer);
        }
        return $this;
    }

}