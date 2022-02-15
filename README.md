MaRDI/WikibaseImport
=====================
This is a fork of the original WikibaseImport that can be found at https://github.com/Wikidata/WikibaseImport
The default branch of this fork is master_mardi. Do not attempt to merge the master branch into master_mardi, 
as this will break the extension, since we are using an older version of Wikibase.

* It's forked from the last commit that was compatible with Wikibase 1.35 (which is what we're using now).
* It adds a command-line option to not recursively import all referenced entities, just the ones specified in the list

Example
-------
Import a specific property without importing any referenced items:
``
cd extensions/WikibaseImport/
php maintenance/importEntities.php --entity P31 --do-not-recurse
``

Import only properties and items specified in a file, and only those:
``
cd extensions/WikibaseImport/
php maintenance/importEntities.php --file import_list.txt --do-not-recurse
``
(the file should contain 1 item or property per line).

WikibaseImport
===============

[![Build Status](https://travis-ci.org/filbertkm/WikibaseImport.svg?branch=master)](https://travis-ci.org/filbertkm/WikibaseImport)

WikibaseImport is a MediaWiki extension that provides a maintenance script for importing entities from another Wikibase instance. (e.. Wikidata)

The script imports the specified entity or entities, including statements, sitelinks, labels, descriptions and aliases. The extension tracks the newly assigned entity id and the original id, so that any other entity that references can be linked appropriately.

The script also imports any referenced entities (e.g. properties, badge items, wikibase-item values) without the statements.

Install
------

Clone ```https://github.com/filbertkm/WikibaseImport.git``` to the extensions folder of your MediaWiki instance.

Then go into the WikibaseImport extension directory and run ```composer update```.

Then, to enable the extension, add it in your ```LocalSettings.php``` file:

```
wfLoadExtension( 'WikibaseImport' );
```

The extension requires a new database table to map entity ids from the foreign
wiki to corresponding ids in the local wiki.

To add the table, run MediaWiki's ```update.php``` maintenance script.

Usage
------
First, navigate to *WikibaseImport* ’s extension folder.

Import a specific entity:

```
php maintenance/importEntities.php --entity Q147
```

Import a list of entities from a text file:

```
php maintenance/importEntities.php --file presidents.csv
```

You need to create the csv with a list of entity ids. For example, get a list
of entity ids from a query (e.g. Wikidata sparql).

Import all properties:

```
php maintenance/importEntities.php --all-properties
```

Import Wikidata entities with specified property:entityId value pair:

```
php maintenance/importEntities.php --query P131:Q64
```

Import a range of entities:

```
php maintenance/importEntities.php --range Q1:Q20
```

Import a list of entities printed by another program:

```
printf 'Q%s\n' {1..20} {100..120} | php maintenance/importEntities.php --stdin
```
