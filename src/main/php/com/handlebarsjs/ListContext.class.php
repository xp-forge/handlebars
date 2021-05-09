<?php namespace com\handlebarsjs;

/**
 * List context for the `each` helper.
 *
 * @test  xp://com.handlebarsjs.unittest.EachHelperTest
 */
class ListContext extends DefaultContext {
  private $list, $last, $element, $index;
  private $offset= null;

  /**
   * Constructor
   *
   * @param  parent $parent
   * @param  var[] $list
   * @param  ?string $element
   * @param  ?string $index
   */
  public function __construct(parent $parent, $list, $element= null, $index= null) {
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
    foreach ($this->list as $this->offset => $this->variables) {
      $fn->write($this, $out);
    }
  }

  public function path($segments) {
    if (null === ($start= $segments[0] ?? null) || $this->element === $start) {
      return $this->variables;
    } else if ('@index' === $start || $this->index === $start) {
      return $this->offset;
    } else if ('@first' === $start) {
      return 0 === $this->offset ? 'true' : null;
    } else if ('@last' === $start) {
      return $this->last === $this->offset ? 'true' : null;
    } else {
      return parent::path($segments);
    }
  }
}