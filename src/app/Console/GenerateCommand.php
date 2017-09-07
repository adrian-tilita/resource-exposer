<?php
namespace AdrianTilita\ResourceExposer\Console;

use AdrianTilita\ResourceExposer\Service\ClassSearchService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateCommand extends Command
{
    const STORE_KEY = 'resource_exposer';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'resource-exposer:search-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search the entire project for defined models and cache the results';

    /**
     * @var null
     */
    private $modelSearchService = null;
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

        $definedModels = $this->getFileSearchService()->findClasses();

        Cache::forever(static::STORE_KEY, $this->formatClassReferences($definedModels));

        $io->success(
            sprintf(
                "Finished parsing for models in project!\nFound %d models in your project.\n\n%s",
                count($definedModels),
                implode(", ", $definedModels)
            ),
            "info"
        );

        return null;
    }

    /**
     * @return ClassSearchService
     */
    private function getFileSearchService(): ClassSearchService
    {
        if (is_null($this->modelSearchService)) {
            $this->modelSearchService = new ClassSearchService(
                app_path(),
                Model::class,
                '.php'
            );
        }
        return $this->modelSearchService;
    }

    /**
     * Format stored models in [resource_name => resource_class] format
     * @param array $definedModels
     * @return array
     */
    private function formatClassReferences(array $definedModels): array
    {
        foreach ($definedModels as $key => $modelName) {
            $split = explode("\\", $modelName);
            $resourceName = strtolower(end($split));
            if (isset($definedModels[$resourceName])) {
                $resourceName = $resourceName . '_' . $this->generateRandomString();
            }
            $definedModels[$resourceName] = $modelName;
            unset($definedModels[$key]);
        }
        return $definedModels;
    }

    /**
     * @return string
     */
    private function generateRandomString(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}