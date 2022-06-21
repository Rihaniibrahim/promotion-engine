<?php

namespace App\Filter\Modifier\Factory;

use App\Filter\Modifier\PriceModifierInterface;
use Laminas\Code\Generator\Exception\ClassNotFoundException;

class PriceModifierFactory implements PriceModifierFactoryInterface
{

    public function create(string $modifierType): PriceModifierInterface
    {
        $modifierClassBasenama = str_replace('_', '', ucwords($modifierType, '_'));

        $modifier = self::PRICE_MODIFIER_NAMESPAACE . $modifierClassBasenama;

        if (!class_exists($modifier)) {
            throw new ClassNotFoundException($modifier);

        }
        return new $modifier();


    }
}