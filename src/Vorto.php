<?php
declare(strict_types=1);

namespace Lekso;

/**
 * Reprezenti ludvorton
 */
class Vorto
{
    protected $db;

    /** @var string la nuntempa vorto */
    public $vorto;
    /** @var array aro de vortoj, kiuj ĝis nun estis faritaj */
    public $celvortoj;
    /** @var int kiom da poentoj valoras la vorto */
    public $poentoj;

    /**
     * Konstruilo
     *
     * @param PDO $db konekto al datumbazo, kiu havas permesitajn vortojn
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->vorto = '';
        $this->celvortoj = [];
        $this->poentoj = 0;
    }

    public function __toString(): string
    {
        return $this->vorto;
    }

    /**
     * Kalkuli, kiom da poentoj valoras la kartoj, kaj aldoni tiom al
     * al poentoj de la vorto.
     *
     * @param array $kartoj valoroj de kartoj
     */
    public function kalkuliPoentojn(array $kartoj)
    {
        $this->poentoj = array_reduce($kartoj, function (int $c, string $i): int {
            return $c + (int)(!Karto::ĉuSpeciala($i) && !Karto::ĉuAjna($i));
        }, $this->poentoj);
    }

    /**
     * Serĉi vorteblojn por la difinitaj kartoj.
     *
     * @param array $kartoj valoroj de kartoj
     * @return array ebloj en tiu formo: [[kartoj], [vortoj], longeco]
     */
    public function serĉi(array $kartoj): array
    {
        $ebloj = [];
        foreach (array_unique($kartoj) as $karto) {
            switch ($karto) {
                case Karto::ALDONI:
                case Karto::FORIGI:
                    $ebloj = array_merge($ebloj, $this->serĉiSupersigne($karto));
                    break;
                case Karto::KOMENCO:
                    $ebloj = array_merge($ebloj, $this->serĉiKomence($kartoj));
                    break;
                case Karto::ANSTATAŬ:
                    $ebloj = array_merge($ebloj, $this->serĉiAnstataŭe($kartoj));
                    break;
                default:
                    $ebloj = array_merge($ebloj, $this->serĉiLitere($karto));
            }
        }
        return $ebloj;
    }

    protected function serĉiSupersigne(string $karto): array
    {
        $mapo = ['C' => 'Ĉ', 'G' => 'Ĝ', 'H' => 'Ĥ',
                 'J' => 'Ĵ', 'S' => 'Ŝ', 'U' => 'Ŭ'];
        if ($karto == Karto::FORIGI) {
            $mapo = array_flip($mapo);
        }
        $ebloj = [];

        $literoj = preg_split('//u', $this->vorto, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < count($literoj); ++$i) {
            if (!isset($mapo[$literoj[$i]])) {
                continue;
            }
            $peto = "SELECT vorto FROM vortoj WHERE vorto LIKE '" .
                mb_substr($this->vorto, 0, $i) . $mapo[$literoj[$i]] .
                mb_substr($this->vorto, $i + 1) . "%'" . $this->petero();
            $rezulto = $this->db->query($peto);
            $vortoj = $rezulto->fetchAll(\PDO::FETCH_COLUMN, 0);
            $rezulto->closeCursor();

            if (!empty($vortoj)) {
                $ebloj[] = [[$karto], $vortoj, mb_strlen($this->vorto)];
            }
        }
        return $ebloj;
    }

    protected function serĉiKomence(array $kartoj): array
    {
        $ebloj = [];
        foreach (array_unique($kartoj) as $karto) {
            if (Karto::ĉuSpeciala($karto)) {
                continue;
            }
            $peto = "SELECT vorto FROM vortoj WHERE vorto LIKE '" . $karto .
                $this->vorto . "%'" . $this->petero();
            $rezulto = $this->db->query($peto);
            $vortoj = $rezulto->fetchAll(\PDO::FETCH_COLUMN, 0);
            $rezulto->closeCursor();

            if (!empty($vortoj)) {
                $ebloj[] = [
                    [Karto::KOMENCO, $karto],
                    $vortoj,
                    mb_strlen($this->vorto) + 1
                ];
            }
        }
        return $ebloj;
    }

    protected function serĉiAnstataŭe(array $kartoj): array
    {
        $ebloj = [];
        $literoj = preg_split('//u', $this->vorto, -1, PREG_SPLIT_NO_EMPTY);
        for ($i = 0; $i < count($literoj); ++$i) {
            foreach ($kartoj as $karto) {
                if (Karto::ĉuSpeciala($karto)) {
                    continue;
                }
                $peto = "SELECT vorto FROM vortoj WHERE vorto LIKE '" .
                    mb_substr($this->vorto, 0, $i) . $karto .
                    mb_substr($this->vorto, $i + 1) . "%'" . $this->petero();
                $rezulto = $this->db->query($peto);
                $vortoj = $rezulto->fetchAll(\PDO::FETCH_COLUMN, 0);
                $rezulto->closeCursor();

                if (!empty($vortoj)) {
                    $ebloj[] = [
                        [Karto::ANSTATAŬ, $karto],
                        $vortoj,
                        mb_strlen($this->vorto)
                    ];
                }
            }
        }
        return $ebloj;
    }

    protected function serĉiLitere(string $karto): array
    {
        $ebloj = [];
        $peto = "SELECT vorto FROM vortoj WHERE vorto LIKE '" . $this->vorto .
            $karto . "%'" . $this->petero();
        $rezulto = $this->db->query($peto);
        $vortoj = $rezulto->fetchAll(\PDO::FETCH_COLUMN, 0);
        $rezulto->closeCursor();

        if (!empty($vortoj)) {
            $ebloj[] = [[$karto], $vortoj, mb_strlen($this->vorto) + 1];
        }
        return $ebloj;
    }

    protected function petero()
    {
        $parto = '';
        if (!empty($this->celvortoj)) {
            $parto = " AND vorto NOT IN ('" . join("','", $this->celvortoj) . "')";
        }
        return $parto;
    }
}
