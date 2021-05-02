<?php namespace com\handlebarsjs;

use com\github\mustache\{Context, DataContext};

/**
 * Hash context for the `each` helper.
 *
 * @test  xp://com.handlebarsjs.unittest.EachHelperTest
 */
class HashContext extends DataContext {
  private $map, $element, $index;
  private $first= true;
  private $last= null;
  private $key= null;

  /**
   * Constructor
   *
   * @param  com.github.mustache.Context $parent
   * @param  [:var]|Generator $iterable
   * @param  ?string $element
   * @param  ?string $index
   */
  public function __construct(Context $parent, $iterable, $element= null, $index= null) {
    parent::__construct(null, $parent);
    $this->map= $iterable;
    $this->last= is_array($iterable) ? (end($this->map) ? key($this->map) : null) : null; // array_key_last for PHP >= 7.3
    $this->element= $element;
    $this->index= $index;
  }

  /**
   * Writes output
   *
   * @param  com.handlebarsjs.Nodes $fn
   * @param  io.streams.OutputStream $out
   */
  public function write($fn, $out) {

    // We modify this context directly while we're going - this way,
    // we save creating context instances for each element.
    foreach ($this->map as $this->key => $this->variables) {
      $fn->write($this, $out);
      $this->first= false;
    }
  }

  /**
   * Looks up segments inside a given collection
   *
   * @param  var $v
   * @param  string[] $segments
   * @return var
   */
  protected function lookup0($v, $segments) {
    $s= $segments[0];
    if ('@key' === $s || $this->index === $s) {
      return $this->key;
    } else if ('@first' === $s) {
      return $this->first ? 'true' : null;
    } else if ('@last' === $s && null !== $this->last) {
      return $this->key === $this->last ? 'true' : null;
    } else if ($this->element === $s) {
      return $this->variables;
    }

    return parent::lookup0($v, $segments);
  }
}