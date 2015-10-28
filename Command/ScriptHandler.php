<?php

namespace Hslavich\SimplesamlphpBundle\Command;

use Composer\Script\CommandEvent;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as SymfonyScriptHandler;

class ScriptHandler extends SymfonyScriptHandler
{
    public static function copySimplesamlphpConfig(CommandEvent $event)
    {
        $options = self::getOptions($event);

        static::executeCommand($event, $options['symfony-bin-dir'], 'simplesamlphp:config');
    }
}
