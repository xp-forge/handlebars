<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsParser;
use com\handlebarsjs\BlockNode;
use com\handlebarsjs\PartialNode;
use com\handlebarsjs\Lookup;
use com\handlebarsjs\Quoted;
use com\handlebarsjs\Expression;
use com\github\mustache\NodeList;
use com\github\mustache\VariableNode;
use text\StringTokenizer;

class ParsingTest extends \unittest\TestCase {

  /**
   * Evaluate a string template against given variables and return the output.
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string
   */
  protected function parse($template) {
    return (new HandlebarsParser())->parse(new StringTokenizer($template));
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
      new NodeList([new VariableNode('log', true, [new Quoted($value)])]),
      $this->parse($notation)
    );
  }

  #[@test]
  public function partial() {
    $this->assertEquals(
      new NodeList([new PartialNode(new Quoted('partial'))]),
      $this->parse('{{> partial}}')
    );
  }

  #[@test]
  public function dynamic_partial() {
    $this->assertEquals(
      new NodeList([new PartialNode(new Expression('partial'))]),
      $this->parse('{{> (partial)}}')
    );
  }

  #[@test]
  public function dynamic_partial_with_lookup_helper() {
    $this->assertEquals(
      new NodeList([new PartialNode(new Expression('lookup', [new Lookup(null), new Quoted('partial')]))]),
      $this->parse('{{> (lookup . "partial")}}')
    );
  }

  #[@test]
  public function partial_with_context() {
    $this->assertEquals(
      new NodeList([new PartialNode(new Quoted('userMessage'), ['tagName' => new Quoted('h1')])]),
      $this->parse('{{> userMessage tagName="h1"}}')
    );
  }

  #[@test]
  public function dynamic_partial_with_context() {
    $this->assertEquals(
      new NodeList([new PartialNode(new Expression('partial'), ['tagName' => new Quoted('h1')])]),
      $this->parse('{{> (partial) tagName="h1"}}')
    );
  }
}