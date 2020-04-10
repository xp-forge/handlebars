<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;
use io\streams\MemoryOutputStream;
use lang\IllegalArgumentException;
use util\log\LogCategory;

class EngineTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new HandlebarsEngine();
  }

  #[@test]
  public function initially_no_logger_set() {
    $engine= new HandlebarsEngine();
    $this->assertNull($engine->helper('log'));
  }

  #[@test]
  public function withLogger_using_closure_sets_logger() {
    $engine= (new HandlebarsEngine())->withLogger(function($args) { });
    $this->assertInstanceOf('function(?): void', $engine->helper('log'));
  }

  #[@test]
  public function withLogger_using_LogCategory_sets_logger() {
    $engine= (new HandlebarsEngine())->withLogger(new LogCategory('test'));
    $this->assertInstanceOf('function(?): void', $engine->helper('log'));
  }

  #[@test]
  public function withLogger_null_unsets_previously_set_logger() {
    $engine= (new HandlebarsEngine())->withLogger(function($args) { });
    $engine->withLogger(null);
    $this->assertNull($engine->helper('log'));
  }

  #[@test, @expect(IllegalArgumentException::class)]
  public function with_non_callable_logger() {
    (new HandlebarsEngine())->withLogger('log');
  }

  #[@test]
  public function evaluate() {
    $engine= new HandlebarsEngine();
    $result= $engine->evaluate($engine->compile('Hello {{name}}'), ['name' => 'World']);

    $this->assertEquals('Hello World', $result);
  }

  #[@test]
  public function write() {
    $engine= new HandlebarsEngine();
    $out= new MemoryOutputStream();
    $engine->write($engine->compile('Hello {{name}}'), ['name' => 'World'], $out);

    $this->assertEquals('Hello World', $out->getBytes());
  }
}