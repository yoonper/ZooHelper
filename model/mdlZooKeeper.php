<?php

class mdlZooKeeper extends mdlBase
{
    public $zk;

    public function __construct()
    {
        $config = parse_ini_file(PATH_ROOT . '/config.ini');
        $this->zk = new Zookeeper($config['server']);
    }

    /**
     * @param $method
     * @param $param
     * @return array
     */
    public function op($method, $param)
    {
        try {
            $exists = method_exists($this, $method);
            $data = $exists ? $this->$method($param) : $this->zk->$method($param);
            return ['success' => $data !== FALSE, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => FALSE, 'data' => $e->getMessage()];
        }
    }

    /**
     * @param $node
     * @return mixed
     */
    protected function create($node)
    {
        $zk = $this->zk;
        $acl = [['perms' => $zk::PERM_ALL, 'scheme' => 'world', 'id' => 'anyone']];
        $data = $zk->create($node, NULL, $acl);
        return $data;
    }

    /**
     * @param $params ['node'=>$node,'data'=>$data]
     * @return mixed
     */
    protected function set($params)
    {
        return $this->zk->set($params['node'], $params['data']);
    }
}