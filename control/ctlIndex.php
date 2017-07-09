<?php

class ctlIndex extends ctlBase
{
    public $zk;

    public function __construct()
    {
        parent::__construct();
        $this->zk = new mdlZooKeeper();
    }

    public function index()
    {
        $data = ['title' => 'ZooHelper'];
        $this->assign('data', $data);
        $this->display('index');
    }

    /**
     * 树形节点
     */
    public function tree()
    {
        list($data, $node) = [[], $_GET['node']];
        $childs = $this->zk->op('getChildren', $node)['data'];
        foreach ($childs as $child) {
            $path = sprintf('%s/%s', rtrim($node, '/'), $child);
            $isLeaf = !(bool)$this->zk->op('getChildren', $path)['data'];
            $data[] = ['id' => $path, 'text' => $child, 'leaf' => $isLeaf];
        }
        echo json_encode($data);
    }

    /**
     * 新增节点
     */
    public function add()
    {
        $node = sprintf('%s/%s', rtrim($_POST['path'], '/'), $_POST['node']);
        $data = $this->zk->op('create', $node);
        $data['success'] && $data['data'] = ['id' => $node, 'text' => $_POST['node'], 'leaf' => true];
        echo json_encode($data);
    }

    /**
     * 删除节点
     */
    public function del()
    {
        $data = $this->zk->op('delete', $_GET['node']);
        $data['success'] && $data['data'] = '删除节点成功！';
        echo json_encode($data);
    }

    /**
     * 更新节点
     */
    public function set()
    {
        $params = ['node' => $_POST['node'], 'data' => $_POST['data']];
        $data = $this->zk->op('set', $params);
        $data['success'] && $data['data'] = '更新节点成功！';
        echo json_encode($data);
    }

    /**
     * 读取节点
     */
    public function get()
    {
        $data = $this->zk->op('get', $_GET['node']);
        echo json_encode($data);
    }

    /**
     * 节点信息
     */
    public function info()
    {
        $data = [];
        $info = $this->zk->op('getAcl', $_GET['node'])['data'][0];
        $info['ctime'] = date('Y-m-d H:i:s', $info['ctime'] / 1000);
        $info['mtime'] = date('Y-m-d H:i:s', $info['mtime'] / 1000);
        foreach ($info as $k => $v) {
            $data[] = "$k = $v";
        }
        echo implode('<br/>', $data);
    }
}