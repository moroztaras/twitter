<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

class JsonResponse extends BaseJsonResponse
{
    /**
     * @param bool $preEncoded If the data is already a JSON string
     */
    public function __construct($data = null, $status = 200, $headers = [], bool $preEncoded = false)
    {
        parent::__construct(null, $status, $headers);
        $this->setData($data, $preEncoded);
    }

    /**
     * @param bool $preEncoded If the data is already a JSON string
     */
    public function setData($data = [], bool $preEncoded = false): static
    {
        if ($preEncoded) {
            $this->data = $data;

            return $this->update();
        }

        return parent::setData($data);
    }
}
