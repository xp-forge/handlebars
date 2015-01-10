Handlebars for XP Framework
============================

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/handlebars.svg)](http://travis-ci.org/xp-forge/handlebars)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_4plus.png)](http://php.net/)
[![Required HHVM 3.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_4plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/handlebars/version.png)](https://packagist.org/packages/xp-forge/handlebars)

The [Handlebars template language](http://handlebarsjs.com/) implemented for the XP Framework.

```php
use com\handlebarsjs\HandlebarsEngine;

$engine= new HandlebarsEngine();
$transformed= $engine->render(
  'Hello {{name}}',
  ['name' => 'World']
);
```

Helpers supported
-----------------
The following helpers are built in:

### The "if" block
```HTML+Django
{{#if licence}}
  A licence is available
{{/if}}

{{#if licence}}
  A licence is available
{{else}}
  <em>Warning: No licence is available!</em>
{{/if}}
```

### The "unless" block
```HTML+Django
{{#unless licence}}
  <em>Warning: No licence is available!</em>
{{/unless}}
```

### The "with" block
```HTML+Django
{{#with person}}
  Full name: {{firstName}} {{lastName}}
{{/with}}
```

### The "each" block
```HTML+Django
<ul>
  {{#each students}}
  <li>Student's name: {{firstName}} {{lastName}}</li>
  {{/each}}
</ul>
```

All of the above block helpers support the `else` statement.

### The "log" helper
```HTML+Django
{{log '"Hello", Frank\'s mother said.'}}
```

To enable logging, pass either a closure or a `util.log.LogCategory` instance to the engine:

```php
use util\log\LogCategory;
use util\log\ConsoleAppender;
use util\cmd\Console;

// Use a logger category:
$logger= (new LogCategory('trace'))->withAppender(new ConsoleAppender());

// Or a closure:
$logger= function($arg) { Console::writeLine('[LOG] ', $arg); };

$engine= (new HandlebarsEngine())->withLogger($logger);
$engine->render(...);
```