<?php


namespace App\System;


use App\Enum\EStatusCode;

class Response
{
    private int $statusCode;

    private string $body;

    /**
     * Response constructor.
     *
     * @param int $statusCode
     * @param string      $body
     */
    public function __construct(int $statusCode, string $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    public function send() : void
    {
        echo $this->getBody();
    }
}