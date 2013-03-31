--------------------
Extra: superCache
--------------------
Version: 0.0.1 beta
Since: March 30th, 2013
Author: Valentin Rasulov <info@artdevue.com>

Display a static cache before initializing MODX Revolution.


На данный момент плагин работает с файловым и APC кэшем.
После установки пакета, вставьте инклуд в файл index.php, примерно 40 строка после
if (!defined('MODX_CORE_PATH')) define('MODX_CORE_PATH', dirname(__FILE__) . '/core/');
вставить:
include_once MODX_CORE_PATH.'components/supercache/supercache_read_cache.php';
перед инклудом классов MODX

При создании ресурса, во вкладке "Настройки", у вас появиться чекбокс Включить superCache.

Настройки по умолчанию находятся в вкладке "Параметры" плагина superCache
	cs_cache_checkbox_defaulr - ставить чекбокс superCache активный при создании нового ресурса
	sc_expires - время жизни кэша (в секундах), если нуль (0), то кэш будет удалён при обнавлении ресурса или очистки кэша
	sc_out - два параметра выбора:
		Treated not cached tags   - Выводиться полностью закэшированная страница (рекомендуеться)
		Untreated not cached tags - Не кэшированные теги выводяться не обработанные (для опытных пользователей)


At the moment the plugin works with the file and the APC cache.
After installing the package, insert inkludit the file index.php, line after about 40
if (!defined('MODX_CORE_PATH')) define('MODX_CORE_PATH', dirname(__FILE__) . '/core/');
insert:
include_once MODX_CORE_PATH.'components/supercache/supercache_read_cache.php';
before classes include MODX

When you create a resource in the "Settings" tab, you will have the checkbox Enable superCache.

The default settings are in the "Settings" tab plug superCache
	cs_cache_checkbox_defaulr - put checkbox superCache active when a new resource
	sc_expires - cache lifetime (in seconds), if the zero (0), then the cache will be deleted when updatings resource or clear the cache
	sc_out - choice of two options:
		Treated not cached tags   - Display a Cached page (recommend)
		Untreated not cached tags - Not cached tags are excreted not treated (for advanced users)