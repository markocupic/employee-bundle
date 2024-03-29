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

namespace Markocupic\EmployeeBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\Model\Collection;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\Template;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Markocupic\EmployeeBundle\Traits\FrontendModuleTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsFrontendModule(EmployeeListController::TYPE, category: 'employee_modules', template: 'mod_employee_list')]
class EmployeeListController extends AbstractFrontendModuleController
{
    use FrontendModuleTrait;

    public const TYPE = 'employee_list';

    public Studio $studio;
    public InsertTagParser $insertTagParser;
    public TwigEnvironment $twig;
    public string $projectDir;
    protected ?Collection $employees = null;

    public function __construct(InsertTagParser $insertTagParser, Studio $studio, TwigEnvironment $twig, string $projectDir)
    {
        $this->insertTagParser = $insertTagParser;
        $this->studio = $studio;
        $this->twig = $twig;
        $this->projectDir = $projectDir;
    }

    public function __invoke(Request $request, ModuleModel $model, string $section, array $classes = null): Response
    {
        if (null === ($this->employees = $this->getEmployees($model))) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return parent::__invoke($request, $model, $section, $classes);
    }

    protected function getEmployees(ModuleModel $model): ?Collection
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

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $arrItems = [];

        while ($this->employees->next()) {
            $arrData = $this->getEmployeeDetails($this->employees->current(), $model, $this);

            $arrItems[] = $arrData;
        }

        $template->employees = $arrItems;

        return $template->getResponse();
    }
}
