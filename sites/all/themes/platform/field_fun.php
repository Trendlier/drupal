<?php

require_once('word_count_paginate.php');

function paginate_field_text(&$node, $field_name, $page_size)
{
    $field_text_items = field_get_items('node', $node, 'field_text');
    $field_text = '';
    foreach ($field_text_items as &$field_text_item)
    {
        paginate_text($field_text_item['value'], $page_size);
        paginate_text($field_text_item['safe_value'], $page_size);
        $field_text .= render(
            field_view_value('node', $node,
                'field_text',
                $field_text_item,
                array('label'=>'hidden')
            ));
    }
    return $field_text;
}

function get_field_text($node, $field_name)
{
    return render(
        field_view_field('node', $node,
            'field_subtitle',
            array('label'=>'hidden')
        ));
}
