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
use Symfony\Component\HttpFoundation\RequestStack;

#[AsHook(ReplaceInsertTagsListener::HOOK, priority: 100)]
class ReplaceInsertTagsListener
{
    public const HOOK = 'replaceInsertTags';
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke(string $strTag): false|string
    {
        if (preg_match('/^vcard_download_url::([\d]+)$/', $strTag, $match)) {
            $request = $this->requestStack->getCurrentRequest();

            return sprintf('%s?downloadVCard=true&amp;id=%s', $request->getUri(), $match[1]);
        }

        return false;
    }
}
