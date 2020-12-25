<?php namespace com\handlebarsjs\unittest;

use unittest\{Assert, Test};

class PartialHelperTest extends HelperTest {

  #[Test]
  public function existing_partial() {
    $this->templates->add('layout', 'My layout');
    Assert::equals('My layout', $this->evaluate('{{> layout}}', []));
  }

  #[Test]
  public function dynamic_partial() {
    $this->templates->add('layout', 'My layout');
    Assert::equals('My layout', $this->evaluate('{{> (whichPartial)}}', ['whichPartial' => 'layout']));
  }

  #[Test]
  public function dynamic_partial_via_lookup() {
    $this->templates->add('layout', 'My layout');
    Assert::equals('My layout', $this->evaluate('{{> (lookup . "whichPartial")}}', ['whichPartial' => 'layout']));
  }

  #[Test]
  public function partial_contexts() {
    $this->templates->add('layout', 'My layout for {{name.en}}');
    Assert::equals('My layout for Tool', $this->evaluate('{{> layout theme}}', [
      'theme' => ['name' => ['en' => 'Tool']]]
    ));
  }

  #[Test]
  public function partial_parameters() {
    $this->templates->add('layout', 'My layout for {{name.en}}');
    Assert::equals('My layout for Tool', $this->evaluate('{{> layout name=theme.name}}', [
      'theme' => ['name' => ['en' => 'Tool']]]
    ));
  }
}