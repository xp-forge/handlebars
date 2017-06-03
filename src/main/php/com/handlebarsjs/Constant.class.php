<?php namespace com\handlebarsjs;

class Constant implements \lang\Value {
  private $value;

  /**
   * Creates a new constant
   *
   * @param  var $value
   */
  public function __construct($value) {
    $this->value= $value;
  }

  /**
   * (string) cast overloading
   *
   * @return string
   */
  public function __toString() {
    if (null === $this->value) {
      return 'null';
    } else if (false === $this->value) {
      return 'false';
    } else if (true === $this->value) {
      return 'true';
    } else {
      return (string)$this->value;
    }
  }

  /**
   * Invocation overloading
   *
   * @param  var $context
   * @return var
   */
  public function __invoke($context) {
    return $this->value;
  }

  /**
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->value, $value->value) : 1;
  }

  /** @return string */
  public function hashCode() {
    return md5($this->value);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'('.$this.')';
  }
}