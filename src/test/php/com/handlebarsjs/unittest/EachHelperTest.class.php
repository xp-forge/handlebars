<?php namespace com\handlebarsjs\unittest;

class EachHelperTest extends HelperTest {

  /**
   * Returns fixture data for the `with_elements` tests
   *
   * @return [:var]
   */
  protected function people() {
    return [
      'people' => [
        ['name' => 'A'],
        ['name' => 'B'],
        ['name' => 'C']
      ]
    ];
  }

  /**
   * Returns fixture data for the `with_hash` tests
   *
   * @return [:var]
   */
  protected function item() {
    return ['item' => ['color' => 'green', 'price' => '$12.40']];
  }

  #[@test]
  public function with_null_list() {
    $this->assertEquals('', $this->evaluate('{{#each people}}* {{name}}{{/each}}', [
      'people' => null
    ]));
  }

  #[@test]
  public function with_empty_list() {
    $this->assertEquals('', $this->evaluate('{{#each people}}* {{name}}{{/each}}', [
      'people' => []
    ]));
  }

  #[@test]
  public function with_one_element_list() {
    $this->assertEquals('Test', $this->evaluate('{{#each people}}{{name}}{{/each}}', [
      'people' => [
        ['name' => 'Test']
      ]
    ]));
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

  #[@test, @values(['else', '^'])]
  public function else_invoked_for_non_truthy($else) {
    $this->assertEquals('Default', $this->evaluate('{{#each var}}-{{.}}-{{'.$else.'}}Default{{/each}}', [
      'var' => false
    ]));
  }

  #[@test, @ignore('Not yet supported, not sure how to implement')]
  public function segment_literal_notation_for_invalid_identifiers() {
    $this->assertEquals('Comment', $this->evaluate('{{#each articles.[10].[#comments]}}{{text}}{{/each}}', [
      'articles' => [
        10 => [
          '#comments' => [
            ['text' => 'Comment']
          ]
        ]
      ]
    ]));
  }

  #[@test]
  public function nested_each_with_hashes() {
    $this->assertEquals('timm :crown: :snowman:', $this->evaluate(
      '{{#each player}}{{name}}{{#each badges}} :{{name}}:{{/each}}{{/each}}',
      ['player' => [
        '#1549' => ['name' => 'timm', 'badges' => [
          '#1' => ['name' => 'crown'],
          '#2' => ['name' => 'snowman']
        ]]
      ]]
    ));
  }

  #[@test]
  public function nested_each_with_lists() {
    $this->assertEquals('timm :crown: :snowman:', $this->evaluate(
      '{{#each player}}{{name}}{{#each badges}} :{{name}}:{{/each}}{{/each}}',
      ['player' => [
        ['name' => 'timm', 'badges' => [
          ['name' => 'crown'],
          ['name' => 'snowman']
        ]]
      ]]
    ));
  }
}