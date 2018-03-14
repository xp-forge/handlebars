<?php namespace com\handlebarsjs;

use com\github\mustache\NodeList;

/**
 * Partial blocks
 *
 * @see   http://handlebarsjs.com/partials.html
 * @test  xp://com.handlebarsjs.unittest.PartialBlockHelperTest
 */
class PartialBlockHelper extends BlockNode {

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
    $template= (string)array_shift($options);
    parent::__construct($template, $options, $fn, $inverse, $start, $end);
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    $templates= $context->engine->getTemplates();

    // {{#> partial context}} vs {{> partial key="Value"}}
    if (isset($this->options[0])) {
      $context= $context->newInstance($this->options[0]($this, $context, []));
    } else if ($this->options) {
      $context= $context->newInstance($context->asTraversable($this->options));
    }

    $source= $templates->source($this->name);
    if ($source->exists()) {
      $this->fn->enter($context);

      $template= $context->engine->load($this->name, $this->start, $this->end, '');
      $previous= $templates->register('@partial-block', $this->fn->block());
      try {
        $context->engine->write($template, $context, $out);
      } finally {
        $templates->register('@partial-block', $previous);
      }
    } else {
      $this->fn->write($context, $out);
    }
  }
}