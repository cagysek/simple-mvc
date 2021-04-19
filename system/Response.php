<?php


namespace App\System;


use App\Enum\EStatusCode;

class Response
{
    private int $statusCode;

    private ?string $body;

    private ?string $redirect;

    /**
     * Response constructor.
     *
     * @param int         $statusCode
     * @param string|null $body
     * @param string|null $redirect
     */
    public function __construct(int $statusCode, ?string $body = NULL, ?string $redirect = NULL)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->redirect = $redirect;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return ?string
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @return ?string
     */
    public function getRedirect(): ?string
    {
        return $this->redirect;
    }

    public function send() : void
    {
        echo $this->getBody();
    }
}