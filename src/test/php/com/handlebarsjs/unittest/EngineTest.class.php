<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;

class EngineTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new HandlebarsEngine();
  }

  #[@test]
  public function initially_no_logger_set() {
    $engine= new HandlebarsEngine();
    $this->assertFalse(isset($engine->helpers['log']));
  }

  #[@test]
  public function withLogger_using_closure_sets_logger() {
    $engine= create(new HandlebarsEngine())->withLogger(function($args) { });
    $this->assertInstanceOf('Closure', $engine->helpers['log']);
  }

  #[@test]
  public function withLogger_using_LogCategory_sets_logger() {
    $engine= create(new HandlebarsEngine())->withLogger(new \util\log\LogCategory('test'));
    $this->assertInstanceOf('Closure', $engine->helpers['log']);
  }

  #[@test]
  public function withLogger_null_unsets_previously_set_logger() {
    $engine= create(new HandlebarsEngine())->withLogger(function($args) { });
    $engine->withLogger(null);
    $this->assertFalse(isset($engine->helpers['log']));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function with_non_callable_logger() {
    create(new HandlebarsEngine())->withLogger('log');
  }
}