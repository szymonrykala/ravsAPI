<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailing\Templates;


class NewCodeTemplate extends Template
{
    protected string $title = 'Twój nowy kod od Ravs';

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
                    <h2>Witaj {$user->name}!</h2>
                    <h3>Poprosiłeś/aś nas o nowy kod - śpieszymy z pomocą!</h3>
                    <p>Twój kod to: <b>{$user->uniqueKey}</b></p>
                    <p>Jeśli nie składałeś/aś takiej prośby, zignoruj tą wiadomość.</p>
                </main>
                {$this->defaultFooter()}
            </body>
            </html>
        ";
    }
}
