<?php

declare(strict_types=1);

namespace App\Messenger\Form;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class CreateFormMiddleware implements MiddlewareInterface
{

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        
    }
}