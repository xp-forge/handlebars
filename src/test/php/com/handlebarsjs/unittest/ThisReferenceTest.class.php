<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;

class ThisReferenceTest extends \unittest\TestCase {

  #[@test, @ignore('Not sure how to implement yet')]
  public function does_not_show_for_falsy_values($value) {
    $this->assertEquals(
      'Test',
      create(new HandlebarsEngine())->render('{{this.name}}', array('name' => 'Test'))
    );
  }
}