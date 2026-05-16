# AGENTS.md - import

## Zweck & Verantwortung

Das `import` Modul ist das **Kern-Framework** des Pacemaker Import-Systems. Es ist ein **Tier 3 Modul** und integriert alle Infrastructure-Tiers (0-2) mit Core-Logik.

**Hauptverantwortung:**
- Zentrale Orchestrierung von Import-Operationen
- Observer Pattern für Row-Level Processing
- Repository Pattern für Daten-Persistierung
- Service Layer für Business Logic
- Event-Driven Architektur für Hooks
- 30+ Listeners und 30+ Observers

## Architektur & Design Patterns

### Kern-Klassen
- **ConfigurationManagerInterface**: Zentrale Konfiguration
- **ImportProcessor**: Haupt-Orchestrator für Imports
- **RowTrait**: Gemeinsame Row-Logik
- **SystemLoggerTrait**: Gemeinsames Logging

### Observers (30+)
- **DynamicAttributeLoader**: Lädt Attribute dynamisch
- **GenericColumnCollectorObserver**: Sammelt Spalten
- **AbstractObserver**: Basis-Klasse für alle Observers
- Spezialisierte Observers für verschiedene Entity-Typen

### Listeners (30+)
- **RenderOperationReportListener**: Rendering von Reports
- **ValidateHeaderRowListener**: Validierung von Header-Zeilen
- **ImportHistoryListener**: Tracking von Import-History
- **CacheUrlRewriteListener**: Caching von URL-Rewrites
- **ArchiveListener**: Archivierung von Import-Dateien

### Verwendete Patterns
- **Observer Pattern**: Für Row-Level Processing
- **Repository Pattern**: Für Daten-Persistierung
- **Service Layer**: Für Business Logic
- **Event-Driven**: Für Hooks und Extensibility
- **Factory Pattern**: Für Object-Erstellung

### Externe Dependencies
- **psr/log** - PSR-3 Logging
- **psr/cache** - PSR-6 Caching
- **psr/container** - PSR-11 DI Container
- **monolog/monolog** - Structured Logging
- **league/event** - Event-System
- **ramsey/uuid** - UUID-Generierung
- **symfony/mailer** - Email-Versand
- **laminas/laminas-filter** - Data Filtering
- **handcraftedinthealps/goodby-csv** - CSV-Parsing
- **ext-json** - JSON-Support
- **ext-zip** - ZIP-Support

## Abhängigkeiten

### Externe Pakete
- **psr/log**, **psr/cache**, **psr/container** - PSR Standards
- **monolog/monolog** - Logging
- **league/event** - Events
- **ramsey/uuid** - UUIDs
- **symfony/mailer** - Email
- **laminas/laminas-filter** - Filtering
- **handcraftedinthealps/goodby-csv** - CSV
- **ext-json**, **ext-zip** - PHP Extensions

### TechDivision Dependencies
- **import-dbal** ^2.0 - DBAL-Interfaces
- **import-dbal-collection** ^2.1 - DBAL-Implementierung
- **import-cache** ^2.0 - Cache-Interfaces
- **import-cache-collection** ^2.0 - Cache-Implementierung
- **import-serializer** ^2.1 - Serializer-Interfaces
- **import-serializer-csv** ^2.1 - CSV-Serializer
- **import-configuration** ^6.1 - Konfiguration-Interfaces

### Abhängig von diesem Modul (9 Reverse Dependencies)
1. **import-app-simple** - Simple Application
2. **import-attribute** - Attribute Importer
3. **import-category** - Category Importer
4. **import-customer** - Customer Importer
5. **import-product** - Product Importer
6. **import-converter** - Converter Framework
7. **import-ee** - EE Functionality
8. **import-configuration-jms** - JMS Configuration
9. **import-cli-simple** - Master CLI

## Wichtige Entry Points

### Haupt-Klassen
```php
// Configuration Manager
ConfigurationManagerInterface::getConfiguration(): ConfigurationInterface
ConfigurationManagerInterface::getOperation($name): OperationConfigurationInterface

// Import Processor
ImportProcessor::process($configuration): void
ImportProcessor::execute($operation): void

// Observer
AbstractObserver::handle($row): void
AbstractObserver::getSubject(): SubjectInterface

// Listener
ListenerInterface::handle(EventInterface $event): void
```

### Verwendungsbeispiel
```php
// In Importern
$processor = new ImportProcessor($configuration);
$processor->process($configuration);

// In Observers
class CustomObserver extends AbstractObserver {
    public function handle($row) {
        $subject = $this->getSubject();
        $subject->addRow($row);
    }
}
```

## Events & Extension Points

### Events
- **BeforeImportEvent**: Vor Import-Start
- **AfterImportEvent**: Nach Import-Ende
- **BeforeOperationEvent**: Vor Operation-Start
- **AfterOperationEvent**: Nach Operation-Ende
- **BeforeRowEvent**: Vor Row-Processing
- **AfterRowEvent**: Nach Row-Processing
- **ImportErrorEvent**: Bei Import-Fehler

### Observer-Hooks
- **BeforeCreate**: Vor Create-Operation
- **AfterCreate**: Nach Create-Operation
- **BeforeUpdate**: Vor Update-Operation
- **AfterUpdate**: Nach Update-Operation
- **BeforeDelete**: Vor Delete-Operation
- **AfterDelete**: Nach Delete-Operation

## Hints für KI-Agenten

### Wichtig zu verstehen
1. **Tier 3 Modul**: Zentral für alle Import-Operationen
2. **Observer Pattern**: Zentral für Row-Level Processing
3. **Event-Driven**: Für Extensibility und Hooks
4. **30+ Observers & Listeners**: Umfangreiche Hook-Punkte
5. **9 Dependents**: Basis für alle Entity-Importer

### Bei Änderungen
- **Breaking Changes**: Beachte alle 9 Dependents
- **Observer-Kompatibilität**: Neue Observers sollten optional sein
- **Event-Kompatibilität**: Neue Events sollten optional sein
- **Backward Compatibility**: Alte Imports sollten noch funktionieren

### Implementierungs-Hinweise
- Nutze Observer Pattern für Custom Processing
- Nutze Events für Hooks
- Beachte Observer-Reihenfolge
- Erwäge Listener-Prioritäten

## Bekannte Einschränkungen

- **Single-Threaded**: Nicht für Multi-Threaded Imports
- **Memory-Intensive**: Große Datenmengen können Memory-Probleme verursachen
- **Keine Transaktionen**: Transaktions-Handling erfolgt in Implementierungen
- **Keine Rollback**: Fehler können zu Daten-Inkonsistenzen führen

## Zusammenfassung

`import` ist das **Kern-Framework** des Pacemaker-Systems. Es bietet die zentrale Orchestrierung, Observer Pattern für Row-Level Processing, und Event-Driven Architektur für Extensibility. Es ist die Basis für alle Entity-Importer.

**Für Agenten:** Verstehe dieses Modul als **Kern-Framework** mit Observer Pattern, Repository Pattern, Service Layer, und Event-Driven Architektur.
