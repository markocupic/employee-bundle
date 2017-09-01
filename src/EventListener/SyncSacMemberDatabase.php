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
use Contao\File;
use Contao\Database;
use Contao\Environment;
use Contao\Controller;


/**
 * Class SyncSacMemberDatabase
 * @package Markocupic\SacpilatusBundle\EventListener
 */
class SyncSacMemberDatabase
{
	/**
	 * @var ContaoFrameworkInterface
	 */
	private $framework;

	/**
	 * @var sectionIds
	 */
	private $sectionIds;

	/**
	 * @var ftp_hostname
	 */
	private $ftp_hostname;

	/**
	 * @var ftp_username
	 */
	private $ftp_username;

	/**
	 * @var ftp_password
	 */
	private $ftp_password;

	/**
	 * @var \Contao\CoreBundle\Framework\Adapter
	 */
	private $input;


	/**
	 * @var \Contao\CoreBundle\Framework\Adapter
	 */
	private $environment;


	/**
	 * @var
	 */
	private $database;


	/**
	 * Constructor.
	 *
	 * @param ContaoFrameworkInterface $framework
	 */
	public function __construct(ContaoFrameworkInterface $framework, $sectionIds, $ftp_hostname, $ftp_username, $ftp_password)
	{
		$this->framework = $framework;
		$this->sectionIds = $sectionIds;
		$this->ftp_hostname = $ftp_hostname;
		$this->ftp_username = $ftp_username;
		$this->ftp_password = $ftp_password;

		$this->input = $this->framework->getAdapter(\Contao\Input::class);
		$this->environment = $this->framework->getAdapter(\Contao\Environment::class);
		$system = $this->framework->getAdapter(\Contao\System::class);
		$this->database = $system->importStatic('Database');

	}

	/**
	 *
	 */
	public function syncSacMemberDatabase()
	{

		if ($this->input->get('cronjob') == 'true')
		{
			if ($this->input->get('action') == 'syncSacMemberDatabase')
			{

				// Load files fromftp
				$this->loadDataFromFtp();
				// Then sync with tl_member
				$this->syncContaoDatabase();
			}
		}
	}

	/**
	 * @throws \Exception
	 */
	private function loadDataFromFtp()
	{


		// Run once per day
		$objDbLog = $this->database->prepare('SELECT * FROM tl_log WHERE action=? ORDER BY tstamp DESC')->limit(1)->execute('TL_CRON_SYNC_SAC_MEMBER_DATABASE');
		if ($objDbLog->numRows)
		{
			if (($objDbLog->tstamp + 24 * 60 * 60) > time())
			{
				return;
			}
		}


		$connId = ftp_connect($this->ftp_hostname);
		ftp_login($connId, $this->ftp_username, $this->ftp_password);

		foreach ($this->sectionIds as $sectionId)
		{
			// Variablen definieren
			$localFile = TL_ROOT . '/system/tmp/Adressen_0000' . $sectionId . '.csv';
			$remoteFile = 'Adressen_0000' . $sectionId . '.csv';


			if (ftp_get($connId, $localFile, $remoteFile, FTP_BINARY))
			{
				//echo "$localFile wurde erfolgreich geschrieben";
			}
			else
			{
				throw new \Exception("Tried to open FTP connection.");
			}
		}
		ftp_close($connId);
	}

