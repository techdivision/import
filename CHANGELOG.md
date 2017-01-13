# Version 1.0.0-alpha12

## Bugfixes

* None

## Features

* Move flag-file handling from techdivision/import-cli-simple to AbstractSubject::import() method
* Add MoveFilesSubject to move files from source-dir to target-dir before importing them

# Version 1.0.0-alpha11

## Bugfixes

* None

## Features

* Moving source/target-dir, as well as other configuration options from subject to global configuration

# Version 1.0.0-alpha10

## Bugfixes

* None

## Features

* Execute tearDown() method when AbstractSubject::import() method fails

# Version 1.0.0-alpha9

## Bugfixes

* None

## Features

* Extend functionality to handle file headers

# Version 1.0.0-alpha8

## Bugfixes

* None

## Features

* Add functionality to load product link attributes and add them to the global status

# Version 1.0.0-alpha7

## Bugfixes

* None

## Features

* Add prepareRow() method to AbstractBaseProcessor
* Add functionality to load Magento 2 configuration data
* Add persist() method to ActionInterface + AbstractAction

# Version 1.0.0-alpha6

## Bugfixes

* None

## Features

* Delete not implemented batch processor functionality
* Add AbstractUpdateProcessor to support update functionality
* Rename PersistProcessor and RemoveProcessor => CreateProcessor and DeleteProcessor
* Rename AbstractAction and ActionInterface persist() and remove() methods => create() and delete()

# Version 1.0.0-alpha5

## Bugfixes

* Fixed some Scrutinizer CI mess detector bugs

## Features

* None

# Version 1.0.0-alpha4

## Bugfixes

* Fixed some PHP 7 warnings/notices

## Features

* Add functionality to load default store on subject start-up

# Version 1.0.0-alpha3

## Bugfixes

* None

## Features

* Extend configuration interfaces to handle new operation functionality

# Version 1.0.0-alpha2

## Bugfixes

* None

## Features

* Refactoring to allow multiple prepared statements per CRUD processor instance
* Load root categories on start-up

# Version 1.0.0-alpha1

## Bugfixes

* None

## Features

* Refactoring + Documentation to prepare for Github release