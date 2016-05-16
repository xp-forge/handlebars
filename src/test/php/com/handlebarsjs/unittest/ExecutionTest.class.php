<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;
use com\github\mustache\InMemory;

class ExecutionTest extends \unittest\TestCase {

  /**
   * Evaluate a string template against given variables and return the output.
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string
   */
  protected function evaluate($template, $variables, $templates= ['test' => 'Partial']) {
    return (new HandlebarsEngine())->withTemplates(new InMemory($templates))->render($template, $variables);
  }

  #[@test]
  public function this_reference_resolves_to_current_scope() {
    $this->assertEquals(
      'Test',
      $this->evaluate('{{this.name}}', ['name' => 'Test'])
    );
  }

  #[@test]
  public function dot_references_resolving_in_scopes() {
    $this->assertEquals(
      'TestPerson',
      $this->evaluate('{{#person}}{{../name}}{{./name}}{{/person}}', [
        'name'   => 'Test',
        'person' => ['name' => 'Person']
      ])
    );
  }

  #[@test]
  public function partial() {
    $this->assertEquals('Partial', $this->evaluate('{{> test}}', []));
  }

  #[@test]
  public function dynamic_partial() {
    $this->assertEquals('Partial', $this->evaluate('{{> (template)}}', ['template' => 'test']));
  }

  #[@test]
  public function complex_dynamic_partial() {
    $this->assertEquals('Partial', $this->evaluate('{{> (lookup . "template")}}', ['template' => 'test']));
  }

  #[@test]
  public function partial_with_context() {
    $this->assertEquals('fetched from context', $this->evaluate(
      '{{> test context}}',
      ['context' => ['name' => 'from context'], 'name' => 'globally'],
      ['test' => 'fetched {{name}}']
    ));
  }

  #[@test]
  public function partial_with_parameter() {
    $this->assertEquals('name was overwritten', $this->evaluate(
      '{{> test name="overwritten"}}',
      ['field' => 'name', 'name' => 'not overwritten, but should have!', ],
      ['test' => '{{field}} was {{name}}']
    ));
  }
}