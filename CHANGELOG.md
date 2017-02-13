# Version 1.0.0-alpha24

## Bugfixes

* None

## Features

* Move BunchKeys from CLI package to this package
* Add SubjectInterface::needsOkFile() method to make sure, that subjects check if an OK file is needed or not

# Version 1.0.0-alpha23

## Bugfixes

* None

## Features

* Add method EavAttributeRepository::findAllByIsUserDefined() to load the user defined attributes
* Move CallbackVisitor and ObserverVisitor to this package and initialize observers/callbacks in AbstractSubject

# Version 1.0.0-alpha22

## Bugfixes

* None

## Features

* Apply header mappings only once per bunch, when headers are initialized
* Add attribute code as key to array with EAV attributes

# Version 1.0.0-alpha21

## Bugfixes

* None

## Features

* Separating artefact export functionality into an interface/trait

# Version 1.0.0-alpha20

## Bugfixes

* None

## Features

* Documentation tasks for class EntityStatus

# Version 1.0.0-alpha19

## Bugfixes

* None

## Features

* Add basic functionality to skip rows, e. g. if product has already been imported

# Version 1.0.0-alpha18

## Bugfixes

* None

## Features

* Re-throw execption instead of wrapping them

# Version 1.0.0-alpha17

## Bugfixes

* None

## Features

* Add debug-mode and log-level configuration options

# Version 1.0.0-alpha16

## Bugfixes

* Add debug log message with actual line nr/file name information

## Features

* None

# Version 1.0.0-alpha15

## Bugfixes

* Add line number to exception message for better debugging possibilities

## Features

* None

# Version 1.0.0-alpha14

## Bugfixes

* Add prefix check for MoveFilesSubject to avoid moving not matching files

## Features

* None

# Version 1.0.0-alpha13

## Bugfixes

* Fixed invalid usage of `continue` in AbstractSubject::import() method

## Features

* None

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