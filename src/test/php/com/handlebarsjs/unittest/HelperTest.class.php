<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\InMemory;
use com\handlebarsjs\HandlebarsEngine;
use unittest\Before;

abstract class HelperTest {
  protected $templates;

  #[Before]
  public function templates() {
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
    try {
      return (new HandlebarsEngine())->withTemplates($this->templates)->render($template, $variables);
    } finally {
      $this->templates->clear();
    }
  }
}