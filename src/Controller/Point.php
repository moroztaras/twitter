<?php

namespace App\Controller;

use JetBrains\PhpStorm\Pure;

class Point
{
    // область видимость - дозволяє звертатися до змінних класту тільки із оприділих місць
    // Спеціфікатори доступа
    // public - доступ до змінної як внутрі класу, так і на зовні
    public $a;
    public $b;
    // private - доступ до змінної тільки внутрі самого класу
    public $x;
    public $y;
    // protected - доступ до цієї змінної матимуть тільки нащадки(дочірні класи) цього класу
    protected $c = 10;

    // статична змінна, до якої можна звертатися як зовні так і внутрі, без створення обєкту цього класа
    public static $d = 120;

    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @param int $x
     *
     * @return Point
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param int $y
     *
     * @return Point
     */
    public function setY($y)
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
