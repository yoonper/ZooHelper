<!DOCTYPE HTML>
<html>
<head>
    <title><-$data.title-></title>
    <meta charset="UTF-8">
    <-css=const js/extjs/theme/theme-crisp.css?v=fixed->
    <-css css/style.css->
    <-js=const js/extjs/ext-all.js?v=fixed->
    <-js js/extjs/theme/theme-crisp.js?v=fixed->
    <-js js/extjs/locale-zh_CN.js?v=fixed->
</head>
<body>
<script>
    Ext.Loader.setConfig({enabled: true, disableCaching: false});

    //资源版本号
    Ext.Boot.Entry.prototype.getLoadUrl = function () {
        var url = Ext.Boot.canonicalUrl(this.url);
        if (!this.loadUrl) {
            this.loadUrl = (url + (url.indexOf('?') == -1 ? '?' : '&') + 'v=<-$version->');
        }
        return this.loadUrl;
    };

    //Toast消息组件
    var toast = Ext.create('Ext.Component', {
        delay: 5000,
        initComponent: function () {
            this.createDiv();
            this.callParent();
        },
        createDiv: function () {
            if (!this.div) {
                this.div = Ext.DomHelper.insertFirst(document.body, {}, true);
                this.div.applyStyles({
                    position: 'fixed', right: '30px', bottom: '30px', width: '300px',
                    zIndex: 99999, fontSize: '14px', lineHeight: '25px', letterSpacing: '1px'
                });
            }
        },
        show: function (text, iconCls) {
            var msg = Ext.DomHelper.append(this.div, '<div>' + text + '</div>', true);
            msg.applyStyles({
                marginTop: '5px', padding: '10px 20px', borderRadius: '5px',
                border: '1px solid #CFCFCF', background: '#FFF', color: '#888', wordWrap: 'break-word'
            });
            var icon = Ext.DomHelper.insertFirst(msg, '<div></div>', true);
            icon.addCls('x-title-icon ' + iconCls);
            icon.applyStyles({width: '16px', height: '16px', margin: '-2px 8px 0 0'});
            msg.ghost('t', {delay: this.delay, remove: true});
        },
        success: function (text) {
            this.show(text, 'icon-success');
        },
        error: function (text) {
            this.show(text, 'icon-error');
        }
    })
</script>