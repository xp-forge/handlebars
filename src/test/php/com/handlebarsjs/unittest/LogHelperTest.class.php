<?php namespace com\handlebarsjs\unittest;

use com\handlebarsjs\HandlebarsEngine;
use unittest\{Assert, Test};
use util\log\{BufferedAppender, Layout, LogCategory, LogLevel, LoggingEvent};

class LogHelperTest {

  #[Test]
  public function log_to_closure() {
    $messages= [];
    $engine= (new HandlebarsEngine())->withLogger(function($args) use(&$messages) {
      $messages[]= $args;
    });
    $engine->render('{{log "Look at me!"}}', []);
    Assert::equals(['Look at me!'], $messages[0]);
  }

  #[Test]
  public function log_to_closure_using_multiple_arguments() {
    $messages= [];
    $engine= (new HandlebarsEngine())->withLogger(function($args) use(&$messages) {
      $messages[]= $args;
    });
    $engine->render('{{log "Look at me" test}}', ['test' => '!']);
    Assert::equals(['Look at me', '!'], $messages[0]);
  }

  #[Test, Values(map: ['' => 'INFO', ' level="debug"' => 'DEBUG', ' level="info"' => 'INFO'])]
  public function log_to_closure_with_level($level, $output) {
    $messages= [];
    $engine= (new HandlebarsEngine())->withLogger(function($args, $level) use(&$messages) {
      $messages[]= '['.strtoupper($level).'] '.implode(' ', $args);
    });
    $engine->render('{{log "Look at me!"'.$level.'}}', []);
    Assert::equals('['.$output.'] Look at me!', $messages[0]);
  }

  #[Test, Values(map: ['' => 'INFO', ' level="debug"' => 'DEBUG', ' level="info"' => 'INFO'])]
  public function log_to_logAppender($level, $output) {
    $appender= (new BufferedAppender())->withLayout(new class() extends Layout {
      public function format(LoggingEvent $event) {
        return sprintf('[%s] %s', LogLevel::nameOf($event->getLevel()), ...$event->getArguments());
      }
    });
    $engine= (new HandlebarsEngine())->withLogger(
      (new LogCategory('trace'))->withAppender($appender)
    );
    $engine->render('{{log "Look at me!"'.$level.'}}', []);
    Assert::equals('['.$output.'] Look at me!', $appender->getBuffer());
  }
}