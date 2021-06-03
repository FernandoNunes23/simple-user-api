<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\Actions\User\UserPostAction;
use \App\Application\Actions\User\UserGetAllAction;
use \App\Application\Actions\User\UserDeleteAction;
use \App\Application\Actions\User\UserPutAction;
use Slim\App;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

	$app->post('/user', UserPostAction::class);
	$app->get('/user', UserGetAllAction::class);
	$app->delete('/user', UserDeleteAction::class);
	$app->put('/user/{id}', UserPutAction::class);
};
