<?php namespace com\handlebarsjs\unittest;

use unittest\{Test, Values};

class LookupHelperTest extends HelperTest {

  #[Test]
  public function should_lookup_arbitrary_content() {
    $this->assertEquals('ZeroOne', $this->evaluate('{{#each goodbyes}}{{lookup ../data .}}{{/each}}', [
      'goodbyes' => [0, 1],
      'data'     => ['Zero', 'One']
    ]));
  }

  #[Test]
  public function should_not_fail_on_undefined_value() {
    $this->assertEquals('', $this->evaluate('{{#each goodbyes}}{{lookup ../bar .}}{{/each}}', [
      'goodbyes' => [0, 1],
      'data'     => ['Zero', 'One']
    ]));
  }

  #[Test, Values(['{{lookup map key}}', '{{lookup map key.name}}', '{{lookup map.sub key}}', '{{lookup map.sub key.name}}'])]
  public function lookup_non_existant($expr) {
    $this->assertEquals('', $this->evaluate($expr, []));
  }
}