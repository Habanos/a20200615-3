<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:82:"/www/wwwroot/chat.kstao.com/public/../application/varjoker/view/setting/index.html";i:1573577086;s:72:"/www/wwwroot/chat.kstao.com/application/varjoker/view/public/header.html";i:1535373624;s:72:"/www/wwwroot/chat.kstao.com/application/varjoker/view/public/footer.html";i:1545506972;}*/ ?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo \think\Config::get('config.title'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <!-- Bootstrap Core CSS -->
    <link href="/static/skin/css/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <!-- Custom CSS -->
    <link href="/static/skin/css/style.css" rel='stylesheet' type='text/css' />
    <!-- Graph CSS -->
    <link href="/static/skin/css/font-awesome.css" rel="stylesheet">
    <!-- jQuery -->
    <link href='/static/skin/css/font.css' rel='stylesheet' type='text/css'>
    <!-- lined-icons -->
    <link rel="stylesheet" href="/static/skin/css/icon-font.min.css" type='text/css' />
    <!-- //lined-icons -->
    <script src="/static/skin/js/jquery-1.10.2.min.js"></script>
    <link href="/static/skin/css/fabochart.css" rel='stylesheet' type='text/css' />
    <!--clock init-->
    <script src="/static/skin/js/css3clock.js"></script>
    <!--Easy Pie Chart-->
    <!--skycons-icons-->
    <script src="/static/skin/js/skycons.js"></script>


    <!--//skycons-icons-->
</head>
<body>
<div class="page-container">
    <!--/content-inner-->
    <div class="left-content">
        <div class="inner-content">
            <!-- header-starts -->
            <div class="header-section">
                <!--menu-right-->
                <div class="top_menu">

                    <!--/profile_details-->
                    <div class="profile_details_left">
                        <ul class="nofitications-dropdown">
                            <li class="dropdown note dra-down">
                                <div id="dd" class="wrapper-dropdown-3" tabindex="1">
                                    <span style="font-size: 10px;">站点设置</span>
                                    <ul class="dropdown">
                                        <li><a class="deutsch">清除缓存</a></li>
                                        <li><a class="deutsch" onclick="delbase()">清空数据库</a></li>
                                    </ul>
                                </div>
                                <script type="text/javascript">

                                    function DropDown(el) {
                                        this.dd = el;
                                        this.placeholder = this.dd.children('span');
                                        this.opts = this.dd.find('ul.dropdown > li');
                                        this.val = '';
                                        this.index = -1;
                                        this.initEvents();
                                    }
                                    DropDown.prototype = {
                                        initEvents : function() {
                                            var obj = this;

                                            obj.dd.on('click', function(event){
                                                $(this).toggleClass('active');
                                                return false;
                                            });

                                            obj.opts.on('click',function(){
                                                var opt = $(this);
                                                obj.val = opt.text();
                                                obj.index = opt.index();
                                                obj.placeholder.text(obj.val);
                                            });
                                        },
                                        getValue : function() {
                                            return this.val;
                                        },
                                        getIndex : function() {
                                            return this.index;
                                        }
                                    }

                                    $(function() {

                                        var dd = new DropDown( $('#dd') );

                                        $(document).click(function() {
                                            // all dropdowns
                                            $('.wrapper-dropdown-3').removeClass('active');
                                        });

                                    });

                                </script>
                            </li>

                            <div class="clearfix"></div>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                    <!--//profile_details-->
                </div>
                <!--//menu-right-->
                <div class="clearfix"></div>
            </div>
            <!-- //header-ends -->
<div class="outter-wp">
    <!--/sub-heard-part-->
    <!--/set-2-->
    <div class="set-1">
        <div class="graph-2 general">
            <div class="sub-heard-part">
                <ol class="breadcrumb m-b-0">
                    <li><a>系统</a></li>
                    <li class="active">站点设置</li>
                </ol>
            </div>
            <div class="grid-1">
                <div class="form-body">
                    <form class="form-horizontal" action="<?php echo url('setting/savesys'); ?>" method="post">
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-2 control-label">站点标题</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="title" value="<?php echo \think\Config::get('config.title'); ?>">
                            </div>
                            <div class="col-sm-2">
                                <!--<p class="help-block">Your help text!</p>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-2 control-label">站点描述</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="description" value="<?php echo \think\Config::get('config.description'); ?>">
                            </div>
                            <div class="col-sm-2">
                                <!--<p class="help-block">Your help text!</p>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-2 control-label">站点域名</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="domain" value="<?php echo \think\Config::get('config.domain'); ?>">
                            </div>
                            <div class="col-sm-2">
                                <!--<p class="help-block">Your help text!</p>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-2 control-label">客服连接</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="kfurl" value="<?php echo \think\Config::get('config.kfurl'); ?>">
                            </div>
                            <div class="col-sm-2">
                                <!--<p class="help-block">Your help text!</p>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-2 control-label">提现最低积分</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="txjifen" value="<?php echo \think\Config::get('config.txjifen'); ?>">
                            </div>
                            <div class="col-sm-2">
                                <!--<p class="help-block">Your help text!</p>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-2 control-label">提现最低金额</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="txmoney" value="<?php echo \think\Config::get('config.txmoney'); ?>">
                            </div>
                            <div class="col-sm-2">
                                <!--<p class="help-block">Your help text!</p>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-2 control-label">ICP备案号</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="icp" value="<?php echo \think\Config::get('config.icp'); ?>">
                            </div>
                            <div class="col-sm-2">
                                <!--<p class="help-block">Your help text!</p>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-2 control-label">网站版权</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="copyright" value="<?php echo \think\Config::get('config.copyright'); ?>">
                            </div>
                            <div class="col-sm-2">
                                <!--<p class="help-block">Your help text!</p>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-2 control-label">是否关闭站点</label>
                            <div class="col-sm-8">

                                    <div class="onoffswitch">
                                        <input type="checkbox" name="switch" class="onoffswitch-checkbox" id="myonoffswitch" onchange="checkField()" <?php if(\think\Config::get('config.switch') == '2'): ?>checked<?php endif; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>

                            </div>
                            <div class="col-sm-2">
                                <!--<p class="help-block">Your help text!</p>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-2 control-label">关闭站点说明</label>
                            <div class="col-sm-8">
                                <textarea name="switchcontent" data-provide="markdown" rows="15" style="width: 100%;"><?php echo \think\Config::get('config.switchcontent'); ?></textarea>
                            </div>
                            <div class="col-sm-2">
                                <!--<p class="help-block">Your help text!</p>-->
                            </div>
                        </div>
                        <button type="submit" class="btn btn-default">提交</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
 <script src="/static/skin/js/cache.js"></script>
<script>
    check = document.getElementById('myonoffswitch');
    function checkField() {
        if(check.checked){
            $('#myonoffswitch').val('2');
        }else{
            $('#myonoffswitch').val('1');
        }
    }
</script>
<!--footer section start-->

<footer>

    <p><?php echo \think\Config::get('config.copyright'); ?></p>
</footer>
<!--footer section end-->
</div>
</div>
<!--//content-inner-->
<!--/sidebar-menu-->
<div class="sidebar-menu">
    <header class="logo">
        <a href="#" class="sidebar-icon"> <span class="fa fa-bars"></span> </a> <a href="index.html"> <span id="logo"> <h1>系统管理</h1></span>
        <!--<img id="logo" src="" alt="Logo"/>-->
    </a>
    </header>
    <div style="border-top:1px solid rgba(69, 74, 84, 0.7)"></div>
    <!--/down-->
    <div class="down">

        <a href="index.html"><span class=" name-caret"><?php echo $admin['adminuser']; ?></span></a>

        <ul>
            <li><a class="tooltips" href="index.html"><span>管理添加</span><i class="lnr lnr-user"></i></a></li>
            <li><a class="tooltips" href="index.html"><span>个人设置</span><i class="lnr lnr-cog"></i></a></li>
            <li><a class="tooltips" href="<?php echo url('login/logout'); ?>"><span>退出登录</span><i class="lnr lnr-power-switch"></i></a></li>
        </ul>
    </div>
    <!--//down-->
    <div class="menu">
        <ul id="menu" >
            <li><a href="<?php echo url('index/index'); ?>"><i class="fa fa-tachometer"></i> <span>控制台</span></a></li>
          <li><a href="<?php echo url('index/source'); ?>"><i class="lnr lnr-code"></i> <span>推广设置</span><span class="fa fa-angle-right" style="float: right"></span></a></li>
            <li><a href="#"><i class="lnr lnr-apartment"></i> <span>首页设置</span><span class="fa fa-angle-right" style="float: right"></span></a>
                <ul>
                    <li><a href="<?php echo url('broadcast/index'); ?>"><i class="fa fa-inbox"></i> 首页轮播</a></li>
                    <li><a href="<?php echo url('notice/index'); ?>"><i class="fa fa-pencil-square-o"></i> 首页公告</a></li>
                    <li><a href="<?php echo url('consultation/index'); ?>"><span class="lnr lnr-highlight"></span> 首页咨询</a></li>
                    <li><a href="<?php echo url('consultation/loanlist'); ?>"><span class="lnr lnr-highlight"></span> 借款轮播</a></li>
                </ul>
            </li>
            <li id="menu-academico" ><a href="#"><i class="fa fa-table"></i> <span> 系统设置</span> <span class="fa fa-angle-right" style="float: right"></span></a>
                <ul id="menu-academico-sub" >
                    <li id="menu-academico-avaliacoes" ><a href="<?php echo url('setting/index'); ?>">站点设置</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('setting/apiindex'); ?>">接口设置</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('setting/auto'); ?>">关于我们</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('setting/xieyi'); ?>">借款协议</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('setting/regtext'); ?>">注册协议</a></li>
                    <li id="menu-academico-avaliacoes" ><a href="<?php echo url('setting/loanindex'); ?>">借款设置</a></li>
                    <li id="menu-academico-avaliacoes" ><a href="<?php echo url('setting/smsindex'); ?>">短信模板设置</a></li>
                    <li id="menu-academico-avaliacoes" ><a href="<?php echo url('setting/tuiguang'); ?>">推广页设置</a></li>
                </ul>
            </li>
            <li id="menu-academico" ><a href="#"><i class="fa fa-file-text-o"></i> <span>用户列表</span> <span class="fa fa-angle-right" style="float: right"></span></a>
                <ul id="menu-academico-sub" >
                    <li id="menu-academico-avaliacoes" ><a href="<?php echo url('user/index'); ?>">所有用户</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('user/index','perfect=2'); ?>">已实名用户</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('user/index','perfect=1'); ?>">未实名用户</a></li>
                </ul>
            </li>
            <!--<li><a href="typography.html"><i class="lnr lnr-pencil"></i> <span>Typography</span></a></li>-->
            <li id="menu-academico" ><a href="#"><i class="lnr lnr-book"></i> <span>借款列表</span> <span class="fa fa-angle-right" style="float: right"></span></a>
                <ul id="menu-academico-sub" >
                  <li id="menu-academico-avaliacoes" ><a href="<?php echo url('loan/index'); ?>">所有订单</a></li>
                    <li id="menu-academico-avaliacoes" ><a href="<?php echo url('loan/index','status=9'); ?>">未审核</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('loan/index','status=1&viptype=1'); ?>">未支付</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('loan/index','status=1&viptype=2'); ?>">已支付</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('loan/index','status=2&viptype=2'); ?>">已放款</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('loan/index','status=3&viptype=2'); ?>">已提款</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('loan/index','status=5&viptype=2'); ?>">已还款</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('loan/index','status=6&viptype=2'); ?>">已拒绝</a></li>
                </ul>
            </li>

            <li id="menu-academico" ><a href="#"><i class="lnr lnr-layers"></i> <span>订单列表</span> <span class="fa fa-angle-right" style="float: right"></span></a>
                <ul id="menu-academico-sub" >
                  <li id="menu-academico-avaliacoes" ><a href="<?php echo url('pay/vip'); ?>">VIP订单</a></li>
                    <li id="menu-academico-avaliacoes" ><a href="<?php echo url('pay/index'); ?>">充值订单</a></li>
                    <li id="menu-academico-boletim" ><a href="<?php echo url('pay/putforward'); ?>">提现订单</a></li>

                </ul>
            </li>
            <!--li><a href="<?php echo url('syslog/index'); ?>"><i class="lnr lnr-pencil"></i> <span>系统日志</span><span class="fa fa-angle-right" style="float: right"></span></a></li-->
        </ul>
    </div>
</div>
<div class="clearfix"></div>
</div>
<script>
    var toggle = true;

    $(".sidebar-icon").click(function() {
        if (toggle)
        {
            $(".page-container").addClass("sidebar-collapsed").removeClass("sidebar-collapsed-back");
            $("#menu span").css({"position":"absolute"});
        }
        else
        {
            $(".page-container").removeClass("sidebar-collapsed").addClass("sidebar-collapsed-back");
            setTimeout(function() {
                $("#menu span").css({"position":"relative"});
            }, 400);
        }

        toggle = !toggle;
    });
    function delbase() {

        if(confirm("确认清空数据库？该操作不可逆，请确认后操作")){
            //点击确定后操作
            window.location.href='<?php echo url('index/cleardatabase'); ?>';
        }
    }
</script>
<!--js -->
<!-- Bootstrap Core JavaScript -->
<script src="/static/skin/js/bootstrap.min.js"></script>
</body>
</html>