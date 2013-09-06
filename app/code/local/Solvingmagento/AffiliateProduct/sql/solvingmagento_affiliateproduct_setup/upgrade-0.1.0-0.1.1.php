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

$attributes = array(
        'price',
        'special_price',
        'special_from_date',
        'special_to_date',
        'minimal_price',
        'tax_class_id'
    );

foreach ($attributes as $attributeCode) {

    $applyTo = explode(',', $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode, 'apply_to'));

    if (!in_array('affiliate', $applyTo)) {
        $applyTo[] = 'affiliate';
        $installer->updateAttribute(
            Mage_Catalog_Model_Product::ENTITY,
            $attributeCode,
            'apply_to',
            join(',', $applyTo)
        );
    }
}

$installer->endSetup();
