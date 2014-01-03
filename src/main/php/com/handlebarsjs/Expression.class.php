<?php namespace com\handlebarsjs;

class Expression extends \lang\Object {
  protected $name;

  /**
   * Creates a new lookup instance
   *
   * @param  string $name
   */
  public function __construct($name, $options= array()) {
    $this->name= $name;
  }

  /**
   * (string) cast overloading
   *
   * @return string
   */
  public function __toString() {
    return '('.$this->name.')';
  }

  /**
   * Invocation overloading
   *
   * @param  var $context
   * @return var
   */
  public function __invoke($context) {
    return $context->lookup($this->name);
  }

  /**
   * Returns whether another lookup is equal to this
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $this->name === $cmp->name;
  }
}