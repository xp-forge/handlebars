<?php namespace com\handlebarsjs\unittest;

class EachHelperTest extends HelperTest {

  #[@test]
  public function with_null_list() {
    $this->assertEquals('', $this->evaluate('{{#each people}}* {{name}}{{/each}}', array(
      'people' => null
    )));
  }

  #[@test]
  public function with_empty_list() {
    $this->assertEquals('', $this->evaluate('{{#each people}}* {{name}}{{/each}}', array(
      'people' => array()
    )));
  }

  #[@test]
  public function with_one_element_list() {
    $this->assertEquals('Test', $this->evaluate('{{#each people}}{{name}}{{/each}}', array(
      'people' => array(
        array('name' => 'Test')
      )
    )));
  }

  #[@test]
  public function with_elements() {
    $this->assertEquals('* A * B * C ', $this->evaluate('{{#each people}}* {{name}} {{/each}}', array(
      'people' => array(
        array('name' => 'A'),
        array('name' => 'B'),
        array('name' => 'C')
      )
    )));
  }

  #[@test]
  public function with_index() {
    $this->assertEquals('0: A 1: B 2: C ', $this->evaluate('{{#each people}}{{@index}}: {{name}} {{/each}}', array(
      'people' => array(
        array('name' => 'A'),
        array('name' => 'B'),
        array('name' => 'C')
      )
    )));
  }

  #[@test]
  public function with_first() {
    $this->assertEquals('1: A : B : C ', $this->evaluate('{{#each people}}{{@first}}: {{name}} {{/each}}', array(
      'people' => array(
        array('name' => 'A'),
        array('name' => 'B'),
        array('name' => 'C')
      )
    )));
  }

  #[@test]
  public function with_last() {
    $this->assertEquals(': A : B 1: C ', $this->evaluate('{{#each people}}{{@last}}: {{name}} {{/each}}', array(
      'people' => array(
        array('name' => 'A'),
        array('name' => 'B'),
        array('name' => 'C')
      )
    )));
  }
}