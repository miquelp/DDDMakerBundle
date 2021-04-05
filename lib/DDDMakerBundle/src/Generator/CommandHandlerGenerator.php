<?php

namespace Mql21\DDDMakerBundle\Generator;

use Mql21\DDDMakerBundle\Exception\ElementAlreadyExistsException;
use Mql21\DDDMakerBundle\Factories\PathFactory;
use Mql21\DDDMakerBundle\Generator\Contract\DDDElementGenerator;

class CommandHandlerGenerator implements DDDElementGenerator
{
    public function generate(string $boundedContextName, string $moduleName, string $commandName): void
    {
    
        $commandHandlerSuffix = "CommandHandler";
        $commandHandlerClassName = "{$commandName}{$commandHandlerSuffix}";
        $commandHandlerFileName = "{$commandHandlerClassName}.php";
        $modulePath = PathFactory::forBoundedContextModules($boundedContextName, $moduleName);
        $commandHandlerFullPath = "{$modulePath}/Application/Command/{$commandHandlerFileName}";
        
        if (file_exists($commandHandlerFullPath)) {
            throw new ElementAlreadyExistsException("Command handler {$commandHandlerClassName} already exists in module \"{$moduleName}\" of bounded context \"{$boundedContextName}\".");
        }
    
        file_put_contents($commandHandlerFullPath, "<?php \n\nnamespace Test\Module;\n\nclass {$commandHandlerClassName}\n{\n}\n");
    }
    
}