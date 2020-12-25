<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\{TemplateFormatException, TextNode, VariableNode};
use com\handlebarsjs\{
  BlockNode,
  Constant,
  Decoration,
  Expression,
  HandlebarsParser,
  Lookup,
  Nodes,
  PartialBlockHelper,
  PartialNode,
  Quoted
};
use text\StringTokenizer;
use unittest\{Assert, Expect, Test, Values};

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

  #[Test]
  public function partial() {
    Assert::equals(
      new Nodes([new PartialNode(new Constant('partial'))]),
      $this->parse('{{> partial}}')
    );
  }

  #[Test]
  public function dynamic_partial() {
    Assert::equals(
      new Nodes([new PartialNode(new Expression('partial'))]),
      $this->parse('{{> (partial)}}')
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
  public function inline_partial() {
    $nodes= new Nodes();
    $nodes->decorate(new Decoration('inline', [new Quoted('myPartial')], new Nodes([new TextNode('Content')])));

    Assert::equals($nodes, $this->parse('{{#*inline "myPartial"}}Content{{/inline}}'));
  }

  #[Test, Expect(['class' => TemplateFormatException::class, 'withMessage' => '/Illegal nesting/'])]
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

  #[Test, Expect(['class' => TemplateFormatException::class, 'withMessage' => '/Illegal nesting, no start tag/'])]
  public function no_start_tag() {
    $this->parse('{{if test}}X{{/if}}');
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