<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Markocupic\EmployeeBundle\Controller\EmployeeTrait;
use Markocupic\EmployeeBundle\Event\PrepareEmployeeDataEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(EmployeeListController::TYPE, category: 'employee_frontend_module')]
class EmployeeListController extends AbstractFrontendModuleController
{
    use EmployeeTrait;

    public const TYPE = 'employee_list';

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        if (null === ($employees = $this->getEmployees($model))) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $arrItems = [];

        while ($employees->next()) {
            $event = new PrepareEmployeeDataEvent($request, $model, [], $employees->current());

            $this->eventDispatcher->dispatch($event);

            $arrItems[] = $event->getTemplateData();
        }

        $template->set('employees', $arrItems);

        return $template->getResponse();
    }
}
