<?php

namespace App\Controller\Default;

use JetBrains\PhpStorm\Pure;

class Cat extends Animal
{
    private string $name = 'Cat';

    #[Pure]
    public function voice(): string
    {
        return "{$this->getName()} makes a meow meow sound";
    }

    // перезавантажений метод із батьківського класу
    public function info(): string
    {
        return "I'm {$this->getName()} I have {$this->getLegs()} legs";
    }

    public function color()
    {
        return 'Grey';
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Cat
     */
    public function setName(string $name): string
    {
        $this->name = $name;

        return $this;
    }
}
