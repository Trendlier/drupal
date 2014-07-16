<?php

function platform_node_product_get($node)
{
    $product = new stdClass();
    $product->nid = $node->nid;
    $product->category_id = get_field_value($node, 'field_category');
    $product->title = $node->title;
    $product->subtitle = get_field_text($node, 'field_subtitle');
    $product->image_url = get_field_image_url($node, 'field_image');
    $image_info = get_field_image_info($node, 'field_image');
    $product->image_width = $image_info['width'];
    $product->image_height = $image_info['height'];
    $product->description = get_field_text($node, 'field_description');
    $product->is_hidden = get_field_value($node, 'field_hidden');
    return $product;
}

function platform_node_news_post_get($node)
{
    $news_post = new stdClass();
    $news_post->nid = $node->nid;
    $news_post->category_id = get_field_value($node, 'field_category');
    $news_post->news_post_type_id =
        get_field_value($node, 'field_news_post_type');
    $news_post->title = $node->title;
    $news_post->subtitle = get_field_text($node, 'field_subtitle');
    $news_post->text = get_field_text($node, 'field_text');
    $news_post->excerpt = get_field_text($node, 'field_excerpt');
    $news_post->is_hidden = get_field_value($node, 'field_hidden');

    try
    {
        $news_post->image_url = get_field_image_url($node, 'field_image');
    }
    catch (Exception $e)
    {
        $news_post->image_url = null;
    }

    // Generate page URLs
    $num_pages = get_field_value($node, 'field_pages');
    $news_post->page_url_array = array();
    $page_url = url('node/' . $news_post->nid, array('absolute' => true));
    $url_delim = (strpos($page_url, '?') === false) ? '?' : '&';
    for ($n = 1; $n <= $num_pages; $n = $n + 1)
    {
        $news_post->page_url_array[] =
            urldecode($page_url . $url_delim . 'page=' . $n);
    }

    // Pull information about the product from the product node
    try
    {
        $product_nid = get_field_node_reference($node, 'field_product');
        $product_node = node_load($product_nid);
        $news_post->product = platform_node_product_get($product_node);
    }
    catch (Exception $e)
    {
        $news_post->product = null;
    }

    return $news_post;
}

function platform_node_quiz_get($node)
{
    $quiz = new stdClass();
    $quiz->nid = $node->nid;
    $quiz->type_id = get_field_value($node, 'field_quiz_type');
    $quiz->category_id = get_field_value($node, 'field_category');
    $quiz->title = $node->title;
    $quiz->total_points = get_field_value($node, 'field_total_points');
    $quiz->is_hidden = get_field_value($node, 'field_hidden');

    $quiz->questions = platform_entity_quiz_questions_get($node);

    return $quiz;
}

function platform_entity_quiz_questions_get($node)
{
    $question_items =
        get_field_collection_items($node, 'field_quiz_questions');
    $questions = array();
    foreach ($question_items as $question_item)
    {
        $questions[] = $question = new stdClass();
        $question->entity_id =
            entity_id('field_collection_item', $question_item);
        $question->text =
            get_field_collection_item_value($question_item, 'field_text');

        $question->possible_answers =
            platform_entity_quiz_possible_answers_get($question_item);
    }

    return $questions;
}

function platform_entity_quiz_possible_answers_get(
    $question_field_collection_item
)
{
    $answer_items =
        get_field_collection_items(
            $question_field_collection_item,
            'field_quiz_q_possible_answers',
            true
        );
    $possible_answers = array();
    foreach ($answer_items as $answer_item)
    {
        $possible_answers[] = $answer = new stdClass();
        $answer->entity_id = entity_id('field_collection_item', $answer_item);
        $answer->answer_text =
            get_field_collection_item_value(
                $answer_item,
                'field_answer_text'
            );
        $answer->points =
            get_field_collection_item_value($answer_item, 'field_points');
    }

    return $possible_answers;
}

function get_field_text($node, $field_name)
{
    $field_text_items = field_get_items('node', $node, $field_name);
    $field_text = '';
    if (is_array($field_text_items))
    {
        foreach ($field_text_items as &$field_text_item)
        {
            $field_text .= $field_text_item['value'];
        }
    }
    return $field_text;
}

function get_field_value($node, $field_name)
{
    $items = field_get_items('node', $node, $field_name);
    if (is_array($items))
    {
        foreach ($items as &$item)
        {
            return $item['value'];
        }
    }
    throw new Exception('Expected value in ' . $field_name);
}

/**
 * @return $nid of referred node
 */
function get_field_node_reference($node, $field_name)
{
    $field_items = field_get_items('node', $node, $field_name);
    if (is_array($field_items))
    {
        foreach ($field_items as $item)
        {
            return $item['nid'];
        }
    }
    throw new Exception('Unexpected error');
}

function get_field_image_url($node, $field_name)
{
    $field_items = field_get_items('node', $node, $field_name);
    if (is_array($field_items))
    {
        foreach ($field_items as $item)
        {
            if (array_key_exists('fid', $item))
            {
                $file = file_load($item['fid']);
                return urldecode(file_create_url($file->uri));
            }
        }
    }
    throw new Exception(
        'fid missing on ' . $node->nid . '/' . $field_name . ' ' .
        'or URI missing on file');
}

function get_field_image_info($node, $field_name)
{
    $field_items = field_get_items('node', $node, $field_name);
    if (is_array($field_items))
    {
        foreach ($field_items as $item)
        {
            if (array_key_exists('fid', $item))
            {
                $file = file_load($item['fid']);
                $image_info = image_get_info(drupal_realpath($file->uri));
                if (strpos($file->filename, '@2x') !== false)
                {
                    $image_info['width'] = (int)($image_info['width'] / 2);
                    $image_info['height'] = (int)($image_info['height'] / 2);
                }
                return $image_info;
            }
        }
    }
    throw new Exception(
        'fid missing on ' . $node->nid . '/' . $field_name . ' ' .
        'or URI missing on file');
}

function get_field_collection_items(
    $entity,
    $field_name,
    $field_collection=false
)
{
    if ($field_collection)
    {
        $field_items = field_get_items(
            'field_collection_item', $entity, $field_name);
    }
    else
    {
        $field_items = field_get_items('node', $entity, $field_name);
    }
    $field_collection_items = array();
    if (is_array($field_items))
    {
        foreach ($field_items as $item_id)
        {
            $field_collection_items[] =
                field_collection_field_get_entity($item_id);
        }
    }
    return $field_collection_items;
}

function get_field_collection_item_value($field_collection_item, $field_name)
{
    $items = field_get_items(
        'field_collection_item', $field_collection_item, $field_name);
    if (is_array($items))
    {
        foreach ($items as &$item)
        {
            return $item['value'];
        }
    }
    throw new Exception('Expected value in ' . $field_name);
}
