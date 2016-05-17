<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;
use com\handlebarsjs\Lookup;
use com\handlebarsjs\Expression;
use com\handlebarsjs\Boolean;
use com\github\mustache\VariableNode;

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
class SubexpressionsTest extends \unittest\TestCase {

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
      ->withHelper('join', function($items, $context, $options) { return key($options).'='.current($options); })
      ->withHelper('equal', function($items, $context, $options) { return $options[0] === $options[1]; })
      ->withHelper('test', function($items, $context, $options) { return 'tested: '.($options[0] ? 'true' : 'false'); })
      ->render($template, $variables)
    ;
  }

  #[@test]
  public function parse_arg_less_helper() {
    $this->assertEquals(
      new VariableNode('test', true, [new Expression('the-west')]),
      $this->parse('{{test (the-west)}}')
    );
  }

  #[@test]
  public function execute_arg_less_helper() {
    $this->assertEquals(
      'tested: true',
      $this->evaluate('{{test (the-west)}}', ['the-west' => true])
    );
  }

  #[@test]
  public function parse_helper_w_args() {
    $this->assertEquals(
      new VariableNode('test', true, [new Expression('equal', [new Lookup('a'), new Lookup('b')])]),
      $this->parse('{{test (equal a b)}}')
    );
  }

  #[@test]
  public function execute_helper_w_args() {
    $this->assertEquals(
      'tested: false',
      $this->evaluate('{{test (equal a b)}}', ['a' => 1, 'b' => 2])
    );
  }

  #[@test]
  public function execute_helper_w_kv_args() {
    $this->assertEquals(
      'key=value',
      $this->evaluate('{{join key="value"}}', [])
    );
  }

  #[@test]
  public function parse_supports_much_nesting() {
    $this->assertEquals(
      new VariableNode('test', true, [new Expression('equal', [
        new Expression('equal', [new Boolean(true), new Boolean(true)]),
        new Boolean(true)
      ])]),
      $this->parse('{{test (equal (equal true true) true)}}')
    );
  }

  #[@test]
  public function execute_supports_much_nesting() {
    $this->assertEquals(
      'tested: true',
      $this->evaluate('{{test (equal (equal true true) true)}}', [])
    );
  }
}