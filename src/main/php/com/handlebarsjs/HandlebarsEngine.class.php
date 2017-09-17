<?php namespace com\handlebarsjs;

use com\github\mustache\MustacheEngine;
use com\github\mustache\Template;
use com\github\mustache\TemplateLoader;
use util\log\LogCategory;
use util\log\LogLevel;
use lang\IllegalArgumentException;

/**
 * Handlebars implementation for the XP Framework.
 *
 * Handlebars provides the power necessary to let you build semantic
 * templates effectively with no frustration.
 *
 * Mustache templates are compatible with Handlebars, so you can take
 * a Mustache template, import it into Handlebars, and start taking
 * advantage of the extra Handlebars features.
 *
 * @test  xp://com.handlebarsjs.unittest.EngineTest
 * @test  xp://com.handlebarsjs.unittest.EachHelperTest
 * @test  xp://com.handlebarsjs.unittest.IfHelperTest
 * @test  xp://com.handlebarsjs.unittest.UnlessHelperTest
 * @test  xp://com.handlebarsjs.unittest.WithHelperTest
 * @test  xp://com.handlebarsjs.unittest.WebsiteExamplesTest
 * @see   http://handlebarsjs.com/
 */
class HandlebarsEngine {
  protected $mustache, $templates;
  protected $builtin= [];

  /**
   * Constructor. Initializes builtin helpers.
   */
  public function __construct() {
    $this->templates= new Templates();
    $this->mustache= (new MustacheEngine())->withTemplates($this->templates)->withParser(new HandlebarsParser());

    // This: Access the current value in the context
    $this->setBuiltin('this', function($node, $context, $options) {
      $variable= $context->lookup(null);
      if ($context->isHash($variable) || $context->isList($variable)) {
        return current($context->asTraversable($variable));
      } else {
        return $variable;
      }
    });

    // Lookup: <where> <what>
    $this->setBuiltin('lookup', function($node, $context, $options) {
      return $options[0][$options[1]];
    });
  }

  /** @return com.github.mustache.TemplateLoader */
  public function templates() { return $this->mustache->getTemplates(); }

  /** @return [:var] */
  public function helpers() { return $this->mustache->helpers; }

  /**
   * Gets a given helper
   *
   * @param  string $name
   * @return var or NULL if no such helper exists.
   */
  public function helper($name) {
    return isset($this->mustache->helpers[$name]) ? $this->mustache->helpers[$name] : null;
  }

  /**
   * Sets built-in
   *
   * @param  string name
   * @param  var builtin
   */
  private function setBuiltin($name, $builtin) {
    if (null === $builtin) {
      unset($this->builtin[$name], $this->mustache->helpers[$name]);
    } else {
      $this->builtin[$name]= $this->mustache->helpers[$name]= $builtin;
    }
  }

  /**
   * Sets a logger to use. Accepts either a closure, a util.log.LogCategory
   * instance or NULL (to unset).
   *
   * @param  var $logger
   * @return self this
   * @throws lang.IllegalArgumentException on argument mismatch
   */
  public function withLogger($logger) {
    if ($logger instanceof \Closure) {
      $this->setBuiltin('log', function($items, $context, $options) use($logger) {
        $logger($options);
        return '';
      });
    } else if ($logger instanceof LogCategory) {
      $this->setBuiltin('log', function($items, $context, $options) use($logger) {
        if (sizeof($options) > 1) {
          $logger->log(LogLevel::named(array_shift($options)), $options);
        } else {
          $logger->log(LogLevel::DEBUG, $options);
        }
        return '';
      });
    } else if (null === $logger) {
      $this->setBuiltin('log', null);
    } else {
      throw new IllegalArgumentException('Expect either a closure, a util.log.LogCategory or NULL, '.typeof($logger)->getName().' given');
    }
    return $this;
  }

  /**
   * Sets template loader to be used
   *
   * @param  com.github.mustache.templates.Templates|com.github.mustache.TemplateLoader $l
   * @return self this
   */
  public function withTemplates($l) {
    $this->templates->delegate($l);
    return $this;
  }

  /**
   * Adds a helper with a given name
   *
   * @param  string $name
   * @param  var $helper
   * @return self this
   */
  public function withHelper($name, $helper) {
    $this->mustache->withHelper($name, $helper);
    return $this;
  }

  /**
   * Sets helpers
   *
   * @param  [:var] $helpers
   * @return self this
   */
  public function withHelpers(array $helpers) {
    $this->mustache->withHelpers(array_merge($this->builtin, $helpers));
    return $this;
  }

  /**
   * Compile a template.
   *
   * @param  string $template The template, as a string
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return com.github.mustache.Template
   */
  public function compile($template, $start= '{{', $end= '}}', $indent= '') {
    return $this->mustache->compile($template, $start, $end, $indent);
  }

  /**
   * Load a template.
   *
   * @param  string $name The template name.
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return com.github.mustache.Template
   */
  public function load($name, $start= '{{', $end= '}}', $indent= '') {
    return $this->mustache->load($name, $start, $end, $indent);
  }

  /**
   * Evaluate a compiled template.
   *
   * @param  com.github.mustache.Template $template The template
   * @param  var $arg Either a view context, or a Context instance
   * @return string The rendered output
   */
  public function evaluate(Template $template, $arg) {
    return $this->mustache->evaluate($template, $arg);
  }

  /**
   * Render a template - like evaluate(), but will compile if necessary.
   *
   * @param  var $template The template, either as string or as compiled Template instance
   * @param  var $arg Either a view context, or a Context instance
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return string The rendered output
   */
  public function render($template, $arg, $start= '{{', $end= '}}', $indent= '') {
    return $this->mustache->render($template, $arg, $start, $end, $indent);
  }

  /**
   * Transform a template by its name, which is previously loaded from
   * the template loader.
   *
   * @param  string $name The template name.
   * @param  var $arg Either a view context, or a Context instance
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return string The rendered output
   */
  public function transform($name, $arg, $start= '{{', $end= '}}', $indent= '') {
    return $this->mustache->transform($name, $arg, $start, $end, $indent);
  }
}