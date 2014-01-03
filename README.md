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
{{log 'message'}}
```
