<?php
/**
 *
 * @category    Solvingmagento
 * @package     Solvingmagento_TestData
 * @copyright   Oleg Ishenko
 * @link        http://www.solvingmagento.com/
 * @author      Oleg Ishenko <oleg.ishenko@solvingmagento.com>
 * @var $installer Mage_Catalog_Model_Resource_Setup 
 */

class Solvingmagento_TestData_Model_Resource_Setup extends Mage_Catalog_Model_Resource_Setup
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
     * Sets a new value for a certain parameter in data arrays of every member 
     * of the attributeData array
     * @param array $attributeData array in form ('attribute_code' => array ('label' => 'Attribute Code'))
     * @param array $resetData array in form ('attribute_parameter_1', 'value')
     * @return array
     */
    public function resetValue($attributeData, $resetData)
    {
        foreach ($attributeData as $code => $data) {
            if (key_exists($resetData[0], $data)) {
                $data[$resetData[0]] = $resetData[1];
                $attributeData[$code] = $data; //redundant?
            }
        }
        
        return $attributeData;
    }
    
    /**
     * Sets a new value for a certain parameter in data arrays of every member 
     * of the attributeData array
     * 
     * @param array $attributeData array in form ('attribute_code' => array ('label' => 'Attribute Code'))
     * @param array $resetData array in form ('attribute_parameter_1', 'value')
     * @return array
     */
    public function setValue($attributeData, $setData)
    {
        foreach ($attributeData as $code => $data) {
            $data[$setData[0]] = $setData[1];
            $attributeData[$code] = $data; //redundant?
        }
        
        return $attributeData;
    }
    
    /**
     *
     * @param type $attributeId
     * @param type $setData
     * @return \Knm_Superattribute_Model_Resource_Setup 
     */
    public function addAttributeToFaszinataSet($attributeId, $setData)
    {
        $this->addAttributeToSet(
                'catalog_product', 
                $setData['setId'], 
                $setData['groupId'], 
                $attributeId);
        return $this;
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
    
    /**
     *
     * @param type $code
     * @param type $values
     * @param type $index 
     */
    public function addAttributeOptions($code, $values, $index = 0)
    {
        if ($index > 0) {
            $suffix = '_'.$index;
        } else {
            $suffix = '';
        }
        
        $attributeId =   $this->getAttributeId('catalog_product', ($code.$suffix));
        
        if ($attributeId > 0) {
            $this->addAttributeOption(
                array('attribute_id' => $attributeId, 'values' => explode(',',$values))
            );
        }
    }
}
?>
