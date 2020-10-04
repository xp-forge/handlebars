<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\{Lookup, PartialNode};
use unittest\Test;

class PartialNodeTest extends \unittest\TestCase {
  
  #[Test]
  public function template() {
    $template= new Lookup('test');
    $partial= new PartialNode($template);
    $this->assertEquals($template, $partial->template());
  }

  #[Test]
  public function string_representation() {
    $partial= new PartialNode(new Lookup('test'));
    $this->assertEquals('com.handlebarsjs.PartialNode{{> com.handlebarsjs.Lookup(test)}}, indent= ""', $partial->toString());
  }

  #[Test]
  public function string_cast() {
    $partial= new PartialNode(new Lookup('test'));
    $this->assertEquals('{{> test}}', (string)$partial);
  }
}