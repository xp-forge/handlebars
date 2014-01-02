<?php namespace com\handlebarsjs;

use com\github\mustache\Node;
use com\github\mustache\NodeList;

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
   * @param com.github.mustache.NodeList $fn
   * @param com.github.mustache.NodeList $inverse
   * @param string $start
   * @param string $end
   */
  public function __construct($name, $options= array(), NodeList $fn= null, NodeList $inverse= null, $start= '{{', $end= '}}') {
    $this->name= $name;
    $this->options= $options;
    $this->fn= $fn ?: new NodeList();
    $this->inverse= $inverse ?: new NodeList();
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
   * @return com.github.mustache.NodeList
   */
  public function fn() {
    return $this->fn;
  }

  /**
   * Returns inverse
   *
   * @return com.github.mustache.NodeList
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
   * Returns options as string, indented with a space on the left if
   * non-empty, an empty string otherwise.
   *
   * @return string
   */
  protected function optionString() {
    $r= '';
    foreach ($this->options as $option) {
      if (false !== strpos($option, ' ')) {
        $r.= ' "'.$option.'"';
      } else {
        $r.= ' '.$option;
      }
    }
    return $r;
  }

  /**
   * Creates a string representation of this node
   *
   * @return string
   */
  public function toString() {
    return sprintf(
      "%s([\n  #%s%s -> %s else -> %s\n])",
      $this->getClassName(),
      $this->name,
      $this->optionString(),
      \xp::stringOf($this->fn, '  '),
      \xp::stringOf($this->inverse, '  ')
    );
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return string
   */
  public function evaluate($context, $indent= '') {
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
      $options= array_merge($this->options, array('fn' => $this->fn, 'inverse' => $this->inverse));
      return $context->engine->render($value($target, $context, $options), $context, $this->start, $this->end);
    } else if ($context->isList($value)) {
      $output= '';
      foreach ($context->asTraversable($value) as $element) {
        $output.= $target->evaluate($context->asContext($element));
      }
      return $output;
    } else if ($context->isHash($value)) {
      return $target->evaluate($context->asContext($value));
    } else {
      return $target->evaluate($context);
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
      \util\Objects::equal($this->options, $cmp->options) &&
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
    return sprintf(
      "%5\$s%1\$s%2\$s%6\$s\n%4\$s%5\$selse%6\$s\n%5\$s/%1\$s%6\$s\n",
      $this->name,
      $this->optionString(),
      (string)$this->fn,
      (string)$this->inverse,
      $this->start,
      $this->end
    );
  }
}