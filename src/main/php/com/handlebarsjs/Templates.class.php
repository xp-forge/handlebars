<?php namespace com\handlebarsjs;

use com\github\mustache\templates\{Compiled, NotFound, Source, Listing, InString};
use com\github\mustache\{Node, Template};
use lang\{ClassLoader, IllegalArgumentException};

/**
 * Template loading implementation
 *
 * @test  xp://com.handlebarsjs.unittest.TemplatesTest
 */
class Templates {
  private static $composite= null;
  private $templates= [];
  private $delegate;

  /** @return lang.XPClass */
  private function composite() {
    if (null === self::$composite) {
      self::$composite= ClassLoader::defineClass('CompositeListing', Listing::class, [], [
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
   * Declares an inline template
   *
   * @param  string $name
   * @param  com.github.mustache.Node $node
   * @throws lang.IllegalArgumentException
   * @return void
   */
  public function declare($name, Node $node) {
    if (isset($this->templates[$name])) {
      throw new IllegalArgumentException('Cannot redeclare template "'.$name.'"');
    }
    $this->templates[$name]= new Compiled(new Template($name, $node));
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
      $this->templates[$name]= new InString($name, $content);
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
    if (isset($this->templates[$name])) {
      return $this->templates[$name];
    } else if ($this->delegate) {
      return $this->templates[$name]= $this->delegate->source($name);
    } else {
      return new NotFound('Cannot find template '.$name);
    }
  }

  /** @return com.github.mustache.templates.Listing */
  public function listing() {
    if ($this->delegate) {
      return $this->composite()->newInstance($this->templates, $this->delegate->listing());
    } else {
      return new Listing('', function($package) { return array_keys($this->templates); });
    }
  }
}