<?php namespace com\handlebarsjs;

use com\github\mustache\TemplateListing;

class CompositeListing extends TemplateListing {
  private $templates;
  private $delegate;

  public function __construct($templates, $delegate) {
    $this->templates= $templates;
    $this->delegate= $delegate;
  }

  public function templates() {
    return array_merge(array_keys($this->templates), $this->delegate->templates());
  }

  public function packages() {
    return $this->delegate->packages();
  }
}