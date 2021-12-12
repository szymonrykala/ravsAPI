<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailing\Templates;


class AccountActivatedTemplate extends Template
{
    protected string $title = 'Aktywacja konta Ravs!';

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
                    <h2>{$user->name} tak trzymaj!</h2>
                    <h3>Twoje konto zostało aktywowane.</h3>
                    <p>Teraz możesz zalogować się do naszej platformy.</p>
                </main>
                {$this->defaultFooter()}
            </body>
            </html>
        ";
    }
}
