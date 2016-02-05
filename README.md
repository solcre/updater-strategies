Updaters Strategies that fetch information to be consume by ApisUy.
=======================

Introduction
------------
This simple package will allow you create your custom strategy to fetch data
from your source and then be used in ApisUy.

Installation
------------

Using Composer (recommended)
----------------------------
Add the following dependency to your composer.json:

     "require": {
        "solcre/updater-strategies": "dev-master"
    }

Alternately, you can run this in console:

    composer require solcre/updater-strategies


Requirements
----------------

### Server

PHP 5.3+

Creating Strategies
----------------

All your strategies must implement SourceUpdaterInterface.

