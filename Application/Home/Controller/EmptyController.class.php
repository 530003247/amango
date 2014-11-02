<?php
namespace Home\Controller;
class EmptyController extends HomeController{
    public function _empty(){
		redirect(U('Index/index'));
    }
}
