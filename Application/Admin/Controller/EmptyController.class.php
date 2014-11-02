<?php
namespace Admin\Controller;

class EmptyController extends \Think\Controller {
    public function _empty(){
		redirect(U('Index/index'));
    }
}
