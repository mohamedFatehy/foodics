<?php

namespace App\ValueObject;

class UnitConverter
{
    const KG_to_G_CONVERSION_RATE = 1000;
    private static float $unitOfWeight;


    public static function fromKgToGram(float $value): float
    {
        self::$unitOfWeight = $value;
        self::validateNonNegative();
        return self::$unitOfWeight * self::KG_to_G_CONVERSION_RATE;
    }

    public static function validateNonNegative(): void
    {
        if ( self::$unitOfWeight < 0) {
            throw new \InvalidArgumentException('UnitConverter value must be non-negative');
        }
    }
}
