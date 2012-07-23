<?php

class DataGenerator extends Varien_Object
{
    public $productTypes;
    public $schmuckFields;
    public $uhrenFields;
    protected $_defaultAttributes;
    public $loremIpsum;
    public $products = array();
    protected $_images = array();
    
    
    public function __construct($setup, $productCount, $counter)
    {
        parent::__construct();
        $this->setSetup($setup);
        $this->setProductCount($productCount);
        $this->setCounter($counter);
        $this->_initialize();
        $this->_readImages();
    }
        
    protected function _initialize()    
    {
        $this->loremIpsum = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do '.
            'eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, '.
            'quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. '.
            'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu '.
            'fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in '.
            ' culpa qui officia deserunt mollit anim id est laborum.';
        
        $this->productTypes =  array(
        array(
            'type'  =>  'Armschmuck',
            'set'   =>  'Schmuck',
            'exclude'  => array(
                'f_geeignet_fuer', 'f_form', 'f_typ','f_teile', 'f_anzahl_der_teile','f_groesse'
            ),
            'art'   => 'Armbänder,Armreife,Armspangen,Bettelarmbänder,Lederarmbänder,Perlarmbänder'

        ),
        array(
            'type'  =>  'Charms_Anhaenger',
            'set'   =>  'Schmuck',
            'exclude'   => array(
                'f_geeignet_fuer', 'f_form', 'f_typ','f_teile', 'f_anzahl_der_teile', 'f_ketten_art',
                'f_groesse'
            ),
            'art'   =>  'Ägyptenanhänger,Amulette,Buchstabenanhänger,Buddhanhänger,Charms,'.
                'Drachenanhänger,Edelsteinanhänger,Elfenanhänger,Engelanhänger,Erotikanhänger,'.
                'Fahrzeuganhänger,Glaube/Liebe/Hoffnunganhänger,Glücksbringer,Gravuranhänger,'.
                'Herzanhänger,Jesusanhänger,Kinder/Babysanhänger,Kreuzanhänger,Kugelanhänger,'.
                'Medaillons,Musikanhänger,Partneranhänger,Pendelanhänger,Pentagrammanhänger,'.
                'Perlenanänger,Rasierklingenanhänger,Schlüsselanhänger,Schutzpatronanhänger,'.
                'Seefahrtanhänger,Sonne/Mond/Sterneanhänger,Sportanhänger,Sternzeichenanhänger,'.
                'Tieranhänger,Totenkopfanhänger,Wappenanhänger,Wikingeranhänger,weitere Anhänger'
        ),
        array(
            'type'  =>  'Halskette',
            'set'   =>  'Schmuck',
            'exclude'   => array(
                'f_geeignet_fuer', 'f_form', 'f_typ','f_teile', 'f_anzahl_der_teile', 'f_ketten_art',
                'f_groesse'
            ),
            'art'   => 'Halsreife,Herrenketten,Kette mit Anhänger,Kette ohne Anhänger,Königsketten,'.
                'Kugelketten,Lederketten,Panzerketten,Perlenketten,Singapurketten,Verlängerungsketten,'.
                'weiterer Halsschmuck'

        ),
        array(
            'type'  =>  'Ohrring',
            'set'   =>  'Schmuck',
            'exclude'   => array(
                'f_geeignet_fuer', 'f_form', 'f_typ','f_teile', 'f_anzahl_der_teile', 'f_ketten_art',
                'f_groesse'
            ),
            'art'   =>  'Clipstecker,Creolen,Ohrboutons,Ohrclips,Ohrhaken,Ohrhänger,Ohrstecker'

        ),
        array(
            'type'  =>  'Ring',
            'set'   =>  'Schmuck',
            'exclude'   =>  array(
                'f_geeignet_fuer', 'f_form', 'f_teile', 'f_anzahl_der_teile', 'f_ketten_art',
                'f_verschluss'
            ),
            'art'   => 'Cocktailring,Eternityringe,Solitärring,Rivièrering,Halbeternityring'
        ),
        array(
            'type'  =>  'Schmuckset',
            'set'   =>  'Schmuck',
            'exclude'   => array(
                'f_geeignet_fuer', 'f_form','f_typ', 'f_ketten_art',
                'f_groesse'
            )
        ),
        array(
            'type'  =>  'Piercingschmuck',
            'set'   =>  'Schmuck',
            'exclude'   => array(
                'f_art', 'f_typ', 'f_teile', 'f_anzahl_der_teile', 'f_ketten_art',
                'f_groesse'
            )
        ),
        array(
            'type'  =>  'Weiterer_Schmuck',
            'set'   =>  'Schmuck',
            'exclude'   => array(
                'f_geeignet_fuer', 'f_form','f_typ','f_teile', 'f_anzahl_der_teile', 'f_ketten_art',
                'f_groesse'
            ),
            'art'   => 'Anstecknadeln,Broschen,Fußkettchen,Geldklammern,Krawattennadeln,'.
                'Schlüsselbänder,Schmuckreinigung,Schmuckkästchen'

        ),
        );
        
        $this->_defaultAttributes = explode(',', 'store,websites,attribute_set,category_ids,'.
            'price,description,short_description,weight,status,has_options,is_in_stock,'.
            'options_container,tax_class_id,visibility');
        
        $this->schmuckFields = array();
            
            $schmuckAttributes = explode(',', 'name,price,description,sku,short_description,weight,'.
            'status,options_container,tax_class_id,visibility,has_options,is_in_stock,'.
            'f_typ,f_teile,f_art,f_kettenart,f_gender,f_gravurartikel,f_laenge,f_form,'.
            'f_geeignet_fuer,f_marke,f_groesse,f_serie,f_modell,f_modelljahr,'.
            'f_besonderheiten,f_usp1,f_usp2,f_usp3,f_usp4,f_usp5,f_verschluss,f_breite,f_hoehe,'.
            'f_durchmesser,f_zertifikat,f_material_1,f_material_2,f_material_3,f_materialfarbe_1,'.
            'f_materialfarbe_2,f_materialfarbe_3,f_edelstein_1,f_edelstein_2,f_edelstein_3,'.
            'f_perlenart_1,f_perlenart_2,f_perlenart_3,f_perlenform_1,f_perlenform_2,'.
            'f_perlenform_3,f_edelsteingewicht_1,f_edelsteingewicht_2,f_edelsteingewicht_3,'.
            'f_farbe_1,f_farbe_2,f_farbe_3,f_schliff_1,f_schliff_2,f_schliff_3,'.
            'f_oberflaechenverarbeitung_1,f_oberflaechenverarbeitung_2,'.
            'f_oberflaechenverarbeitung_3,f_durchmesser_1,f_durchmesser_2,f_durchmesser_3,'.
            'f_perlenoberflaeche_1,f_perlenoberflaeche_2,f_perlenoberflaeche_3,f_anzahl_1,'.
            'f_anzahl_2,f_anzahl_3,f_fassung_1,f_fassung_2,f_fassung_3,f_reinheit_1,f_reinheit_2,'.
            'f_reinheit_3,f_variante');
            
        foreach ($schmuckAttributes as $attributeCode) {
            $this->schmuckFields[$attributeCode] = array('options' => array(), 'value' => null);
        }
        
        $this->schmuckFields = $this->_getValueOptions('Schmuck', $this->schmuckFields);
        
        
        $this->uhrenFields = array();
            
            $uhrenAttributes = explode(',', 'name,price,description,sku,short_description,weight,'.
            'status,options_container,tax_class_id,visibility,has_options,is_in_stock,f_serie,'.
            'f_gravurartikel,f_wasserdicht,f_uhrenart,f_uhrwerk,f_glas,f_gehaeuseform,f_gender,'.
            'f_art,f_marke,f_modell,f_modelljahr,f_armbandbreite,f_armbandlaenge,f_gehaeusematerial,'.
            'f_gehaeusedurchmesser,f_gehaeusehoehe,f_gehaeuseboden,f_ziffernblattfarbe,'.
            'f_ziffern,f_schliesse,f_krone,f_luenette,f_wasserdichtigkeit,'.
            'f_beschichtungsverfahren,f_besonderheiten,f_usp1,f_usp2,f_usp3,f_usp4,'.
            'f_usp5,f_armbandmaterial_1,f_armbandmaterial_2,f_armbandmaterial_3,'.
            'f_armbandfarbe_1,f_armbandfarbe_2,f_armbandfarbe_3');
            
        foreach ($uhrenAttributes as $atttributeCode) {
            $this->uhrenFields[$atttributeCode] = array('options' => array(), 'value' => null);
        }
            
        $this->uhrenFields = $this->_getValueOptions('Uhren', $this->uhrenFields);
        $this->_configurables = array();
        $this->_maxSizeOptions = 5;
           
    }
    
