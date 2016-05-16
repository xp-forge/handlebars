<?php namespace com\handlebarsjs;

use lang\Object;

/**
 * Partials
 *
 * @see  http://handlebarsjs.com/partials.html
 * @test xp://com.handlebarsjs.unittest.PartialNodeTest
 */
class PartialNode extends \com\github\mustache\Node {
  protected $template;
  protected $indent;

  /**
   * Creates a new partial node
   *
   * @param lang.Object $template The template
   * @param string $indent What to indent with
   */
  public function __construct(Object $template, $indent= '') {
    $this->template= $template;
    $this->indent= $indent;
  }

  /**
   * Returns this partial's template
   *
   * @return lang.Object
   */
  public function template() { return $this->template; }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'{{> '.$this->template->toString().'}}, indent= "'.$this->indent.'"';
  }

  /**
   * Check whether a given value is equal to this node list
   *
   * @param  var $cmp The value
   * @return bool
   */
  public function equals($cmp) {
    return 
      $cmp instanceof self &&
      $this->indent === $cmp->indent &&
      $this->template->equals($cmp->template)
    ;
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context) {
    try {
      return $context->engine->transform($this->template->__invoke($context), $context, '{{', '}}', $this->indent);
    } catch (TemplateNotFoundException $e) {
      return '';    // Spec dictates this, though I think this is not good behaviour.
    }
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return '{{> '.$this->template.'}}';
  }
}