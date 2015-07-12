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
   * @return string
   */
  public function evaluate($context) {
    $f= $this->options[0];
    $target= $f($context);
    $out= '';
    if ($context->isList($target)) {
      $list= $context->asTraversable($target);
      $size= sizeof($list);
      foreach ($list as $index => $element) {
        $out.= $this->fn->evaluate(new ListContext($index, $size, $context->asContext($element)));
      }
    } else if ($context->isHash($target)) {
      $hash= $context->asTraversable($target);
      $out= '';
      $first= true;
      foreach ($hash as $key => $value) {
        $out.= $this->fn->evaluate(new HashContext($key, $first, $context->asContext($value)));
        $first= false;
      }
    } else {
      $out= $this->inverse->evaluate($context);
    }
    return $out;
  }
}