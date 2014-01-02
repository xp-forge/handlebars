<?php namespace com\handlebarsjs;

use com\github\mustache\MustacheEngine;
use util\log\LogCategory;
use util\log\LogLevel;

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
 * @test  xp://com.handlebarsjs.unittest.WebsiteExamplesTest
 * @see   http://handlebarsjs.com/
 */
class HandlebarsEngine extends MustacheEngine {
  protected $builtin= array();

  /**
   * Constructor. Initializes builtin helpers.
   */
  public function __construct() {
    parent::__construct();

    // Each: Traverse lists and hashes
    $this->setBuiltin('each', function($items, $context, $options) {
      $target= $context->lookup($options[0]);
      $out= '';
      if ($context->isList($target)) {
        $list= $context->asTraversable($target);
        $size= sizeof($list);
        foreach ($list as $index => $element) {
          $out.= $items->evaluate(new ListContext($index, $size, $context->asContext($element)));
        }
      } else if ($context->isHash($target)) {
        $hash= $context->asTraversable($target);
        $out= '';
        $first= true;
        foreach ($hash as $key => $value) {
          $out.= $items->evaluate(new HashContext($key, $first, $context->asContext($value)));
          $first= false;
        }
      }
      return $out;
    });

    // If: Evaluate content in same context if value is truthy
    $this->setBuiltin('if', function($items, $context, $options) {
      if ($context->isTruthy($context->lookup($options[0]))) {
        return $options['fn']->evaluate($context);
      } else {
        return $options['inverse']->evaluate($context);
      }
    });

    // Unless: Evaluate content in same context if value is falsy
    $this->setBuiltin('unless', function($items, $context, $options) {
      $target= $context->lookup($options[0]);
      if ($context->isTruthy($target)) {
        return '';
      }
      return $items->evaluate($context);
    });

    // With: Evaluate content in context defined by argument
    $this->setBuiltin('with', function($items, $context, $options) {
      $target= $context->lookup($options[0]);
      if ($context->isTruthy($target)) {
        return $items->evaluate($context->asContext($target));
      }
      return '';
    });

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

      // This can be optimized once we want to set the dependency on xp-framework/core
      // to a 6.0.0 minimum, see https://github.com/xp-framework/core/pull/4
      $this->setBuiltin('log', function($items, $context, $options) use($logger) {
        $level= array_shift($options);
        LogLevel::named($level);
        call_user_func_array(array($logger, $level), $options);
        return '';
      });
    } else if (null === $logger) {
      $this->setBuiltin('log', null);
    } else {
      throw new \lang\IllegalArgumentException('Expect either a closure, a util.log.LogCategory or NULL, '.\xp::typeOf($logger).' given');
    }
    return $this;
  }
}