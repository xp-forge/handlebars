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
   * @return void
   */
  public function enter($context) {
    foreach ($this->decorations as $decoration) {
      $decoration->enter($context);
    }
  }

  /**
   * Returns block without decorators
   *
   * @return com.github.mustache.NodeList
   */
  public function block() { return new NodeList($this->nodes); }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    foreach ($this->decorations as $decoration) {
      $decoration->enter($context);
    }
    return parent::evaluate($context);
  }
}