<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;

abstract class HelperTest extends \unittest\TestCase {

  /**
   * Evaluate a string template against given variables and return the output.
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string
   */
  protected function evaluate($template, $variables) {
    return (new HandlebarsEngine())->render($template, $variables);
  }
}