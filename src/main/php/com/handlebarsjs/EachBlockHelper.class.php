<?php namespace com\handlebarsjs;

/**
 * Each: Traverse lists and hashes
 *
 * @test xp://com.handlebarsjs.unittest.EachHelperTest
 */
class EachBlockHelper extends BlockNode {
  private $params;

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
    parent::__construct('each', $options, $fn, $inverse, $start, $end);
    $this->params= isset($options[1]) ? cast($options[1], BlockParams::class)->names : [];
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    $target= $this->options[0]($this, $context, []);

    if ($target instanceof \Generator) {
      (new HashContext($context, $target, ...$this->params))->write($this->fn, $out);
    } else if ($context->isList($target)) {
      (new ListContext($context, $target, ...$this->params))->write($this->fn, $out);
    } else if ($context->isHash($target)) {
      (new HashContext($context, $target, ...$this->params))->write($this->fn, $out);
    } else {
      $this->inverse->write($context, $out);
    }
  }
}