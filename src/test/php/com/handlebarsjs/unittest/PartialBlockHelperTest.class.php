<?php namespace com\handlebarsjs\unittest;

use test\{Assert, Test};

class PartialBlockHelperTest extends HelperTest {

  #[Test]
  public function existing_partial() {
    $templates= $this->templates(['layout' => 'My layout']);
    Assert::equals('My layout', $this->engine($templates)->render('{{#> layout}}My content{{/layout}}', []));
  }

  #[Test]
  public function existing_partial_with_partial_block() {
    $templates= $this->templates(['layout' => 'My layout {{> @partial-block }}']);
    Assert::equals('My layout My content', $this->engine($templates)->render('{{#> layout}}My content{{/layout}}', []));
  }

  #[Test]
  public function non_existant_partial_renders_default() {
    Assert::equals('My content', $this->engine()->render('{{#> layout}}My content{{/layout}}', []));
  }

  #[Test]
  public function nested_partial_block() {
    $templates= $this->templates(['layout' => '{{#> inner}}Inner{{/inner}} {{> @partial-block }}']);
    Assert::equals('Inner Outer', $this->engine($templates)->render('{{#> layout}}Outer{{/layout}}', []));
  }

  #[Test]
  public function default_can_reference_options() {
    Assert::equals('Title', $this->engine()->render('{{#> layout title="Title"}}{{title}}{{/layout}}', []));
  }

  #[Test]
  public function block_can_reference_options() {
    $templates= $this->templates(['layout' => '{{title}}']);
    Assert::equals('Title', $this->engine($templates)->render('{{#> layout title="Title"}}Default{{/layout}}', []));
  }

  #[Test]
  public function block_can_reference_options_hash() {
    $templates= $this->templates(['layout' => '{{title}} - {{name.en}}']);
    Assert::equals('Home - Tool', $this->engine($templates)->render('{{#> layout title="Home" name=theme.name}}Default{{/layout}}', [
      'theme' => ['name' => ['en' => 'Tool']]
    ]));
  }

  #[Test]
  public function partial_inside_each() {
    $templates= $this->templates(['list' => '[{{#each .}}<item>{{> @partial-block}}</item>{{/each}}]']);
    Assert::equals(
      '[<item>value = a</item><item>value = b</item><item>value = c</item>]',
      $this->engine($templates)->render('{{#> list value}}value = {{.}}{{/list}}', ['value' => ['a', 'b', 'c']])
    );
  }

  #[Test]
  public function inline() {
    $templates= $this->templates(['layout' => 'The {{#> content}}failure{{/content}} comes here']);
    Assert::equals(
      'The content comes here',
      $this->engine($templates)->render('{{#> layout}}{{#*inline "content"}}content{{/inline}}{{/layout}}', [])
    );
  }

  #[Test]
  public function apply_inline_partial_inside_each() {
    $templates= $this->templates([
      'fixture' => '
        {{#*inline "list-item"}}<li>{{.}}</li>{{/inline}}
        <ul>{{#each items}}{{> list-item}}{{/each}}</ul>
      '
    ]);

    Assert::equals(
      '<ul><li>One</li><li>Two</li></ul>',
      trim($this->engine($templates)->transform('fixture', ['items' => ['One', 'Two']]))
    );
  }

  #[Test]
  public function inline_template_not_available_after_transformation() {
    $engine= $this->engine($this->templates([
      'fixture' => '{{#*inline "test"}}Test{{/inline}}{{> test}}'
    ]));
    $engine->transform('fixture', []);

    Assert::false($engine->templates()->source('test')->exists());
  }

  #[Test]
  public function layout() {
    $templates= $this->templates([
      'includes/hero' => '<div class="hero"><img src="{{hero-src}}" alt="{{hero-alt}}"/></div>',
      'layout'        => trim('
      <html>
        <head><title>{{title}}</title>{{#> head}}{{/head}}</head>
        <body>
          {{#> hero}}(No hero){{/hero}}
        </body>
      </html>'
    )]);

    Assert::equals(trim('
      <html>
        <head><title>Home</title><link rel="stylesheet" href="style.css"></head>
        <body>
          <div class="hero"><img src="hero.jpg" alt="Hero 1 alt title"/></div>
        </body>
      </html>'),
      $this->engine($templates)->render(trim('
        {{#> layout title="Home"}}
          {{#*inline "head"}}<link rel="stylesheet" href="style.css">{{/inline}}
          {{#*inline "hero"}}{{> includes/hero hero-src="hero.jpg" hero-alt="Hero 1 alt title"}}{{/inline}}
        {{/layout}}'),
        []
      )
    );
  }
}