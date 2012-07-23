<?php
/**
 * This file creates a csv file which is ready for importing data with
 * magmi into the magento database.
 *
 * The following steps are performed to create random data:
 * 1. create the csv file
 * 2. add a header
 * 3. fill with products
 * 4. close file
 */

$products_count = 40000;

/**
 * 1. Create the file.
 */
$res = fopen('../var/import/my_products.csv', 'w');

/**
 * 2. Add a header.
 */
$header_array = array(
    'store', 'websites', 'attribute_set', 'category_ids','sku', 'name', 'price',
    'status', 'simples_skus', 'type', 'configurable_attributes', 'options_container',
    'has_options', 'is_in_stock', 'edelstein', 'gender', 'surface', 'tax_class_id',
    'visibility', 'description', 'short_description', 'qty', 'color'
);
write_csv_file($header_array);


/**
 * Basic variables.
 */
// the two different product types
$types  = array('simple', 'configurable');
// product colors
$colors = array(
    'Black', 'Blue', 'Brown', 'Gray', 'Green', 'Magenta', 'Pink', 'Red', 'Silver',
    'White'
);
// Gems
$gems   = array(
    'Achat', 'Akoyazuchtperle', 'Amethyst', 'Apatit', 'Aquamarin', 'Bergkristall',
    'Bernstein', 'Blautopas'
);
// Gender
$genders = array('Children', 'Mens', 'Unisex', 'Womens');
// Surface
$surfaces = array(
    'diamantiert', 'geschwärzt', 'mattiert', 'poliert', 'rhodiniert', 'strukturiert',
    'vergoldet', 'versilbert'
);
// Categories
$categories = array(10, 22, 23, 13, 8, 12, 15, 18, 4, 5, 19);

/**
 * 3. Create random data.
 *
 * Loop that creates each product. These are the steps for a product:
 * 1. decide if simple or configurable
 * 2. for simple product:
 *    - create sku and all needed attributes
 */
$counter = 0;
while ($counter < $products_count) {
    $type   = $types[rand(0, count($types)-1)];

    $function = "create_{$type}";
    $counter  = $function($counter);

    $counter++;
}


/**
 * 4. Close file.
 */
close_file($res);


/**
 * Create a simple product.
 */
function create_simple($counter, $sku=false, $gender=false, $price=false, $cats=false, $gem=false, $color=false) {
    $type = 'simple';

    // Only visible when stand alone product
    $visibility = 1;
    if (!$sku) {
        $sku = "sku_{$counter}";
        $visibility = 4;
    }

    if (empty($cats)) {
        $cats = get_categories();
    }
    $cats = implode(',', $cats);

    // Gem
    if (!$gem)      $gem = $GLOBALS['gems'][rand(0, count($GLOBALS['gems'])-1)];
    // Gender
    if (!$gender)   $gender = $GLOBALS['genders'][rand(0, count($GLOBALS['genders'])-1)];
    // Price
    if (!$price)    $price  = rand(0, 100).'.99';
    // Color
    $color  = $GLOBALS['colors'][rand(0, count($GLOBALS['colors'])-1)];
    // Surface
    $surface= $GLOBALS['surfaces'][rand(0, count($GLOBALS['surfaces'])-1)];
    // Name
    $name   = "$gem $color $surface for $gender - $counter";

    // CSV array
    $line_array = array(
        'admin', 'base', 'default', $cats, $sku, $name, $price, 1, '', $type,
        '', '', 0, 1, $gem, $gender, $surface, 1, $visibility, 'description',
        'short_description', 1000, $color
    );
    // write to file
    write_csv_file($line_array);

    return $counter;
}


/**
 * Create a configurable product.
 */
function create_configurable($counter) {
    $type = 'configurable';

    $sku    = "sku_{$counter}";

    $gem    = $GLOBALS['gems'][rand(0, count($GLOBALS['gems'])-1)];
    $color  = $GLOBALS['colors'][rand(0, count($GLOBALS['colors'])-1)];
    $surface= $GLOBALS['surfaces'][rand(0, count($GLOBALS['surfaces'])-1)];
    $gender = $GLOBALS['genders'][rand(0, count($GLOBALS['genders'])-1)];
    $name   = "$gem $color $surface for $gender - $counter";
    $price  = rand(0, 100).'.99';
    $cats   = get_categories();

    $count_simples = rand(2, 10);
    $simple_skus = array();
    $i = 1;
    for (; $i <= $count_simples; $i++) {
        $simple_sku = "{$sku}_{$i}";
        $simple_skus[] = $simple_sku;
        create_simple($i, $simple_sku, $gender, $price, $cats);
    }

    $counter += $i;
    $simples_skus = implode(',', $simple_skus);
    $sku = "{$sku}_cfg";
    $conf_attributes = implode(',', array('surface', 'edelstein', 'color'));
    $cats = implode(',', $cats);

    // CSV array
    $line_array = array(
        'admin', 'base', 'default', $cats, $sku, $name, $price, 1, $simples_skus,
        $type, $conf_attributes, '', 1, 1, $gem, $gender, $surface, 1, 4,
        'description', 'short_description', 1000, $color
    );

    // write to file
    write_csv_file($line_array);

    return $counter;
}


/**
 * Returns an array with categorie ids.
 */
function get_categories() {
    $cat = array();
    $count_cat = rand(1, 4);
    for ($i = 0; $i < $count_cat; $i++) {
        $cat[] = $GLOBALS['categories'][rand(0, count($GLOBALS['categories'])-1)];
    }
    return array_unique($cat);
}


/**
 * Write array to csv file
 */
function write_csv_file($array) {
    if (!fputcsv($GLOBALS['res'], $array, ';', '"')) {
        echo 'There was an error writing the content';
        close_file($res);
    }
}


/**
 * Closes the given resource. New function, because we need this a lot!
 */
function close_file($res) {
    if (!fclose($res)) {
        die('could not close file!');
    }
}
