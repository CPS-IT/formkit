<?php

namespace Cpsit\Formkit\DataProvider;

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
 * Interface SelectOptionsDataProviderInterface
 * Provides options data for a select field
 * Implementations must be registered with
 */
interface SelectOptionsDataProviderInterface
{
    public const KEY_LABEL = 'label';
    public const KEY_VALUE = 'value';

    /**
     * Returns an individual key. By this key will be
     * determined if the data provider matches for a given
     * select field.
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Get the options for a given select field.
     * Example :
     * [
     *   [
     *     'label' => 'foo'
     *     'value' => 'foo label'
     *   ]
     *   [
     *     'label' => 'bar'
     *     'value' => 'bar label'
     *   ]
     * ]
     * @return array An array of options containing label and value.
     */
    public function getOptions(): array;
}
