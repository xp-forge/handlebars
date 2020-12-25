<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;
use io\streams\MemoryOutputStream;
use lang\IllegalArgumentException;
use unittest\{Assert, Expect, Test};
use util\log\LogCategory;

class EngineTest {

  #[Test]
  public function can_create() {
    new HandlebarsEngine();
  }

  #[Test]
  public function initially_no_logger_set() {
    $engine= new HandlebarsEngine();
    Assert::null($engine->helper('log'));
  }

  #[Test]
  public function withLogger_using_closure_sets_logger() {
    $engine= (new HandlebarsEngine())->withLogger(function($args) { });
    Assert::instance('function(?): void', $engine->helper('log'));
  }

  #[Test]
  public function withLogger_using_LogCategory_sets_logger() {
    $engine= (new HandlebarsEngine())->withLogger(new LogCategory('test'));
    Assert::instance('function(?): void', $engine->helper('log'));
  }

  #[Test]
  public function withLogger_null_unsets_previously_set_logger() {
    $engine= (new HandlebarsEngine())->withLogger(function($args) { });
    $engine->withLogger(null);
    Assert::null($engine->helper('log'));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function with_non_callable_logger() {
    (new HandlebarsEngine())->withLogger('log');
  }

  #[Test]
  public function evaluate() {
    $engine= new HandlebarsEngine();
    $result= $engine->evaluate($engine->compile('Hello {{name}}'), ['name' => 'World']);

    Assert::equals('Hello World', $result);
  }

  #[Test]
  public function write() {
    $engine= new HandlebarsEngine();
    $out= new MemoryOutputStream();
    $engine->write($engine->compile('Hello {{name}}'), ['name' => 'World'], $out);

    Assert::equals('Hello World', $out->getBytes());
  }
}