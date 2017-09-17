<?php namespace com\handlebarsjs\unittest;

class PartialBlockHelperTest extends HelperTest {

  #[@test]
  public function existing_partial() {
    $this->templates->add('layout', 'My layout');
    $this->assertEquals('My layout', $this->evaluate('{{#> layout}}My content{{/layout}}', []));
  }

  #[@test]
  public function existing_partial_with_partial_block() {
    $this->templates->add('layout', 'My layout {{> @partial-block }}');
    $this->assertEquals('My layout My content', $this->evaluate('{{#> layout}}My content{{/layout}}', []));
  }

  #[@test]
  public function non_existant_partial_renders_default() {
    $this->assertEquals('My content', $this->evaluate('{{#> layout}}My content{{/layout}}', []));
  }

  #[@test]
  public function layout() {
    $this->templates->add('layout', '<head><title>{{title}}</title>{{#> head}}{{/head}}</head>');
    $this->assertEquals('<head><title>Home</title><link rel="stylesheet" href="style.css"></head>', $this->evaluate(
      '{{#> layout title="Home"}}'.
        '{{#*inline "head"}}<link rel="stylesheet" href="style.css">{{/inline}}'.
      '{{/layout}}',
      []
    ));
  }
}