<?php

namespace App\Http\Responses;

class SuccessResponse extends AbstractFormattedApiResponse
{
    private array $data;

    public function __construct(int $statusCode, string $message, array $data = [])
    {
        parent::__construct($statusCode, $message);
        $this->data = $data;
    }

    public function setStatusCode(int $statusCode): AbstractFormattedApiResponse
    {
        if ($statusCode == null) $statusCode = 200;

        return parent::setStatusCode($statusCode);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): SuccessResponse
    {
        $this->data = $data;
        return $this;
    }

    protected function toArray(): array
    {
        $array = parent::toArray();
        if (!empty($this->data)) $array['data'] = $this->data;

        return $array;
    }
}
