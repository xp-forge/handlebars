<?php namespace com\handlebarsjs;

/** Block helpers factory */
class BlockHelpers {
  private $impl= [];

  /** @param [:string] $byName */
  public function __construct($byName) {
    foreach ($byName as $name => $impl) {
      $this->register($name, $impl);
    }
  }

  /**
   * Register a named implementation
   *
   * @param  string $name
   * @param  ?string|function(var[], com.github.ParseState): com.github.mustache.Node $impl
   * @return self
   */
  public function register($name, $impl) {
    if (null === $impl) {
      unset($this->impl[$name]);
    } else if (is_string($impl)) {
      $this->impl[$name]= function($options, $state) use($impl) {
        return $state->target->add(new $impl($options, null, null, $state->start, $state->end));
      };
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
      return $impl($options, $state);
    } else {
      return $state->target->add(new BlockNode($name, $options, null, null, $state->start, $state->end));
    }
  }
}