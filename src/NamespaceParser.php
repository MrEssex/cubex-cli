<?php
namespace MrEssex\CubexCli;

use JsonException;
use RuntimeException;

class NamespaceParser
{
  /**
   * Get the files from within a namespace
   *
   * @param $projectRoot
   * @param $namespace
   *
   * @return array|string[]
   * @throws JsonException
   */
  public static function getFilesInNamespace($projectRoot, $namespace): array
  {
    if(empty($namespace))
    {
      return [];
    }

    $namespaceDirectory = self::getNamespaceDirectory($projectRoot, $namespace);

    if(!$namespaceDirectory)
    {
      return [];
    }

    $files = scandir($namespaceDirectory);

    $classes = array_map(static function ($file) use ($namespace) {
      return $namespace . '\\' . str_replace('.php', '', $file);
    }, $files);

    return array_filter($classes, static function ($possibleClass) {
      return class_exists($possibleClass);
    });
  }

  /**
   * Get the directory based off of a namespace (must be psr-4 compliant)
   *
   * @param $projectRoot
   * @param $namespace
   *
   * @return false|string
   * @throws JsonException
   */
  protected static function getNamespaceDirectory($projectRoot, $namespace)
  {
    $composerNamespaces = self::getDefinedNamespaces($projectRoot);

    if(empty($composerNamespaces))
    {
      return false;
    }

    $namespaceFragments = explode('\\', $namespace);
    $undefinedNamespaceFragments = [];

    while($namespaceFragments)
    {
      $possibleNamespace = implode('\\', $namespaceFragments) . '\\';
      if(array_key_exists($possibleNamespace, $composerNamespaces))
      {
        return realpath(
          $projectRoot . '/' . $composerNamespaces[$possibleNamespace] . '/' . ltrim(
            implode('/', $undefinedNamespaceFragments),
            '/'
          )
        );
      }

      array_unshift($undefinedNamespaceFragments, array_pop($namespaceFragments));
    }

    return false;
  }

  /**
   * Get the namespace defined in the root composer.json
   *
   * @param $projectRoot
   *
   * @return array
   * @throws JsonException
   */
  protected static function getDefinedNamespaces($projectRoot): array
  {
    $composerJsonPath = $projectRoot . '/composer.json';
    $composerConfig = json_decode(file_get_contents($composerJsonPath), false, 512, JSON_THROW_ON_ERROR);

    return (array)$composerConfig->autoload->{'psr-4'};
  }

  /**
   * Get the application namespace.
   *
   * @param $projectRoot
   *
   * @return string
   * @throws JsonException
   */
  public static function rootNamespace($projectRoot): string
  {
    if(!$projectRoot)
    {
      return '';
    }

    foreach(self::getDefinedNamespaces($projectRoot) as $namespace => $path)
    {
      foreach((array)$path as $pathChoice)
      {
        if(realpath(self::_path($projectRoot)) === realpath(self::_basePath($projectRoot, $pathChoice)))
        {
          return $namespace;
        }
      }
    }

    throw new RuntimeException('Unable to detect application namespace.');
  }

  /**
   * Get the base path from the root
   *
   * @param null   $path
   * @param string $projectRoot
   *
   * @return string
   */
  protected static function _basePath(string $projectRoot, $path = null): string
  {
    $loc = $projectRoot;

    if($path !== null)
    {
      $loc .= DIRECTORY_SEPARATOR . $path;
    }

    return $loc;
  }

  /**
   * return the src directory
   *
   * @param $projectRoot
   *
   * @return string
   */
  protected static function _path($projectRoot): string
  {
    return self::_basePath($projectRoot, 'src');
  }

}
