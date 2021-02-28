<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\{InMemory, Templating, TextNode};
use com\handlebarsjs\{Transformation, HandlebarsParser};
use unittest\{Assert, Test};

class TransformationTest {

  /** Returns templating for use as constructor parameter */
  private function templating(): Templating {
    return new Templating(new InMemory(), new HandlebarsParser());
  }

  #[Test]
  public function can_create() {
    new Transformation($this->templating());
  }

  #[Test]
  public function source() {
    $fixture= new Transformation($this->templating());
    $fixture->templates->register('test', new TextNode('My content'));
    Assert::equals('My content', $fixture->templates->load('test')->code());
  }

  #[Test]
  public function non_existant_source() {
    $fixture= new Transformation($this->templating());
    Assert::false($fixture->templates->load('non-existant')->exists());
  }

  #[Test]
  public function existant_source() {
    $fixture= new Transformation($this->templating());
    $fixture->templates->register('test', new TextNode('My content'));
    Assert::true($fixture->templates->load('test')->exists());
  }

  #[Test]
  public function register_returns_previous() {
    $fixture= new Transformation($this->templating());

    $prev= [];
    $prev[]= $fixture->templates->register('@partial-block', new TextNode('A'));
    $prev[]= (string)$fixture->templates->register('@partial-block', new TextNode('B'));
    $prev[]= (string)$fixture->templates->register('@partial-block', new TextNode('C'));

    Assert::equals([null, 'A', 'B'], $prev);
  }
}