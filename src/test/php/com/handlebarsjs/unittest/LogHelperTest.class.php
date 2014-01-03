<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;
use util\log\BufferedAppender;
use util\log\LogCategory;
use util\log\layout\PatternLayout;

class LogHelperTest extends \unittest\TestCase {

  #[@test]
  public function log_to_closure() {
    $messages= create('new util.collections.Vector<string[]>()');
    $engine= create(new HandlebarsEngine())->withLogger(function($args) use($messages) {
      $messages->add($args);
    });
    $engine->render('{{log "Look at me!"}}', array());
    $this->assertEquals(array('Look at me!'), $messages[0]);
  }

  #[@test]
  public function log_to_closure_with_multiple_arguments() {
    $messages= create('new util.collections.Vector<string>()');
    $engine= create(new HandlebarsEngine())->withLogger(function($args) use($messages) {
      $level= array_shift($args);
      $messages->add('['.$level.'] '.implode(' ', $args));
    });
    $engine->render('{{log "info" "Look at me!"}}', array());
    $this->assertEquals('[info] Look at me!', $messages[0]);
  }

  #[@test]
  public function log_to_LogAppender_from_util_log() {
    $appender= create(new BufferedAppender())->withLayout(new PatternLayout('[%l] %m'));
    $engine= create(new HandlebarsEngine())->withLogger(
      create(new LogCategory('trace'))->withAppender($appender)
    );
    $engine->render('{{log "info" "Look at me!"}}', array());
    $this->assertEquals('[info] Look at me!', $appender->getBuffer());
  }
}