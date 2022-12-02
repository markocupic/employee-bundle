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

use Contao\Config;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Input;
use Contao\ModuleModel;
use Contao\Template;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Markocupic\EmployeeBundle\Traits\FrontendModuleTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

#[AsFrontendModule(EmployeeReaderController::TYPE, category: 'employee_modules')]
class EmployeeReaderController extends AbstractFrontendModuleController
{
    use FrontendModuleTrait;

    public const TYPE = 'employee_reader';

    public Studio $studio;
    public InsertTagParser $insertTagParser;
    public TwigEnvironment $twig;
    private ScopeMatcher $scopeMatcher;
    public string $projectDir;

    public function __construct(InsertTagParser $insertTagParser, Studio $studio, TwigEnvironment $twig, ScopeMatcher $scopeMatcher, string $projectDir)
    {
        $this->insertTagParser = $insertTagParser;
        $this->studio = $studio;
        $this->twig = $twig;
        $this->scopeMatcher = $scopeMatcher;
        $this->projectDir = $projectDir;
    }

    public function __invoke(Request $request, ModuleModel $model, string $section, array $classes = null): Response
    {

        if($this->scopeMatcher->isFrontendRequest($request)){
            // Set the item from the auto_item parameter
            if (!isset($_GET['items']) && isset($_GET['auto_item']) && Config::get('useAutoItem')) {
                Input::setGet('items', Input::get('auto_item'));
            }

            // Return an empty string if "items" is not set (to combine list and reader on same page)
            $alias = Input::get('items');

            if (!$alias) {
                return new Response('', Response::HTTP_NO_CONTENT);
            }

            if (null === ($this->employee = EmployeeModel::findPublishedByIdOrAlias($alias))) {
                return new Response('', Response::HTTP_NO_CONTENT);
            }
        }

        return parent::__invoke($request, $model, $section, $classes);
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $arrData = $this->getEmployeeDetails($this->employee->current(), $model, $this);

        $template->employee = $arrData;

        return $template->getResponse();
    }
}
