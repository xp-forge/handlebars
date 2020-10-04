<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\{InMemory, TextNode};
use com\handlebarsjs\Templates;
use unittest\Test;

class TemplatesTest extends \unittest\TestCase {

  #[Test]
  public function can_create() {
    new Templates();
  }

  #[Test]
  public function source() {
    $fixture= new Templates();
    $fixture->register('test', 'My content');
    $this->assertEquals('My content', $fixture->source('test')->code());
  }

  #[Test]
  public function non_existant_source() {
    $fixture= new Templates();
    $this->assertFalse($fixture->source('non-existant')->exists());
  }

  #[Test]
  public function existant_source() {
    $fixture= new Templates();
    $fixture->register('test', 'My content');
    $this->assertTrue($fixture->source('test')->exists());
  }

  #[Test]
  public function register_returns_previous() {
    $fixture= new Templates();

    $prev= [];
    $prev[]= $fixture->register('@partial-block', 'A');
    $prev[]= $fixture->register('@partial-block', new TextNode('B'))->code();
    $prev[]= $fixture->register('@partial-block', 'C')->code();

    $this->assertEquals([null, 'A', 'B'], $prev);
  }

  #[Test]
  public function listing_empty_by_default() {
    $this->assertEquals([], (new Templates())->listing()->templates());
  }

  #[Test]
  public function listing_with_registered() {
    $fixture= new Templates();
    $fixture->register('test', 'My content');
    $this->assertEquals(['test'], $fixture->listing()->templates());
  }

  #[Test]
  public function listing_with_delegate() {
    $fixture= new Templates();
    $fixture->delegate(new InMemory(['test' => 'My content']));
    $this->assertEquals(['test'], $fixture->listing()->templates());
  }

  #[Test]
  public function listing_with_delegate_and_registered() {
    $fixture= new Templates();
    $fixture->register('a', 'My content');
    $fixture->delegate(new InMemory(['b' => 'My content']));
    $this->assertEquals(['a', 'b'], $fixture->listing()->templates());
  }
}