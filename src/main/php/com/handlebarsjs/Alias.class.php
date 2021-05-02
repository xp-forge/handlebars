<?php namespace com\handlebarsjs;

class Alias implements \lang\Value {
  private $name;

  /**
   * Creates a new constant
   *
   * @param  var $name
   */
  public function __construct($name) {
    $this->name= $name;
  }

  /**
   * (string) cast overloading
   *
   * @return string
   */
  public function __toString() {
    return 'as |'.$this->name.'|';
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
    return [$this->name => $context];
  }

  /**
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? $this->name <=> $value->name : 1;
  }

  /** @return string */
  public function hashCode() {
    return md5($this->name);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'('.$this.')';
  }
}