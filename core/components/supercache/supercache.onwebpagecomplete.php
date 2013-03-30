<?php
$file_cache = false;
$handler = false;
// получаем с конфига параметры
$cache_prefix = $modx->getOption('cache_prefix');
$cache_handler = $modx->getOption('cache_handler');
// формируем ключ кэша
$file = md5($_SERVER['REQUEST_URI']);
// очишаем на url
$q = str_replace('.','_',$_REQUEST[$modx->getOption('request_param_alias')]);

// формируем путь для нашего кэша
$arrayUrl = array_filter(explode('/',$q),function($el){ return !empty($el);});
$pathUri = implode('/',$arrayUrl);
$cache_path = 'static/'.$pathUri;
// проверяем какой кэш и существует ли файл в кэше
switch ($cache_handler) {
	case 'cache.xPDOAPCCache':
		if(apc_exists($cache_path.'/'.$cache_prefix.$file)) {
			$file_cache = true;			
		}
		$handler = true;
	break;
	case 'xPDOFileCache':
		if(file_exists(MODX_CORE_PATH.'cache/'.$cache_path.'/'.$file.'.cache.php')) {
			$file_cache = true;			
		}
		$handler = true;
	break;
}
// если файла нету в кэше и кэш у нас активный, то пишем вывод в кэш
if($file_cache == false && $handler == true && $modx->getCacheManager()) {
	if(isset($sc_out) && $sc_out == 1) {
		$output = &$modx->resource->_output;
	} else {
		$output = &$modx->resource->_content;
	}
	$cacheArray = array('output'=>$output, 'time_start'=>time(), 'expires' => $sc_expires);
	$modx->cacheManager->set($file,$cacheArray,0,array(
	xPDO::OPT_CACHE_KEY => $cache_path,
	xPDO::OPT_CACHE_HANDLER => $modx->getOption('cache_resource_handler', null, $modx->getOption(xPDO::OPT_CACHE_HANDLER)),
	xPDO::OPT_CACHE_FORMAT => (integer) $modx->getOption('cache_resource_format', null, $modx->getOption(xPDO::OPT_CACHE_FORMAT, null, xPDOCacheManager::CACHE_PHP)),
	));
}