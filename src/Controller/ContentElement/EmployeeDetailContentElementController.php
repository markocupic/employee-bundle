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

namespace Markocupic\EmployeeBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Markocupic\EmployeeBundle\Event\PrepareEmployeeDataEvent;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(EmployeeDetailContentElementController::TYPE, category: 'employee')]
class EmployeeDetailContentElementController extends AbstractContentElementController
{
    public const TYPE = 'employee_detail';

    private EmployeeModel|null $employee = null;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if (!empty($model->selectEmployee)) {
            $this->employee = EmployeeModel::findPublishedByIdOrAlias($model->selectEmployee);
        }

        if (null === $this->employee) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $templateData = [];

        $event = new PrepareEmployeeDataEvent($request, $this->employee, $templateData, $model);

        $this->eventDispatcher->dispatch($event);

        $template->set('employee', $event->getTemplateData());

        return $template->getResponse();
    }
}
