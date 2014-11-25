<?php
    function set_kb($string){
      return str_replace(' ', '', $string);
    }
    function set_queData($list){
        $data = '';
        foreach ($list as $key => $value) {
            $choices  = set_data(parse_config($value['a_choices']));
            $typedata = set_data(parse_config($value['a_typedata']));
            $data .= "{
            	          quesid: '{$value['id']}',
                       titletype: '{$value['q_titletype']}',
                           title: '{$value['q_title']}',
                        typedata: [{$typedata}],
                         choices: [{$choices}],
                            type:'{$value['a_type']}',
                      rightIndex: '{$value['q_right']}'
                      },";
        }
            return $data;
    }
    function set_data($list){
        foreach ($list as $key => $value) {
            $list[$key] = '\''.$value.'\'';
        }
            $liststr = implode(',', $list);
            return $liststr;
    }
    //设置模板
    function set_jstpl($link_url,$info,$list,$common,$id){
        //判断是否存在缓存  5小时
        $cachetpl = S('Addonsexamcache'.$id);
        if(!empty($cachetpl)){
            return $cachetpl;
        }
        //不存在  重新定义
        //分享图片
        $img_url  = get_cover_pic($info['logo']);
        //分数段
        $scoreparam  = '';
        $score_param = array();
        $score_param = parse_config($info['score_param']);
        foreach ($score_param as $key => $value) {
            $scoreparam .= '{score: '.$key.',comment: "'.$value.'"},';
        }
        //题目列表
        $queData = set_queData($list);

        $tpl = <<<str
(function() {
  window.amangoshare = {
    "img_url": "{$img_url}",
    "img_width": "120",
    "img_height": "120",
    "link": "{$link_url}",
    "desc": "我正在参加【{$info['title']}】！你也来试试！",
    "title": "{$info['title']}"
  };
  window.buttonurl   = [
      '{$common[0]}',
      '{$common[1]}',
      '{$common[2]}',
      '{$common[3]}',
  ];
  window.nowdatajson = {
    queData: [
      {$queData}
    ],
    fullScore: {$info['score']},
    scoreArray: [
      {$scoreparam}
    ]
  };

}).call(this);
str;
        S('Addonsexamcache'.$id,$tpl,60*60);
        return $tpl;
    }