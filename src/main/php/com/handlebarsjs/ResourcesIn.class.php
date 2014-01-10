<?php namespace com\handlebarsjs;

/**
 * Classloader template loading loads templates from the class path.
 * The default file extension is `.handlebars`, though it can be
 * overwritten by passing additional extension to the constructor.
 */
class ResourcesIn extends \com\github\mustache\ResourcesIn {

  /**
   * Creates a new class loader based template loader
   *
   * @param var $base The delegate, either an IClassLoader or a string
   * @param string[] $extensions File extensions to check, including leading "."
   */
  public function __construct($arg, $extensions= array('.handlebars')) {
    parent::__construct($arg, $extensions);
  }
}