# Advanced WordPress Hooks API

Set of (experimental) wrappers that allow to hook more elaborate WordPress events without coding intermediary functions.

WordPress only operates with callbacks for actions and filters. That means that you always need to use callback, either:

 - provided by core (limited)
 - closures (messy)
 - coded by yourself (this - a lot)

This plugins offers number of custom `add_*` functions to hook more elaborate events:

 - `add_action_with_args()` - hook callback **and** arguments to run it with
 - `add_filter_return()` - override filter with arbitrary value
 - `add_filter_prepend()` and `add_filter_append()` - hook suffix/prefix values for filtered string and arrays
 - `add_filter_replace()` - edit substrings or array values in filter
 - `add_action_once()` and `add_filter_once()` - hook callback to only fire once
 - `add_method()` - quickly add class methods to hooks of same name

Both implementation and set of functions are experimental.

# Changelog

## 0.7

* _(enhancement)_ implemented `add_action_once()` and `add_filter_once()`, props Eric Mann

## 0.6

* _(enhancement)_ changed license to MIT
* _(enhancement)_ cleaned up coding style
* _(enhancement)_ moved handler class to separate file
* _(enhancement)_ implemented removing of handler from hooks
* **(breaking change)** changed signatures to make priority optional

## 0.5

* _(enhancement)_ new add_method() function
* _(enhancement)_ more return points for better logic and compatibility

## 0.4.1

* _(bugfix)_ action() method expected argument passed (to be compatible with filters), changed to func_get_arg()

## 0.4

* Initial public repository release.
