<?php
defined('IN_IA') or exit ('Access Denied');

class Core extends WeModuleSite
{

    public function getMainMenu()
    {
        global $_W, $_GPC;
        ini_set("memory_limit", "800M");
        set_time_limit(0);
        $do = $_GPC['do'];
        $navemenu = array();
        $cur_color = ' style="color:#d9534f;" ';
        $system = pdo_get('cjdc_system', array('uniacid' => $_W['uniacid']));
        if ($_W['role'] == 'operator') {
            $navemenu[0] = array(
                'title' => '<a href="javascript:void(0)" class="panel-title wytitle"><icon style="color:#8d8d8d;" class="fa fa-cog"></icon>  业务菜单</a>',
                'items' => array(
                    0 => $this->createMainMenu('门店列表', $do, 'store', ''),

                )
            );
        } elseif ($_W['isfounder'] || $_W['role'] == 'manager' || $_W['role'] == 'operator') {
            $navemenu[0] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=store&m=zh_cjdianc" class="panel-title wytitle" id="yframe-0"><icon style="color:#8d8d8d;" class="fa fa-cubes"></icon>  门店管理</a>',
                'items' => array(
                    // 0 => $this->createMainMenu('数据概况 ', $do, 'gaikuangdata', ''),
                    1 => $this->createMainMenu('门店列表', $do, 'store', ''),
                    2 => $this->createMainMenu('门店账号', $do, 'account', ''),
                    // 3 => $this->createMainMenu('小程序端账号', $do, 'admin', ''),

                    4 => $this->createMainMenu('入驻期限', $do, 'rzqx', ''),
                    5 => $this->createMainMenu('入驻设置 ', $do, 'rzset', ''),
                    6 => $this->createMainMenu('入驻审核', $do, 'rzcheck', ''),
                )
            );
            $navemenu[1] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=order&m=zh_cjdianc" class="panel-title wytitle" id="yframe-1"><icon style="color:#8d8d8d;" class="fa fa-bars"></icon>  订单管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('外卖订单 ', $do, 'order', ''),
                    1 => $this->createMainMenu('店内订单 ', $do, 'dnorder', ''),
                    2 => $this->createMainMenu('当面付订单 ', $do, 'dmorder', ''),
                    3 => $this->createMainMenu('预约订单 ', $do, 'ydorder', ''),
                )
            );


            $navemenu[2] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=ad&m=zh_cjdianc" class="panel-title wytitle" id="yframe-2"><icon style="color:#8d8d8d;" class="fa fa-life-ring"></icon>广告管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('广告列表 ', $do, 'ad', ''),
                    1 => $this->createMainMenu('广告添加', $do, 'addad', ''),
                    2 => $this->createMainMenu('流量主管理', $do, 'llz', '')
                )
            );
            $navemenu[3] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=nav&m=zh_cjdianc" class="panel-title wytitle" id="yframe-3"><icon style="color:#8d8d8d;margin-right:15px;" class="fa fa-compass"></icon>  导航管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('底部导航管理 ', $do, 'nav', ''),
                    1 => $this->createMainMenu('分类导航管理 ', $do, 'typead', ''),
                )
            );
            //  $navemenu[3] = array(
            //     'title' => '<a href="index.php?c=site&a=entry&op=display&do=area&m=zh_cjdianc" class="panel-title wytitle" id="yframe-3"><icon style="color:#8d8d8d;" class="fa fa-map-marker"></icon>  门店区域</a>',
            //     'items' => array(
            //          0 => $this->createMainMenu('区域列表', $do, 'area', ''),
            //         1 => $this->createMainMenu('区域添加', $do, 'addarea', ''),
            //     )
            // );
            $navemenu[4] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=typeset&m=zh_cjdianc" class="panel-title wytitle" id="yframe-4"><icon style="color:#8d8d8d;" class="fa fa-university"></icon>  门店类型</a>',
                'items' => array(
                    0 => $this->createMainMenu('分类设置 ', $do, 'typeset', ''),
                    1 => $this->createMainMenu('类型管理 ', $do, 'storetype', ''),
                    2 => $this->createMainMenu('类型添加 ', $do, 'addstoretype', ''),

                )
            );
            $navemenu[5] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=coupons&m=zh_cjdianc" class="panel-title wytitle" id="yframe-5"><icon style="color:#8d8d8d;" class="fa fa-cubes"></icon>  天降红包</a>',
                'items' => array(
                    0 => $this->createMainMenu('红包管理 ', $do, 'coupons', ''),
                    1 => $this->createMainMenu('红包设置 ', $do, 'couponset', ''),
                )
            );
            // $navemenu[6] = array(
            //     'title' => '<a href="index.php?c=site&a=entry&op=display&do=cptj&m=zh_cjdianc" class="panel-title wytitle" id="yframe-5"><icon style="color:#8d8d8d;" class="fa fa-trophy"></icon>菜品推荐</a>',
            //     'items' => array(
            //         0 => $this->createMainMenu('推荐管理 ', $do, 'cptj', ''),
            //     )
            // );
            // $navemenu[5] = array(
            //     'title' => '<a href="index.php?c=site&a=entry&op=display&do=ruzhu&m=zh_cjdianc" class="panel-title wytitle" id="yframe-5"><icon style="color:#8d8d8d;" class="fa fa-cubes"></icon>  入驻管理</a>',
            //     'items' => array(
            //         0 => $this->createMainMenu('申请列表 ', $do, 'ruzhu', ''),
            //         1 => $this->createMainMenu('入驻设置 ', $do, 'ruzhusz', ''),
            //     )
            // );


            if ($system['jfgn'] == 1) {
                $navemenu[8] = array(
                    'title' => '<a href="index.php?c=site&a=entry&op=display&do=jfgoods&m=zh_cjdianc" class="panel-title wytitle" id="yframe-8"><icon style="color:#8d8d8d;" class="fa fa-star-half-o"></icon>  积分商城</a>',
                    'items' => array(
                        0 => $this->createMainMenu('签到管理', $do, 'integral', ''),
                        1 => $this->createMainMenu('商品列表', $do, 'jfgoods', ''),
                        2 => $this->createMainMenu('商品分类', $do, 'jftype', ''),
                        3 => $this->createMainMenu('积分设置', $do, 'jfsz', ''),

                    )
                );
            }

            if ($system['fxgn'] == 1) {
                $navemenu[9] = array(
                    'title' => '<a href="index.php?c=site&a=entry&op=display&do=fxlist&m=zh_cjdianc" class="panel-title wytitle" id="yframe-9"><icon style="color:#8d8d8d;" class="fa fa-users"></icon>分销系统</a>',
                    'items' => array(
                        0 => $this->createMainMenu('分销商管理', $do, 'fxlist', ''),
                        1 => $this->createMainMenu('分销设置', $do, 'fxset', ''),
                        2 => $this->createMainMenu('提现申请', $do, 'fxtx', ''),
                        3 => $this->createMainMenu('分销订单', $do, 'fxorder', ''),
                    )
                );
            }
            if ($system['qggn'] == 1) {
                $navemenu[19] = array(
                    'title' => '<a href="index.php?c=site&a=entry&op=display&do=qgorderall&m=zh_cjdianc" class="panel-title wytitle" id="yframe-19"><icon style="color:#8d8d8d;" class="fa fa-life-ring"></icon>限时抢购</a>',
                    'items' => array(
                        0 => $this->createMainMenu('订单管理', $do, 'qgorderall', ''),
                        1 => $this->createMainMenu('商品管理', $do, 'qggoodall', ''),
                        2 => $this->createMainMenu('分类列表 ', $do, 'rushtype', ''),
                        3 => $this->createMainMenu('分类添加', $do, 'addrushtype', '')

                    )
                );
            }
            if ($system['ptgn'] == 1) {
                $navemenu[18] = array(
                    'title' => '<a href="index.php?c=site&a=entry&op=display&do=allgroupgoods&m=zh_cjdianc" class="panel-title wytitle" id="yframe-18"><icon style="color:#8d8d8d;" class="fa fa fa-newspaper-o"></icon>拼团管理</a>',
                    'items' => array(
                        2 => $this->createMainMenu('商品管理', $do, 'allgroupgoods', ''),
                        0 => $this->createMainMenu('分类列表 ', $do, 'grouptype', ''),
                        1 => $this->createMainMenu('分类添加', $do, 'addgrouptype', ''),

                    )
                );
            }
            // $navemenu[10] = array(
            //     'title' => '<icon style="color:#8d8d8d;" class="fa fa-cog"></icon>  充值中心',
            //     'items' => array(
            //        0 => $this->createMainMenu('充值优惠', $do, 'chongzhi', ''),
            //        1 => $this->createMainMenu('充值记录', $do, 'czjl', '')
            //     )
            // );
            //  $navemenu[11] = array(
            //     'title' => '<a href="index.php?c=site&a=entry&op=display&do=integral&m=zh_cjdianc" class="panel-title wytitle" id="yframe-11"><icon style="color:#8d8d8d;" class="fa fa-pencil-square-o"></icon>  签到管理</a>',
            //     'items' => array(
            //          0 => $this->createMainMenu('签到规则 ', $do, 'integral', '')
            //     )
            // );


            if ($system['hygn'] == 1) {
                $navemenu[16] = array(
                    'title' => '<a href="index.php?c=site&a=entry&op=display&do=hyuser&m=zh_cjdianc" class="panel-title wytitle" id="yframe-16"><icon style="color:#8d8d8d;" class="fa fa-star-half-o"></icon>  会员卡</a>',
                    'items' => array(
                        0 => $this->createMainMenu('会员列表', $do, 'hyuser', ''),
                        1 => $this->createMainMenu('会员期限', $do, 'hyqx', ''),
                        2 => $this->createMainMenu('会员优惠券', $do, 'hycoupons', ''),
                        3 => $this->createMainMenu('会员设置', $do, 'hyset', ''),
                    )
                );
            }
            $navemenu[17] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=message&m=zh_cjdianc" class="panel-title wytitle" id="yframe-17"><icon style="color:#8d8d8d;" class="fa fa-users"></icon>消息推送</a>',
                'items' => array(
                    0 => $this->createMainMenu('消息推送', $do, 'message', ''),
                    1 => $this->createMainMenu('发送记录', $do, 'messagelist', ''),
                )
            );
            $navemenu[12] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=user&m=zh_cjdianc" class="panel-title wytitle" id="yframe-12"><icon style="color:#8d8d8d;" class="fa fa-user"></icon>  用户管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('用户列表 ', $do, 'user', ''),
                )
            );
            $navemenu[13] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=txlist&m=zh_cjdianc" class="panel-title wytitle" id="yframe-13"><icon style="color:#8d8d8d;" class="fa fa-jpy"></icon>  财务管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('提现管理 ', $do, 'txlist', ''),
                    1 => $this->createMainMenu('提现设置 ', $do, 'txsz', ''),
                    2 => $this->createMainMenu('充值优惠', $do, 'chongzhi', ''),
                    3 => $this->createMainMenu('充值记录', $do, 'czjl', ''),
                    4 => $this->createMainMenu('充值设置', $do, 'czsz', '')
                )
            );
            // $navemenu[15] = array(
            //     'title' => '<a href="index.php?c=site&a=entry&op=display&do=analysis&m=zh_cjdianc" class="panel-title wytitle" id="yframe-15"><icon style="color:#8d8d8d;" class="fa fa-recycle"></icon>  经营分析</a>',
            //     'items' => array(
            //         0 => $this->createMainMenu('经营分析 ', $do, 'analysis', ''),
            //         // 1 => $this->createMainMenu('营业统计 ', $do, 'statistics', ''),
            //         // 2 => $this->createMainMenu('商家分析', $do, 'selleranaly', '')
            //     )
            // );
            $navemenu[7] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=assess&m=zh_cjdianc" class="panel-title wytitle" id="yframe-7"><icon style="color:#8d8d8d;" class="fa fa-money"></icon>评论管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('评论管理', $do, 'assess', ''),
                )
            );
            $navarr = array(
                0 => $this->createMainMenu('基本信息 ', $do, 'settings', ''),
                1 => $this->createMainMenu('小程序配置', $do, 'peiz', ''),
                2 => $this->createMainMenu('支付配置', $do, 'pay', ''),
                 //3 => $this->createMainMenu('收货配置', $do, 'address', ''),
                //  3 => $this->createMainMenu('达达配置 ', $do, 'dasettings', ''),
                //  4 => $this->createMainMenu('快服务', $do, 'kfw', ''),
                5 => $this->createMainMenu('模板消息', $do, 'template', ''),
                6 => $this->createMainMenu('帮助中心', $do, 'help', ''),
                // 6 => $this->createMainMenu('系统更新', $do, 'heli', ''),
                7 => $this->createMainMenu('短信设置', $do, 'sms', ''),
                8 => $this->createMainMenu('配送设置', $do, 'dispatch', ''),
            );
            if ($system['is_dada'] == 1) {
                array_push($navarr, $this->createMainMenu('达达配置 ', $do, 'dasettings', ''));
            }
            if ($system['is_kfw'] == 1) {
                array_push($navarr, $this->createMainMenu('快服务 ', $do, 'kfw', ''));
            }
            $navemenu[14] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=settings&m=zh_cjdianc" class="panel-title wytitle" id="yframe-14"><icon style="color:#8d8d8d;" class="fa fa-cog"></icon>系统设置</a>',
                'items' => $navarr,
            );
            if ($_W['role'] == 'founder') {
                $navemenu[15] = array(
                    'title' => '<a href="index.php?c=site&a=entry&op=display&do=wxapplist&m=zh_cjdianc" class="panel-title wytitle" id="yframe-15"><icon style="color:#8d8d8d;margin-right:15px;" class="fa fa-unlock-alt"></icon>  权限设置</a>',
                    'items' => array(
                        1 => $this->createMainMenu('小程序列表', $do, 'wxapplist', ''),

                    )
                );
            }


        }
        return $navemenu;
    }


    public function getMainMenu2()
    {
        global $_W, $_GPC;
        ini_set("memory_limit", "800M");
        set_time_limit(0);
        $system = pdo_get('cjdc_system', array('uniacid' => $_W['uniacid']));
        $do = $_GPC['do'];
        $navemenu = array();
        $cur_color = ' style="color:#d9534f;" ';
        if ($_W['isfounder'] || $_W['role'] == 'manager' || $_W['role'] == 'operator') {
            $navemenu[0] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=index&m=zh_cjdianc" class="panel-title wytitle" id="yframe-0"><icon style="color:#8d8d8d;" class="fa fa-key"></icon>  门店设置</a>',
                'items' => array(
                    0 => $this->createMainMenu('数据概况', $do, 'index', ''),
                    1 => $this->createMainMenu('门店信息 ', $do, 'storeinfo', ''),
                    2 => $this->createMainMenu('营业时间 ', $do, 'yingyetime', ''),
                    3 => $this->createMainMenu('配送设置 ', $do, 'peisongset', ''),
                    4 => $this->createMainMenu('送达时间 ', $do, 'service', ''),
                    5 => $this->createMainMenu('预约设置 ', $do, 'yyset', ''),
                    // 5 => $this->createMainMenu('呼叫服务员 ', $do, 'call', ''),
                    // 2 => $this->createMainMenu('销售统计 ', $do, 'ygdata', ''),
                    // 4 => $this->createMainMenu('员工管理 ', $do, 'test2', ''),
                    // 4 => $this->createMainMenu('积分设置 ', $do, 'injfset', ''),
                    // 5 => $this->createMainMenu('支付设置', $do, 'inpay', '')
                )
            );
            $navemenu[1] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=inorder&m=zh_cjdianc" class="panel-title wytitle" id="yframe-1"><icon style="color:#8d8d8d;" class="fa fa-bars"></icon>  订单管理</a>',
                'items' => array(

                    0 => $this->createMainMenu('外卖订单', $do, 'inorder', ''),
                    1 => $this->createMainMenu('店内订单', $do, 'indnorder', ''),
                    3 => $this->createMainMenu('当面付订单', $do, 'indmorder', ''),
                    4 => $this->createMainMenu('预约订单', $do, 'inydorder', ''),

                )
            );


            $navemenu[2] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=dishes2&m=zh_cjdianc" class="panel-title wytitle" id="yframe-2"><icon style="color:#8d8d8d;" class="fa fa-trophy"></icon>  商品管理</a>',
                'items' => array(
                    // 0 => $this->createMainMenu('商品列表 ', $do, 'dishes2', ''),
                    // 2 => $this->createMainMenu('商品分类', $do, 'dishestype', ''),
                    // 1 => $this->createMainMenu('添加菜品', $do, 'adddishes', ''),
                    // 3 => $this->createMainMenu('添加分类', $do, 'adddishestype', ''),
                )
            );
            $navemenu[5] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=inuser&m=zh_cjdianc" class="panel-title wytitle" id="yframe-5"><icon style="color:#8d8d8d;" class="fa fa-user"></icon>  会员管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('会员管理 ', $do, 'inuser', ''),

                )
            );
            $navemenu[9] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=storead&m=zh_cjdianc" class="panel-title wytitle" id="yframe-9"><icon style="color:#8d8d8d;" class="fa fa-life-ring"></icon>  广告管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('广告管理 ', $do, 'storead', ''),

                )
            );
            $navemenu[3] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=table&m=zh_cjdianc" class="panel-title wytitle" id="yframe-3"><icon style="color:#8d8d8d;" class="fa fa-binoculars"></icon>  餐桌管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('餐桌列表 ', $do, 'table', ''),
                    1 => $this->createMainMenu('餐桌类型', $do, 'tabletype', ''),
                    2 => $this->createMainMenu('餐桌设置', $do, 'intabelset', ''),
                    3 => $this->createMainMenu('预定时间', $do, 'reservation', ''),
                )

            );
            $navemenu[10] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=calllog&m=zh_cjdianc" class="panel-title wytitle" id="yframe-10"><icon style="color:#8d8d8d;margin-right:15px;" class="fa fa-sellsy"></icon>  呼叫服务</a>',
                'items' => array(

                    1 => $this->createMainMenu('呼叫记录', $do, 'calllog', ''),
                    2 => $this->createMainMenu('呼叫设置 ', $do, 'call', ''),

                )
            );
            $navemenu[13] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=number&m=zh_cjdianc" class="panel-title wytitle" id="yframe-13"><icon style="color:#8d8d8d;margin-right:15px;" class="fa fa-sellsy"></icon>  排队取号</a>',
                'items' => array(
                    0 => $this->createMainMenu('排队分类', $do, 'numbertype', ''),
                    3 => $this->createMainMenu('排队取号', $do, 'number', ''),

                )
            );
            $navemenu[4] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=ygquan&m=zh_cjdianc" class="panel-title wytitle" id="yframe-4"><icon style="color:#8d8d8d;" class="fa fa-gift"></icon>  营销设置</a>',
                'items' => array(
                    // 0 => $this->createMainMenu('营销插件 ', $do, 'ygquan', ''),
                    // 1 => $this->createMainMenu('满减活动 ', $do, 'injian', ''),
                    // 2 => $this->createMainMenu('优惠券 ', $do, 'incoupons', ''),
                    // 5 => $this->createMainMenu('达达配送 ', $do, 'dada', ''),
                    // 6 => $this->createMainMenu('快服务 ', $do, 'kfwset', ''),
                    // 8 => $this->createMainMenu('自动接单 ', $do, 'laoz', ''),


                    // 3 => $this->createMainMenu('代金券 ', $do, 'voucher', ''),
                    // 4 => $this->createMainMenu('积分设置 ', $do, 'injfset', ''),
                    // 4 => $this->createMainMenu('短信通道 ', $do, 'insms', ''),
                    //  7 => $this->createMainMenu('人人快递 ', $do, 'rrset', ''),
                    // 9 => $this->createMainMenu('门店媒体 ', $do, 'mt', '')
                )
            );
            // $navemenu[5] = array(
            //     'title' => '<a href="index.php?c=site&a=entry&op=display&do=ygdata&m=zh_cjdianc" class="panel-title wytitle" id="yframe-5"><icon style="color:#8d8d8d;" class="fa fa-key"></icon>  数据统计</a>',
            //     'items' => array(
            //         0 => $this->createMainMenu('销售统计', $do, 'ygdata', ''),
            //         1 => $this->createMainMenu('消费排行 ', $do, 'ygranking', ''),
            //         // 2 => $this->createMainMenu('经营分析 ', $do, 'inanalysis', ''),

            //     )
            // );
            if ($system['qggn'] == 1) {
                $navemenu[12] = array(
                    'title' => '<a href="index.php?c=site&a=entry&op=display&do=qggoods&m=zh_cjdianc" class="panel-title wytitle" id="yframe-12"><icon style="color:#8d8d8d;" class="fa fa-clipboard"></icon>  限时抢购</a>',
                    'items' => array(
                        0 => $this->createMainMenu('商品管理 ', $do, 'qggoods', ''),
                        1 => $this->createMainMenu('订单管理 ', $do, 'qgorder', ''),
                    )
                );
            }
            if ($system['ptgn'] == 1) {
                $navemenu[11] = array(
                    'title' => '<a href="index.php?c=site&a=entry&op=display&do=groupgoods&m=zh_cjdianc" class="panel-title wytitle" id="yframe-11"><icon style="color:#8d8d8d;" class="fa fa-clipboard"></icon>  拼团管理</a>',
                    'items' => array(
                        0 => $this->createMainMenu('商品管理 ', $do, 'groupgoods', ''),
                        1 => $this->createMainMenu('订单管理 ', $do, 'grouporder', ''),
                        3 => $this->createMainMenu('拼团管理 ', $do, 'group', ''),
                        4 => $this->createMainMenu('核销管理 ', $do, 'grouphx', ''),
                    )
                );
            }
            $navemenu[6] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=zjlist&m=zh_cjdianc" class="panel-title wytitle" id="yframe-6"><icon style="color:#8d8d8d;" class="fa fa-book"></icon>  资金管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('外卖资金管理', $do, 'zjlist', ''),
                    1 => $this->createMainMenu('店内资金管理 ', $do, 'dnzjlist', ''),
                    2 => $this->createMainMenu('收银管理 ', $do, 'dmzjlist', ''),
                    3 => $this->createMainMenu('预约资金管理 ', $do, 'yyzjlist', ''),
                    4 => $this->createMainMenu('抢购资金管理 ', $do, 'qgzjlist', ''),
                    5 => $this->createMainMenu('拼团资金管理 ', $do, 'ptzjlist', ''),
                    7 => $this->createMainMenu('财务管理 ', $do, 'finance', ''),
                    // 1 => $this->createMainMenu('申请提现', $do, 'intx', ''),
                    // 2 => $this->createMainMenu('提现流水', $do, 'intxlist', ''),
                )
            );


            $navemenu[7] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=comment&m=zh_cjdianc" class="panel-title wytitle" id="yframe-7"><icon style="color:#8d8d8d;" class="fa fa-money"></icon>  评论管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('评论管理', $do, 'comment', ''),
                )
            );


            $navemenu[8] = array(
                'title' => '<a href="index.php?c=site&a=entry&op=display&do=print&m=zh_cjdianc" class="panel-title wytitle" id="yframe-8"><icon style="color:#8d8d8d;" class="fa fa-clipboard"></icon>  打印设置</a>',
                'items' => array(
                    0 => $this->createMainMenu('打印设备 ', $do, 'print', ''),
                    1 => $this->createMainMenu('添加打印 ', $do, 'addprint', ''),
                    3 => $this->createMainMenu('打印标签 ', $do, 'printlabel', ''),
                    4 => $this->createMainMenu('打印设置 ', $do, 'printset', ''),
                )
            );
            // $navemenu[9] = array(
            //     'title' => '<a href="index.php?c=site&a=entry&op=display&do=assess2&m=zh_cjdianc" class="panel-title wytitle" id="yframe-9"><icon style="color:#8d8d8d;" class="fa fa-user"></icon>  评论管理</a>',
            //     'items' => array(
            //          0 => $this->createMainMenu('评论管理 ', $do, 'assess2', ''),
            //     )
            // );

        }
        return $navemenu;
    }


    public function getNaveMenu($storeid, $action, $uid = '')
    {
        global $_W, $_GPC;
        ini_set("memory_limit", "800M");
        set_time_limit(0);
        $system = pdo_get('cjdc_system', array('uniacid' => $_W['uniacid']));
        $account = pdo_get('cjdc_account', array('storeid' => $storeid, 'uid' => $uid));
        $storeInfo = pdo_get('cjdc_storeset', array('store_id' => $storeid));
        $array = array();
        if ($storeInfo['is_wm'] == 1) {
            array_push($array, $this->createSubMenu('外卖订单', $do, 'dlinorder', 'fa-angle-right', $cur_color, $storeid));
        }
        if ($storeInfo['is_dn'] == 1) {
            array_push($array, $this->createSubMenu('店内订单', $do, 'dlindnorder', 'fa-angle-right', $cur_color, $storeid));
        }

        if ($storeInfo['is_yy'] == 1) {
            array_push($array, $this->createSubMenu('预约订单', $do, 'dlinydorder', 'fa-angle-right', $cur_color, $storeid));
        }
        if ($storeInfo['is_sy'] == 1) {
            array_push($array, $this->createSubMenu('当面付订单', $do, 'dlindmorder', 'fa-angle-right', $cur_color, $storeid));
        }

        $do = $_GPC['do'];
        $navemenu = array();
        $cur_color = '#8d8d8d';
        if ($account['role'] == 1) {
            $navemenu[0] = array(
                'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=start&m=zh_cjdianc" class="panel-title wytitle" id="yframe-0"><icon style="color:#8d8d8d;" class="fa fa-cog"></icon>  门店设置</a>',
                'items' => array(
                    0 => $this->createSubMenu('数据概况', $do, 'start', 'fa-angle-right', $cur_color, $storeid),
                    1 => $this->createSubMenu('门店信息 ', $do, 'dlstoreinfo', 'fa-angle-right', $cur_color, $storeid),
                    2 => $this->createSubMenu('营业时间 ', $do, 'dlyingyetime', 'fa-angle-right', $cur_color, $storeid),
                    3 => $this->createSubMenu('配送设置 ', $do, 'dlpeisongset', 'fa-angle-right', $cur_color, $storeid),
                    4 => $this->createSubMenu('配送费 ', $do, 'dlpsmoney', 'fa-angle-right', $cur_color, $storeid),
                    5 => $this->createSubMenu('送达时间 ', $do, 'dlservice', 'fa-angle-right', $cur_color, $storeid),
                    6 => $this->createSubMenu('预约设置', $do, 'dlyyset', 'fa-angle-right', $cur_color, $storeid),
                    7 => $this->createSubMenu('账号修改', $do, 'modify', 'fa-angle-right', $cur_color, $storeid),
                ),
                'icon' => 'fa fa-user-md'
            );
            $cur_color = '#8d8d8d';
            $navemenu[1] = array(
                'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlinorder&m=zh_cjdianc" class="panel-title wytitle" id="yframe-1"><icon style="color:#8d8d8d;" class="fa fa-cog"></icon>  订单管理</a>',

                'items' => $array

            );
            $cur_color = '#8d8d8d';
            $navemenu[2] = array(
                'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dldishes2&m=zh_cjdianc" class="panel-title wytitle" id="yframe-2"><icon style="color:' . $cur_color . ';" class="fa fa-trophy"></icon> 商品管理</a>',
            );
             $navemenu[5] = array(
                        'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlinuser&m=zh_cjdianc" class="panel-title wytitle" id="yframe-5"><icon style="color:#8d8d8d;" class="fa fa-user"></icon>会员管理</a>',
                        'items' => array(
                            0 => $this->createSubMenu('会员管理 ', $do, 'dlinuser', 'fa-angle-right', $cur_color, $storeid),

                        )
                    );
            $navemenu[9] = array(
                'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlstoread&m=zh_cjdianc" class="panel-title wytitle" id="yframe-9"><icon style="color:#8d8d8d;" class="fa fa-life-ring"></icon>  广告管理</a>',
                'items' => array(
                    0 => $this->createSubMenu('广告管理 ', $do, 'dlstoread', 'fa-angle-right', $cur_color, $storeid),

                )
            );
            $cur_color = '#8d8d8d';

            if ($storeInfo['is_dn'] == 1 || $storeInfo['is_yy'] == 1) {
                $navemenu[3] = array(
                    'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dltable2&m=zh_cjdianc" class="panel-title wytitle" id="yframe-3"><icon style="color:' . $cur_color . ';" class="fa fa-binoculars"></icon>  餐桌管理</a>',
                    'items' => array(
                        0 => $this->createSubMenu('餐桌列表 ', $do, 'dltable2', 'fa-angle-right', $cur_color, $storeid),
                        1 => $this->createSubMenu('餐桌类型', $do, 'dltabletype2', 'fa-angle-right', $cur_color, $storeid),
                        5 => $this->createSubMenu('餐桌设置', $do, 'dlintabelset', 'fa-angle-right', $cur_color, $storeid),
                        3 => $this->createSubMenu('预定时间', $do, 'dlreservation', 'fa-angle-right', $cur_color, $storeid),
                    ),
                );

            }
            if ($storeInfo['is_dn'] == 1) {
                $navemenu[10] = array(
                    'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlcalllog&m=zh_cjdianc" class="panel-title wytitle" id="yframe-10"><icon style="color:#8d8d8d;margin-right:15px;" class="fa fa-sellsy"></icon>  呼叫服务</a>',
                    'items' => array(

                        1 => $this->createSubMenu('呼叫记录', $do, 'dlcalllog', 'fa-angle-right', $cur_color, $storeid),
                        2 => $this->createSubMenu('呼叫设置 ', $do, 'dlcall', 'fa-angle-right', $cur_color, $storeid),

                    )
                );
            }
            if ($storeInfo['is_pd']) {
                $navemenu[13] = array(
                    'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlnumber&m=zh_cjdianc" class="panel-title wytitle" id="yframe-13"><icon style="color:#8d8d8d;margin-right:15px;" class="fa fa-sellsy"></icon>  排队取号</a>',
                    'items' => array(
                        0 => $this->createSubMenu('排队分类', $do, 'dlnumbertype', 'fa-angle-right', $cur_color, $storeid),
                        3 => $this->createSubMenu('排队取号', $do, 'dlnumber', 'fa-angle-right', $cur_color, $storeid),

                    )
                );
            }
            $cur_color = '#8d8d8d';
            $navemenu[4] = array(
                'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlygquan&m=zh_cjdianc" class="panel-title wytitle" id="yframe-4"><icon style="color:' . $cur_color . ';" class="fa fa-gift"></icon>  营销设置</a>',

            );
            if ($system['qggn'] == 1 and $storeInfo['is_qg']) {
                $navemenu[12] = array(
                    'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlqgorder&m=zh_cjdianc" class="panel-title wytitle" id="yframe-12"><icon style="color:#8d8d8d;" class="fa fa-clipboard"></icon>  限时抢购</a>',
                    'items' => array(
                        0 => $this->createSubMenu('订单管理 ', $do, 'dlqgorder', 'fa-angle-right', $cur_color, $storeid),
                        1 => $this->createSubMenu('商品管理 ', $do, 'dlqggoods', 'fa-angle-right', $cur_color, $storeid),
                    )
                );
            }
            if ($system['ptgn'] == 1 and $storeInfo['is_pt']) {
                $navemenu[11] = array(
                    'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlgroupgoods&m=zh_cjdianc" class="panel-title wytitle" id="yframe-11"><icon style="color:#8d8d8d;" class="fa fa-clipboard"></icon>  拼团管理</a>',
                    'items' => array(
                        0 => $this->createSubMenu('商品管理 ', $do, 'dlgroupgoods', 'fa-angle-right', $cur_color, $storeid),
                        1 => $this->createSubMenu('订单管理 ', $do, 'dlgrouporder', 'fa-angle-right', $cur_color, $storeid),
                        3 => $this->createSubMenu('拼团管理 ', $do, 'dlgroup', 'fa-angle-right', $cur_color, $storeid),
                        4 => $this->createSubMenu('核销管理 ', $do, 'dlgrouphx', 'fa-angle-right', $cur_color, $storeid),
                    )
                );
            }
            $navemenu[6] = array(
                'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlzjlist&m=zh_cjdianc" class="panel-title wytitle" id="yframe-6"><icon style="color:#8d8d8d;" class="fa fa-book"></icon>  资金管理</a>',
                'items' => array(
                    0 => $this->createSubMenu('外卖资金管理', $do, 'dlzjlist', 'fa-angle-right', $cur_color, $storeid),
                    1 => $this->createSubMenu('店内资金管理 ', $do, 'dldnzjlist', 'fa-angle-right', $cur_color, $storeid),
                    2 => $this->createSubMenu('收银管理 ', $do, 'dldmzjlist', 'fa-angle-right', $cur_color, $storeid),
                    3 => $this->createSubMenu('预约资金管理 ', $do, 'dlyyzjlist', 'fa-angle-right', $cur_color, $storeid),
                    4 => $this->createSubMenu('抢购资金管理 ', $do, 'dlqgzjlist', 'fa-angle-right', $cur_color, $storeid),
                    5 => $this->createSubMenu('拼团资金管理 ', $do, 'dlptzjlist', 'fa-angle-right', $cur_color, $storeid),
                    7 => $this->createSubMenu('财务管理 ', $do, 'dlfinance', 'fa-angle-right', $cur_color, $storeid),
                )
            );
            $navemenu[7] = array(
                'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlcomment&m=zh_cjdianc" class="panel-title wytitle" id="yframe-7"><icon style="color:#8d8d8d;" class="fa fa-money"></icon>  评论管理</a>',
                'items' => array(
                    0 => $this->createMainMenu('评论管理', $do, 'dlcomment', 'fa-angle-right', $cur_color, $storeid),
                )
            );
            $cur_color = '#8d8d8d';
            $navemenu[8] = array(
                'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlprint&m=zh_cjdianc" class="panel-title wytitle" id="yframe-8"><icon style="color:' . $cur_color . ';" class="fa fa-database"></icon>  打印设置</a>',
                'items' => array(
                    0 => $this->createSubMenu('打印设备 ', $do, 'dlprint', 'fa-angle-right', $cur_color, $storeid),
                    1 => $this->createSubMenu('添加打印 ', $do, 'dladdprint', 'fa-angle-right', $cur_color, $storeid),
                    3 => $this->createSubMenu('打印标签 ', $do, 'dlprintlabel', 'fa-angle-right', $cur_color, $storeid),
                    4 => $this->createSubMenu('打印设置 ', $do, 'dlprintset', 'fa-angle-right', $cur_color, $storeid),
                )
            );

            $navemenu[14] = array(

                'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlaccount&m=zh_cjdianc" class="panel-title wytitle" id="yframe-14"><icon style="color:#8d8d8d;" class="fa fa-newspaper-o"></icon>  账号管理</a>',
                'items' => array(

                    1 => $this->createSubMenu('账号管理 ', $do, 'dlaccount', 'fa-angle-right', $cur_color, $storeid),
                    2 => $this->createSubMenu('账号添加 ', $do, 'dladdaccount', 'fa-angle-right', $cur_color, $storeid),
                )
            );


        } else {
            $arr = explode(',', $account['authority']);
            foreach ($arr as $key => $value) {
                if ($value == 'start') {
                    $navemenu[0] = array(
                        'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=start&m=zh_cjdianc" class="panel-title wytitle" id="yframe-0"><icon style="color:#8d8d8d;" class="fa fa-cog"></icon>  门店设置</a>',
                        'items' => array(
                            0 => $this->createSubMenu('数据概况', $do, 'start', 'fa-angle-right', $cur_color, $storeid),
                            1 => $this->createSubMenu('门店信息 ', $do, 'dlstoreinfo', 'fa-angle-right', $cur_color, $storeid),
                            2 => $this->createSubMenu('营业时间 ', $do, 'dlyingyetime', 'fa-angle-right', $cur_color, $storeid),
                            3 => $this->createSubMenu('配送设置 ', $do, 'dlpeisongset', 'fa-angle-right', $cur_color, $storeid),
                            4 => $this->createSubMenu('配送费 ', $do, 'dlpsmoney', 'fa-angle-right', $cur_color, $storeid),
                            5 => $this->createSubMenu('送达时间 ', $do, 'dlservice', 'fa-angle-right', $cur_color, $storeid),
                            6 => $this->createSubMenu('预约设置', $do, 'dlyyset', 'fa-angle-right', $cur_color, $storeid),
                        ),
                        'icon' => 'fa fa-user-md'
                    );

                }
                if ($value == 'dlinorder') {
                    $navemenu[1] = array(
                        'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlinorder&m=zh_cjdianc" class="panel-title wytitle" id="yframe-1"><icon style="color:#8d8d8d;" class="fa fa-cog"></icon>  订单管理</a>',

                        'items' => $array

                    );
                }

                if ($value == 'dldishes2') {
                    $navemenu[2] = array(
                        'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dldishes2&m=zh_cjdianc" class="panel-title wytitle" id="yframe-2"><icon style="color:' . $cur_color . ';" class="fa fa-trophy"></icon> 商品管理</a>',
                    );
                }
               

                if ($value == 'dlstoread') {
                    $navemenu[9] = array(
                        'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlstoread&m=zh_cjdianc" class="panel-title wytitle" id="yframe-9"><icon style="color:#8d8d8d;" class="fa fa-life-ring"></icon>广告管理</a>',
                        'items' => array(
                            0 => $this->createSubMenu('广告管理 ', $do, 'dlstoread', 'fa-angle-right', $cur_color, $storeid),

                        )
                    );
                }
                if ($value == 'dltable2') {
                    if ($storeInfo['is_dn'] == 1 || $storeInfo['is_yy'] == 1) {
                        $navemenu[3] = array(
                            'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dltable2&m=zh_cjdianc" class="panel-title wytitle" id="yframe-3"><icon style="color:' . $cur_color . ';" class="fa fa-binoculars"></icon>  餐桌管理</a>',
                            'items' => array(
                                0 => $this->createSubMenu('餐桌列表 ', $do, 'dltable2', 'fa-angle-right', $cur_color, $storeid),
                                1 => $this->createSubMenu('餐桌类型', $do, 'dltabletype2', 'fa-angle-right', $cur_color, $storeid),
                                5 => $this->createSubMenu('餐桌设置', $do, 'dlintabelset', 'fa-angle-right', $cur_color, $storeid),
                                3 => $this->createSubMenu('预定时间', $do, 'dlreservation', 'fa-angle-right', $cur_color, $storeid),
                            ),
                        );

                    }
                }
                if ($value == 'dlcalllog') {
                    if ($storeInfo['is_dn'] == 1) {
                        $navemenu[10] = array(
                            'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlcalllog&m=zh_cjdianc" class="panel-title wytitle" id="yframe-10"><icon style="color:#8d8d8d;margin-right:15px;" class="fa fa-sellsy"></icon>  呼叫服务</a>',
                            'items' => array(

                                1 => $this->createSubMenu('呼叫记录', $do, 'dlcalllog', 'fa-angle-right', $cur_color, $storeid),
                                2 => $this->createSubMenu('呼叫设置 ', $do, 'dlcall', 'fa-angle-right', $cur_color, $storeid),

                            )
                        );
                    }
                }
                if ($value == 'dlygquan') {
                    $navemenu[4] = array(
                        'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlygquan&m=zh_cjdianc" class="panel-title wytitle" id="yframe-4"><icon style="color:' . $cur_color . ';" class="fa fa-gift"></icon>  营销设置</a>',

                    );
                }
                if ($value == 'dlzjlist') {
                    $navemenu[6] = array(
                        'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlzjlist&m=zh_cjdianc" class="panel-title wytitle" id="yframe-6"><icon style="color:#8d8d8d;" class="fa fa-book"></icon> 资金管理</a>',
                        'items' => array(
                            0 => $this->createSubMenu('外卖资金管理', $do, 'dlzjlist', 'fa-angle-right', $cur_color, $storeid),
                            1 => $this->createSubMenu('店内资金管理 ', $do, 'dldnzjlist', 'fa-angle-right', $cur_color, $storeid),
                            2 => $this->createSubMenu('当面付资金管理 ', $do, 'dldmzjlist', 'fa-angle-right', $cur_color, $storeid),
                            3 => $this->createSubMenu('预约资金管理 ', $do, 'dlyyzjlist', 'fa-angle-right', $cur_color, $storeid),
                            4 => $this->createSubMenu('抢购资金管理 ', $do, 'dlqgzjlist', 'fa-angle-right', $cur_color, $storeid),
                            5 => $this->createSubMenu('拼团资金管理 ', $do, 'dlptzjlist', 'fa-angle-right', $cur_color, $storeid),
                            7 => $this->createSubMenu('财务管理 ', $do, 'dlfinance', 'fa-angle-right', $cur_color, $storeid),
                        )
                    );
                }
                if ($value == 'dlcomment') {
                    $navemenu[7] = array(
                        'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlcomment&m=zh_cjdianc" class="panel-title wytitle" id="yframe-7"><icon style="color:#8d8d8d;" class="fa fa-money"></icon>  评论管理</a>',
                        'items' => array(
                            0 => $this->createMainMenu('评论管理', $do, 'dlcomment', 'fa-angle-right', $cur_color, $storeid),
                        )
                    );
                }
                if ($value == 'dlprint') {
                    $navemenu[8] = array(
                        'title' => '<a href="cjdianc.php?c=site&a=entry&op=display&do=dlprint&m=zh_cjdianc" class="panel-title wytitle" id="yframe-8"><icon style="color:' . $cur_color . ';" class="fa fa-database"></icon>  打印设置</a>',
                        'items' => array(
                            0 => $this->createSubMenu('打印设备 ', $do, 'dlprint', 'fa-angle-right', $cur_color, $storeid),
                            1 => $this->createSubMenu('添加打印 ', $do, 'dladdprint', 'fa-angle-right', $cur_color, $storeid),
                            3 => $this->createSubMenu('打印标签 ', $do, 'dlprintlabel', 'fa-angle-right', $cur_color, $storeid),
                            4 => $this->createSubMenu('打印设置 ', $do, 'dlprintset', 'fa-angle-right', $cur_color, $storeid),
                        )
                    );
                }

            }

        }


        return $navemenu;
    }


    function createCoverMenu($title, $method, $op, $icon = "fa-image", $color = '#d9534f')
    {
        global $_GPC, $_W;
        $cur_op = $_GPC['op'];
        $color = ' style="color:' . $color . ';" ';
        return array('title' => $title, 'url' => $op != $cur_op ? $this->createWebUrl($method, array('op' => $op)) : '',
            'active' => $op == $cur_op ? ' active' : '',
            'append' => array(
                'title' => '<i class="fa fa-angle-right"></i>',
            )
        );
    }

    function createMainMenu($title, $do, $method, $icon = "fa-image", $color = '')
    {
        $color = ' style="color:' . $color . ';" ';

        return array('title' => $title, 'url' => $do != $method ? $this->createWebUrl($method, array('op' => 'display')) : '',
            'active' => $do == $method ? ' active' : '',
            'append' => array(
                'title' => '<i ' . $color . ' class="fa fa-angle-right"></i>',
            )
        );
    }

    /*  function createSubMenu($title, $do, $method, $icon = "fa-image", $color = '#d9534f', $storeid)
      {
          $color = ' style="color:'.$color.';" ';
          $url = $this->createWebUrl($method, array('op' => 'display', 'storeid' => $storeid));
          if ($method == 'stores') {
              $url = $this->createWebUrl('stores', array('op' => 'post', 'id' => $storeid, 'storeid' => $storeid));
          }

          return array('title' => $title, 'url' => $do != $method ? $url : '',
              'active' => $do == $method ? ' active' : '',
              'append' => array(
                  'title' => '<i class="fa '.$icon.'"></i>',
              )
          );
      }

  */
    function createWebUrl2($do, $query = array())
    {
        $query['do'] = $do;
        $query['m'] = strtolower($this->modulename);

        return $this->wurl('site/entry', $query);
    }

    function wurl($segment, $params = array())
    {

        list($controller, $action, $do) = explode('/', $segment);
        $url = './cjdianc.php?';
        if (!empty($controller)) {
            $url .= "c={$controller}&";
        }
        if (!empty($action)) {
            $url .= "a={$action}&";
        }
        if (!empty($do)) {
            $url .= "do={$do}&";
        }
        if (!empty($params)) {
            $queryString = http_build_query($params, '', '&');
            $url .= $queryString;
        }
        return $url;
    }

    function createSubMenu($title, $do, $method, $icon = "fa-image", $color = '#d9534f', $storeid)
    {
        $color = ' style="color:' . $color . ';" ';
        $url = $this->createWebUrl2($method, array('op' => 'display', 'storeid' => $storeid));
        if ($method == 'stores2') {
            $url = $this->createWebUrl2('stores2', array('op' => 'post', 'id' => $storeid, 'storeid' => $storeid));
        }


        return array('title' => $title, 'url' => $do != $method ? $url : '',
            'active' => $do == $method ? ' active' : '',
            'append' => array(
                'title' => '<i class="fa ' . $icon . '"></i>',
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


    public function qtPrint($order_id)
    { //前台打印
        global $_W, $_GPC;
        include IA_ROOT . '/addons/zh_cjdianc/print/dyj.php';
        $res = pdo_get('cjdc_order', array('id' => $order_id));
        $store = pdo_get('cjdc_store', array('id' => $res['store_id']), 'name');
        $res3 = pdo_getall('cjdc_dyj', array('store_id' => $res['store_id'], 'state' => 1, 'location' => 1));
        $res2 = pdo_getall('cjdc_order_goods', array('order_id' => $order_id));
        if ($res['type'] == 2) {
            $sql = " select a.name,b.name as type_name from " . tablename('cjdc_table') . " a left join " . tablename('cjdc_table_type') . " b on a.type_id=b.id where a.id={$res['table_id']}";
            $table = pdo_fetch($sql);
        }
        if ($res['pay_type'] == 1) {
            $is_yue = "微信支付";
        } elseif ($res['pay_type'] == 2) {
            $is_yue = "余额支付";
        } elseif ($res['pay_type'] == 3) {
            $is_yue = "积分支付";
        } elseif ($res['pay_type'] == 4) {
            $is_yue = "货到付款";
        } elseif ($res['pay_type'] == 5) {
            $is_yue = "餐后付款";
        }

        if ($res['order_type'] == 2) {
            $is_take = "店内自提\n";
        } elseif ($res['order_type'] == 3) {
            $is_take = "预约到店";
        }
        foreach ($res3 as $key => $value) {
            //var_dump($res3);die;
            $style = "\n";
            if ($value['type'] == 3) {
                $style = "<BR>";
            }
            if ($value['type'] == 4) {
                $style = "<0D0A>";
            }

            $content = $style;
            if ($value['type'] == 1 && $value['num']) {
                $content = "^N" . $value['num'];
            }
            if ($res['type'] == 4) {
                $user = pdo_get('cjdc_user', array('id' => $res['user_id']), 'name');
                $content .= "              收银台" . $style . $style . $style;
                $content .= "--------------------------------" . $style;
                $content .= "金额    ：" . $res['money'] . "元" . $style;
                $content .= "--------------------------------" . $style;
                $content .= "付款人  ：" . $user['name'] . $style;
                $content .= "--------------------------------" . $style;
                $content .= "商家名称：" . $store['name'] . $style;
                $content .= "--------------------------------" . $style;
                $content .= "付款时间：" . $res['time'] . $style;
                $content .= "--------------------------------" . $style;
                $content .= "流水号：" . $res['order_num'] . $style;

            } else {
                $content .= "          " . $store['name'] . $style . $style;
                $content .= "         订单编号  #" . $res['id'] . $style;
                if ($res['type'] == 1) {
                    $content .= "          " . $is_take . $style;
                    $content .= "          " . $res3['dyj_title'] . $style;
                    $content .= "------------" . $is_yue . "------------" . $style;

                    $content .= "--------------------------------" . $style;
                    $content .= "下单时间：" . $res['time'] . $style;
                }
                if ($res['type'] == 2) {
                    $content .= "------------" . $is_yue . "------------" . $style;
                    $content .= "--------------------------------" . $style;
                    $content .= "开台时间：" . $res['time'] . $style . $style;
                    $content .= "桌号：" . $table['type_name'] . '(' . $table['name'] . ')' . $style;
                }
                $content .= "--------------------------------" . $style;
                if ($res['order_type'] == 2 && $res['type'] == 1) {
                    $content .= "自提时间：" . $res['delivery_time'] . $style;
                    $content .= "--------------------------------" . $style;
                }
                if ($res['order_type'] == 1 && $res['type'] == 1) {
                    $content .= "送达时间：" . $res['delivery_time'] . $style;
                    $content .= "--------------------------------" . $style;
                }
                $content .= '名称' . str_repeat(" ", 15) . "数量  价格" . $style;
                $content .= "--------------------------------" . $style;
                $name = '';
                for ($i = 0; $i < count($res2); $i++) {
                    $name = $res2[$i]['name'];
                    if ($res2[$i]['spec']) {
                        $name = $res2[$i]['name'] . '(' . $res2[$i]['spec'] . ')';
                    }
                    $content .= "" . $name . "$style";
                    $content .= str_repeat(" ", 20) . $res2[$i]['number'] . "   " . number_format($res2[$i]['number'] * $res2[$i]['money'], 2) . $style;
                }
                $content .= "--------------------------------" . $style;
                if ($res['type'] == 1) {
                    $content .= "包装费：　　　　　　　　 " . $res['box_money'] . $style;
                    $content .= "--------------------------------" . $style;
                    $content .= "配送费：　　　　　　　　 " . $res['ps_money'] . $style;
                    $content .= "--------------------------------" . $style;
                    if ($res['mj_money'] > 0) {
                        $content .= "满减优惠：　　　　　　　-" . number_format($res['mj_money'], 2) . $style;
                    }
                    if ($res['xyh_money'] > 0) {
                        $content .= "新用户立减：　　　　　　-" . number_format($res['xyh_money'], 2) . $style;
                        $content .= "--------------------------------." . $style;
                    }
                    if ($res['zk_money'] > 0) {
                        $content .= "会员折扣：　　　　　　-" . number_format($res['zk_money'], 2) . $style;
                        $content .= "--------------------------------." . $style;
                    }


                    $content .= "已付：　　　　　　　　　 " . $res['money'] . $style;
                    $content .= "--------------------------------" . $style;
                    $content .= "流水号：" . $res['order_num'] . $style;
                    $content .= "送货地点：" . $res['address'] . $style;
                    $content .= "联系电话：" . $res['tel'] . $style;
                    $content .= "联系人：" . $res['name'] . $style;
                }
                if ($res['type'] == 2) {

                    if ($res['mj_money'] > 0) {
                        $content .= "满减优惠：　　　　　　　-" . number_format($res['mj_money'], 2) . $style;
                        $content .= "--------------------------------." . $style;
                    }
                    if ($res['pay_type'] == 5) {
                        $content .= "应付：　　　　　　　　　 " . $res['money'] . $style;
                    } else {
                        $content .= "已付：　　　　　　　　　 " . $res['money'] . $style;
                    }
                    $content .= "--------------------------------" . $style;
                    $content .= "流水号：" . $res['order_num'] . $style;
                }
                if ($res['note']) {
                    $content .= "备注：" . $res['note'] . $style;
                }
            }
            if ($value['type'] == 1) {//365
                $rst = Dyj::dy($value['dyj_id'], $content, $value['dyj_key']);
            }
            if ($value['type'] == 2) {//易联云
                $rst = Dyj::ylydy($value['api'], $value['token'], $value['yy_id'], $value['mid'], $content);
                //var_dump($rst);
                //die;
            }
            if ($value['type'] == 3) {//飞蛾
                $rst = Dyj::fedy($value['fezh'], $value['fe_ukey'], $value['fe_dycode'], $content, $value['num']);
            }
            if ($value['type'] == 4) {//喜讯
                $url = "115.28.15.113:60002";
                $pages = empty($value['num']) ? 1 : $value['num'];
                $data = array(
                    'dingdanID' => 'dingdanID=' . $res['order_num'], //订单号
                    'dayinjisn' => 'dayinjisn=' . $value['xx_sn'], //打印机ID号
                    'dingdan' => 'dingdan=' . $content, //订单内容
                    'pages' => 'pages=' . $pages, //联数
                    'replyURL' => 'replyURL=1'); //回复确认URL
                $post_data = implode('&', $data);
                $rst = Dyj::postData($url, $post_data);
                //var_dump($rst);die;

            }

        }

    }

//后厨打印
    public function hcPrint($order_id)
    {
        global $_W, $_GPC;
        include IA_ROOT . '/addons/zh_cjdianc/print/hcdyj.php';
        $res = pdo_get('cjdc_order', array('id' => $order_id));
        $sql = "select a.*,b.label_id from" . tablename('cjdc_order_goods') . "a left join " . tablename('cjdc_goods') . " b on a.dishes_id=b.id  where a.order_id={$order_id}";
        $res2 = pdo_fetchall($sql);
        if ($res['type'] == 2) {
            //$table = pdo_get('cjdc_table', array('id' => $res['table_id']));
            $sql = " select a.name,b.name as type_name from " . tablename('cjdc_table') . " a left join " . tablename('cjdc_table_type') . " b on a.type_id=b.id where a.id={$res['table_id']}";
            $table = pdo_fetch($sql);
        }

        //$res2=pdo_getall('cjdc_order_goods',array('order_id'=>$order_id));
        $result = array();

        //判断打印类型
        $type = pdo_get('cjdc_storeset', array('store_id' => $res['store_id']));
        if ($type['print_type'] == 2) {
            //按相同标签组成新的数组
            foreach ($res2 as $k => $v) {
                $result[$v['label_id']][] = $v;
            }
            foreach ($result as $key => $value) {
                $content1 = '';
                $res3 = pdo_getall('cjdc_dyj', array('store_id' => $res['store_id'], 'state' => 1, 'location' => 2, 'tag_id' => $key));
                $content1 .= "         订单编号  #" . $_GPC['order_id'] . "\n\n";
                $content1 .= "       " . $res3[0]['dyj_title'] . "\n\n";
                $content1 .= "开台时间：" . $res['time'] . "\n\n";
                if ($res['type'] == 2) {
                    $content1 .= "桌号：" . $table['type_name'] . '(' . $table['name'] . ")\n\n";
                }
                $content1 .= "--------------------------------" . "\n";
                $content1 .= '名称' . str_repeat(" ", 15) . "数量\n\n";
                $content = '';
                foreach ($value as $key2 => $value2) {
                    $content .= "" . $value2['name'] . "\n";
                    $content .= str_repeat(" ", 20) . $value2['number'] . "\n";
                }
                if ($res3) {
                    foreach ($res3 as $key3 => $value3) {
                        $content = $content1 . $content;
                        if ($value3['type'] == 1) {//365
                            $rst = Hcdyj::dy($value3['dyj_id'], $content, $value3['dyj_key']);
                        }
                        if ($value3['type'] == 2) {//易联云

                            $rst = Hcdyj::ylydy($value3['api'], $value3['token'], $value3['yy_id'], $value3['mid'], $content);

                        }
                        if ($value['type'] == 3) {//飞蛾
                            $rst = Hcdyj::fedy($value3['fezh'], $value3['fe_ukey'], $value3['fe_dycode'], $content, $value3['num']);
                        }
                        if ($value['type'] == 4) {//喜讯
                            $url = "115.28.15.113:60002";
                            $pages = empty($value3['num']) ? 1 : $value3['num'];
                            $data = array(
                                'dingdanID' => 'dingdanID=' . $res['order_num'], //订单号
                                'dayinjisn' => 'dayinjisn=' . $value3['xx_sn'], //打印机ID号
                                'dingdan' => 'dingdan=' . $content, //订单内容
                                'pages' => 'pages=' . $pages, //联数
                                'replyURL' => 'replyURL=1'); //回复确认URL
                            $post_data = implode('&', $data);
                            $rst = Hcdyj::postData($url, $post_data);

                        }
                    }
                }
            }

        } else {
            $res3 = pdo_getall('cjdc_dyj', array('store_id' => $res['store_id'], 'state' => 1, 'location' => 2));
            $content1 .= "         订单编号  #" . $order_id . "\n\n";
            $content1 .= "          " . $res3[0]['dyj_title'] . "\n\n";
            $content1 .= "下单时间：" . $res['time'] . "\n\n";
            $content1 .= '名称' . str_repeat(" ", 15) . "数量\n\n";
            $content1 .= "--------------------------------" . "\n";
            $content = '';
            foreach ($res2 as $key2 => $value2) {
                $content .= "" . $value2['name'] . "\n";
                $content .= str_repeat(" ", 20) . $value2['number'] . "\n";
            }

            foreach ($res3 as $key => $value) {
                $content = $content1 . $content;
                if ($value['type'] == 1) {//365
                    $rst = Hcdyj::dy($value['dyj_id'], $content, $value['dyj_key']);
                }
                if ($value['type'] == 2) {//易联云
                    $rst = Hcdyj::ylydy($value['api'], $value['token'], $value['yy_id'], $value['mid'], $content);
                }
                if ($value['type'] == 3) {//飞蛾
                    $rst = Hcdyj::fedy($value['fezh'], $value['fe_ukey'], $value['fe_dycode'], $content, $value['num']);
                }
                if ($value['type'] == 4) {//喜讯
                    $url = "115.28.15.113:60002";
                    $pages = empty($value['num']) ? 1 : $value['num'];
                    $data = array(
                        'dingdanID' => 'dingdanID=' . $res['order_num'], //订单号
                        'dayinjisn' => 'dayinjisn=' . $value['xx_sn'], //打印机ID号
                        'dingdan' => 'dingdan=' . $content, //订单内容
                        'pages' => 'pages=' . $pages, //联数
                        'replyURL' => 'replyURL=1'); //回复确认URL
                    $post_data = implode('&', $data);
                    $rst = Hcdyj::postData($url, $post_data);

                }
            }
        }

    }


    //订单退款
    public function wxrefund($order_id)
    {
        global $_W, $_GPC;
        include_once IA_ROOT . '/addons/zh_cjdianc/cert/WxPay.Api.php';
        load()->model('account');
        load()->func('communication');
        $refund_order = pdo_get('cjdc_order', array('id' => $order_id));
        $WxPayApi = new WxPayApi();
        $input = new WxPayRefund();
        $path_cert = IA_ROOT . "/addons/zh_cjdianc/cert/" . 'apiclient_cert_' . $_W['uniacid'] . '.pem';
        $path_key = IA_ROOT . "/addons/zh_cjdianc/cert/" . 'apiclient_key_' . $_W['uniacid'] . '.pem';
        $account_info = $_W['account'];
        $res = pdo_get('cjdc_pay', array('uniacid' => $_W['uniacid']));
        $system = pdo_get('cjdc_system', array('uniacid' => $_W['uniacid']));

        $appid = $system['appid'];
        $key = $res['wxkey'];
        $mchid = $res['mchid'];
        $out_trade_no = $refund_order['code'];
        $fee = $refund_order['money'] * 100;
        $input->SetAppid($appid);
        $input->SetMch_id($mchid);
        $input->SetOp_user_id($mchid);
        $input->SetRefund_fee($fee);
        $input->SetTotal_fee($fee);
        // $input->SetTransaction_id($refundid);
        $input->SetOut_refund_no($refund_order['order_num']);
        $input->SetOut_trade_no($out_trade_no);
        $result = $WxPayApi->refund($input, 6, $path_cert, $path_key, $key);
        return $result;
    }


    //商户退款
    public function shrefund($sh_id)
    {
        global $_W, $_GPC;
        include_once IA_ROOT . '/addons/zh_cjdianc/cert/WxPay.Api.php';
        load()->model('account');
        load()->func('communication');
        $refund_order = pdo_get('cjdc_store', array('id' => $sh_id));
        $WxPayApi = new WxPayApi();
        $input = new WxPayRefund();
        $path_cert = IA_ROOT . "/addons/zh_cjdianc/cert/" . 'apiclient_cert_' . $_W['uniacid'] . '.pem';
        $path_key = IA_ROOT . "/addons/zh_cjdianc/cert/" . 'apiclient_key_' . $_W['uniacid'] . '.pem';
        $account_info = $_W['account'];
        $res = pdo_get('cjdc_pay', array('uniacid' => $_W['uniacid']));
        $system = pdo_get('cjdc_system', array('uniacid' => $_W['uniacid']));

        $appid = $system['appid'];
        $key = $res['wxkey'];
        $mchid = $res['mchid'];
        $out_trade_no = $refund_order['code'];
        $fee = $refund_order['money'] * 100;
        $input->SetAppid($appid);
        $input->SetMch_id($mchid);
        $input->SetOp_user_id($mchid);
        $input->SetRefund_fee($fee);
        $input->SetTotal_fee($fee);
        // $input->SetTransaction_id($refundid);
        $input->SetOut_refund_no($refund_order['order_num']);
        $input->SetOut_trade_no($out_trade_no);
        $result = $WxPayApi->refund($input, 6, $path_cert, $path_key, $key);
        return $result;
    }

//达达
    public function dada($order_id)
    {
        global $_W, $_GPC;
        include IA_ROOT . '/addons/zh_cjdianc/peisong/peisong.php';
        $order = pdo_get('cjdc_order', array('id' => $order_id));
        $set = pdo_get('cjdc_psset', array('store_id' => $order['store_id']));
        $system = pdo_get('cjdc_system', array('uniacid' => $_W['uniacid']));
//*********************配置项*************************
        $config = array();
        $config['app_key'] = $system['dada_key'];
        $config['app_secret'] = $system['dada_secret'];
        $config['source_id'] = $set['source_id'];
        $config['url'] = 'http://newopen.imdada.cn/api/cityCode/list';
        //获取城市code
        $city = explode(',', $order['area']);
        $city = $city['1'];
        $city = substr($city, 0, strpos($city, '市'));
        $cityCode = Peisong::getCityCode($config, $city);
//发单请求数据,只是样例数据，根据自己的需求进行更改。
        $config['url'] = 'http://newopen.imdada.cn/api/order/addOrder';
        $data2 = array(
            'shop_no' => $set['shop_no'],//门店编号
            'origin_id' => $order['order_num'],//订单id
            'city_code' => $cityCode,//城市
            'tips' => 0,//小费
            'info' => $order['note'],//备注
            'cargo_price' => $order['money'],
            'is_prepay' => 0,
            'expected_fetch_time' => time() + 600,
            'receiver_name' => $order['name'],
            'receiver_address' => $order['address'],
            'receiver_phone' => $order['tel'],
            'receiver_lat' => $order['lat'],
            'receiver_lng' => $order['lng'],
            'callback' => $_W['siteroot'] . "addons/zh_cjdianc/payment/peisong/notify.php"
        );
        $result = Peisong::requestMethod($config, $data2);
        return $result;
    }


    public function kfw($order_id)
    {
        global $_W, $_GPC;
        include IA_ROOT . '/addons/zh_cjdianc/peisong/peisong.php';
        $order = pdo_get('cjdc_order', array('id' => $order_id));
        $goods = pdo_getall('cjdc_order_goods', array('order_id' => $order_id, 'uniacid' => $_W['uniacid']), array('name', 'spec'));
        $goods_info = '';
        foreach ($goods as $key => $value) {
            $goods_info .= ',' . $value['name'];
            if ($value['spec']) {
                $goods_info .= $value['name'] . "(" . $value['spec'] . ")";
            }
        }
        $goods_info = mb_substr($goods_info, 1);

        $set = pdo_get('cjdc_kfwset', array('store_id' => $order['store_id']));
        $storeInfo = pdo_get('cjdc_store', array('id' => $order['store_id']));
        $system = pdo_get('cjdc_system', array('uniacid' => $_W['uniacid']));
        $city = explode(',', $order['area']);
        $city = $city['1'];
        if (!$set['access_token']) {//绑定商户
            $res = file_get_contents("http://api.kfw.net/quanta/d/bind?user_id=" . $set['user_id'] . "&app_id=" . $system['kfw_appid']);
            $res = json_decode($res);
            $data['access_token'] = $res->data->access_token;
            $data['openid'] = $res->data->openid;
            pdo_update('cjdc_kfwset', $data, array('store_id' => $order['store_id']));
            $set = pdo_get('cjdc_kfwset', array('store_id' => $order['store_id']));
        }
        $sender_zb = explode(',', $storeInfo['coordinates']);
        $sender_zb = peisong::coordinate_switchf($sender_zb[0], $sender_zb[1]);
        $zb = peisong::coordinate_switchf($order['lat'], $order['lng']);
        //下订单
        $data = array(
            'app_id' => $system['kfw_appid'],
            'access_token' => $set['access_token'],
            'order_id' => $order['order_num'],
            'business' => '1',
            'openid' => $set['openid'],
            'goods_info' => $goods_info,
            'goods_price' => '0',
            'sender_address' => $storeInfo['address'],
            'sender_city' => $city,
            'sender_tel' => $storeInfo['tel'],
            'receiver_address' => $order['address'],
            'receiver_city' => $city,
            'receiver_tel' => $order['tel'],
            'sender_lat' => $sender_zb['Latitude'],
            'sender_lng' => $sender_zb['Longitude'],
            'receiver_lat' => $zb['Latitude'],
            'receiver_lng' => $zb['Longitude'],
            'callback_url' => $_W['siteroot'] . "addons/zh_cjdianc/payment/peisong/notify2.php",
        );
        $obj = new KfwOpenapi();
        $sign = $obj->getSign($data, $system['kfw_appsecret']);
        $data['sign'] = $sign;
        $url = "http://openapi.kfw.net/openapi/v1/order/add";
        $result = $obj->requestWithPost($url, $data);
        return $result;


    }


    public function qxkfw($order_id)
    {
        global $_W, $_GPC;
        include IA_ROOT . '/addons/zh_cjdianc/peisong/peisong.php';
        $order = pdo_get('cjdc_order', array('id' => $order_id));
        $set = pdo_get('cjdc_kfwset', array('store_id' => $order['store_id']));
        $storeInfo = pdo_get('cjdc_store', array('id' => $order['store_id']));
        $system = pdo_get('cjdc_system', array('uniacid' => $_W['uniacid']));
        //下订单
        $data = array(
            'app_id' => $system['kfw_appid'],
            'access_token' => $set['access_token'],
            'order_id' => $order['order_num'],
            'reason' => '客户取消订单',
            'ship_id' => $order['ship_id'],
        );
        $obj = new KfwOpenapi();
        $sign = $obj->getSign($data, $system['kfw_appsecret']);
        $data['sign'] = $sign;
        $url = "http://openapi.kfw.net/openapi/v1/order/cancel";
        $result = $obj->requestWithPost($url, $data);
        // return json_decode($result)->ship_id;
        //var_dump(json_decode($result));die;


    }


//改变佣金
    public function updcommission($order_id)
    {
        global $_W, $_GPC;
        $res = pdo_update('cjdc_earnings', array('state' => 2), array('order_id' => $order_id));
        if ($res) {
            echo '1';
        } else {
            echo '2';
        }

    }

    public function invalidcommission($order_id)
    {
        global $_W, $_GPC;
        $res = pdo_update('cjdc_earnings', array('state' => 3), array('order_id' => $order_id));
        // if($res){
        // 	echo '1';
        // }else{
        // 	echo '2';
        // }

    }


    public function cjpt($order_id)
    {
        global $_W, $_GPC;
        include IA_ROOT . '/addons/zh_cjdianc/peisong/cjpt.php';
        $order = pdo_get('cjdc_order', array('id' => $order_id));
        $store = pdo_get('cjdc_store', array('id' => $order['store_id']), array('name', 'address', 'tel', 'coordinates', 'logo'));
        $zb = explode(",", $store['coordinates']);
        $goods = pdo_getall('cjdc_order_goods', array('order_id' => $order_id));
        $bind = pdo_get('cjpt_bind', array('cy_uniacid' => $_W['uniacid']));
        $goods_info = '';
        foreach ($goods as $key => $value) {
            $goods_info .= '#' . $value['name'];
            if ($value['spec']) {
                $goods_info .= $value['name'] . "(" . $value['spec'] . ")";
            }
            $goods_info .= "数量:" . $value['number'] . "价格" . $value['money'];
        }
        $goods_info = mb_substr($goods_info, 1);

        //下订单
        $data = array(
            'order_id' => $order['order_num'],
            'goods_info' => $goods_info,
            'goods_price' => $order['money'],
            'sender_name' => $store['name'],
            'sender_address' => $store['address'],
            'sender_tel' => $store['tel'],
            'sender_lat' => $zb[0],
            'sender_lng' => $zb[1],
            'receiver_address' => $order['address'],
            'receiver_name' => $order['name'],
            'receiver_tel' => $order['tel'],
            'receiver_lat' => $order['lat'],
            'receiver_lng' => $order['lng'],
            'note' => $order['note'],
            'store_logo' => $store['logo'],
            'yh_money' => $order['discount'],
            'origin_id' => $order_id,
            'pay_type' => $order['pay_type'],
            'delivery_time'=>$order['delivery_time'],
            'uniacid' => $_W['uniacid'],
        );
        //var_dump($data);die;
        $url = $_W['siteroot'] . "app/index.php?i=" . $bind['pt_uniacid'] . "&c=entry&a=wxapp&do=addOrder&m=zh_cjpt";
        $result = cjpt::requestWithPost($url, $data);
        //var_dump($result);die;
        return $result;

    }

    public function qxpt($order_id)
    {
        global $_W, $_GPC;
        include IA_ROOT . '/addons/zh_cjdianc/peisong/cjpt.php';
        $order = pdo_get('cjdc_order', array('id' => $order_id));
        $bind = pdo_get('cjpt_bind', array('cy_uniacid' => $_W['uniacid']));
        $newstr = substr($news, 0, strlen($news) - 1);
        //下订单
        $data = array(
            'order_id' => $order['order_num'],
            'uniacid' => $_W['uniacid'],
        );
        $url = $_W['siteroot'] . "app/index.php?i=" . $bind['pt_uniacid'] . "&c=entry&a=wxapp&do=qxOrder&m=zh_cjpt";
        $result = cjpt::requestWithPost($url, $data);
        return $result;


    }

    public function doPageQxDada($order_id)
    {
        global $_W, $_GPC;
        include IA_ROOT . '/addons/zh_cjdianc/peisong/peisong.php';
        $order = pdo_get('cjdc_order', array('id' => $order_id));
        $set = pdo_get('cjdc_psset', array('store_id' => $order['store_id']));
        $system = pdo_get('cjdc_system', array('uniacid' => $_W['uniacid']));
//*********************配置项*************************
        $config = array();
        $config['app_key'] = $system['dada_key'];
        $config['app_secret'] = $system['dada_secret'];
        $config['source_id'] = $set['source_id'];
        $config['url'] = 'http://newopen.imdada.cn/api/order/formalCancel';
        $data = array(
            'order_id' => $order['order_num'],
            'cancel_reason_id' => 4,
            'cancel_reason' => '',
        );
        $result = Peisong::requestMethod($config, $data);
        return $result;
    }


}