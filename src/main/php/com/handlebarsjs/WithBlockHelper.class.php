<?php namespace com\handlebarsjs;

/**
 * With: Evaluate content in context defined by argument
 *
 * @test xp://com.handlebarsjs.unittest.WithHelperTest
 */
class WithBlockHelper extends BlockNode {

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
    parent::__construct('with', $options, $fn, $inverse, $start, $end);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    $f= $this->options[0];
    $target= $f($this, $context, []);
    if ($context->isTruthy($target)) {
      $this->fn->write($context->asContext($target), $out);
    } else {
      $this->inverse->write($context->asContext($target), $out);
    }
  }
}