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
	queryPanel: Nextras\TracyQueryPanel\Bridges\NetteDI\QueryPanelExtension
```

Additional resources
--------------------

- Original forum thread: http://forum.nette.org/en/18901-tracy-universal-query-panel

License
-------

MIT. See full [license](license.md).
