<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;

class EngineTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new HandlebarsEngine();
  }

  #[@test]
  public function each_helper_builtin() {
    $this->assertInstanceOf('Closure', create(new HandlebarsEngine())->helpers['each']);
  }

  #[@test]
  public function if_helper_builtin() {
    $this->assertInstanceOf('Closure', create(new HandlebarsEngine())->helpers['if']);
  }

  #[@test]
  public function with_helper_builtin() {
    $this->assertInstanceOf('Closure', create(new HandlebarsEngine())->helpers['with']);
  }
}