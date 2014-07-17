<?php

require_once(DRUPAL_ROOT . '/sites/all/libraries/aws-sdk-php/aws-autoloader.php');

use Aws\S3\S3Client;

function platform_s3_render_node($nid, $n)
{
    $node = node_load($nid);
    $_GET['page'] = $n;
    ob_start();
    require(DRUPAL_ROOT . '/sites/all/themes/platform/page--node--news_post.tpl.php');
    $output = ob_get_contents();
    ob_clean();
    return $output;
}

function platform_s3_upload_page($drupal_page_url, $nid, $n)
{
    $IMGS = 'JPG|jpg|JPEG|jpeg|PNG|png|GIF|gif';
    //$page_data = file_get_contents($drupal_page_url);
    $page_data = platform_s3_render_node($nid, $n);
    $page_data = preg_replace_callback(
        '/http:\/\/[^\/]+\/drupal\/[^"]+\/files\/([^"]+\.(' . $IMGS . '))/',
        function($matches)
        {
            $key = $matches[1];
            $resource_url = $matches[0];
            $body = file_get_contents($resource_url);
            $content_type = 'image/' . $matches[2];
            try
            {
                return platform_s3_upload_file($key, $body, $content_type);
            }
            catch (Exception $e)
            {
                return $resource_url;
            }
        },
        $page_data
    );
    $key = 'news_post_node_' . $nid . '_' . $n . '.html';
    $body = $page_data;
    $content_type = 'text/html';
    try
    {
        return platform_s3_upload_file($key, $body, $content_type);
    }
    catch(Exception $e)
    {
        return $drupal_page_url;
    }
}

function platform_s3_upload_file($key, $body, $content_type)
{
    $options = array(
        'key' => variable_get('aws_key'),
        'secret' => variable_get('aws_secret'),
    );

    $client = S3Client::factory($options);
    //$client->addSubscriber(\Guzzle\Plugin\Log\LogPlugin::getDebugPlugin());

    $result = $client->putObject(array(
        'Bucket' => 'trendliercmsbucket',
        'Key' => $key,
        'Body' => $body,
        'ACL' => 'public-read',
        'ContentType' => $content_type,
    ));

    return $result['ObjectURL'];
}
