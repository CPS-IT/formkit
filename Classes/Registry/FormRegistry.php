<?php

namespace Cpsit\Formkit\Registry;

use Cpsit\Formkit\Exception\InvalidFormIdException;
use JsonException;
use TYPO3\CMS\Core\Cache\Backend\AbstractBackend;
use TYPO3\CMS\Core\Cache\Backend\RedisBackend;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2024 Dirk Wenzel <wenzel@cps-it.de>
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the text file GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
class FormRegistry
{
    public const CACHE_KEY_FORMKIT_DEFINITIONS = 'formkit_definitions';
    public const CACHE_KEY_FORMKIT_FORMS = 'formkit_forms';
    public const KEY_FILE = 'file';
    public const KEY_SOURCE = 'source';

    public const DEFAULT_CACHE_CONFIGURATIONS = [
        self::CACHE_KEY_FORMKIT_DEFINITIONS => [
            'frontend' => VariableFrontend::class,
            'backend' => RedisBackend::class,
            'groups' => [
                'all',
                'system',
            ],
            'options' => [
                'defaultLifetime' => AbstractBackend::UNLIMITED_LIFETIME,
            ],
        ],
        self::CACHE_KEY_FORMKIT_FORMS => [
            'frontend' => VariableFrontend::class,
            'backend' => RedisBackend::class,
            'groups' => [
                'all',
                'system',
            ],
            'options' => [
                'defaultLifetime' => AbstractBackend::UNLIMITED_LIFETIME,
            ],
        ],
    ];
    public const ERROR_INVALID_ID = 'Cannot register form definition with id %s. Id must not be empty';

    public function __construct(
        private readonly FrontendInterface $cache,
    ) {}

    /**
     * @throws InvalidFormIdException
     */
    public static function registerDefinitionFile(string $id, string $path): void
    {
        if (empty($id)) {
            throw new InvalidFormIdException(
                sprintf(self::ERROR_INVALID_ID, $id),
                1728660696
            );
        }

        if (self::isRegistered($id)) {
            return;
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['formkit']['definitionFiles'][$id] = $path;
    }

    public function hasFormDefinition(string $id): bool
    {
        return self::isRegistered($id);
    }

    public function getFormDefinition(string $id): array
    {
        if (!self::isRegistered($id)) {
            return [];
        }

        try {
            $cacheEntry = $this->cache->get($id);
            if (!$cacheEntry) {
                $path = self::getDefinitionPath($id);
                $yamlFileLoader = new YamlFileLoader();
                $fileContent = $yamlFileLoader->load($path);
                // @todo: build form by parsing placeholders
                $cacheEntry = json_encode($fileContent, JSON_THROW_ON_ERROR);
                $this->cache->set(
                    $id,
                    $cacheEntry
                );
            }
            $definition = json_decode($cacheEntry, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $definition = [];
        }

        return $definition;
    }

    public static function addCacheConfiguration(): void
    {
        foreach (self::DEFAULT_CACHE_CONFIGURATIONS as $id => $configuration) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$id] ??= [];
            //$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$id] = $configuration;
        }
    }

    /**
     * @param string $id
     * @return bool
     */
    protected static function isRegistered(string $id): bool
    {
        return isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['formkit']['definitionFiles'][$id]);
    }

    protected static function getDefinitionPath(string $id): string
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['formkit']['definitionFiles'][$id] ?? '';
    }
}
