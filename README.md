Propel Logger Bundle
=========

This logger show you full queries stacktraces and duplicate queries.  
For each stacktrace you can see the inner class/file code.

## Prerequisites

This version of the bundle requires Symfony `=> 2.2.x`. If you use Symfony `2.1.x`, please switch to the branch `2.1`.

## Installation

### Step 1: Download PropelLoggerBundle using composer

In your composer.json, add PropelLoggerBundle **(only for dev)** :

```js
{
    "require-dev": {
        "divi/propel-logger-bundle": "2.2.*@dev"
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

Configure the logger, you must provide your application namespace (only the organisation, like "Acme"). You can provide an array, and/or regex.
Warning: if you provide a fully namespace, like "Foo\Bar", please double the backslash : it's a regex.

``` yaml
# app/config/config.yml
divi_propel_logger:
    namespaces: ["Acme", "Foo\\Bar"]
```

## How to use

Now, in the Symfony web debug toolbar, you'll have a red circle if you have a duplicate query. If you want to see the stacktrace, open the Propel profiler, and click on "Explain the query" on the selected query.

## Test instance

You can have memory leak issue in test instance (unit tests) due to the stacktrace saving process.  
If an issue appears, just load the bundle only for dev instance in your AppKernel, move the bundle configuration to `config_dev.yml` and in the `config_test.yml`, do not import the `config_dev.yml` file but `config.yml`. Finally, copy and paste the `monolog` configuration section from the dev config to your test config file.

## Issue or new feature ?

Feel free to post your issue or feature request in the [issue tracker](https://github.com/Divi/PropelLoggerBundle/issues) !
