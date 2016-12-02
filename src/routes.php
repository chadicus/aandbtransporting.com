<?php
use DominionEnterprises\Filterer;
use DominionEnterprises\Util;
use Zend\Diactoros\Stream;

// Routes
$app->get('/', function ($request, $response, $args) {
    // Render index view
    return $this->renderer->render($response, 'index.html', $args);
});

$app->post('/contact', function ($request, $response, $args) {
    error_log('in contact post');
    $success = false;
    $message = 'Unknown Error';
    error_log(getenv('CONTACT_EMAIL'));
    try {
        $filters = [
            'email' => [['email']],
            'name' => [['string'], ['strip_tags']],
            'subject' => [['string'], ['strip_tags']],
            'message' => [['string'], ['strip_tags']],
        ];
        list($success, $filteredInput, $error) = Filterer::filter($filters, $request->getParsedBody());
        Util::ensure(true, $success, $error);

        /**
        $this->mailgun->sendMessage(
		    getenv('MAILGUN_DOMAIN'),
		    [
        	    'from' => $filteredInput['email'],
                'to' => getenv('CONTACT_EMAIL'),
                'subject' => $filteredInput['subject'],
                'text' => "{$filteredInput['message']}\n\n{$filteredInput['name']}",
		    ]
        );
        */
    } catch (Exception $e) {
        $message = $e->getMessage();
        $success = false;
    }

	$result = [
        'success' => $success,
        'message' => $message,
	];

    $stream = fopen('php://temp', 'r+');
    fwrite($stream, json_encode($result));
    rewind($stream);

    return $response->withHeader('Content-Type', 'application/json')->withBody(new Stream($stream));
});
