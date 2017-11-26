<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\Templates;
use com\github\mustache\TextNode;
use com\github\mustache\InMemory;

class TemplatesTest extends \unittest\TestCase {

  #[@test]
  public function can_create() {
    new Templates();
  }

  #[@test]
  public function source() {
    $fixture= new Templates();
    $fixture->register('test', 'My content');
    $this->assertEquals('My content', $fixture->source('test')->code());
  }

  #[@test]
  public function non_existant_source() {
    $fixture= new Templates();
    $this->assertFalse($fixture->source('non-existant')->exists());
  }

  #[@test]
  public function existant_source() {
    $fixture= new Templates();
    $fixture->register('test', 'My content');
    $this->assertTrue($fixture->source('test')->exists());
  }

  #[@test]
  public function register_returns_previous() {
    $fixture= new Templates();

    $prev= [];
    $prev[]= $fixture->register('@partial-block', 'A');
    $prev[]= $fixture->register('@partial-block', new TextNode('B'))->code();
    $prev[]= $fixture->register('@partial-block', 'C')->code();

    $this->assertEquals([null, 'A', 'B'], $prev);
  }

  #[@test]
  public function listing_empty_by_default() {
    $this->assertEquals([], (new Templates())->listing()->templates());
  }

  #[@test]
  public function listing_with_registered() {
    $fixture= new Templates();
    $fixture->register('test', 'My content');
    $this->assertEquals(['test'], $fixture->listing()->templates());
  }

  #[@test]
  public function listing_with_delegate() {
    $fixture= new Templates();
    $fixture->delegate(new InMemory(['test' => 'My content']));
    $this->assertEquals(['test'], $fixture->listing()->templates());
  }

  #[@test]
  public function listing_with_delegate_and_registered() {
    $fixture= new Templates();
    $fixture->register('a', 'My content');
    $fixture->delegate(new InMemory(['b' => 'My content']));
    $this->assertEquals(['a', 'b'], $fixture->listing()->templates());
  }
}