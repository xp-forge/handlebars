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
   * @param  string[] $options
   * @param  ?com.github.mustache.NodeList $fn
   * @param  ?com.github.mustache.NodeList $inverse
   * @param  string $start
   * @param  string $end
   */
  public function __construct($options= [], $fn= null, $inverse= null, $start= '{{', $end= '}}') {
    parent::__construct('if', $options, $fn, $inverse, $start, $end);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    $target= $this->options[0]($this, $context, []);

    if ($target instanceof \Generator ? $target->valid() : $context->isTruthy($target)) {
      $this->fn->write($context, $out);
    } else {
      $this->inverse->write($context, $out);
    }
  }
}