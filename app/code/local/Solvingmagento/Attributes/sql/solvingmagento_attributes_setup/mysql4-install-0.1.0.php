<?php
/**
 *
 * @category    Solvingmagento
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
    array('f_wristbandmaterial'   =>  array('label'   =>  'Wristband material',  'input'  => 'select')),
    array('f_wristbandcolor'   =>  array('label'   =>  'Wristband color', 'input'  => 'multiselect')),
    array('f_material'   =>  array('label'   =>  'Material', 'aSet'   => 'schmuck', 'input'  => 'select')),
    array('f_materialcolor'   =>  array('label'   =>  'Material color',  'input'  => 'multiselect')),
    array('f_gem'   =>  array('label'   =>  'Gem',  'input'  => 'select')),
    array('f_pearltype'   =>  array('label'   =>  'Pearl type',   'input'  => 'select')),
    array('f_pearlform'   =>  array('label'   =>  'Pearl form', 'input'  => 'select')),
    array('f_color'   =>  array('label'   =>  'Color',  'input'  => 'multiselect')),
    array('f_cut'   =>  array('label'   =>  'Cut',  'input'  => 'multiselect')),
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

$applyTo = array(
    Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
);

$filterableData = array(
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => true,
    'comparable'                    => true,
    'visible_in_advanced_search'    => true,
    'apply_to'                      => implode(',',$applyTo)
    
);

$nonFilterableData  = array(
    'required'                      => false,
    'user_defined'                  => true,
    'apply_to'                      => implode(',',$applyTo)
);

//set additional options
foreach ($filterable as $key => $attributeData) {
    $filterable[$key] = $this->extendAttibuteData($attributeData, $filterableData);
}

foreach ($nonFilterable as $key => $attributeData) {
    $nonFilterable[$key] = $this->extendAttibuteData($attributeData, $nonFilterableData);
}


//add custom attribute group
foreach ($attributeSets as $setName => $data) {
    $installer->addAttributeGroup('catalog_product', $data['setId'], 'Solvingmagento');
    
    $attributeSets[$setName]['groupId'] = 
        $installer->getAttributeGroup(
            'catalog_product', 
            $data['setId'], 
            'Solvingmagento', 
            'attribute_group_id'
        );
}

//install attributes that are not in 1,2,3 groups
$attributeArray = 
    $this->rebuildAttributeArray(array_merge($filterable ,$nonFilterable));

foreach ($attributeArray as $attributeCode => $attributeData) {
    $installer->addAttribute('catalog_product', $attributeCode, $attributeData);
    $attributeId = $installer->getAttributeId('catalog_product', $attributeCode);
   
    $this->addAttributeToSet(
        'catalog_product', 
        $attributeSets['Trinkets']['setId'], 
        $attributeSets['Trinkets']['groupId'], 
        $attributeId
    );

    
    if (($attributeData['input'] == 'select'))
        $installer->updateAttribute('catalog_product', $attributeCode, 'backend_type', 'int');
}


//add default attributes and their groups to the new sets
$defaultAttributes = array(
    'General'               =>  array(
        'name','sku','weight','status','tax_class_id','url_key','visibility','news_from_date',
        'news_to_date','country_of_manufacture'
        ),
    'Prices'                =>  array(
        'price','group_price','tier_price','special_price','special_from_date','special_to_date',
        'enable_googlecheckout','msrp_enabled','msrp_display_actual_price_type','msrp'
        ),
    'Meta Information'      =>  array(
        'meta_title','meta_keyword','meta_description'
        ),
    'Images'                =>  array(
        'image','small_image', 'gallery','thumbnail','media_gallery'
        ),
    'Description'           =>  array(
        'description','short_description','custom_layout_update'
        ),
    'Design'                =>  array(
        'custom_design','custom_design_from','custom_design_to', 'options_container','price_view',
        'page_layout'
        ),
    'Recurring Profile'     =>  array(
        'is_recurring','recurring_profile' 
        ),
    'Gift Options'          =>  array(
        'gift_message_available'
        )
    );


foreach ($attributeSets as $attributeSet) {
    foreach ($defaultAttributes as $groupName => $attributeGroup) {
        $installer->addAttributeGroup('catalog_product', $attributeSet['setId'], $groupName);
        $groupId =  $installer->getAttributeGroup(
                'catalog_product', 
                $attributeSet['setId'], 
                $groupName,
                'attribute_group_id'
            );
        foreach ($attributeGroup as $attributeCode) {
            $attributeId = $installer->getAttributeId('catalog_product', $attributeCode);
            if ($attributeId > 0) {
                $installer->addAttributeToSet(
                    'catalog_product', 
                    $attributeSet['setId'], 
                    $groupId, 
                    $attributeId
                );
            }
        }
    }
    
}

$attributes = Mage::getModel('eav/entity_attribute')->getResourceCollection()
    ->addFilter('entity_type_id', 10)
    ->addFilter('frontend_input', 'multiselect');

foreach ($attributes as $attribute) {
    $installer->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        $attribute->getAttributeCode(),
        'backend_model',
        'eav/entity_attribute_backend_array'
    );
}
$installer->endSetup();
?>