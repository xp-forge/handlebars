HandleBars change log
=====================

## ?.?.? / ????-??-??

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

* Changed dependency to use XP ~6.0 (instead of dev-master) - @thekid
* Fixed logging to a `util.log.LogCategory` with only one argument, e.g.
  `{{log 'Hello World'}}`. Use *debug* by by default.
  (@thekid)

## 0.1.0 / 2015-01-10

* First public release - @thekid