<?php

namespace App\Controller\Default;

use JetBrains\PhpStorm\Pure;

class Dog extends Animal
{
    private string $name = 'Dog';

    #[Pure]
    public function voice(): string
    {
        return "{$this->getName()} makes a woof woof sound";
    }

    // перезавантажений метод із батьківського класу
    public function info(): string
    {
        return "I'm {$this->getName()} I have {$this->getLegs()} legs";
    }

    public function parentInfo()
    {
        // визов батьківського ментоду (не перегруженого)
        parent::info();
    }

    public function color()
    {
        return 'Black';
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Dog
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
