<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;
use util\log\{BufferedAppender, Layout, LogCategory, LogLevel, LoggingEvent};

class LogHelperTest extends \unittest\TestCase {

  #[@test]
  public function log_to_closure() {
    $messages= [];
    $engine= (new HandlebarsEngine())->withLogger(function($args) use(&$messages) {
      $messages[]= $args;
    });
    $engine->render('{{log "Look at me!"}}', []);
    $this->assertEquals(['Look at me!'], $messages[0]);
  }

  #[@test]
  public function log_to_closure_with_multiple_arguments() {
    $messages= [];
    $engine= (new HandlebarsEngine())->withLogger(function($args) use(&$messages) {
      $level= array_shift($args);
      $messages[]= '['.$level.'] '.implode(' ', $args);
    });
    $engine->render('{{log "info" "Look at me!"}}', []);
    $this->assertEquals('[info] Look at me!', $messages[0]);
  }

  #[@test]
  public function log_to_LogAppender_from_util_log() {
    $appender= (new BufferedAppender())->withLayout(new class() extends Layout {
      public function format(LoggingEvent $event) {
        return sprintf('[%s] %s', LogLevel::nameOf($event->getLevel()), ...$event->getArguments());
      }
    });
    $engine= (new HandlebarsEngine())->withLogger(
      (new LogCategory('trace'))->withAppender($appender)
    );
    $engine->render('{{log "info" "Look at me!"}}', []);
    $this->assertEquals('[INFO] Look at me!', $appender->getBuffer());
  }

  #[@test]
  public function log_to_LogAppender_from_util_log_with_only_one_argument() {
    $appender= (new BufferedAppender())->withLayout(new class() extends Layout {
      public function format(LoggingEvent $event) {
        return sprintf('[%s] %s', LogLevel::nameOf($event->getLevel()), ...$event->getArguments());
      }
    });
    $engine= (new HandlebarsEngine())->withLogger(
      (new LogCategory('trace'))->withAppender($appender)
    );
    $engine->render('{{log "Look at me!"}}', []);
    $this->assertEquals('[DEBUG] Look at me!', $appender->getBuffer());
  }
}