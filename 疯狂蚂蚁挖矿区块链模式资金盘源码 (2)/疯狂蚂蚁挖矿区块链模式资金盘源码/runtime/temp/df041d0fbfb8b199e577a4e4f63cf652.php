<?php /*a:1:{s:73:"D:\phpstudy\PHPTutorial\WWW\demo\application\index\view\market\index.html";i:1555433041;}*/ ?>
﻿<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <title><?php echo htmlentities(app('config')->get('hello.title')); ?></title>
    <link rel="stylesheet" type="text/css" href="/static/jycss/iconfont.css">
    <link rel="shortcut icon" href="favicon.ico"/>
    <link href="/static/jycss/mui.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="/static/images/iconfont.css">
    <link href="/static/jycss/bootstrap.min.css" rel="stylesheet"/>
    <link href="/static/jycss/base.css" rel="stylesheet"/>
    <link href="/static/jycss/style.css" rel="stylesheet"/>
    <link href="/static/jycss/public.css" rel="stylesheet"/>
    <style>
        body {
            line-height: 1 !important;
        }

        .foot-bar {
            position: absolute;
            padding: 0;
            width: 100%;
            right: 0;
            left: 0;
            bottom: 0;
            z-index: 10;
            height: 60px;
            text-align: center;

            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            table-layout: fixed;
            padding-top: 10px;
            border-top: solid 1px #272727;
            /*background-image: linear-gradient(top,#F6383A,#AD0406);
            background-image: -webkit-linear-gradient(top,#F6383A,#AD0406);*/
            background-color: #272727;
            font-family: simun;
        }

        .foot-bar .foot-menu {
            width: 18.3%;
            display: inline-block;
        }

        .foot-bar .foot-menu a {
            color: #FFF;
        }

        .foot-bar .foot-menu a:hover {
            color: #FFF;
        }

        .foot-bar .foot-menu .iconfont {
            margin: 0;
            padding: 0;
            font-size: 20px;
        }

        .foot-bar .foot-menu span {
            margin: 0;
            padding: 5px 0px 0px 0px;
            display: block;
            font-size: 14px;
        }

        @media only screen and (-webkit-min-device-pixel-ratio: 2) {
            .foot-bar:before {
                transform: scaleY(0.7);
            }
        }

        @media only screen and (-webkit-min-device-pixel-ratio: 3) {
            .foot-bar:before {
                transform: scaleY(0.5);
            }
        }
      	 .foot-menu img{
        width: 30%;
        height: 30%;
        margin: 0 auto;
    }
    .foot-menu span{
       color:#4f528c;
    }
    .foot-menu .active{
      color: #53acff;
    }
    .foot-bar{
    	background: #323663;
    }
    </style>
</head>
<body>

<header class="mui-bar mui-bar-nav mui_header mui_list_header">
    <h1 class="mui-title">交易</h1>
</header>
<!-- nav -->
<!-- nav -->

<nav class="foot-bar">
        <div class="foot-menu"><a href="/account.html">
           <img src="/static/42/images/shouye@<?php echo app('request')->path()=='account' || app('request')->path() == ''?'3' : '1'; ?>x.png" alt=""><span class="<?php echo app('request')->path()=='account' || app('request')->path() == ''?'active' : ''; ?>">首页</span></a></div>
        <div class="foot-menu"><a href="/machine.html">
            <img src="/static/42/images/kuangjishangcheng@<?php echo app('request')->path()=='machine'?'3' : '1'; ?>x.png" alt=""></i><span class="<?php echo app('request')->path()=='machine'?'active' : ''; ?>">我的蚁后</span></a></div>
        <div class="foot-menu"><a href="/team.html">
            <img src="/static/42/images/tuandui@<?php echo app('request')->path()=='team'?'3' : '1'; ?>x.png" alt=""><span class="<?php echo app('request')->path()=='team'?'active' : ''; ?>">我的团队</span></a></div>
        <div class="foot-menu"><a href="/market.html">
            <img src="/static/42/images/jiaoyi%20(1)@<?php echo app('request')->path()=='market'?'3' : '1'; ?>x.png" alt=""></i><span class="<?php echo app('request')->path()=='market'?'active' : ''; ?>">交易中心</span></a></div>
        <div class="foot-menu"><a href="/home.html">
            <img src="/static/42/images/wo@<?php echo app('request')->path()=='home'?'3' : '1'; ?>x.png" alt=""><span class="<?php echo app('request')->path()=='home'?'active' : ''; ?>">会员中心</span></a></div>
    </nav>
