<?php namespace com\handlebarsjs;

use lang\MethodNotImplementedException;

/** 
 * Decorators allow for blocks to be annotated with metadata or wrapped in
 * functionality prior to execution of the block.
 *
 * @see  https://github.com/wycats/handlebars.js/blob/master/docs/decorators-api.md
 */
class Decoration {
  private $name, $options, $fn;

  /**
   * Creates a new decoration
   *
   * @param  string $name
   * @param  var[] $options
   * @param  com.handlebarsjs.Nodes $fn
   */
  public function __construct($name, $options, Nodes $fn= null) {
    $this->name= $name;
    $this->options= $options;
    $this->fn= $fn ?: new Nodes();
  }

  /** @return string */
  public function name() { return $this->name; }

  /** @return com.handlebarsjs.Nodes */
  public function fn() { return $this->fn; }

  /**
   * Evaluates this decoration
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return void
   */
  public function evaluate($context) {
    $l= '*'.$this->name;
    if (isset($context->engine->helpers[$l])) {
      $f= $context->engine->helpers[$l];
      $f($this->fn, $context, $this->options);
    } else {
      throw new MethodNotImplementedException('No such decorator '.$l);
    }
  }
}