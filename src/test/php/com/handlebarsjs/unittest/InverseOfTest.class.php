<?php namespace com\handlebarsjs\unittest;

use test\{Assert, Test, Values};

class InverseOfTest extends HelperTest {

  #[Test, Values([[null, 'Guest'], [['name' => 'Test'], 'User Test']])]
  public function inverse_section($input, $outcome) {
    Assert::equals($outcome, $this->evaluate(
      '{{#person}}User {{name}}{{/person}}{{^person}}Guest{{/person}}',
      ['person' => $input]
    ));
  }

  #[Test, Values([[[], 'no people'], [['A', 'B'], 'AB']])]
  public function inverse_iterator($input, $outcome) {
    Assert::equals($outcome, $this->evaluate(
      '{{#people}}{{this}}{{/people}}{{^people}}no people{{/people}}',
      ['people' => $input]
    ));
  }

  #[Test, Values([[[], 'no people'], [['A', 'B'], 'AB']])]
  public function inverse_iterator_with_else($input, $outcome) {
    Assert::equals($outcome, $this->evaluate(
      '{{#people}}{{this}}{{else}}no people{{/people}}',
      ['people' => $input]
    ));
  }

  #[Test, Values([[[], 'no people'], [['A', 'B'], 'AB']])]
  public function inverse_iterator_with_short_else($input, $outcome) {
    Assert::equals($outcome, $this->evaluate(
      '{{#people}}{{this}}{{^}}no people{{/people}}',
      ['people' => $input]
    ));
  }

  #[Test, Values([[[], 'no people'], [['A', 'B'], 'some people']])]
  public function inverse_if_with_else($input, $outcome) {
    Assert::equals($outcome, $this->evaluate(
      '{{^if people}}no people{{else}}some people{{/if}}',
      ['people' => $input]
    ));
  }

  #[Test, Values([[[], 'no people'], [['A', 'B'], 'some people']])]
  public function inverse_if_with_short_else($input, $outcome) {
    Assert::equals($outcome, $this->evaluate(
      '{{^if people}}no people{{^}}some people{{/if}}',
      ['people' => $input]
    ));
  }
}