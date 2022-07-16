<?php

namespace App\Controller;

// Батьківський клас.

use Doctrine\ORM\Mapping as ORM;

class Animal
{
    /**
     * @ORM\Column(type="integer")
     */
    private int $legs = 4;

    // доступ до цього методу мають нащадки цього класу
    public function info(): string
    {
        return "I have {$this->legs} legs";
    }

    public function getLegs(): int
    {
        return $this->legs;
    }

    /**
     * @return Animal
     */
    public function setLegs(int $legs)
    {
        /* @var int $legs */
        $this->legs = $legs;

        return $this;
    }
}
