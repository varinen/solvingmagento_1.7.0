<?php
/**
 * Solvingmagento_DownloadableSize install script
 *
 * PHP version 5.3
 *
 * @category Solvingmagento
 * @package Solvingmagento_DownloadableSize
 * @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @copyright 2013 Oleg Ishenko
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version GIT: <0.1.0>
 * @link http://www.solvingmagento.com/
 *
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

$connection->addColumn(
    $installer->getTable('downloadable/link'),
    'filesize',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => false,
        'comment'   => 'File size in KB'
    )
);

$connection->addColumn(
    $installer->getTable('downloadable/link'),
    'sample_filesize',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false,
        'comment'  => 'Sample link file size in KB'
    )
);


$connection->addColumn(
    $installer->getTable('downloadable/sample'),
    'sample_filesize',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false,
        'comment'  => 'Sample file size in KB'
    )
);

$installer->endSetup();
