<?php
// DIC configuration
$container = $app->getContainer();
// Register View Renderer
// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

$container['mailgun'] = function ($c) {
    return new Mailgun\Mailgun(getenv('MAILGUN_APIKEY'));
};

$container['recaptcha'] = function ($c) {
    return new \ReCaptcha\ReCaptcha(getenv('RECAPTCHA_PRIVKEY'));
};
