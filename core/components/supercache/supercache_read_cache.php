<?php 
  // функция для вывода памяти (можно не использовать)
  function convert($size) {
   $unit=array('b','kb','mb','gb','tb','pb');
   return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
  }
  // функция извлечения массива с файла
  function geFile($key) {
    $value= null;
    if (file_exists($key)) {
      if ($files = @fopen($key, 'rb')) {      
        if (flock($files, LOCK_SH)) {
          $value= @include $key;
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
  // Получаем название ключа для кэша
  $file = md5($_SERVER['REQUEST_URI']);
  // достаём с кэша конфиг файл 
  $system_cache_pach = MODX_CORE_PATH.'cache/system_settings/config.cache.php';
  $valueFile = geFile($system_cache_pach);  
  // формируем путь для нашего кэша
  $q = str_replace('.','_',$_REQUEST[$valueFile['request_param_alias']]);
  $arrayUrl = array_filter(explode('/',$q),function($el){ return !empty($el);});
  $pathUri = implode('/',$arrayUrl);
  if(strlen($pathUri) > 0) $pathUri .= '/';   
  // берём с настроек модекса префикс кэша и время жизни кэша
  $cache_prefix = $valueFile['cache_prefix'];
  //$cache_expires = intval($valueFile['cache_expires']);
  // проверяем. если есть файл в кэше
  switch ($valueFile['cache_handler']) {
    case 'cache.xPDOAPCCache':      
      $cache_key = "static/$pathUri$cache_prefix$file";
      // проверяем, есть ли файл в кэше
      if(apc_exists($cache_key)) {
        // получаем файл с кэша
        $value = apc_fetch($cache_key);
        // Если его время жизни ещё не прошло или cache_expires у нас нуль, то выводим
        if (time() < ($value['time_start'] + $value['expires']) || $value['expires'] == 0) {
            // подсчитываем время и память
            $time = microtime(true) - $tstart ;
            // парсим наш тег [^t^] и заменяем на время
            echo str_replace('[^t^]', 'APCCache: '.round($time,6).' s. :: Memory: '.convert(memory_get_usage() - $start_memory_usage), $value['output']); // Выводим содержимое файла 
            exit;
        } else {
          // удаляем файл кэша
          apc_delete($cache_key);
        }
      }               
    break;
    case 'xPDOFileCache':
      $cache_file = MODX_CORE_PATH."cache/static/$pathUri$file.cache.php"; 
      // проверяем, есть ли файл в кэше      
      if (file_exists($cache_file)) {
        // получаем наш файл
        $value = geFile($cache_file);           
        // Если его время жизни ещё не прошло или cache_expires у нас нуль, то выводим
        if (time() < ($value['time_start'] + $value['expires']) || $value['expires'] == 0) {
            // подсчитываем время и память
            $time = microtime(true) - $tstart ;
            // парсим наш тег [^t^] и заменяем на время
            echo str_replace('[^t^]', 'FileCache: '.round($time,6).' s. :: Memory: '.convert(memory_get_usage() - $start_memory_usage), $value['output']); // Выводим содержимое файла
            exit;
        } else {
          // удаляем файл с кэша
          unlink($cache_file);
        }
      }
    break;
  }

