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


/**
 * Class ValidateForms
 * @package Markocupic\SacpilatusBundle\EventListener
 */
class ValidateForms
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @var
     */
    private $eventStoriesUploadPath;

    /**
     * @var \Contao\CoreBundle\Framework\Adapter
     */
    private $input;

    /**
     * @var
     */
    private $system;

    /**
     * @var
     */
    private $feUser;

    /**
     * @var
     */
    private $database;

    /**
     * @var
     */
    private $calendarEventsModelAdapter;

    /**
     * @var
     */
    private $userModelAdapter;

    /**
     * @var
     */
    private $filesModelAdapter;

    /**
     * ValidateForms constructor.
     * @param ContaoFrameworkInterface $framework
     * @param $eventStoriesUploadPath
     */
    public function __construct(ContaoFrameworkInterface $framework, $eventStoriesUploadPath)
    {
        $this->framework = $framework;

        $this->eventStoriesUploadPath = $eventStoriesUploadPath;

        $this->input = $this->framework->getAdapter(\Contao\Input::class);

        $this->system = $this->framework->getAdapter(\Contao\System::class);

        $this->feUser = $this->system->importStatic('FrontendUser');

        $this->database = $this->system->importStatic('Database');

        $this->calendarEventsModelAdapter = $this->framework->getAdapter(\Contao\CalendarEventsModel::class);

        $this->filesModelAdapter = $this->framework->getAdapter(\Contao\FilesModel::class);

        $this->userModelAdapter = $this->framework->getAdapter(\Contao\UserModel::class);

    }

    /**
     * @param $arrTarget
     */
    public function postUpload($arrTarget)
    {

    }


    /**
     * @param $arrFields
     * @param $formId
     * @param \Form $objForm
     * @return mixed
     */
    public function compileFormFields($arrFields, $formId, $objForm)
    {
        return $arrFields;
    }


    /**
     * @param \Widget $objWidget
     * @param $strForm
     * @param $arrForm
     * @param $objForm
     * @return \Widget
     */
    public function loadFormField(\Contao\Widget $objWidget, $strForm, $arrForm, $objForm)
    {
        if ($arrForm['formID'] == 'form-course-registration') {

            if ($this->input->get('events') != '') {
                $objEvent = $this->calendarEventsModelAdapter->findByIdOrAlias($this->input->get('events'));
                if ($objEvent !== null) {
                    if ($objWidget->name == 'event_name') {
                        $objWidget->value = $objEvent->title;
                    }
                    if ($objWidget->name == 'pid') {
                        $objWidget->value = $objEvent->id;
                    }
                    if ($objWidget->name == 'addedOn') {
                        $objWidget->value = time();
                    }

                    // Add main guide to the email recipient
                    if ($objEvent->mainInstructor) {
                        $objUser = $this->userModelAdapter->findByPk($objEvent->mainInstructor);
                        if ($objUser !== null) {
                            if ($objUser->email != '') {
                                $objForm->recipient = $objUser->email;
                                $objForm->subject = 'Anmeldung für Kurs ' . '"' . $objEvent->title . '"';
                            }
                        }
                    }
                }
            }
        }


        if ($arrForm['formID'] == 'form-write-event-story' && FE_USER_LOGGED_IN && $this->input->get('eventId')) {
            $oEvent = $this->calendarEventsModelAdapter->findByPk($this->input->get('eventId'));
            if ($this->feUser !== null && $oEvent !== null) {
                $objStory = $this->database->prepare('SELECT * FROM tl_calendar_events_story WHERE sacMemberId=? && pid=?')->execute($this->feUser->sacMemberId, $this->input->get('eventId'));
                if ($objStory->numRows) {
                    if ($objWidget->name == 'text') {
                        $objWidget->value = $objStory->text;
                    }

                    if ($objWidget->name == 'youtubeId') {
                        $objWidget->value = $objStory->youtubeId;
                    }
                }
            }
        }
        return $objWidget;
    }


    /**
     * @param $objWidget
     * @param $formId
     * @param $arrData
     * @param \Form $objForm
     * @return mixed
     */
    public function validateFormField(\Contao\Widget $objWidget, $formId, $arrForm, $objForm)
    {
        if ($arrForm['formID'] == 'form-course-registration') {
            if ($objWidget->name == 'sacMemberId') {

                if ($objWidget->value != '') {

                    // Validate sacMemberId
                    $objDb = $this->database->prepare('SELECT * FROM tl_member WHERE sacMemberId=? AND disable=?')->execute($objWidget->value, '');
                    if (!$objDb->numRows) {
                        $objWidget->addError(sprintf('Die Mitgliedernummer "%s" wurde nicht in der Mitgliederdatenbank gefunden.', $objWidget->value));
                        return $objWidget;
                    }


                    // Prevent duplicate entries
                    $objDb = $this->database->prepare('SELECT * FROM tl_calendar_events_member WHERE pid=? AND sacMemberId=?')->execute($this->input->get('events'), $objWidget->value);
                    if ($objDb->numRows) {
                        $objWidget->addError('Für diesen Event liegt von dir bereits eine Anmeldung vor.');
                        return $objWidget;
                    }
                }
            }
        }

        return $objWidget;
    }


    /**
     * @param $arrSubmitted
     * @param $arrLabels
     * @param $arrFields
     * @param \Form $objForm
     */
    public function prepareFormData($arrSubmitted, $arrLabels, $arrFields, $objForm)
    {

    }


    /**
     * @param $arrSet
     * @param \Form $objForm
     * @return mixed
     */
    public function storeFormData($arrSet, $objForm)
    {
        return $arrSet;
    }


    /**
     * @param $arrSubmitted
     * @param $arrForm
     * @param $arrFiles
     * @param $arrLabels
     * @param \Form $objForm
     */
    public function processFormData($arrSubmitted, $arrForm, $arrFiles, $arrLabels, $objForm)
    {

        if ($arrForm['formID'] == 'form-write-event-story') {

            $oEvent = $this->calendarEventsModelAdapter->findByPk($this->input->get('eventId'));
            if ($this->feUser !== null && $oEvent !== null) {
                $set = array(
                    'pid' => $this->input->get('eventId'),
                    'sacMemberId' => $this->feUser->sacMemberId,
                    'youtubeId' => $this->input->post('youtubeId'),
                    'tstamp' => time(),
                    'text' => $this->input->post('text'),
                );
                $objStory = $this->database->prepare('SELECT * FROM tl_calendar_events_story WHERE sacMemberId=? && pid=?')->execute($this->feUser->sacMemberId, $this->input->get('eventId'));
                if ($objStory->numRows) {

                    $this->database->prepare('UPDATE tl_calendar_events_story %s WHERE id=?')->set($set)->execute($objStory->id);

                } else {
                    //$set['addedOn'] = time();
                    $this->database->prepare('INSERT INTO tl_calendar_events_story %s')->set($set)->execute();
                }
            }

            if (FE_USER_LOGGED_IN) {
                // Manage Fileuploads
                if ($this->input->post('attachfiles')) {
                    $eventId = $this->input->get('eventId');
                    $objStory = $this->database->prepare('SELECT * FROM tl_calendar_events_story WHERE sacMemberId=? && pid=?')->execute($this->feUser->sacMemberId, $eventId);
                    if ($objStory->numRows) {
                        $widgetId = $this->input->post('attachfiles');
                        $objFile = json_decode($widgetId[0]);
                        $arrFiles = $objFile->files;
                        $tmpDir = $objFile->addToFile;

                        foreach ($arrFiles as $file) {
                            $strPath = $this->eventStoriesUploadPath . '/tmp/' . $tmpDir . '/' . $file;
                            if (is_file(TL_ROOT . '/' . $strPath)) {
                                $objFile = $this->filesModelAdapter->findByPath($strPath);
                                if ($objFile !== null) {
                                    $targetDir = $this->eventStoriesUploadPath . '/' . $objStory->id;
                                    $fileNewPath = $targetDir . '/' . $objFile->id . '.' . $objFile->extension;
                                    $oFile = new \Contao\File($strPath);
                                    $oFile->resizeTo(1000, 1000, 'proportional');
                                    // Create folder if it does not exist
                                    if (!is_dir(TL_ROOT . '/' . $targetDir)) {
                                        new \Contao\Folder($targetDir);
                                    }
                                    $oFile->copyTo($fileNewPath);
                                    $oFile->delete();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}