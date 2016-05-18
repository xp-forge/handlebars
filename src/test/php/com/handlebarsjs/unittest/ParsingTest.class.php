<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsParser;
use com\handlebarsjs\BlockNode;
use com\handlebarsjs\PartialNode;
use com\handlebarsjs\Lookup;
use com\handlebarsjs\Quoted;
use com\handlebarsjs\Constant;
use com\handlebarsjs\Expression;
use com\github\mustache\NodeList;
use com\github\mustache\VariableNode;
use com\github\mustache\TemplateFormatException;
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
      new NodeList([new PartialNode(new Constant('partial'))]),
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
      new NodeList([new PartialNode(new Constant('userMessage'), ['tagName' => new Quoted('h1')])]),
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

  #[@test, @expect(class= TemplateFormatException::class, withMessage= '/Illegal nesting/')]
  public function incorrect_ending_tag() {
    $this->parse('{{#each users}}...{{/each users}}');
  }

  #[@test, @values([
  #  ['-1', -1], ['0', 0], ['1', 1], ['6100', 6100],
  #  ['-1.0', -1.0], ['0.0', 0.0], ['1.5', 1.5], ['47.11', 47.11],
  #  ['true', true], ['false', false], ['null', null]
  #])]
  public function constants($literal, $value) {
    $this->assertEquals(
      [new Constant($value)],
      $this->parse('{{test '.$literal.'}}')->nodeAt(0)->options()
    );
  }

  #[@test, @values([
  #  ['""', ''], ['"Test"', 'Test'],
  #  ['"\""', '"'], ['"\"\""', '""'], ['"\"Quoted\""', '"Quoted"']
  #])]
  public function quoted($literal, $value) {
    $this->assertEquals(
      [new Quoted($value)],
      $this->parse('{{test '.$literal.'}}')->nodeAt(0)->options()
    );
  }
}