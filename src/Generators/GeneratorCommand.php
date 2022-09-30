<?php
namespace MrEssex\CubexCli\Generators;

use JsonException;
use MrEssex\CubexCli\ConsoleCommand;
use MrEssex\CubexCli\NamespaceParser;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class GeneratorCommand extends ConsoleCommand
{
  protected string $type = "Console command";

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   * @param string          $name
   *
   * @return void
   * @throws JsonException
   */
  protected function executeCommand(InputInterface $input, OutputInterface $output, string $name): void
  {
    $name = $this->_qualifyClassName($name);
    $path = $this->_getPath($name);

    $this->_makeDirectory($path);

    if($this->_buildClass($name))
    {
      file_put_contents($path, $this->_buildClass($name));
    }

    $info = $this->type;

    $output->writeln($info . '  created successfully');
  }

  /**
   * Parse the class name and format according to the root namespace.
   *
   * @param string $name
   *
   * @return string
   * @throws JsonException
   */
  protected function _qualifyClassName(string $name): string
  {
    $name = ltrim($name, '\\/');
    $name = str_replace('/', '\\', $name);

    $rootNamespace = NamespaceParser::rootNamespace($this->getContext()->getProjectRoot());

    if(str_starts_with($name, $rootNamespace))
    {
      return $name;
    }

    return $this->_qualifyClassName($this->getDefaultNamespace(trim($rootNamespace, '\\')) . '\\' . $name);
  }

  /**
   * Get the default namespace for the class.
   *
   * @param string $rootNamespace
   *
   * @return string
   */
  protected function getDefaultNamespace(string $rootNamespace): string
  {
    return $rootNamespace . '\Cli';
  }

  /**
   * Get the destination class path.
   *
   * @param string $name
   *
   * @return string
   * @throws JsonException
   */
  protected function _getPath(string $name): string
  {
    $name = str_replace(NamespaceParser::rootNamespace($this->getContext()->getProjectRoot()), '', $name);
    return $this->getContext()->getProjectRoot() . '/src/' . str_replace('\\', '/', $name) . '.php';
  }

  /**
   * Build the class based on the passed in stub
   *
   * @param string $name
   *
   * @return string|null
   */
  protected function _buildClass(string $name): ?string
  {
    $stub = file_get_contents($this->_getStub());

    if(!$stub)
    {
      return null;
    }

    return $this->_replaceNamespace($stub, $name)->_replaceClass($stub, $name);
  }

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  abstract protected function _getStub(): string;

  /**
   * Replace the namespace for the given stub.
   *
   * @param string $stub
   * @param string $name
   *
   * @return $this
   */
  protected function _replaceNamespace(string &$stub, string $name): GeneratorCommand
  {
    $stub = str_replace(['DummyNamespace'], [$this->_getNamespace($name)], $stub);
    return $this;
  }

  /**
   * Get the full namespace for a given class, without the class name.
   *
   * @param string $name
   *
   * @return string
   */
  protected function _getNamespace(string $name): string
  {
    return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
  }

  /**
   * Replace the class name for the given stub.
   *
   * @param string $stub
   * @param string $name
   *
   * @return string
   */
  protected function _replaceClass(string $stub, string $name): string
  {
    $class = str_replace($this->_getNamespace($name) . '\\', '', $name);
    return str_replace(['DummyClass'], $class, $stub);
  }

  /**
   * Build the directory for the class if necessary.
   *
   * @param string $path
   *
   * @return string
   */
  protected function _makeDirectory(string $path): string
  {
    if(!is_dir(dirname($path)))
    {
      return mkdir(dirname($path), 0777, true);
    }

    return $path;
  }
}
