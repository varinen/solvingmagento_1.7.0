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


$installer = $this;


$installer->startSetup();

//attribute option data array
$data = array (
    'brand' => 'adidas,Armani,Billabong,Boss,Boss Orange,Breil,Breytenbach,Casio,Cerruti 1881,'.
        'Certina,Chronix,Citizen,ck Calvin Klein,Columbia,Converse,Diesel,DKNY,Dolce & Gabbana,'.
        'Dugena,Ebel,edc,Esprit,Festina,Flik Flak,Fossil,Gant,Gc,Guess,Haemmer,Hamilton,Hello Kitty,'.
        'HipHop,Ice-Watch,ISEE,IWC,Jacques Farel Kids,Jacques Lemans,JOOP,Junghans,Junkers,Lacoste,'.
        'LunaTik,Marc O Polo,Michael Kors,Michele,Nautica,Nixon,Omega,Oregon Scientific,Police,'.
        'Puma,Rolex,S.Oliver,Seiko,Skagen,Swatch,Tag Heuer,THE ONE Binary,TIMEX,Tissot,Tom Tailor,'.
        'Tommy Hilfiger,ToyWatch,Traser,TW Steel,Vagary,Zeppelin,Zodiac',

    'model_year' => '2000,2001,2002,2003,2004,2005,2006,2007,2008,2009,2010,2011,2012',

    'form'  => 'round,square,oval,sphere,brick',

    'type' => 'Chronograph,Amulet,Pin,Wristband,Wrist,Brosh,Clipster,Cocktail ring,Drangon hanger,'.
        'Gem hanger, Erotic hanger,Regular junk, Glittered junk,Crystall junk,Stainless steel junk,'.
        'Weird junk,Exotic junk,Star shaped junk,Beaded junk,Junk with beads,Junk without beads,',
        'Lucky junk,Ivory junk,Mother of pearl junk,Pearl junk,Junk with pearls,Chain junk',

    'gender' => 'Men,Women,Children,Unisex',

    'wristband_material' => 'Leather,Stainless steel,Yellow gold,White gold,Rubber,Plastic,Silver,'.
        'Titanium',

    'case_form' => 'round,square,oval,sphere,brick',

    'glas_cover' => 'Rock chrystal,Saphire glass,Plastic',

    'mechanichs' => 'Radio,Solar,Quartz,Manually wound,Automatically wound,Hybrid',

    'parts' => 'Analog,Digital,Analog/Digital',

    'water_resistant' => 'yes,no,water repellent',

    'engraving' => 'Yes,No',

    'size' => '51,52,53,54,55,56,57,58,59,60,61,62',

    'suitable_for' => 'casual,special occasions,funeral',

);

foreach ($data as $code => $attributeOptions) {
    $installer->addAttributeOptions($code, $attributeOptions);
}
  
$installer->endSetup();

?>