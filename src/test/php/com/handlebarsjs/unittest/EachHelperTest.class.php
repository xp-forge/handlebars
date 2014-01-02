<?php namespace com\handlebarsjs\unittest;

class EachHelperTest extends HelperTest {

  /**
   * Returns fixture data for the `with_elements` tests
   *
   * @return [:var]
   */
  protected function people() {
    return array(
      'people' => array(
        array('name' => 'A'),
        array('name' => 'B'),
        array('name' => 'C')
      )
    );
  }

  /**
   * Returns fixture data for the `with_hash` tests
   *
   * @return [:var]
   */
  protected function item() {
    return array('item' => array('color' => 'green', 'price' => '$12.40'));
  }

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
    $this->assertEquals('* A * B * C ', $this->evaluate(
      '{{#each people}}* {{name}} {{/each}}',
      $this->people()
    ));
  }

  #[@test]
  public function with_elements_and_index() {
    $this->assertEquals('0: A 1: B 2: C ', $this->evaluate(
      '{{#each people}}{{@index}}: {{name}} {{/each}}',
      $this->people()
    ));
  }

  #[@test]
  public function with_elements_and_first() {
    $this->assertEquals('true: A : B : C ', $this->evaluate(
      '{{#each people}}{{@first}}: {{name}} {{/each}}',
      $this->people()
    ));
  }

  #[@test]
  public function with_elements_and_last() {
    $this->assertEquals(': A : B true: C ', $this->evaluate(
      '{{#each people}}{{@last}}: {{name}} {{/each}}',
      $this->people()
    ));
  }

  #[@test]
  public function with_hash_properties() {
    $this->assertEquals('green $12.40 ', $this->evaluate(
      '{{#each item}}{{.}} {{/each}}',
      $this->item()
    ));
  }

  #[@test]
  public function with_hash_properties_and_index() {
    $this->assertEquals('color: green price: $12.40 ', $this->evaluate(
      '{{#each item}}{{@key}}: {{.}} {{/each}}',
      $this->item()
    ));
  }

  #[@test]
  public function with_hash_properties_and_first() {
    $this->assertEquals('true: green : $12.40 ', $this->evaluate(
      '{{#each item}}{{@first}}: {{.}} {{/each}}',
      $this->item()
    ));
  }

  #[@test]
  public function else_invoked_for_non_truthy() {
    $this->assertEquals('Default', $this->evaluate('{{#each var}}-{{.}}-{{else}}Default{{/each}}', array(
      'var' => false
    )));
  }
}