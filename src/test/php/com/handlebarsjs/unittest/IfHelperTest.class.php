<?php namespace com\handlebarsjs\unittest;

use unittest\{Test, Values};

class IfHelperTest extends HelperTest {

  #[Test, Values([null, false, '', 0, [[]]])]
  public function does_not_show_for_falsy_values($value) {
    $this->assertEquals('', $this->evaluate('{{#if var}}-{{var}}-{{/if}}', [
      'var' => $value
    ]));
  }

  #[Test, Values([['-1-', true], ['-true-', 'true'], ['-1-', 1], ['-1-', 1.0]])]
  public function shows_for_truthy_values($expected, $value) {
    $this->assertEquals($expected, $this->evaluate('{{#if var}}-{{var}}-{{/if}}', [
      'var' => $value
    ]));
  }

  #[Test]
  public function shows_for_non_empty_array() {
    $this->assertEquals('-123-', $this->evaluate('{{#if var}}-{{#var}}{{.}}{{/var}}-{{/if}}', [
      'var' => [1, 2, 3]
    ]));
  }

  #[Test, Values(['else', '^'])]
  public function else_invoked_for_non_truthy($else) {
    $this->assertEquals('Default', $this->evaluate('{{#if var}}-{{var}}-{{'.$else.'}}Default{{/if}}', [
      'var' => false
    ]));
  }

  #[Test]
  public function from_iterator() {
    $f= function() {
      yield 'A';
      yield 'B';
      yield 'C';
    };

    $this->assertEquals('ABC', $this->evaluate(
      '{{#if people}}{{#each people}}{{.}}{{/each}}{{else}}(empty){{/if}}',
      ['people' => $f()]
    ));
  }

  #[Test]
  public function from_empty_iterator() {
    $f= function() {
      return;
      yield 'A';
    };

    $this->assertEquals('(empty)', $this->evaluate(
      '{{#if people}}{{#each people}}{{.}}{{/each}}{{else}}(empty){{/if}}',
      ['people' => $f()]
    ));
  }
}