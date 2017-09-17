<?php namespace com\handlebarsjs;

use com\github\mustache\NodeList;

class Nodes extends NodeList {
  private $decorations= [];

  /**
   * Add a decoration
   *
   * @param  com.handlebarsjs.Decoration $decoration
   * @return com.handlebarsjs.Decoration
   */
  public function decorate($decoration) {
    $this->decorations[]= $decoration;
    return $decoration;
  }

  /**
   * Evaluates decorators
   *
   * @param  com.github.mustache.Context $context the rendering context
   */
  public function decorators($context) {
    foreach ($this->decorations as $decoration) {
      $decoration->evaluate($context);
    }
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   */
  public function evaluate($context) {
    foreach ($this->decorations as $decoration) {
      $decoration->evaluate($context);
    }
    return parent::evaluate($context);
  }
}