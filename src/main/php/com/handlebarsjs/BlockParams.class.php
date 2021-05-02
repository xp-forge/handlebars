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
    return Objects::hashOf($this->names);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'('.implode(' ', $this->names).')';
  }
}