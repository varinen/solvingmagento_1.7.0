<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

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
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => false,
        'comment'   => 'Sample link file size in KB'
    )
);


$connection->addColumn(
    $installer->getTable('downloadable/sample'),
    'sample_filesize',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => false,
        'comment'   => 'Sample file size in KB'
    )
);

$installer->endSetup();
