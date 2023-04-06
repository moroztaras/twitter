<?php

declare(strict_types=1);

namespace App\Exception\Api;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class JsonHttpException extends HttpException
{
    /**
     * @var mixed|null
     */
    private mixed $data;

    /**
     * @param int        $statusCode
     * @param string     $message
     * @param mixed|null $data
     */
    public function __construct($statusCode, $message = null, $data = null)
    {
        parent::__construct($statusCode, $message);

        $this->setData($data);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }
}
