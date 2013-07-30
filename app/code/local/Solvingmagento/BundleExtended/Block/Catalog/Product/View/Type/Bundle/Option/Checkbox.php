<?php
class Solvingmagento_BundleExtended_Block_Catalog_Product_View_Type_Bundle_Option_Checkbox
    extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Checkbox
{
    /**
     * Set template
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('bundleextended/catalog/product/view/type/bundle/option/checkbox.phtml');
    }
    
    public function getSelectionValues()
    {
     
        $option    = $this->getOption();
        $selections = $option->getSelections();
        
        $result = array();
        
        foreach ($selections as $selection) {
            $result[$selection->getSelectionId()] = array(
                'default_qty'  => $selection->getSelectionQty() * 1,
                'user_defined' => (bool) $selection->getSelectionCanChangeQty()
            );
        }
        
        return $result;
    }
}