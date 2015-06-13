HandleBars change log
=====================

## ?.?.? / ????-??-??

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
