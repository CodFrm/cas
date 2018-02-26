<?php
/**
 *============================
 * author:Farmer
 * time:2018/2/24
 * function:
 *============================
 */

namespace icf\lib;


class model {
    protected $data;
    protected $where;
    private $table;

    public function __construct($table, $where = '') {
        $this->table = $table;
        $this->where = $where;
        if ($where !== '') {
            $this->data = db::table($table)->where($where)->find();
        }
    }

    public function __get($name) {
        // TODO: Implement __get() method.
        if (substr($name, 0, 1) == '_') {
            $tmpKey = $this->table . $name;
        } else {
            $tmpKey = $name;
        }
        if (isset($this->data[$tmpKey])) {
            return $this->data[$tmpKey];
        } else {
            throw new \Exception('not find ' . $name);
        }
    }

    public function __set($name, $value) {
        // TODO: Implement __set() method.
        if (substr($name, 0, 1) == '_') {
            $tmpKey = $this->table . $name;
        } else {
            $tmpKey = $name;
        }
        $this->data[$tmpKey] = $value;
    }

    /**
     * 添加数据到数据库
     * @author Farmer
     * @return int
     */
    public function add() {
        db::table($this->table)->insert($this->data);
        return db::table()->lastinsertid();
    }

    /**
     * 修改数据
     * @author Farmer
     * @param $where
     */
    public function put($where) {
        db::table($this->table)->where($where)->update($this->data);
    }

    /**
     * 获取数据
     * @author Farmer
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * 设置数据
     * @author Farmer
     * @param $data
     */
    public function setData($data) {
        $this->data = $data;
    }
}