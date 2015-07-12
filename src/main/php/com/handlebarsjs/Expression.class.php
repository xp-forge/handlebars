<?php namespace com\handlebarsjs;

class Expression extends \lang\Object {
  protected $name;
  protected $options;

  /**
   * Creates a new lookup instance
   *
   * @param  string $name
   * @param  var[] $options
   */
  public function __construct($name, $options= []) {
    $this->name= $name;
    $this->options= $options;
  }

  /**
   * (string) cast overloading
   *
   * @return string
   */
  public function __toString() {
    return '('.$this->name.($this->options ? ' '.implode(' ', $this->options) : '').')';
  }

  /**
   * Invocation overloading
   *
   * @param  var $context
   * @return var
   */
  public function __invoke($context) {
    $r= $context->lookup($this->name);
    if ($context->isCallable($r)) {

      // Subexpressions are called with their options as arguments,
      // which in turn may be subexpressions or values to be looked up.
      $pass= [];
      foreach ($this->options as $option) {
        $pass[]= $option($context);
      }
      return call_user_func_array($r, $pass);
    } else {
      return $r;
    }
  }

  /**
   * Returns whether another lookup is equal to this
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    return (
      $cmp instanceof self &&
      $this->name === $cmp->name &&
      \util\Objects::equal($this->options, $cmp->options)
    );
  }
}