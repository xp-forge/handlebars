<?php namespace com\handlebarsjs;

use lang\XPClass;

/**
 * Block helpers factory
 */
class BlockHelpers {
  private static $byName;

  static function __static() {
    self::$byName= [
      'if'     => new XPClass(IfBlockHelper::class),
      'unless' => new XPClass(UnlessBlockHelper::class),
      'with'   => new XPClass(WithBlockHelper::class),
      'each'   => new XPClass(EachBlockHelper::class),
      '>'      => new XPClass(PartialBlockHelper::class)
    ];
  }

  /**
   * Gets a block helper class by a given name
   *
   * @param  string $name
   * @return ?lang.XPClass
   */
  public static function named($name) {
    return self::$byName[$name] ?? null;
  }

  /**
   * Creates a new with block helper
   *
   * @param string $name
   * @param string[] $options
   * @param com.github.mustache.NodeList $fn
   * @param com.github.mustache.NodeList $inverse
   * @param string $start
   * @param string $end
   */
  public static function newInstance($name, $options, $fn, $inverse, $start, $end) {
    if (isset(self::$byName[$name])) {
      return self::$byName[$name]->newInstance($options, $fn, $inverse, $start, $end);
    } else {
      return new BlockNode($name, $options, $fn, $inverse, $start, $end);
    }
  }
}