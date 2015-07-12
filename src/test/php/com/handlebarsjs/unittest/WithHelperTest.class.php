<?php namespace com\handlebarsjs\unittest;

class WithHelperTest extends HelperTest {

  #[@test, @values([null, false, '', 0, [[]]])]
  public function does_not_show_for_falsy_values($value) {
    $this->assertEquals('', $this->evaluate('{{#with person}}-{{var}}-{{/with}}', [
      'var' => $value
    ]));
  }

  #[@test]
  public function switches_context() {
    $this->assertEquals('Alan Johnson', $this->evaluate(
      '{{#with person}}{{first}} {{last}}{{/with}}',
      ['person' => ['first' => 'Alan', 'last' => 'Johnson']]
    ));
  }

  #[@test, @values(['else', '^'])]
  public function else_invoked_for_non_truthy($else) {
    $this->assertEquals('Default', $this->evaluate('{{#with var}}-{{.}}-{{'.$else.'}}Default{{/with}}', [
      'var' => false
    ]));
  }
}