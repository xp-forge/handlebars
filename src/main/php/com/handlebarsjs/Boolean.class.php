<?php namespace com\handlebarsjs;

class Boolean extends \lang\Object {
  protected $value;

  /**
   * Creates a new boolean instance
   *
   * @param  bool $value
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
    return $this->value ? 'true' : 'false';
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
}