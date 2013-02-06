<?php
/**
 * Solvingmagento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  Solvingmagento
 * @package   Solvingmagento
 * @author    Oleg Ishenko <oleg.ishenko@solvingamegnto.com>
 * @copyright 2013 Oleg Ishenko
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.solvingmagento.com
 * 
 */



$installer = $this;

$installer->startSetup();

$installer->run(
    "INSERT INTO `{$this->getTable('core_email_template')}` 
    (`template_code`, `template_text`, `template_type`, `template_subject`)
    VALUES (
        'Welcome New Customer',
        'Hello {{customer.name}},<br><br>Welcome to our shop!',
        '2',
        'Welcome!'
    )"
);


$installer->endSetup();