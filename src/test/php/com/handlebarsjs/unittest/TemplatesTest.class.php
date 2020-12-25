<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\{InMemory, TextNode};
use com\handlebarsjs\Templates;
use unittest\{Assert, Test};

class TemplatesTest {

  #[Test]
  public function can_create() {
    new Templates();
  }

  #[Test]
  public function source() {
    $fixture= new Templates();
    $fixture->register('test', 'My content');
    Assert::equals('My content', $fixture->source('test')->code());
  }

  #[Test]
  public function non_existant_source() {
    $fixture= new Templates();
    Assert::false($fixture->source('non-existant')->exists());
  }

  #[Test]
  public function existant_source() {
    $fixture= new Templates();
    $fixture->register('test', 'My content');
    Assert::true($fixture->source('test')->exists());
  }

  #[Test]
  public function register_returns_previous() {
    $fixture= new Templates();

    $prev= [];
    $prev[]= $fixture->register('@partial-block', 'A');
    $prev[]= $fixture->register('@partial-block', new TextNode('B'))->code();
    $prev[]= $fixture->register('@partial-block', 'C')->code();

    Assert::equals([null, 'A', 'B'], $prev);
  }

  #[Test]
  public function listing_empty_by_default() {
    Assert::equals([], (new Templates())->listing()->templates());
  }

  #[Test]
  public function listing_with_registered() {
    $fixture= new Templates();
    $fixture->register('test', 'My content');
    Assert::equals(['test'], $fixture->listing()->templates());
  }

  #[Test]
  public function listing_with_delegate() {
    $fixture= new Templates();
    $fixture->delegate(new InMemory(['test' => 'My content']));
    Assert::equals(['test'], $fixture->listing()->templates());
  }

  #[Test]
  public function listing_with_delegate_and_registered() {
    $fixture= new Templates();
    $fixture->register('a', 'My content');
    $fixture->delegate(new InMemory(['b' => 'My content']));
    Assert::equals(['a', 'b'], $fixture->listing()->templates());
  }
}