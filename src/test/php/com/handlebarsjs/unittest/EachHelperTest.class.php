<?php namespace com\handlebarsjs\unittest;

use unittest\{Assert, Ignore, Test, Values};

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

  #[Test]
  public function with_null_list() {
    Assert::equals('', $this->evaluate('{{#each people}}* {{name}}{{/each}}', [
      'people' => null
    ]));
  }

  #[Test]
  public function with_empty_list() {
    Assert::equals('', $this->evaluate('{{#each people}}* {{name}}{{/each}}', [
      'people' => []
    ]));
  }

  #[Test]
  public function with_one_element_list() {
    Assert::equals('Test', $this->evaluate('{{#each people}}{{name}}{{/each}}', [
      'people' => [
        ['name' => 'Test']
      ]
    ]));
  }

  #[Test]
  public function with_elements() {
    Assert::equals('* A * B * C ', $this->evaluate(
      '{{#each people}}* {{name}} {{/each}}',
      $this->people()
    ));
  }

  #[Test]
  public function with_elements_and_index() {
    Assert::equals('0: A 1: B 2: C ', $this->evaluate(
      '{{#each people}}{{@index}}: {{name}} {{/each}}',
      $this->people()
    ));
  }

  #[Test]
  public function with_elements_and_first() {
    Assert::equals('true: A : B : C ', $this->evaluate(
      '{{#each people}}{{@first}}: {{name}} {{/each}}',
      $this->people()
    ));
  }

  #[Test]
  public function with_elements_and_last() {
    Assert::equals(': A : B true: C ', $this->evaluate(
      '{{#each people}}{{@last}}: {{name}} {{/each}}',
      $this->people()
    ));
  }

  #[Test]
  public function with_hash_properties() {
    Assert::equals('green $12.40 ', $this->evaluate(
      '{{#each item}}{{.}} {{/each}}',
      $this->item()
    ));
  }

  #[Test]
  public function with_hash_properties_and_index() {
    Assert::equals('color: green price: $12.40 ', $this->evaluate(
      '{{#each item}}{{@key}}: {{.}} {{/each}}',
      $this->item()
    ));
  }

  #[Test]
  public function with_hash_properties_and_first() {
    Assert::equals('true: green : $12.40 ', $this->evaluate(
      '{{#each item}}{{@first}}: {{.}} {{/each}}',
      $this->item()
    ));
  }

  #[Test]
  public function with_hash_properties_and_last() {
    Assert::equals(': green true: $12.40 ', $this->evaluate(
      '{{#each item}}{{@last}}: {{.}} {{/each}}',
      $this->item()
    ));
  }

  #[Test, Values(['else', '^'])]
  public function else_invoked_for_non_truthy($else) {
    Assert::equals('Default', $this->evaluate('{{#each var}}-{{.}}-{{'.$else.'}}Default{{/each}}', [
      'var' => false
    ]));
  }

  #[Test, Ignore('Not yet supported, not sure how to implement')]
  public function segment_literal_notation_for_invalid_identifiers() {
    Assert::equals('Comment', $this->evaluate('{{#each articles.[10].[#comments]}}{{text}}{{/each}}', [
      'articles' => [
        10 => [
          '#comments' => [
            ['text' => 'Comment']
          ]
        ]
      ]
    ]));
  }

  #[Test]
  public function nested_each_with_hashes() {
    Assert::equals('timm :crown: :snowman:', $this->evaluate(
      '{{#each player}}{{name}}{{#each badges}} :{{name}}:{{/each}}{{/each}}',
      ['player' => [
        '#1549' => ['name' => 'timm', 'badges' => [
          '#1' => ['name' => 'crown'],
          '#2' => ['name' => 'snowman']
        ]]
      ]]
    ));
  }

  #[Test]
  public function nested_each_with_lists() {
    Assert::equals('timm :crown: :snowman:', $this->evaluate(
      '{{#each player}}{{name}}{{#each badges}} :{{name}}:{{/each}}{{/each}}',
      ['player' => [
        ['name' => 'timm', 'badges' => [
          ['name' => 'crown'],
          ['name' => 'snowman']
        ]]
      ]]
    ));
  }

  #[Test]
  public function from_iterator() {
    $f= function() {
      yield 'A';
      yield 'B';
      yield 'C';
    };

    Assert::equals('0:A,1:B,2:C', $this->evaluate(
      '{{#each people}}{{#unless @first}},{{/unless}}{{@key}}:{{.}}{{/each}}',
      ['people' => $f()]
    ));
  }

  #[Test]
  public function from_iterator_with_keys() {
    $f= function() {
      yield 'a' => 'A';
      yield 'b' => 'B';
      yield 'c' => 'C';
    };

    Assert::equals('a:A b:B c:C', $this->evaluate(
      '{{#each people}}{{#unless @first}} {{/unless}}{{@key}}:{{.}}{{/each}}',
      ['people' => $f()]
    ));
  }

  #[Test]
  public function hash_with_as_index_element() {
    Assert::equals('key: value', $this->evaluate(
      '{{#each items as |item index|}}{{index}}: {{item.name}}{{/each}}',
      ['items' => ['key' => 'value']]
    ));
  }

  #[Test]
  public function hash_with_as_element() {
    Assert::equals('key: value', $this->evaluate(
      '{{#each items as |item|}}{{@key}}: {{item.name}}{{/each}}',
      ['items' => ['key' => 'value']]
    ));
  }

  #[Test]
  public function generator_with_as() {
    Assert::equals('key: value', $this->evaluate(
      '{{#each items as |item index|}}{{index}}: {{item.name}}{{/each}}',
      ['items' => (function() { yield 'key' => 'value'; })()]
    ));
  }

  #[Test]
  public function array_with_as_index_element() {
    Assert::equals('0: value', $this->evaluate(
      '{{#each items as |item index|}}{{index}}: {{item.name}}{{/each}}',
      ['items' => ['value']]
    ));
  }

  #[Test]
  public function array_with_as_element() {
    Assert::equals('0: value', $this->evaluate(
      '{{#each items as |item|}}{{@index}}: {{item.name}}{{/each}}',
      ['items' => ['value']]
    ));
  }
}