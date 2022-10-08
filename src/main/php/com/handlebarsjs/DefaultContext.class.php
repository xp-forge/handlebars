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
   * Lookups up paths using the given segments, including literal segments.
   *
   * @param  string[] $segments
   * @return var
   */
  public function path($segments) {
    $v= $this->variables;
    foreach ($segments as $segment) {
      if (null === ($v= $this->pointer($v, $segment))) return null;
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
   * Parse input into segments. Handles literal segments including quoted
   * strings, e.g. `input.["item-class"]`.
   *
   * @see    https://handlebarsjs.com/guide/expressions.html#literal-segments
   * @param  string $input
   * @return string[]
   */
  private function segments($input) {
    $r= [];
    $o= 0;
    while ($o < strlen($input)) {
      if ('[' === $input[$o]) {
        $c= $input[$o + 1] ?? null;
        if ('"' === $c || "'" === $c) {
          $o+= 2;
          $q= '';

          quoted: $s= strcspn($input, $c, $o);
          if ('\\' === $input[$o + $s - 1] ?? null) {
            $q.= substr($input, $o, $s - 1).$c;
            $o+= $s + 1;
            goto quoted;
          }

          $r[]= $q.substr($input, $o, $s);
          $o+= $s + 4;
        } else {
          $s= strcspn($input, ']', $o);
          $r[]= substr($input, $o + 1, $s - 1);
          $o+= $s + 2;
        }
      } else {
        $s= strcspn($input, '.', $o);
        $r[]= substr($input, $o, $s);
        $o+= $s + 1;
      }
    }
    return $r;
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
   * @see    https://handlebarsjs.com/guide/expressions.html#disambiguating-helpers-calls-and-property-lookup
   * @param  ?string $name Name including optional segments, separated by dots.
   * @param  bool $helpers Whether to check helpers
   * @return var the variable, or null if nothing is found
   */
  public final function lookup($name, $helpers= true) {
    if (null === $name) {
      return $this->variables;
    } else if ('.' !== $name[0]) {
      $segments= $this->segments($name);
      if ('this' === $segments[0]) {
        return $this->path(array_slice($segments, 1));
      } else if ('@root' === $segments[0]) {
        $context= $this;
        while (null !== $context->parent) {
          $context= $context->parent;
        }
        return $context->path(array_slice($segments, 1));
      } else if ($helpers) {
        $v= $this->engine->helpers;
        foreach ($segments as $segment) {
          if (null === ($v= $this->helper($v, $segment))) goto property;
        }
        return $v;
      }

      property: $context= $this;
      do {
        $v= $context->path($segments);
      } while (null === $v && $context= $context->parent);
      return $v;
    } else if ('/' === $name[1] ?? null) {
      return $this->path(explode('.', substr($name, 2)));
    } else {
      $context= $this;
      $offset= 0;
      while (0 === substr_compare($name, '../', $offset, 3)) {
        if (null === ($context= $context->parent)) return null;
        $offset+= 3;
      }

      $path= substr($name, $offset);
      return '.' === $path ? $context->variables : $context->path(explode('.', $path));
    }
  }
}