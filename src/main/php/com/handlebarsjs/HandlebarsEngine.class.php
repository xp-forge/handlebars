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
      $list= $context->lookup($options[0]);
      if ($context->isList($list)) {
        $traversable= $context->asTraversable($list);
        $size= sizeof($traversable);
        $out= '';
        foreach ($traversable as $index => $element) {
          $out.= $items->evaluate(new ListContext($index, $size, $context->asContext($element)));
        }
        return $out;
      } else {
        return '';
      }
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