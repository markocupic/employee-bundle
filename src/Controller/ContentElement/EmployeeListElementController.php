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
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\Database;
use Contao\StringUtil;
use Contao\Template;
use Contao\Validator;
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
        $rows = [];
        $template->hasItems = false;

        if ($model->showAllPublishedEmployees) {
            $objDb = Database::getInstance()
                ->prepare('SELECT * FROM tl_employee WHERE id>1000 && published=? ORDER BY lastname, firstname')
                ->execute(1)
            ;
            $arrIds = $objDb->fetchEach('id');
        } else {
            $arrIds = StringUtil::deserialize($model->selectEmployee, true);
        }

        if(count($arrIds)) {
            $template->hasItems = true;
            foreach ($arrIds as $userId) {
                $objDb = Database::getInstance()
                    ->prepare('SELECT * FROM tl_employee WHERE id=? AND published=?')
                    ->execute($userId, 1);

                while ($objDb->next()) {
                    $row = $objDb->row();

                    if (Validator::isUuid($row['singleSRC'])) {
                        $row['singleSRC'] = StringUtil::binToUuid($row['singleSRC']);
                    }
                    $row['interview'] = StringUtil::deserialize($row['interview'], true);
                    $row['businessHours'] = StringUtil::deserialize($row['businessHours'], true);

                    $rows[] = $row;
                }
            }
        }
        $template->rows = $rows;

        return $template->getResponse();
    }
}
