<?php

/**
 * IMPORTANT: YOU MUST END EVERY QUERY FUNCTION WITH db_set_active(),
 * ELSE FUTURE QUERIES MAY UNWITTINGLY USE THE WRONG DATABASE.
 */

function platform_db_category_add($form_values)
{
    db_set_active('platform');

    $id = db_insert('category')
        ->fields(array(
            'name' => $form_values['name'],
        ))
        ->execute();

    db_set_active();

    return $id;
}
