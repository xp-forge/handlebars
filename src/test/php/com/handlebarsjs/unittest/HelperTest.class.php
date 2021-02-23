<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\InMemory;
use com\github\mustache\templates\Templates;
use com\handlebarsjs\HandlebarsEngine;

/** Base class for all helper tests */
abstract class HelperTest {

  /** Returns in-memory templates initialized from a given map */
  protected function templates(array $templates= []): Templates {
    return new InMemory($templates);
  }

  /** Returns handlebars engine with specified templates */
  protected function engine(Templates $templates= null): HandlebarsEngine {
    return (new HandlebarsEngine())->withTemplates($templates ?? $this->templates());
  }

  /**
   * Evaluate a string template against given variables and return the output.
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string
   */
  protected function evaluate($template, $variables) {
    return $this->engine()->render($template, $variables);
  }
}