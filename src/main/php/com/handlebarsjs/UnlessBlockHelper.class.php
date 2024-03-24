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
   * @param  string[] $options
   * @param  ?com.github.mustache.NodeList $fn
   * @param  ?com.github.mustache.NodeList $inverse
   * @param  string $start
   * @param  string $end
   */
  public function __construct($options= [], $fn= null, $inverse= null, $start= '{{', $end= '}}') {
    parent::__construct('unless', $options, $fn, $inverse, $start, $end);
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

    if ($target instanceof \Generator ? $target->valid() : $context->isTruthy($target)) {
      $this->inverse->write($context, $out);
    } else {
      $this->fn->write($context, $out);
    }
  }
}