<?php namespace com\handlebarsjs;

use util\log\LogCategory;
use util\log\LogLevel;

/**
 * LogCategory::log()
 *
 * This can be optimized once we want to set the dependency on 
 * xp-framework/core to a 6.0.0 minimum.
 *
 * @see https://github.com/xp-framework/core/pull/4
 */
class LogCategoryExtensions extends \lang\Object {

  static function __import($scope) {
    \xp::extensions(__CLASS__, $scope);
  }
  
  /**
   * Log method
   *
   * @param  util.log.LogCategory $self
   * @param  int $level
   * @param  var[] $args
   */
  public static function log(LogCategory $self, $level, $args) {
    call_user_func_array(array($self, LogLevel::nameOf($level)), $args);
  }
}