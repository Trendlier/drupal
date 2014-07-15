<?php

/**
 * IMPORTANT: YOU MUST END EVERY QUERY FUNCTION WITH db_set_active(),
 * ELSE FUTURE QUERIES MAY UNWITTINGLY USE THE WRONG DATABASE.
 */

function platform_db_news_article_pages_set($news_post, $news_post_id)
{
    platform_db_news_article_pages_delete($news_post_id);
    platform_db_news_article_pages_insert($news_post, $news_post_id);
}

function platform_db_news_article_pages_delete($news_post_id)
{
    db_set_active('platform');

    db_delete('news_article_page')
        ->condition('news_post_id', $news_post_id)
        ->execute();

    db_set_active();
}

function platform_db_news_article_pages_insert($news_post, $news_post_id)
{
    foreach ($news_post->page_url_array as $page_url)
    {
        platfrom_db_news_article_page_insert($page_url, $news_post_id);
    }
}

function platfrom_db_news_article_page_insert($page_url, $news_post_id)
{
    db_set_active('platform');

    db_insert('news_article_page')
        ->fields(array(
            'news_post_id' => $news_post_id,
            'page_url' => $page_url,
        ))
        ->execute();

    db_set_active();
}