<!--mui-scroll-wrapper-->
<div id="pullrefresh" class="mui-content mui-scroll-wrapper">
    <div class="mui-scroll">
        <div id="segmentedControl" class="mui-segmented-control mui_title">
            <a class="mui-control-item mui-active">交易大厅</a>
            <a class="mui-control-item" href="/market/buy.html">买进</a>
            <a class="mui-control-item" href="/market/selling.html">卖出</a>
        </div>
        <div class="market-priceimg" style="padding: 15px;display:none">
          <div id="market" class="mb-3" style="height: 10rem;"></div>
        </div>
        <div id="item1" class="mui-control-content mui-active" style="overflow: auto;">
            <div class="mui_online mui_push">
                <h3>等待买入</h3>
                <div class="mui_cov_ul">
                    <div class="mui_sum sum_tit">
                        <span>数量</span>
                        <span>单价</span>
                        <span>合计金额</span>
                        <span>操作</span>
                    </div>
                    <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($i % 2 );++$i;?>
                    <div class="mui_sum">
                        <span><?php echo htmlentities(money($li['number'])); ?></span>
                        <span><?php echo htmlentities(money($li['price'])); ?>¥</span>
                        <span><?php echo htmlentities(money($li['total'])); ?>¥</span>
                        <span><a class="cd-popup-trigger1  sale" data-id="<?php echo htmlentities($li['tid']); ?>">卖出</a></span>
                    </div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <div class="mui-content-padded">
                    <ul class="mui-pager">
                        <div class="pagination">
                            <?php if($page==1): ?>
                            <li class="disabled previous page-item"><span class="page-link">上一页</span></li>
                            <?php else: ?>
                            <li class="previous page-item"><a class="page-link" href="/market.html?page=<?php echo htmlentities($page-1); ?>">上一页</a></li>
                            <?php endif; if(($page+1)<$index): ?>
                            <li class="next page-item"><a class="page-link" href="/market.html?page=<?php echo htmlentities($page+1); ?>">下一页</a></li>
                            <?php else: ?>
                            <li class="disabled next page-item"><span class="page-link">下一页</span></li>
                            <?php endif; ?>

                        </div>
                    </ul>
              	 <br><br><br><br>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="cd-popup1">
    <div class="cd-popup-container1">
        <div class="cd-cont">
            <h4>卖出</h4>
            <div class="cd-txt">
                <div class="t1">请输入交易密码，输入后点击确认（卖给TA）</div>
                <div class="t2">
                    <input type="password" class="inp" placeholder="请输入交易密码" id="password">
                </div>
              	<div class="t2">
                    <input type="text" class="inp" placeholder="请输入身份照号" id="idcard">
                </div>
                <div class="t2">
                    <input type="hidden" id="orderid">
                </div>
            </div>
            <div class="cd-bot"><a href="#" class="bot">确认</a><a href="#" class="cd-popup-close">关闭</a></div>
        </div>
    </div>
</div>
<script src="/static/jyjs/mui.min.js"></script>
<script type="text/javascript" src="/static/jyjs/jquery-2.1.1.js"></script>
<script src="/static/jyjs/layer.js"></script>
<script src="https://cdn.bootcss.com/echarts/4.0.4/echarts.min.js"></script>
<script>

    mui.init({
        pullRefresh: {
            container: '#pullrefresh',
            down: {
                callback: pulldownRefresh
            }


        }
    });

    /**
     * 下拉刷新具体业务实现
     */
    function pulldownRefresh() {
        setTimeout(function () {
            window.location.reload()
        }, 1500);
    }

    mui("#pullrefresh").on('tap', 'a', function (event) {
        this.click();
        if (this.href != '') {

            window.location.href = this.href
        }

        event.stopPropagation();//  阻止除了 a标签以外事件的点击
    });


