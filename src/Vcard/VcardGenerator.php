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

namespace Markocupic\EmployeeBundle\Vcard;

use Contao\File;
use Contao\FrontendTemplate;
use Markocupic\EmployeeBundle\Model\EmployeeModel;

class VcardGenerator
{
    public const VCARD_TEMPLATE = 'partial_employee_vcard';

    /**
     * @throws \Exception
     */
    public static function sendToBrowser(EmployeeModel $objEmployee): void
    {
        $objTemplate = new FrontendTemplate(static::VCARD_TEMPLATE);

        // Set data from object
        $arrData = $objEmployee->row();

        foreach (array_keys($arrData) as $k) {
            if (\is_string($arrData[$k])) {
                $arrData[$k] = html_entity_decode($arrData[$k]);
                $arrData[$k] = utf8_decode($arrData[$k]);
                $arrData[$k] = trim($arrData[$k]);
            }
        }

        $objTemplate->setData($arrData);

        // Set fname
        $objTemplate->fn = sprintf('%s %s %s', $arrData['title'], $arrData['firstname'], $arrData['lastname']);

        // Create tmp-file
        $objFile = new File('system/tmp/'.time().'.vcf', true);
        $objFile->append($objTemplate->parse());
        $objFile->close();
        $objFile->sendToBrowser(sprintf('vcard-%s-%s.vcf', $arrData['firstname'], $arrData['lastname']));
    }
}
