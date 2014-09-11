<?php

namespace MeadSteve\SnsPhwatch;

use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use Silex\Application;
use League\Flysystem\Adapter\AwsS3 as Adapter;
use Symfony\Component\HttpFoundation\Request;

class App extends Application
{
    public function __construct($s3BucketName, $s3Key, $s3Secret)
    {
        parent::__construct();

        $this->get('/hello', function() {
            return 'Hello';
        });

        $app = $this;
        $this->post('/sns/{topic}', function($topic, Request $request) use ($app) {
            // Always json from sns apparently:
            $data = json_decode($request->getContent(), true);

            // Store any subscrition info
            if (isset($data['SubscribeURL'])) {
                $app['s3_file_system']->write('subscriptions/' . $topic . '-suburl.txt', $data['SubscribeURL']);
            }

            // Store the actual message in s3
            $app['s3_file_system']->write($topic . '/' . $data['MessageId'] . '.message', $data['Message']);
            return 'Hello';
        });

        $this['s3_file_system'] = $this->share(function () use ($s3BucketName, $s3Key, $s3Secret) {
            $client = S3Client::factory(array(
                'key'    => $s3Key,
                'secret' => $s3Secret,
            ));
            $filesystem = new Filesystem(new Adapter($client, $s3BucketName));
            return $filesystem;
        });

    }
}
 