<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

chdir(dirname(__FILE__));
require_once '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Lekso\Karto;
use Lekso\Vorto;

class VortoTest extends TestCase
{
    public function testSerĉiLitere() {
        $v = new Vorto(new \PDO('sqlite:../datumoj.db'));
        $v->vorto = 'TEG';
        $v->celvortoj = ['TEO', 'TEGI'];

        $rezulto = $v->serĉi(['S', 'M']);
        $this->assertEquals([[['M'], ['TEGMENTO'], 4]], $rezulto);
    }

    public function testSerĉiLitereAjnan() {
        $v = new Vorto(new \PDO('sqlite:../datumoj.db'));
        $v->vorto = 'TEG';
        $v->celvortoj = ['TEO', 'TEGI'];

        $rezulto = $v->serĉi([Karto::AJNA]);
        $this->assertEquals(
            [[[Karto::AJNA], ['TEGO', 'TEGENARIO', 'TEGMENTO', 'TEGOLO',
            'TEGUO', 'TEGUCIGALPO', 'TEGUMENTO'], 4]],
            $rezulto
        );
    }

    public function testSerĉiKomence() {
        $v = new Vorto(new \PDO('sqlite:../datumoj.db'));
        $v->vorto = 'TEG';
        $v->celvortoj = ['TEO', 'TEGI'];

        $rezulto = $v->serĉi([Karto::KOMENCO, 'S']);
        $this->assertEquals(
            [[[Karto::KOMENCO, 'S'], ['STEGO', 'STEGOCEFALOJ', 'STEGOSAŬRO'], 4]],
            $rezulto
        );
    }

    public function testSerĉiAldone() {
        $v = new Vorto(new \PDO('sqlite:../datumoj.db'));
        $v->vorto = 'STEL';
        $v->celvortoj = ['TEO', 'TELEFONO', 'STELO'];

        $rezulto = $v->serĉi([Karto::ALDONI]);
        $this->assertEquals([[[Karto::ALDONI], ['ŜTELI'], 4]], $rezulto);
    }

    public function testSerĉiForige() {
        $v = new Vorto(new \PDO('sqlite:../datumoj.db'));
        $v->vorto = 'ŜTEL';
        $v->celvortoj = ['TEO', 'TELEFONO', 'ŜTELO'];

        $rezulto = $v->serĉi([Karto::FORIGI]);
        $this->assertEquals(
            [[[Karto::FORIGI], ['STELO', 'STELARIO', 'STELEO', 'STELIONO'], 4]], 
            $rezulto
        );
    }

    public function testSerĉiAnstataŭe() {
        $v = new Vorto(new \PDO('sqlite:../datumoj.db'));
        $v->vorto = 'ŜTEL';
        $v->celvortoj = ['TEO', 'TELEFONO', 'ŜTELO'];

        $rezulto = $v->serĉi([Karto::ANSTATAŬ, 'A']);
        $this->assertEquals(
            [[[Karto::ANSTATAŬ, 'A'], ['ATELO', 'ATELIERO'], 4],
            [[Karto::ANSTATAŬ, 'A'], ['ŜTALO'], 4]],
            $rezulto
        );
    }
}
