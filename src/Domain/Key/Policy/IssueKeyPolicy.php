<?php

declare(strict_types=1);

namespace App\Domain\Key\Policy;



class IssueKeyPolicy extends KeyPolicy
{

    public function __invoke(string $rfidKey)
    {
        $this->room->valiadateRfidKey($rfidKey);

        $this->resevation->start();

        $this->room->occupy();

        return $this->resevation;
    }
}
