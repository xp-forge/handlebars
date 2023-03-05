<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\{Lookup, PartialNode};
use test\{Assert, Test};

class PartialNodeTest {
  
  #[Test]
  public function template() {
    $template= new Lookup('test');
    $partial= new PartialNode($template);
    Assert::equals($template, $partial->template());
  }

  #[Test]
  public function string_representation() {
    $partial= new PartialNode(new Lookup('test'));
    Assert::equals('com.handlebarsjs.PartialNode{{> com.handlebarsjs.Lookup(test)}}, indent= ""', $partial->toString());
  }

  #[Test]
  public function string_cast() {
    $partial= new PartialNode(new Lookup('test'));
    Assert::equals('{{> test}}', (string)$partial);
  }
}