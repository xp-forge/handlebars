<?php namespace com\handlebarsjs;

use com\github\mustache\TemplateLoader;
use com\github\mustache\TemplateNotFoundException;
use io\streams\MemoryInputStream;

class Templates implements TemplateLoader {
  private $templates= [];
  private $delegate;

  public function delegate($delegate) {
    $this->delegate= $delegate;
  }

  public function register($name, $content) {
    $this->templates[$name]= $content;
  }

  public function remove($name) {
    unset($this->templates[$name]);
  }

  /**
   * Load a template by a given name
   *
   * @param  string $name The template name without file extension
   * @return io.streams.InputStream
   * @throws com.github.mustache.TemplateNotFoundException
   */
  public function load($name) {
    if (isset($this->templates[$name])) {
      return new MemoryInputStream($this->templates[$name]);
    } else if ($this->delegate) {
      return $this->delegate->load($name);
    } else {
      throw new TemplateNotFoundException('Cannot find template '.$name);
    }
  }
}