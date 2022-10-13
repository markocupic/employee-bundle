<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license LGPL-3.0+
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\Template;
use Contao\Validator;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(category="employee_elements")
 */
class EmployeeSingleElementController extends AbstractContentElementController
{
    public const TYPE = 'employee_single_element';

    protected InsertTagParser $insertTagParser;
    protected string $projectDir;
    protected ?EmployeeModel $employee = null;

    public function __construct(InsertTagParser $insertTagParser, string $projectDir)
    {
        $this->insertTagParser = $insertTagParser;
        $this->projectDir = $projectDir;
    }

    public function __invoke(Request $request, ContentModel $model, string $section, array $classes = null): Response
    {
        if (null === ($this->employee = EmployeeModel::findPublishedById($model->selectEmployee))) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return parent::__invoke($request, $model, $section, $classes);
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        if (Validator::isUuid($this->employee->singleSRC)) {
            $objFile = FilesModel::findByUuid($this->employee->singleSRC);

            if (null !== $objFile && is_file($this->projectDir.'/'.$objFile->path)) {
                $model->singleSRC = $objFile->path;

                Controller::addImageToTemplate($template, $model->row(), null, null, $objFile);
                $template->hasImage = true;
            }
        }

        $this->employee->publications = $this->insertTagParser->replaceInline($this->employee->publications);
        $this->employee->interview = StringUtil::deserialize($this->employee->interview, true);
        $this->employee->businessHours = StringUtil::deserialize($this->employee->businessHours, true);
        $template->employee = $this->employee;

        return $template->getResponse();
    }
}
