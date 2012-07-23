<?php
/**
 *
 * @category    Knm
 * @package     Solvingmagento_Attributes
 * @copyright   Copyright (c) 2012 Oleg Ishenko
 * @link        http://www.solvingmagento.com/
 * @author      Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @var $installer Solvingamegnto_Attributes_Model_Resource_Setup 
 */


$installer = $this;

$installer->startSetup();

//install sets
$attributeSets = array('Trinkets' => array ('setId' => -1, 'groupId'  => -1));

$installer->addAttributeSet('catalog_product', 'Trinkets');

$attributeSets['Trinkets']['setId'] = $installer->getAttributeSetId('catalog_product', 'Trinkets');


// the f_ prefix in the attribute code plays no functional role other than making it easier to spot
// our specific attribute in the attribute list in backend.

//filterable attributes, non-grouped
$filterable = array(
    array('f_brand' => array('label'   => 'Brand', 'input'  => 'multiselect')),
    array('f_suitable_for' => array('label'   => 'Suitable for', 'input'  => 'multiselect')),
    array('f_form' => array('label'   => 'Form', 'input'  => 'multiselect')),
    array('f_type' => array('label'   => 'Type', 'input'  => 'multiselect')),
    array('f_chaintype' => array('label'   => 'Chain type', 'input'  => 'multiselect')),
    array('f_gender' => array('label'   => 'Geschlecht', 'input'  => 'multiselect')),
    array('f_caseform' => array('label'   => 'Case form', 'input'  => 'multiselect')),
    array('f_glass' => array('label'   => 'Glass', 'input'  => 'select')),
    array('f_movement' => array('label'   => 'Movement', 'input'  => 'select')),
    array('f_watchtype' => array('label'   => 'Watch type', 'input'  => 'multiselect')),
    array('f_waterresistance' => array('label'   => 'Water resistance', 'input'  => 'select')),
    array('f_engraving' => array('label'   => 'Engraving available', 'input'  => 'select')),
    array('f_chainlength' => array('label'   => 'Chain length', 'input'  => 'select')),
    array('f_ringsize' => array('label'   => 'Ring size', 'input'  => 'select')),
);


//non-filterable
$nonFilterable = array(
    array('f_casingmaterial' =>  array('label'   => 'Casing material', 'input'  => 'multiselect')),
    array('f_digits' =>  array('label'   => 'Digits', 'input'  => 'multiselect')),
    array('f_lunette' =>  array('label'   => 'Lunette', 'input'  => 'multiselect')),
    array('f_lock' =>  array('label'   => 'Lock', 'input'  => 'select')),
    array('f_cetrificate' =>  array('label'   => 'Certificate available', 'input'  => 'text'))
);