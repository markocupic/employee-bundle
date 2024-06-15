<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license LGPL-3.0+
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Model\Collection;
use Contao\ModuleModel;
use Contao\StringUtil;
use Markocupic\EmployeeBundle\Event\PrepareEmployeeDataEvent;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(EmployeeListController::TYPE, category: 'employee_modules')]
class EmployeeListController extends AbstractFrontendModuleController
{
    public const TYPE = 'employee_list';
    protected Collection|null $employees = null;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        if (null === ($this->employees = $this->getEmployees($model))) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $arrItems = [];

        while ($this->employees->next()) {
            $event = new PrepareEmployeeDataEvent($request, $this->employees->current(), [], $model);

            $this->eventDispatcher->dispatch($event);

            $arrItems[] = $event->getTemplateData();
        }

        $template->set('employees', $arrItems);

        return $template->getResponse();
    }

    protected function getEmployees(ModuleModel $model): Collection|null
    {
        $arrOptions = [
            'order' => 'id ASC', // default order
        ];

        $blnOrderBy = false;

        // Override default order
        if ($model->addSorting) {
            $orderByItems = StringUtil::deserialize($model->orderBy, true);
            $arrOrderBy = [];

            foreach ($orderByItems as $orderBy) {
                $blnOrderBy = true;
                $arrOrderBy[] = trim($orderBy['column'].' '.$orderBy['sortDirection']);
            }

            if ($blnOrderBy) {
                $arrOptions['order'] = implode(', ', $arrOrderBy);
            }
        }

        if ($model->showAllPublishedEmployees) {
            return EmployeeModel::findAllPublished($arrOptions);
        }

        $arrModels = [];
        $arrIds = StringUtil::deserialize($model->selectEmployee, true);

        if ($blnOrderBy) {
            return EmployeeModel::findMultipleAndPublishedByIds($arrIds, $arrOptions);
        }

        // Take the order from the tl_module checkboxWizard if no order is set.
        foreach ($arrIds as $id) {
            if (null !== ($objModel = EmployeeModel::findPublishedById($id, $arrOptions))) {
                $arrModels[] = $objModel;
            }
        }

        return new Collection($arrModels, 'tl_employee');
    }
}
