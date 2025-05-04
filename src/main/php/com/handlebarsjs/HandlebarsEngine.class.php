<?php namespace com\handlebarsjs;

use com\github\mustache\{Context, DataContext, Template, FilesIn, InMemory};
use io\{Folder, Path};
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
  protected $parser, $templates;
  public $helpers;

  static function __static() {
    self::$builtin= ['lookup' => fn($node, $context, $options) => $options[0][$options[1]] ?? null];
  }

  /**
   * Create new instance and initialize builtin helpers
   *
   * @param  ?com.handlebarsjs.HandlebarsParser $parser
   */
  public function __construct($parser= null) {
    $this->parser= $parser ?? new HandlebarsParser();
    $this->templates= new Templates();
    $this->helpers= self::$builtin;
  }

  /** @return com.handlebarsjs.HandlebarsParser */
  public function parser() { return $this->parser; }

  /** @return com.github.mustache.TemplateLoader */
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
   * Sets a logger to use. Pass NULL to remove logger.
   *
   * @param  ?function(var[], ?string): var|util.log.LogCategory $logger
   * @return self this
   * @throws lang.IllegalArgumentException on argument mismatch
   */
  public function withLogger($logger) {
    if (null === $logger) {
      unset($this->helpers['log']);
    } else if ($logger instanceof \Closure) {
      $this->helpers['log']= function($items, $context, $options) use($logger) {
        $level= $options['level'] ?? 'info';
        unset($options['level']);
        $logger($options, $level);
        return '';
      };
    } else if ($logger instanceof LogCategory) {
      $this->helpers['log']= function($items, $context, $options) use($logger) {
        if (isset($options['level'])) {
          $level= LogLevel::named($options['level']);
          unset($options['level']);
        } else {
          $level= LogLevel::INFO;
        }
        $logger->log($level, $options);
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
   * @param  string|io.Folder|io.Path|[:string]|com.github.mustache.templates.Templates $arg
   * @return self this
   */
  public function withTemplates($arg) {
    if ($arg instanceof Folder || $arg instanceof Path || is_string($arg)) {
      $this->templates->delegate(new FilesIn($arg, ['.handlebars']));
    } else if (is_array($arg)) {
      $this->templates->delegate(new InMemory($arg));
    } else {
      $this->templates->delegate($arg);
    }
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
    return $this->templates->tokens($template)->compile($this->parser, $start, $end, $indent);
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
    return $this->templates->source($name)->compile($this->parser, $start, $end, $indent);
  }

  /**
   * Evaluate a compiled template.
   *
   * @param  com.github.mustache.Template $template The template
   * @param  [:var]|com.github.mustache.Context $arg Context
   * @return string The rendered output
   */
  public function evaluate(Template $template, $arg) {
    $c= $arg instanceof Context ? new DefaultContext($arg->variables, $arg->parent) : new DefaultContext($arg);
    return $template->evaluate($c->withEngine($this));
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
    $c= $arg instanceof Context ? new DefaultContext($arg->variables, $arg->parent) : new DefaultContext($arg);
    $template->write($c->withEngine($this), $out);
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