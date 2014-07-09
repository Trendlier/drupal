<?php

function platform_node_product_get($node)
{
    $product = new stdClass();
    $product->nid = $node->nid;
    $product->category_id = get_field_value($node, 'field_category');
    $product->title = $node->title;
    $product->subtitle = get_field_text($node, 'field_subtitle');
    $product->image_url = get_field_image_url($node, 'field_image');
    $product->image_width = get_field_value($node, 'field_image_width');
    $product->image_height = get_field_value($node, 'field_image_height');
    $product->description = get_field_text($node, 'field_description');
    $product->is_hidden = get_field_value($node, 'field_hidden');
    return $product;
}

function platform_node_news_post_get($node)
{
    $news_post = new stdClass();
    $news_post->nid = $node->nid;
    $news_post->news_post_type_id =
        get_field_value($node, 'field_news_post_type');
    $news_post->title = $node->title;
    $news_post->subtitle = get_field_text($node, 'field_subtitle');
    $news_post->text = get_field_text($node, 'field_text');
    $news_post->excerpt = get_field_text($node, 'field_excerpt');
    $news_post->is_hidden = get_field_value($node, 'field_hidden');

    // Pull information about the product from the product node
    $product_nid = get_field_node_reference($node, 'field_product');
    $product_node = node_load($product_nid);
    $news_post->product = platform_node_product_get($product_node);

    return $news_post;
}

function get_field_text($node, $field_name)
{
    $field_text_items = field_get_items('node', $node, $field_name);
    $field_text = '';
    foreach ($field_text_items as &$field_text_item)
    {
        $field_text .= $field_text_item['value'];
    }
    return $field_text;
}

function get_field_value($node, $field_name)
{
    $items = field_get_items('node', $node, $field_name);
    foreach ($items as &$item)
    {
        return $item['value'];
    }
    throw Exception('Expected value in ' . $field_name);
}

/**
 * @return $nid of referred node
 */
function get_field_node_reference($node, $field_name)
{
    $field_items = field_get_items('node', $node, $field_name);
    foreach ($field_items as $item)
    {
        return $item['nid'];
    }
    throw new Exception('Unexpected error');
}

function get_field_image_url($node, $field_name)
{
    $field_items = field_get_items('node', $node, $field_name);
    foreach ($field_items as $item)
    {
        if (array_key_exists('fid', $item))
        {
            $file = file_load($item['fid']);
            return file_create_url($file->uri);
        }
    }
    throw new Exception(
        'fid missing on ' . $node->nid . '/' . $field_name . ' ' .
        'or URI missing on file');
}
