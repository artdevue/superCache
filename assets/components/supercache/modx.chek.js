Ext.onReady(function () {
    var mainPanel = Ext.getCmp('modx-panel-resource'),
        data = mainPanel.config.record.properties,
        modxPageSettingsRightBoxLeft = Ext.getCmp('modx-page-settings-right-box-left');
    if (!data) {
        data = {}
    }
    if (!data.supercache) {
        data.supercache = {}
    }
    if (!data.supercache.supercache) {
        data.supercache.supercache = csCacheCheckboxDefaulr
    }

    modxPageSettingsRightBoxLeft.add({
        xtype: 'xcheckbox',
        boxLabel: 'Включить superCache',
        description: 'При активации superCache, вывод ресурса из кэша будет происходить до инициализации парсера модекса',
        hideLabel: true,
        name: 'supercache',
        id: 'modx-resource-supercache',
        inputValue: 1,
        checked: data.supercache.supercache !== undefined && data.supercache.supercache !== null ? parseInt(data.supercache.supercache) : true
    })
});