<?php
class Solvingmagento_DownloadableSize_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Returns file size for a download resource
     *
     * @param string $resource     resource URl or path to the resource
     * @param string $resourceType resource type: file or url
     *
     * @return int
     */
    public function getFileSize($resource, $resourceType)
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
}