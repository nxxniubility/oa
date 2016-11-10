<?php
namespace Weixin\Controller;

use Common\Controller\BaseController;

class IndexController extends BaseController
{

    //控制器前置
    public function _initialize()
    {
        parent::_initialize();

    }


    /*
    |--------------------------------------------------------------------------
    | 首页
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function index()
    {
        $_redata = D('Weixin','Service')->getToken();

    }


    /*
    |--------------------------------------------------------------------------
    | 微信消息回调地址
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function setweixn()
    {

    }

    /*
    |--------------------------------------------------------------------------
    | 微信自定义菜单
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function settingmenu()
    {

    }

    /*
     * 微信回调验证
     */
    private function checkSignature()
    {
        $signature = $_GET["signature"];

        $timestamp = $_GET["timestamp"];

        $nonce = $_GET["nonce"];

        $token = 'oazelininfo888';

        $tmpArr = array($token, $timestamp, $nonce);

        sort($tmpArr, SORT_STRING);

        $tmpStr = implode( $tmpArr );

        $tmpStr = sha1( $tmpStr );


        if( $tmpStr == $signature ){

            return true;

        }else{

            return false;

        }

    }

}