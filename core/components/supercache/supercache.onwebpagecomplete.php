<?php
$file_cache = false;
$handler = false;
$sc_expires = isset($sc_expires) ? $sc_expires : 0;

/* get with the config settings */
$cache_prefix = $modx->getOption('cache_prefix');
$cache_handler = $modx->getOption('cache_handler');
/* form the cache key */
$file = md5($_SERVER['REQUEST_URI']);
/* clear the url of debris */
if (isset($_REQUEST[$modx->getOption('request_param_alias')])) {
    $q = str_replace('.', '_', $_REQUEST[$modx->getOption('request_param_alias')]);
    $arrayUrl = array_filter(explode('/', $q), function ($el) {
        return !empty($el);
    });
    $pathUri = implode('/', $arrayUrl);
} else {
    $pathUri = 'index';
}
$cache_path = 'static/' . $pathUri;
/* check the cache, and whether the cache file */
switch ($cache_handler) {
    case 'cache.xPDOAPCCache':
        if (apc_exists($cache_path . '/' . $cache_prefix . $file)) {
            $file_cache = true;
        }
        $handler = true;
        break;
    case 'xPDOFileCache':
        if (file_exists(MODX_CORE_PATH . 'cache/' . $cache_path . '/' . $file . '.cache.php')) {
            $file_cache = true;
        }
        $handler = true;
        break;
}
/* The cache file is not present, and cache activity, then we write the output to a cache */
if ($file_cache == false && $handler == true && $modx->getCacheManager()) {
    if (isset($sc_out) && $sc_out == 1) {
        $output = & $modx->resource->_output;
    } else {
        $output = & $modx->resource->_content;
    }
    $cacheArray = array('output' => $output, 'time_start' => time(), 'expires' => $sc_expires);
    $modx->cacheManager->set($file, $cacheArray, 0, array(
        xPDO::OPT_CACHE_KEY => $cache_path,
        xPDO::OPT_CACHE_HANDLER => $modx->getOption('cache_resource_handler', null, $modx->getOption(xPDO::OPT_CACHE_HANDLER)),
        xPDO::OPT_CACHE_FORMAT => (integer)$modx->getOption('cache_resource_format', null, $modx->getOption(xPDO::OPT_CACHE_FORMAT, null, xPDOCacheManager::CACHE_PHP)),
    ));
}