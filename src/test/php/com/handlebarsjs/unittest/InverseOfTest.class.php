<?php namespace com\handlebarsjs\unittest;

use test\{Assert, Test, Values};

class InverseOfTest extends HelperTest {

  #[Test, Values([[[], 'no people'], [['A', 'B'], 'some people']])]
  public function inverse_if_with_else($input, $outcome) {
    Assert::equals($outcome, $this->evaluate(
      '{{^if people}}no people{{else}}some people{{/if}}',
      ['people' => $input]
    ));
  }
}