<?php

namespace App\Http\Responses;

use http\Exception\InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractFormattedApiResponse
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

    public function setStatusCode(int $statusCode): AbstractFormattedApiResponse
    {
        $this->statusCode = $statusCode;
        $this->setStatus();
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): AbstractFormattedApiResponse
    {
        $this->message = $message;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    private function setStatus(): void
    {
        $status = Response::$statusTexts[$this->statusCode] ?? null;
        if ($status == null)
            throw new InvalidArgumentException(sprintf('Le code de status "%d" est invalide', $this->statusCode));

        $this->status = $status;
    }

    public function toJson(): JsonResponse
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
