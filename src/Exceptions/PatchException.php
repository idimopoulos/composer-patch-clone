<?php

namespace ComposerPatchClone\Exceptions;

use Exception;

/**
 * Custom exception for patch management errors.
 */
class PatchException extends Exception
{
    /**
     * Construct a new PatchException instance.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Convert the exception to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
