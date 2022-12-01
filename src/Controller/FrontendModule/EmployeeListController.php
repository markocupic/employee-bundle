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
use Contao\Database;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\Template;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Markocupic\EmployeeBundle\Traits\FrontendModuleTrait;
use Model\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsFrontendModule(EmployeeListController::TYPE, category: 'employee_modules')]
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
        $arrIds = $this->getEmployees($model);

        $arrOptions = [
            'order' => 'tl_employee.lastname, tl_employee.firstname',
        ];

        if (null === ($this->employees = EmployeeModel::findMultipleAndPublishedByIds($arrIds, $arrOptions))) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return parent::__invoke($request, $model, $section, $classes);
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

    protected function getEmployees(ModuleModel $model): array
    {
        if ($model->showAllPublishedEmployees) {
            $objDb = Database::getInstance()
                ->execute('SELECT id FROM tl_employee')
            ;
            $arrIds = $objDb->fetchEach('id');
        } else {
            $arrIds = StringUtil::deserialize($model->selectEmployee, true);
        }

        return $arrIds;
    }
}
