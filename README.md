# M2IF - Magento 2 Import Framework

[![Latest Stable Version](https://img.shields.io/packagist/v/techdivision/import.svg?style=flat-square)](https://packagist.org/packages/techdivision/import) 
 [![Total Downloads](https://img.shields.io/packagist/dt/techdivision/import.svg?style=flat-square)](https://packagist.org/packages/techdivision/import)
 [![License](https://img.shields.io/packagist/l/techdivision/import.svg?style=flat-square)](https://packagist.org/packages/techdivision/import)
 [![Build Status](https://img.shields.io/travis/techdivision/import/master.svg?style=flat-square)](http://travis-ci.org/techdivision/import)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/techdivision/import/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import/?branch=master) [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/techdivision/import/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import/?branch=master)

This is the core library for the Magento 2 Import Framework M2IF.

The objective of M2IF is to provide a fully functional replacement for the Magento 2 standard import functionality
with a 100 % CSV file compatibility. In contrast to other approaches, the framework is not build as a Magento 2
extension, although it has no dependencies to other frameworks like Symfony, Laravel or others. Instead, it 
provides independent components, that can be tied together as needed, by adding them as composer dependency.

A simple command line implementation should give a brief overiew of how a simple import application could look
like. For more information, have a look at the [M2IF - Simple Console Tool](https://github.com/techdivision/import-cli-simple) 
repository

## Performance & Memory Comparison

Import performance is one of the main topics, as in many, especially the bigger projects with more than 100.000
products, the necessary time to keep the catalog up to date, may always be a bottleneck or an issue.

This performance comparison below, will actually **NOT** be a the real source of truth, as M2IF doesn't yet provide 
the complete functionality the Magento 2 standard import does. But as M2IF meanwhile will provide the most common 
features, it would give you a nice impression about what will be possible.

### Preparation

For the comparison, we used a standard Magento 2 CE v2.1.2 with sample data. As M2IF actually doesn't support
Downloadable und Grouped Products, we removed these from the CSV file(s). This finally results in

* 23 custom attributes
* 1 bundle product
* 147 configurable products
* 1.891 simple products

that we exported with the standard CSV export and created 4 bunches with ~500 products. To start the import process
itself we used the [M2IF - Simple Console Tool](https://github.com/techdivision/import-cli-simple). 

To execute the Magento 2 standard import we used a simple M2 extension that extends the Magento 2 command line tool. 
The extension is provided by CedricBlondeau and can be found on [Github](https://github.com/cedricblondeau/magento2-module-catalog-import-command).

Before we start the import (both the Magento 2 + M2IF), we removed the already imported image files, as importing
the same images again and again will lower performance.

### Results

On a MacBook Pro (Retina, Mid 2012) with the following configuration

* Intel Core i7 2.3 GHz
* 8 GB RAM
* 256 GB HDD

using PHP 5.6 + MySQL 5.6.34 we actually achive these results

| Operation            | M2 Standard  |       M2IF |    Improvement |
|:---------------------|:-------------|:-----------|:---------------|
| replace              |        113 s |       32 s |       ~ x 3.50 |
| add-update           |        114 s |       49 s |       ~ x 2.30 |
| delete               |         10 s |       10 s |       ~ x 0.00 |

Beside Performance, the Memory Usage will also be a topic in some cases. As well as the Performance
topic, Memory Usage will be relevant in projects with more than 100.000 products.

When importing the sample data, as described under [Performance](#performance-memory-comparison), M2IF has a memory peak of 38.1 MB
in contrast to Magento 2 standard import with 149.4 MB. For M2IF, it doesn't matter how big the CSV will be.

> As already mentioned, please keep in mind, this comparison lacks of some functionality the Magento 2 standard 
> import provides and and exectutes when running it. Especially the data validation will took some time and M2IF 
> acutally lacks of any validation functionality!

## Status

Actually we've a pre-alpha status, so we **STRONGLY** recommend not to use M2IF in production now.

The following functionality is already available, for CE and EE

- [x] Delete, Add/Update + Replace Import Mode
- [x] Categories
- [x] Simple Products
- [x] Configurable Products
- [x] Bundle Products
- [x] Product Relations
- [x] Media Gallery
- [x] Inventory
- [x] Relation with existing Categories
- [x] Relation with existing Websites
- [x] Url Rewrites
- [x] Extended Logging
- [x] Fine Grained Error Handling

This and many more is, what we're actually working on

- [ ] Downloadable Products
- [ ] Grouped Products
- [ ] Customizable Options
- [ ] Product Reviews
- [ ] Valididation
- [ ] Import Scheduled Product Updates
- [ ] RESTFul Webservice
- [ ] Archiving (equivalent to Magento Standard Functionality)
- [ ] History (Append to Standard Magento Import History)
- [ ] Seamless Magento 2 Backend Integration
- [ ] Tier Prices

Planned with future versions, are importer types for

- [ ] Product Attributes
- [ ] Customers
- [ ] Advanced Pricing

And finally the project will provide exporting functionality, but that has to be discussed.

## Related Libraries

As this is the main library that provides Magento 2 import core functionality, the specific
functionality to import products, category etc. is part of additional libraries.

### Applications

Applications are importer implementations that uses the M2IF to make the import functionality
available, e. g. on commanline.

* [import-cli-simple](https://github.com/techdivision/import-cli-simple) - A simple console implementation that uses M2IF to provide Magento 2 CE/EE import functionality

### Core Libraries CE

These are the M2IF core libraries for the Magento 2 Communit Edition (CE).

* [import-product](https://github.com/techdivision/import-product) - Provides product import functionality
* [import-product-bundle](https://github.com/techdivision/import-product-bundle) - Provides bundle product import functionality
* [import-product-link](https://github.com/techdivision/import-product-link) - Provides product relation import functionality
* [import-product-media](https://github.com/techdivision/import-product-media) - Provides product image import functionality
* [import-product-variant](https://github.com/techdivision/import-product-variant) - Provides configurable product import functionality
* [import-category](https://github.com/techdivision/import-category) - Provides category import functionality

### Core Libraries EE

These are the M2IF core libraries for the Magento 2 Communit Edition (EE).

* [import-ee](https://github.com/techdivision/import-ee) - Provides core import functionality for Magento 2 EE
* [import-product-ee](https://github.com/techdivision/import-product-ee) - Provides product import functionality for Magento 2 EE
* [import-product-bundle-ee](https://github.com/techdivision/import-product-bundle-ee) - Provides bundle product import functionality for Magento 2 EE
* [import-product-link-ee](https://github.com/techdivision/import-product-link-ee) - Provides product import relation functionality for Magento 2 EE
* [import-product-media-ee](https://github.com/techdivision/import-product-media-ee) - Provides product import image functionality for Magento 2 EE
* [import-product-variant-ee](https://github.com/techdivision/import-product-variant-ee) - Provides configurable product import functionality for Magento 2 EE
* [import-category-ee](https://github.com/techdivision/import-category-ee) - Provides category import functionality for Magento 2 EE

### Libraries for 3rd Party Extensions CE/EE

Finally we plan to support as many 3rd party extensions as possible.

* [import-product-magic360](https://github.com/techdivision/import-cli-simple) - Provides import functionality for the [Magictoolbox Magic360 Extension](https://www.magictoolbox.com/magic360/)

## Basic Workflow

The importer has a component based architecture and provides a plug-in mechanism to add new functionality.
Each component is provided by it's own composer library, whereas the libraries will have dependencies to each
other, at least to this library.

Each component **COULD** provide a subject that'll be executed synchronously by the command. When a 
subject is executed, it's `import()` method will be invoked and a unique ID for the actual import process, 
as well as the name of the file that has to be imported, will be passed. To save memory, the `import()` 
method opens a stream to parse the passed file line by line. For each line found, the `importRow()` method 
will be invoked, that has exactly one parameter, which is the actual row, that has to be processed.

As described above, a subject can implement the import functionality, it is responsible for, by itself. A 
better and more generic solution are observers, that can be registered e. g. in a configuration file. A 
subject can have one or more observers, that will be invoked, by the subject's `importRow()` method. This 
means, for each row in the CSV file, all registered observers are executed synchronously.

## Standard Plugins

The standard plugins are part of the M2IF core and can be used out-of-the box.

### Global Data

Load's the global data, necessary for the import process from the database and add's it to the registry,
so that every plugin can access it.

The configuration has to be like

```json
{
  "class-name": "TechDivision\\Import\\Plugins\\GlobalsPlugin"
}

### Subject

This the plugin that does the main work by invoking the subjects as well as their registered observers and
callbacks.

The plugin configuration is

```json
{
  "class-name": "TechDivision\\Import\\Plugins\\SubjectPlugin",
  "subjects": [ ... ]
}
```

### Archive

The archive plugin zip's the import artefacts and moves them to the configured archive folder.

```json
{
  "class-name": "TechDivision\\Import\\Plugins\\ArchivePlugin"
}
```

### Missing Option Values

This plugin provides the extended functionality to track whether an attribute option value, referenced 
in a CSV import file, is available or not, depending on `debug mode` enabled or not. If the `debug mode` 
is **NOT** enabled, an exception will be thrown immediately, else each missing attribute option value 
will be written to the CSV file `missing-option-values.csv` that'll stored in the temporary import 
directory and optionally sent to the specified mail receipients.

The configuration of the plugin can look like

```json
{
  "class-name": "TechDivision\\Import\\Plugins\\MissingOptionValuesPlugin",
  "swift-mailer" : {
    "factory" : "TechDivision\\Import\\Utils\\SwiftMailer\\SmtpTransportMailerFactory",
    "mailer-factory" : "\\Swift_Mailer",
    "params" : [
      {
        "to" : "info@my-domain.tld",
        "from" : "info@my-domain.tld",
        "subject": "Something Went Wrong",
        "content-type" : "text/plain"
      }
    ],
    "transport" : {
      "transport-factory" : "\\Swift_SmtpTransport",
      "params" : [
        {
          "smtp-host" : "my-domain.tld",
          "smtp-port" : 25,
          "smtp-security" : "tls",
          "smtp-auth-mode" : "LOGIN",
          "smtp-username" : "your-username",
          "smtp-password" : "your-password"
        }
      ]
    }
  }
}
```

whereas the `swift-mailer` configuration node is optionally. Only if the configuration for the Swift Mailer
is available, the CSV file will be send to the specified recipients.