<?php namespace com\handlebarsjs;

use com\github\mustache\NodeList;

/**
 * A raw section starts with `{{{{...}}}}` and ends with `{{{{/...}}}}`.
 *
 * @see  https://handlebarsjs.com/guide/expressions.html#escaping-handlebars-expressions
 */
class RawSection extends NodeList {
  private $name;

  /** @param string $name */
  public function __construct($name) {
    parent::__construct([]);
    $this->name= $name;
  }

  /** @return string */
  public function name() { return $this->name; }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    foreach ($this->nodes as $node) {
      $out->write($node->__toString());
    }
  }

  /**
   * Overload (string) cast
   *
   * @return string
   */
  public function __toString() {
    return '{{{{'.$this->name.'}}}}'.trim(implode('', $this->nodes)).'{{{{/'.$this->name.'}}}}';
  }
}