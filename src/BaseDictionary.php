<?php

namespace Oblak\Vocative;

use Oblak\Vocative\Interfaces\Dictionary;
use Stringable;

class BaseDictionary implements Dictionary
{
    /**
     * Dictionary of exceptions
     *
     * @var array<string,string>
     */
    private array $names = [
        'ALEKSA'     => 'ALEKSA',
        'ALEKSANDAR' => 'ALEKSANDRE',
        'ANKA'       => 'ANKA',
        'BLAŽA'      => 'BLAŽO',
        'CANA'       => 'CANO',
        'CVETA'      => 'CVETO',
        'GROZDA'     => 'GROZDA',
        'IVKA'       => 'IVKA',
        'JANA'       => 'JANO',
        'JELA'       => 'JELO',
        'JEVTA'      => 'JEVTO',
        'KRSTA'      => 'KRSTO',
        'LARA'       => 'LARA',
        'LAZA'       => 'LAZO',
        'LOLA'       => 'LOLO',
        'LUKA'       => 'LUKA',
        'MATA'       => 'MATO',
        'MATEJ'      => 'MATEJ',
        'MELISA'     => 'MELISA',
        'MICA'       => 'MICO',
        'MILESA'     => 'MILESA',
        'MIĆA'       => 'MIĆO',
        'MOŠA'       => 'MOŠO',
        'NATA'       => 'NATO',
        'OLJA'       => 'OLJA',
        'OSTOJA'     => 'OSTOJA',
        'RATAR'      => 'RATARE',
        'RELJA'      => 'RELJA',
        'SIMA'       => 'SIMO',
        'SLAĐAN'     => 'SLAĐAN',
        'STANA'      => 'STANO',
        'STAVRA'     => 'STAVRO',
        'TOMA'       => 'TOMO',
        'TOŠA'       => 'TOŠO',
        'VERA'       => 'VERA',
        'ZLATA'      => 'ZLATO',
        'ŠANA'       => 'ŠANO',
    ];

    public function hasName(string|Stringable $name): bool
    {
        return isset($this->names[(string)$name]);
    }

    public function getName(string|Stringable $name): string
    {
        return $this->names[(string)$name];
    }
}
