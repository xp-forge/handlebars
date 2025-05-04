<?php namespace com\handlebarsjs;

use com\github\mustache\templates\{Compiled, NotFound, Source, InString, Templates as Base};
use com\github\mustache\{Node, Template, TemplateListing};
use lang\ClassLoader;
use text\StringTokenizer;

/**
 * Template loading implementation
 *
 * @test  com.handlebarsjs.unittest.TemplatesTest
 */
class Templates extends Base {
  private static $composite= null;
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
   * @param  string|com.github.mustache.templates.Source|com.github.mustache.Node $content
   * @return string
   */
  public function register($name, $content) {
    $previous= $this->templates[$name] ?? null;

    if (null === $content) {
      unset($this->templates[$name]);
    } else if ($content instanceof Source) {
      $this->templates[$name]= $content;
    } else if ($content instanceof Node) {
      $this->templates[$name]= new Compiled(new Template($name, $content));
    } else {
      $this->templates[$name]= new InString($name, (string)$content);
    }

    return $previous;
  }

  /**
   * Parses a template
   *
   * @param  string $content
   * @param  string $name
   * @return com.github.mustache.templates.Source
   */
  public function tokens($content, $name= '(string)') {
    return new InString($name, (string)$content);
  }

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name without file extension
   * @return com.github.mustache.templates.Source
   */
  public function source($name) {
    return $this->templates[$name] ?? ($this->delegate
      ? $this->delegate->source($name)
      : new NotFound('Cannot find template '.$name)
    );
  }

  /** @return com.github.mustache.TemplateListing */
  public function listing() {
    if ($this->delegate) {
      return new CompositeListing($this->templates, $this->delegate->listing());
    } else {
      return new TemplateListing('', fn($package) => array_keys($this->templates));
    }
  }
}