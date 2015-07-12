<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;

class ExecutionTest extends \unittest\TestCase {

  /**
   * Evaluate a string template against given variables and return the output.
   *
   * @param  string $template
   * @param  [:var] $variables
   * @return string
   */
  protected function evaluate($template, $variables) {
    return (new HandlebarsEngine())->render($template, $variables);
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
}