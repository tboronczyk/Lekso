<?php
declare(strict_types=1);
namespace Lekso;

class Amaso
{
    protected $kartoj;

    public function __construct(array $dist) {
        $this->kartoj = [];
        foreach ($dist as $karto => $kvanto) {
            $this->kartoj = array_merge(
                $this->kartoj,
                array_fill(0, $kvanto, $karto)
            );
        }
    }

    public function miksi() {
        shuffle($this->kartoj);
    }

    public function preni(): string {
        if ($this->kvanto == 0) {
            throw new \Exception('Ne plu restas kartoj');
        }
        return array_pop($this->kartoj);
    }

    public function aldoni(string $karto) {
        $this->kartoj[] = $karto;
    }

    public function __get($atrib) {
        switch ($atrib) {
            case 'kartoj':
                return $this->kartoj;
            case 'kvanto': 
                return count($this->kartoj);
            default:
                throw new \Exception("Atributo $atrib estas nekonata");
        }
    }
}
