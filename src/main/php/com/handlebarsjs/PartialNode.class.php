<?php namespace com\handlebarsjs;

use com\github\mustache\Node;
use lang\Value;
use util\Objects;

/**
 * Partials
 *
 * @see  http://handlebarsjs.com/partials.html
 * @test xp://com.handlebarsjs.unittest.PartialNodeTest
 */
class PartialNode extends Node {
  protected $template, $options, $indent;

  /**
   * Creates a new partial node
   *
   * @param lang.Value $template The template
   * @param [:var] $options
   * @param string $indent What to indent with
   */
  public function __construct(Value $template, $options= [], $indent= '') {
    $this->template= $template;
    $this->options= $options;
    $this->indent= $indent;
  }

  /**
   * Returns this partial's template
   *
   * @return lang.Value
   */
  public function template() { return $this->template; }

  /**
   * Returns options passed to this section
   *
   * @return string[]
   */
  public function options() { return $this->options; }

  /**
   * Returns options as string, indented with a space on the left if
   * non-empty, an empty string otherwise.
   *
   * @return string
   */
  protected function optionString() {
    $r= '';
    foreach ($this->options as $key => $option) {
      $r.= ' '.$key.'= '.(string)$option;
    }
    return $r;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'({{> '.$this->template->toString().$this->optionString().'}}, indent= "'.$this->indent.'")';
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
      Objects::equal($this->options, $cmp->options) &&
      $this->template->equals($cmp->template)
    ;
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {

    // {{> partial context}} vs {{> partial key="Value"}}
    if (isset($this->options[0])) {
      $context= $context->newInstance($this->options[0]($this, $context, []));
    } else if ($this->options) {
      $pass= [];
      foreach ($context->asTraversable($this->options) as $key => $value) {
        $pass[$key]= $value($this, $context, []);
      }
      $context= $context->newInstance($pass);
    }

    $engine= $context->engine;
    $template= $engine->load($this->template->__invoke($this, $context, []), '{{', '}}', $this->indent);
    $engine->write($template, $context, $out);
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return '{{> '.$this->template.$this->optionString().'}}';
  }
}