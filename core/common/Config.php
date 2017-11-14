<?php
/**
 * Phanbook : Delightfully simple forum software
 *
 * Licensed under The GNU License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link    http://phanbook.com Phanbook Project
 * @since   1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
namespace Phanbook\Common;

use LogicException;
use RuntimeException;
use Phalcon\Config as PhalconConfig;

/**
 * \Phanbook\Common\Config
 *
 * The application config.
 *
 * @package Phanbook\Common
 */
class Config extends PhalconConfig
{
    /**
     * Current Application stage.
     * @var string
     */
    protected $stage = ENV_LOCAL;

    /**
     * Config constructor.
     *
     * @param array|null $config The Application config.
     * @param string $stage The current Application stage.
     */
    public function __construct(array $config = null, $stage = ENV_LOCAL)
    {
        // @todo Validate stage
        $this->stage = $stage;

        parent::__construct($config);
    }

    /**
     * Load configuration from all files.
     *
     * @param string $stage
     * @return Config
     * @throws LogicException
     * @throws RuntimeException
     */
    public static function factory($stage = ENV_LOCAL)
    {
        $configPath = config_path('config.php');
        if (!file_exists($configPath) || !is_file($configPath)) {
            throw new RuntimeException(
                sprintf(
                    'The Application config not found. Please make sure that the file "%s" is present',
                    $configPath
                )
            );
        }

        /** @noinspection PhpIncludeInspection */
        $config = require $configPath;

        if (is_array($config)) {
            $config = new self($config, $stage);
        }

        if (!$config instanceof PhalconConfig) {
            throw new RuntimeException(
                sprintf(
                    'The Application config must be an instance of %s.',
                    PhalconConfig::class
                )
            );
        }

        if ($stage !== ENV_PRODUCTION) {
            if (file_exists(config_path('config.' . APPLICATION_ENV . '.php'))) {
                /** @noinspection PhpIncludeInspection */
                $local = require config_path('config.' . APPLICATION_ENV . '.php');

                if (is_array($local)) {
                    $local = new self($config, $stage);
                }

                if ($local instanceof PhalconConfig) {
                    $config->merge($local);
                }
            }
        }

        return $config;
    }
}
