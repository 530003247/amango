<?php

namespace Addons\Exam\Model;
use Think\Model;

/**
 * Exam模型
 * model   作用: 列表页显示参数设置
 * _fields 作用: 编辑时候显示字段 
 */
class AddonsexamModel extends Model{
    protected $_validate = array(
        array('id', 'set_id', '请编辑正确的考卷ID', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('title', '1,10', '考卷标题长度为1-10个字符', self::EXISTS_VALIDATE, 'length'),
        array('author','require','请设置出卷作者'),
        array('author', '1,5', '出卷作者长度为1-5个字符', self::EXISTS_VALIDATE, 'length'),
        array('desc','require','请简要描述考卷简介'),
        array('desc', '1,30', '出卷作者长度为1-30个字符', self::EXISTS_VALIDATE, 'length'),
        array('score', 'check_scoretype', '请设置合法数字分数', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('score_param', 'check_score', '请输入分数节点', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('password', 'check_password', '请输入9位以内1-9的不重复数字，建议四位;默认为空', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('keyword','require','考卷关键词不能为空'),
        array('keyword','','该考卷关键词已存在,换个关键词',0,'unique',1), // 在新增的时候验证name字段是否唯一
        array('has_share',array(0,1),'请选择是否开启分享',2,'in'),
        array('has_name',array(0,1),'请选择是否实名制考试',2,'in'),
        array('order',array(0,1),'请选择答题排序',2,'in'),
    );
    protected $_auto = array(
        array('logo', 'set_logo', self::MODEL_BOTH, 'callback'),
        array('views', 1, self::MODEL_INSERT),
        array('status', 1, self::MODEL_INSERT),
        array('createtime', NOW_TIME, self::MODEL_BOTH),
    );

    public $_fields = array(
        'id'=>array(
            'name'=>'id',//字段名
            'title'=>'ID',//显示标题
            'type'=>'num',//字段类型
            'remark'=>'',// 备注，相当于配置里的tip
            'is_show'=>3,// 1-始终显示 2-新增显示 3-编辑显示 0-不显示
            'value'=>0,//默认值
        ),
        'title'=>array(
            'name'=>'title',
            'title'=>'书名',
            'type'=>'string',
            'remark'=>'',
            'is_show'=>1,
            'value'=>0,
            'is_must'=>1,
        ),
    );
    public function check_password(){
        $password = I('post.password');
        if(!empty($password)){
            $len = strlen($password);
            if(!is_numeric($password)||$len>9){
                return false;
            } else {
                $newpassword  = str_split($password);
                $oldcount     = count($newpassword);
                $new_password = array_unique($newpassword);
                $newcount     = count($new_password);
                return ($oldcount==$newcount)?true:false;
            }
        }
    }
    public function set_id(){
        $id = I('post.id');
        if(!empty($id)){
            return (is_numeric($id)&&$id>0)?true:false;
        }
        return true;
    }
    public function check_score(){
        $allscore = I('post.score');
        $param = parse_config(I('post.score_param'));
        foreach ($param as $key => $value) {
            if(!is_numeric($key)||$key>$allscore){
                return false;  
            }
        }
            return true;
    }
    public function set_logo(){
        $logourl = I('post.logo');
        return empty($logourl)?ADDON_PUBLIC.'/ad1.jpg':$logourl;
    }
    public function check_scoretype(){
        if(is_numeric(I('post.score'))){
            return true;
        } else {
            return false;
        }
    }
}
