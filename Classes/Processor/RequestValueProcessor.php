<?php

namespace Cpsit\Formkit\Processor;

use Nng\Nnrestapi\Mvc\Request;
use TYPO3\CMS\Core\Utility\ArrayUtility;

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
class RequestValueProcessor implements DefinitionProcessorInterface
{
    public const MATCH_PATTERN = '%val\(.*\)%';
    public function process($definition, Request $request): array
    {
        $arguments = $request->getArguments();
        $settings = [];
        if (isset($arguments['settings'])) {
            try {
                $settings = json_decode($arguments['settings'], true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                // nop
            }
        }
        foreach ($definition as $key => &$value) {
            if ($this->canProcess((string)$key, $value, $request)) {
                $settingsKey = str_replace(['%val(', ')%'], '', $value);
                if (!ArrayUtility::isValidPath($settings, $settingsKey)) {
                    continue;
                }
                $value = ArrayUtility::getValueByPath($settings, $settingsKey);
            }
            if (is_array($value)) {
                $value = $this->process($value, $request);
            }
        }

        return $definition;
    }

    public function canProcess(string $key, $value, Request $request): bool
    {
        return is_string($value) && preg_match(self::MATCH_PATTERN, $value);
    }
}
