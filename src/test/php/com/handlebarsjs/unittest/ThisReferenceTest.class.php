<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;

class ThisReferenceTest extends \unittest\TestCase {

  #[@test]
  public function does_not_show_for_falsy_values() {
    $this->assertEquals(
      'Test',
      create(new HandlebarsEngine())->render('{{this.name}}', array('name' => 'Test'))
    );
  }
}