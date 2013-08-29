<?php
/**
 * Solvingmagento_DownloadableSize helper class
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

/** Solvingmagento_DownloadableSize_Helper_Data
 *
 * @category Solvingmagento
 * @package Solvingmagento_DownloadableSize
 *
 * @author Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version Release: <package_version>
 * @link http://www.solvingmagento.com/
 */
class Solvingmagento_DownloadableSize_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Sets the file size parameter to a link or sample
     *
     * @param Mage_Downloadable_Model_Link | Mage_Downloadable_Model_Sample $object sample or link object
     * @param string $typeParam resource type parameter name
     * @param string $fileParam file parameter name
     * @param string $urlParam  URL parameter name
     * @param string $sizeParam file size parameter name
     *
     * @return void
     */
    public function setFileSize($object, $typeParam, $fileParam, $urlParam, $sizeParam)
    {
        $resourceType = $object->getData($typeParam);

        if ($resourceType == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
            $resource = Mage::helper('downloadable/file')->getFilePath(
                $this->getBasePath($object, $typeParam), $object->getData($fileParam)
            );
        } elseif ($resourceType == Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {
            $resource = $object->getData($urlParam);
        }

        if (!isset($resource)) {
            $filesize = 0;
        } else {
            $filesize = $this->getFileSize($resource, $resourceType);
        }

        $object->setData($sizeParam, $filesize);
    }

    /**
     * Returns file size for a download resource
     *
     * @param string $resource     resource URl or path to the resource
     * @param string $resourceType resource type: file or url
     *
     * @return int
     */
    protected function getFileSize($resource, $resourceType)
    {
        //removing an existing instance of the download helper
        Mage::unregister('_helper/downloadable/download');

        $helper = Mage::helper('downloadable/download');

        $helper->setResource($resource, $resourceType);

        $filesize = 0;

        try {
            $filesize = $helper->getFilesize();
        } catch (Exception $e) {
            Mage::logException($e);

        }

        $filesize = round($filesize / 1024);

        return $filesize;
    }

    /**
     * Returns a path to sample or link files
     *
     * @param Mage_Downloadable_Model_Link | Mage_Downloadable_Model_Sample $object sample or link object
     * @param string $typeParam type parameter
     *
     * @return string
     */
    protected function getBasePath($object, $typeParam)
    {
        $path = '';
        if ($object instanceof Mage_Downloadable_Model_Link) {
            if ($typeParam == 'link_type') {
                $path = Mage_Downloadable_Model_Link::getBasePath();
            } else if ($typeParam == 'sample_type') {
                $path = Mage_Downloadable_Model_Link::getBaseSamplePath();
            }
        } else if ($object instanceof Mage_Downloadable_Model_Sample) {
            $path = Mage_Downloadable_Model_Sample::getBasePath();
        }

        return $path;
    }

    /**
     * Formats file size to unit and locale settings
     *
     * @param int $filesize file size in KB
     *
     * @return string
     */
    public function formatFileSize($filesize)
    {
        $unit = 'KB';

        if ($filesize / 1024 > 1) {
            $filesize = round($filesize / 1024, 2);
            $unit     = 'MB';
        }

        $filesize = Zend_Locale_Format::toNumber(
            $filesize,
            array('locale' => Mage::app()->getLocale()->getLocale())
        );

        $filesize .= ' ' . $unit;

        return $filesize;
    }
}
