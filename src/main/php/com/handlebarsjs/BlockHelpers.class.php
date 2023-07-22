<?php namespace com\handlebarsjs;

/** Block helpers factory */
class BlockHelpers {
  private $impl;

  /** @param [:string] $impl */
  public function __construct($impl) {
    $this->impl= $impl;
  }

  /**
   * Register a named implementation
   *
   * @param  string $name
   * @param  ?string $impl
   * @return self
   */
  public function register($name, $impl) {
    if (null === $impl) {
      unset($this->impl[$name]);
    } else {
      $this->impl[$name]= $impl;
    }
    return $this;
  }

  /**
   * Creates a new with block helper
   *
   * - Creates instances of named block implementations
   * - Registers `*inline` partials in top-level nodes
   * - Uses default block implementation otherwise
   *
   * @param  var[] $options
   * @param  com.github.ParseState $state
   * @return com.github.mustache.Node
   */
  public function newInstance($options, $state) {
    $name= array_shift($options);
    if ($impl= $this->impl[$name] ?? null) {
      return $state->target->add(new $impl($options, null, null, $state->start, $state->end));
    } else if ('*inline' === $name) {
      return new BlockNode('inline', $options, $state->parents[0]->declare($options[0] ?? null));
    } else {
      return $state->target->add(new BlockNode($name, $options, null, null, $state->start, $state->end));
    }
  }
}