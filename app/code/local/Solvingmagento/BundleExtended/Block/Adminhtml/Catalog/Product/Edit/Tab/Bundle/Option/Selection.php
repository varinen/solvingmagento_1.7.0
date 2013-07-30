<?php
class Solvingmagento_BundleExtended_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Selection 
    extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Selection
{
    /**
     * Initialize bundle option selection block
     */
    public function __construct()
    {
        $this->setTemplate('bundleextended/product/edit/bundle/option/selection.phtml');
        $this->setCanReadPrice(true);
        $this->setCanEditPrice(true);
    }
}