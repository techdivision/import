# M2IF - Magento 2 Import Framework

[![Latest Stable Version](https://img.shields.io/packagist/v/techdivision/import.svg?style=flat-square)](https://packagist.org/packages/techdivision/import) 
 [![Total Downloads](https://img.shields.io/packagist/dt/techdivision/import.svg?style=flat-square)](https://packagist.org/packages/techdivision/import)
 [![License](https://img.shields.io/packagist/l/techdivision/import.svg?style=flat-square)](https://packagist.org/packages/techdivision/import)
 [![Build Status](https://img.shields.io/travis/techdivision/import/master.svg?style=flat-square)](http://travis-ci.org/techdivision/import)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/techdivision/import/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import/?branch=master) [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/techdivision/import/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import/?branch=master)

This is the core library for the Magento 2 Import Framework M2IF.

The objective M2IF is to provide a fully functional replacement for the Magento 2 standard import functionality
with a 100 % CSV file compatibility. In contrast to other approaches, the framework is not build as a Magento 2
extension, although it has no dependencies to other frameworks like Symfony, Laravel or others. Instead, it 
provides independent components, that can be tied together as needed by adding them as composer dependency.

A simple command line implementation should give a brief overiew of how a simple import application could look
like. For more information, have a look at the [techdivision/import-cli-simple](https://github.com/techdivision/import-cli-simple) 
repository

## Status

Actually we've a pre-alpha status, so we **STRONGLY** recommend not to use M2IF in production now.

The following functionality is already available, for CE and EE

[x] Simple Products
[x] Configurable Products
[x] Bundle Products
[x] Product Relations
[x] Media Gallery
[x] Inventory
[x] Relation with existing Categories
[x] Relation with existing Websites

This is, what we're working on

[ ] Archiving (equivalent to Magento Standard Functionality)
[ ] History (Append to Standard Magento Import History)
[ ] Tier Prices
[ ] Categories
[ ] Product Attributes

## Basic Workflow

The importer has a component based architecture and provides a plug-in mechanism to add new functionality.
Each component is provided by it's own composer library whereas the libraries will have dependencies to each
other, at least to this library.

Each component **COULD** provide a subject that'll be executed synchronously by the command. When a 
subject is executed, it's `import()` method will be invoked and a uniuque ID for the actualy import process, 
as well as the name of the file that has to be imported, will be passed. The save memory, the `import()` 
method opens a stream to parse the passed file line by line. For each line found, the `importRow()` method 
will be invoked, that has exactly one parameter, which is the actual row, that has to be processed.

As described above, a subject can implement the import functionality, it is responsible for, by itself. A 
better and more generic solution are observers, that can be registered in the configuration file. A subject 
can have one or more observers, that will be invoked, by the subject's `importRow()` method. This means, for
each row in the CSV file, all registered observers are executed synchronously.