<?php

namespace Payment\Enums;

enum PaymentTypeEnum: int
{
    case incoming = 1;
    case outcoming = 2;
    case send = 3;
}
