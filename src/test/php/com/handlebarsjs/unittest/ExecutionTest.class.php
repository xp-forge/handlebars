<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\{InMemory, TemplateNotFoundException};
use com\handlebarsjs\HandlebarsEngine;

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
  public function partial_with_constant_parameter() {
    $this->assertEquals('name was overwritten', $this->evaluate(
      '{{> test name="overwritten"}}',
      ['field' => 'name', 'name' => 'not overwritten, but should have!'],
      ['test' => '{{field}} was {{name}}']
    ));
  }

  #[@test]
  public function partial_with_variable_parameter() {
    $this->assertEquals('name was overwritten', $this->evaluate(
      '{{> test name=val}}',
      ['field' => 'name', 'name' => 'not overwritten, but should have!', 'val' => 'overwritten'],
      ['test' => '{{field}} was {{name}}']
    ));
  }

  #[@test, @expect(['class' => TemplateNotFoundException::class, 'withMessage' => '/Cannot find template undefined/'])]
  public function undefined_partial() {
    $this->evaluate('{{> undefined}}', []);
  }

  #[@test, @expect(['class' => TemplateNotFoundException::class, 'withMessage' => '/Cannot find template undefined/'])]
  public function undefined_dynamic_partial() {
    $this->evaluate('{{> (template)}}', ['template' => 'undefined']);
  }
}