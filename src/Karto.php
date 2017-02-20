<?php
declare(strict_types=1);
namespace Lekso;

/**
 * Utilaj funkcioj por identigi kartojn
 */
class Karto
{
    const AJNA = '_';
    const ALDONI = '+';
    const FORIGI = '-';
    const KOMENCO = '<';
    const ANSTATAŬ = '!';

    protected function __construct() { }

    /**
     * Liveri, ĉu la karto estas speciala karto.
     *
     * @param string $karto valoro de la karto
     * @return bool ĉu la karto estas speciala
     */
    public static function ĉuSpeciala(string $karto): bool {
        return in_array($karto, [self::ALDONI, self::FORIGI,
            self::KOMENCO, self::ANSTATAŬ]);
    }

    /**
     * Liveri, ĉu la karto estas la ajna karto.
     *
     * @param string $karto valoro de la karto
     * @return bool ĉu la karto estas la ajna karto
     */
    public static function ĉuAjna(string $karto): bool {
        return $karto == self::AJNA;
    }

    /**
     * Liveri, ĉu la karto estas litera karto.
     *
     * @param string $karto valoro de la karto
     * @return bool ĉu la karto estas litera
     */
    public static function ĉuLitera(string $karto): bool {
        return (!self::ĉuSpeciala($karto) && !self::ĉuAjna($karto));
    }
}
