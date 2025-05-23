<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\VariableNode;
use com\handlebarsjs\{Constant, Expression, HandlebarsEngine, Lookup};
use test\{Assert, Test};

/**
 * Tests subexpressions, which may appear inside node options inside
 * braces. Subexpressions may be nested.
 *
 * ```mustache
 * {{test (equal (equal true true) true)}}
 * ```
 *
 * @see  https://github.com/wycats/handlebars.js/blob/master/spec/subexpressions.js
 */
class SubexpressionsTest {

  /**
   * Parse a string template and return the first node in the parsed syntax
   *
   * @param  string $template
   * @return com.github.mustache.Node
   */
  protected function parse($template) {
    return (new HandlebarsEngine())->compile($template)->root()->nodeAt(0);
  }

  /**
   * Evalzate a string template and return the result
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return com.github.mustache.Node
   */
  protected function evaluate($template, $variables) {
    return (new HandlebarsEngine())
      ->withHelper('pass', fn($items, $context, $options) => $options[0])
      ->withHelper('join', fn($items, $context, $options) => key($options).'='.current($options))
      ->withHelper('equal', fn($items, $context, $options) => $options[0] === $options[1])
      ->withHelper('test', fn($items, $context, $options) => 'tested: '.($options[0] ? 'true' : 'false'))
      ->render($template, $variables)
    ;
  }

  #[Test]
  public function parse_arg_less_helper() {
    Assert::equals(
      new VariableNode('test', true, [new Expression('the-west')]),
      $this->parse('{{test (the-west)}}')
    );
  }

  #[Test]
  public function execute_arg_less_helper() {
    Assert::equals(
      'tested: true',
      $this->evaluate('{{test (the-west)}}', ['the-west' => true])
    );
  }

  #[Test]
  public function parse_helper_w_args() {
    Assert::equals(
      new VariableNode('test', true, [new Expression('equal', [new Lookup('a'), new Lookup('b')])]),
      $this->parse('{{test (equal a b)}}')
    );
  }

  #[Test]
  public function execute_helper_w_args() {
    Assert::equals(
      'tested: false',
      $this->evaluate('{{test (equal a b)}}', ['a' => 1, 'b' => 2])
    );
  }

  #[Test]
  public function execute_helper_w_kv_args() {
    Assert::equals(
      'key=value',
      $this->evaluate('{{join key="value"}}', [])
    );
  }

  #[Test]
  public function execute_subexpression_w_kv_args_constant() {
    Assert::equals(
      'key=value',
      $this->evaluate('{{pass (join key="value")}}', [])
    );
  }

  #[Test]
  public function execute_subexpression_w_kv_args_variable() {
    Assert::equals(
      'key=value',
      $this->evaluate('{{pass (join key=var)}}', ['var' => 'value'])
    );
  }

  #[Test]
  public function execute_subexpression_w_kv_args_path() {
    Assert::equals(
      'key=value',
      $this->evaluate('{{pass (join key=var.val)}}', ['var' => ['val' => 'value']])
    );
  }

  #[Test]
  public function parse_supports_much_nesting() {
    Assert::equals(
      new VariableNode('test', true, [new Expression('equal', [
        new Expression('equal', [new Constant(true), new Constant(true)]),
        new Constant(true)
      ])]),
      $this->parse('{{test (equal (equal true true) true)}}')
    );
  }

  #[Test]
  public function execute_supports_much_nesting() {
    Assert::equals(
      'tested: true',
      $this->evaluate('{{test (equal (equal true true) true)}}', [])
    );
  }
}