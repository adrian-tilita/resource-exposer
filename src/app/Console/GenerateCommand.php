<?php
namespace AdrianTilita\ResourceExposer\Console;

use AdrianTilita\ResourceExposer\Service\ClassSearchService;
use AdrianTilita\ResourceExposer\Service\ModelListService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GenerateCommand
 * @package AdrianTilita\ResourceExposer\Console
 */
class GenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'exposer:setup';

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
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $io = new SymfonyStyle($this->input, $this->output);
        $io->note(
            sprintf(
                "Default search path for models will be <%s>.\n" .
                " To run search on different path view help information",
                realpath(app_path())
            )
        );

        $this->getModelListService()->search();
        $modelList = $this->getModelListService()->fetchAll();

        $io->success(
            sprintf(
                "Finished parsing for models in project!\nFound %d models in your project.\n\n%s",
                count($modelList),
                implode(", ", $modelList)
            ),
            "info"
        );

        return null;
    }

    /**
     * @return ModelListService
     */
    private function getModelListService(): ModelListService
    {
        if (is_null($this->modelListService)) {
            $this->modelListService = new ModelListService(
                new ClassSearchService(
                    app_path(),
                    Model::class,
                    '.php'
                )
            );
        }
        return $this->modelListService;
    }
}
