<?php
/**
 * @license see LICENSE
 */

namespace Serps\ErrorDispatcher;

class ErrorMatcher {

    protected $handlersIndexed = [];
    protected $patternNamesIndexed = [];
    protected $realPatternsIndexed = [];
    protected $compiled = false;

    protected $defaultHandler;

    public function __construct($defaultHandler = null)
    {
        if(null == $defaultHandler){
            $defaultHandler = function($errorName, $errorMessage){
                throw new NoMatchingErrorException($errorName, $errorMessage);
            };
        }

        $this->defaultHandler = $defaultHandler;
    }

    public function addSimpleHandler($pattern, $handler, $name = null){
        $this->addHandler($this->preparePattern($pattern), $handler, null !== $name ? $name : $pattern);
    }

    public function addHandler($pattern, $handler, $name = null){
        if(is_callable($handler)){
            $this->handlersIndexed[] = $handler;
            $this->patternNamesIndexed[] = null !== $name ? $name : $pattern;
            $this->realPatternsIndexed[] = $pattern;
        }else{
            throw new \InvalidArgumentException('$handler parameter is not a valid handler. A callable is expected');
        }

        $this->compiled = false;
    }

    public function preparePattern($pattern){
        return str_replace('*', '[a-z0-9]*', $pattern);
    }

    private function getCompiledPattern(){
        if(false === $this->compiled){
            if(count($this->realPatternsIndexed) > 0) {
                $this->compiled = '<(^' . implode("$)|(^", $this->realPatternsIndexed) . '$)>Ai';
            } else {
                $this->compiled = "";
            }
        }
        return $this->compiled;
    }

    public function findMatch($errorName){
        $pattern = $this->getCompiledPattern();
        if($pattern && preg_match($pattern, $errorName, $matches)){
            $resultIndex = count($matches) - 2;
            return new ErrorMatch($this->patternNamesIndexed[$resultIndex], $this->handlersIndexed[$resultIndex], $pattern);
        }else{
            return new ErrorMatch('DEFAULT', $this->defaultHandler, null);
        }
    }

    public function handle($errorName, $errorMessage = null){
        $match = $this->findMatch($errorName);
        return call_user_func($match->getHandler(), $errorName, $errorMessage);
    }

}
