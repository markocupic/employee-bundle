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

namespace Markocupic\EmployeeBundle\Listener\ContaoHooks;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Input;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Markocupic\EmployeeBundle\Vcard\VcardGenerator;

/**
 * @Hook("generatePage", priority=100)
 */
class GeneratePageListener
{
    public function __invoke(PageModel $objPage, LayoutModel $objLayout, PageRegular $objPageRegular): void
    {
        // Trigger VCard download
        if ('true' === Input::get('downloadVCard') && '' !== Input::get('id')) {
            if (null !== ($objEmployee = EmployeeModel::findByPk(Input::get('id')))) {
                VcardGenerator::sendToBrowser($objEmployee);
            }
        }
    }
}
