<?php namespace com\handlebarsjs;

use com\github\mustache\{Context, DataContext};

/**
 * List context for the `each` helper.
 *
 * @test  xp://com.handlebarsjs.unittest.EachHelperTest
 */
class ListContext extends Context {
  protected $index;
  protected $last;
  protected $backing;

  /**
   * Constructor
   *
   * @param  int $index
   * @param  int $size
   * @param  parent $backing
   */
  public function __construct($index, $size, parent $backing) {
    parent::__construct($backing->variables, $backing->parent);
    $this->index= $index;
    $this->last= $size - 1;
    $this->backing= $backing;
  }

  /**
   * Returns a context inherited from this context
   *
   * @param  var $result
   * @param  parent $parent
   * @return self
   */
  public function asContext($result, $parent= null) {
    return new DataContext($result, $parent ?? $this);
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
      return 0 === $this->index ? 'true' : null;
    } else if ('@last' === $segment) {
      return $this->last === $this->index ? 'true' : null;
    } else if ('@index' === $segment) {
      return $this->index;
    } else {
      return $this->backing->pointer($ptr, $segment);
    }
  }
}