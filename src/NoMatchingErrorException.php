<?php
/**
 * @license see LICENSE
 */

namespace Serps\ErrorDispatcher;


class NoMatchingErrorException extends \Exception
{

    protected $handledErrorName;
    protected $handledErrorMessage;

    /**
     * NoMatchingErrorException constructor.
     * @param $handledErrorCode
     * @param $handledErrorMessage
     */
    public function __construct($handledErrorName, $handledErrorMessage, $code = 0, \Exception $previous = null)
    {
        $this->handledErrorName = $handledErrorName;
        $this->handledErrorMessage = $handledErrorMessage;

        $message = 'Unhandled error: "' . $handledErrorMessage . '" with code ' . $handledErrorName;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getHandledErrorName()
    {
        return $this->handledErrorName;
    }

    /**
     * @return int
     */
    public function getHandledErrorMessage()
    {
        return $this->handledErrorMessage;
    }
}
