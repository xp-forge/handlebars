<?php namespace com\handlebarsjs;

class Quoted implements \lang\Value {
  protected $name;

  /**
   * Creates a new Quoted instance
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
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->chars, $value->chars) : 1;
  }

  /** @return string */
  public function hashCode() {
    return md5($this->chars);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'('.$this.')';
  }
}