<?php

function word_count_paginate($str, $page_size, $page_number)
{
    $words = preg_split('/\s+/', $str);
    return
        implode(' ',
            array_slice(
                $words,
                ($page_number - 1) * $page_size,
                $page_size
            )
        );
}

function paginate_text($text, $page_size)
{
    if (array_key_exists('page', $_GET))
    {
        $page_number = $_GET['page'];
        $text =
            word_count_paginate(
                $text,
                $page_size,
                $page_number
            );
    }
    return $text;
}
