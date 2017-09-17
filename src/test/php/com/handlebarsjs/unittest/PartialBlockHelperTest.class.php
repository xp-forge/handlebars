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
    $this->templates->add('includes/hero', '<div class="hero"><img src="{{hero-src}}" alt="{{hero-alt}}"/></div>');
    $this->templates->add('layout', trim('
      <html>
        <head><title>{{title}}</title>{{#> head}}{{/head}}</head>
        <body>
          {{#> hero}}(No hero){{/hero}}
        </body>
      </html>
    '));

    $this->assertEquals(trim('
      <html>
        <head><title>Home</title><link rel="stylesheet" href="style.css"></head>
        <body>
          <div class="hero"><img src="hero.jpg" alt="Hero 1 alt title"/></div>
        </body>
      </html>'),
      $this->evaluate(trim('
        {{#> layout title="Home"}}
          {{#*inline "head"}}<link rel="stylesheet" href="style.css">{{/inline}}
          {{#*inline "hero"}}{{> includes/hero hero-src="hero.jpg" hero-alt="Hero 1 alt title"}}{{/inline}}
        {{/layout}}'),
        []
      )
    );
  }
}