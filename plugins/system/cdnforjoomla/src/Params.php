<?php
/**
 * @package         CDN for Joomla!
 * @version         6.1.1
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Plugin\System\CDNforJoomla;

defined('_JEXEC') or die;

use JUri;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\RegEx as RL_RegEx;

class Params
{
	protected static $params = null;
	protected static $domain = null;
	protected static $sets   = null;

	public static function get()
	{
		if ( ! is_null(self::$params))
		{
			return self::$params;
		}

		self::$params = RL_Parameters::getInstance()->getPluginParams('cdnforjoomla');

		return self::$params;
	}

	public static function getSets()
	{
		if ( ! is_null(self::$sets))
		{
			return self::$sets;
		}

		$sets = [self::getSet()];
		self::removeEmptySets($sets);

		self::$sets = $sets;

		return self::$sets;
	}

	private static function removeEmptySets(&$sets)
	{
		foreach ($sets as $i => $set)
		{
			if (empty($set) || empty($set->cdns) || empty($set->searches))
			{
				unset($sets[$i]);
			}
		}
	}

	private static function getSet($setid = 1)
	{
		$params = self::get();

		$setid = ($setid <= 1) ? '' : '_' . (int) $setid;

		if ($setid && ( ! isset($params->{'use_extra' . $setid}) || ! $params->{'use_extra' . $setid}))
		{
			return false;
		}


		$filetypes = self::getFileTypes($params->{'filetypes' . $setid});

		if (empty($filetypes))
		{
			return false;
		}

		$set = (object) [];

		$set->cdn = rtrim($params->{'cdn' . $setid}, '/');

		$set->protocol = 'http://';

		$set->filetypes         = $filetypes;
		$set->ignorefiles       = self::getFileTypes($params->{'ignorefiles' . $setid});
		$set->enable_in_scripts = $params->{'enable_in_scripts' . $setid};


		$set->root = trim($params->{'root' . $setid}, '/');

		$set->searches    = self::getFiletypeSearches($set);
		$set->js_searches = self::getFiletypeSearchesJavascript($set);
		$set->cdns        = self::getCdnPaths($set->cdn);

		return $set;
	}

	public static function getFileTypes($filetypes)
	{
		return explode(',',
			str_replace(
				["\n", '\n', ' ', ',.'],
				[',', ',', '', ','],
				trim($filetypes)
			)
		);
	}


	/*
	 * Searches are replaced by:
	 * '\1http(s)://' . $this->params->cdn . '/\3\4'
	 * \2 is used to reference the possible starting quote
	 */
	private static function getFiletypeSearches($settings)
	{
		if (empty($settings->filetypes))
		{
			return [];
		}

		$url = self::getUrlRegex($settings->filetypes, $settings->root);

		return self::getSearchesByUrl($url);
	}

	/*
	 * Searches are replaced by:
	 * '\1http(s)://' . $this->params->cdn . '/\3\4'
	 * \2 is used to reference the possible starting quote
	 */
	private static function getFiletypeSearchesJavascript($settings)
	{
		if (empty($settings->filetypes))
		{
			return [];
		}

		$url = self::getUrlRegex($settings->filetypes, $settings->root);

		return self::getSearchesJavascriptByUrl($url);
	}

	/*
	 * Searches are replaced by:
	 * '\1http(s)://' . [cdn] . '/\3\4'
	 * \2 is used to reference the possible starting quote
	 */
	private static function getUrlRegex($filetypes, $root)
	{
		// Domain url or root path
		$roots   = [];
		$roots[] = 'LSLASH';
		$roots[] = str_replace(['http\\://', 'https\\://'], '(?:https?\:)?//', RL_RegEx::quote(JUri::root()));

		if (JUri::root(1))
		{
			$roots[] = RL_RegEx::quote(JUri::root(1) . '/');
		}

		$filetypes = implode('|', $filetypes);
		$root      = RL_RegEx::quote($root);

		return
			'(?:' . implode('|', $roots) . ')' . $root . '\/?'
			. '([a-z0-9-_]+(?:/[^ \?QUOTES]+|[^ \?\/QUOTES]+)\.(?:' . $filetypes . ')(?:\?[^QUOTES]*)?)';
	}

	private static function getSearchesJavascriptByUrl($url)
	{
		$url_regex = '\s*' . str_replace('QUOTES', '"\'', $url) . '\s*';
		$url_regex = str_replace('LSLASH', '', $url_regex);

		$searches = [];

		$searches[] = '((["\']))' . $url_regex . '(["\'])'; // "..."

		return $searches;
	}

	private static function getSearchesByUrl($url)
	{
		$tag_attribs = self::getSearchTagAttributes();

		$url_regex                 = '\s*' . str_replace('QUOTES', '"\'', $url) . '\s*';
		$url_regex                 = str_replace('LSLASH', '/?', $url_regex);
		$url_regex_can_have_spaces = str_replace('[^ ', '[^', $url_regex);

		$searches = [];

		// attrib="..."
		$searches[] = '((?:' . $tag_attribs . ')\s*(["\']))' . $url_regex_can_have_spaces . '((?: [^"\']*)?\2)';
		// attrib=...
		$searches[] = '((?:' . $tag_attribs . ')())' . $url_regex . '([\s|>])';
		// url(...) or url("...")
		$searches[] = '(url\(\s*((?:["\'])?))' . $url_regex_can_have_spaces . '(\2\s*[,\)])';
		// load...(...) or load...("...")
		$searches[] = '(load[a-z]*\(\s*((?:["\'])?))' . $url_regex_can_have_spaces . '(\2\s*[,\)])';
		// "image" : "..."
		$searches['image'] = '((["\'])image\2\s*:\s*\2)' . $url_regex_can_have_spaces . '(\2)';

		// add ')' to the no quote checks
		$url_regex        = '\s*' . str_replace('QUOTES', '"\'\)', $url) . '\s*';
		$searches['url('] = '(url\(\s*())' . $url_regex . '(\s*\))'; // url(...)

		return $searches;
	}

	private static function getSearchTagAttributes()
	{
		$attributes = [
			'href=',
			'src=',
			'srcset=',
			'data-[a-z0-9-_]+=',
			'longdesc=',
			'poster=',
			'@import',
			'name="movie" value=',
			'property="og:image" content=',
			'itemprop="image" content=',
			'TileImage" content=',
			'rel="{\'link\':',
		];

		return str_replace(['"', '=', ' '], ['["\']?', '\s*=\s*', '\s+'], implode('|', $attributes));
	}

	private static function getCdnPaths($cdn)
	{
		$cdns = explode(',', $cdn);

		$paths = [];

		foreach ($cdns as $i => $cdn)
		{
			if (empty($cdn))
			{
				continue;
			}

			$cdn = RL_RegEx::replace('^.*\://', '', trim($cdn));

			if (empty($cdn))
			{
				continue;
			}

			$paths[] = $cdn;
		}

		return $paths;
	}

}
