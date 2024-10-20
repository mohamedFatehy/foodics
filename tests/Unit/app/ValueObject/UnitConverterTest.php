<?php

namespace Tests\Unit\app\ValueObject;

use App\ValueObject\UnitConverter;
use InvalidArgumentException;

it('test converts 0 kilograms to 0 grams', function () {
    $result = UnitConverter::fromKgToGram(0);
    expect($result)->toBe(0.0);
});

it('test will throws an exception for negative values', function () {
    UnitConverter::fromKgToGram(-5);
})->throws(InvalidArgumentException::class, 'UnitConverter value must be non-negative');

it('test can decimal values of kilograms', function () {
    $result = UnitConverter::fromKgToGram(0.5);
    expect($result)->toBe(500.0);
});

it('test convert kilograms to grams', function () {
    $result = UnitConverter::fromKgToGram(20);
    expect($result)->toBe(20000.0);
});
