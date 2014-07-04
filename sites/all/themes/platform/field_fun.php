<?php

require_once('word_count_paginate.php');

function paginate_field_text(&$node, $field_name, $page_size)
{
    $field_text_items = field_get_items('node', $node, 'field_text');
    $field_text = '';
    foreach ($field_text_items as &$field_text_item)
    {
        $field_text .= paginate_text($field_text_item['value'], $page_size);
    }
    return $field_text;
}

function get_field_text($node, $field_name)
{
    $field_text_items = field_get_items('node', $node, 'field_text');
    $field_text = '';
    foreach ($field_text_items as &$field_text_item)
    {
        $field_text .= $field_text_item['value'];
    }
    return $field_text;}
