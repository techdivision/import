# Version 14.0.4

## Bugfixes

* Bugfixing invalid delimiter parameter usage for ValueCsvSerializer::serialize() and ValueCsvSerializer::unserialize() methods

## Features

* None

# Version 14.0.3

## Bugfixes

* Bugfix for attribute import with empty option labels

## Features

* None

# Version 14.0.2

## Bugfixes

* Invoke flushCache() instead of invlidateTags() method when finally cleaning cache

## Features

* None

# Version 14.0.1

## Bugfixes

* Fix for invalid type casting e.g. for special_price

## Features

* None

# Version 14.0.0

## Bugfixes

* None

## Features

* Extend additional attribute serializer functionality
* Make SQL for loading EAV attribute option values case sensitive
* Extend observer and callback instanciation with the possibility to use a factory

# Version 13.0.1

## Bugfixes

* Fixed issue when formatting float/decimals on a localized system

## Features

* None

# Version 13.0.0

## Bugfixes

* Fixed issue when formatting decimals greater than 999

## Features

* Add Magento Edition + Version output to RenderAnsiArtListener implementation
* Extend NumberConverterInterface + DateConverterInterface as well as implementations

# Version 12.0.7

## Bugfixes

* None

## Features

* Make ZIP archive created by Archive flat (remove directory structure)

# Version 12.0.6

## Bugfixes

* Add missing command mapping for attribute set import

## Features

* None

# Version 12.0.5

## Bugfixes

* Remove unnecessary dependency to ramsey/uuid

## Features

* None

# Version 12.0.4

## Bugfixes

* Fixed issue with ignored --cache-enabled option when not cache configuration is available

## Features

* None

# Version 12.0.3

## Bugfixes

* Fixed issue in cache warmer functionality

## Features

* None

# Version 12.0.2

## Bugfixes

* Fixed issue in LocalCacheAdapter::invalidateTags() method that leads to inconsistent cache data

## Features

* None

# Version 12.0.1

## Bugfixes

* Add missing method MissingOptionValuesPlugin::isDebugMode()

## Features

* None

# Version 12.0.0

## Bugfixes

* Fixed issue with invalid return value of LocalCacheAdapter::isCached($key) method
* Fixed issue when renaming images with the same filename withing one import process

## Features

* Optimize SQL to load image types in the optimal order for further processing
* Impovements to significantly lower the memory footprint in production mode

# Version 11.1.0

## Bugfixes

* None

## Features

* Add LocalCacheAdapter implementation for maximum performance and declare it as the default one

# Version 11.0.0

## Bugfixes

* Fixed issue in SimpleFileResolver that causes an exception when old CSV files without a .ok file are available in the source directory

## Features

* Refactor cache integration to optimize in multiprocess and -threaed environments

# Version 10.0.1

## Bugfixes

* Fixed invalid cache initialization on missing default configuration

## Features

* None

# Version 10.0.0

## Bugfixes

* None

## Features

* Switch to http://www.php-cache.com as PSR-6 compliant cache implementation

# Version 9.0.0

## Bugfixes

* None

## Features

* Refactor Cache Integration for PSR-6 compliance
* Add additional events on plugin and subject level

# Version 8.0.0

## Bugfixes

* Fixed issue in CommandNames::isCommandName() method

## Features

* Add customer group repository functionality
* Add utility class with Magento edition names
* Add utility class to handle CE/EE primary key functionality
* Add commands for importing MSI inventory + product tier prices

# Version 7.0.1

## Bugfixes

* Fixed some PSR-2 errors

## Features

* None

# Version 7.0.0

## Bugfixes

* None

## Features

* Refactoring tasks to make implementation more generic

# Version 6.0.1

## Bugfixes

* Fix File permission for create folder

## Features

* None

# Version 6.0.0

## Bugfixes

* Add methods to load attribute option values by entity type ID to replace methods without entity type ID

## Features

* None

# Version 5.0.0

## Bugfixes

* None

## Features

* Add constants for customer + customer address import commands
* Refactoring AbstractAttributeTrait to support customer import functionality

