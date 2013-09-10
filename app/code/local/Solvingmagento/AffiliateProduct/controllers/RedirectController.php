<?php
/**
 * Solvingmagento_AffiliateProduct product redirect controller
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

/** Solvingmagento_AffiliateProduct_RedirectController
 *
 * @category Solvingmagento
 * @package  Solvingmagento_AffiliateProduct
 *
 * @author  Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version Release: <package_version>
 * @link    http://www.solvingmagento.com/
 */
class Solvingmagento_AffiliateProduct_RedirectController extends Mage_Core_Controller_Front_Action
{
    /** @var  Solvingmagento_AffiliateProduct_Helper_Data */
    protected $helper;

    /**
     * Protected construct method
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->helper = Mage::helper('solvingmagento_affiliateproduct');
    }

    /**
     * Make sure the customer is authenticated of necessary
     *
     * @return Mage_Core_Controller_Front_Action | void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $authenticationRequired = (bool) Mage::getStoreConfig(
            Solvingmagento_AffiliateProduct_Model_Product_Type::XML_PATH_AUTHENTICATION
        );

        if ($authenticationRequired) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($customer && $customer->getId()) {
                $validationResult = $customer->validate();
                if ((true !== $validationResult) && is_array($validationResult)) {
                    foreach ($validationResult as $error) {
                        Mage::getSingleton('core/session')->addError($error);
                    }
                    $this->goBack();
                    $this->setFlag('', self::FLAG_NO_DISPATCH, true);

                    return $this;
                }
                return $this;
            } else {
                Mage::getSingleton('customer/session')->addError(
                    $this->helper->__('You must log in to access the partner product')
                );
                $this->_redirect('customer/account/login');
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                return $this;
            }
        }
    }

    /**
     * Redirect to partner link
     *
     * @return void
     */
    public function productAction()
    {
        $productId  = (int) $this->getRequest()->getParam('id');

        $product = Mage::getModel('catalog/product')->load($productId);

        if (($product instanceof Mage_Catalog_Model_Product)
            && ($product->getTypeId() === Solvingmagento_AffiliateProduct_Model_Product_Type::TYPE_AFFILIATE)
        ) {
            if (!Zend_Uri::check($product->getAffiliateLink())) {
                Mage::getSingleton('core/session')->addError(
                    $this->helper->__('The partner product is not accessible.')
                );

                $this->goBack();
                return;
            }

            $this->getResponse()->setRedirect($product->getAffiliateLink());
            return;

        } else {
            Mage::getSingleton('core/session')->addError(
                $this->helper->__('Affiliate product not found')
            );

            $this->goBack();
            return;
        }
    }

    /**
     * Performs a redirect to a previously visited page
     *
     * @return Solvingmagento_AffiliateProduct_RedirectController
     */
    protected function goBack()
    {
        $returnUrl = $this->_getRefererUrl();

        if ($returnUrl) {
            $this->getResponse()->setRedirect($returnUrl);
        } else {
            $this->_redirect('checkout/cart');
        }

        return $this;
    }
}
