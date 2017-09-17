<?php namespace com\handlebarsjs;

use lang\MethodNotImplementedException;

/** 
 * Decorators allow for blocks to be annotated with metadata or wrapped in
 * functionality prior to execution of the block.
 *
 * @see  https://github.com/wycats/handlebars.js/blob/master/docs/decorators-api.md
 */
class Decoration {
  private $kind, $options, $fn;

  /**
   * Creates a new decoration
   *
   * @param  string $kind
   * @param  var[] $options
   * @param  com.handlebarsjs.Nodes $fn
   */
  public function __construct($kind, $options, Nodes $fn= null) {
    $this->kind= $kind;
    $this->options= $options;
    $this->fn= $fn ?: new Nodes();
  }

  /** @return string */
  public function name() { return substr($this->kind, 1); }

  /** @return com.handlebarsjs.Nodes */
  public function fn() { return $this->fn; }

  /**
   * Evaluates this decoration
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @return void
   */
  public function enter($context) {
    if (isset($context->engine->helpers[$this->kind])) {
      $f= $context->engine->helpers[$this->kind];
      $f($this->fn, $context, $this->options);
    } else {
      throw new MethodNotImplementedException('No such decorator '.$this->kind);
    }
  }
}