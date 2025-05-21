<?php

namespace Oblak\Vocative;

use Oblak\Vocative\Interfaces\Dictionary;
use Stringable;

/**
 * Dictionary without exceptions
 */
class NullDictionary implements Dictionary
{
    public function hasName(string|Stringable $name): bool
    {
        return false;
    }

    public function getName(string|Stringable $name): string
    {
        return (string) $name;
    }
}
