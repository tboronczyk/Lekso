#! /usr/bin/php
<?php
/**
 * Elŝuti fontdosieron, kiu enhavas kapvortojn de PIV, kaj eltiri la vortojn
 * por meti en datumbazo. Tiu datumbazo devas esti starigita antaŭ oni lanĉas
 * la simuladon.
 */
declare(strict_types=1);

chdir(dirname(__FILE__));

if (file_exists('datumoj.db')) {
    exit;
}

if (!file_exists('piv2.xml')) {
    file_put_contents('piv2.xml', file_get_contents(
        'http://kursoj.pagesperso-orange.fr/piv2/piv2.xml'
    ));
}
$xml = simplexml_load_file('piv2.xml');

$db = new PDO('sqlite:datumoj.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->query('PRAGMA encoding = "UTF-8"');
$db->query('CREATE TABLE vortoj (vorto TEXT)');

$peto = $db->prepare('INSERT INTO vortoj (vorto) VALUES (:vorto)');
$peto->bindParam(':vorto', $vorto);
foreach ($xml->radiko as $r) {
    $vorto = mb_strtoupper((string)($r->drv[0]['form']), 'UTF-8');
    $peto->execute();
}
