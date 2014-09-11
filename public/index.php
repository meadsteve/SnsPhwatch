<?php
require_once __DIR__ . "/../vendor/autoload.php";
$awsConfig = require_once __DIR__ . "/../aws-config.php";

$app = new \MeadSteve\SnsPhwatch\App(
    $awsConfig['s3BucketName'],
    $awsConfig['s3Key'],
    $awsConfig['s3Secret']
);

$app->run();