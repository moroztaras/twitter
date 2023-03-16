<?php

namespace App\Controller\Default;

class People
{
    private string $name;

    public function __construct()
    {
        echo 'Виклик конструктора';
        $this->setName('Taras');
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return People
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
