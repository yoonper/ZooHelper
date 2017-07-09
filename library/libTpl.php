<?php

class libTpl
{
    private $_val = [];
    private $_cache = TRUE;
    private $_fileTpl = '';
    private $_fileCompile = '';
    private $_leftDelimiter = '<-';
    private $_rightDelimiter = '->';
    private $_dirCompile = '/dev/shm';

    /*
     * 显示模板
     */
    public final function display($tplFile)
    {
        $this->_fileTpl = sprintf('%s/template/%s.tpl', PATH_ROOT, $tplFile);
        if (!is_file($this->_fileTpl)) {
            echo 'the template does not exist';
            exit;
        }
        $this->_fileCompile = sprintf('%s/%s.php', $this->_dirCompile, md5($this->_fileTpl));
        $isNew = filemtime($this->_fileTpl) > filemtime($this->_fileCompile);
        if (!$this->_cache || !is_file($this->_fileCompile) || $isNew) {
            $this->_compile(); //重新编译
        }
        extract($this->_val);
        include $this->_fileCompile;
    }

    /**
     * 变量赋值
     * @param $name
     * @param $value
     */
    public final function assign($name, $value)
    {
        $this->_val[$name] = $value;
    }

    /*
     * 编译模板
     */
    private function _compile()
    {
        $data = file_get_contents($this->_fileTpl);
        $data = $this->_parse($data);
        if (!file_put_contents($this->_fileCompile, $data)) {
            echo 'compile failed';
            exit;
        }
    }

    /**
     * 解析模板
     * @param $data
     * @return mixed
     */
    public function _parse($data)
    {
        $v = VERSION;
        list($ld, $rd) = [$this->_leftDelimiter, $this->_rightDelimiter];
        //引用模板
        $data = preg_replace("#{$ld}include (.+){$rd}#i", "<?php \$this->display('$1');?>", $data);
        //PHP标签
        $data = preg_replace("#{$ld}php\s+(.+){$rd}#Us", '<?php $1?>', $data);
        //变量标签
        $var = "[a-z0-9_]+";
        $data = preg_replace("#{$ld}\\$($var){$rd}#", "<?php echo \$$1;?>", $data);
        //CSS标签
        $tpl = '<link href="%s%s" rel="stylesheet" type="text/css"/>';
        $data = preg_replace("#{$ld}css(=const)?\s+((http://|https://).+?){$rd}#", "<?php echo sprintf('$tpl', '$2', '$1' ? '' : '?v=$v'); ?>", $data);
        $data = preg_replace("#{$ld}css(=const)?\s+(.+?){$rd}#", "<?php echo sprintf('$tpl', '/resource/$2', '$1' ? '' : '?v=$v'); ?>", $data);
        //JS标签
        $tpl = '<script src="%s%s" type="text/javascript"></script>';
        $data = preg_replace("#{$ld}js(=const)?\s+((http://|https://).+?){$rd}#", "<?php echo sprintf('$tpl', '$2', '$1' ? '' : '?v=$v'); ?>", $data);
        $data = preg_replace("#{$ld}js(=const)?\s+(.+?){$rd}#", "<?php echo sprintf('$tpl', '/resource/$2', '$1' ? '' : '?v=$v'); ?>", $data);
        //图片地址
        $data = preg_replace("#{$ld}img\s+(.+?){$rd}#", "/resource/$1?v=$v", $data);
        return $data;
    }
}