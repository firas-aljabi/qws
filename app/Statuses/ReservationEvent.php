<?php

namespace App\Statuses;

class ReservationEvent

{
    public const WEDDING = 1;
    public const EVINING = 2;
    public const OTHERS = 3;
    public const MORNING = 4;
    public const BIRTHDAY = 5;

    public static array $statuses = [self::WEDDING, self::EVINING, self::OTHERS];
}
