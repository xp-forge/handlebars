<?php namespace com\handlebarsjs;

/**
 * Hash context for the `each` helper.
 *
 * @test  xp://com.handlebarsjs.unittest.EachHelperTest
 */
class HashContext extends DefaultContext {
  private $map, $element, $index;
  private $first= true;
  private $last= null;
  private $key= null;

  /**
   * Constructor
   *
   * @param  parent $parent
   * @param  [:var]|Generator $iterable
   * @param  ?string $element
   * @param  ?string $index
   */
  public function __construct(parent $parent, $iterable, $element= null, $index= null) {
    parent::__construct(null, $parent);
    $this->map= $iterable;
    $this->last= is_array($iterable) ? (end($this->map) ? key($this->map) : null) : null; // array_key_last for PHP >= 7.3
    $this->element= $element;
    $this->index= $index;
  }

  /**
   * Returns a context inherited from this context
   *
   * @param  var $result
   * @return self
   */
  public function asContext($result) {
    return new parent($result, $this);
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

  public function path($segments) {
    if (null === ($start= $segments[0] ?? null) || $this->element === $start) {
      return $this->variables;
    } else if ('@key' === $start || $this->index === $start) {
      return $this->key;
    } else if ('@first' === $start) {
      return $this->first ? 'true' : null;
    } else if ('@last' === $start && null !== $this->last) {
      return $this->key === $this->last ? 'true' : null;
    } else {
      return parent::path($segments);
    }
  }
}