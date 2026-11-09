<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class ChatbotException extends RuntimeException
{
    protected bool $apiError;

    public function __construct(string $message, bool $apiError = false, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->apiError = $apiError;
    }

    public static function notConfigured(): self
    {
        return new self('The AI assistant is not configured yet. Please add an API key.');
    }

    public static function api(string $message): self
    {
        return new self($message, true);
    }

    public function isApiError(): bool
    {
        return $this->apiError;
    }
}
