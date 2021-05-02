<?php namespace com\handlebarsjs;

use com\github\mustache\{Context, DataContext};

/**
 * List context for the `each` helper.
 *
 * @test  xp://com.handlebarsjs.unittest.EachHelperTest
 */
class ListContext extends DataContext {
  private $list, $last, $element, $index;
  private $offset= null;

  /**
   * Constructor
   *
   * @param  com.github.mustache.Context $parent
   * @param  var[] $list
   * @param  ?string $element
   * @param  ?string $index
   */
  public function __construct(Context $parent, $list, $element= null, $index= null) {
    parent::__construct(null, $parent);
    $this->list= $list;
    $this->last= sizeof($this->list) - 1;
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
    return new DataContext($result, $this);
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
    foreach ($this->list as $this->offset => $this->variables) {
      $fn->write($this, $out);
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
    if ('@index' === $s || $this->index === $s) {
      return $this->offset;
    } else if ('@first' === $s) {
      return 0 === $this->offset ? 'true' : null;
    } else if ('@last' === $s) {
      return $this->last === $this->offset ? 'true' : null;
    } else if ($this->element === $s) {
      return $this->variables;
    }

    return parent::lookup0($v, $segments);
  }
}