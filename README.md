Propel Logger Bundle
=========

This logger show you full queries stacktraces and duplicate queries.

## Prerequisites

This version of the bundle requires Symfony => 2.2.x.
If you use Symfony <= 2.1.x, please switch the to branch "2.1".

## Installation

### Step 1: Download PropelLoggerBundle using composer

In your composer.json, add PropelLoggerBundle :

```js
{
    "require": {
        "divi/propel-logger-bundle": "dev-master"
    }
}
```

Now, you must update your vendors using this command :

``` bash
$ php composer.phar update divi/propel-logger-bundle
```

### Step 2: Enable the bundle

Enable the bundle using the AppKernel :

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Divi\PropelLoggerBundle\DiviPropelLoggerBundle(),
    );
}
```

### Step 3: Configure your project

Configure the logger, you must provide your application namespace (only the organisation, like "Acme").
You can provide an array, and/or regex.
Warning: if you provide a fully namespace, like "Foo\Bar", please double the backslash : it's a regex.

``` yaml
# app/config/config.yml
divi_propel_logger:
    namespaces: ["Acme", "Foo\\Bar"]
```

## How to use

Now, in the Symfony web debug toolbar, you'll have a red circle if you have a duplicate query. If you want to see the stacktrace, open the Propel profiler, and click on "Explain the query" on the selected query.

## Issue or new feature ?

Feel free to post your issue or feature request in the [issue tracker](https://github.com/Divi/PropelLoggerBundle/issues) !