<?php namespace com\handlebarsjs\unittest;

use unittest\{Assert, Test, Values};

class UnlessHelperTest extends HelperTest {

  #[Test, Values([null, false, '', 0, [[]]])]
  public function shows_for_falsy_values($value) {
    Assert::equals('-Default-', $this->evaluate('{{#unless var}}-Default-{{/unless}}', [
      'var' => $value
    ]));
  }

  #[Test, Values([true, 'true', 1, 1.0, [['non-empty-array']] ])]
  public function does_not_show_for_truthy_values($value) {
    Assert::equals('', $this->evaluate('{{#unless var}}-Default-{{/unless}}', [
      'var' => $value
    ]));
  }

  #[Test, Values(['else', '^'])]
  public function else_invoked_for_truthy($else) {
    Assert::equals('Default', $this->evaluate('{{#unless var}}-{{var}}-{{'.$else.'}}Default{{/unless}}', [
      'var' => true
    ]));
  }

  #[Test]
  public function from_iterator() {
    $f= function() {
      yield 'A';
      yield 'B';
      yield 'C';
    };

    Assert::equals('ABC', $this->evaluate(
      '{{#unless people}}(empty){{else}}{{#each people}}{{.}}{{/each}}{{/unless}}',
      ['people' => $f()]
    ));
  }

  #[Test]
  public function from_empty_iterator() {
    $f= function() {
      return;
      yield 'A';
    };

    Assert::equals('(empty)', $this->evaluate(
      '{{#unless people}}(empty){{else}}{{#each people}}{{.}}{{/each}}{{/unless}}',
      ['people' => $f()]
    ));
  }
}