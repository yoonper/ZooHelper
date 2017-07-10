<-include header->
<script>
    Ext.onReady(function () {
        var tree = Ext.create('Ext.tree.Panel', {
            region: 'west', title: '节点列表', iconCls: 'icon-folder', width: 300, split: true,
            store: {proxy: {type: 'ajax', url: 'index/tree'}, root: {id: '/', text: '/', expanded: true}},
            listeners: {
                itemclick: function (t, node) {
                    Ext.Ajax.request({
                        url: 'index/get?node=' + node.id,
                        success: function (data) {
                            data = Ext.util.JSON.decode(data.responseText);
                            Ext.getCmp('btn-edit').setDisabled(!data.success);
                            Ext.getCmp('content').setValue(data.success ? data.data : '读取失败..');
                        }
                    });
                },
                itemcollapse: function (node) {
                    node.set('loaded', false);
                }
            }
        });

        var container = Ext.create('Ext.container.Container', {
            renderTo: Ext.getBody(), height: '100%', layout: 'border', defaults: {split: true, splitterResize: true},
            items: [
                {
                    region: 'north', height: 45, xtype: 'toolbar',
                    items: [
                        {xtype: 'image', src: '<-img img/logo.png->', height: 16, width: 16, margin: '0 10'},
                        {
                            xtype: 'label', text: 'ZooHelper',
                            style: 'font-family:verdana !important;font-size:15px;letter-spacing:5px;'
                        }, '->',
                        {
                            text: '工具说明', iconCls: 'icon-code', margin: '0 20 0 0',
                            handler: function () {
                                Ext.Msg.show({
                                    title: this.text, iconCls: this.iconCls,
                                    message: 'Email：yonper@qq.com<br/>' +
                                    '<a href="http://www.yoonper.com" target="_blank">http://www.yoonper.com</a>'
                                });
                            }
                        }
                    ]
                },
                tree,
                Ext.create('Ext.form.Panel', {
                    region: 'center', title: '节点内容', layout: 'fit',
                    iconCls: 'icon-content', bodyStyle: {padding: '0 20px 20px 20px'},
                    tbar: [
                        '->', {text: '节点信息', iconCls: 'icon-info', handler: info},
                        '-', {text: '删除节点', iconCls: 'icon-del', handler: del},
                        '-', {text: '新增节点', iconCls: 'icon-add', handler: add},
                        '-', {text: '更新节点', id: 'btn-edit', iconCls: 'icon-edit', handler: set, margin: '20 20 0 0'}
                    ],
                    items: [{id: 'content', xtype: 'textarea'}]
                })
            ]
        });

        //选中节点
        function selection() {
            if (tree.selection != null) {
                return tree.selection;
            } else {
                toast.error('没有选中节点！');
                return false;
            }
        }

        //新增节点
        function add() {
            var node = selection();
            if (!node) return false;
            Ext.MessageBox.prompt({
                title: '请输入节点名', iconCls: 'icon-add', prompt: true, buttons: Ext.Msg.OKCANCEL,
                fn: function (btn, text) {
                    if (btn != 'ok') return false;
                    Ext.Ajax.request({
                        url: 'index/add', method: 'post', params: {path: node.data.id, node: text},
                        success: function (data) {
                            data = Ext.util.JSON.decode(data.responseText);
                            if (!data.success) {
                                toast.error(data.data);
                                return;
                            }
                            node.expand();
                            node.appendChild(data.data);
                            toast.success('新增节点成功！');
                        }
                    });
                }
            });
        }

        //删除节点
        function del() {
            var node = selection();
            if (!node) return false;
            var msg = '确定删除 <span style="color:#F66">' + node.data.id + '</span> 节点？';
            Ext.Msg.show({
                title: '删除确认', iconCls: 'icon-del', message: msg, buttons: Ext.Msg.OKCANCEL,
                fn: function (btn) {
                    if (btn != 'ok') return false;
                    Ext.Ajax.request({
                        url: 'index/del?node=' + node.data.id,
                        success: function (data) {
                            data = Ext.util.JSON.decode(data.responseText);
                            if (!data.success) {
                                toast.error(data.data);
                                return;
                            }
                            node.parentNode.removeChild(node);
                            toast.success(data.data);
                        }
                    });
                }
            });
        }

        //更新节点
        function set() {
            var node = selection().data;
            if (!node) return false;
            var content = Ext.getCmp('content').getValue();
            var msg = '确定更新以下信息到 <span style="color:#F66">' + node.id + '</span> 节点？';
            msg += '<textarea readonly id="input-update">' + content + '</textarea>';
            Ext.Msg.show({
                title: '更新确认', iconCls: 'icon-edit', message: msg,
                buttons: Ext.Msg.OKCANCEL, width: 800, maxWidth: 800,
                fn: function (btn) {
                    if (btn != 'ok') return false;
                    Ext.Ajax.request({
                        url: 'index/set', method: 'post', params: {node: node.id, data: content},
                        success: function (data) {
                            data = Ext.util.JSON.decode(data.responseText);
                            data.success ? toast.success(data.data) : toast.error(data.data);
                        }
                    });
                }
            });
        }

        //节点信息
        function info() {
            var node = selection().data;
            if (!node) return false;
            Ext.Ajax.request({
                url: 'index/info?node=' + node.id,
                success: function (data) {
                    Ext.Msg.show({title: '节点信息', iconCls: 'icon-info', message: data.responseText});
                }
            });
        }

        //自适应大小
        Ext.on('resize', function (width, height) {
            container.setWidth(width);
            container.setHeight(height);
        });
    });
</script>
<-include footer->