<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Controller;

use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Markocupic\EmployeeBundle\VCard\VCardGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DownloadVCardController extends AbstractController
{
    public function __construct(
        private readonly VCardGenerator $VCardGenerator,
    ) {
    }

    #[Route('/_employee/download_vcard/{identifier}', name: self::class, defaults: ['_scope' => 'frontend'])]
    public function __invoke(Request $request, int|string $identifier = 0): BinaryFileResponse|Response
    {
        if (!$identifier) {
            return new Response('No id/alias defined!');
        }

        if (null !== ($objEmployee = EmployeeModel::findPublishedByIdOrAlias($identifier))) {
            $splFileObject = $this->VCardGenerator->getVCard($objEmployee, $request);

            return $this->file($splFileObject->getFileInfo());
        }

        return new Response(sprintf('Employee with identifier %s not found!', $identifier));
    }
}
