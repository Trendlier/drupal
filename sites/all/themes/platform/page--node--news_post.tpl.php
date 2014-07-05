<?php

require_once('field_fun.php');

/**
 * Determine page dimensions
 */
if (array_key_exists('page', $_GET))
{
    $page_number = $_GET['page'];
    $page_offset = ($page_number > 1 ? 19: 0) + ($page_number - 1) * 323;
    $page_height = ($page_number == 1 ? 342 : 323);
}
else
{
    $page_offset = 0;
    $page_height = 342;
}

/**
 * Get field text
 */
$field_text = get_field_text($node, 'field_text');

/**
 * Get field subtitle
 */
$field_subtitle = get_field_text($node, 'field_subtitle');
$field_subtitle = $field_subtitle ? $field_subtitle : $node->title;

/**
 * Get field product
 */
$product_nid = get_field_node_reference($node, 'field_product');
$product_node = node_load($product_nid);
$product_image_url = get_field_image_url($product_node, 'field_image');

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php print $node->title; ?></title>
        <meta name="description" content="<?php print $field_subtitle; ?>">
        <style type="text/css">
            h1 {font-family: RionaSans-Bold;font-size: 18px;line-height: 19px;}
            p {font-family: RionaSans-Regular;font-size: 12px;}
            .page_container {
                position: relative;
                overflow: hidden;
                height: <?php print $page_height; ?>px;
                width: 296px;
                margin: auto;
                line-height: 19px;
            }
            .page_offset_container {
                position: relative;
                top: -<?php print $page_offset; ?>px;
            }
            .product_img_container {
                max-height: 148px;
                overflow: hidden;
            }
            .product_img {
                max-width: 296px;
                height: auto;
            }
        </style>
    </head>
    <body>
        <div class="page_container">
            <div class="page_offset_container">
                <div class="product_img_container">
                    <img class="product_img" src="<?php print $product_image_url; ?>" />
                </div>
                <div style="width: 284px">
                    <h1><?php print $field_subtitle; ?></h1>
                    <p><?php print $field_text; ?></p>
                </div>
            </div>
        </div>
    </body>
</html>
