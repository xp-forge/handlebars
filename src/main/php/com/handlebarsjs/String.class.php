<?php namespace com\handlebarsjs;

class String extends \lang\Object {
  protected $name;

  /**
   * Creates a new String instance
   *
   * @param  string $chars
   */
  public function __construct($chars) {
    $this->chars= $chars;
  }

  /**
   * (string) cast overloading
   *
   * @return string
   */
  public function __toString() {
    return '"'.str_replace('"', '\\"', $this->chars).'"';
  }

  /**
   * Invocation overloading
   *
   * @param  var $context
   * @return var
   */
  public function __invoke($context) {
    return $this->chars;
  }

  /**
   * Returns whether another String is equal to this
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->chars === $cmp->chars;
  }
}