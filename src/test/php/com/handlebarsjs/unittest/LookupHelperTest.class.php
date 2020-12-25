<?php namespace com\handlebarsjs\unittest;

use unittest\Assert;
use unittest\{Test, Values};

class LookupHelperTest extends HelperTest {

  #[Test]
  public function should_lookup_arbitrary_content() {
    Assert::equals('ZeroOne', $this->evaluate('{{#each goodbyes}}{{lookup ../data .}}{{/each}}', [
      'goodbyes' => [0, 1],
      'data'     => ['Zero', 'One']
    ]));
  }

  #[Test]
  public function should_not_fail_on_undefined_value() {
    Assert::equals('', $this->evaluate('{{#each goodbyes}}{{lookup ../bar .}}{{/each}}', [
      'goodbyes' => [0, 1],
      'data'     => ['Zero', 'One']
    ]));
  }

  #[Test, Values(['{{lookup map key}}', '{{lookup map key.name}}', '{{lookup map.sub key}}', '{{lookup map.sub key.name}}'])]
  public function lookup_non_existant($expr) {
    Assert::equals('', $this->evaluate($expr, []));
  }
}