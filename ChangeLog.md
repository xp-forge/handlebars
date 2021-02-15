HandleBars change log
=====================

## ?.?.? / ????-??-??

## 5.2.0 / 2021-02-15

* Merged PR #14: Inline MustacheEngine, further reducing indirections and
  dependencies
  (@thekid)

## 5.1.1 / 2020-12-29

* Fixed `com.handlebarsjs.BlockNode` string cast not yielding options
  correctly separated from node name
  (@thekid)

## 5.1.0 / 2020-12-26

* Merged PR #13: Add support for escaping tags - either by prefixing a
  backslash (`\{{escaped}}`) or by using quadruple mustaches. See
  https://handlebarsjs.com/guide/expressions.html#escaping-handlebars-expressions
  (@thekid)

## 5.0.0 / 2020-04-10

* Implemented xp-framework/rfc#334: Drop PHP 5.6:
  . **Heads up:** Minimum required PHP version now is PHP 7.0.0
  . Rewrote code base, grouping use statements
  . Converted `newinstance` to anonymous classes
  . Rewrote `isset(X) ? X : default` to `X ?? default`
  (@thekid)

## 4.3.4 / 2020-04-05

* Implemented RFC #335: Remove deprecated key/value pair annotation syntax
  (@thekid)

## 4.3.3 / 2019-12-01

* Made compatible with XP 10 - @thekid

## 4.3.2 / 2019-08-20

* Made compatible with PHP 7.4 - refrain using `{}` for string offsets
  (@thekid)

## 4.3.1 / 2019-05-14

* Fix issue #12: Fix hash options passed to partials - @thekid

## 4.3.0 / 2019-01-13

* Added support for generators in `each`, where they are treated like
  hashes, producing `@key` and `@first` selectors for each yielded value.
  Hoewever, note generators can only be iterated once due to the nature of
  their implementation!
  (@thekid)
* Added support for generators in `if` and `unless`. Iterators yielding
  at least one element are considered truthy.
  (@thekid)

## 4.2.3 / 2018-08-24

* Made compatible with `xp-framework/logging` version 9.0.0 - @thekid

## 4.2.2 / 2018-08-22

* Fixed multiline tokens creating hundreds of zero option values - @thekid

## 4.2.1 / 2018-04-22

* Fixed issue #11: Call to a member function name() on null when no start
  tag is present, but a close tag is encountered
  (@thekid)

## 4.2.0 / 2018-03-14

* Merged pull request #10: Add HandleBarsEngine::write() - @thekid

## 4.1.0 / 2017-11-26

* Merged pull request #9: Partial blocks and inline partials - @thekid

## 4.0.2 / 2017-09-01

* Fixed issue #8: Call to undefined method `VariableNode::lookup()` when
  using variables for partial context
  (@thekid)

## 4.0.1 / 2017-06-12

* Fixed issue #6: Dependencies - @thekid

## 4.0.0 / 2017-06-03

* Added forward compatibility with XP 9.0.0 - @thekid

## 3.0.2 / 2017-05-20

* Refactored code to use `typeof()` instead of `xp::typeOf()`, see
  https://github.com/xp-framework/rfc/issues/323
  (@thekid)

## 3.0.1 / 2017-03-20

* Fixed lang.Error (Argument 3 passed to `ListContext::__construct()`
  must be an instance of com\github\mustache\Context, none given).
  (@thekid)

## 3.0.0 / 2016-08-28

* **Heads up: Dropped PHP 5.5 support!** - @thekid
* Added forward compatibility with XP 8.0.0 - @thekid
* Fixed issue #4: Missing tests for literals - by adding tests and
  fixing numbers, null and empty strings in the course of doing so
  (@thekid)
* Fixed issue #3: Error with incorrect end tag - @thekid
* Implemented partials contexts and parameters. These can be used to
  create shared partials and to expose data from other contexts.
  (@thekid)
* Implemented [dynamic partials](http://handlebarsjs.com/partials.html)
  (@thekid)
* Added support for the `lookup` builtin added in HandleBars.js 2.0.0
  See https://github.com/wycats/handlebars.js/commit/306feb4
  (@thekid)
* Fixed subexpressions lookup for *current nodeset* (`.`).
  (@thekid)
* Made helper signature for subexpressions consistent with signatures
  for nodes: `function(Node $items, Context $context, $options)`
  (@thekid)

## 2.0.0 / 2016-02-21

* Added version compatibility with XP 7 - @thekid

## 1.0.2 / 2016-01-23

* Fix code to use `nameof()` instead of the deprecated `getClassName()`
  method from lang.Generic. See xp-framework/core#120
  (@thekid)

## 1.0.1 / 2015-12-20

* Declared dependency on xp-framework/logging, which has since been
  extracted from XP core.
  (@thekid)

## 1.0.0 / 2015-12-14

* **Heads up**: Changed minimum XP version to XP 6.5.0, and with it the
  minimum PHP version to PHP 5.5.
  (@thekid)

## 0.5.0 / 2015-10-10

* Dropped dependency on `com.handlebarsjs.LogCategoryExtensions` which
  was necessary as long as xp-framework/core#4 hadn't been merged
  (@thekid)

## 0.4.1 / 2015-07-12

* Rewrote codebase to use short array syntax - @thekid

## 0.4.0 / 2015-07-12

* Added forward compatibility with XP 6.4.0 (@thekid)
* Added preliminary PHP 7 support (alpha2 and beta1) (@thekid)

## 0.3.0 / 2015-06-13

* Verified support for PHP7 when using XP 6.3.1
  (@thekid)
* Renamed the package-internal *String* class to `com.handlebarsjs.Quoted`.
  See https://wiki.php.net/rfc/reserve_more_types_in_php_7
  (@thekid)

## 0.2.0 / 2015-02-12

* Changed dependency to use XP 6.0 (instead of dev-master) - @thekid
* Fixed logging to a `util.log.LogCategory` with only one argument, e.g.
  `{{log 'Hello World'}}`. Use *debug* by by default.
  (@thekid)

## 0.1.0 / 2015-01-10

* First public release - @thekid