# Version 4.0.0

## Bugfixes

* None

## Features

* Add Converter for numbers and date
* Move CSV configuration from subject to import/export adapter configuration
* Add Serializer implementation to serialize/unserialize values from import files
* Add FileResolver implementation to make configuration of import file + OK file handling more generic

# Version 3.0.0

## Bugfixes

* None

## Features

* Tap doctrine/dbal to version 2.5.x

# Version 2.0.1

## Bugfixes

* Add missing artefact initialization in ExportableTrait::newArtefact() method
* Fixed invalid .inProgress file deletion in AbstractSubject::import() method

## Features

* None

# Version 2.0.0

## Bugfixes

* Fixed issue that creates column original_data also if no original data is available

## Features

* Add configuration option create-imported-file to subject configuration
* Add getter SubjectConfigurationInterface::isCreatingImportedFile() method
* Add functionality to NOT create .imported flagfile based on configuration value to enable multiple subjects processing the same CSV file

# Version 1.0.0

## Bugfixes

* None

## Features

* Move PHPUnit test from tests to tests/unit folder for integration test compatibility reasons

# Version 1.0.0-beta65

## Bugfixes

* None

## Features

* Add missing interface for UrlRewriteAction

# Version 1.0.0-beta64

## Bugfixes

* None

## Features

* Add missing interfaces for actions, repositories and emitter factory
* Replace class type hints for ImportProcessor with interfaces

# Version 1.0.0-beta63

## Bugfixes

* None

## Features

* Add --single-transaction parameter to configuration
* Add additional event names

# Version 1.0.0-beta62

## Bugfixes

* None

## Features

* Add configurable events to AbstractSubject to allow simple extend of artefact and row handling

# Version 1.0.0-beta61

## Bugfixes

* None

## Features

* Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements

# Version 1.0.0-beta60

## Bugfixes

* None

## Features

* Refactor cache warmer functionality for optimized memory management

# Version 1.0.0-beta59

## Bugfixes

* None

## Features

* Add row counter the logs the number of processed rows per minute

# Version 1.0.0-beta58

## Bugfixes

* Update category path handling in order to use store view specific slugs

## Features

* None

# Version 1.0.0-beta57

## Bugfixes

* None

## Features

* Update processed file status in AbstractSubject

# Version 1.0.0-beta56

## Bugfixes

* None

## Features

* Make image types dynamic and extensible

# Version 1.0.0-beta55

## Bugfixes

* None

## Features

* Add file status to registry when invoking AbstractSubject::tearDown() method
* Set serial and filename in MoveFilesSubject::import() method

# Version 1.0.0-beta54

## Bugfixes

* None

## Features

* Add interfaces for observer and callback visitor implementations

# Version 1.0.0-beta53

## Bugfixes

* None

## Features

* Add new method EavAttributeRepository::findOneByEntityTypeIdAndAttributeCode() for loading attributes in integration tests

# Version 1.0.0-beta52

## Bugfixes

* None

## Features

* Add override parameter to method ExportableTrait::addArtefact() method
* Format exception message when the executionn of INSERT/UPDATE/DELETE statements fails

# Version 1.0.0-beta51

## Bugfixes

* None

## Features

* Wrap \PDOExceptions in AbstractBaseProcessor for more detailed database releated exceptions

# Version 1.0.0-beta50

## Bugfixes

* Columns with empty values, related to the inventory, doesn't overwrite already existing values

## Features

* None

# Version 1.0.0-beta49

## Bugfixes

* Fixed invalid interruption of observer chain when skipping row

## Features

* None

# Version 1.0.0-beta48

## Bugfixes

* None

## Features

* Add configuration key for clean-up URL rewrites

# Version 1.0.0-beta47

## Bugfixes

* None

## Features

* Refactor file upload functionality

# Version 1.0.0-beta46

## Bugfixes

* None

## Features

* Add functionality to delete entity attributes with empty values in column names

# Version 1.0.0-beta45

## Bugfixes

* None

## Features

* Optimize log output
* Add CacheWarmerPlugin
* Add interfaces for all repositories

