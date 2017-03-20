<?php namespace com\handlebarsjs;

use com\github\mustache\Context;
use com\github\mustache\DataContext;

/**
 * Hash context for the `each` helper.
 *
 * @test  xp://com.handlebarsjs.unittest.EachHelperTest
 */
class HashContext extends Context {
  protected $key;
  protected $first;
  protected $backing;

  /**
   * Constructor
   *
   * @param  string $key
   * @param  bool $first
   * @param  parent $backing
   */
  public function __construct($key, $first, parent $backing) {
    parent::__construct($backing->variables, $backing->parent);
    $this->key= $key;
    $this->first= $first;
    $this->backing= $backing;
  }

  /**
   * Returns a context inherited from this context
   *
   * @param  var $result
   * @return self
   */
  public function asContext($result) {
    return new DataContext($result, $this);
  }

  /**
   * Helper method to retrieve a pointer inside a given data structure
   * using a given segment. Returns null if there is no such segment.
   * Called from within the `lookup()` method for every segment in the
   * variable name.
   *
   * @param  var $ptr
   * @param  string $segment
   * @return var
   */
  protected function pointer($ptr, $segment) {
    if ('@first' === $segment) {
      return $this->first ? 'true' : null;
    } else if ('@key' === $segment) {
      return $this->key;
    } else {
      return $this->backing->pointer($ptr, $segment);
    }
  }
}