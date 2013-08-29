<?php
/**
 * Solvingmagento_DownloadableSize observer class
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

/** Solvingmagento_DownloadableSize_Model_Observer
 *
 * @category Solvingmagento
 * @package Solvingmagento_DownloadableSize
 *
 * @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version Release: <package_version>
 * @link http://www.solvingmagento.com/
 */

class Solvingmagento_DownloadableSize_Model_Observer
{
    /**
     * Sets file size to link and sample files
     *
     * @param Varien_Event_Observer $observer observer object
     *
     * @return void
     */
    public function saveFileSize(Varien_Event_Observer $observer)
    {
        $object = $observer->getEvent()->getObject();
        if (($object instanceof Mage_Downloadable_Model_Link)
            || ($object instanceof Mage_Downloadable_Model_Sample)
        ) {

            if ($object->getLinkType()) {
                Mage::helper('solvingmagento_downloadablesize')
                    ->setFileSize($object, 'link_type', 'link_file', 'link_url', 'filesize');
            }

            if ($object->getSampleType()) {
                Mage::helper('solvingmagento_downloadablesize')
                    ->setFileSize($object, 'sample_type', 'sample_file', 'sample_url', 'sample_filesize');
            }
        }
        return;
    }
}
