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

