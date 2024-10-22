<?php

declare(strict_types=1);

/*
 * This file is part of the formkit project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Cpsit\Formkit\Api\Form;

use Cpsit\Formkit\Domain\Factory\FormFactory;
use Cpsit\Formkit\Domain\Model\Form;
use Cpsit\Formkit\Domain\Model\NullForm;
use Nng\Nnrestapi\Annotations as Api;
use Nng\Nnrestapi\Api\AbstractApi;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Http\Response;

/**
 * Get configuration for a formkit compatible form
 *
 * @Api\Endpoint
 */
class Get extends AbstractApi
{
    public const KEY_ID = 'id';

    public function __construct(
        //protected readonly FormRepository $formRepository,
        protected FormFactory                $formFactory,
        protected readonly FrontendInterface $cache
    )
    {
    }

    /**
     * Call via GET-request with an id:
     *
     * Example:
     *
     * https://www.mywebsite.com/api/formkit/form/simple-mail-form
     *
     * ### Response:
     *
     * ```
     * {
     *  "data": [
     *     "schema": [
     *       {
     *         "$formkit": "text",
     *         "label": "First Name",
     *         "name": "firstName",
     *         "id": "first-name",
     *         "prefix-icon": "email"
     *         "placeholder": "Enter your first name",
     *         "required": true,
     *         "help": "Please provide your first name.",
     *         "validation": "length:3,150",
     *         "validationMessages": {
     *           "length": "Instructions cannot be more than 120 characters."
     *         }
     *       },
     *       {
     *         "$formkit": "email",
     *         "label": "Email Address",
     *         "name": "email",
     *         "id": "email",
     *         "placeholder": "Enter your email",
     *         "validation": "email",
     *         "help": "We'll never share your email with anyone else."
     *       },
     *       {
     *         "$formkit": "select",
     *         "label": "Choose your country",
     *         "name": "country",
     *         "id": "country",
     *         "validation": "required",
     *         "options": [
     *           {
     *             "label": "United States",
     *             "value": "us"
     *           },
     *           {
     *             "label": "Canada",
     *             "value": "ca"
     *           },
     *           {
     *             "label": "Germany",
     *             "value": "de"
     *           }
     *         ],
     *         "help": "Please select your country."
     *       },
     *       {
     *         "$formkit": "checkbox",
     *         "label": "I agree to the terms and conditions",
     *         "name": "terms",
     *         "id": "terms",
     *         "validation": "required",
     *         "help": "You must agree before submitting."
     *       },
     *       {
     *         "$formkit": "submit",
     *         "label": "Submit form",
     *         "name": "submit",
     *         "id": "submit",
     *         "help": "Submit the form"
     *       }
     *     ],
     *     "settings": {
     *       "states": [
     *         {
     *           "status": "new",
     *           "label": "label status `new`",
     *           "title": "title status `new`",
     *           "description": "description status `new`",
     *         },
     *         {
     *           "status": "validated",
     *           "label": "label status `validated`",
     *           "title": "title status `validated`",
     *           "description": "description status `validated`",
     *         },
     *         {
     *           "status": "received",
     *           "label": "label status `received`",
     *           "title": "title status `received`",
     *           "description": "description status `received`",
     *         },
     *         {
     *           "status": "approved",
     *           "label": "label status `approved`",
     *           "title": "title status `approved`",
     *           "description": "description status `approved`",
     *         },
     *         {
     *           "status": "published",
     *           "label": "label status `published`",
     *           "title": "title status `published`",
     *           "description": "description status `published`",
     *         },
     *       ],
     *     },
     *   }
     * ```
     *
     * @Api\Route("GET /formkit/form/{id}")
     * @Api\Access("public")
     * @Api\Localize
     * @return Response
     * @throws \Exception
     */
    public function getIndexAction(): Response
    {
        if (!$this->isRequestValid()) {
            // Return an `invalid parameters` (422) Response
            return $this->response->invalid(
                "Invalid parameters for form GET.",
                "1728650710"
            );
        }

        $id = $this->request->getArguments()[static::KEY_ID];
        $cacheIdentifier = md5(serialize($this->request->getArguments()));

        if ($this->cache->has($cacheIdentifier)) {
            $data = $this->cache->get($cacheIdentifier);
            $form = new Form();
            $form->unserialize($data);
        } else {
            $form = $this->formFactory->createAndParse($id, $this->request);
        }
        if ($form instanceof NullForm) {
            // Return a `not found` (404) Response
            return $this->response->notFound(
                'Form not found',
                '1728804217'
            );
        }

        $this->cache->set($cacheIdentifier, $form->serialize());

        return $this->response
            ->setMessage('success')
            ->setStatus(200)
            ->setBody([
                'code' => $this->response->getStatus(),
                'message' => $this->response->getMessage(),
                'data' => $form->toArray()
            ])
            ->render();
    }

    protected function isRequestValid(): bool
    {
        $id = $this->request->getArguments()[static::KEY_ID] ?? null;

        return (bool)$id;

    }
}
