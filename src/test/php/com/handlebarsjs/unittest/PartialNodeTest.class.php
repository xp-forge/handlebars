<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\PartialNode;
use com\handlebarsjs\Lookup;

class PartialNodeTest extends \unittest\TestCase {
  
  #[@test]
  public function template() {
    $template= new Lookup('test');
    $partial= new PartialNode($template);
    $this->assertEquals($template, $partial->template());
  }

  #[@test]
  public function string_representation() {
    $partial= new PartialNode(new Lookup('test'));
    $this->assertEquals('com.handlebarsjs.PartialNode{{> com.handlebarsjs.Lookup(test)}}, indent= ""', $partial->toString());
  }

  #[@test]
  public function string_cast() {
    $partial= new PartialNode(new Lookup('test'));
    $this->assertEquals('{{> test}}', (string)$partial);
  }
}