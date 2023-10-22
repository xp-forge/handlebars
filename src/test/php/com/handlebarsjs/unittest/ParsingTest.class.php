<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\{TemplateFormatException, TextNode, VariableNode, NodeList};
use com\handlebarsjs\{
  BlockNode,
  Constant,
  Expression,
  HandlebarsParser,
  Lookup,
  Nodes,
  PartialBlockHelper,
  PartialNode,
  Quoted
};
use lang\IllegalArgumentException;
use test\{Assert, Expect, Test, Values};
use text\StringTokenizer;

class ParsingTest {

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

  #[Test]
  public function empty_string_parsed_to_empty_nodes() {
    Assert::equals(new Nodes([]), $this->parse(''));
  }

  #[Test, Values(['foo', 'foo?', 'foo_', 'foo-', 'foo:', 'foo-bar'])]
  public function parses_simple_mustaches($value) {
    Assert::equals(new Nodes([new VariableNode($value)]), $this->parse('{{'.$value.'}}'));
  }

  #[Test]
  public function with_block_helper() {
    Assert::equals(
      new Nodes([new BlockNode('with', [new Lookup('person')])]),
      $this->parse('{{#with person}}{{/with}}')
    );
  }

  #[Test, Values([['message', '{{log "message"}}'], ['message', "{{log 'message'}}"], ['message ""', '{{log "message \"\""}}'], ['message \'\'', "{{log 'message \'\''}}"], ['message "a"', '{{log "message \"a\""}}'], ['message \'a\'', "{{log 'message \'a\''}}"], ['message "a b"', '{{log "message \"a b\""}}'], ['message \'a b\'', "{{log 'message \'a b\''}}"]])]
  public function log_helper_with_string_option($value, $notation) {
    Assert::equals(
      new Nodes([new VariableNode('log', true, [new Quoted($value)])]),
      $this->parse($notation)
    );
  }

  #[Test, Values([['{{> partial}}'], ['{{>partial}}']])]
  public function partial($notation) {
    Assert::equals(
      new Nodes([new PartialNode(new Constant('partial'))]),
      $this->parse($notation)
    );
  }

  #[Test, Values([['{{> (partial)}}'], ['{{>(partial)}}']])]
  public function dynamic_partial($notation) {
    Assert::equals(
      new Nodes([new PartialNode(new Expression('partial'))]),
      $this->parse($notation)
    );
  }

  #[Test, Values([['{{& html}}'], ['{{&html}}']])]
  public function unescaped($notation) {
    Assert::equals(
      new Nodes([new VariableNode('html', false)]),
      $this->parse($notation)
    );
  }

  #[Test]
  public function dynamic_partial_with_lookup_helper() {
    Assert::equals(
      new Nodes([new PartialNode(new Expression('lookup', [new Lookup(null), new Quoted('partial')]))]),
      $this->parse('{{> (lookup . "partial")}}')
    );
  }

  #[Test]
  public function partial_with_context() {
    Assert::equals(
      new Nodes([new PartialNode(new Constant('userMessage'), ['tagName' => new Quoted('h1')])]),
      $this->parse('{{> userMessage tagName="h1"}}')
    );
  }

  #[Test]
  public function dynamic_partial_with_context() {
    Assert::equals(
      new Nodes([new PartialNode(new Expression('partial'), ['tagName' => new Quoted('h1')])]),
      $this->parse('{{> (partial) tagName="h1"}}')
    );
  }

  #[Test]
  public function partial_block() {
    Assert::equals(
      new Nodes([new PartialBlockHelper(['layout'], new Nodes([
        new TextNode('Content')
      ]))]),
      $this->parse('{{#> layout}}Content{{/layout}}')
    );
  }

  #[Test]
  public function inline_partials() {
    Assert::equals(
      ['one' => new NodeList([new TextNode('One')]), 'two' => new NodeList([new TextNode('Two')])],
      $this->parse('{{#*inline "one"}}One{{/inline}}{{#*inline "two"}}Two{{/inline}}')->partials()
    );
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function inline_name_cannot_be_missing() {
    $this->parse('{{#*inline}}...{{/inline}}');
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function inline_name_cannot_be_reference() {
    $this->parse('{{#*inline one}}...{{/inline}}');
  }

  #[Test, Expect(class: TemplateFormatException::class, message: '/Illegal nesting/')]
  public function incorrect_ending_tag() {
    $this->parse('{{#each users}}...{{/each users}}');
  }

  #[Test, Values([['-1', -1], ['0', 0], ['1', 1], ['6100', 6100], ['-1.0', -1.0], ['0.0', 0.0], ['1.5', 1.5], ['47.11', 47.11], ['true', true], ['false', false], ['null', null]])]
  public function constants($literal, $value) {
    Assert::equals(
      [new Constant($value)],
      $this->parse('{{test '.$literal.'}}')->nodeAt(0)->options()
    );
  }

  #[Test, Values([['""', ''], ['"Test"', 'Test'], ['"\""', '"'], ['"\"\""', '""'], ['"\"Quoted\""', '"Quoted"']])]
  public function quoted($literal, $value) {
    Assert::equals(
      [new Quoted($value)],
      $this->parse('{{test '.$literal.'}}')->nodeAt(0)->options()
    );
  }

  #[Test, Expect(class: TemplateFormatException::class, message: 'Illegal nesting, expected {{/if}}, have {{/unless}}')]
  public function illegal_nesting() {
    $this->parse('{{#if test}}X{{/unless}}');
  }

  #[Test, Expect(class: TemplateFormatException::class, message: 'Illegal nesting, no start tag, but have {{/if}}')]
  public function no_start_tag() {
    $this->parse('{{if test}}X{{/if}}');
  }

  #[Test, Expect(class: TemplateFormatException::class, message: 'Unclosed section {{#section}}')]
  public function unclosed_section() {
    $this->parse('{{#section}}X');
  }

  #[Test, Expect(class: TemplateFormatException::class, message: 'Unclosed section {{#if}}')]
  public function unclosed_if() {
    $this->parse('{{#if test}}X');
  }

  #[Test]
  public function multiline_tag() {
    $p= $this->parse(trim('
      {{multiline
        value
      }}
    '));
    Assert::equals(new VariableNode('multiline', true, [new Lookup('value')]), $p->nodeAt(0));
  }
}