<?php namespace com\handlebarsjs\unittest;

use unittest\{Assert, Test};

class PartialHelperTest extends HelperTest {

  #[Test]
  public function existing_partial() {
    $templates= $this->templates(['layout' => 'My layout']);
    Assert::equals('My layout', $this->engine($templates)->render('{{> layout}}', []));
  }

  #[Test]
  public function dynamic_partial() {
    $templates= $this->templates(['layout' => 'My layout']);
    Assert::equals('My layout', $this->engine($templates)->render('{{> (whichPartial)}}', ['whichPartial' => 'layout']));
  }

  #[Test]
  public function dynamic_partial_via_lookup() {
    $templates= $this->templates(['layout' => 'My layout']);
    Assert::equals('My layout', $this->engine($templates)->render('{{> (lookup . "whichPartial")}}', ['whichPartial' => 'layout']));
  }

  #[Test]
  public function partial_contexts() {
    $templates= $this->templates(['layout' => 'My layout for {{name.en}}']);
    Assert::equals('My layout for Tool', $this->engine($templates)->render('{{> layout theme}}', [
      'theme' => ['name' => ['en' => 'Tool']]]
    ));
  }

  #[Test]
  public function partial_parameters() {
    $templates= $this->templates(['layout' => 'My layout for {{name.en}}']);
    Assert::equals('My layout for Tool', $this->engine($templates)->render('{{> layout name=theme.name}}', [
      'theme' => ['name' => ['en' => 'Tool']]]
    ));
  }
}