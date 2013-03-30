<?php
switch($modx->event->name) {
    case 'OnDocFormPrerender':
        $modx->regClientStartupScript('<script type="text/javascript">
		var csCacheCheckboxDefaulr = '.((boolean)$cs_cache_checkbox_defaulr ? 1 : 0).';
		</script>');
        $modx->regClientStartupScript(MODX_ASSETS_URL.'components/supercache/modx.chek.js');
        break;
    case 'OnBeforeDocFormSave':
        $resource =& $modx->event->params['resource'];
        if (isset($_POST['supercache'])) {
            $resource->setProperties(array('supercache'=>$_POST['supercache']),'supercache',false);
        }
        break;
    case 'OnWebPageComplete':
        $supercache = &$modx->resource->getProperty('supercache','supercache',0);
        if($supercache == 0) break;
        include_once MODX_CORE_PATH.'components/supercache/supercache.onwebpagecomplete.php';
        break;
    case 'OnSiteRefresh':
    case 'OnDocFormSave':
        if($modx->cacheManager->refresh(array('static'=> array()))) {
            $modx->log(modX::LOG_LEVEL_INFO,'SuperCache clear cache. '.$modx->lexicon('refresh_success'));
        }
        break;
}