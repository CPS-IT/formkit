<?php

namespace Cpsit\Formkit\Processor;

use Nng\Nnrestapi\Mvc\Request;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

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
class LocalizationProcessor implements DefinitionProcessorInterface
{
    public const MATCH_PATTERN = '%ll\(.*\)%';
    public const REPLACE_PATTERN = '%ll\(.*\)%';
    protected LanguageService $languageService;
    public function __construct(
        private readonly LanguageServiceFactory $languageServiceFactory,
    ) {

    }
    public function process($definition, Request $request): array
    {
        $languageService = $this->languageService ?? $this->languageServiceFactory->createFromSiteLanguage(
            $request->getMvcRequest()->getAttribute('language')
            ?? $request->getMvcRequest()->getAttribute('site')->getDefaultLanguage());
        // @todo: get settings, site, language from request
        foreach ($definition as $key => &$value) {
            if (is_array($value)){
                $value = $this->process($value, $request);
            }
            if ($this->canProcess($value, $request)) {
                $languageKey = str_replace(['%ll(', ')%'], '', $value);
                if ($replacement = $languageService->sL($languageKey)) {
                    $value = $replacement;
                }
            }
        }

        return $definition;
    }

    public function canProcess($value, Request $request): bool
    {
        return is_string($value) && preg_match(self::MATCH_PATTERN, $value);
    }
}