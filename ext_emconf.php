<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Formkit',
    'description' => 'Formkit is an extension to the TYPO3 CMS providing content elements for forms. The forms are rendered by a frontend app based on Formkit (see https://formkit.com/)"',
    'category' => 'fe',
    'state' => 'alpha',
    'author_company' => 'coding. powerful. systems. CPS GmbH',
    'author' => 'Dirk Wenzel',
    'author_email' => 'd.wenzel@familie-redlich.de',
    'version' => '0.1.0',
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '11.5.99',
                ],
            'conflicts' =>
                [
                ],
            'suggests' =>
                [
                ],
        ],
];
