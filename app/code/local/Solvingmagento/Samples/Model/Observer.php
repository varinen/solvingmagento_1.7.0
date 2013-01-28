<?php
class Solvingmagento_Samples_Model_Order
{
    public function catchOrderPlaceAfter(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        Mage::log('Order ID '.$order->GetIncrementId());
    }
}
