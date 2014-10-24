<?php
// +----------------------------------------------------------------------
// | Amango [ 芒果一站式微信营销系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.Amango.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ChenDenlu <530003247@vip.qq.com>
// +----------------------------------------------------------------------
namespace Common\Api;
class WxuserApi {
    /**
     * 获取微信用户的信息
     * @param  integer $id    用户ID
     * @param  string  $field 要获取的字段名
     * @return array         分类信息
     */
    public static function get_info($id=null,$field=null){
        if (empty($id)) {
            return false;
        } else {
            $info = empty($field) ? M('Weixinmember')->where(array('id'=>$id))->find() : M('Weixinmember')->where(array('id'=>$id))->field($field)->find();
        }
        return empty($info) ? false : $info;
    }
    /**
     * 获取微信用户列表
     * @param  string  $field 要获取的字段名
     * @return array          用户列表
     */
    public static function get_info_list($field=null){
        $list = empty($field) ? M('Weixinmember')->select() : M('Weixinmember')->field($field)->select();
        return empty($info) ? false : $list;
    }
    /**
     * 更新微信用户的信息
     * @param  integer $id    用户ID
     * @param  string  $data  要更新的数据
     * @return array          分类信息
     */
    public static function update_info($id=null,$data=null){
        if (empty($id)) {
            return false;
        } 

        if (empty($data)) {
            return true;
        }

        $oldpass = self::get_info($id,'ucpassword,ucmember');
            if(strlen(strtolower($data['ucpassword']))>=9&&$oldpass['ucpassword']!=$data['ucpassword']){
                //同步更改Uenter的密码
                $User    = new \User\Api\UserApi;
                $return  = $User->updateInfo($id, $oldpass['ucpassword'], array('ucpassword'=>$data['ucpassword']));
                if($return['status']===false){
                    return false;
                }
            } else {
                unset($data['ucpassword']);
            }

        M('Weixinmember')->where(array('id'=>$id))->save($data);
        return true;
    }

}