<?php

/**
 * IMPORTANT: YOU MUST END EVERY QUERY FUNCTION WITH db_set_active(),
 * ELSE FUTURE QUERIES MAY UNWITTINGLY USE THE WRONG DATABASE.
 */

function platform_db_news_post_add_edit($news_post)
{
    if (is_object($news_post->product))
    {
        $product_id = platform_db_product_id_get($news_post->product->nid);
    }
    else
    {
        $product_id = null;
    }

    $id = platform_db_news_post_id_get($news_post->nid);

    if (is_null($id))
    {
        $id = platform_db_news_post_insert($news_post, $product_id);
        drupal_set_message(
                'Added ' . $news_post->title . ' ' .
                '(ID ' . $id . ')');
        platform_db_news_post_node($id, $news_post->nid);
    }
    else
    {
        platform_db_news_post_update($id, $news_post, $product_id);
        drupal_set_message(
                'Updated ' . $news_post->title . ' ' .
                '(ID ' . $id . ')');
    }

    // Maintain page URLs
    platform_db_news_article_pages_set($news_post, $id);
}

function platform_db_news_post_remove($node)
{
    $id = platform_db_news_post_id_get($node->nid);
    if (is_null($id))
    {
        throw new Exception(
            'News post for node ' . $node->nid . ' not in platform DB!');
    }

    platform_db_news_post_node_delete($id, $node->nid);
    platform_db_news_post_delete($id);

    drupal_set_message('Deleted the news post (ID ' . $id . ')');
}

function platform_db_news_post_insert($news_post, $product_id)
{
    db_set_active('platform');

    $now = new DateTime('now', new DateTimeZone('UTC'));

    $id = db_insert('news_post')
        ->fields(array(
            'category_id' => $news_post->category_id,
            'subcategory_id' => $news_post->subcategory_id,
            'news_post_type_id' => $news_post->news_post_type_id,
            'heading' => $news_post->heading,
            'title' => $news_post->title,
            'subtitle' => $news_post->subtitle,
            'text' => $news_post->excerpt,
            'product_id' => $product_id,
            'is_hidden' => $news_post->is_hidden,
            'created_utc' => $now->format('c'),
        ))
        ->execute();

    db_set_active();

    return $id;
}

function platform_db_news_post_node($id, $nid)
{
    db_set_active();

    db_insert('platform.news_post_node')
        ->fields(array(
            'news_post_id' => $id,
            'node_id' => $nid
        ))
        ->execute();

    db_set_active();
}

function platform_db_news_post_id_get($nid)
{
    db_set_active();

    $record = db_select('platform.news_post_node', 'pn')
        ->fields('pn')
        ->condition('pn.node_id', $nid)
        ->execute()
        ->fetchObject();

    db_set_active();

    if (is_object($record))
    {
        return $record->news_post_id;
    }
    else
    {
        return null;
    }
}

function platform_db_news_post_update($id, $news_post, $product_id)
{
    db_set_active('platform');

    db_update('news_post')
        ->fields(array(
            'category_id' => $news_post->category_id,
            'subcategory_id' => $news_post->subcategory_id,
            'news_post_type_id' => $news_post->news_post_type_id,
            'heading' => $news_post->heading,
            'title' => $news_post->title,
            'subtitle' => $news_post->subtitle,
            'text' => $news_post->excerpt,
            'product_id' => $product_id,
            'is_hidden' => $news_post->is_hidden,
        ))
        ->condition('id', $id)
        ->execute();

    db_set_active();
}

function platform_db_news_post_node_delete($id, $nid)
{
    db_set_active();

    db_delete('platform.news_post_node')
        ->condition('news_post_id', $id)
        ->condition('node_id', $nid)
        ->execute();

    db_set_active();
}

function platform_db_news_post_delete($id)
{
    db_set_active('platform');

    db_update('news_post')
        ->fields(array(
            'is_deleted' => true,
        ))
        ->condition('id', $id)
        ->execute();

    db_set_active();
}
