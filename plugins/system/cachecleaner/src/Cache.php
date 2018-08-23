<?php
/**
 * @package         Cache Cleaner
 * @version         6.3.0
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Plugin\System\CacheCleaner;

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

use JFactory;
use JFile;
use JFolder;
use JHttpFactory;
use JText;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Language as RL_Language;

class Cache
{
	static $show_message = true;
	static $message      = '';
	static $error        = '';
	static $thirdparties = ['jre', 'jotcache', 'siteground', 'maxcdn', 'keycdn', 'cdn77', 'cloudflare'];

	public static function clean()
	{

		if ( ! self::getCleanType())
		{
			return false;
		}

		// Run the main purge actions
		$result = self::purge();

		// only handle messages in html
		if ( ! RL_Document::isHtml())
		{
			return false;
		}

		if (JFactory::getApplication()->input->getInt('break'))
		{
			die(
			str_replace('<br>',
				' - ',
				$result->error ?: '+' . $result->message
			)
			);
		}

		if (self::$show_message && $result->message)
		{
			JFactory::getApplication()->enqueueMessage(
				$result->error ?: $result->message,
				($result->error ? 'error' : 'message')
			);
		}
	}

	private static function getCleanType()
	{
		$params = Params::get();

		$cleancache = trim(JFactory::getApplication()->input->getString('cleancache'));

		// Clean via url
		if ( ! empty($cleancache))
		{
			// Return if on frontend and no secret url key is given
			if (RL_Document::isClient('site') && $cleancache != $params->frontend_secret)
			{
				return '';
			}

			// Return if on login page
			if (RL_Document::isClient('administrator') && JFactory::getUser()->get('guest'))
			{
				return '';
			}

			if (JFactory::getApplication()->input->getWord('src') == 'button')
			{
				return 'button';
			}

			self::$show_message = true;

			if (RL_Document::isClient('site') && $cleancache == $params->frontend_secret)
			{
				self::$show_message = $params->frontend_secret_msg;
			}

			return 'clean';
		}

		// Clean via save task
		if (self::passTask())
		{
			return 'save';
		}


		return '';
	}

	private static function passTask()
	{
		$params = Params::get();

		if ( ! $task = JFactory::getApplication()->input->get('task'))
		{
			return false;
		}

		$task = explode('.', $task, 2);
		$task = isset($task[1]) ? $task[1] : $task[0];
		if (strpos($task, 'save') === 0)
		{
			$task = 'save';
		}

		$tasks = array_diff(array_map('trim', explode(',', $params->auto_save_tasks)), ['']);

		if (empty($tasks) || ! in_array($task, $tasks))
		{
			return false;
		}

		if (RL_Document::isClient('administrator') && $params->auto_save_admin)
		{
			self::$show_message = $params->auto_save_admin_msg;

			return true;
		}

		if (RL_Document::isClient('site') && $params->auto_save_front)
		{
			self::$show_message = $params->auto_save_front_msg;

			return true;
		}

		return false;
	}

	public static function purge()
	{
		$params = Params::get();

		// Joomla cache
		if (self::passType('purge'))
		{
			Cache\Joomla::purge();
		}


		// Folders
		if (self::passType('clean_tmp'))
		{
			Cache\Folders::purge_tmp();
		}

		// Purge OPcache
		if (self::passType('purge_opcache'))
		{
			Cache\Joomla::purgeOPcache();
		}

		// Purge expired cache
		if (self::passType('purge'))
		{
			Cache\Joomla::purgeExpired();
		}

		// Purge update cache
		if (self::passType('purge_updates'))
		{
			Cache\Joomla::purgeUpdates();
		}


		return self::getResult();
	}

	private static function passType($type)
	{
		$params = Params::get();

		if (empty($params->{$type}))
		{
			return false;
		}

		if ($params->{$type} == 2 && self::getCleanType() != 'button')
		{
			return false;
		}

		return true;
	}


	public static function getResult($show_size = null)
	{
		$show_size = ! is_null($show_size) ? $show_size : Params::get()->show_size;

		// Load language for messaging
		RL_Language::load('mod_cachecleaner');

		$result = (object) [
			'error'   => self::getError(),
			'message' => self::$message ?: JText::_('CC_CACHE_CLEANED'),
		];

		if ($result->error)
		{
			$error = JText::_('CC_NOT_ALL_CACHE_COULD_BE_REMOVED');
			$error .= $result->error !== true ? '<br>' . $result->error : '';

			$result->error = $error;

			return $result;
		}

		if ($show_size && Cache\Cache::getSize())
		{
			$result->message .= ' (' . Cache\Cache::getSize() . ')';
		}

		return $result;
	}

	public static function getMessage()
	{
		return self::$message;
	}

	public static function getError()
	{
		return self::$error;
	}

	public static function setMessage($message = '')
	{
		self::$message = $message;
	}

	public static function setError($error = '')
	{
		self::$error = $error;
	}

	public static function addMessage($message = '')
	{
		self::$message .= self::$message ? '<br>' : '';
		self::$message .= $message;
	}

	public static function addError($error = '')
	{
		self::$error .= self::$error ? '<br>' : '';
		self::$error .= $error;
	}
}
