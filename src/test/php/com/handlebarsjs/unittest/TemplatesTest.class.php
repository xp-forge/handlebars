<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\Templates;
use com\github\mustache\TemplateNotFoundException;

class TemplatesTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new Templates();
  }

  #[@test]
  public function load() {
    $fixture= new Templates();
    $fixture->register('@partial-block', 'My content');
    $this->assertEquals('My content', $fixture->load('@partial-block')->read());
  }

  #[@test, @expect(TemplateNotFoundException::class)]
  public function load_non_existant() {
    $fixture= new Templates();
    $fixture->load('non-existant');
  }

  #[@test, @expect(TemplateNotFoundException::class)]
  public function load_removed() {
    $fixture= new Templates();
    $fixture->register('@partial-block', 'My content');
    $fixture->remove('@partial-block');
    $fixture->load('@partial-block');
  }
}