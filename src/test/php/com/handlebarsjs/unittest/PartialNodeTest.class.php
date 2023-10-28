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
    Assert::equals(
      'com.handlebarsjs.PartialNode(> com.handlebarsjs.Lookup(test)}}, indent= "")',
      (new PartialNode(new Lookup('test')))->toString()
    );
  }

  #[Test]
  public function string_cast() {
    Assert::equals('{{> test}}', (string)new PartialNode(new Lookup('test')));
  }
}