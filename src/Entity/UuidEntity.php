<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait UuidEntity
{
    /**
     * namespace needs to be defined explicitly to make serializer work.
     */
    #[ORM\Column(length: 255, unique: true)]
    private string $uuid;

    public function setUuid(UuidInterface $uuid): self
    {
        $this->uuid = $uuid->toString();

        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    private function createUuid()
    {
        $this->uuid = Uuid::uuid4()->toString();
    }
}
