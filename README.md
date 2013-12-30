HandleBars for XP Framework
============================
The [HandleBars template language](http://handlebarsjs.com/) implemented for the XP Framework.

```php
$engine= new \com\handlebarsjs\HandleBarsEngine();
$transformed= $engine->render(
  'Hello {{name}}',
  array('name' => 'World')
);
```

