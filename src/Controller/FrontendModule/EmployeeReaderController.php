<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic <m.cupic@gmx.ch>
 * @license LGPL-3.0+
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Input;
use Contao\ModuleModel;
use Markocupic\EmployeeBundle\Event\PrepareEmployeeDataEvent;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(EmployeeReaderController::TYPE, category: 'employee_frontend_module')]
class EmployeeReaderController extends AbstractFrontendModuleController
{
    public const TYPE = 'employee_reader';
    public EmployeeModel|null $employee = null;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ScopeMatcher $scopeMatcher,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $this->initializeContaoFramework();

        $alias = !empty(Input::get('items')) ? Input::get('items', null) : Input::get('auto_item', null);

        if (empty($alias)) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        if (null === ($this->employee = EmployeeModel::findPublishedByIdOrAlias($alias))) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $event = new PrepareEmployeeDataEvent($request, $model, [], $this->employee->current());

        $this->eventDispatcher->dispatch($event);

        $template->set('employee', $event->getTemplateData());

        return $template->getResponse();
    }
}
