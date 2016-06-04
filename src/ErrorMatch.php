<?php
/**
 * @license see LICENSE
 */

namespace Serps\ErrorDispatcher;


class ErrorMatch
{

    protected $matchedFlag;
    protected $handler;
    protected $pattern;

    /**
     * ErrorMatch constructor.
     * @param $matchedFlag
     * @param $handler
     */
    public function __construct($matchedFlag, callable $handler, $pattern)
    {
        $this->matchedFlag = $matchedFlag;
        $this->handler = $handler;
        $this->pattern = $pattern;
    }

    /**
     * name of the pattern
     * @return string
     */
    public function getName()
    {
        return $this->matchedFlag;
    }

    /**
     * pattern that matched
     * @return string
     */
    public function getPattern(){
        return $this->pattern;
    }

    /**
     * handler for the error
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }




}