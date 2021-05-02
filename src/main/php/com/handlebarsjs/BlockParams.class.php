<?php namespace com\handlebarsjs;

use lang\Value;
use util\Objects;

class BlockParams implements Value {
  public $names;

  /**
   * Creates a new constant
   *
   * @param  string[] $names
   */
  public function __construct($names) {
    $this->names= $names;
  }

  /**
   * (string) cast overloading
   *
   * @return string
   */
  public function __toString() {
    return 'as |'.implode(' ', $this->names).'|';
  }

  /**
   * Invocation overloading
   *
   * @param  com.github.mustache.Node $node
   * @param  com.github.mustache.Context $context
   * @param  var[] $options
   * @return var
   */
  public function __invoke($node, $context, $options) {
    return [$this->names[0] => $context]; // FIXME
  }

  /**
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? Objects::compare($this->names, $value->names) : 1;
  }

  /** @return string */
  public function hashCode() {
    return Objects::hashOf($this->name);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'('.implode(' ', $this->names).')';
  }
}