<?php namespace com\handlebarsjs;

/**
 * File-based template loading loads templates from a given folder.
 * The default file extension is `.handlebars`, though it can be
 * overwritten by passing additional extension to the constructor.
 */
class FilesIn extends \com\github\mustache\FilesIn {

  /**
   * Creates a new file-based template loader
   *
   * @param var $base The base folder, either an io.Folder or a string
   * @param string[] $extensions File extensions to check, including leading "."
   */
  public function __construct($arg, $extensions= array('.handlebars')) {
    parent::__construct($arg, $extensions);
  }
}