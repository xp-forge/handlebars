<?php namespace com\handlebarsjs;

use com\github\mustache\NodeList;
use lang\IllegalArgumentException;

/** @test com.handlebarsjs.unittest.NodesTest */
class Nodes extends NodeList {
  private $partials= [];

  /**
   * Declares a partial and returns the nodes associated with the name.
   *
   * @param  string|com.handlebarsjs.Quoted $partial
   * @param  ?parent|self $nodes
   * @return self
   * @throws lang.IllegalArgumentException
   */
  public function declare($partial, $nodes= null) {
    if ($partial instanceof Quoted) {
      $name= $partial->chars;
    } else if (is_string($partial)) {
      $name= $partial;
    } else {
      throw new IllegalArgumentException('Partial names must be strings or Quoted instances, have '.typeof($partial));
    }

    if ($nodes instanceof parent) {
      return $this->partials[$name]= new self($nodes->nodes);
    } else {
      return $this->partials[$name]= $nodes ?? new self();
    }
  }

  /**
   * Inherits partials from a given parent
   *
   * @param  self $parent
   * @return self
   */
  public function inheriting(self $parent) {
    $this->partials= $parent->partials;
    return $this;
  }

  /** @return [:self] */
  public function partials() { return $this->partials; }

  /**
   * Returns a partial with a given name, or NULL if this partial does
   * not exist.
   *
   * @param  string $partial
   * @return ?self
   */
  public function partial($partial) {
    return $this->partials[$partial] ?? null;
  }

  /**
   * Evaluates this node
   *
   * @param  com.github.mustache.Context $context the rendering context
   * @param  io.streams.OutputStream $out
   */
  public function write($context, $out) {
    $templates= $context->engine->templates();
    $previous= [];
    foreach ($this->partials as $name => $partial) {
      $previous[$name]= $templates->register($name, $partial);
    }

    // Restore partials to previous state after processing this template
    try {
      parent::write($context, $out);
    } finally {
      foreach ($previous as $name => $partial) {
        $templates->register($name, $partial);
      }
    }
  }
}