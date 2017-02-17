<?php
declare(strict_types=1);
namespace Lekso;

class Ludanto
{
    public $kartoj;
    public $poentoj;
    public $pasita;

    public function __construct() {
        $this->kartoj = [];
        $this->poentoj = 0;
        $this->pasita = false;
    }

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

    public function serĉi(\PDO $db): array {
        $vorto = $this->ludo->vorto;
        $celvortoj = $this->ludo->celvortoj;

        $ebloj = [];
        foreach (array_unique($this->kartoj) as $karto) {
            switch ($karto) {
                case Karto::KOMENCO:
                    $ebloj = array_merge($ebloj, $this->serĉiKomence());
                    break;
                case Karto::ALDONI:
                    $ebloj = array_merge($ebloj, $this->serĉiAldone());
                    break;
                case Karto::FORIGI:
                    $ebloj = array_merge($ebloj, $this->serĉiForige());
                    break;
                case Karto::ANSTATAŬ:
                    $ebloj = array_merge($ebloj, $this->serĉiAnstataŭe());
                    break;
                default:
                    $ebloj = array_merge($ebloj, $this->serĉiLitere($karto));
            }
        } 
        return $ebloj;
    }

    protected function serĉiLitere(\PDO $db, string $karto): array {
        $ebloj = [];
        $peto = 'SELECT vorto FROM vortoj WHERE vorto LIKE ' .
            "'{$this->ludo->vorto}$karto%'" . $this->peteroCelVortoj();
        $rezulto = $db->query($peto);
        $vortoj = $rezulto->fetchAll(\PDO::FETCH_COLUMN, 0);
        $rezulto->closeCursor();

        if (!empty($vortoj)) {
            $ebloj[] = [
                mb_strlen($this->ludo->vorto) + 1,
                [$karto],
                $vortoj
            ];
        }
        return $ebloj;
    }

    protected function serĉiKomence(\PDO $db): array {
        $ebloj = [];
        foreach ($this->kartoj as $karto) {
            if (Karto::ĉuSpeciala($karto)) {
                continue;
            }
            $peto = 'SELECT vorto FROM vortoj WHERE vorto LIKE ' .
                "'$karto{$this->ludo->vorto}%'" . $this->peteroCelVortoj();
            $rezulto = $db->query($peto);
            $vortoj = $rezulto->fetchAll(\PDO::FETCH_COLUMN, 0);
            $rezulto->closeCursor();

            if (!empty($vortoj)) {
                $ebloj[] = [
                    mb_strlen($this->ludo->vorto) + 1,
                    [Karto::KOMENCO, $karto],
                    $vortoj
                ];
            }
        }
        return $ebloj;
    }

    protected function serĉiAldone(\PDO $db): array {
        $mapo = ['C' => 'Ĉ', 'G' => 'Ĝ', 'H' => 'Ĥ', 'J' => 'Ĵ',
            'S' => 'Ŝ', 'U' => 'Ŭ'];
        $ebloj = [];

        $literoj = preg_split('//u', $this->ludo->vorto, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < count($literoj); ++$i) {
            $litero = $literoj[$i];
            if (!isset($mapo[$litero])) {
                continue;
            }
            $peto = 'SELECT vorto FROM vortoj WHERE vorto LIKE ' .
                "'" . mb_substr($this->ludo->vorto, 0, $i) .
                $mapo[$litero] . mb_substr($this->ludo->vorto, $i + 1) . "%'" .
                $this->peteroCelVortoj();
            $rezulto = $db->query($peto);
            $vortoj = $rezulto->fetchAll(\PDO::FETCH_COLUMN, 0);
            $rezulto->closeCursor();

            if (!empty($vortoj)) {
                $ebloj[] = [
                    mb_strlen($this->ludo->vorto),
                    [Karto::ALDONI],
                    $vortoj
                ];
            }
        }
        return $ebloj;
    }

    protected function serĉiForige(\PDO $db): array {
        $mapo = ['Ĉ' => 'C', 'Ĝ' => 'G', 'Ĥ' => 'H', 'Ĵ' => 'J',
            'Ŝ' => 'S', 'Ŭ' => 'u'];
        $ebloj = [];

        $literoj = preg_split('//u', $this->ludo->vorto, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < count($literoj); ++$i) {
            $litero = $literoj[$i];
            if (!isset($mapo[$litero])) {
                continue;
            }
            $peto = 'SELECT vorto FROM vortoj WHERE vorto LIKE ' .
                "'" . mb_substr($this->ludo->vorto, 0, $i) .
                $mapo[$litero] . mb_substr($this->ludo->vorto, $i + 1) . "%'" .
                $this->peteroCelVortoj();
            $rezulto = $db->query($peto);
            $vortoj = $rezulto->fetchAll(\PDO::FETCH_COLUMN, 0);
            $rezulto->closeCursor();

            if (!empty($vortoj)) {
                $ebloj[] = [
                    mb_strlen($this->ludo->vorto),
                    [Karto::FORIGI],
                    $vortoj
                ];
            }
        }
        return $ebloj;
    }

    protected function serĉiAnstataŭe(\PDO $db): array {
        $ebloj = [];
        $literoj = preg_split('//u', $this->ludo->vorto, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < count($literoj); ++$i) {
            foreach ($this->kartoj as $karto) {
                if (Karto::ĉuSpeciala($karto)) {
                    continue;
                }
                $peto = 'SELECT vorto FROM vortoj WHERE vorto LIKE ' .
                    "'" . mb_substr($this->ludo->vorto, 0, $i) . $karto .
                    mb_substr($this->ludo->vorto, $i + 1) . "%'" .
                    $this->peteroCelVortoj();
                $rezulto = $db->query($peto);
                $vortoj = $rezulto->fetchAll(\PDO::FETCH_COLUMN, 0);
                $rezulto->closeCursor();

                if (!empty($vortoj)) {
                    $ebloj[] = [
                        mb_strlen($this->ludo->vorto), 
                        [Karto::ANSTATAŬ, $karto],
                        $vortoj
                    ];
                }
            }
        }
        return $ebloj;
    }

    protected function peteroCelVortoj() {
        $parto = '';
        if (!empty($this->ludo->celvortoj)) {
            $parto = ' AND vorto NOT IN ' .
                "('" . join("','", $this->ludo->celvortoj) .  "')";
        }
        return $parto;
    }
}

