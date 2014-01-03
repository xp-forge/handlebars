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
  public function __construct($name, $options= array()) {
    $this->name= $name;
    $this->options= $options;
  }

  /**
   * Returns options as string, indented with a space on the left if
   * non-empty, an empty string otherwise.
   *
   * @return string
   */
  protected function optionString() {
    $r= '';
    foreach ($this->options as $option) {
      if ($option instanceof \lang\Generic) {
        $r.= ' '.$option;
      } else {
        $r.= ' "'.$option.'"';
      }
    }
    return $r;
  }

  /**
   * (string) cast overloading
   *
   * @return string
   */
  public function __toString() {
    return '('.$this->name.$this->optionString().')';
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
    return (
      $cmp instanceof self &&
      $this->name === $cmp->name &&
      \util\Objects::equal($this->options, $cmp->options)
    );
  }
}