    protected function _getValueOptions($setName, $fields)
    {
        $setup = $this->getSetup();
        
        $setId = $setup->getAttributeSetId('catalog_product', $setName);
        
        $collection = Mage::getModel('catalog/resource_product_attribute_collection')
            ->setAttributeSetFilter($setId)
            ->load();
        
        foreach ($collection as $attribute) {
            if (key_exists($attribute->getAttributeCode(), $fields) &&
                (($attribute->getFrontendInput() == 'select') || 
                ($attribute->getFrontendInput() == 'multiselect'))) {
                
                $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setAttributeFilter($attribute->getId())
                    ->setStoreFilter(0, false)
                    ->load();
                foreach ($valuesCollection as $optionValue) {
                    $fields[$attribute->getAttributeCode()]['options'][] = $optionValue->getValue();
                }
                
            }
        }
        return $fields;
            
    }
    
    protected function _getAttributeValues($attributes, $type, $exclude) 
    {
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
        return $attributes;
    }
    
    public function generateData()
    {
        $counter = $this->getCounter();
        $sets = array('Schmuck', 'Uhren');
        while ($counter < ($this->getProductCount() +$this->getCounter())) {
            $set = $sets[rand(0,1)];
            
            $counter  = $this->productCreate($counter, $set, 'configurable');

            $counter++;
        }
        echo count($this->products).' product generated!';
        $this->_writeCsv();
    }
    
