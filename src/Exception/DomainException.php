<?php

declare(strict_types=1);

namespace LmcUser\Exception;

use RuntimeException;

class DomainException extends RuntimeException implements
    ExceptionInterface
{
}
