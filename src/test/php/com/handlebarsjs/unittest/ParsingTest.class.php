<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsParser;
use com\handlebarsjs\BlockNode;
use com\handlebarsjs\Lookup;
use com\handlebarsjs\String;
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
    return create(new HandlebarsParser())->parse(new \text\StringTokenizer($template));
  }

  #[@test]
  public function empty_string_parsed_to_empty_nodes() {
    $this->assertEquals(new NodeList([]), $this->parse(''));
  }

  #[@test, @values(['foo', 'foo?', 'foo_', 'foo-', 'foo:', 'foo-bar'])]
  public function parses_simple_mustaches($value) {
    $this->assertEquals(new NodeList([new VariableNode($value)]), $this->parse('{{'.$value.'}}'));
  }

  #[@test]
  public function with_block_helper() {
    $this->assertEquals(
      new NodeList([new BlockNode('with', [new Lookup('person')])]),
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
      new NodeList([new VariableNode('log', true, [new String($value)])]),
      $this->parse($notation)
    );
  }
}