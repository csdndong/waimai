<?php
defined('IN_IA') or exit ('Access Denied');

class Core extends WeModuleSite
{
  
    public function getMainMenu()
    {
        global $_W, $_GPC;

        $do = $_GPC['do'];
        $navemenu = array();
        $cur_color = ' style="color:#d9534f;" ';
        $system=pdo_get('cjdc_system',array('uniacid'=>$_W['uniacid']));

            $navemenu[0] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=index&m=zh_cjpt" class="panel-title wytitle" id="yframe-0"><icon style="color:#8d8d8d;" class="fa fa-cubes"></icon> 首页</a>',
                'items' => array(
                     0 => $this->createMainMenu('首页 ', $do, 'index', ''),
              
          
                )
     
            );
            $navemenu[1] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=dispatch&m=zh_cjpt" class="panel-title wytitle" id="yframe-1"><icon style="color:#8d8d8d;" class="fa fa-bars"></icon>  调度中心</a>',
                'items' => array(
                     0 => $this->createMainMenu('订单列表 ', $do, 'dispatch', ''),
                     1 => $this->createMainMenu('异常订单 ', $do, 'abnormal', ''),
                         
                )
            );
           
          $navemenu[2] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=ad&m=zh_cjpt" class="panel-title wytitle" id="yframe-2"><icon style="color:#8d8d8d;" class="fa fa-life-ring"></icon>  广告管理</a>',
                'items' => array(
                     0 => $this->createMainMenu('广告列表 ', $do, 'ad', ''),
                    1 => $this->createMainMenu('广告添加', $do, 'addad', ''),
                )
            );
           
            //  $navemenu[2] = array(
            //     'title' => '<a href="index.php?c=site&a=entry&op=display&do=ad&m=zh_cjpt" class="panel-title wytitle" id="yframe-2"><icon style="color:#8d8d8d;" class="fa fa-life-ring"></icon>  数据中心</a>',
            //     'items' => array(
            //         0 => $this->createMainMenu('资金对账 ', $do, 'ad', ''),
              
            //     )
            // );
            //  $navemenu[3] = array(
            //     'title' => '<a href="index.php?c=site&a=entry&op=display&do=nav&m=zh_cjpt" class="panel-title wytitle" id="yframe-3"><icon style="color:#8d8d8d;margin-right:15px;" class="fa fa-compass"></icon>  导航管理</a>',
            //     'items' => array(
            //          0 => $this->createMainMenu('底部导航管理 ', $do, 'nav', ''),
            //          1 => $this->createMainMenu('分类导航管理 ', $do, 'typead', ''),
            //     )
            // );

            $navemenu[4] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=rider&m=zh_cjpt" class="panel-title wytitle" id="yframe-4"><icon style="color:#8d8d8d;" class="fa fa-user"></icon>  骑手管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('骑手信息 ', $do, 'rider', ''),
                    1 => $this->createMainMenu('派单记录 ', $do, 'pdorder', ''),

                    
                )
            );
            $navemenu[5] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=capital&m=zh_cjpt" class="panel-title wytitle" id="yframe-5"><icon style="color:#8d8d8d;" class="fa fa-money"></icon>资金对账</a>',
                'items' => array(
                      0 => $this->createMainMenu('资金对账 ', $do, 'capital', ''),
                      1 => $this->createMainMenu('提现记录 ', $do, 'txlist', ''),
                      2 => $this->createMainMenu('提现设置 ', $do, 'txsz', ''),
                )
            );
            $navemenu[6] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=psmoney&m=zh_cjpt" class="panel-title wytitle" id="yframe-6"><icon style="color:#8d8d8d;" class="fa fa-space-shuttle"></icon>跑腿配置</a>',
                'items' => array(
                      0 => $this->createMainMenu('配送费 ', $do, 'psmoney', ''),
                      1 => $this->createMainMenu('添加配送费 ', $do, 'addpsmoney', ''),
                )
                
            );
         
            $navemenu[14] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=settings&m=zh_cjpt" class="panel-title wytitle" id="yframe-14"><icon style="color:#8d8d8d;" class="fa fa-cog"></icon>  系统设置</a>',
                'items' => array(
                    0 => $this->createMainMenu('基本信息 ', $do, 'settings', ''),
                    1 => $this->createMainMenu('小程序配置', $do, 'peiz', ''),
                    //5 => $this->createMainMenu('模板消息', $do, 'template', ''), 
                    2 => $this->createMainMenu('支付配置', $do, 'pay', ''),                      
                    3 => $this->createMainMenu('短信设置', $do, 'sms', ''), 
                    4 => $this->createMainMenu('餐饮绑定', $do, 'wxapplist', ''),
                    5 => $this->createMainMenu('邮件设置', $do, 'email', ''), 
                    6 => $this->createMainMenu('帮助中心', $do, 'help', ''),
                    7 => $this->createMainMenu('公告中心', $do, 'notice', ''),   
                )
            );
  
           
            
        
        return $navemenu;
    }


    function createCoverMenu($title, $method, $op, $icon = "fa-image", $color = '#d9534f')
    {
        global $_GPC, $_W;
        $cur_op = $_GPC['op'];
        $color = ' style="color:'.$color.';" ';
        return array('title' => $title, 'url' => $op != $cur_op ? $this->createWebUrl($method, array('op' => $op)) : '',
            'active' => $op == $cur_op ? ' active' : '',
            'append' => array(
                'title' => '<i class="fa fa-angle-right"></i>',
            )
        );
    }

    function createMainMenu($title, $do, $method, $icon = "fa-image", $color = '')
    {
        $color = ' style="color:'.$color.';" ';

        return array('title' => $title, 'url' => $do != $method ? $this->createWebUrl($method, array('op' => 'display')) : '',
            'active' => $do == $method ? ' active' : '',
            'append' => array(
                'title' => '<i '.$color.' class="fa fa-angle-right"></i>',
            )
        );
    }


        function createSubMenu($title, $do, $method, $icon = "fa-image", $color = '#d9534f', $storeid)
    {
        $color = ' style="color:'.$color.';" ';
        $url = $this->createWebUrl2($method, array('op' => 'display', 'storeid' => $storeid));
        if ($method == 'stores2') {
            $url = $this->createWebUrl2('stores2', array('op' => 'post', 'id' => $storeid, 'storeid' => $storeid));
        }



        return array('title' => $title, 'url' => $do != $method ? $url : '',
            'active' => $do == $method ? ' active' : '',
            'append' => array(
                'title' => '<i class="fa '.$icon.'"></i>',
            )
        );
    }
    public function getStoreById($id)
    {
        $store = pdo_fetch("SELECT * FROM " . tablename('cjdc_store') . " WHERE id=:id LIMIT 1", array(':id' => $id));
        return $store;
    }


    public function set_tabbar($action, $storeid)
    {
        $actions_titles = $this->actions_titles;
        $html = '<ul class="nav nav-tabs">';
        foreach ($actions_titles as $key => $value) {
            if ($key == 'stores') {
                $url = $this->createWebUrl('stores', array('op' => 'post', 'id' => $storeid));
            } else {
                $url = $this->createWebUrl($key, array('op' => 'display', 'storeid' => $storeid));
            }

            $html .= '<li class="' . ($key == $action ? 'active' : '') . '"><a href="' . $url . '">' . $value . '</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }



    public function doPageSmstest(){
        global $_W, $_GPC;
        $qs=pdo_get('cjpt_rider',array('id'=>$_GPC['qs_id']));
        $order=pdo_get('cjpt_dispatch',array('id'=>$_GPC['order_id']));
        $res=pdo_get('cjpt_system',array('uniacid'=>$_W['uniacid']));
        $tpl_id=$res['tpl_id4'];            
        $tel=$qs['tel'];
        $key=$res['appkey'];
        $code=urlencode("#order_num#=".$order['order_id']."&#order#=".$qs['ps_num']."&#address#=".$order['sender_address']);
        $url = "http://v.juhe.cn/sms/send?mobile=".$tel."&tpl_id=".$tpl_id."&tpl_value=".$code."&key=".$key;    
        $data=file_get_contents($url);
        print_r($data);
    }





}