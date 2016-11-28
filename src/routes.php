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
    $success = false;
    $message = 'Unknown Error';
    try {
        $filters = [
            'from' => [['email']],
            'subject' => [['string'], ['strip_tags']],
            'text' => [['string'], ['strip_tags']],
        ];
        list($success, $filteredInput, $error) = Filterer::filter($filters, $request->getParsedBody(), true);
        Util::ensur(true, $success, $error);

        $this->mailgun->sendMessage(
		    getenv('MAILGUN_DOMAIN'),
		    [
        	    'from' => $from,
                'to' => getenv('CONTACT_EMAIL'),
                'subject' => $subject,
                'text' => $text,
		    ]
        );
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
