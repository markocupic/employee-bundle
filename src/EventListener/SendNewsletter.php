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
use Contao\System;
use Contao\Email;
use Contao\Database;
use Contao\FrontendTemplate;
use Contao\Validator;


/**
 * Class SendNewsletter
 * @package Markocupic\SacpilatusBundle\EventListener
 */
class SendNewsletter
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @var sectionIds
     */
    private $input;

    /**
     * @var ftp_hostname
     */
    private $email;

    /**
     * @var ftp_username
     */
    private $database;


    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
        $this->input = $this->framework->getAdapter(\Contao\Input::class);

        $system = $this->framework->getAdapter(\Contao\System::class);
        $this->database = $system->importStatic('Database');

    }

    /**
     *
     */
    public function sendNewsletter()
    {

        if ($this->input->get('cronjob') == 'true') {
            if ($this->input->get('action') == 'sendNewsletter') {
                $start = time();
                $limit = 25;
                if ($this->input->get('limit') > 0) {
                    $limit = $this->input->get('limit');
                }
                $objMember = $this->database->prepare("SELECT * FROM tl_member WHERE email != ? AND newsletterSent=?")->limit($limit)->execute('', '');
                if(!$objMember->numRows)
                {
                    return;
                }
                while ($objMember->next()) {
                    $this->email = new \Contao\Email();

                    //$this->email->from = 'm.cupic@gmx.ch';
                    $this->email->from = 'geschaeftsstelle@sac-pilatus.ch';

                    $this->email->fromName = 'Geschaeftsstelle SAC Sektion Pilatus';

                    //$this->email->replyTo('m.cupic@gmx.ch');
                    $this->email->replyTo('geschaeftsstelle@sac-pilatus.ch');

                    $this->email->subject = 'Umfrage zum Redesign der Webseite des SAC Pilatus';

                    // HTML
                    $objTemplate = new \Contao\FrontendTemplate('newsletterRelaunchWebsiteSurveyHtml');
                    $objTemplate->firstname = $objMember->firstname;
                    $objTemplate->imageSRC = 'http://sac-kurse.kletterkader.com/files/sac_pilatus/page_assets/newsletter/image-sac-survey.jpg';
                    $objTemplate->surveyLink = 'https://docs.google.com/forms/d/e/1FAIpQLSftI21CwMu6s4gxKykPugg-sSAkEaxBtxzfVG29-D2-F1-UZg/viewform';
                    $this->email->html = $objTemplate->parse();

                    // Text
                    $objTemplate = new \Contao\FrontendTemplate('newsletterRelaunchWebsiteSurveyText');
                    $objTemplate->firstname = $objMember->firstname;
                    $this->email->text = $objTemplate->parse();

                    // Send email
                    if (Validator::isEmail($objMember->email)) {
                        //$this->email->sendTo('m.cupic@gmx.ch');
                        $this->email->sendTo($objMember->email);
                    }

                    // Set flag in tl_member
                    $set = array('newsletterSent' => '1');
                    $this->database->prepare('UPDATE tl_member %s WHERE id=?')->set($set)->execute($objMember->id);

                }
                mail('m.cupic@gmx.ch', $limit . ' E-Mails in ' . round(time() - $start) . ' s.','');
            }
        }
    }
}