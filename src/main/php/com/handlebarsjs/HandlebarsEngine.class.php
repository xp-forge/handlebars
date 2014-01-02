<?php namespace com\handlebarsjs;

use com\github\mustache\MustacheEngine;

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
    $this->builtin['each']= function($items, $context, $options) {
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
    };
    $this->helpers= $this->builtin;   // Initially
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
}