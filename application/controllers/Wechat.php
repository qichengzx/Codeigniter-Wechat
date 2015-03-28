<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wechat extends CI_Controller{

    protected $msg = "";
    protected $from_username = "";
    protected $to_username = "";
    protected $content = "";

    public function __construct() {
        parent::__construct();
        //error_reporting(E_ALL);
        $this->load->library('weixinapi');
    }


    public function index(){

        $post = file_get_contents('php://input');
        if (empty($post)) {
            die();
        }
        $msg = simplexml_load_string($post, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        //log_message('debug', $msg->Content);
        //
        switch ( $msg->MsgType ) {

            case 'event':
                
                switch ( $msg -> Event ) {

                    case 'subscribe':
                        $this->onSubscribe( $msg );
                        break;

                    case 'unsubscribe':
                        $this->onUnsubscribe( $msg );
                        break;

                    case 'SCAN':
                        $this->onScan( $msg );
                        break;

                    case 'LOCATION':
                        $this->onEventLocation( $msg );
                        break;

                    case 'CLICK':
                        $this->onClick( $msg );
                        break;
                }

            break;

            case 'text':
                $this->_ontext( $msg );
            break;

            case 'image':
                $this->onImage( $msg );
            break;

            case 'location':
                $this->onLocation( $msg );
            break;

            case 'link':
                $this->onLink( $msg );
            break;

            case 'voice':
                $this->_onvoice( $msg );
            break;

            default:
                $this->onUnknown( $msg );
            break;

        }
    }



    public function _ontext($msg){


        $data = array(
                    'to'    => $msg->FromUserName,
                    'from'  => $msg->ToUserName,
                    'content'   => trim($msg->Content)
                );
        $this->load->view('wechat/text',$data);

    }


}