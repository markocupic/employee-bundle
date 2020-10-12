<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2020 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Database;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Contao\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EmployeeReaderElementController.
 */
class EmployeeReaderElementController extends AbstractContentElementController
{
    protected $objEmployee;

    public function __invoke(Request $request, ContentModel $model, string $section, array $classes = null): Response
    {
        if ($this->page instanceof PageModel && $this->get('contao.routing.scope_matcher')->isFrontendRequest($request)) {
            $userId = $this->selectEmployee;
            $objDb = Database::getInstance()
                ->prepare('SELECT * FROM tl_employee WHERE id=? AND published=?')
                ->limit(1)
                ->execute($userId, 1)
            ;

            if (!$objDb->numRows) {
                return new Response('', Response::HTTP_NO_CONTENT);
            }
            $this->objEmployee = $objDb;
        }

        return parent::__invoke($request, $model, $section, $classes);
    }

    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();

        $services['translator'] = TranslatorInterface::class;
        $services['contao.routing.scope_matcher'] = ScopeMatcher::class;

        return $services;
    }

    /**
     * Generate the content element.
     */
    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        $row = $this->objEmployee->row();

        if (Validator::isUuid($row['singleSRC'])) {
            $row['singleSRC'] = StringUtil::binToUuid($row['singleSRC']);
        }
        $row['interview'] = StringUtil::deserialize($row['interview'], true);
        $row['businessHours'] = StringUtil::deserialize($row['businessHours'], true);

        $template->row = $row;

        return $template->getResponse();
    }
}
