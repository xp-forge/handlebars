<?php namespace com\handlebarsjs\unittest;

use unittest\{Assert, Test, Values};

class WithHelperTest extends HelperTest {

  #[Test, Values([null, false, '', 0, [[]]])]
  public function does_not_show_for_falsy_values($value) {
    Assert::equals('', $this->evaluate('{{#with person}}-{{var}}-{{/with}}', [
      'var' => $value
    ]));
  }

  #[Test]
  public function switches_context() {
    Assert::equals('Alan Johnson', $this->evaluate(
      '{{#with person}}{{first}} {{last}}{{/with}}',
      ['person' => ['first' => 'Alan', 'last' => 'Johnson']]
    ));
  }

  #[Test, Values(['else', '^'])]
  public function else_invoked_for_non_truthy($else) {
    Assert::equals('Default', $this->evaluate('{{#with var}}-{{.}}-{{'.$else.'}}Default{{/with}}', [
      'var' => false
    ]));
  }

  #[Test]
  public function with_as() {
    Assert::equals("Karlsruhe: 49° 0' 34&quot;, 8° 24' 15&quot;", $this->evaluate(
      '{{#with city.location as | loc |}}{{city.name}}: {{loc.north}}, {{loc.east}}{{/with}}',
      ['city' => ['name' => 'Karlsruhe', 'location' => ['north' => '49° 0\' 34"', 'east' => '8° 24\' 15"']]]
    ));
  }
}