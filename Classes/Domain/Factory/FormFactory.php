<?php

namespace Cpsit\Formkit\Domain\Factory;

use Cpsit\Formkit\Registry\FormRegistry;
use Cpsit\Formkit\Domain\Model\Form;
use Cpsit\Formkit\Domain\Model\NullForm;
use Nng\Nnrestapi\Mvc\Request;
use Cpsit\Formkit\Processor\DefinitionProcessorInterface;

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
readonly class FormFactory
{
    /**
     * @param iterable<DefinitionProcessorInterface> $processors
     */
    public function __construct(
        private FormRegistry $formRegistry,
        private iterable     $processors
    )
    {

    }

    public function createFromDefinition(string $id): Form
    {
        if (!$this->formRegistry->hasFormDefinition($id)) {
            return new NullForm();
        }

        $definition = $this->formRegistry->getFormDefinition($id);
        return new Form($id, $definition);
    }

    public function createAndParse(string $id, Request $request): Form
    {
        $form = $this->createFromDefinition($id);
        if ($form instanceof NullForm)
        {
            return $form;
        }
        $definition = $form->toArray();
        foreach (iterator_to_array($this->processors) as $processor) {
            $definition = $processor->process($definition, $request);
        }

        return new Form($id, $definition);
    }
}
