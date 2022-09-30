<?php
namespace MrEssex\CubexCli\Generators;

class ConsoleMakeCommand extends GeneratorCommand
{
  /**
   * return the name of the command
   *
   * @return string
   */
  public function getName(): string
  {
    return 'make:console';
  }

  /**
   * Get the stub file for the generator
   *
   * @return string
   */
  protected function _getStub(): string
  {
    return __DIR__ . '/stubs/console.stub';
  }
}
