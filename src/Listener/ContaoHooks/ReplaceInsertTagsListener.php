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

namespace Markocupic\EmployeeBundle\Listener\ContaoHooks;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Environment;

/**
 * @Hook("replaceInsertTags")
 */
class ReplaceInsertTagsListener
{
    /**
     * @param $strTag
     *
     * @return false|string
     */
    public function replaceInsertTags($strTag)
    {
        if (preg_match('/^vcard_download_url::([\d]+)$/', $strTag, $match)) {
            return Environment::get('request').'?downloadVCard=true&amp;id='.$match[1];
        }

        return false;
    }
}
