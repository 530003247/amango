<?php

namespace Addons\Exam\Model;
use Think\Model;

/**
 * Exam模型
 * model   作用: 列表页显示参数设置
 * _fields 作用: 编辑时候显示字段
 */
class AddonsquesModel extends Model{
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
    //二维数组 list
    public function check_questions($list){
        if(empty($list)){
            return '';
        } else {
            $new_list = array();
            $error    = array();
            $q_type   = array('text','image','audio','video');
            $a_type   = array('select','bool','text','checkbox');
            //0排序 1题目描述 2题目类型 3额外参数 4答案类型 5答案选项 6正确答案
            //TODO 此处逻辑并未完全匹配完整 自行增加
            $i = 1;
            $nowtime = time();
            foreach ($list as $key => $value) {
                $td = $i++;
                $param = array();
                if(!empty($value['paixu'])){
                    if(!is_numeric($value['paixu'])){
                        $error[$td][] = '排序只能为数字型';
                    }
                } else {
                    $value['paixu'] = $nowtime + $i;
                }
                if(empty($value['q_title'])){
                    $error[$td][] = '题目描述不能为空';
                }
                if(!in_array($value['q_titletype'], $q_type)){
                    $error[$td][] = '题目类型支持书面题，看图题，听力题，视频题';
                }
                if(!in_array($value['a_type'], $a_type)){
                    $error[$td][] = '答案类型支持单选，判断，填空，多选';
                }
                if($value['q_right']==''&&$value['q_right']!=0){
                    $error[$td][] = '正确答案不能为空';
                }
                if(in_array($value['a_type'], array('select','checkbox'))){
                    $qright    = array();
                    $a_choices = array();
                    $q_right   = $value['q_right'];
                    $a_choices = parse_config($value['a_choices']);
                    switch ($value['a_type']) {
                        case 'select':
                            $allwnum   = count($a_choices)-1;
                            if(!is_numeric($q_right)||$q_right>$allwnum){
                                $error[$td][] = '单选答案排序只能为0-24数字,并且不能大于选项';
                            }
                            break;
                        case 'checkbox':
                            $qright    = explode(',', $q_right);
                            foreach ($qright as $k => $v) {
                                if(empty($a_choices[$v])||!is_numeric($v)){
                                    $error[$td][] = '多选答案排序只能为0-24数字,并且不能大于选项';
                                }
                            }
                            break;
                    }
                }
                if($value['a_type']!='text'){
                    $param = array_values(parse_config($value['a_choices']));
                    if (empty($param)) {
                        $error[$td][] = '答案选项不能为空';
                    }
                }
                if(empty($error[$td])){
                    $new_list[] = $value;
                }
            }
        }
                if(empty($error)){
                    return array('1',$new_list);
                } else {
                    return array('0',$error);
                }
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
