<?php

namespace App\Enums;

enum InventoryItemsUnit: int
{
    case Box = 1;
    case Packet = 2;
    case Liter = 3;
    case Inch = 4;
    case Feet = 5;
    case Piece = 6;
    case Dozen  = 7;
    case Pair  = 8;
    case Meter  = 9;

    public static function getNameById(int $id): ?string
    {
        return match($id) {
            self::Box->value => 'Box',
            self::Packet->value => 'Packet',
            self::Liter->value => 'Liter',
            self::Inch->value => 'Inch',
            self::Feet->value => 'Feet',
            self::Piece->value => 'Piece',
            self::Dozen->value => 'Dozen',
            self::Pair->value => 'Pair',
            self::Meter->value => 'Meter',
            default => null,
        };
    }

    public static function getValueByName(string $name): ?int
    {
        return match($name) {
            'Box' => self::Box->value,
            'Packet' => self::Packet->value,
            'Liter' => self::Liter->value,
            'Inch' => self::Inch->value,
            'Feet' => self::Feet->value,
            'Piece' => self::Piece->value,
            'Dozen' => self::Dozen->value,
            'Pair' => self::Pair->value,
            'Meter' => self::Meter->value,
            default => null,
        };
    }
}
