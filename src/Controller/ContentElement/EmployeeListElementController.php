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
use Contao\Database;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use Contao\Validator;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmployeeListElementController.
 */
class EmployeeListElementController extends AbstractContentElementController
{
    /**
     * Generate the content element.
     */
    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        $arrItems = [];
        $i = 0;
        $template->hasItems = false;
        $strLightboxId = 'lb' . $model->id;


        if ($model->showAllPublishedEmployees) {
            $objDb = Database::getInstance()
                ->prepare('SELECT * FROM tl_employee WHERE published=? ORDER BY lastname, firstname')
                ->execute(1)
            ;
            $arrIds = $objDb->fetchEach('id');
        } else {
            $arrIds = StringUtil::deserialize($model->selectEmployee, true);
        }

        if (\count($arrIds)) {
                if (null !== ($objEmployee = EmployeeModel::findMultipleByIds($arrIds))) {
                    while($objEmployee->next()) {
                        $template->hasItems = true;

                        $arrItems[$i]['employee'] = $objEmployee->row();
                        $arrItems[$i]['employee']['interview'] = StringUtil::deserialize($arrItems[$i]['employee']['interview'], true);
                        $arrItems[$i]['employee']['businessHours'] = StringUtil::deserialize($arrItems[$i]['employee']['interview']['businessHours'], true);

                        if (Validator::isUuid($arrItems[$i]['employee']['singleSRC'])) {
                            $objFile = FilesModel::findByUuid($arrItems[$i]['employee']['singleSRC']);

                            if (null !== $objFile && is_file(System::getContainer()->getParameter('kernel.project_dir').'/'.$objFile->path)) {
                                $arrItems[$i]['employee']['hasImage'] = true;
                                $arrItems[$i]['singleSRC'] = $objFile->path;
                                // Add size and margin
                                $arrItems[$i]['size'] = $model->size;
                                $arrItems[$i]['imagemargin'] = $model->imagemargin;
                                $arrItems[$i]['fullsize'] = $model->fullsize;
                                $arrItems[$i]['filesModel'] = $objFile;

                                $objImageTempl = new \stdClass();

                                Controller::addImageToTemplate($objImageTempl, $arrItems[$i], null, $strLightboxId, $arrItems[$i]['filesModel']);
                                $arrItems[$i]['arrImgData'] = (array)$objImageTempl;
                            }
                        }
                        ++$i;
                    }
                }

        }
        $template->items = $arrItems;

        return $template->getResponse();
    }
}
