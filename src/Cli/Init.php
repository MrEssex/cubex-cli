<?php

namespace MrEssex\CubexCli\Cli;

use MrEssex\CubexCli\ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends ConsoleCommand
{
  /**
   * return the name of the command
   *
   * @return string
   */
  public function getName(): string
  {
    return 'init';
  }

  /**
   * Copy across the cubex file as a default
   *
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function executeCommand(InputInterface $input, OutputInterface $output): void
  {
    $runningFrom = getcwd();

    $cubex = $runningFrom . '/cubex';

    if(!file_exists($cubex))
    {
      copy(dirname(__DIR__, 2) . '/cubex', $cubex);
    }
  }
}
