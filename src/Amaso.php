<?php
declare(strict_types=1);
namespace Lekso;

/**
 * Reprezenti stakon de kartoj
 */
class Amaso
{
    /** @var array la kartoj en la amaso (nurlega per __get()) */
    protected $kartoj;

    /**
     * Konstruilo
     *
     * @param array $dist distribuo de kartoj, ekz [A => 10, B => 8, ...]
     */
    public function __construct(array $dist) {
        $this->kartoj = [];
        foreach ($dist as $karto => $kvanto) {
            $this->kartoj = array_merge(
                $this->kartoj,
                array_fill(0, $kvanto, $karto)
            );
        }
    }

    /**
     * Miksi la kartojn en la amaso.
     */
    public function miksi() {
        shuffle($this->kartoj);
    }

    /**
     * Preni karton de la amaso.
     *
     * @return string valoro de la karto
     */
    public function preni(): string {
        if ($this->kvanto == 0) {
            throw new \Exception('Ne plu restas kartoj');
        }
        return array_pop($this->kartoj);
    }

    /**
     * Meti karton sur la amason.
     *
     * @param string $karto valoro de la karto
     */
    public function aldoni(string $karto) {
        $this->kartoj[] = $karto;
    }

    public function __get($atrib) {
        switch ($atrib) {
            // kartoj en la amso (nurlega)
            case 'kartoj':
                return $this->kartoj;
            // Kvanto de kartoj en la amaso (nurlega)
            case 'kvanto': 
                return count($this->kartoj);
            default:
                throw new \Exception("Atributo $atrib estas nekonata");
        }
    }
}
