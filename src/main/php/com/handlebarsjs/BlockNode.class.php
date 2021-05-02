<?php namespace com\handlebarsjs;

use com\github\mustache\Node;
use util\Objects;

/**
 * A block starts with {{#sec}} (or {{^sec}} for inverted blocks)
 * and ends with {{/sec}} and consists of 0..n nested nodes.
 * Optionally, a block may have an "else" section.
 */
class BlockNode extends Node {
  protected $name;
  protected $options;
  protected $fn;
  protected $inverse;
  protected $start;
  protected $end;

  /**
   * Creates a new section node
   *
   * @param string $name
   * @param string[] $options
   * @param com.handlebarsjs.Nodes $fn
   * @param com.handlebarsjs.Nodes $inverse
   * @param string $start
   * @param string $end
   */
  public function __construct($name, $options= [], Nodes $fn= null, Nodes $inverse= null, $start= '{{', $end= '}}') {
    $this->name= $name;
    $this->options= $options;
    $this->fn= $fn ?: new Nodes();
    $this->inverse= $inverse ?: new Nodes();
    $this->start= $start;
    $this->end= $end;
  }

  /**
   * Returns this section's name
   *
   * @return string
   */
  public function name() {
    return $this->name;
  }

  /**
   * Returns fn
   *
   * @return com.handlebarsjs.Nodes
   */
  public function fn() {
    return $this->fn;
  }

  /**
   * Returns inverse
   *
   * @return com.handlebarsjs.Nodes
   */
  public function inverse() {
    return $this->inverse;
  }

  /**
   * Returns options passed to this section
   *
   * @return string[]
   */
  public function options() {
    return $this->options;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return sprintf(
      "%s([\n  #%s%s -> %s else -> %s\n])",
      nameof($this),
      $this->name,
      ($this->options ? ' '.implode(' ', $this->options) : ''),
      Objects::stringOf($this->fn, '  '),
      Objects::stringOf($this->inverse, '  ')
    );
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    $value= $context->lookup($this->name);
    if ($context->isTruthy($value)) {
      $target= $this->fn;
    } else {
      $target= $this->inverse;
    }

    // Have defined value, apply following:
    // * If the value is a function, call it
    // * If the value is a list, expand list for all values inside
    // * If the value is a hash, use it as context
    // * Otherwise, simply delegate evaluation to node list
    if ($context->isCallable($value)) {
      $out->write($context->asRendering($value, $target, array_merge(
        $this->options,
        ['fn' => $this->fn, 'inverse' => $this->inverse]
      )));
    } else if ($context->isList($value)) {
      foreach ($context->asTraversable($value) as $element) {
        $target->write($context->asContext($element), $out);
      }
    } else if ($context->isHash($value)) {
      $target->write($context->asContext($value), $out);
    } else {
      $target->write($context, $out);
    }
  }

  /**
   * Check whether a given value is equal to this node list
   *
   * @param  var $cmp The value
   * @return bool
   */
  public function equals($cmp) {
    return (
      $cmp instanceof self &&
      $this->name === $cmp->name &&
      $this->start === $cmp->start &&
      $this->end === $cmp->end &&
      Objects::equal($this->options, $cmp->options) &&
      $this->fn->equals($cmp->fn) &&
      $this->inverse->equals($cmp->inverse)
    );
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    $s= $this->start.'#'.$this->name;
    $this->options && $s.= ' '.implode(' ', $this->options);
    $s.= $this->end.$this->fn->__toString();

    $this->inverse->length() && $s.= $this->start.'else'.$this->end.$this->inverse->__toString();
    return $s.$this->start.'/'.$this->name.$this->end;
  }
}