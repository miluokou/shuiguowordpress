<?php
defined( 'ABSPATH' ) || exit;

class WPGate_Alipay extends WC_Payment_Gateway {
    public function __construct() {
        $this->id = 'alipay';
        $this->icon = WPGate_URI . 'images/alipay.png';
        $this->has_fields = false;

        $this->init_form_fields();
        $this->init_settings();

        $this->method_title = '支付宝';
        $this->method_description = '支付宝网关，如需支持手机端网站支付可<a href="https://www.wpcom.cn/plugins/woo-paygate.html" target="_blank">升级付费版</a>';

        $this->title = $this->get_option( 'title' );
        $this->description = $this->get_option( 'description' );
        $this->order_button_text = __('Proceed to Alipay', 'wpcom');

        $this->form_fields = array (
            'enabled' => array (
                'title' => '启用/禁用',
                'type' => 'checkbox',
                'label' => '启用支付宝付款',
                'default' => 'no'
            ),
            'title' => array (
                'title' => '标题',
                'type' => 'text',
                'description' => '顾客支付的时候会看到关于该支付方式的说明',
                'default' => '支付宝',
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
                'description' => '应用ID',
                'css' => 'width:400px'
            ),
            'private_key' => array (
                'title' => '商户私钥',
                'type' => 'text',
                'description' => '',
                'css' => 'width:400px'
            ),
            'public_key' => array (
                'title' => '支付宝公钥',
                'type' => 'text',
                'description' => '查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥',
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
        add_action( 'woocommerce_api_wpgate_alipay', array($this, 'check_response') );
        add_action( 'woocommerce_receipt_alipay', array($this, 'receipt_page') );
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array ($this,'process_admin_options') );
        add_action( 'woocommerce_update_options_payment_gateways', array ($this,'process_admin_options') );
        add_filter( 'woocommerce_payment_gateways', array($this, 'add_gateway') );
    }

    function log( $log, $level = 'debug' ){
        $logger = wc_get_logger();
        $context = array( 'source' => $this->id );
        $logger->log( $level, $log, $context );
    }

    function config($order_id){
        $settings = get_option('woocommerce_alipay_settings');
        extract( $settings );
        $order = new WC_Order( $order_id );
        $config = array (   
            //应用ID,您的APPID。
            'app_id' => $appid,
            //商户私钥
            'merchant_private_key' => $private_key,
            //异步通知地址
            'notify_url' => WC()->api_request_url('WPGate_Alipay'),
            //同步跳转
            'return_url' => $order->get_checkout_order_received_url(),
            //编码格式
            'charset' => "UTF-8",
            //签名方式
            'sign_type'=>"RSA2",
            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => $public_key,
        );
        return $config;
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

        require_once WPGate_DIR . 'alipay/pagepay/service/AlipayTradeService.php';

        if(!isset($_POST['out_trade_no'])) exit;
        $arr = $_POST;
        $arr['fund_bill_list'] = stripslashes($arr['fund_bill_list']);
        $config = $this->config($arr['out_trade_no']);
        $alipaySevice = new AlipayTradeService($config);
        $result = $alipaySevice->check($arr);

        if( $flag<1 ) $this->log('#'.$arr['out_trade_no'].' 支付宝异步通知：' . $result . '；通知数据：' . json_encode($arr));
        if( $flag<1 ) $this->log('#'.$arr['out_trade_no'].' 支付宝通知验证：' . ($result && $arr['app_id'] == $config['app_id']));
        if( $result && $arr['app_id'] == $config['app_id'] ){
            $trade_status = $arr['trade_status'];
            if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                $order = new WC_Order( $arr['out_trade_no'] );
                if( $flag<1 ) $this->log('#'.$arr['out_trade_no'].' 支付宝异步通知验证成功：' . $trade_status .'；支付宝订单：' . $arr['trade_no']);
                if($order->needs_payment()){
                    $order->payment_complete( $arr['trade_no'] );
                }
            }
            echo "success";
        }else{
            echo "fail";
        }
        exit;
    }

    public function receipt_page($order_id) {
        // 避免重复执行log，比如自动抓取描述可能会允许一次
        global $flag;
        if(!isset($flag)) $flag = -1;
        $flag++;

        $order = new WC_Order($order_id);
        if( !$order||$order->is_paid() ) return;

        include_once WPGate_DIR . 'alipay/pagepay/service/AlipayTradeService.php';
        include_once WPGate_DIR . 'alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

        $exchange_rate = floatval($this->get_option('exchange_rate'));
        if($exchange_rate<=0) $exchange_rate=1;

        $total = round($order->get_total() * $exchange_rate, 2 );

        //构造参数
        $payRequestBuilder = new AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($this->get_order_title($order, 1));
        $payRequestBuilder->setSubject($this->get_order_title($order));
        $payRequestBuilder->setTotalAmount($total);
        $payRequestBuilder->setOutTradeNo($order_id);

        $config = $this->config($order_id);
        $aop = new AlipayTradeService($config);
        if( $flag<1 ) $this->log('#'.$order_id.' 支付宝发起付款，订单号：'.$order_id.'；价格：'.$total);?>
        <div class="wpgate-alipay">
            <img class="j-lazy" src="<?php echo WPGate_URI;?>images/loading.gif" width="32" height="32" alt="Loading" />
            <p>正在转入支付平台...</p>
            <div class="wpgate-alipay-html"><?php $aop->pagePay($payRequestBuilder, $config['return_url'], $config['notify_url']);?></div>
        </div>
        <?php
    }

    public function get_order_title($order, $desc=0){
        $id = method_exists($order, 'get_id')?$order->get_id():$order->id;
        $title = "#{$id} " . get_option('blogname');
        return $title;
    }
}