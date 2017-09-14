<?php
namespace AdrianTilita\ResourceExposer\Bridge;

use AdrianTilita\ResourceExposer\Provider\ApplicationServiceProvider;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\ParameterBag;

class ConfigBridge
{
    /**
     * @cons string Available usable config keys
     */
    const CONFIG_KEY_TRANSFORMERS = 'transformers';
    const CONFIG_KEY_AUTHORIZATION = 'authorization';

    /**
     * @var ParameterBag
     */
    private $configParameterBag;

    /**
     * @var ConfigBridge
     */
    private static $instance;

    /**
     * @return ConfigBridge
     */
    public static function getInstance(): ConfigBridge
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * ConfigBridge constructor.
     */
    private function __construct()
    {
        $configHandler = Config::class;
        $baseConfig = [];
        if ($configHandler::has(ApplicationServiceProvider::APPLICATION_IDENTIFIER)) {
            $baseConfig = $configHandler::get(ApplicationServiceProvider::APPLICATION_IDENTIFIER);
        }
        $this->configParameterBag = $this->normalizeConfig($baseConfig);
    }

    /**
     * @param array $configData
     * @return ParameterBag
     */
    private function normalizeConfig(array $configData): ParameterBag
    {
        if (!isset($configData[static::CONFIG_KEY_TRANSFORMERS])) {
            $configData[static::CONFIG_KEY_TRANSFORMERS] = [];
        }
        if (!isset($configData[static::CONFIG_KEY_AUTHORIZATION])) {
            $configData[static::CONFIG_KEY_AUTHORIZATION] = [
                'username' => 'foo',
                'password' => 'bar'
            ];
        }
        return new ParameterBag($configData);
    }

    /**
     * Return values for a config item
     * @param string $field
     * @return mixed
     */
    public function get(string $field)
    {
        return $this->configParameterBag->get($field);
    }

    /**
     * Has item for config
     * @param string $field
     * @return bool
     */
    public function has(string $field): bool
    {
        return $this->configParameterBag->has($field);
    }
}
