<?php namespace com\handlebarsjs;

class Constant extends \lang\Object {
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
   * Returns whether another boolean is equal to this
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->value === $cmp->value;
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    return nameof($this).'('.$this.')';
  }
}