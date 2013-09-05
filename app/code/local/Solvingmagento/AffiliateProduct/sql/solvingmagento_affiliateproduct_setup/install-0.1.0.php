<?php
/**
 * Solvingmagento_AffiliateProduct install script
 *
 * PHP version 5.3
 *
 * @category Solvingmagento
 * @package Solvingmagento_AffiliateProduct
 * @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @copyright 2013 Oleg Ishenko
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version GIT: <0.1.0>
 * @link http://www.solvingmagento.com/
 *
 */


/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Add attributes to the eav/attribute table
 */
$installer->addAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'affiliate_link',
    array(
        'type'                    => 'text',
        'backend'                 => '',
        'frontend'                => '',
        'label'                   => 'Affiliate Link',
        'input'                   => 'text',
        'class'                   => '',
        'source'                  => '',
        'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'                 => true,
        'required'                => true,
        'user_defined'            => false,
        'default'                 => '',
        'searchable'              => false,
        'filterable'              => false,
        'comparable'              => false,
        'visible_on_front'        => false,
        'unique'                  => false,
        'apply_to'                => 'affiliate',
        'is_configurable'         => false,
        'used_in_product_listing' => false
    )
);

$defaultSetId = $installer->getAttributeSetId('catalog_product', 'default');

$installer->addAttributeGroup(
    'catalog_product',
    $defaultSetId,
    'Affiliate Information'
);

//find out the id of the new group
$groupId = $installer->getAttributeGroup(
    'catalog_product',
    $defaultSetId,
    'Affiliate Information',
    'attribute_group_id'
);

$attributeId = $installer->getAttributeId(
    'catalog_product',
    'affiliate_link'
);
//assign the attribtue to the group and set
if ($attributeId > 0) {
    $installer->addAttributeToSet(
        'catalog_product',
        $defaultSetId,
        $groupId,
        $attributeId
    );
}

$installer->endSetup();