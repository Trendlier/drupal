<?php

function platform_get_news_post($node)
{
    $news_post = new stdClass();
    $news_post->title = $node->title;

    $news_post->subtitle = get_field_text($node, 'field_subtitle');
    $news_post->subtitle = (
        $news_post->subtitle ?
        $news_post->subtitle : $node->title);

    $news_post->text = get_field_text($node, 'field_text');

    $product_nid = get_field_node_reference($node, 'field_product');
    $product_node = node_load($product_nid);
    $news_post->product_image_url =
        get_field_image_url($product_node, 'field_image');

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
        return file_create_url($item['uri']);
    }
    throw new Exception('Unexpected error');
}
