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

namespace Markocupic\EmployeeBundle\Event;

use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\HttpFoundation\Request;

class GenerateVCardEvent
{
    public function __construct(
        private readonly Request $request,
        private array $arrData,
        private readonly EmployeeModel $employee,
        private string $fileName,
        private string $templateName,
    ) {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getTemplateData(): array
    {
        return $this->arrData;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function getEmployee(): EmployeeModel|null
    {
        return $this->employee;
    }

    public function setTemplateData(array $arrData): void
    {
        $this->arrData = $arrData;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function setTemplateName(string $templateName): void
    {
        $this->templateName = $templateName;
    }
}