# Version 1.0.0-beta44

## Bugfixes

* None

## Features

* Refactor artefact handling

# Version 1.0.0-beta43

## Bugfixes

* None

## Features

* Add functionality to persist stores, store groups and store websites (for usage with integration tests)

# Version 1.0.0-beta42

## Bugfixes

* Use admin store view as default when no store view code has been set in CSV row

## Features

* None

# Version 1.0.0-beta41

## Bugfixes

* None

## Features

* Refactoring for better URL rewrite + attribute handling

# Version 1.0.0-beta40

## Bugfixes

* None

## Features

* Refactored find() methods in UrlRewriteRepository
* Refactored SQL statements for URL rewrite handling in class SqlStatements

# Version 1.0.0-beta39

## Bugfixes

* Fixed issue, that RowTrait::hasValue() + RowTrait::getValue() methods doesn't returen 0 values

## Features

* Add AbstractBaseProcessor::hasPreparedStatement() method to query for statement that has already been prepared

# Version 1.0.0-beta38

## Bugfixes

* None

## Features

* Extends configuration with custom header mappings + image types

# Version 1.0.0-beta37

## Bugfixes

* None

## Features

* Extends subject configuration with custom header mappings + image types
* Initialize header mappings in AbstractSubject with values from subject configuration
* Add constants in CommandName and EntityTypeCode classes for product price + inventory import

# Version 1.0.0-beta36

## Bugfixes

* Fixed critical issue because of not mapped column names when try to load a column's value

## Features

* None

# Version 1.0.0-beta35

## Bugfixes

* None

## Features

* Add listContents() method to filesystem adapters

# Version 1.0.0-beta34

## Bugfixes

* None

## Features

* Refactoring for optimized filesystem handling

# Version 1.0.0-beta33

## Bugfixes

* None

## Features

* Add PHPUnit tests for SubjectPlugin

# Version 1.0.0-beta32

## Bugfixes

* None

## Features

* Replace array with system loggers with a collection

# Version 1.0.0-beta31

## Bugfixes

* None

## Features

* Make RowTrait::getValue() method public

# Version 1.0.0-beta30

## Bugfixes

* None

## Features

* Add PHPUnit tests for subjects, observers + plugins
* Add EntitySubjectInterface for entity related subjects

# Version 1.0.0-beta29

## Bugfixes

* Fix issue which caused ID sorted category paths

## Features

* None

# Version 1.0.0-beta28

## Bugfixes

* Don't throw exception when no user defined EAV attributes for a given entity type code are available

## Features

* None

# Version 1.0.0-beta27

## Bugfixes

* None

## Features

* Refactor to optimize DI integration

# Version 1.0.0-beta26

## Bugfixes

* Add missing psr/container library

## Features

* None

# Version 1.0.0-beta25

## Bugfixes

* Fix issue which caused multiple attribute option values for same option_id and store_id

## Features

* None

# Version 1.0.0-beta24

## Bugfixes

* None

## Features

* Add mapping for command import:create:configuration-file to entity type

# Version 1.0.0-beta23

## Bugfixes

* None

## Features

* Add command name to create a new configuration file

# Version 1.0.0-beta22

## Bugfixes

* None

## Features

* Add CSV import + export adapter
* Add plugin + subject factory

# Version 1.0.0-beta21

## Bugfixes

* Use str_getcsv for explode in AbstractSubject

## Features

* None
 
# Version 1.0.0-beta20

## Bugfixes

* None

## Features

* Add ConfigurationFactoryInterface for all configuration factories
* Add utility class CommandNames that contains all available commands
* Add utility class DependencyInjectionKeys containing basic DI keys
* Add utility class CommandNameToEntityTypeCode for mappings from command name to entity type code

# Version 1.0.0-beta19

## Bugfixes

* None

## Features

* Use Robo for Travis-CI build process 
* Add SqlStatementsInterface + ConnectionInterface to optimize DI
* Add and integrate SqlStatements class + PDOConnectionWrapper class 

# Version 1.0.0-beta18

## Bugfixes

* None

