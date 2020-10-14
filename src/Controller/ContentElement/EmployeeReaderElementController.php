<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2020 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use Contao\Validator;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmployeeReaderElementController.
 */
class EmployeeReaderElementController extends AbstractContentElementController
{
    /**
     * @var EmployeeModel|null
     */
    protected $objEmployee;

    public function __invoke(Request $request, ContentModel $model, string $section, array $classes = null): Response
    {
        if (null === ($this->objEmployee = EmployeeModel::findPublishedById($model->selectEmployee))) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return parent::__invoke($request, $model, $section, $classes);
    }

    /**
     * Generate the content element.
     */
    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        if (Validator::isUuid($this->objEmployee->singleSRC)) {
            $objFile = FilesModel::findByUuid($this->objEmployee->singleSRC);

            if (null !== $objFile && is_file(System::getContainer()->getParameter('kernel.project_dir').'/'.$objFile->path)) {
                $model->singleSRC = $objFile->path;
                Controller::addImageToTemplate($template, $model->row(), null, null, $objFile);
                $template->addImage = true;
            }
        }

        $this->objEmployee->publications = Controller::replaceInsertTags($this->objEmployee->publications);
        $this->objEmployee->interview = StringUtil::deserialize($this->objEmployee->interview, true);
        $this->objEmployee->businessHours = StringUtil::deserialize($this->objEmployee->businessHours, true);
        $template->employee = $this->objEmployee;

        return $template->getResponse();
    }
}
