<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;
use unittest\{Assert, Test};

/** See https://handlebarsjs.com/guide/expressions.html#escaping-handlebars-expressions */
class EscapingTest {

  /**
   * Render a template with a list of variables
   *
   * @param  string $template
   * @param  [:var] $helpers
   * @return string
   */
  protected function render($template, $variables= []) {
    return (new HandlebarsEngine())->render($template, $variables);
  }

  #[Test]
  public function backslash_mustache() {
    Assert::equals('{{escaped}}', $this->render('\\{{escaped}}'));
  }

  #[Test]
  public function backslash_mustache_inside_text() {
    Assert::equals('An {{escaped}} tag', $this->render('An \\{{escaped}} tag'));
  }

  #[Test]
  public function backslash_mustache_at_beginning() {
    Assert::equals('{{escaped}} tags', $this->render('\\{{escaped}} tags'));
  }

  #[Test]
  public function backslash_mustache_at_end() {
    Assert::equals('It\'s {{escaped}}', $this->render('It\'s \\{{escaped}}'));
  }

  #[Test]
  public function backslash_mustaches() {
    Assert::equals('Escaped {{a}}{{b}}+{{c}} tags', $this->render('Escaped \\{{a}}\\{{b}}+\\{{c}} tags'));
  }

  #[Test]
  public function backslash_mustaches_and_unescaped_mustache() {
    Assert::equals('Escaped {{a}}{{b}}+{{c}} tags', $this->render('Escaped \\{{a}}\\{{b}}{{and}}\\{{c}} tags', ['and' => '+']));
  }

  #[Test]
  public function raw_blocks() {
    Assert::equals(
      'Handlebars syntax here: {{handlebars}}!',
      $this->render('Handlebars syntax here: {{{{raw}}}}{{handlebars}}{{{{/raw}}}}!')
    );
  }
}