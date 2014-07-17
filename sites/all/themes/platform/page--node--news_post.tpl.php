<?php

module_load_include('php', 'platform', 'node_fun');

/**
 * Determine page dimensions
 *
 * TODO: Move this into its own JS module that handles pagination using
 * jQuery by finding the offset height of the content within the window.
 * The JS should also not display a line at the bottom if it will be cut off,
 * and be smart enough to display that very line on the next page.
 */
if (array_key_exists('page', $_GET))
{
    $page_number = $_GET['page'];
//    $page_offset = ($page_number > 1 ? 19: 0) + ($page_number - 1) * 323;
//    $page_height = ($page_number == 1 ? 342 : 323);
    $page_offset = ($page_number - 1) * 300 - ($page_number > 1 ? 0: 0);
    $page_height =  342;
}
else
{
    $page_offset = 0;
    $page_height = 342;
}

$news_post = platform_node_news_post_get($node);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php print $news_post->title; ?></title>
        <meta name="description" content="<?php print $news_post->subtitle; ?>">
        <!-- TODO: Move the page_container CSS into the same JS module for
                   for automatic pagination. -->
        <style type="text/css">
            h1 {font-family: RionaSans-Bold;font-size: 18px;line-height: 19px;}
            p {font-family: RionaSans-Regular;font-size: 12px;}

            .page_container {
                position: relative;
                overflow: hidden;
                height: 342px;
                width: 296px;
                margin: auto;
                line-height: 19px;
            }
            .page_offset_container {
                position: relative;
                height: 342px;
                -webkit-column-gap: 10px;
                -moz-column-gap: 10px;
                column-gap: 10px;
                -webkit-column-width: 296px;
                -moz-column-width: 296px;
                column-width: 296px;
                right: <?php print $page_offset; ?>px;
            }
            .product_img_container {
                overflow: hidden;
            }
            img {
                max-width: 296px;
                height: auto;
            }
        </style>
    </head>
    <body>
        <div class="page_container">
            <div class="page_offset_container">
                <?php if (!is_null($news_post->product)): ?>
                    <div class="product_img_container">
                        <img class="product_img" src="<?php print $news_post->product->image_url; ?>" />
                    </div>
                <?php elseif (!is_null($news_post->image_url)): ?>
                    <div class="product_img_container">
                        <img class="product_img" src="<?php print $news_post->image_url; ?>" />
                    </div>
                <?php endif; ?>
                <div >
                    <h1><?php print $news_post->subtitle ? $news_post->subtitle : $news_post->title; ?></h1>
                    <p><?php print $news_post->text; ?></p>
                </div>
            </div>
        </div>
    </body>
</html>
