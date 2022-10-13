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
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Database;
use Contao\FilesModel;
use Contao\Model\Collection;
use Contao\StringUtil;
use Contao\Template;
use Contao\Validator;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(category="employee_elements")
 */
class EmployeeListElementController extends AbstractContentElementController
{
    public const TYPE = 'employee_list_element';

    protected string $projectDir;
    protected ?Collection $employee = null;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function __invoke(Request $request, ContentModel $model, string $section, array $classes = null): Response
    {
        if ($model->showAllPublishedEmployees) {
            $objDb = Database::getInstance()
                ->execute('SELECT id FROM tl_employee')
            ;
            $arrIds = $objDb->fetchEach('id');
        } else {
            $arrIds = StringUtil::deserialize($model->selectEmployee, true);
        }

        $arrOptions = [
            'order' => 'tl_employee.lastname, tl_employee.firstname',
        ];

        if (null === ($this->employee = EmployeeModel::findMultipleAndPublishedByIds($arrIds, $arrOptions))) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return parent::__invoke($request, $model, $section, $classes);
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        $arrItems = [];
        $strLightboxId = 'lb'.$model->id;
        $i = 0;

        while ($this->employee->next()) {
            $arrItems[$i]['employee'] = $this->employee->row();
            $arrItems[$i]['employee']['interview'] = StringUtil::deserialize($arrItems[$i]['employee']['interview'], true);
            $arrItems[$i]['employee']['businessHours'] = StringUtil::deserialize($arrItems[$i]['employee']['interview']['businessHours'], true);

            // Add image to template
            if (Validator::isUuid($arrItems[$i]['employee']['singleSRC'])) {
                $objFile = FilesModel::findByUuid($arrItems[$i]['employee']['singleSRC']);

                if (null !== $objFile && is_file($this->projectDir.'/'.$objFile->path)) {
                    $arrItems[$i]['employee']['hasImage'] = true;
                    $arrItems[$i]['singleSRC'] = $objFile->path;
                    // Add size and margin
                    $arrItems[$i]['size'] = $model->size;
                    $arrItems[$i]['imagemargin'] = $model->imagemargin;
                    $arrItems[$i]['fullsize'] = $model->fullsize;
                    $arrItems[$i]['filesModel'] = $objFile;

                    $objImageTempl = new \stdClass();
                    Controller::addImageToTemplate($objImageTempl, $arrItems[$i], null, $strLightboxId, $arrItems[$i]['filesModel']);
                    $arrItems[$i]['arrImgData'] = (array) $objImageTempl;
                }
            }
            ++$i;
        }

        $template->items = $arrItems;

        return $template->getResponse();
    }
}
