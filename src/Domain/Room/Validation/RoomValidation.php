<?php

declare(strict_types=1);

namespace App\Domain\Room\Validation;

use App\Domain\Model\SchemaValidator;



class RoomValidation extends SchemaValidator
{
    /**
     * {@inheritDoc}
     */
    protected array $fields = [
        'name', 'roomType', 'seatsCount', 'floor'
    ];

    /**
     * {@inheritDoc}
     */
    protected function defineSchema($validator): void
    {
        $this->setAsNameString('name', 'Nazwa sali zawiera niedozwolone znaki');

        $this->setAsType(['seatsCount', 'floor', 'buildingId'], 'integer');
        $this->setAsType(['blocked'], 'boolean');

        $validator
            ->inList(
                'roomType',
                ["Sala laboratoryjna", "Sala konferencyjna", "Sala wykładowa"],
                'Dozwolone typy sal to \'Sala laboratoryjna\', \'Sala konferencyjna\', \'Sala wykładowa\''
            )
            ->range('seatsCount', [1, 9999], 'Zakres ilości miejsc to 1-9999')
            ->range('floor', [-1, 99], 'Zakres pięter to -1-99');
    }
}
