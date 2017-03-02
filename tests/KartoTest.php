<?php
declare(strict_types=1);

chdir(dirname(__FILE__));
require_once '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Lekso\Karto;

class KartoTest extends TestCase
{
    public function testĈuSpeciala()
    {
        $kartoj = [Karto::ALDONI, Karto::FORIGI, Karto::KOMENCO,
            Karto::ANSTATAŬ, Karto::AJNA, 'A', 'Z'];

        $rezultoj = [];
        foreach ($kartoj as $karto) {
            $rezultoj[] = Karto::ĉuSpeciala($karto);
        }

        $this->assertEquals(
            [true, true, true, true, false, false, false],
            $rezultoj
        );
    }

    public function testĈuAja()
    {
        $kartoj = [Karto::AJNA, 'A'];

        $rezultoj = [];
        foreach ($kartoj as $karto) {
            $rezultoj[] = Karto::ĉuAjna($karto);
        }

        $this->assertEquals([true, false], $rezultoj);
    }

    public function testĈuLitera()
    {
        $kartoj = [Karto::ALDONI, Karto::AJNA, 'A', 'Z'];

        $rezultoj = [];
        foreach ($kartoj as $karto) {
            $rezultoj[] = Karto::ĉuLitera($karto);
        }

        $this->assertEquals([false, false, true, true], $rezultoj);
    }
}
