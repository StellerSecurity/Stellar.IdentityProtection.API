<?php

namespace App;

enum IdentityStatus: int
{

    case NOT_CHECKED = -1;

    case UNKNOWN = 0;
    case CLEAN = 1;

    CASE BREACHED = 3;

}
