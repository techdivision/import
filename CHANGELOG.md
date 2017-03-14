# Version 1.0.0-alpha43

## Bugfixes

* None

## Features

* Add interfaces for logger configuration

# Version 1.0.0-alpha42

## Bugfixes

* Fixed access to not existent status key in SubjectPlugin class

## Features

* Add getRegistryProcessor() method to AbstractObserver class

# Version 1.0.0-alpha41

## Bugfixes

* None

## Features

* Add stop() and isStopped() methods to ApplicationInterface
* Stop processing when no CSV files that have to be imported are found

# Version 1.0.0-alpha40

## Bugfixes

* None

## Features

* Move UrlRewriteRepository to this library

# Version 1.0.0-alpha39

## Bugfixes

* None

## Features

* Added configuration key file specific to the file upload

# Version 1.0.0-alpha38

## Bugfixes

* None

## Features

* Add functionality to load values from Magento 2 core_config_data table

# Version 1.0.0-alpha37

## Bugfixes

* Fixed invalid return of entity_id instead of url_rewrite_id in UrlRewriteUpdateProcessor

## Features

* None

# Version 1.0.0-alpha36

## Bugfixes

* None

## Features

* Add URL path to pre-loaded categories

# Version 1.0.0-alpha35

## Bugfixes

* None

## Features

* Switch log level to debug instead of warning, when a column can not be mapped to a attribute directly

# Version 1.0.0-alpha34

## Bugfixes

* None

## Features

* Move transaction handling from SubjectPlugin::process() to Simple::process() method

# Version 1.0.0-alpha33

## Bugfixes

* None

## Features

* Refactor filesystem/file upload functionality
* Refactor EAV attribute handling
* Move EE utilities to techdivision/import-ee library
* Extract attribute import functionality from AbstractAttrbiuteObserver to AttributeObserverTrait

# Version 1.0.0-alpha32

## Bugfixes

* None

## Features

* Add UrlKeyFilterTrait that provides string to URL key convertion functionality

# Version 1.0.0-alpha31

## Bugfixes

* None

## Features

* Move generic UrlRewrite actions/processor to this library

# Version 1.0.0-alpha30

## Bugfixes

* None

## Features

* Refactoring for optimisation for category import functionality

# Version 1.0.0-alpha29

## Bugfixes

* None

## Features

* Add PHPUnit test für SubjectPlugin class

# Version 1.0.0-alpha28

## Bugfixes

* None

## Features

* Refactoring for new plugin functionality

# Version 1.0.0-alpha27

## Bugfixes

* None

## Features

* Add new configuration options for PID filename + if an OK file is needed

# Version 1.0.0-alpha26

## Bugfixes

* None

## Features

* Add missing methods addDatabase() and clearDatabases() to ConfigurationInterface
* Replace parameter ID from getDatabase() method with value from getUseDbId() method in ConfigurationInterface

# Version 1.0.0-alpha25

## Bugfixes

* None

## Features

* Extend configuration to implement multiple database handling

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
