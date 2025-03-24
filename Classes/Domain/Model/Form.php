<?php

namespace Cpsit\Formkit\Domain\Model;

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
class Form implements \Serializable
{
    public const KEY_DESCRIPTION = 'description';
    public const KEY_ID = 'id';
    public const KEY_LABEL = 'label';
    public const KEY_SCHEMA = 'schema';
    public const KEY_SETTINGS = 'settings';
    public const KEY_LANGUAGE_FILES = 'languageFiles';

    protected string $description = '';
    protected string $id = '';
    protected string $label = '';
    protected array $schema = [];
    protected array $settings = [];
    protected array $languageFiles = [];
    public function __construct(string $id = '', array $formDefinition = [])
    {
        $this->id = $id;
        if (!empty($formDefinition[self::KEY_LANGUAGE_FILES]) && is_array($formDefinition[self::KEY_LANGUAGE_FILES])) {
            $this->languageFiles = $formDefinition[self::KEY_LANGUAGE_FILES];
        }
        if (!empty($formDefinition[self::KEY_SCHEMA]) && is_array($formDefinition[self::KEY_SCHEMA])) {
            $this->schema = $formDefinition[self::KEY_SCHEMA];
        }
        if (!empty($formDefinition[self::KEY_SETTINGS]) && is_array($formDefinition[self::KEY_SETTINGS])) {
            $this->settings = $formDefinition[self::KEY_SETTINGS];
        }
        if (!empty($formDefinition[self::KEY_DESCRIPTION]) && is_string($formDefinition[self::KEY_DESCRIPTION])) {
            $this->description = $formDefinition[self::KEY_DESCRIPTION];
        }
        if (!empty($formDefinition[self::KEY_LABEL]) && is_string($formDefinition[self::KEY_LABEL])) {
            $this->label = $formDefinition[self::KEY_LABEL];
        }
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function unserialize(string $data): void
    {
        $data = unserialize($data, ['allowed_classes' => false]);
        $this->__unserialize($data);
    }

    public function __serialize(): array
    {
        return [
            self::KEY_ID => $this->id,
            self::KEY_LABEL => $this->label,
            self::KEY_LANGUAGE_FILES => $this->languageFiles,
            self::KEY_DESCRIPTION => $this->description,
            self::KEY_SCHEMA => $this->schema,
            self::KEY_SETTINGS => $this->settings,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data[self::KEY_ID] ?? '';
        $this->description = $data[self::KEY_DESCRIPTION] ?? '';
        $this->label = $data[self::KEY_LABEL] ?? '';
        $this->languageFiles = $data[self::KEY_LANGUAGE_FILES] ?? [];
        $this->schema = $data[self::KEY_SCHEMA] ?? [];
        $this->settings = $data[self::KEY_SETTINGS] ?? [];
    }

    public function toArray(): array
    {
        return $this->__serialize();
    }
}
