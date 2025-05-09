<?php

defined('TYPO3') or die();

use Cpsit\Formkit\Registry\FormRegistry;

FormRegistry::addCacheConfiguration();
/** @noinspection PhpUnhandledExceptionInspection */
FormRegistry::registerDefinitionFile(
    'formkit-simple-mail-form',
    'EXT:formkit/Resources/Public/Example/simpleMailForm.yaml'
);
