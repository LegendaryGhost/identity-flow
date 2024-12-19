<?php

namespace App\Http\Responses;

use http\Exception\InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiResponseContent
{
    protected int    $statusCode;
    protected string $message;
    private   string $status;

    protected function __construct(int $statusCode, string $message)
    {
        $this->setStatusCode($statusCode)
            ->setMessage($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): AbstractApiResponseContent
    {
        if (array_key_exists($statusCode, Response::$statusTexts) === false)
            throw new InvalidArgumentException(sprintf('Le code de status "%d" est invalide', $statusCode));

        $this->statusCode = $statusCode;
        $this->status     = Response::$statusTexts[$statusCode];
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): AbstractApiResponseContent
    {
        $this->message = $message;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function createJsonResponse(): JsonResponse
    {
        return response()->json($this->toArray(), $this->statusCode);
    }

    protected function toArray(): array
    {
        return [
            'status'      => $this->status,
            'status_code' => $this->statusCode,
            'message'     => $this->message,
        ];
    }
}
