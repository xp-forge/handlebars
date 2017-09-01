<?php namespace com\handlebarsjs;

class Lookup implements \lang\Value {
  protected $name;

  /**
   * Creates a new lookup instance
   *
   * @param  string $name
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
    return (string)$this->name;
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
    return $context->lookup($this->name);
  }

  /**
   * Compares
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->name, $value->name) : 1;
  }

  /** @return string */
  public function hashCode() {
    return md5($this->name);
  }

  /** @return string */
  public function toString() {
    return nameof($this).'('.$this->name.')';
  }
}