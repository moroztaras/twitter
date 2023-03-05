<?php

namespace App\Controller;

// Батьківський клас.

// final class - це клас який не може мати нащадків
// final class className ()
// {
//    тіло класу
// }

// Abstract - ми не можемо створювати об'єкти даного класу, тільки нащадків.
// абстрактний клас використовується тільки для наслідування

abstract class Animal
{
    private int $legs = 4;

    // доступ до цього методу мають нащадки цього класу
    public function info(): string
    {
        return "I have {$this->legs} legs";
    }

    // abstract method - метод без тіла(без реалізації), але він повинен обов'язково бути реалізований у кожному із класами нащадках
    abstract public function color();

    // final - метод який не може бути перевантажений/переоридільонний у класах його нащадків
//    final public function nameMethod() {
//
//    }

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