    function productCreate($counter, $set, $type, $sku = false, $art = false, 
        $productType = false, $gender = false) {
        
        $product = new Varien_Object();
        if (!$sku)
            $sku    = "sku_{$counter}";
        
        $configurableAttributes = array();
            
        if ($set == 'Uhren') {
            $productType = false;
            $productAttributes = $this->_getAttributeValues($this->uhrenFields, $type, array());
            if (!$productAttributes)
                return false;
            //simples must have the same art as their parent configurables
            if ($art) {
                $productAttributes['f_art']['value'] = $art;
            }
            if ($gender) {
                $productAttributes['f_gender']['value'] = $gender;
            }
            $name = $productAttributes['f_art']['value'].' '.
                $productAttributes['f_uhrenart']['value'].' für '.
                $productAttributes['f_gender']['value'].', Armband aus '.
                $productAttributes['f_armbandmaterial_1']['value'].' -'.$counter;
            
            if ($type == 'configurable') {
                $configurableAttributes = array('f_armbandmaterial_1', 'f_armbandfarbe_1');
            }
                
        } else if ($set == 'Schmuck') {
            if (!$productType)
                $productType = $this->productTypes[rand(0, count($this->productTypes)-1)];
            if ($art) {
                $artValue = $art;
            } else if (isset($productType['art'])) {
                $artOptions = explode(',',$productType['art']);
                $artValue = $artOptions[rand(0, count($artOptions)-1)];
            } else 
                $artValue = null;
            $productAttributes = $this->_getAttributeValues($this->schmuckFields, $type, $exclude);
            if (!$productAttributes)
                return false;
            
            if ($gender) {
                $productAttributes['f_gender']['value'] = $gender;
            }
            $productAttributes['f_art']['value'] = $artValue;
            $name = $productType['type'].' für '.$productAttributes['f_gender']['value'].', aus '.
                $productAttributes['f_material_1']['value'].' mit '.
                $productAttributes['f_edelstein_1']['value'].' - '.$type.' '.$counter;
            
            if ($type == 'configurable') {
                $configurableAttributes = array('f_material_1', 'f_edelstein_1',
                    'f_oberflaechenverarbeitung_1', 'f_farbe_1');
                if ($productType['type'] == 'Ringe') {
                    $configurableAttributes[] = 'f_groesse';
                }
            }
            
            
        }
        
        if ($type == 'configurable') {
            $count_simples = rand(2, 5);
            $simple_skus = array();
            $i = 1;
            for (; $i <= $count_simples; $i++) {
                $simple_sku = "{$sku}_{$i}";
                $simple_skus[] = $simple_sku;
                $this->productCreate(
                    $i, 
                    $set, 
                    'simple', 
                    $simple_sku, 
                    $productAttributes['f_art']['value'],
                    $productType,
                    $productAttributes['f_gender']['value']
                );
            }

            $counter += $i;
            $simples_skus = implode(',', $simple_skus);
            $productAttributes['simples_skus']['value'] = $simples_skus;
            $sku = "{$sku}_cfg";
        } else {
            $productAttributes['simples_skus']['value'] = '';
        }
        $productAttributes['configurable_attributes']['value'] = 
            implode(',', $configurableAttributes);
        
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
   
        $this->products[] = $product;

        return $counter;
    }
    
