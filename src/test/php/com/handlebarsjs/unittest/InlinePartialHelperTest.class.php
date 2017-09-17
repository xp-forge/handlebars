<?php namespace com\handlebarsjs\unittest;

class InlinePartialHelperTest extends HelperTest {

  #[@test]
  public function basic_usage() {
    $template= '{{#*inline "myPartial"}}My Content{{/inline}}{{#each children}}{{> myPartial}} {{/each}}';
    $this->assertEquals('My Content My Content My Content ', $this->evaluate($template, ['children' => [1, 2, 3]]));
  }
}