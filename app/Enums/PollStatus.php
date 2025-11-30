<?php

namespace App\Enums;

enum PollStatus: int
{
    case Draft = 1;
    case Scheduled = 2;
    case Active = 3;
    case Closed = 4;
}
