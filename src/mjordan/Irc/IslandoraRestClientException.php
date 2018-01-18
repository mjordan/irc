<?php

namespace mjordan\Irc;

/**
 * Islandora REST Client Exception base class.
 */
class IslandoraRestClientException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $response
     *    The Guzzle response. null if none is available.
     * @param string $message
     *    The message provided with the thrown exception.
     * @param $int $code
     *    The code provided with the thrown exception.
     * @param Exception $incoming
     *    The exception that this Exception wraps.
     */
    public function __construct($response, $message, $code = 0, \Exception $previous = null)
    {
        $this->guzzleResponse = $response;
        $this->message = $message;
        $this->code = $code;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Getter for the Guzzle response.
     *
     * @return object
     *   The Guzzle response.
     */
    public function getGuzzleResponse()
    {
        return $this->guzzleResponse;
    }
}