## Features

* Refactor ArchivePlugin to also support specify an absolut path for the archive directory

# Version 1.0.0-beta17

## Bugfixes

* None

## Features

* Add type hints to constructor of CategoryAssembler

# Version 1.0.0-beta16

## Bugfixes

* None

## Features

* Refactoring Symfony DI integration

# Version 1.0.0-beta15

## Bugfixes

* Replace method to load attribute option values

## Features

* None

# Version 1.0.0-beta14

## Bugfixes

* Fixed issue when additional attributes contain a comma (,)
* Fixed issue when store view option values are missing

## Features

* None

# Version 1.0.0-beta13

## Bugfixes

* None

## Features

* Optimize exception creation in AbstractEavSubject

# Version 1.0.0-beta12

## Bugfixes

* None

## Features

* Add countDatabases() method to ConfigurationInterface
* Remove getDefaultLibraries() method from ApplicationInterface

# Version 1.0.0-beta11

## Bugfixes

* None

## Features

* Add repository to load EAV entity attribute data
* Load EAV entity attribute data on system start up
* Add getArtefactsByTypeAndEntityId() method to ExportableTrait

# Version 1.0.0-beta10

## Bugfixes

* None

## Features

* Add getSystemName() method to ConfigurationInterface

# Version 1.0.0-beta9

## Bugfixes

* None

## Features

* SubjectConfigurationInterface, SubjectConfigurationInterface + SwiftMailerConfigurationInterface now extend ParamsConfigurationInterface

# Version 1.0.0-beta8

## Bugfixes

* None

## Features

* Add getMultipleValueDelimiter() + getMultipleFieldDelimiter() method to SubjectConfigurationInterface

# Version 1.0.0-beta7

## Bugfixes

* None

## Features

* Move getRowStoreId() method from AbstractEavSubject to AbstractSubjectMethod
* Move getRowStoreId() method from AbstractObserverTrait to AbstractObserver

# Version 1.0.0-beta6

## Bugfixes

* None

## Features

* Add getMultipleValueDelimiter() method to ConfigurationInterface/AbstractSubject/AbstractObserver

# Version 1.0.0-beta5

## Bugfixes

* Fixed typo on BackendTypeKeys

## Features

* None

# Version 1.0.0-beta4

## Bugfixes

* Fixed typo on BackendTypeKeys

## Features

* None

# Version 1.0.0-beta3

## Bugfixes

* None

## Features

* Add EAV attribute entity type
* Add utility class with available backend types
* Refactor exception wrapper to also wrap unexpected exceptions

# Version 1.0.0-beta2

## Bugfixes

* None

## Features

* Remove unncessary use statement

# Version 1.0.0-beta1

## Bugfixes

* None

## Features

* Integrate Symfony DI functionality

# Version 1.0.0-alpha51

## Bugfixes

* Fixed invalid OK file handling
* Moved generic exceptions from techdivision/import-cli-simple to this library

## Features

* None

# Version 1.0.0-alpha50

## Bugfixes

* Remove FilesytemTrait use statement from FileUploadTrait to avoid PHP 5.6 PHPUnit error

## Features

* None

# Version 1.0.0-alpha49

## Bugfixes

* None

## Features

* Make select, multiselect + boolean callbacks abstract
* Refactoring for DI integration

# Version 1.0.0-alpha48

## Bugfixes

* Fixed initialization issues for ImportProcessor and CoreConfigDataRepository

## Features

* None

# Version 1.0.0-alpha47

## Bugfixes

* None

## Features

* Add constructor injection to ImportProcessor and Repository instances

# Version 1.0.0-alpha46

## Bugfixes

* None

## Features

* Add new SendmailTransportMailerFactory for usage with SwiftMailer

# Version 1.0.0-alpha45

## Bugfixes

* None

## Features

* Add functionality to use SwiftMailer for logging and mail sending purposes
* Add plugin to create a CSV file with missing attribute option values

# Version 1.0.0-alpha44

## Bugfixes

* None

## Features

* Extend method getSystemLogger() with parameter name to load a specific logger

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
