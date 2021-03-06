<?php
namespace AdrianTilita\ResourceExposer\Console;

use AdrianTilita\ResourceExposer\Bridge\CacheBridge;
use AdrianTilita\ResourceExposer\Log\CliLog;
use AdrianTilita\ResourceExposer\Service\ModelListService;
use NeedleProject\Common\ClassFinder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GenerateCommand
 * @package AdrianTilita\ResourceExposer\Console
 */
class SearchModelsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'exposer:search-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search the entire project for defined models and cache the results';

    /**
     * @var null|ModelListService
     */
    private $modelListService = null;

    /**
     * @var null|ClassFinder
     */
    private $classSearchService = null;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {

        $symfonyStyleOutput = new SymfonyStyle($this->input, $this->output);
        $symfonyStyleOutput->note(
            sprintf(
                "Default search path for models will be <%s>.\n" .
                " To run search on different path view help information",
                realpath(app_path())
            )
        );

        // enable debug mode
        if ($this->output->isVerbose() || $this->output->isDebug()) {
            $this->getClassFinder()->setLogger(new CliLog($this->output));
        }

        $this->getModelListService()->search();
        $modelList = $this->getModelListService()->fetchAll();

        $symfonyStyleOutput->success(
            sprintf(
                "Finished parsing for models in project!\nFound %d models in your project.\n\n%s",
                count($modelList),
                implode(", ", $modelList)
            )
        );
        return null;
    }

    /**
     * @return ClassFinder
     */
    private function getClassFinder(): ClassFinder
    {
        if (is_null($this->classSearchService)) {
            $this->classSearchService = new ClassFinder(
                app_path(),
                Model::class
            );
        }
        return $this->classSearchService;
    }
    /**
     * @return ModelListService
     */
    private function getModelListService(): ModelListService
    {
        if (is_null($this->modelListService)) {
            $this->modelListService = new ModelListService(
                $this->getClassFinder(),
                new CacheBridge()
            );
        }
        return $this->modelListService;
    }
}
