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

namespace Markocupic\EmployeeBundle\Traits;

use Contao\Config;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Image\Studio\FigureBuilder;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\FilesModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Validator;
use Markocupic\EmployeeBundle\Model\EmployeeModel;

trait FrontendModuleTrait
{
    protected ?FigureBuilder $figureBuilder = null;

    public function getFigureBuilder(ModuleModel $moduleModel, Studio $studio): FigureBuilder
    {
        if ($this->figureBuilder) {
            return $this->figureBuilder;
        }

        $figureBuilder = $studio->createFigureBuilder();

        $figureBuilder->setSize(StringUtil::deserialize($moduleModel->size));

        if ($moduleModel->fullsize) {
            $figureBuilder->setLightboxGroupIdentifier('lb_employee_list_'.$moduleModel->id);
            $figureBuilder->enableLightbox();
        }

        return $figureBuilder;
    }

    public function getEmployeeDetails(EmployeeModel $employeeModel, ModuleModel $moduleModel, AbstractFrontendModuleController $module): array
    {
        $arrData = $employeeModel->row();

        $arrData['hasPortraitImage'] = false;
        $arrData['publications'] = $module->insertTagParser->replaceInline($arrData['publications']);

        $arrData['interview'] = StringUtil::deserialize($arrData['interview'] ?? null, true);
        $arrData['businessHours'] = StringUtil::deserialize($arrData['businessHours'] ?? null, true);
        $arrData['href'] = false;

        $objJumpToPage = $this->getJumpToPage($moduleModel);

        if ($objJumpToPage) {
            $params = (Config::get('useAutoItem') ? '/' : '/items/').($arrData['alias'] ?: $arrData['id']);
            $arrData['href'] = StringUtil::ampersand($objJumpToPage->getFrontendUrl($params));
        }

        // Add figure to the template
        if ($moduleModel->addPortraitImage && Validator::isUuid($arrData['singleSRC'] ?? '')) {
            $objFile = FilesModel::findByUuid($arrData['singleSRC']);

            if (null !== $objFile && is_file($module->projectDir.'/'.$objFile->path)) {
                $arrData['hasPortraitImage'] = true;
                $arrData['singleSRC'] = StringUtil::binToUuid($objFile->uuid);

                $figureBuilder = $this->getFigureBuilder($moduleModel, $module->studio);
                $figure = $figureBuilder->fromUuid($objFile->uuid)->build();

                $arrData['figure'] = $module->twig->render(
                    '@ContaoCore/Image/Studio/figure.html.twig',
                    [
                        'figure' => $figure,
                    ]
                );
            }
        }

        // Add figure to the template
        $arrData['hasGallery'] = false;
        $arrData['gallery'] = [];

        if ($moduleModel->addGallery && Validator::isUuid($arrData['singleSRC'] ?? '')) {
            $arrMultiSrc = StringUtil::deserialize($arrData['multiSRC']);

            if (!empty($arrMultiSrc)) {
                foreach ($arrMultiSrc as $uuid) {
                    if (Validator::isBinaryUuid($uuid)) {
                        $objFile = FilesModel::findByUuid($arrData['singleSRC']);

                        if (null !== $objFile && is_file($module->projectDir.'/'.$objFile->path)) {
                            $arrData['hasPortraitImage'] = true;
                            $arrData['singleSRC'] = StringUtil::binToUuid($objFile->uuid);

                            $figureBuilder = $this->getFigureBuilder($moduleModel, $module->studio);
                            $figure = $figureBuilder->fromUuid($objFile->uuid)->build();

                            $arrData['hasGallery'] = true;
                            $arrData['gallery'][] = $module->twig->render(
                                '@ContaoCore/Image/Studio/figure.html.twig',
                                [
                                    'figure' => $figure,
                                ]
                            );
                        }
                    }
                }
            }
        }

        return $arrData;
    }

    protected function getJumpToPage(ModuleModel $moduleModel): ?PageModel
    {
        if (($objTarget = $moduleModel->getRelated('jumpTo')) instanceof PageModel) {
            return $objTarget;
        }

        return null;
    }
}
