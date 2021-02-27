<?php namespace com\handlebarsjs;

use com\github\mustache\templates\Compiled;
use com\github\mustache\{Templating, Node};
use lang\IllegalArgumentException;

class Transformation extends Templating {
  private $inline= [];

  public function __construct($templating) {
    parent::__construct($templating->sources(), $templating->parser());
  }

  /**
   * Declares an inline template, raising an error if there was a previously
   * registered template by the same name.
   *
   * @param  string $name
   * @param  ?com.github.mustache.Node $template
   * @throws lang.IllegalArgumentException
   * @return void
   */
  public function declare($name, Node $template) {
    if (isset($this->inline[$name])) {
      throw new IllegalArgumentException('Cannot redeclare *inline "'.$name.'"');
    }
    $this->inline[$name]= new Compiled($template);
  }

  /**
   * Declares an inline template and returns any previously registered template
   * by the same name.
   *
   * @param  string $name
   * @param  ?com.github.mustache.Node $content
   * @return ?com.github.mustache.Node
   */
  public function register($name, Node $template= null) {
    $previous= isset($this->inline[$name]) ? $this->inline[$name]->template() : null;
    $this->inline[$name]= new Compiled($template);
    return $previous;
  }

  /**
   * Loads a given template by its name
   *
   * @param  string $name
   * @return com.github.mustache.templates.Source
   */
  public function load($name) {
    return $this->inline[$name] ?? parent::load($name);
  }
}