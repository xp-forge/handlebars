<?php namespace com\handlebarsjs;

/**
 * If: Evaluate content in same context if value is truthy
 *
 * @test xp://com.handlebarsjs.unittest.IfHelperTest
 */
class IfBlockHelper extends BlockNode {

  /**
   * Creates a new with block helper
   *
   * @param string[] $options
   * @param com.github.mustache.NodeList $fn
   * @param com.github.mustache.NodeList $inverse
   * @param string $start
   * @param string $end
   */
  public function __construct($options= array(), NodeList $fn= null, NodeList $inverse= null, $start= '{{', $end= '}}') {
    parent::__construct('if', $options, $fn, $inverse, $start, $end);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    $f= $this->options[0];
    if ($context->isTruthy($f($context))) {
      return $this->fn->evaluate($context);
    } else {
      return $this->inverse->evaluate($context);
    }
  }
}