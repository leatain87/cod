<?php

/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
 * optmized downloads
 *
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2014 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

class JchPlatformCache implements JchInterfaceCache
{
	protected static $lifetime = 1440;

        /**
         * 
         * @param type $id
         * @param type $lifetime
         * @return type
         */
        public static function getCache($id)
        {
                $oCache = self::getCacheObject('output');
                $aCache = $oCache->get($id);

                if ($aCache === false)
                {
			$oCache->clean('plg_jch_optimize');

                        return false;
                }

                return $aCache['result'];
        }

        /**
         * 
         * @param type $id
         * @param type $lifetime
         * @param type $function
         * @param type $args
         * @return type
         */
        public static function getCallbackCache($id, $function, $args)
        {
                $oCache = self::getCacheObject('callback');
                $oCache->get($function, $args, $id);

		//Joomla! doesn't check if the cache is stored so we gotta check ourselves
		return self::getCache($id);
        }

        /**
         * 
         * @param type $type
         * @return type
         */
        public static function getCacheObject($type='callback')
        {
                $aOptions = array(
                        'defaultgroup' => 'plg_jch_optimize',
                        'checkTime'    => true,
                        'application'  => 'site',
                        'language'     => 'en-GB',
                        'cachebase'    => JPATH_SITE . '/cache',
                        'storage'      => 'file'
                );

                $oCache = JCache::getInstance($type, $aOptions);

                $oCache->setCaching(true);
                $oCache->setLifeTime(self::$lifetime);

                return $oCache;
        }
        
        /**
         * 
         * @param type $lifetime
         */
        public static function gc()
        {
                $oCache = self::getCacheObject('callback');
                $oCache->gc();

		$staticcachepath = JchPlatformPaths::cachePath(false);

		if (file_exists($staticcachepath))
		{
			$options = array(
				'cachebase' => $staticcachepath,
				'lifetime' => self::$lifetime,
				'storage' => 'file'
			);

			$oStaticCache = JCache::getInstance('output', $options);
			$oStaticCache->gc();
		}

		//Delete page cache
		$oJcache = JCache::getInstance();
		$oJcache->clean('page');
        }

}
