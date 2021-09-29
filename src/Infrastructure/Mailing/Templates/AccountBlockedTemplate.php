<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailing\Templates;


class AccountBlockedTemplate extends Template
{
    protected string $title = 'Konto Ravs zablokowane';

    /** {@inheritDoc} */
    protected function __render(array $context): string
    {
        /** @var User $user */
        $user = $context['user'];

        $reason = $context['reason'];

        return "<!DOCTYPE html> 
        <html lang='pl'>
           {$this->defaultHead()}
            <body>
                <head>
                    <h1>{$this->title}</h1>
                </head>
                <main>
                    <h2>Witaj {$user->name}!</h2>
                    <h3>Mamy złą wiadomość,</h3>

                    <p>Twoje konto Ravs zostało zablokowane z powodu: <b>{$reason}</b>.</p>
                    <p>
                        Aby odblokować konto, postępuj zgodnie z instrukcją zamieszczoną w aplikacji.</br>
                        Jeśli masz pytanie, skontaktuj się z administratorem.
                    </p>

                </main>
                {$this->defaultFooter()}
            </body>
            </html>
        ";
    }
}
