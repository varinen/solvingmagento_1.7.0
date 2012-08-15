<?php
/**
 *
 * @category    Solvingmagento
 * @package     Solvingmagento_Attributes
 * @copyright   Copyright (c) 2012 Oleg Ishenko
 * @link        http://www.solvingmagento.com/
 * @author      Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * 
 */

class Solvingmagento_Attributes_Model_Resource_Setup extends Mage_Catalog_Model_Resource_Setup
{
    /**
     * Adds data to attribute arrays that share the same data
     * @param array $attributeData array in form ('attribute_code' => array ('label' => 'Attribute Code'))
     * @param array $sharedValues array in form ('attribute_parameter_1' => 'value')
     * @return array
     */
    public function extendAttibuteData($attributeData, $sharedValues)
    {
        foreach ($attributeData as $code => $data) {
            $attributeData[$code] = array_merge($data, $sharedValues);
        }
        
        return $attributeData;
    }
    
    /**
     *
     * @param type $attributeArray
     * @return array 
     */
    public function rebuildAttributeArray($attributeArray) 
    {
        $result = array();
        foreach ($attributeArray as $attribute) {
            foreach ($attribute as $code    =>  $data) {
                $result[$code] = $data;
            }
        }
        
        return $result;
    }
    
}