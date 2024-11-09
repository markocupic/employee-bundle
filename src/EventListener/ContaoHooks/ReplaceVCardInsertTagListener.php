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

namespace Markocupic\EmployeeBundle\EventListener\ContaoHooks;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Markocupic\EmployeeBundle\Controller\DownloadVCardController;
use Symfony\Component\Routing\RouterInterface;

#[AsHook(self::HOOK, priority: 100)]
class ReplaceVCardInsertTagListener
{
    public const HOOK = 'replaceInsertTags';

    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    public function __invoke(string $strTag): string|false
    {
        if (preg_match('/^employee_vcard_download_url::(.*)$/', $strTag, $match)) {
            return $this->router->generate(DownloadVCardController::class, ['identifier' => $match[1]]);
        }

        return false;
    }
}
