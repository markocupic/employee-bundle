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

namespace Markocupic\EmployeeBundle\Event;

use Contao\ContentModel;
use Contao\ModuleModel;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\HttpFoundation\Request;

class PrepareEmployeeDataEvent
{
    public function __construct(
        private readonly Request $request,
        private readonly EmployeeModel|null $employee = null,
        private array $arrData,
        private readonly ContentModel|ModuleModel|null $model,
    ) {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getEmployee(): EmployeeModel|null
    {
        return $this->employee;
    }

    public function getTemplateData(): array
    {
        return $this->arrData;
    }

    public function getModel(): ContentModel|ModuleModel|null
    {
        return $this->model;
    }

    public function setTemplateData(array $arrData): void
    {
        $this->arrData = $arrData;
    }
}
