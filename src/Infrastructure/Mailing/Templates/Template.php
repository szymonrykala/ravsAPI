<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailing\Templates;


abstract class Template
{
    /** context of the template */
    private array $context;

    /** title of the template */
    protected string $title = 'Unnamed template';

    public function __construct(array $context = [])
    {
        $this->context = $context;
    }

    /** Renders default footer of the email */
    protected function defaultFooter(): string
    {
        return '
            <footer>
                <p></br>Życzymy miłego dnia,</br> Zespół Ravs</p>
            </footer>
        ';
    }

    /** Renders default head of the email */
    protected function defaultHead(): string
    {
        return "
        <head>
            <meta charset='UTF-8'>
            <meta http-equiv='Content-Type'  content='text/html charset=UTF-8' />
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$this->title}</title>
        </head>";
    }

    /** Renders the template body using context */
    abstract protected function __render(array $context): string;

    /** Triggers the rendering method with context */
    public function render(): string
    {
        return $this->__render($this->context);
    }


    /** returns title of the template */
    public function getTitle(): string
    {
        return $this->title;
    }
}
