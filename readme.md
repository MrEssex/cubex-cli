# Cubex Cli

## Installation

1. Run ```composer require MrEssex/Cubex-Cli```
2. Run ```./vendor/mressex/cubex-cli/cubex init```. This command creates a sample **cubex** file in your root directory.

## Usage

* Run ```cubex make:console command-name``` to create a new command.
* When you create a new command, it will have **{PSR-4-Namespace}\Cli** namespace. For example, if you
  run ```cubex make:console Example```, you will get ***{PSR-4-Namespace}\Cli\Example*** as a fully qualified class name.
* Run ```cubex list``` to confirm.

or add ```CliWrapper::initialise($app);```, where ```$app``` is an instance of cubex,
to your cubex file in the root directory

## Registering Alternative CLI Vendors

* Qdd ```CliWrapper::initialiseAlternativeRoot($app, {path});```, where ```$app``` is an instance of cubex,
  to your cubex file in the root directory and ```{path}``` is the namespace/path inside vendor of the package e.g. ```mressex/cubex-translate```
