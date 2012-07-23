<?php
require_once 'DataGenerator.php';

class RingGenerator extends DataGenerator  
{
    protected $_configurables;
    protected $_maxSizeOptions;
    
    
    public function generateData()
    {
        $counter = $this->getCounter();
        while ($counter < ($this->getProductCount() +$this->getCounter())) {
            $set = 'Schmuck';
            $counter  = $this->productCreate($counter, $set, 'configurable');
            $counter++;
        }
        echo count($this->products).' product generated!';
        $this->_writeCsv();
    }
    
    function productCreate($counter, $set, $type, $sku = false, $art = false, 
        $productType = false, $gender = false, $parentSku = '', $marke = '') {
        
       
        
        $productType = array(
            'type'  =>  'Halskette',
            'set'   =>  'Schmuck',
            'exclude'   => array(
                'f_geeignet_fuer', 'f_form', 'f_typ','f_teile', 'f_anzahl_der_teile', 
                'f_groesse'
            ),
            'art'   => 'Halsreife,Herrenketten,Kette mit Anhänger,Kette ohne Anhänger,Königsketten,'.
                'Kugelketten,Lederketten,Panzerketten,Perlenketten,Singapurketten,Verlängerungsketten,'.
                'weiterer Halsschmuck'

        );
        
        $product = new Varien_Object();
        if (!$sku)
            $sku    = "chain_sku_{$counter}";
        
        $configurableAttributes = array();
            
         if ($set == 'Schmuck') {
            if ($art) {
                $artValue = $art;
            } else if (isset($productType['art'])) {
                $artOptions = explode(',',$productType['art']);
                $artValue = $artOptions[rand(0, count($artOptions)-1)];
            } else 
                $artValue = null;
            $productAttributes = 
                $this->_getAttributeValues($this->schmuckFields, $type, $exclude, $parentSku);
            if (!$productAttributes)
                return false;
            if ($gender) {
                $productAttributes['f_gender']['value'] = $gender;
            }
            if (strlen($marke) > 0) {
                $productAttributes['f_marke']['value'] = $marke;
            }
            $productAttributes['f_art']['value'] = $artValue;
            
            
            if ($type == 'configurable') {
                $configurableAttributes = array('f_variante', 'f_laenge');
            }
            
            
            
            
        }
        
        if ($type == 'configurable') {
            $count_simples = rand(10, 30);
            $simple_skus = array();
            $i = 1;
            for ($i; $i <= $count_simples; $i++) {
                $simple_sku = "{$sku}_{$i}";
                $simple_skus[] = $simple_sku;
                $this->productCreate(
                    $i, 
                    $set, 
                    'simple', 
                    $simple_sku, 
                    $productAttributes['f_art']['value'],
                    $productType,
                    $productAttributes['f_gender']['value'],
                    "{$sku}_cfg",
                    $productAttributes['f_marke']['value']    
                );
            }

            $counter += $i;
            $simples_skus = implode(',', $simple_skus);
            $productAttributes['simples_skus']['value'] = $simples_skus;
            $sku = "{$sku}_cfg";
            //$productAttributes['f_variante']['value'] = '';
            //$productAttributes['f_groesse']['value'] = '';
            $this->_configurables[$sku] = array();
        } else {
            $productAttributes['simples_skus']['value'] = '';
        }
        $productAttributes['configurable_attributes']['value'] = 
            implode(',', $configurableAttributes);
        
        $name = $productType['type'].' für '.$productAttributes['f_gender']['value'].',  '.
                $productAttributes['f_art']['value'].' von '.
                $productAttributes['f_marke']['value'].'  - '.
                $productAttributes['f_variante']['value'].' '.
                $productAttributes['f_laenge']['value'].' '.$type.' '.$counter;
        
        $productAttributes['name']['value'] = $name;
        $productAttributes['type']['value'] = $type;
        $productAttributes['qty']['value'] = 1000;
        $productAttributes['store']['value'] = 'admin';
        $productAttributes['websites']['value'] = 'base';
        $productAttributes['attribute_set']['value'] = $set;
        $productAttributes['sku']['value'] = $sku;
        $productAttributes['category_ids']['value'] = 
            implode(',',$this->getData(strtolower($set).'_category'));
        
        
    
        $product->setAttributes($productAttributes);
        
        $product = $this->_addImages($product);
   
        $this->products[] = $product;
        if (strlen($parentSku) > 0) {
            $this->_configurables[$parentSku][] = $product;
        }
        return $counter;
    }
    
