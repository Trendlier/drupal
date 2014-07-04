<?php

require_once('field_fun.php');

/**
 * Get field text
 */
$field_text = paginate_field_text($node, 'field_text', 30);

/**
 * Get field subtitle
 */
$field_subtitle = get_field_text($node, 'field_subtitle');
$field_subtitle = $field_subtitle ? $field_subtitle : $node->title;

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php print $node->title; ?></title>
        <meta name="description" content="<?php print $field_subtitle; ?>">
        <style type="text/css">
            h1 {font-family: RionaSans-Bold;line-height: 16px;font-size: 12pt;}
            p {font-family: RionaSans-Regular;font-size: 9.5pt;}
        </style>
    </head>
    <body>
        <div style="margin: auto; width: 296px;">
        <div style="height:160px;overflow:hidden">
            <!-- TODO: put IMG here -->
        </div>
        <div style="width: 284px">
            <h1><?php print $field_subtitle; ?></h1>
            <p><?php print $field_text; ?></p>
        </div>
        </div>
    </body>
</html>