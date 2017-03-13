<?php
declare(strict_types=1);

namespace Lekso\Testoj;

use PHPUnit\Framework\TestCase;
use Lekso\Ludanto;
use Lekso\Amaso;
use Lekso\Karto;

chdir(dirname(__FILE__));
require_once '../vendor/autoload.php';

class LudantoTest extends TestCase
{
    public function testPreni()
    {
        $ludanto = new Ludanto();
        $amaso = new Amaso(['A' => 1, 'B' => 2, 'C' => 3]);

        $this->assertEquals(3, $ludanto->preni($amaso, 3));
        $this->assertEquals(['C', 'C', 'C'], $ludanto->kartoj);
    }

    public function testPreniTroMulte()
    {
        $ludanto = new Ludanto();
        $amaso = new Amaso(['A' => 1]);

        $this->assertEquals(1, $ludanto->preni($amaso, 3));
        $this->assertEquals(['A'], $ludanto->kartoj);
    }
}
