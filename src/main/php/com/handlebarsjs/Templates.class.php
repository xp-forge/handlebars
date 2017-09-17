<?php namespace com\handlebarsjs;

use text\StringTokenizer;
use com\github\mustache\Node;
use com\github\mustache\Template;
use com\github\mustache\templates\Tokens;
use com\github\mustache\templates\Compiled;
use com\github\mustache\templates\NotFound;

/**
 * Template loading implementation
 *
 * @test  xp://com.handlebarsjs.unittest.TemplatesTest
 */
class Templates extends \com\github\mustache\templates\Templates {
  private $templates= [];
  private $delegate;

  /**
   * Sets delegate loader
   *
   * @param  com.github.mustache.templates.Templates $delegate
   * @return void
   */
  public function delegate($delegate) {
    $this->delegate= $delegate;
  }

  /**
   * Adds template
   *
   * @param  string $name
   * @param  string|com.github.mustache.Node $content
   * @return string
   */
  public function register($name, $content) {
    $previous= isset($this->templates[$name]) ? $this->templates[$name] : null;
    if ($content instanceof Node) {
      $this->templates[$name]= new Compiled(new Template($name, $content));
    } else {
      $this->templates[$name]= new Tokens($name, new StringTokenizer($content));
    }
    return $previous;
  }

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name without file extension
   * @return com.github.mustache.templates.Source
   */
  public function source($name) {
    if (isset($this->templates[$name])) {
      return $this->templates[$name];
    } else if ($this->delegate) {
      return $this->delegate->source($name);
    } else {
      return new NotFound('Cannot find template '.$name);
    }
  }

  /** @return com.github.mustache.TemplateListing */
  public function listing() {
    return $this->delegate->listing();
  }
}