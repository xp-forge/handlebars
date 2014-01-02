<?php namespace com\handlebarsjs;

use com\github\mustache\MustacheEngine;

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
        $out= '';
        foreach ($list as $element) {
          $out.= $items->evaluate($context->asContext($element));
        }
        return $out;
      } else {
        return '';
      }
    };
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