<?php

require_once(DRUPAL_ROOT . '/sites/all/libraries/aws-sdk-php/aws-autoloader.php');

use Aws\S3\S3Client;

function platform_s3_upload_page($drupal_page_url, $nid)
{
    $IMGS = 'JPG|jpg|JPEG|jpeg|PNG|png|GIF|gif';
    $page_data = file_get_contents($drupal_page_url);
    $page_data = preg_replace_callback(
        '/http:\/\/[^\/]+\/drupal\/[^"]+\/files\/([^"]+\.(' . $IMGS . '))/',
        function($matches)
        {
            $key = $matches[1];
            $resource_url = $matches[0];
            $body = file_get_contents($resource_url);
            try
            {
                return platform_s3_upload_file($key, $body);
            }
            catch (Exception $e)
            {
                return $resource_url;
            }
        },
        $page_data
    );
    $key = 'news_post_node_' . $nid;
    $body = $page_data;
    try
    {
        return platform_s3_upload_file($key, $body);
    }
    catch(Exception $e)
    {
        return $drupal_page_url;
    }
}

function platform_s3_upload_file($key, $body)
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
    ));

    return $result['ObjectURL'];
}
