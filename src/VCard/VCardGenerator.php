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

namespace Markocupic\EmployeeBundle\VCard;

use Contao\File;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class VCardGenerator
{
    public const VCARD_TEMPLATE = 'partial_employee_vcard';

    private TwigEnvironment $twig;

    public function __construct(TwigEnvironment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendToBrowser(EmployeeModel $objEmployee): void
    {
        $arrData = array_map(static fn ($value) => trim(utf8_decode(html_entity_decode((string) $value))), $objEmployee->row());

        // Add the file name
        $arrData['file_name'] = sprintf('%s %s %s', $arrData['title'], $arrData['firstname'], $arrData['lastname']);

        // Create temp file
        $objFile = new File('system/tmp/'.time().'.vcf');
        $objFile->append($this->twig->render('@MarkocupicEmployee/vcard/vcard.twig', $arrData));
        $objFile->close();

        $objFile->sendToBrowser(sprintf('vcard-%s-%s.vcf', $arrData['firstname'], $arrData['lastname']));
    }
}
