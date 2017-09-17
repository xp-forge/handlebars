<?php namespace com\handlebarsjs;

use com\github\mustache\NodeList;
use com\github\mustache\TemplateNotFoundException;

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
   * @return string
   */
  public function evaluate($context) {
    $templates= $context->engine->getTemplates();

    try {
      $templates->register('@partial-block', $this->fn);
      return $context->engine->transform($this->name, $context, $this->start, $this->end, '');
    } catch (TemplateNotFoundException $e) {
      return $this->fn->evaluate($context);
    } finally {
      $templates->remove('@partial-block');
    }
  }
}