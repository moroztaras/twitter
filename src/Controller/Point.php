<?php

namespace App\Controller;

class Point
{
    // область видимость - дозволяє звертатися до змінних класту тільки із оприділих місць
    // Спеціфікатори доступа
    // public - доступ до змінної як всередині класу, так і на зовні
    public $a;
    public $b;
    // private - доступ до змінної тільки в середині самого класу
    public $x;
    public $y;
    // protected - доступ до цієї змінної матимуть тільки нащадки(дочірні класи) цього класу
    protected $c = 10;

    // статична змінна, до якої можна звертатися як зовні, так і в середині, без створення об'єкту цього класу
    public static $d = 120;

    // Constructor class
    public function __constructor(int $x = 0, int $y = 0)
    {
        $this
            ->setX($x)
            ->setY($y)
        ;
    }

    // __toString - інтерполює об'єкт в рядок
    public function __toString()
    {
        return "({$this->a},{$this->b},{$this->c},{$this->getX()},{$this->getY()})";
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function setX(int $x): Point
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setY(int $y): Point
    {
        $this->y = $y;

        return $this;
    }

    // Method
    public function printText(): string
    {
        return 'Hello Symfony 6 & PHP 8 & docker!<br>';
    }

    // Static method
    public static function staticMethodPrintText(): string
    {
        return 'Static method<br>';
    }

    // Distance between two points
    public function distanse()
    {
        return sqrt($this->getX() ** 2 + $this->getY() ** 2);
    }
}
