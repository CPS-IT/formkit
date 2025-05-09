<?php

namespace Cpsit\Formkit\Processor;

use Cpsit\Formkit\DataProvider\SelectOptionsDataProviderInterface;
use Nng\Nnrestapi\Mvc\Request;

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

/**
 * Class SelectOptionsProcessor
 * Provides options for select fields.
 * Usage:
 * ```yaml
 *   $formkit: 'select*
 *   options: "%dataProvider(foo-options)%"
 *   label: 'foo'
 *   name: 'bar'
 *   id: 'bar'
 * ```
 */
class SelectOptionsProcessor implements DefinitionProcessorInterface
{
    public const KEY_OPTIONS = 'options';
    public const MATCH_PATTERN = '%dataProvider\(.*\)%';

    public function __construct(
        private iterable $dataProviders
    ) {
        $this->dataProviders = iterator_to_array($dataProviders);
    }

    public function process($definition, Request $request): array
    {
        foreach ($definition as $key => &$value) {
            if (is_array($value)) {
                $value = $this->process($value, $request);
            }
            if ($key !== self::KEY_OPTIONS) {
                continue;
            }
            if ($this->canProcess($key, $value, $request)) {
                $dataProviderKey = str_replace(['%dataProvider(', ')%'], '', $value);

                foreach ($this->dataProviders as $dataProvider) {
                    if (!$dataProvider instanceof SelectOptionsDataProviderInterface) {
                        continue;
                    }
                    // first matching data provider wins
                    if ($dataProvider->getKey() === $dataProviderKey) {
                        $value = $dataProvider->getOptions();
                        break;
                    }
                }
            }
        }

        return $definition;
    }

    public function canProcess(string $key, $value, Request $request): bool
    {
        return is_string($value) && preg_match(self::MATCH_PATTERN, $value);
    }
}
