<?php

namespace Mql21\DDDMakerBundle\Maker;

use Mql21\DDDMakerBundle\ConfigManager\ConfigManager;
use Mql21\DDDMakerBundle\Generator\Builder\DDDClassBuilder;
use Mql21\DDDMakerBundle\Generator\DTO\QueryResponseGenerator;
use Mql21\DDDMakerBundle\Locator\BoundedContextModuleLocator;
use Mql21\DDDMakerBundle\Interaction\DTOAttributeInteractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MakeQueryResponse extends Command
{
    protected static $defaultName = 'ddd:cqs:make:response';
    
    private QueryResponseGenerator $queryResponseGenerator;
    private BoundedContextModuleLocator $boundedContextModuleLocator;
    private DTOAttributeInteractor $attributeQuestioner;
    private ConfigManager $configManager;
    private DDDClassBuilder $classBuilder;
    
    public function __construct(
        BoundedContextModuleLocator $boundedContextModuleLocator,
        ConfigManager $configManager,
        DDDClassBuilder $classBuilder,
        DTOAttributeInteractor $dtoAttributeInteractor
    ) {
        $this->boundedContextModuleLocator = $boundedContextModuleLocator;
        $this->configManager = $configManager;
        $this->attributeQuestioner = $dtoAttributeInteractor;
        $this->classBuilder = $classBuilder;
        
        parent::__construct(self::$defaultName);
    }
    
    protected function configure()
    {
        $this
            ->setDescription('Creates a query response in the Application layer.')
            ->addArgument(
                'boundedContext',
                InputArgument::REQUIRED,
                'The name of the bounded context where Command will be saved into.'
            )
            ->addArgument(
                'module',
                InputArgument::REQUIRED,
                'The name of the module inside the bounded context where Command will be saved into.'
            );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $boundedContextName = $input->getArgument('boundedContext');
        $moduleName = $input->getArgument('module');
        
        $this->boundedContextModuleLocator->checkIfBoundedContextModuleExists($boundedContextName, $moduleName);
        
        $responseNameQuestion = new Question("<info> What should the response be called?</info>\n > ");
        $questionHelper = $this->getHelper('question');
        $responseName = $questionHelper->ask($input, $output, $responseNameQuestion);
        
        $this->queryResponseGenerator = new QueryResponseGenerator(
            $this->attributeQuestioner->ask($input, $output, $questionHelper),
            $this->configManager,
            $this->classBuilder
        );
        $this->queryResponseGenerator->generate($boundedContextName, $moduleName, $responseName);
        
        $output->writeln("<info> Response {$responseName} has been successfully created! </info>\n\n");
        
        return Command::SUCCESS;
    }
}