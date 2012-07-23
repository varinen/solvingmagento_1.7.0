<?php
ob_start();
require_once '../app/Mage.php';
require_once 'createincludes/DataGenerator.php';
Mage::run('german','store');
ob_clean();

$productCount = 10000;

$counter = 0 + (int)$_GET['counter'];

$dataGenerator = new DataGenerator(
    Mage::getModel('knm_superattribute/resource_setup', 'knm_superattribute_setup'),
    $productCount,
    $counter
);

$dataGenerator->setSchmuckCategory(array(35));
$dataGenerator->setUhrenCategory(array(36));

$dataGenerator->generateData();

?>
