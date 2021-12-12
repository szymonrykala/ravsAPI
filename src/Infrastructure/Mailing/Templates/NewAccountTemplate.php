<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailing\Templates;


class NewAccountTemplate extends Template
{
    protected string $title = 'Witaj w Ravs!';

    /** {@inheritDoc} */
    protected function __render(array $context): string
    {
        /** @var User $user */
        $user = $context['user'];

        return "<!DOCTYPE html> 
        <html lang='pl'>
           {$this->defaultHead()}
            <body>
                <head>
                    <h1>{$this->title}</h1>
                </head>
                <main>
                    <h2>Miło Cię poznać {$user->name}!</h2>
                    <h3>Cieszymy się na naszą współpracę.</h3>

                    <p>Twój kod aktywacyjny to: <b>{$user->uniqueKey}</b></p>
                    <p>Użyj go do aktywacji konta na naszej platformie.</p>
                </main>
                {$this->defaultFooter()}
            </body>
            </html>
        ";
    }
}
