<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;
use com\github\mustache\InMemory;

abstract class HelperTest extends \unittest\TestCase {
  protected $templates;

  /** @return void */
  public function setUp() {
    $this->templates= new InMemory();
  }

  /**
   * Evaluate a string template against given variables and return the output.
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string
   */
  protected function evaluate($template, $variables) {
    return (new HandlebarsEngine())->withTemplates($this->templates)->render($template, $variables);
  }
}