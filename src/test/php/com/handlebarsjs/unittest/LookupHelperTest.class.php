<?php namespace com\handlebarsjs\unittest;

class LookupHelperTest extends HelperTest {

  #[@test]
  public function should_lookup_arbitrary_content() {
    $this->assertEquals('ZeroOne', $this->evaluate('{{#each goodbyes}}{{lookup ../data .}}{{/each}}', [
      'goodbyes' => [0, 1],
      'data'     => ['Zero', 'One']
    ]));
  }

  #[@test]
  public function should_not_fail_on_undefined_value() {
    $this->assertEquals('', $this->evaluate('{{#each goodbyes}}{{lookup ../bar .}}{{/each}}', [
      'goodbyes' => [0, 1],
      'data'     => ['Zero', 'One']
    ]));
  }

  #[@test, @values([
  #  '{{lookup map key}}',
  #  '{{lookup map key.name}}',
  #  '{{lookup map.sub key}}',
  #  '{{lookup map.sub key.name}}'
  #])]
  public function lookup_non_existant($expr) {
    $this->assertEquals('', $this->evaluate($expr, []));
  }
}