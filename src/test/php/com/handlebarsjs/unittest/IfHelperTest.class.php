<?php namespace com\handlebarsjs\unittest;

class IfHelperTest extends HelperTest {

  #[@test, @values([null, false, '', 0, [[]]])]
  public function does_not_show_for_falsy_values($value) {
    $this->assertEquals('', $this->evaluate('{{#if var}}-{{var}}-{{/if}}', array(
      'var' => $value
    )));
  }

  #[@test, @values([
  #  ['-1-', true],
  #  ['-true-', 'true'],
  #  ['-1-', 1],
  #  ['-1-', 1.0]
  #])]
  public function shows_for_truthy_values($expected, $value) {
    $this->assertEquals($expected, $this->evaluate('{{#if var}}-{{var}}-{{/if}}', array(
      'var' => $value
    )));
  }

  #[@test]
  public function shows_for_non_empty_array() {
    $this->assertEquals('-123-', $this->evaluate('{{#if var}}-{{#var}}{{.}}{{/var}}-{{/if}}', array(
      'var' => array(1, 2, 3)
    )));
  }

  #[@test]
  public function else_invoked_for_non_truthy() {
    $this->assertEquals('Default', $this->evaluate('{{#if var}}-{{var}}-{{else}}Default{{/if}}', array(
      'var' => false
    )));
  }
}