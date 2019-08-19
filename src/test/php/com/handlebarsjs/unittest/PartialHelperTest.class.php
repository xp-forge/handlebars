<?php namespace com\handlebarsjs\unittest;

class PartialHelperTest extends HelperTest {

  #[@test]
  public function existing_partial() {
    $this->templates->add('layout', 'My layout');
    $this->assertEquals('My layout', $this->evaluate('{{> layout}}', []));
  }

  #[@test]
  public function dynamic_partial() {
    $this->templates->add('layout', 'My layout');
    $this->assertEquals('My layout', $this->evaluate('{{> (whichPartial)}}', ['whichPartial' => 'layout']));
  }

  #[@test]
  public function dynamic_partial_via_lookup() {
    $this->templates->add('layout', 'My layout');
    $this->assertEquals('My layout', $this->evaluate('{{> (lookup . "whichPartial")}}', ['whichPartial' => 'layout']));
  }

  #[@test]
  public function partial_contexts() {
    $this->templates->add('layout', 'My layout for {{name.en}}');
    $this->assertEquals('My layout for Tool', $this->evaluate('{{> layout theme}}', [
      'theme' => ['name' => ['en' => 'Tool']]]
    ));
  }

  #[@test]
  public function partial_parameters() {
    $this->templates->add('layout', 'My layout for {{name.en}}');
    $this->assertEquals('My layout for Tool', $this->evaluate('{{> layout name=theme.name}}', [
      'theme' => ['name' => ['en' => 'Tool']]]
    ));
  }
}