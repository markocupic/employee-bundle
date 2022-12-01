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

namespace Markocupic\EmployeeBundle\EventListener\ContaoHooks;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Markocupic\EmployeeBundle\VCard\VCardGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsHook(GeneratePageListener::HOOK, priority: 100)]
class GeneratePageListener
{
    public const HOOK = 'generatePage';
    private VCardGenerator $VCardGenerator;
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, VCardGenerator $VCardGenerator)
    {
        $this->VCardGenerator = $VCardGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(PageModel $objPage, LayoutModel $objLayout, PageRegular $objPageRegular): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $id = $request->query->get('id');

        // Trigger VCard download
        if ($id && 'true' === $request->query->get('downloadVCard')) {
            if (null !== ($objEmployee = EmployeeModel::findByPk($id))) {
                $this->VCardGenerator->sendToBrowser($objEmployee);
            }
        }
    }
}
