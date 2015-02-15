Nextras\TracyQueryPanel
=======================

[![Build Status](https://travis-ci.org/nextras/tracy-query-panel.svg?branch=master)](https://travis-ci.org/nextras/tracy-query-panel)
[![Downloads this Month](https://img.shields.io/packagist/dm/nextras/tracy-query-panel.svg?style=flat)](https://packagist.org/packages/nextras/tracy-query-panel)
[![Stable version](http://img.shields.io/packagist/v/nextras/tracy-query-panel.svg?style=flat)](https://packagist.org/packages/nextras/tracy-query-panel)
[![HHVM Status](http://img.shields.io/hhvm/nextras/tracy-query-panel.svg?style=flat)](http://hhvm.h4cc.de/package/nextras/tracy-query-panel)


Installation
------------

Add to your `composer.json`:

```bash
$ composer require nextras/tracy-query-panel
```

Register Nette DI extension:

```yaml
extensions:
	queryPanel: Nextras\TracyQueryPanel\DI\QueryPanelExtension

queryPanel:
    - Nextras\TracyQueryPanel\Handlers\DibiHandler
```

Possibly create implementations of `IVoidQueries` based on your service hooks and pass them to `QueryPanel::addQuery()`.

Currently implemented query handlers are:

- DibiConnection
- Nette\Database

However, it should be super straightforward to implement `IQuery` producer for any engine.

Example legacy implementations for:

- Neo4j
- Elasticsearch

can be found at https://github.com/Mikulas/nette-panel-queries/tree/master/queries.
These implementations will not however work out of the box.

If you happen to create implementations for your engine, please consider sending a pull request into this repo.

Additional resources
--------------------

- Original forum thread: http://forum.nette.org/en/18901-tracy-universal-query-panel

License
-------

MIT. See full [license](license.md).
