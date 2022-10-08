<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\{InMemory, TemplateNotFoundException};
use com\handlebarsjs\HandlebarsEngine;
use unittest\{Assert, Expect, Test};

class ExecutionTest {

  /**
   * Evaluate a string template against given variables and return the output.
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string
   */
  protected function evaluate($template, $variables, $templates= ['test' => 'Partial']) {
    return (new HandlebarsEngine())
      ->withTemplates(new InMemory($templates))
      ->withHelper('date', function($node, $context, $options) { return date('Y-m-d', $options[0] ?? null); })
      ->withHelper('time', ['short' => ['24' => function($node, $context, $options) { return date('H:i', $options[0] ?? null); }]])
      ->render($template, $variables)
    ;
  }

  #[Test]
  public function this_reference_resolves_to_current_scope() {
    Assert::equals('Test', $this->evaluate('{{this.name}}', ['name' => 'Test']));
  }

  #[Test]
  public function this_has_no_special_meaning_in_path() {
    Assert::equals('Test', $this->evaluate('{{./this}}', ['this' => 'Test']));
  }

  #[Test]
  public function root_reference() {
    Assert::equals('Test', $this->evaluate('{{@root.name.en}}', ['name' => ['en' => 'Test']]));
  }

  #[Test]
  public function root_reference_in_nested_context() {
    Assert::equals(
      'Test Test',
      $this->evaluate('{{#with name.en}}{{.}} {{@root.name.en}}{{/with}}', ['name' => ['en' => 'Test']])
    );
  }

  #[Test]
  public function helper_has_priority_over_property() {
    $context= ['date' => 'yesterday'];
    Assert::equals(
      date('Y-m-d').' yesterday yesterday',
      $this->evaluate('{{date}} {{./date}} {{this.date}}', $context)
    );
  }

  #[Test]
  public function nested_helper_has_priority_over_property() {
    $context= ['time' => ['short' => ['24' => 'now']]];
    Assert::equals(
      date('H:i').' now now',
      $this->evaluate('{{time.short.24}} {{./time.short.24}} {{this.time.short.24}}', $context)
    );
  }

  #[Test]
  public function partial_inside_each() {
    Assert::equals('Test #1', $this->evaluate(
      '{{#each it}}{{> test select=.}}{{/each}}',
      ['it' => [1]],
      ['test' => 'Test #{{select}}']
    ));
  }

  #[Test]
  public function root_reference_inside_nested() {
    Assert::equals(
      'A Test',
      $this->evaluate('{{#each iteration}}{{#with fixture}}{{article}} {{@root.name.en}}{{/with}}{{/each}}', [
        'name' => ['en' => 'Test'],
        'iteration' => [['fixture' => ['article' => 'A']]]
      ])
    );
  }

  #[Test]
  public function dot_references_resolving_in_scopes() {
    Assert::equals(
      'ATestPerson',
      $this->evaluate('{{#nested}}{{#person}}{{../../name}}{{../name}}{{./name}}{{/person}}{{/nested}}', [
        'name'   => 'A',
        'nested' => [
          'name'   => 'Test',
          'person' => ['name' => 'Person']
        ]
      ])
    );
  }

  #[Test]
  public function partial() {
    Assert::equals('Partial', $this->evaluate('{{> test}}', []));
  }

  #[Test]
  public function dynamic_partial() {
    Assert::equals('Partial', $this->evaluate('{{> (template)}}', ['template' => 'test']));
  }

  #[Test]
  public function complex_dynamic_partial() {
    Assert::equals('Partial', $this->evaluate('{{> (lookup . "template")}}', ['template' => 'test']));
  }

  #[Test]
  public function partial_with_context() {
    Assert::equals('fetched from context', $this->evaluate(
      '{{> test context}}',
      ['context' => ['name' => 'from context'], 'name' => 'globally'],
      ['test' => 'fetched {{name}}']
    ));
  }

  #[Test]
  public function partial_with_constant_parameter() {
    Assert::equals('name was overwritten', $this->evaluate(
      '{{> test name="overwritten"}}',
      ['field' => 'name', 'name' => 'not overwritten, but should have!'],
      ['test' => '{{field}} was {{name}}']
    ));
  }

  #[Test]
  public function partial_with_variable_parameter() {
    Assert::equals('name was overwritten', $this->evaluate(
      '{{> test name=val}}',
      ['field' => 'name', 'name' => 'not overwritten, but should have!', 'val' => 'overwritten'],
      ['test' => '{{field}} was {{name}}']
    ));
  }

  #[Test, Expect(['class' => TemplateNotFoundException::class, 'withMessage' => '/Cannot find template undefined/'])]
  public function undefined_partial() {
    $this->evaluate('{{> undefined}}', []);
  }

  #[Test, Expect(['class' => TemplateNotFoundException::class, 'withMessage' => '/Cannot find template undefined/'])]
  public function undefined_dynamic_partial() {
    $this->evaluate('{{> (template)}}', ['template' => 'undefined']);
  }
}