    protected function _writeCsv()
    {
        $res = fopen('../var/import/my_products.csv', 'w');
        $header = $this->_getHeader();
        if (!fputcsv($res, $header, ';', '"')) {
            echo 'There was an error writing the content';
        }
  
        foreach ($this->products as $product) {
            $data  = $product->getAttributes();
            ksort($data);
            $csvArray = array();
            foreach ($header as $field) {
                if (key_exists($field, $data)) {
                    $csvArray[] = $data[$field]['value'];
                } else {
                    $csvArray[] = '';
                }
            }
            if (!fputcsv($res, $csvArray, ';', '"')) {
                echo 'There was an error writing the content';
                $this->_closeFile($res);
            }
        }
        $this->_closeFile($res);
    }
    
    protected function _closeFile($res) 
    {
        if (!fclose($res)) {
            die('could not close file!');
        }
    }
    
    protected function _getHeader()
    {
        $setFields = array('Schmuck' => array(), 'Uhren' =>  array());
        $header = array();
        $i = 0;
        while (((sizeof($setFields['Schmuck'] == 0)) || (sizeof($setFields['Uhren'] == 0)))
                && ($i < sizeof($this->products))) {
            $attributes = $this->products[$i]->getAttributes();
            if ($attributes['attribute_set']['value'] == 'Schmuck') {
                $setFields['Schmuck'] = $attributes;
            }
            if ($attributes['attribute_set']['value'] == 'Uhren') {
                $setFields['Uhren'] = $attributes;
            }

            $keys = array_keys($attributes);
            $diff = array_diff($keys,$header);
                $header = array_merge($header, $diff);
            $i++;
        }
        $imageColumns = array('image', 'small_image', 'thumbnail_image', 'media_gallery');
        
        sort($header);
        $header = array_merge($header, $imageColumns);
        return $header;
            
    }
    
    protected function _watermarkImage ($sourceFile, $waterMarkText, $destinationFile) 
    {
        list($width, $height) = getimagesize($sourceFile);
        $imageP = imagecreatetruecolor($width, $height);
        $image = imagecreatefromjpeg($sourceFile);
        imagecopyresampled($imageP, $image, 0, 0, 0, 0, $width, $height, $width, $height);
        $black = imagecolorallocate($imageP, 253, 253, 253);
        $font = $_SERVER['DOCUMENT_ROOT'].'/var/importsource/arial.ttf';
        $font_size = 10;
        imagettftext($imageP, $font_size, 0, 10, 20, $black, $font, $waterMarkText);
        if ($destinationFile <> '') {
        imagejpeg ($imageP, $destinationFile, 100);
        } else {
        header('Content-Type: image/jpeg');
        imagejpeg($imageP, null, 100);
        };
        imagedestroy($image);
        imagedestroy($imageP);
    }
    
    protected function _addImages($product)
    {
        $dir = $_SERVER['DOCUMENT_ROOT'];
        $attributes = $product->getAttributes();
        $sku = $attributes['sku']['value']; 
        if (count($this->_images) > 0) {
            $baseImageName = $this->_images[(rand(0, count($this->_images)-1))];
            $this->_watermarkImage(
                $dir.'/var/importsource/'.$baseImageName,
                $sku,
                $dir.'/media/import/'.$sku.'.jpg'
            );
            $attributes['image']['value'] = $sku.'.jpg';
            $attributes['small_image']['value'] = $sku.'.jpg';
            $attributes['thumbnail_image']['value'] = $sku.'.jpg';
            
            $maxGallery = rand(2, 5);
            $gallery = array();
            for ($i = 0; $i < $maxGallery; $i++){
                $skuSimple = $sku.'_'.$i;
                $galleryImageName = $this->_images[(rand(0, count($this->_images)-1))];
                $this->_watermarkImage(
                    $dir.'/var/importsource/'.$galleryImageName,
                    $skuSimple,
                    $dir.'/media/import/'.$skuSimple.'.jpg'
                );
                $gallery[] = $skuSimple.'.jpg';
                $attributes['media_gallery']['value'] = implode(';', $gallery);
            }
            $product->setAttributes($attributes);
        }
        return $product;
    }
    
    protected function _readImages()
    {
        $dir = $_SERVER['DOCUMENT_ROOT'].'/var/';
        try {
            $dirHandle = opendir($dir.'importsource');
            if ($dirHandle) {
                $files = array();

                while (false !== ($entry = readdir($dirHandle))) {
                    if (strstr($entry, 'jpg'))
                        $files[] = $entry;
                }

                $this->_images = $files;
                closedir($dirHandle);
            }
        } catch(Exception $e) {
            echo 'doh!: '.$e->getMessage();
        }
    }
}
?>
