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
```HTML+Django
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
```HTML+Django
{{#unless licence}}
  Warning: No licence is available!
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
{{#each students}}
  Student's name: {{firstName}} {{lastName}}
{{/each}}
```

### The "log" helper
```HTML+Django
{{log 'message'}}
```