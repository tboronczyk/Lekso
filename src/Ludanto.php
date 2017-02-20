<?php
declare(strict_types=1);
namespace Lekso;

/**
 * Reprezenti ludanto de la ludo
 */
class Ludanto
{
    /** @var array la kartoj de la ludanto */
    public $kartoj;
    /** @var int kiom da poentoj havas la ludanto */
    public $poentoj;
    /** @var bool ĉu la ludanto pasigis sian vicon */
    public $pasita;

    /**
     * Konstruilo
     */
    public function __construct() {
        $this->kartoj = [];
        $this->poentoj = 0;
        $this->pasita = false;
    }

    /**
     * Preni karton de la amaso.
     *
     * @param Amaso $amaso la amaso de kartoj
     * @param int $kvanto kiom da kartoj estu prenita
     */
    public function preni(Amaso $amaso, int $kvanto): int {
        if ($kvanto < 1) {
            throw new \Exception('Preno de malpli ol unu karto');
        }
        for ($i = 0; $i < $kvanto; ++$i) {
            if ($amaso->kvanto == 0) {
                return $i;
            }
            $this->kartoj[] = $amaso->preni();
        }
        return $i;
    }
}

