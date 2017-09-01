<?php

/**
 * SAC Pilatus web plugin
 * Copyright (c) 2008-2017 Marko Cupic
 * @package sacpilatus-bundle
 * @author Marko Cupic m.cupic@gmx.ch, 2017
 * @link    https://sac-kurse.kletterkader.com
 */

namespace Markocupic\SacpilatusBundle\EventListener;


use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Input;
use Contao\FilesModel;
use Contao\Files;
use Contao\Folder;
use Contao\Database;
use Contao\User;
use Contao\BackendUser;
use Contao\UserModel;


/**
 * Class PostLogin
 * @package Markocupic\SacpilatusBundle\EventListener
 */
class PostLogin
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;


    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
        $this->input = $this->framework->getAdapter(Input::class);
    }

    /**
     * @param User $user
     */
    public function prepareBeUserAccount(User $user)
    {

        // Check all users
        if ($user instanceof BackendUser)
        {
            // Create user directories
            $objUser = Database::getInstance()->prepare('SELECT * FROM tl_user')->execute();
            while ($objUser->next())
            {
                new Folder(SACP_BE_USER_DIRECTORY_ROOT . '/' . $objUser->username);
                new Folder(SACP_BE_USER_DIRECTORY_ROOT . '/' . $objUser->username . '/avatar');
                new Folder(SACP_BE_USER_DIRECTORY_ROOT . '/' . $objUser->username . '/documents');
                new Folder(SACP_BE_USER_DIRECTORY_ROOT . '/' . $objUser->username . '/images');

                // Copy default avatar
                if (!is_file(TL_ROOT . '/' . SACP_BE_USER_DIRECTORY_ROOT . '/' . $objUser->username . '/avatar/default.jpg'))
                {
                    Files::getInstance()->copy(SACP_BE_USER_DIRECTORY_ROOT . '/new/avatar/default.jpg', SACP_BE_USER_DIRECTORY_ROOT . '/' . $objUser->username . '/avatar/default.jpg');
                }

                // Add filemount for the user directory
                $strFolder = SACP_BE_USER_DIRECTORY_ROOT . '/' . $objUser->username;
                $objFile = FilesModel::findByPath($strFolder);
                $arrFileMounts = unserialize($objUser->filemounts);
                $arrFileMounts[] = $objFile->uuid;
                $userModel = UserModel::findByPk($objUser->id);
                if ($userModel !== null)
                {
                    $userModel->filemounts = serialize(array_unique($arrFileMounts));
                    $userModel->inherit = 'extend';
                    $userModel->save();
                }
            }
        }

    }
}