</script>
<script type="text/javascript">

    /*弹框JS内容*/
    jQuery(document).ready(function ($) {
        //打开窗口
        $('.cd-popup-trigger1').on('click', function (event) {

            $('#orderid').val($(this).attr('data-id'));
            event.preventDefault();
            $('.cd-popup1').addClass('is-visible1');
            //$(".dialog-addquxiao").hide()
        });
        //关闭窗口
        $('.cd-popup1').on('click', function (event) {
            if ($(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup1')) {
                event.preventDefault();
                $(this).removeClass('is-visible1');
            }
        });
        //ESC关闭
        $(document).keyup(function (event) {
            if (event.which == '27') {
                $('.cd-popup1').removeClass('is-visible1');
            }
        });
        $('.bot').on('click', function (event) {
            var orderid = $('#orderid').val();
            var password = $('#password').val();
          	var idcard = $('#idcard').val();
            if (orderid == '') {
                layer.msg('请选择订单');
                return;
            }
            if (password == '') {
                layer.msg('请填写交易密码');
                return;
            }
          	if (idcard == '') {
                layer.msg('请填写身份证号');
                return;
            }
            var load = layer.load(0);
            $.ajax({
                url: "/trade/action",
                type: 'post',
                data: {
                    id : orderid,
                    command : 1,
                    safeword: password,
                  	idcard: idcard
                }, success: function (e) {
                    layer.close(load);
                    if (e.code == 200) {
                        layer.msg(e.message, {time: 1000}, function () {
                            window.location.href = "/market/selling.html"
                        })
                    } else {
                        layer.msg(e.message)
                    }
                }, error: function (e) {
                    layer.close(load);
                    layer.msg('网络不给力');
                }
            })
        })
        window.number_format = function(number, n){
          n = n ? parseInt(n) : 8;
          if (n <= 0) return Math.round(number);
          number = Math.round(number * Math.pow(10, n)) / Math.pow(10, n);
          return number;
        }
      	$.post('/market/overview', {}, function(res){
                if (res.code == 200) {
                    var data = [], price = 0, today = {};
                    for (var i = 0; i < res.data.market.length; i++) {
                      var item = res.data.market[i];
                      data.push({
                        name: item.date,
                        value: [item.date, item.price],
                      });
                      if (i == 0) {
                        price = item.price;
                        today = item;
                      }
                    }
                    chart(price, data ,res.qnum ,res.finish);
                }
            });
            var myChart = echarts.init(document.getElementById('market'));
            // 生成图表
            var chart = function(price, data , qnum , finsih){
                // 指定图表的配置项和数据
                var option = {
                    title: {
                        show: true,
                        text: '当前价格：￥' + price+ '                  求购数量：' + qnum + '   成交数量：'+ finsih,
                        textStyle: {
                            fontSize: 12,
                            color: '#323663',
                            fontWeight: 'normal'
                        }
                    },
                    grid: {
                        top: 40,
                        left: 30,
                        right: 20,
                        bottom: 20,
                        borderColor: '#323663',
                    },
                    tooltip: {
                        trigger: 'axis',
                        formatter: function (params) {
                            params = params[0];
                            var date = new Date(params.name);
                            return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' <br />最高成交价 ' + params.value[1] + '￥';
                        },
                        axisPointer: {
                            animation: false
                        }
                    },
                    xAxis: {
                        type: 'time',
                        boundaryGap: true,
                        splitLine: {
                            show: false
                        },
                        axisLine:{
                            lineStyle:{
                                color:'#323663',
                            }
                        }
                    },
                    yAxis: {
                        type: 'value',
                        boundaryGap: [0, '100%'],
                        splitLine: {
                            show: false
                        },
                        min: function(value){
                            return number_format(value.min * 0.99, 2);
                        },
                        max: function(value){
                            return number_format(value.max * 1.01, 2);
                        },
                        axisLine:{
                            lineStyle:{
                                color:'#323663',
                            }
                        }
                    },
                    series: [{
                        name: '最新成交价',
                        type: 'line',
                        showSymbol: true,
                        hoverAnimation: false,
                        data: data,
                        smooth: true,
                        symbolSize: 6,
                    }]
                };
                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option);
            }
    });
</script>
</body>

</html>