<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsParser;
use com\handlebarsjs\BlockNode;
use com\handlebarsjs\Lookup;
use com\handlebarsjs\Expression;
use com\github\mustache\NodeList;
use com\github\mustache\VariableNode;

class ParsingTest extends \unittest\TestCase {

  /**
   * Evaluate a string template against given variables and return the output.
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string
   */
  protected function parse($template) {
    return create(new HandlebarsParser())->parse($template);
  }

  #[@test]
  public function empty_string_parsed_to_empty_nodes() {
    $this->assertEquals(new NodeList(array()), $this->parse(''));
  }

  #[@test]
  public function with_block_helper() {
    $this->assertEquals(
      new NodeList(array(new BlockNode('with', array(new Lookup('person'))))),
      $this->parse('{{#with person}}{{/with}}')
    );
  }

  #[@test, @values([
  #  ['message', '{{log "message"}}'],
  #  ['message', "{{log 'message'}}"],
  #  ['message ""', '{{log "message \"\""}}'],
  #  ['message \'\'', "{{log 'message \'\''}}"],
  #  ['message "a"', '{{log "message \"a\""}}'],
  #  ['message \'a\'', "{{log 'message \'a\''}}"],
  #  ['message "a b"', '{{log "message \"a b\""}}'],
  #  ['message \'a b\'', "{{log 'message \'a b\''}}"]
  #])]
  public function log_helper_with_string_option($value, $notation) {
    $this->assertEquals(
      new NodeList(array(new VariableNode('log', true, array($value)))),
      $this->parse($notation)
    );
  }

  #[@test]
  public function arg_less_helper_subexpression() {
    $this->assertEquals(
      new NodeList(array(new VariableNode('test', true, array(new Expression('the-west'))))),
      $this->parse('{{test (the-west)}}')
    );
  }
}