<?php namespace com\handlebarsjs\unittest;

use com\github\mustache\{FilesIn, InMemory};
use com\handlebarsjs\{HandlebarsEngine, HandlebarsParser};
use io\streams\MemoryOutputStream;
use io\{Path, File, Files, Folder};
use lang\{IllegalArgumentException, Environment};
use test\{Assert, Expect, Test, Values};
use util\log\LogCategory;

class EngineTest {

  /** @return iterable */
  private function files() {
    yield [function($temp) { return $temp->getURI(); }, 'string'];
    yield [function($temp) { return new Folder($temp->getURI()); }, 'io.Folder'];
    yield [function($temp) { return new Path($temp->getURI()); }, 'io.Path'];
    yield [function($temp) { return new FilesIn($temp, ['.handlebars']); }, 'com.github.mustache.FilesIn'];
  }

  #[Test]
  public function can_create() {
    new HandlebarsEngine();
  }

  #[Test]
  public function initially_no_logger_set() {
    $engine= new HandlebarsEngine();
    Assert::null($engine->helper('log'));
  }

  #[Test]
  public function withLogger_using_closure_sets_logger() {
    $engine= (new HandlebarsEngine())->withLogger(function($args) { });
    Assert::instance('function(?): void', $engine->helper('log'));
  }

  #[Test]
  public function withLogger_using_LogCategory_sets_logger() {
    $engine= (new HandlebarsEngine())->withLogger(new LogCategory('test'));
    Assert::instance('function(?): void', $engine->helper('log'));
  }

  #[Test]
  public function withLogger_null_unsets_previously_set_logger() {
    $engine= (new HandlebarsEngine())->withLogger(function($args) { });
    $engine->withLogger(null);
    Assert::null($engine->helper('log'));
  }

  #[Test, Expect(IllegalArgumentException::class)]
  public function with_non_callable_logger() {
    (new HandlebarsEngine())->withLogger('log');
  }

  #[Test]
  public function evaluate() {
    $engine= new HandlebarsEngine();
    $result= $engine->evaluate($engine->compile('Hello {{name}}'), ['name' => 'World']);

    Assert::equals('Hello World', $result);
  }

  #[Test]
  public function write() {
    $engine= new HandlebarsEngine();
    $out= new MemoryOutputStream();
    $engine->write($engine->compile('Hello {{name}}'), ['name' => 'World'], $out);

    Assert::equals('Hello World', $out->bytes());
  }

  #[Test]
  public function exchange_parser() {
    $engine= new HandlebarsEngine(new class() extends HandlebarsParser {
      public function version() { return '1.0.0'; }
    });
    Assert::equals('1.0.0', $engine->parser()->version());
  }

  #[Test]
  public function templates_initially_empty() {
    $engine= new HandlebarsEngine();
    Assert::equals([], $engine->templates()->listing()->templates());
  }

  #[Test]
  public function templates_from_loader() {
    $engine= (new HandlebarsEngine())->withTemplates(new InMemory(['test' => 'Hello {{name}}']));
    Assert::equals(['test'], $engine->templates()->listing()->templates());
  }

  #[Test]
  public function templates_from_map() {
    $engine= (new HandlebarsEngine())->withTemplates(['test' => 'Hello {{name}}']);
    Assert::equals(['test'], $engine->templates()->listing()->templates());
  }

  #[Test, Values(from: 'files')]
  public function templates_from_files($arg) {
    $temp= new Folder(Environment::tempDir(), md5(self::class));
    $temp->create();

    try {
      Files::write(new File($temp, 'test.handlebars'), 'Hello {{name}}');
      Files::write(new File($temp, 'translations.csv'), 'Not included in listing');

      $engine= (new HandlebarsEngine())->withTemplates($arg($temp));
      Assert::equals(['test'], $engine->templates()->listing()->templates());
    } finally {
      $temp->unlink();
    }
  }
}