    protected function _getAttributeValues($attributes, $type, $exclude, $parentSku = '') 
    {
        if (!is_array($exclude)) { $exclude = array();}
        
        
        foreach ($attributes as $attributeCode => $attributeData) {
            $attributeValue = null;
            if (in_array($attributeCode, $exclude)) {
                continue;
            }
            if (in_array($attributeCode, $this->_defaultAttributes)) {
                switch ($attributeCode) {
                    case 'price': $attributeValue = rand(0, 100).'.99';
                        break;
                    case 'description': $parts = rand(0,4);
                        for ($i = 0; $i <= $parts; $i++) {
                            $attributeValue .= "<p>$this->loremIpsum</p>";
                        }
                        break;
                    case 'short_description': $attributeValue = substr($this->loremIpsum, 0, 50).
                        '...';
                        break;
                    case 'weight': $attributeValue = rand (0, 1000);
                        break;
                    case 'status': $attributeValue = 1;
                        break;
                    case 'options_container': $attributeValue = '';
                        break;
                    case 'has_options': if ($type == 'configurable') $attributeValue = 1;
                        else $attributeValue = 0;
                        break;
                    case 'is_in_stock': $attributeValue = 1;
                        break;
                    case 'tax_class_id': $attributeValue = 1;
                        break;
                    case 'visibility': if ($type == 'configurable') $attributeValue = 4;
                        else $attributeValue = 1;
                }
            }
            if (count($attributeData['options'])>0) {
                $attributeValue = 
                    $attributeData['options'][rand(0, count($attributeData['options'])-1)];
            }
            
            $attributeData['value'] = $attributeValue;
            $attributes[$attributeCode] = $attributeData;
        }
        if ($type == 'simple')
                $attributes = $this->_getVariante($attributes, $parentSku);
            if (!$attributes)
                return false;
        return $attributes;
    }
    /**
     * a simple product may not have the same variant and the same size as its siblings.
     * Theme may be no more products with the same variant as 5;
     * @param type $attributes
     * @param type $parentSku
     * @return string|boolean 
     */
    protected function _getVariante($attributes, $parentSku) 
    {
        $siblings = $this->_configurables[$parentSku];
        $seedData = 
            $this->_getAvailableVariantsAndSizes($siblings, $attributes['f_laenge']['options']);
        if (sizeof($seedData['variants']) > 0) {
            $variantOption = $seedData['variants'][(rand(0, count($seedData['variants'])-1))];
            $variant = $variantOption['variant'];
            sort($variantOption['sizes']);
            $sizeInd = rand(0, count($variantOption['sizes'])-1);
            $size = $variantOption['sizes'][$sizeInd];
            $materials = $seedData['materials'][$variant];
            
            $attributes['f_variante']['value'] = $variant;
            $attributes['f_laenge']['value'] = $size;
            $attributes['f_material_1']['value'] = $materials['material'];
            $attributes['f_oberflaechenverarbeitung_1']['value'] = $materials['ov'];
            $attributes['f_edelstein_1']['value'] = $materials['edelstein'];
            
        } else {
            $attributes = $this->_getSeedVariantValues($attributes);
        }
        if ((int)$attributes['f_laenge']['value'] <= 0) {
            $break = 0;
        }
       return $attributes;
    }
    
    protected function _getAvailableVariantsAndSizes($siblings, $sizeOptions) 
    {
        $interimResults = array();
        $materialData = array();
        foreach ($siblings as $sibling) {
            $attr = $sibling->getAttributes();
            $interimResults[$attr['f_variante']['value']][] = $attr['f_laenge']['value'];
            $materialData[$attr['f_variante']['value']] = array(
                'material' => $attr['f_material_1']['value'],
                'ov' => $attr['f_oberflaechenverarbeitung_1']['value'],
                'edelstein' => $attr['f_edelstein_1']['value'],
            );
        }
        $result = array();
        foreach ($interimResults as $variant => $options) {
            if (sizeof($options) >= $this->_maxSizeOptions) {
                continue;
            }
            $availableOptions = array_diff($sizeOptions, $options);
            if (count($availableOptions) <=0)
                continue;
            $result[] = array(
                'variant' => $variant,
                'sizes' => $availableOptions
            );
        }
        return array(
            'variants' => $result,
            'materials' => $materialData
        );
    }
    
    protected function _getSeedVariantValues($attributes) 
    {
        $found = false;
        $counter = 0;
        while (!$found) {
            $counter++;
            if ($counter > 200) {
                break;
            }
            
            $material = $attributes['f_material_1']['options'][rand(0, count($attributes['f_material_1']['options'])-1)];
            $ov = $attributes['f_oberflaechenverarbeitung_1']['options'][rand(0, count($attributes['f_oberflaechenverarbeitung_1']['options'])-1)];
            $edelstein = $attributes['f_edelstein_1']['options'][rand(0, count($attributes['f_edelstein_1']['options'])-1)];
            $laenge = $attributes['f_laenge']['options'][rand(0, count($attributes['f_laenge']['options'])-1)];
            $proposedValue = str_replace(',', '', $material).' '.
                    str_replace(',', '', $ov).' '.str_replace(',', '', $edelstein);
            if (!in_array($proposedValue, $attributes['f_variante']['options'])) {
                continue;
            } else {
                $found = true;
            }
            $attributes['f_material_1']['value'] = $material;
            $attributes['f_oberflaechenverarbeitung_1']['value'] = $ov;
            $attributes['f_edelstein_1']['value'] = $edelstein;
            $attributes['f_laenge']['value'] = $laenge;
            $attributes['f_variante']['value'] = $proposedValue;
            
            return $attributes;
        }
        return false;
        
        
    }
    

    public function ping()
    {
        echo __DIR__;
        echo '<br />';
        echo $_SERVER['DOCUMENT_ROOT'];
        die();
    }
    
}
?>