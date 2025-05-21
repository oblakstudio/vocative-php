<?php

/**
 * Dictionary interface file.
 *
 * @package    Vocative
 * @subpackage Interfaces
 */

namespace Oblak\Vocative\Interfaces;

use Stringable;

/**
 * Describes the interface for a dictionary
 */
interface Dictionary
{
    /**
     * Check if a name is in the dictionary
     *
     * @param string|Stringable $name Name in nominative case
     * @return bool
     */
    public function hasName(string|Stringable $name): bool;

    /**
     * Get the vocative case form of a name
     *
     * @param string|Stringable $name Name in nominative case
     * @return string
     */
    public function getName(string|Stringable $name): string;
}
