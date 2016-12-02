<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);

$checkProxyHeaders = true;
$trustedProxies = [];
$app->add(new RKA\Middleware\IpAddress($checkProxyHeaders, $trustedProxies));
