<?php
ob_start();
require_once '../app/Mage.php';
require_once 'createincludes/ArmschmuckGenerator.php';
Mage::run('default','store');
ob_clean();

$productCount = 1000;

$counter = 0 + (int)$_GET['counter'];

$dataGenerator = new RingGenerator(
    Mage::getModel('knm_superattribute/resource_setup', 'knm_superattribute_setup'),
    $productCount,
    $counter
);

$dataGenerator->setSchmuckCategory(array(45));
if ($_GET['ping'] == 1) {
    $dataGenerator->ping();
}

$dataGenerator->generateData();

?>