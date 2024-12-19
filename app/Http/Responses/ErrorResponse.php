<?php

namespace App\Http\Responses;

class ErrorResponse extends AbstractFormattedApiResponse
{
    private array $errors;

    public function __construct(int $statusCode, string $message, array $errors = [])
    {
        parent::__construct($statusCode, $message);
        $this->errors = $errors;
    }

    public function setStatusCode(int $statusCode): AbstractFormattedApiResponse
    {
        if ($statusCode == null) $statusCode = 500;

        return parent::setStatusCode($statusCode);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): ErrorResponse
    {
        $this->errors = $errors;
        return $this;
    }

    protected function toArray(): array
    {
        $array = parent::toArray();

        unset($array['message']);
        $array['error'] = $this->message;
        if (!empty($this->errors)) $array['errors'] = $this->errors;

        return $array;
    }
}
