<?php
/**
 * Solvingmagento_OneStepCheckout controller class
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

/** Solvingmagento_OneStepCheckout_OnestepController
 *
 * @category Solvingmagento
 * @package  Solvingmagento_OneStepCheckout
 *
 * @author  Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version Release: <package_version>
 * @link    http://www.solvingmagento.com/
 */
$controllerPath = Mage::getBaseDir('code') . DS . 'core' . DS . 'Mage' . DS . 'Checkout' . DS . 'controllers' . DS . 'OnepageController.php';
require_once $controllerPath;

class Solvingmagento_OneStepCheckout_OnestepController extends Mage_Checkout_OnepageController
{
    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        if (!$this->getOnestep()->getQuote()->hasItems()
            || $this->getOnestep()->getQuote()->getHasError()
            || $this->getOnestep()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    /**
     * Get one page checkout model
     *
     * @return Solvingmagento_OneStepCheckout_Model_Type_Onestep
     */
    public function getOnestep()
    {
        return Mage::getSingleton('slvmto_onestepc/type_onestep');
    }

    /**
     * Checkout page
     */
    public function indexAction()
    {
        if (!Mage::helper('slvmto_onestepc')->oneStepCheckoutEnabled()) {
            Mage::getSingleton('checkout/session')
                ->addError($this->__('One Step checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnestep()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                Mage::getStoreConfig('sales/minimum_order/error_message') :
                Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')
            ->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getOnestep()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        $this->renderLayout();
    }

    public function saveMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('checkout_method');
            $result = $this->getOnepage()->saveCheckoutMethod($method);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function updateShippingMethodsAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $post   = $this->getRequest()->getPost();
        $result = array('error' => 1, 'message' => Mage::helper('checkout')->__('Error saving checkout data'));

        if ($post) {

            $billing           = $post['billing'];
            $shipping          = $post['shipping'];
            $usingCase         = isset($billing['use_for_shipping']) ? (int) $billing['use_for_shipping'] : 0;
            $billingAddressId  = isset($post['billing_address_id']) ? (int) $post['billing_address_id'] : false;
            $shippingAddressId = isset($post['shipping_address_id']) ? (int) $post['shipping_address_id'] : false;


            if ($this->saveAddressData($billing, $billingAddressId, 'billing') === false) {
                return;
            }

            if ($usingCase <= 0) {
                if ($this->saveAddressData($shipping, $shippingAddressId, 'shipping') === false) {
                    return;
                }
            }

            $result = array('success' => true);

            /* check quote for virtual */
            if ($this->getOnestep()->getQuote()->isVirtual()) {
                $result['update_step']['shipping_method'] = $this->_getShippingMethodsHtml('none');
            } else {
                $result['update_step']['shipping_method'] = $this->_getShippingMethodsHtml();
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Saves address data
     */
    protected function saveAddressData($data, $addressId, $type)
    {
        $type = strtolower($type);

        if ($type != 'shipping' && $type != 'billing') {
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    array('error' => 1, 'message' => Mage::helper('checkout')->__('Error saving checkout data'))
                )
            );
            return false;
        }
        $method = 'save' . ucwords($type);
        $result = $this->getOnestep()->$method($data, $addressId);

        if (isset($result['error'])) {
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return false;
        }

        return $result;
    }

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data   = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnestep()->saveShippingMethod($data);
            /*
            $result will have error data if shipping method is empty
            */
            if (!isset($result['error'])) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                    array('request'=>$this->getRequest(),
                        'quote'=>$this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                $result['update_step']['payment_method'] = $this->_getPaymentMethodsHtml();
            }
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Get shipping method step html
     *
     * @return string
     */
    protected function _getShippingMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onestep_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    /**
     * Get payment method step html
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }


    public function updatePaymentMethodsAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $post   = $this->getRequest()->getPost();
        $result = array('error' => 1, 'message' => Mage::helper('checkout')->__('Error saving checkout data'));

        if ($post) {

            $billing           = $post['billing'];
            $shipping          = $post['shipping'];
            $usingCase         = isset($billing['use_for_shipping']) ? (int) $billing['use_for_shipping'] : 0;
            $billingAddressId  = isset($post['billing_address_id']) ? (int) $post['billing_address_id'] : false;
            $shippingAddressId = isset($post['shipping_address_id']) ? (int) $post['shipping_address_id'] : false;
            $shippingMethod    = $this->getRequest()->getPost('shipping_method', '');



            if ($this->saveAddressData($billing, $billingAddressId, 'billing') === false) {
                return;
            }

            if ($usingCase <= 0) {
                if ($this->saveAddressData($shipping, $shippingAddressId, 'shipping') === false) {
                    return;
                }
            }

            $result = $this->getOnestep()->saveShippingMethod($shippingMethod);

            if (!isset($result['error'])) {
                $result['update_step']['payment_method'] = $this->_getPaymentMethodsHtml();
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

    }
}
