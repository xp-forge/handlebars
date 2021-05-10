<?php namespace com\handlebarsjs;

use com\github\mustache\{Context, DataContext};

/**
 * Default context for handlebars. Supports `@root` and `this` in addition
 * to `./` and `../` notations from Mustache.
 *
 * @test  xp://com.handlebarsjs.unittest.ExecutionTest
 */
class DefaultContext extends DataContext {

  /**
   * Lookups up paths using the given segments
   *
   * @param  string[] $segments
   * @return var
   */
  public function path($segments) {
    $v= $this->variables;
    foreach ($segments as $segment) {
      if ($v !== null) $v= $this->pointer($v, $segment);
    }
    return $v;
  }

  /**
   * Returns a context inherited from a given context, or, if omitted,
   * from this context.
   *
   * @param  var $result
   * @param  self $parent
   * @return self
   */
  public final function newInstance($result, Context $parent= null) {
    return new self($result, $parent ?: $this);
  }

  /**
   * Looks up variable:
   *
   * - (null)
   * - person.name
   * - ./name
   * - ../name (and ../@index but not ../@root)
   * - ../../name
   * - this (but no special meaning for ./this and ../this)
   * - @root
   *
   * @param  ?string $name Name including optional segments, separated by dots.
   * @param  bool $helpers Whether to check helpers
   * @return var the variable, or null if nothing is found
   */
  public final function lookup($name, $helpers= true) {
    if (null === $name) {
      $segments= [];
      $v= $this->variables;
    } else if ('.' !== $name[0]) {
      $segments= explode('.', $name);
      if ('this' === $segments[0]) {
        $v= $this->path(array_slice($segments, 1));
      } else if ('@root' === $segments[0]) {
        $context= $this;
        while (null !== $context->parent) {
          $context= $context->parent;
        }
        return $context->path(array_slice($segments, 1));
      } else {
        $context= $this;
        do {
          $v= $context->path($segments);
        } while (null === $v && $context= $context->parent);
      }
    } else if (0 === strncmp('./', $name, 2)) {
      $segments= explode('.', substr($name, 2));
      $v= $this->path($segments);
    } else {
      $context= $this;
      $offset= 0;
      while (0 === substr_compare($name, '../', $offset, 3)) {
        $context= $context->parent;
        $offset+= 3;
      }
      $segments= explode('.', substr($name, $offset));
      $v= $context ? $context->path($segments) : null;
    }

    // Check helpers
    if (null === $v && $helpers) {
      $v= $this->engine->helpers;
      foreach ($segments as $segment) {
        if ($v !== null) $v= $this->helper($v, $segment);
      }
    }
    return $v;
  }
}