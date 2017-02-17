<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

chdir(dirname(__FILE__));
require_once '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Lekso\Amaso;

class AmasoTest extends TestCase
{
    public function testKartoj() {
        $a = new Amaso(['A' => 1, 'B' => 2, 'C' => 3]);

        $this->assertEquals(['A', 'B', 'B', 'C', 'C', 'C'], $a->kartoj);
    }

    public function testKvanto() {
        $a = new Amaso(['A' => 1, 'B' => 2, 'C' => 3]);

        $this->assertEquals(6, $a->kvanto);
    }

    public function testMiksi() {
        $a = new Amaso(['A' => 1, 'B' => 2, 'C' => 3]);
        $a->miksi();

        $this->assertNotEquals(['A', 'B', 'B', 'C', 'C', 'C'], $a->kartoj);
    }

    public function testPreni() {
        $a = new Amaso(['A' => 1, 'B' => 2, 'C' => 3]);

        $this->assertEquals('C', $a->preni());
        $this->assertEquals(5, $a->kvanto);
    }

    public function testAldoni() {
        $a = new Amaso(['A' => 1, 'B' => 2, 'C' => 3]);
        $a->aldoni('Ĉ');

        $this->assertEquals(7, $a->kvanto);
    }
}
