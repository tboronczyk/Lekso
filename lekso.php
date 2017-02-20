#! /usr/bin/php
<?php
/**
 * Simulado de la ludo Lekso
 */
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

chdir(dirname(__FILE__));
require_once 'vendor/autoload.php';

use Lekso\Amaso;
use Lekso\Karto;
use Lekso\Ludanto;
use Lekso\Vorto;

define('KVANT_LUDANTOJ', 4);
define('KVANT_KARTOJ', 7);
define('KART_DISTRIBUO', [
    'A' => 10, 'B' => 8,  'C' => 6,  'Ĉ' => 4,  'D' => 8,  'E' => 10,
    'F' => 6,  'G' => 6,  'Ĝ' => 4,  'H' => 4,  'Ĥ' => 2,  'I' => 10,
    'J' => 6,  'Ĵ' => 2,  'K' => 6,  'L' => 8,  'M' => 8,  'N' => 10,
    'O' => 8,  'P' => 8,  'R' => 8,  'S' => 8,  'Ŝ' => 4,  'T' => 8,
    'U' => 8,  'Ŭ' => 4,  'V' => 6,  'Z' => 6,
    Karto::AJNA => 4,     Karto::ALDONI => 4,   Karto::FORIGI => 4,
    Karto::KOMENCO => 4,  Karto::ANSTATAŬ => 4
]);

$amaso = new Amaso(KART_DISTRIBUO);
$amaso->miksi();

$ludantoj = [];
for ($i = 0; $i < KVANT_LUDANTOJ; ++$i) {
    $ludantoj[$i] = new Ludanto();
    $ludantoj[$i]->preni($amaso, KVANT_KARTOJ);
}

$db = new PDO('sqlite:datumoj.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$vorto = new Vorto($db);

for (;;) {
    $karto = $amaso->preni();
    if (Karto::ĉuLitera($karto)) {
        $vorto->vorto = $karto;
        $vorto->poentoj = 1;
        break;
    }
    $amaso->aldoni($karto);
    $amaso->miksi();
}

echo 'La ludo komenciĝas kun ' . count($ludantoj) . " ludantoj\n";
$vicaroj = 0;
for ($i = 0;; ++$i) {
    if (array_reduce($ludantoj, function ($c, $l) {
        return $c && $l->pasita;
    }, true)) {
        break;
    }
    if ($i == count($ludantoj)) {
        ++$vicaroj;
        $i = 0;
    }

    echo "Vorto: $vorto [" . join(', ', $vorto->celvortoj) . "]\n";
    $ebloj = $vorto->serĉi($ludantoj[$i]->kartoj);
    if (!count($ebloj)) {
        if (count($vorto->celvortoj)) {
            $j = ($i == 0 ? count($ludantoj) : $i) - 1;
            $ludantoj[$j]->poentoj += $vorto->poentoj - 1;
            echo 'Ludanto ' . ($j + 1) . " gajnas poentojn\n";
        }
        $vorto->vorto = mb_substr($vorto->vorto, -1);
        $vorto->poentoj = 1;
        $vorto->celvortoj = [];

        $ebloj = $vorto->serĉi($ludantoj[$i]->kartoj);
        if (!count($ebloj)) {
            $ludantoj[$i]->pasita = true;
            echo 'Ludanto ' . ($i + 1) . " pasigas la vicon\n";
            continue;
        }
    }
    $ludantoj[$i]->pasita = false;

    $eblo = $ebloj[array_rand($ebloj)];
    $v = $eblo[1][array_rand($eblo[1])];

    $vorto->vorto = mb_substr($v, 0, $eblo[2]);
    $vorto->celvortoj[] = $v;
    $vorto->kalkuliPoentojn($eblo[0]);

    echo 'Ludanto ' . ($i + 1) . ' [' . join(', ', $ludantoj[$i]->kartoj) . 
        '] metas ' . join (', ', $eblo[0]) . "\n";
    foreach ($eblo[0] as $litero) {
        $j = array_search($litero, $ludantoj[$i]->kartoj);
        unset($ludantoj[$i]->kartoj[$j]);
    }
    $ludantoj[$i]->preni($amaso, KVANT_KARTOJ - count($ludantoj[$i]->kartoj));
}

echo "---\nLa ludo finiĝas post $vicaroj vicaroj\n";
foreach ($ludantoj as $i => $ludanto) {
    echo 'Ludanto ' . ($i + 1) . ": {$ludanto->poentoj} poentoj [" .
        join(', ', $ludanto->kartoj) . "]\n";
}
echo $amaso->kvanto . " kartoj restas en la amaso\n";
