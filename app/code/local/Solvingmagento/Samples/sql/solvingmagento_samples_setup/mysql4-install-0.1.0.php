<?php
/**
 * Installer script
 * 
 * @category  Solvingmagento
 * @package   Solvingmagento_Samples
 * @author    Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @copyright Copyright (c) 2012 - 2013 Oleg Ishenko
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version   GIT: <0.1.0>
 * @link      http://www.solvingmagento.com/
 
 * @var $installer Mage_Eav_Model_Entity_Setup 
 */

$installer = $this;

$installer->startSetup();

$mySetName = 'MySet';

//install sets
$attributeSets = array(
     $mySetName  => array ('setId' => -1)
);

$attributes = array(
    'General Attributes'    =>  array(
        'name', 'sku', 'weight', 'status', 'tax_class_id', 'url_key', 
        'visibility', 'news_from_date', 'news_to_date', 'country_of_manufacture'
    ),
    'Product Descriptions'  => array('description', 'short_description')
    
);

foreach ($attributeSets as $setName => $setId) {
    
    //create my set
    $installer->addAttributeSet('catalog_product', $setName);
    
    $attributeSets[$setName]['setId'] = 
        $installer->getAttributeSetId('catalog_product', $setName);
    
    
    foreach ($attributes as $groupName => $groupedAttributes) {
        //add the attribute groups
        $installer->addAttributeGroup(
            'catalog_product', 
            $attributeSets[$setName]['setId'], 
            $groupName
        );
        //find out the id of the new group
        $groupId =  $installer->getAttributeGroup(
            'catalog_product', 
            $attributeSets[$setName]['setId'], 
            $groupName,
            'attribute_group_id'
        );
        
        //for each attribute in a group
        foreach ($groupedAttributes as $attributeCode) {
            $attributeId = $installer->getAttributeId(
                'catalog_product', 
                $attributeCode
            );
            //assign the attribtue to the group and set
            if ($attributeId > 0) {
                $installer->addAttributeToSet(
                    'catalog_product', 
                    $attributeSets[$setName]['setId'], 
                    $groupId, 
                    $attributeId
                );
            }
        }
    }
}

$installer->endSetup();