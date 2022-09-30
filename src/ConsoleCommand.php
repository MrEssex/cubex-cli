<?php
namespace MrEssex\CubexCli;

use Packaged\Context\WithContext;
use Packaged\Context\WithContextTrait;

class ConsoleCommand extends \Cubex\Console\ConsoleCommand implements WithContext
{
  use WithContextTrait;
}
