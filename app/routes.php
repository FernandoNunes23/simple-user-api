<?php
declare(strict_types=1);

use App\Application\Actions\Account\AccountEventAction;
use App\Application\Actions\Account\AccountGetBalanceAction;
use App\Application\Actions\Reset\ResetAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/balance', AccountGetBalanceAction::class);

    $app->post('/event', AccountEventAction::class);

    $app->post('/reset', ResetAction::class);
};
