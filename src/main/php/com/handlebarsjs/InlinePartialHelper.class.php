<?php namespace com\handlebarsjs;

use com\github\mustache\NodeList;

/**
 * Partial blocks
 *
 * @see   http://handlebarsjs.com/partials.html
 * @test  xp://com.handlebarsjs.unittest.InlinePartialHelperTest
 */
class InlinePartialHelper extends BlockNode {

  /**
   * Creates a new with block helper
   *
   * @param string[] $options
   * @param com.github.mustache.NodeList $fn
   * @param com.github.mustache.NodeList $inverse
   * @param string $start
   * @param string $end
   */
  public function __construct($options= [], NodeList $fn= null, NodeList $inverse= null, $start= '{{', $end= '}}') {
    parent::__construct('inline', $options, $fn, $inverse, $start, $end);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    $f= $this->options[0];
    $target= $f($this, $context, []);

    $context->engine->getTemplates()->register($target, $this->fn);
  }
}