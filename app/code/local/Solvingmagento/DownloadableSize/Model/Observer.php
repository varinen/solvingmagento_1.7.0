<?php

class Solvingmagento_DownloadableSize_Model_Observer
{
    public function saveFileSize(Varien_Event_Observer $observer)
    {
        $object = $observer->getEvent()->getObject();
        if (($object instanceof Mage_Downloadable_Model_Link)
            || ($object instanceof Mage_Downloadable_Model_Sample)
        ) {

            if ($object->getType()) {
                $this->getFileSize($object, 'link_type', 'link_file', 'link_url', 'filesize');
            }

            if ($object->getSampleType()) {
                $this->getFileSize($object, 'sample_type', 'sample_file', 'sample_url', 'sample_filesize');
            }
        }
        return;
    }

    protected function getFileSize($object, $typeParam, $fileParam, $urlParam, $sizeParam)
    {
        $resourceType = $object->getData($typeParam);

        if ($resourceType == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
            $resource = Mage::helper('downloadable/file')->getFilePath(
                Mage_Downloadable_Model_Link::getBasePath(), $object->getData($fileParam)
            );
        } elseif ($resourceType == Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {
            $resource = $object->getData($urlParam);
        }
        $filesize = Mage::helper('solvingmagento_downloadablesize')->getFileSize($resource, $resourceType);

        $object->setData($sizeParam, $filesize);
    }
}