	/**
	 *
	 */
	private function syncContaoDatabase()
	{
		$startTime = time();


		// Run once per day
		$objDbLog = $this->database->prepare('SELECT * FROM tl_log WHERE action=? ORDER BY tstamp DESC')->limit(1)->execute('TL_CRON_SYNC_SAC_MEMBER_DATABASE');
		if ($objDbLog->numRows)
		{
			if (($objDbLog->tstamp + 24 * 60 * 60) > time())
			{
				return;
			}
		}

		$objDb = $this->database->execute('SELECT sacMemberId FROM tl_member');
		$arrMemberIDS = $objDb->fetchEach('sacMemberId');

		$arrMember = array();
		foreach ($this->sectionIds as $sectionId)
		{

			$objFile = new \Contao\File('system/tmp/Adressen_0000' . $sectionId . '.csv');
			if ($objFile !== null)
			{
				$arrFile = $objFile->getContentAsArray();
				foreach ($arrFile as $line)
				{
					// End of line
					if (strpos($line, '* * * Dateiende * * *') !== false)
					{
						continue;
					}

					$arrLine = explode('$', $line);
					$set = array();
					$set['sacMemberId'] = intval($arrLine[0]);
					$set['username'] = intval($arrLine[0]);
					// Mehrere Sektionsmitgliedschaften möglich
					$set['sectionId'] = array(intval($arrLine[1]));
					$set['lastname'] = $arrLine[2];
					$set['firstname'] = $arrLine[3];
					$set['addressExtra'] = $arrLine[4];
					$set['street'] = trim($arrLine[5]);
					$set['streetExtra'] = $arrLine[6];
					$set['postal'] = $arrLine[7];
					$set['city'] = $arrLine[8];
					$set['country'] = strtolower($arrLine[9]) == '' ? 'ch' : strtolower($arrLine[9]);
					$set['dateOfBirth'] = strtotime($arrLine[10]);
					$set['phoneBusiness'] = $arrLine[11];
					$set['phone'] = $arrLine[12];
					$set['mobile'] = $arrLine[14];
					$set['fax'] = $arrLine[15];
					$set['email'] = $arrLine[16];
					$set['gender'] = strtolower($arrLine[17]) == 'weiblich' ? 'female' : 'male';
					$set['profession'] = $arrLine[18];
					$set['language'] = strtolower($arrLine[19]) == 'd' ? 'de' : strtolower($arrLine[19]);
					$set['entryYear'] = $arrLine[20];
					$set['membershipType'] = $arrLine[23];
					$set['sectionInfo1'] = $arrLine[24];
					$set['sectionInfo2'] = $arrLine[25];
					$set['sectionInfo3'] = $arrLine[26];
					$set['sectionInfo4'] = $arrLine[27];
					$set['debit'] = $arrLine[28];
					$set['memberStatus'] = $arrLine[29];
					$set['tstamp'] = time();
					$set['disable'] = '';

					$set = array_map(function ($value)
					{
						if (!is_array($value))
						{
							$value = trim($value);
							return utf8_encode($value);
						}
						return $value;

					}, $set);

					// Check if the member is already in the array
					if (isset($arrMember[$set['sacMemberId']]))
					{
						$arrMember[$set['sacMemberId']]['sectionId'] = array_merge($arrMember[$set['sacMemberId']]['sectionId'], $set['sectionId']);
						//die(print_r($arrMember[$set['sacMemberId']], true));
					}
					else
					{
						$arrMember[$set['sacMemberId']] = $set;
					}
				}

			}
		}

		// Set tl_member active to ''
		$this->database->prepare('UPDATE tl_member SET disable=?')->execute('1');

		$i = 0;
		foreach ($arrMember as $sacMemberId => $arrValues)
		{
			$arrValues['sectionId'] = serialize($arrValues['sectionId']);
			if (!in_array($sacMemberId, $arrMemberIDS))
			{
				$this->database->prepare('INSERT INTO tl_member %s')->set($arrValues)->execute();
			}
			else
			{
				$this->database->prepare('UPDATE tl_member %s WHERE sacMemberId=?')->set($arrValues)->execute($sacMemberId);
			}


			// Log, if sync has finished without errors (max script execution time!!!!)
			$i++;
			if ($i == count($arrMember))
			{
				$duration = time() - $startTime;
				$objAgent = $this->environment->get('agent');
				$set = array(
					'tstamp' => time(),
					'source' => 'BE',
					'action' => 'TL_CRON_SYNC_SAC_MEMBER_DATABASE',
					'func' => __METHOD__,
					'ip' => $this->environment->get('ip'),
					'browser' => $objAgent->string,
					'text' => 'Finished syncing SAC member database with tl_member. Synced ' . count($arrMember) . ' entries. Duration: ' . $duration . ' s'
				);
				$this->database->prepare('INSERT INTO tl_log %s')->set($set)->execute();
			}
		}
	}
}