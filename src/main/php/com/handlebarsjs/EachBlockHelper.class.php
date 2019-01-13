<?php namespace com\handlebarsjs;

/**
 * Each: Traverse lists and hashes
 *
 * @test xp://com.handlebarsjs.unittest.EachHelperTest
 */
class EachBlockHelper extends BlockNode {

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

    if ($target instanceof \Generator) {
      $first= true;
      foreach ($target as $key => $value) {
        $this->fn->write(new HashContext($key, $first, $context->asContext($value)), $out);
        $first= false;
      }
    } else if ($context->isList($target)) {
      $list= $context->asTraversable($target);
      $size= sizeof($list);
      foreach ($list as $index => $element) {
        $this->fn->write(new ListContext($index, $size, $context->asContext($element)), $out);
      }
    } else if ($context->isHash($target)) {
      $hash= $context->asTraversable($target);
      $first= true;
      foreach ($hash as $key => $value) {
        $this->fn->write(new HashContext($key, $first, $context->asContext($value)), $out);
        $first= false;
      }
    } else {
      $this->inverse->write($context, $out);
    }
  }
}