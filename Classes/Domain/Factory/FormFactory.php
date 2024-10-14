<?php

namespace Cpsit\Formkit\Domain\Factory;

use Cpsit\Formkit\Cache\FormRegistry;
use Cpsit\Formkit\Domain\Model\Form;
use Cpsit\Formkit\Domain\Model\NullForm;
use Cpsit\Formkit\Domain\Repository\FormRepository;

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
class FormFactory
{
    public function __construct(
        private FormRegistry $formRegistry
    )
    {

    }

    public function createFormFromDefinition(string $id): Form
    {
        if ($this->formRegistry->hasFormDefinition($id)) {
            return new NullForm($id, []);
        }

        $definition = $this->formRegistry->getFormDefinition($id);
        // @todo: parse schema for TYPO3-specific keys
        return new Form($id, $definition);
    }
}
