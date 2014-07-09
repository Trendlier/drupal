<?php

/**
 * IMPORTANT: YOU MUST END EVERY DB FUNCTION WITH db_set_active(),
 * ELSE FUTURE DB CALLS MAY UNWITTINGLY USE THE WRONG DATABASE.
 */

function platform_db_product_add($product)
{
    db_set_active('platform');

    $now = new DateTime('now', new DateTimeZone('UTC'));

    $id = db_insert('product')
        ->fields(array(
            'title' => $product->title,
            'category_id' => $product->category_id,
            'subtitle' => $product->subtitle,
            'image_url' => $product->image_url,
            'image_width' => $product->image_width,
            'image_height' => $product->image_height,
            'description' => $product->description,
            'is_hidden' => $product->is_hidden,
            'created_utc' => $now->format('c'),
        ))
        ->execute();

    db_set_active();

    return $id;
}

function platform_db_product_node($id, $nid)
{
    db_set_active();

    db_insert('platform.product_node')
        ->fields(array(
            'product_id' => $id,
            'node_id' => $nid
        ))
        ->execute();

    db_set_active();
}

function platform_db_product_id_get($nid)
{
    db_set_active();

    $record = db_select('platform.product_node', 'pn')
        ->fields('pn')
        ->condition('pn.node_id', $nid)
        ->execute()
        ->fetchObject();

    db_set_active();

    if (is_object($record))
    {
        return $record->product_id;
    }
    else
    {
        throw Exception('Product for node ' . $nid . ' not in platform DB!');
    }
}

function platform_db_product_edit($id, $product)
{
    db_set_active('platform');

    db_update('product')
        ->fields(array(
            'title' => $product->title,
            'category_id' => $product->category_id,
            'subtitle' => $product->subtitle,
            'image_url' => $product->image_url,
            'image_width' => $product->image_width,
            'image_height' => $product->image_height,
            'description' => $product->description,
            'is_hidden' => $product->is_hidden,
        ))
        ->condition('id', $id)
        ->execute();

    db_set_active();
}

function platform_db_product_node_remove($id, $nid)
{
    db_set_active();

    db_delete('platform.product_node')
        ->condition('product_id', $id)
        ->condition('node_id', $nid)
        ->execute();

    db_set_active();
}

function platform_db_product_remove($id)
{
    db_set_active('platform');

    db_update('product')
        ->fields(array(
            'is_deleted' => true,
        ))
        ->condition('id', $id)
        ->execute();

    db_set_active();
}
