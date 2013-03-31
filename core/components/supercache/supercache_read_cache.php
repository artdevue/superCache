<?php
/* function to display memory (can not use) */
function convert($size)
{
    $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

/* function retrieve an array from a file */
function geFile($key)
{
    $value = null;
    if (file_exists($key)) {
        if ($files = @fopen($key, 'rb')) {
            if (flock($files, LOCK_SH)) {
                $value = @include $key;
                flock($files, LOCK_UN);
                if ($value === null) {
                    fclose($files);
                    @ unlink($key);
                }
            }
            @fclose($files);
        }
    }
    return $value;
}

$start_memory_usage = memory_get_usage();
/* Create a name for the cache key  */
$file = md5($_SERVER['REQUEST_URI']);
/* We get out of the cache configuration file */
$system_cache_pach = MODX_CORE_PATH . 'cache/system_settings/config.cache.php';
$valueFile = geFile($system_cache_pach);
/* form the way for our cache */
if (isset($_REQUEST[$valueFile['request_param_alias']])) {
    $q = str_replace('.', '_', $_REQUEST[$valueFile['request_param_alias']]);
    $arrayUrl = array_filter(explode('/', $q), function ($el) {
        return !empty($el);
    });
    $pathUri = implode('/', $arrayUrl);
    if (strlen($pathUri) > 0) $pathUri .= '/';
} else {
    $pathUri = 'index/';
}
/* We take in setting MODX, prefix cache and cache lifetime */
$cache_prefix = $valueFile['cache_prefix'];
/* check the type of cache */
switch ($valueFile['cache_handler']) {
    case 'cache.xPDOAPCCache':
        $cache_key = "static/$pathUri$cache_prefix$file";
        /* check if there is a file in the cache */
        if (apc_exists($cache_key)) {
            /* get a file from the cache */
            $value = apc_fetch($cache_key);
            /* If the lifetime of the file has not passed or cache_expires have zero, then the output file */
            if (time() < ($value['time_start'] + $value['expires']) || $value['expires'] == 0) {
                /* We believe the time and memory */
                $time = microtime(true) - $tstart;
                /* parse our tag [^ t ^] and replace it with the time and memory */
                echo str_replace('[^t^]', 'APCCache: ' . round($time, 6) . ' s. :: Memory: ' . convert(memory_get_usage() - $start_memory_usage), $value['output']);
                exit;
            } else {
                /* delete the file cache */
                apc_delete($cache_key);
            }
        }
        break;
    case 'xPDOFileCache':
        $cache_file = MODX_CORE_PATH . "cache/static/$pathUri$file.cache.php";
        /* check if there is a file in the cache */
        if (file_exists($cache_file)) {
            /* get a file from the cache */
            $value = geFile($cache_file);
            /* If the lifetime of the file has not passed or cache_expires have zero, then the output file */
            if (time() < ($value['time_start'] + $value['expires']) || $value['expires'] == 0) {
                /* We believe the time and memory */
                $time = microtime(true) - $tstart;
                /* parse our tag [^ t ^] and replace it with the time and memory */
                echo str_replace('[^t^]', 'FileCache: ' . round($time, 6) . ' s. :: Memory: ' . convert(memory_get_usage() - $start_memory_usage), $value['output']);
                exit;
            } else {
                /* delete the file cache */
                unlink($cache_file);
            }
        }
        break;
}

