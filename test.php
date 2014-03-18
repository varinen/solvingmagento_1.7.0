<?php
require_once 'app/Mage.php';
Mage::app('default');

$order = Mage::getModel('sales/order')->load(29);

$data = $order->getData();

$xml = new SimpleXMLElement('<root/>');

$callback =
    function ($value, $key) use (&$xml, &$callback) {
        if ($value instanceof Varien_Object && is_array($value->getData())) {
            $value = $value->getData();
        }
        if (is_array($value)) {
            array_walk_recursive($value, $callback);
        }
        $xml->addChild($key, (string) $value);
    };

array_walk_recursive($data, $callback);
print_r($xml->asXML());
