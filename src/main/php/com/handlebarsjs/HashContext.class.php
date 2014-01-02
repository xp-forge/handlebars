<?php namespace com\handlebarsjs;

/**
 * Hash context for the `each` helper.
 *
 * @test  xp://com.handlebarsjs.unittest.EachHelperTest
 */
class HashContext extends \com\github\mustache\Context {
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
      return $this->first;
    } else if ('@key' === $segment) {
      return $this->key;
    } else {
      return $this->backing->pointer($ptr, $segment);
    }
  }
}