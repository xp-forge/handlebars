<?php namespace com\handlebarsjs;

use com\github\mustache\templates\Compiled;
use com\github\mustache\{Templating, Scope, Node, TemplateFormatException};

/**
 * Handlebars templates transformations include support for `*inline`,
 * which declares inline templates, only available during the scope of
 * the transformation.
 *
 * @test  com.handlebarsjs.unittest.TransformationTest
 */
class Transformation extends Scope {

  /**
   * Creates a new transformation scope
   *
   * @param  com.github.mustache.Templating $templates
   * @param  [:var] $helpers
   */
  public function __construct(Templating $templates, $helpers= []) {
    $this->templates= new class($templates->sources(), $templates->parser()) extends Templating {
      private $inline= [];

      /** Declares an inline template */
      public function declare($name, Node $template) {
        if (isset($this->inline[$name])) {
          throw new TemplateFormatException('Cannot redeclare *inline "'.$name.'"');
        }
        $this->inline[$name]= new Compiled($template);
      }

      /** Overwrites an inline template and returns any previously registered template */
      public function register($name, Node $template= null) {
        $previous= isset($this->inline[$name]) ? $this->inline[$name]->template() : null;
        if (null === $template) {
          unset($this->inline[$name]);
        } else {
          $this->inline[$name]= new Compiled($template);
        }
        return $previous;
      }

      /** Loads a given template by its name */
      public function load($name) {
        return $this->inline[$name] ?? parent::load($name);
      }
    };

    $this->helpers= $helpers;
  }
}