<?php namespace com\handlebarsjs;

use com\github\mustache\{Scope, Context, DataContext, Templating, Template};
use lang\IllegalArgumentException;
use util\log\{LogCategory, LogLevel};

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
  protected static $builtin;
  private $templates, $helpers;

  static function __static() {
    self::$builtin= [
      'this'    => function($node, $context, $options) {
        $value= $context->lookup(null);
        if ($context->isHash($value) || $context->isList($value)) {
          return current($value);
        } else {
          return $value;
        }
      },
      'lookup'  => function($node, $context, $options) {
        return $options[0][$options[1]] ?? null;
      },
      '*inline' => function($node, $context, $options) {
        $context->scope->templates->declare($options[0]($node, $context, []), $node);
      }
    ];
  }

  /** Create new instance and initialize builtin helpers */
  public function __construct() {
    $this->templates= new Templating(new FilesIn('.'),  new HandlebarsParser());
    $this->helpers= self::$builtin;
  }

  /** @return com.github.mustache.Templating */
  public function templates() { return $this->templates; }

  /** @return [:var] */
  public function helpers() { return $this->helpers; }

  /**
   * Gets a given helper
   *
   * @param  string $name
   * @return var or NULL if no such helper exists.
   */
  public function helper($name) {
    return $this->helpers[$name] ?? null;
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
    if (null === $logger) {
      unset($this->helpers['log']);
    } else if ($logger instanceof \Closure) {
      $this->helpers['log']= function($items, $context, $options) use($logger) {
        $logger($options);
        return '';
      };
    } else if ($logger instanceof LogCategory) {
      $this->helpers['log']= function($items, $context, $options) use($logger) {
        if (sizeof($options) > 1) {
          $logger->log(LogLevel::named(array_shift($options)), $options);
        } else {
          $logger->log(LogLevel::DEBUG, $options);
        }
        return '';
      };
    } else {
      throw new IllegalArgumentException(sprintf(
        'Expect either a closure, a util.log.LogCategory or NULL, %s given',
        typeof($logger)->getName()
      ));
    }
    return $this;
  }

  /**
   * Sets template loader to be used
   *
   * @param  com.github.mustache.templates.Sources $sources
   * @return self this
   */
  public function withTemplates($sources) {
    $this->templates->from($sources);
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
    $this->helpers[$name]= $helper;
    return $this;
  }

  /**
   * Sets helpers
   *
   * @param  [:var] $helpers
   * @return self this
   */
  public function withHelpers(array $helpers) {
    $this->helpers= self::$builtin + $helpers;
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
     return $this->templates->compile($template, $start, $end, $indent);
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
    return $this->templates->compile($this->templates->load($name), $start, $end, $indent);
  }

  /**
   * Evaluate a compiled template.
   *
   * @param  com.github.mustache.Template $template The template
   * @param  [:var]|com.github.mustache.Context $arg Context
   * @return string The rendered output
   */
  public function evaluate(Template $template, $arg) {
    $c= $arg instanceof Context ? $arg : new DataContext($arg);
    return $template->evaluate($c->inScope(new Scope(new Transformation($this->templates), $this->helpers)));
  }

  /**
   * Evaluate a compiled template.
   *
   * @param  com.github.mustache.Template $template The template
   * @param  [:var]|com.github.mustache.Context $arg Context
   * @param  io.streams.OutputStream $out
   * @return void
   */
  public function write(Template $template, $arg, $out) {
    $c= $arg instanceof Context ? $arg : new DataContext($arg);
    $template->write($c->inScope(new Scope(new Transformation($this->templates), $this->helpers)), $out);
  }

  /**
   * Render a template - like evaluate(), but will compile if necessary.
   *
   * @param  string $template The template as a string
   * @param  [:var]|com.github.mustache.Context $arg Context
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return string The rendered output
   */
  public function render($template, $arg, $start= '{{', $end= '}}', $indent= '') {
    return $this->evaluate($this->compile($template, $start, $end, $indent), $arg);
  }

  /**
   * Transform a template by its name, which is previously loaded from
   * the template loader.
   *
   * @param  string $name The template name.
   * @param  [:var]|com.github.mustache.Context $arg Context
   * @param  string $start Initial start tag, defaults to "{{"
   * @param  string $end Initial end tag, defaults to "}}"
   * @param  string $indent Indenting level, defaults to no indenting
   * @return string The rendered output
   */
  public function transform($name, $arg, $start= '{{', $end= '}}', $indent= '') {
    return $this->evaluate($this->load($name, $start, $end, $indent), $arg);
  }
}