<?php
defined( 'ABSPATH' ) || exit;

class WPGate_Wxpay extends WC_Payment_Gateway {
    public function __construct() {
        $this->id = 'wxpay';
        $this->icon = WPGate_URI . 'images/wxpay.png';
        $this->has_fields = false;

        $this->init_form_fields();
        $this->init_settings();

        $this->method_title = '微信支付';
        $this->method_description = '微信支付网关，如需支持H5支付、微信内置浏览器公众号支付可<a href="https://www.wpcom.cn/plugins/woo-paygate.html" target="_blank">升级付费版</a>';

        $this->title = $this->get_option( 'title' );
        $this->description = $this->get_option( 'description' );
        $this->order_button_text = __('Proceed to WeChatPay', 'wpcom');
        
        $this->form_fields = array (
            'enabled' => array (
                'title' => '启用/禁用',
                'type' => 'checkbox',
                'label' => '启用微信支付',
                'default' => 'no'
            ),
            'title' => array (
                'title' => '标题',
                'type' => 'text',
                'description' => '顾客支付的时候会看到关于该支付方式的说明',
                'default' => '微信支付',
                'css' => 'width:400px'
            ),
            'description' => array (
                'title' => '描述',
                'type' => 'textarea',
                'description' => '顾客在你网站上看到的付款方式描述',
                'css' => 'width:400px'
            ),
            'appid' => array (
                'title' => 'APPID',
                'type' => 'text',
                'description' => '绑定支付的APPID（必须配置，开户邮件中可查看）',
                'css' => 'width:400px'
            ),
            'mchid' => array (
                'title' => '商户号',
                'type' => 'text',
                'description' => 'MCHID 商户号（必须配置，开户邮件中可查看）',
                'css' => 'width:400px'
            ),
            'key' => array (
                'title' => '支付密钥',
                'type' => 'text',
                'description' => '商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）',
                'css' => 'width:400px',
            ),
            'exchange_rate'=> array (
                'title' => '汇率',
                'type' => 'text',
                'default'=>1,
                'description' => '如果网站货币不是人民币，可在此设置汇率，如果是人民币则可直接设置成1',
                'css' => 'width:80px;'
            )
        );

        add_action( 'woocommerce_api_wpgate_wxpay', array($this, 'check_response') );
        add_action( 'woocommerce_receipt_wxpay', array($this, 'receipt_page') );
        add_action( 'wp_ajax_WPGate_order_status', array($this, 'order_status') );
        add_action( 'wp_ajax_nopriv_WPGate_order_status', array($this, 'order_status') );
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array ($this,'process_admin_options') );
        add_action( 'woocommerce_update_options_payment_gateways', array ($this,'process_admin_options') );
        add_filter( 'woocommerce_payment_gateways', array($this, 'add_gateway') );
    }

    function log( $log, $level = 'debug' ){
        $logger = wc_get_logger();
        $context = array( 'source' => $this->id );
        $logger->log( $level, $log, $context );
    }

    function add_gateway($methods){
        $methods[] = $this;
        return $methods;
    }
    
    public function process_payment($order_id) {
        $order = new WC_Order ( $order_id );
        return array (
            'result' => 'success',
            'redirect' => $order->get_checkout_payment_url ( true )
        );
    }
    
    public function check_response() {
        // 避免重复执行log，比如自动抓取描述可能会允许一次
        global $flag;
        if(!isset($flag)) $flag = -1;
        $flag++;

        $xml = file_get_contents('php://input');
        if(empty($xml)) exit;

        //排除非微信回调
        if(strpos($xml, 'transaction_id')===false
            ||strpos($xml, 'appid')===false
            ||strpos($xml, 'mch_id')===false) exit;

        include_once WPGate_DIR . 'wxpay/notify.php';

        // 如果返回成功则验证签名
        try {
            $config = new WPGate_WeChat_Config();
            $notify = new PayNotifyCallBack();
            $notify->Handle($config, false);

            $values = $notify->FromXml($xml);

            if( $flag<1 ) $this->log('#'.$values['out_trade_no'].' 微信异步通知数据：' . json_encode($values));
            if($values['return_code'] == 'SUCCESS' && $values['result_code'] == 'SUCCESS') {
                $order = new WC_Order( $values['out_trade_no'] );
                if( $flag<1 ) $this->log('#'.$values['out_trade_no'].' 微信异步通知验证成功：' . $values['result_code'] .'；微信订单：' . $values['transaction_id']);
                if($order->needs_payment()){
                    $order->payment_complete( $values['transaction_id'] );
                }
                echo $notify->ToXml();
            }
            exit;
        } catch ( WechatPaymentException $e ) {
            exit;
        }
    }

    function order_status() {
        $order_id = isset($_POST['order'])?$_POST ['order'] : '';
        $order = new WC_Order ( $order_id );
        $isPaid = !$order->needs_payment();
        echo json_encode ( array (
            'status' => $isPaid? 'paid' : 'unpaid',
            'url' => $this->get_return_url( $order )
        ));
        exit;
    }

    public function receipt_page($order_id) {
        // 避免重复执行log，比如自动抓取描述可能会允许一次
        global $flag;
        if(!isset($flag)) $flag = -1;
        $flag++;

        $order = new WC_Order($order_id);
        if( !$order||$order->is_paid() ) return;

        include_once WPGate_DIR . 'wxpay/WxPay.NativePay.php';

        $input = new WxPayUnifiedOrder();
        $input->SetBody($this->get_order_title($order) );
        $input->SetOut_trade_no( $order_id );    
        $total = $order->get_total();

        $exchange_rate = floatval($this->get_option('exchange_rate'));
        if($exchange_rate<=0){
            $exchange_rate=1;
        }

        $total = round ($total * $exchange_rate, 2 );
        $totalFee = ( int ) ($total * 100);

        $input->SetTotal_fee( $totalFee );

        $startTime = date("YmdHis", current_time('timestamp'));
        $expiredTime = date("YmdHis", current_time('timestamp') + 600);
        $input->SetTime_start( $startTime );
        $input->SetTime_expire( $expiredTime );

        $input->SetNotify_url( WC()->api_request_url('WPGate_Wxpay') );
    
        $input->SetTrade_type( "NATIVE" );

        $items = $order->get_items();
        foreach ($items as $key => $item) {
            $product_id = $item->get_product_id();
        }
        if(isset($product_id)) $input->SetProduct_id( $product_id );

        try {
            $notify = new NativePay();
            $result = $notify->GetPayUrl($input);
        } catch (Exception $e) {
            $error_msg = $error_msg ? $error_msg : $e->getMessage();
            echo '<div class="text-center" style="margin-bottom: 50px;">' . $error_msg . '</div>';
            return;
        }

        $error_msg = '';
        if( isset($result['return_code']) && $result['return_code']=='FAIL' ){
            $error_msg =  "错误提示：".$result['return_msg'];
        }
        $url =isset($result['code_url'])? $result ["code_url"]:''; ?>
        <div class="wpgate-weixin">
        <?php if($error_msg){ ?>
             <div class="text-center">
                <span style="color:red;"><?php echo $error_msg?></span>
             </div>
         <?php } else {
            if( $flag<1 ) $this->log('#'.$order_id.' 微信发起付款，订单号：'.$order_id.'；价格：'.$total); ?>
             <div class="wpgate-wechat-inner">
                <div id="j-wpgate-wechat" class="wpgate-wechat-img" data-url="<?php echo $url;?>" data-order="<?php echo $order_id;?>"></div>
                <p class="wpgate-wechat-note">使用微信扫描二维码进行支付</p>
             </div>
         <?php } ?>
        </div>
        <?php
    }

    public  function get_order_title($order){
        $id = method_exists($order, 'get_id')?$order->get_id():$order->id;
        $title = get_option('blogname');
        $order_items = $order->get_items();
        if( $order_items && count($order_items)>0 ){
            $title = "#{$id} ";
            $index = 0;
            foreach ($order_items as $item_id =>$item){
                $title .= $item['name'];
                if($index++>0){
                    $title .= $trimmarker;
                    break;
                }
            }
        }
        return $title;
    }
}