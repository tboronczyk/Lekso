<?php
declare(strict_types=1);
namespace Lekso;

class Karto
{
    const AJNA = '_';
    const ALDONI = '+';
    const FORIGI = '-';
    const KOMENCO = '<';
    const ANSTATAŬ = '!';

    protected function __construct() { }

    public static function ĉuSpeciala(string $karto): bool {
        return in_array($karto, [self::ALDONI, self::FORIGI,
            self::KOMENCO, self::ANSTATAŬ]);
    }

    public static function ĉuAjna(string $karto): bool {
        return $karto == self::AJNA;
    }

    public static function ĉuLitera(string $karto): bool {
        return (!self::ĉuSpeciala($karto) && !self::ĉuAjna($karto));
    }
}
