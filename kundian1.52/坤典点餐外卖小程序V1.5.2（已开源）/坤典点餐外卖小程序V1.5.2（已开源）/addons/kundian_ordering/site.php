<?php
/**
 * 点餐外卖模块微站定义
 *
 * @author cqkundian
 * @url https://s.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
!defined('ROOT_KUNDIAN_ORDERING') && define('ROOT_KUNDIAN_ORDERING', IA_ROOT . '/addons/kundian_ordering/');
class Kundian_orderingModuleSite extends WeModuleSite {

    public function doWebSlide() {
        include "inc/web/slide.inc.php";
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebProduct() {
        include 'inc/web/product.inc.php';
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebGiftToken() {
        include "inc/web/giftToken.inc.php";
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebUser() {
        include "inc/web/user.inc.php";
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebSetBase() {
        include 'inc/web/config.inc.php';
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebGiftLevel() {
        include "inc/web/giftLevel.inc.php";
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebCustomer() {
        include "inc/web/customer.inc.php";
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebOrder() {
        include 'inc/web/order.inc.php';
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebProductType() {
        include 'inc/web/product_type.inc.php';
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebSetInform() {
        include 'inc/web/inform.inc.php';
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebGiftSale() {
        include 'inc/web/sale.inc.php';
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebMakeOrder() {
        include 'inc/web/make_order.inc.php';
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebAbout() {
        include 'inc/web/about.inc.php';
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebCancelPerson() {
        include 'inc/web/cancel_person.inc.php';
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebPrint() {
        include 'inc/web/print.inc.php';
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    public function doWebGoods(){
        include "inc/web/goods.inc.php";
    }
    public function doWebDesk(){
        include 'inc/web/desk.inc.php';
    }
    public function doWebQrcode(){
        include 'inc/web/qrcode.inc.php';
    }

}