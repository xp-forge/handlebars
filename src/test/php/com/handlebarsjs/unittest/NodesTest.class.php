<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\VariableNode;
use com\handlebarsjs\Nodes;
use test\{Assert, Before, Test};

class NodesTest {
  private $partial;

  #[Before]
  public function partial() {
    $this->partial= new Nodes([new VariableNode('test')]);
  }

  #[Test]
  public function can_create() {
    new Nodes();
  }

  #[Test]
  public function partial_null() {
    Assert::null((new Nodes())->partial('test'));
  }

  #[Test]
  public function partials_empty() {
    Assert::equals([], (new Nodes())->partials());
  }

  #[Test]
  public function declare_partial() {
    $fixture= new Nodes();
    $fixture->declare('test', $this->partial);

    Assert::equals($this->partial, $fixture->partial('test'));
    Assert::equals(['test' => $this->partial], $fixture->partials());
  }

  #[Test]
  public function inheriting_partials() {
    $parent= new Nodes();
    $parent->declare('test', $this->partial);
    $child= (new Nodes())->inheriting($parent);

    Assert::equals($this->partial, $child->partial('test'));
    Assert::equals(['test' => $this->partial], $child->partials());
  }
}