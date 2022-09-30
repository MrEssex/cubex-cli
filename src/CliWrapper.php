<?php
namespace MrEssex\CubexCli;

use Cubex\Cubex;
use Exception;
use JsonException;
use MrEssex\CubexCli\Cli\Init;
use MrEssex\CubexCli\Generators\ConsoleMakeCommand;

class CliWrapper
{
  /**
   * Wrap the console within an app with our default commands, and load from the
   * default cli namespace
   *
   * @param Cubex $app
   *
   * @throws Exception
   */
  public static function initialise(Cubex $app): void
  {
    $ctx = $app->getContext();

    $app->getConsole()->add(ConsoleMakeCommand::withContext($ctx->getContext()));
    $app->getConsole()->add(Init::withContext($ctx->getContext()));

    $namespaces = NamespaceParser::getFilesInNamespace(
      $ctx->getProjectRoot(),
      NamespaceParser::rootNamespace($ctx->getProjectRoot()) . 'Cli'
    );

    if($namespaces)
    {
      /** @var ConsoleCommand $classes */
      foreach($namespaces as $classes)
      {
        $app->getConsole()->add($classes::withContext($ctx));
      }
    }
  }

  /**
   * Initiate CLI's from alternative projects within vendor
   *
   * @param Cubex  $app
   * @param string $namespace
   *
   * @throws JsonException
   * @throws Exception
   */
  public static function initialiseAlternativeRoot(Cubex $app, string $namespace): void
  {
    $ctx = $app->getContext();

    $projectRoot = $ctx->getProjectRoot() . '/vendor/' . $namespace;
    $namespaces = NamespaceParser::rootNamespace($projectRoot);

    $namespaces = NamespaceParser::getFilesInNamespace(
      $projectRoot,
      $namespaces . 'Cli'
    );

    if($namespaces)
    {
      /** @var ConsoleCommand $classes */
      foreach($namespaces as $classes)
      {
        if(method_exists($classes, 'withContext'))
        {
          $app->getConsole()->add($classes::withContext($ctx));
        } else {
          $app->getConsole()->add(new $classes);
        }
      }
    }
  }

}
