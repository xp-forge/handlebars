Handlebars for XP Framework
============================
The [Handlebars template language](http://handlebarsjs.com/) implemented for the XP Framework.

```php
$engine= new \com\handlebarsjs\HandlebarsEngine();
$transformed= $engine->render(
  'Hello {{name}}',
  array('name' => 'World')
);
```

Helpers supported
-----------------
The following helpers are built in:

### The "if" block
```mustache
{{#if licence}}
  A licence is available
{{/if}}

{{#if licence}}
  A licence is available
{{else}}
  Warning: No licence is available!
{{/if}}
```

### The "unless" block
```mustache
{{#unless licence}}
  Warning: No licence is available!
{{/unless}}
```

### The "with" block
```mustache
{{#with person}}
  Full name: {{firstName}} {{lastName}}
{{/with}}
```

### The "each" block
```mustache
{{#each students}}
  Student's name: {{firstName}} {{lastName}}
{{/each}}
```

### The "log" helper
```mustache
{{log 'message'}}
```