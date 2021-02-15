<?php namespace com\handlebarsjs;

use com\github\mustache\templates\{Compiled, NotFound, Source, Tokens};
use com\github\mustache\{Node, Template, TemplateListing};
use lang\ClassLoader;
use text\StringTokenizer;

/**
 * Template loading implementation
 *
 * @test  xp://com.handlebarsjs.unittest.TemplatesTest
 */
class Templates extends \com\github\mustache\templates\Templates {
  private static $composite= null;
  private $templates= [];
  private $delegate;

  /** @return lang.XPClass */
  private function composite() {
    if (null === self::$composite) {
      self::$composite= ClassLoader::defineClass('CompositeListing', TemplateListing::class, [], [
        'templates' => null,
        'delegate' => null,
        '__construct' => function($templates, $delegate) {
          $this->templates= $templates;
          $this->delegate= $delegate;
        },
        'templates' => function() {
          return array_merge(array_keys($this->templates), $this->delegate->templates());
        },
        'packages' => function() {
          return $this->delegate->packages();
        }
      ]);
    }
    return self::$composite;
  }

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
      $this->templates[$name]= new Tokens($name, new StringTokenizer($content));
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
    return new Tokens($name, new StringTokenizer($content));
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
    if ($this->delegate) {
      return $this->composite()->newInstance($this->templates, $this->delegate->listing());
    } else {
      return new TemplateListing(null, function($package) { return array_keys($this->templates); });
    }
  }
}