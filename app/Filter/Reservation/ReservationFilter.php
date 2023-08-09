<?php

namespace App\Filter\Reservation;

use App\Filter\OthersBaseFilter;

class ReservationFilter extends OthersBaseFilter
{
    public ?int $type = null;

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): void
    {
        $this->type = $type;
    }
}
