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
    protected array $names = [
        'ALEKSA'     => 'ALEKSA',
        'ALEKSANDAR' => 'ALEKSANDRE',
        'ANKA'       => 'ANKA',
        'ANĐA'       => 'ANĐO',
        'BLAŽA'      => 'BLAŽO',
        'CANA'       => 'CANO',
        'CVETA'      => 'CVETO',
        'DORIS'      => 'DORIS',
        'FEMA'       => 'FEMO',
        'GROZDA'     => 'GROZDO',
        'IVKA'       => 'IVKA',
        'JANA'       => 'JANO',
        'JELA'       => 'JELO',
        'JEVTA'      => 'JEVTO',
        'KRSTA'      => 'KRSTO',
        'LARA'       => 'LARO',
        'LAZA'       => 'LAZO',
        'LENA'       => 'LENO',
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
        'RANĐA'      => 'RANĐO',
        'RATAR'      => 'RATARE',
        'RELJA'      => 'RELJA',
        'RUŽA'       => 'RUŽO',
        'SIMA'       => 'SIMO',
        'SLAĐAN'     => 'SLAĐAN',
        'STANA'      => 'STANO',
        'STAVRA'     => 'STAVRO',
        'TOMA'       => 'TOMO',
        'TOŠA'       => 'TOŠO',
        'UNA'        => 'UNO',
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
