<?php

declare(strict_types=1);

namespace App\OAuth\Controller;

use React\Http\Message\Response;

class DefaultController
{
    public function __invoke()
    {
        return Response::plaintext("HELLO WÖRLD!");
    }
}