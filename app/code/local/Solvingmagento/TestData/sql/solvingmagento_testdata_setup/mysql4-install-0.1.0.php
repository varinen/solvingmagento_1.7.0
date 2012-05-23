<?php
/**
 *
 * @category    Solvingmagento
 * @package     Solvingmagento_TestData
 * @copyright   Oleg Ishenko
 * @link        http://www.solvingmagento.com/
 * @author      Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @var $installer Mage_Catalog_Model_Resource_Setup 
 */

$installer = $this;

$installer->startSetup();

//install sets
$attributeSets = array(
    'Configurable Components'   => array ('setId' => -1, 'groupId'  => -1),
);

foreach ($attributeSets as $setName => $setId) {
    $installer->addAttributeSet('catalog_product', $setName);
    $attributeSets[$setName]['setId'] = $installer->getAttributeSetId('catalog_product', $setName);
}


//filterable attributes
$filterable = array(
    array('brand' => array('label'   => 'Brand', 'input'  => 'select')),
    array('suitable_for' => array('label'   => 'Suitable for', 'input'  => 'multiselect')),
    array('form' => array('label'   => 'Form',  'input'  => 'multiselect')),
    array('junk_type' => array('label'   => 'Type', 'input'  => 'multiselect')),
    array('parts' => array('label'   => 'Parts', 'input'  => 'multiselect')),
    array('gender' => array('label'   => 'Gender',  'input'  => 'multiselect')),
    array('case_form' => array('label'   => 'Case form',  'input'  => 'select')),
    array('glass_cover' => array('label'   => 'Glass cover', 'input'  => 'select')),
    array('mechanics' => array('label'   => 'Mechanics',  'input'  => 'select')),
    array('water_resistant' => array('label'   => 'Water resistant',  'input'  => 'select')),
    array('engraving' => array('label'   => 'Engraving available',  'input'  => 'select')),
    array('size' => array('label'   => 'Size', 'input'  => 'select')),
);

//non-filterable, non-grouped
$nonFilterable = array(
    array('model' =>  array('label'   => 'Model', 'input'  => 'text')),
    array('model_year' =>  array('label'   => 'Model year',  'input'  => 'multiselect')),
    array('wristband_width' =>  array('label'   => 'Wristband width', 'input'  => 'text')),
    array('wristband_material' =>  array('label'   => 'Wristband material', 'input'  => 'text')),
    array('wristband_length' =>  array('label'   => 'GehÃ¤usematerial', 'aSet'  => 'uhren', 'input'  => 'multiselect')),
    array('case_diameter' =>  array('label'   => 'Case diameter', 'aSet'  => 'uhren', 'input'  => 'text')),
    array('case_height' =>  array('label'   => 'Case_height',  'input'  => 'text')),
    array('certificate' =>  array('label'   => 'Certificate', 'input'  => 'text')),
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

foreach ($nonFilterable as $key =>$attributeData) {
    $nonFilterable[$key] = $this->extendAttibuteData($attributeData, $nonFilterableData);
}

//add Solvingamgento attribute group
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


//install attributes 
$attributesArray = 
    $this->rebuildAttributeArray(array_merge($filterable, $nonFilterable));

foreach ($attributesArray as $attributeCode => $attributeData) {
    $installer->addAttribute('catalog_product', $attributeCode, $attributeData);
    $attributeId = $installer->getAttributeId('catalog_product', $attributeCode);
    $installer->addAttributeToFaszinataSet($attributeId, $attributeSets['Configurable Components']);
    
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


$installer->endSetup();
?>
