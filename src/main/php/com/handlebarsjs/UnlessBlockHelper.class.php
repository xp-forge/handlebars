<?php namespace com\handlebarsjs;

/**
 * Unless: Evaluate content in same context if value is falsy
 *
 * @test xp://com.handlebarsjs.unittest.UnlessHelperTest
 */
class UnlessBlockHelper extends BlockNode {

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
    parent::__construct('unless', $options, $fn, $inverse, $start, $end);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    $f= $this->options[0];
    if ($context->isTruthy($f($this, $context, []))) {
      return $this->inverse->evaluate($context);
    } else {
      return $this->fn->evaluate($context);
    }
  }
}