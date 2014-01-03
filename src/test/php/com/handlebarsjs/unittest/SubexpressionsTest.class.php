<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsParser;
use com\handlebarsjs\Lookup;
use com\handlebarsjs\Expression;
use com\github\mustache\VariableNode;

/**
 * @see  https://github.com/wycats/handlebars.js/blob/master/spec/subexpressions.js
 */
class SubexpressionsTest extends \unittest\TestCase {

  /**
   * Evaluate a string template against given variables and return the output.
   *
   * @param  string $template
   * @return com.github.mustache.Node The first node in the parsed syntax
   */
  protected function parse($template) {
    return create(new HandlebarsParser())->parse($template)->nodeAt(0);
  }

  #[@test]
  public function arg_less_helper() {
    $this->assertEquals(
      new VariableNode('test', true, array(new Expression('the-west'))),
      $this->parse('{{test (the-west)}}')
    );
  }

  #[@test]
  public function helper_w_args() {
    $this->assertEquals(
      new VariableNode('test', true, array(new Expression('equal', array(new Lookup('a'), new Lookup('b'))))),
      $this->parse('{{test (equal a b)}}')
    );
  }
}