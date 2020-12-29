Handlebars for XP Framework
============================

[![Build status on GitHub](https://github.com/xp-forge/handlebars/workflows/Tests/badge.svg)](https://github.com/xp-forge/handlebars/actions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.svg)](http://php.net/)
[![Supports PHP 8.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-8_0plus.svg)](http://php.net/)
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
```handlebars
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
```handlebars
{{#unless licence}}
  <em>Warning: No licence is available!</em>
{{/unless}}
```

### The "with" block
```handlebars
{{#with person}}
  Full name: {{firstName}} {{lastName}}
{{/with}}
```

### The "each" block
```handlebars
<ul>
  {{#each students}}
    <li>Student's name: {{firstName}} {{lastName}}</li>
  {{/each}}
</ul>
```

All of the above block helpers support the `else` statement.

### The "log" helper
```handlebars
{{log '"Hello", Frank\'s mother said.'}}
```

To enable logging, pass either a closure or a `util.log.LogCategory` instance to the engine:

```php
use util\log\Logging;
use util\cmd\Console;

// Use a logger category:
$logger= Logging::named('trace')->toConsole();

// Or a closure:
$logger= function($arg) { Console::writeLine('[LOG] ', $arg); };

$engine= (new HandlebarsEngine())->withLogger($logger);
$engine->render(...);
```

Futher reading
--------------
https://handlebars-lang.github.io/spec/