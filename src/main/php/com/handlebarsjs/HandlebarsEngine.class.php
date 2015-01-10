<?php namespace com\handlebarsjs;

use com\github\mustache\MustacheEngine;
use util\log\LogCategory;
use util\log\LogLevel;
use lang\IllegalArgumentException;
new \import('com.handlebarsjs.LogCategoryExtensions');

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
class HandlebarsEngine extends MustacheEngine {
  protected $builtin= [];

  /**
   * Constructor. Initializes builtin helpers.
   */
  public function __construct() {
    parent::__construct();

    // This: Access the current value in the context
    $this->setBuiltin('this', function($items, $context, $options) {
      $variable= $context->lookup(null);
      if ($context->isHash($variable) || $context->isList($variable)) {
        return current($context->asTraversable($variable));
      } else {
        return $variable;
      }
    });

    // Overwrite parser
    $this->parser= new HandlebarsParser();
  }

  /**
   * Sets built-in
   *
   * @param  string name
   * @param  var builtin
   */
  protected function setBuiltin($name, $builtin) {
    if (null === $builtin) {
      unset($this->builtin[$name], $this->helpers[$name]);
    } else {
      $this->builtin[$name]= $this->helpers[$name]= $builtin;
    }
  }

  /**
   * Sets helpers
   *
   * @param  [:var] $helpers
   * @return self this
   */
  public function withHelpers(array $helpers) {
    return parent::withHelpers(array_merge($this->builtin, $helpers));
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
      throw new IllegalArgumentException('Expect either a closure, a util.log.LogCategory or NULL, '.\xp::typeOf($logger).' given');
    }
    return $this;
  }
}