<?php
/**
 * @license see LICENSE
 */
namespace Serps\ErrorDispatcher\Test;

use Serps\ErrorDispatcher\NoMatchingErrorException;
use Serps\ErrorDispatcher\ErrorMatcher;

class ErrorMatcherTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleFind(){
        $errorConfiguration = new ErrorMatcher();
        $errorConfiguration->addHandler('FOO::BAR', function(){});

        $match = $errorConfiguration->findMatch('FOO::BAR');
        $this->assertEquals('FOO::BAR', $match->getName());

        $match = $errorConfiguration->findMatch('FOO::*');
        $this->assertEquals('DEFAULT', $match->getName());
    }

    public function testSimpleWildcard(){
        $errorConfiguration = new ErrorMatcher();
        $errorConfiguration->addSimpleHandler('FOO::*', function(){});

        $match = $errorConfiguration->findMatch('FOO::BAR');
        $this->assertEquals('FOO::*', $match->getName());

        $match = $errorConfiguration->findMatch('FOO::');
        $this->assertEquals('FOO::*', $match->getName());

        $match = $errorConfiguration->findMatch('FOO::*');
        $this->assertEquals('DEFAULT', $match->getName());

        $match = $errorConfiguration->findMatch('FOO::*');
        $this->assertEquals('DEFAULT', $match->getName());
    }

    public function testMoreThanOneHandler(){
        $errorConfiguration = new ErrorMatcher();
        $errorConfiguration->addSimpleHandler('FOO::*', function(){});
        $errorConfiguration->addSimpleHandler('BAR::*', function(){});

        $match = $errorConfiguration->findMatch('FOO::BAR');
        $this->assertEquals('FOO::*', $match->getName());

        $match = $errorConfiguration->findMatch('FOO::');
        $this->assertEquals('FOO::*', $match->getName());

        $match = $errorConfiguration->findMatch('BAR::FOO');
        $this->assertEquals('BAR::*', $match->getName());

        $match = $errorConfiguration->findMatch('BAR::BAZ');
        $this->assertEquals('BAR::*', $match->getName());

        $match = $errorConfiguration->findMatch('BAZ::FOO::BAR');
        $this->assertEquals('DEFAULT', $match->getName());
    }

    public function testOrder(){
        $errorConfiguration = new ErrorMatcher();
        $errorConfiguration->addSimpleHandler('FOO::BAR', function(){}, '1st');
        $errorConfiguration->addSimpleHandler('FOO::BAR', function(){}, '2d');

        $match = $errorConfiguration->findMatch('FOO::BAR');
        $this->assertEquals('1st', $match->getName());
    }

    public function testImplicitDefaultHandler(){

        $errorConfiguration = new ErrorMatcher();

        try{
            $errorConfiguration->handle('FOO', 'foo bar');
            $this->fail('exception not thrown');
        }catch(NoMatchingErrorException $e){
            $this->assertEquals('FOO', $e->getHandledErrorName());
            $this->assertEquals('foo bar', $e->getHandledErrorMessage());
        }

    }

    public function testExplicitDefaultHandler(){

        $errorConfiguration = new ErrorMatcher(function(){
            return "explicit exception";
        });

        $value = $errorConfiguration->handle('FOO', 'foo bar');
        $this->assertEquals('explicit exception', $value);
    }


}
