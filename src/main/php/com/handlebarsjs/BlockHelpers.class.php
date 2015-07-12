<?php namespace com\handlebarsjs;

use lang\XPClass;

/**
 * Block helpers factory
 */
class BlockHelpers extends \lang\Object {
  protected static $byName= [];

  static function __static() {
    self::$byName['if']= XPClass::forName('com.handlebarsjs.IfBlockHelper');
    self::$byName['unless']= XPClass::forName('com.handlebarsjs.UnlessBlockHelper');
    self::$byName['with']= XPClass::forName('com.handlebarsjs.WithBlockHelper');
    self::$byName['each']= XPClass::forName('com.handlebarsjs.EachBlockHelper');
  }

  /**
   * Gets a block helper class by a given name
   *
   * @param  string $name
   * @return lang.XPClass or NULL
   */
  public static function named($name) {
    return isset(self::$byName[$name]) ? self::$byName[$name] : null;
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