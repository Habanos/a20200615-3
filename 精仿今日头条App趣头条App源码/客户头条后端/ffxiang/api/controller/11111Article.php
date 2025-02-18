<?php
namespace app\api\controller;
use app\api\controller\Common;  // 公用   
use think\Db;       //数据库
use think\Request; //获取请求类型
use think\Validate;
class Article extends Common
{
    public function index(){
        
     //    $request = Request::instance();
     //    $city=$request->param('city');
 
     //    $city=urldecode($city);
     //   // $city=urldecode($city);
     //    // echo urlencode()("你好"); 
     //    // echo urldecode("%E4%BD%A0%E5%A5%BD");
        
     //    //$city = iconv( "UTF-8", "gb2312//IGNORE" ,$city);
     //    //file_put_contents("alipay.txt",json_encode($city),FILE_APPEND);  
     // //   $city = iconv("utf-8","gb2312",urldecode($city)); //等同于javascript decodeURI("%E7%94%B5%E5%BD%B1");

     //    dump($city);
        // exit(" 新闻资讯APP出售QQ：1013175107");
 
    }
    // 获取配置文件
    public function config(){
        $config=config();
        $data['yaoqing']=$config['yaoqing'];
		$config['appConfig']['imgurl'] = _imgUrl() ;
        $data['config']=$config['appConfig'];
        echo json_encode($data);
    }
    //
    // 文章列表
    // typeid       指定分类文章 多个分类填写 ,
    // flags        属性文章 多个属性填写 ,
    // mychannel    频道ID 多个频道填写 ,
    // pages 调用显示数量
    // field        字段
    //APP文章接口
    public function lists(){
        $request = Request::instance();
        $token=$request->param('token');
        if($token){
            $uids=_Dencrypt($token,'D');
            if(!$uids){
                $token='';
            }
        }
        $typeid=$request->param('typeid/d') ? $request->param('typeid/d') : '';
        $flags=$request->param('flags/d') ? $request->param('flags/d') : '';
        $mychannel=$request->param('mychannel');
        $pages=$request->param('page/d') ? $request->param('page/d') : 1;
        $uid=$request->param('uid/d') ? $request->param('uid/d') : '';
        $search=$request->param('search') ? $request->param('search') : '';
        $weitoutiao=$request->param('weitoutiao/d') ? $request->param('weitoutiao/d') : '';
        $date=$request->param('date/d') ? $request->param('date/d') : '';
        $city=$request->param('city') ? urldecode($request->param('city')) : '';
		$per_page = 5;
        if($city=='index.php'){
            $city='';
        }
        //2018年1月21日 12:18:29　城市搜索
        if($city){
            $typeid="";
            $where['title|tags|content|description|keywords']=['like','%'.$city.'%'];
           // file_put_contents("alipay.txt",$city."\r\n",FILE_APPEND);   
        }
 
        // exit(json_encode(input('')));
     //   file_put_contents("alipay.txt",$request->param('city')."\r\n",FILE_APPEND);    
        $where['hide']=1;
        $where['zhiding']=0;
        if($typeid && $typeid != 999999999 && $typeid !=111111111){
            // 获取下属所有ID
            if($typeid!=132){
                $typeids=_typeTid($typeid);
                $typeids=implode(',',$typeids);
                $where['typeid']=['in',$typeids]; 
            }
        }

        //if($flags){
        //    $where['flags']=['in',$flags];
        //}
		$order = 'des desc,id desc';
        if($mychannel){
            // echo $mychannel;
            $where['mychannel']=['in',$mychannel]; 
			if($mychannel == 4){
				$order = 'rand()';
				$per_page = 10;
				$vids=$request->param('vids');
				if($vids){
					$vids = explode(",",$vids);
				    $where['id'] = ['not in',$vids]; 
				}
				$where['weitoutiao'] = 0; 
			}
        }else{
            // $where['mychannel']=['<>',4];
        }
        if($typeid==132){
            $where['mychannel']=3;
        }
       
        if($typeid==111111111){
            $where['tuijian']=1;
        }
      
        if($uid){

            $where['uid']=$uid;
            // if($weitoutiao){
            //     $where['weitoutiao']=['in',1,2];
            // }else{
            //     $where['weitoutiao']=['in',0,2];
            // }
        }else{
            if($typeid != 999999999 && $typeid != 111111111){
                if($weitoutiao){
                    $where['weitoutiao']=['in',1,2];
                }else{
                    $where['weitoutiao']=['in',0,2];
                }
            }
        }


        if($search){
            $where['title|tags']=['like','%'.$search.'%'];
        }
         
        // $where['qiniu_video_type']=0;
        // $time=date('Y-m-d' , strtotime("-30 day"));
        // $where['update_time']=['>= time',$time];
        if($date){
          //  $where['create_time']=['<= time',date('Y-m-d',$date)];
        }
        // $UidGuanzhuList=_userGuanzhuList(341);
       
        // echo $UidGuanzhuList;
        // exit();
        // 导航关注
        if($token && $typeid==999999999){
            $UidGuanzhuList=_userGuanzhuList($uids); 
            if(!$UidGuanzhuList){
                exit(json_encode(['per_page'=>1,'total'=>0,'current_page'=>1,'data'=>''])); 
            }
            $UidGuanzhuList=implode(',',$UidGuanzhuList);
            $where['uid']=['in',$UidGuanzhuList];  
        }

       $data=[];
	   
	   $data['per_page']=$per_page;
       $data['data']=db('article')->where($where)->order($order)->field('id,title,description,mychannel,image,update_time,source,pingNum,images,videodate,uid,click,weitoutiao,zan,video,qiniu_video_type,content')->limit($per_page*($pages-1),$per_page)->select();
       $data['total']=Db::name('article')->where($where)->count();
       
       $data['current_page']=$pages;
       // var_dump($where);exit; 
        $config=config()['appConfig'];
        foreach ($data['data'] as $k => $v) {
			if($v['mychannel']==4){
			   Db::name('article')->where(['id'=>$v['id'],'hide'=>1])->setInc('click');
			}
            if($v['mychannel']==3 && $v['video'] || $v['mychannel']==4){
                $data['data'][$k]['video']=_imgUrl().$v['video'];
            }
           // $data['data'][$k]['image']=_imgUrl().$imageImg;
            if($v['image'] && $v['mychannel']!=3){
                if($v['mychannel']==4){
                    $data['data'][$k]['image']=_imgUrl().$v['image'];
                }else{
                    $data['data'][$k]['image']=_imgUrl().$v['image']."?imageView2/1/w/172/h/120";
                }
            }else{
                if($v['image']){
                    $data['data'][$k]['image']=_imgUrl().$v['image'];
                }else{
                    if($v['mychannel']==1){
                        
                    }else{
                        $data['data'][$k]['image']=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                    } 
                }
            }
            $data['data'][$k]['create_time']=_time($v['update_time']);
            if($search){
                $data['data'][$k]['title']=str_replace($search,'<b class="fx-search">'.$search.'</b>',$v['title']);
            }
            if($v['uid']){
                $user=_user($v['uid']);
                if($user){
                    $data['data'][$k]['user']['username']=$user['username'];
                    $data['data'][$k]['user']['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100"; 
                }
                $data['data'][$k]['uid']=$user['id'];
            }
            if($v['weitoutiao']==0){
                $kuandu="?imageView2/1/w/192/h/124";
                if($v['mychannel']==2){
                    $images='';
                    // foreach (json_decode($v['images'],true) as $ks => $vs) {
                    //     if($ks<3){
                    //         $images.='<div class="aui-col-xs-4"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vs['img'].$kuandu.'" src="../image/loadingImage.png"></div>';
                    //     }else{
                    //         continue;
                    //     }
                    // }
                    $data['data'][$k]['pcList']=json_decode($v['images'],true);
                    if($v['image']){
                        $images='<div class="aui-col-xs-12" style="height:11.5rem"><img style="height:11.5rem" id="ffxiangImgCache" ffxiang-src="'._imgUrl().$v['image'].'?imageView2/1/w/400/h/280" src="../image/loadingImage.png" /></div>';
                    }else{
                        $images=json_decode($v['images'],true);
                        $images=$images['0']['img'];
                        $images='<div class="aui-col-xs-12" style="height:11.5rem"><img style="height:11.5rem" id="ffxiangImgCache" ffxiang-src="'._imgUrl().$images.'?imageView2/1/w/400/h/280" src="../image/loadingImage.png" /></div>';
                    }
                    $data['data'][$k]['images']=$images.'<em class="aui-iconfont aui-icon-image"> '.count(json_decode($v['images'],true)).' 图</em>';
                }else if($v['mychannel']==3){
                    // $user=_user($v['uid']);
                    // if($user){
                    //     $data['data'][$k]['user']['username']=$user['username'];
                    //     $data['data'][$k]['user']['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100"; 
                    // }
                    // $data['data'][$k]['uid']=$user['id'];
                    
                }else if($v['mychannel']==1){
                    $contet=[];
					preg_match_all("/img src=\"(.*?)\"/", $v['content'],$img);
                  //  dump($img[1]);
					foreach($img[1] as $ka=>$va){
					    if(strpos($va,'http') !== false){ 
					    	$contet[]=$va;
					    }else if(strpos($va,'/public/uploads/us/') !== false){
                            $contet[]=$config['url'].$va;
                        }else{
					        $contet[]=_imgUrl().$va;
					    }
					}
                    $data['data'][$k]['imagesArticleList']=$contet;
                }
            }else{
                $data['data'][$k]['description']=_substr($v['description'],100,'<a>...[查看全文]</a>'); 
                if($v['mychannel']==3 || $v['video'] || $v['mychannel']==4){
                    if($v['qiniu_video_type']){
                        $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/200/h/200';
                    }else{
                        if($v['image']){
                            $image=_imgUrl().'/'.$v['image'].'?imageView2/1/w/300/h/200';
                        }else{
                            $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                        }
                    }
                    $data['data'][$k]['image']=$image;
                    // $images='<div class="aui-col-xs-12" tapmode onclick=""><img id="ffxiangImgCache" ffxiang-src="'.$image.'" src="../image/loadingImage.png" class="fx-video-img"></div>';
                }else{
                    $kuandu="?imageView2/1/w/200/h/200";
                    $data['data'][$k]['images']=json_decode(htmlspecialchars_decode($v['images']),true);
                    if($data['data'][$k]['images']){
                        $images='';
                        $imagesPro='';
                        foreach ($data['data'][$k]['images'] as $ks=> $vsa) {
                            if(count($data['data'][$k]['images'])==1 || count($data['data'][$k]['images'])==2){
                                $col="aui-col-xs-6";
                            }else{
                                $col="aui-col-xs-4";
                            }
                            if(is_array($vsa)){
                                $vsa=$vsa['img'];
                            } 
                            $imagesPro[]=_imgUrl().$vsa;
                            $images.='<div class="'.$col.'" tapmode onclick="img('.$v['id'].','.$ks.')"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vsa.$kuandu.'" src="../image/loadingImage.png"></div>';
                        }
                        $data['data'][$k]['images']=$images;
                        $data['data'][$k]['imagesPro']=$imagesPro;
                    } 
                }                
                if($v['uid']){
                     
                    if($token){
                        // 是否关注会员 
                        $data['data'][$k]['guanzhu']=Db::name('member_guanzhu')->where(['uid'=>$uids,'gz_uid'=>$v['uid']])->value('id');
                        // 是否赞
                        $article_zan=Db::name('article_zan')->where(['aid'=>$v['id'],'uid'=>$uids])->cache(_cache('db'))->find();
                        if($article_zan['zan']){
                            $data['data'][$k]['zanUser']=1;
                        }else{
                            $data['data'][$k]['zanUser']=0;
                        }
                    }
                }
            }
            count($data['data'][$k]['content']);
            $data['data'][$k]['url']=url('article/view',['id'=>$v['id']]);
        }
        $ad=count($data['data']);
        // echo $ad;
        if($ad>1){
			if($mychannel!=4 && $data['data'][0]['mychannel']!=4){
			$awhere['position'] = ['like','%noad%'];
			$awhere['hide'] = 1;
			if($data['data'][0]['mychannel'] == 3){
				$awhere['position'] = ['like','%video%'];
			}
			if($data['data'][0]['weitoutiao'] == 1){
				$awhere['position'] = ['like','%weitoutiao%'];
			}
			if($data['data'][0]['weitoutiao'] == 3){
				$awhere['position'] = ['like','%tieba%'];
			}
			if(!$data['data'][0]['weitoutiao'] && $data['data'][0]['mychannel'] !=3){
				$awhere['position'] = ['like','%news%'];
			}
			if($typeid==111111111){
               $awhere['position'] = ['like','%news%'];
            }
			
      
			$data['awhere'] = $awhere;
            $sdDb=Db::name('ad')->where($awhere)->order('rand()')->find();
            if($sdDb){
                $arr=['mychannel'=>99,'url'=>$sdDb['url'],'title'=>$sdDb['title'],'ad'=>$sdDb['ad'],'image'=>_imgUrl().$sdDb['image'],'source'=>$sdDb['source'],'jinbi'=>_get_jinbi($sdDb['jingbi'])];
                array_push($data['data'],$arr);
                
            }
			}else{
				//小视频广告  广告链接
				$swhere['hide'] = 1;
				$swhere['position'] = ['like','%xiaoshipin%'];
				 $sdDb=Db::name('ad')->where($swhere)->order('rand()')->find();
				 if($sdDb){
                $arr=['id'=>$sdDb['id'],'mychannel'=>99,'url'=>$sdDb['url'],'title'=>$sdDb['title'],'ad'=>$sdDb['ad'],'image'=>_imgUrl().$sdDb['image'],'video'=>_imgUrl().$sdDb['video'],'source'=>$sdDb['source'],'pingNum'=>0,'zan'=>0,'jinbi'=>_get_jinbi($sdDb['jingbi'])];
                array_push($data['data'],$arr);
                
                }
			}
        }
        // var_dump($data);exit;
    //	exit(dump($data));
        echo json_encode($data);
    }
    //pc端文章获取
    public function pcLists(){
        $request = Request::instance();
        $token=$request->param('token');
        if($token){
            $uids=_Dencrypt($token,'D');
            if(!$uids){
                $token='';
            }
        }
        $typeid=$request->param('typeid/d') ? $request->param('typeid/d') : '';
        $flags=$request->param('flags/d') ? $request->param('flags/d') : '';
        $mychannel=$request->param('mychannel');
        $pages=$request->param('page/d') ? $request->param('page/d') : 1;
        $uid=$request->param('uid/d') ? $request->param('uid/d') : '';
        $search=$request->param('search') ? $request->param('search') : '';
        $weitoutiao=$request->param('weitoutiao/d') ? $request->param('weitoutiao/d') : '';
        $date=$request->param('date/d') ? $request->param('date/d') : '';
        $city=$request->param('city') ? urldecode($request->param('city')) : '';
        if($city=='index.php'){
            $city='';
        }
        //2018年1月21日 12:18:29　城市搜索
        if($city){
            $typeid="";
            $where['title|tags|content|description|keywords']=['like','%'.$city.'%'];
           // file_put_contents("alipay.txt",$city."\r\n",FILE_APPEND);   
        }
 
        // exit(json_encode(input('')));
     //   file_put_contents("alipay.txt",$request->param('city')."\r\n",FILE_APPEND);    
        $where['hide']=1;
        // $where['zhiding']=0;
        if($typeid && $typeid != 999999999 && $typeid !=111111111){
            // 获取下属所有ID
            if($typeid!=132){
                $typeids=_typeTid($typeid);
                $typeids=implode(',',$typeids);
                $where['typeid']=['in',$typeids]; 
            }
        }

        //if($flags){
        //    $where['flags']=['in',$flags];
        //}
        if($mychannel){
            // echo $mychannel;
            $where['mychannel']=['in',$mychannel]; 
        }else{
            // $where['mychannel']=['<>',4];
        }
        if($typeid==132){
            $where['mychannel']=3;
        }
       
        if($typeid==111111111){
            $where['tuijian']=1;
        }
      
        if($uid){

            $where['uid']=$uid;
            // if($weitoutiao){
            //     $where['weitoutiao']=['in',1,2];
            // }else{
            //     $where['weitoutiao']=['in',0,2];
            // }
        }else{
            if($typeid != 999999999 && $typeid != 111111111){
                if($weitoutiao){
                    $where['weitoutiao']=['in',1,2];
                }else{
                    $where['weitoutiao']=['in',0,2];
                }
            }
        }


        if($search){
            $where['title|tags']=['like','%'.$search.'%'];
        }
         
        // $where['qiniu_video_type']=0;
        // $time=date('Y-m-d' , strtotime("-30 day"));
        // $where['update_time']=['>= time',$time];
        if($date){
          //  $where['create_time']=['<= time',date('Y-m-d',$date)];
        }
        // $UidGuanzhuList=_userGuanzhuList(341);
       
        // echo $UidGuanzhuList;
        // exit();
        // 导航关注
        if($token && $typeid==999999999){
            $UidGuanzhuList=_userGuanzhuList($uids); 
            if(!$UidGuanzhuList){
                exit(json_encode(['per_page'=>1,'total'=>0,'current_page'=>1,'data'=>''])); 
            }
            $UidGuanzhuList=implode(',',$UidGuanzhuList);
            $where['uid']=['in',$UidGuanzhuList];  
        }

        $data=[];
       $data['data']=db('article')->where($where)->order('des desc,id desc')->field('id,title,description,mychannel,image,update_time,source,pingNum,images,videodate,uid,click,weitoutiao,zan,video,qiniu_video_type,content')->limit(15*($pages-1),155)->select();
       $data['total']=Db::name('article')->where($where)->count();
       $data['per_page']=15;
       $data['current_page']=$pages;
       // var_dump($where);exit; 
        $config=config()['appConfig'];
        foreach ($data['data'] as $k => $v) {
            if($v['mychannel']==3 && $v['video'] || $v['mychannel']==4){
                $data['data'][$k]['video']=_imgUrl().$v['video'];
            }
           // $data['data'][$k]['image']=_imgUrl().$imageImg;
            if($v['image'] && $v['mychannel']!=3){
                if($v['mychannel']==4){
                    $data['data'][$k]['image']=_imgUrl().$v['image'];
                }else{
                    $data['data'][$k]['image']=_imgUrl().$v['image']."?imageView2/1/w/172/h/120";
                }
            }else{
                if($v['image']){
                    $data['data'][$k]['image']=_imgUrl().$v['image'];
                }else{
                    if($v['mychannel']==1){
                        
                    }else{
                        $data['data'][$k]['image']=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                    } 
                }
            }
            $data['data'][$k]['create_time']=_time($v['update_time']);
            if($search){
                $data['data'][$k]['title']=str_replace($search,'<b class="fx-search">'.$search.'</b>',$v['title']);
            }
            if($v['uid']){
                $user=_user($v['uid']);
                if($user){
                    $data['data'][$k]['user']['username']=$user['username'];
                    $data['data'][$k]['user']['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100"; 
                }
                $data['data'][$k]['uid']=$user['id'];
            }
            if($v['weitoutiao']==0){
                $kuandu="?imageView2/1/w/192/h/124";
                if($v['mychannel']==2){
                    $images='';
                    // foreach (json_decode($v['images'],true) as $ks => $vs) {
                    //     if($ks<3){
                    //         $images.='<div class="aui-col-xs-4"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vs['img'].$kuandu.'" src="../image/loadingImage.png"></div>';
                    //     }else{
                    //         continue;
                    //     }
                    // }
                    $data['data'][$k]['pcList']=json_decode($v['images'],true);
                    if($v['image']){
                        $images='<div class="aui-col-xs-12" style="height:11.5rem"><img style="height:11.5rem" id="ffxiangImgCache" ffxiang-src="'._imgUrl().$v['image'].'?imageView2/1/w/400/h/280" src="../image/loadingImage.png" /></div>';
                    }else{
                        $images=json_decode($v['images'],true);
                        $images=$images['0']['img'];
                        $images='<div class="aui-col-xs-12" style="height:11.5rem"><img style="height:11.5rem" id="ffxiangImgCache" ffxiang-src="'._imgUrl().$images.'?imageView2/1/w/400/h/280" src="../image/loadingImage.png" /></div>';
                    }
                    $data['data'][$k]['images']=$images.'<em class="aui-iconfont aui-icon-image"> '.count(json_decode($v['images'],true)).' 图</em>';
                }else if($v['mychannel']==3){
                    // $user=_user($v['uid']);
                    // if($user){
                    //     $data['data'][$k]['user']['username']=$user['username'];
                    //     $data['data'][$k]['user']['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100"; 
                    // }
                    // $data['data'][$k]['uid']=$user['id'];
                    
                }else if($v['mychannel']==1){
                    $contet=[];
                    preg_match_all("/img src=\"(.*?)\"/", $v['content'],$img);
                  //  dump($img[1]);
                    foreach($img[1] as $ka=>$va){
                        if(strpos($va,'http') !== false){ 
                            $contet[]=$va;
                        }else if(strpos($va,'/public/uploads/us/') !== false){
                            $contet[]=$config['url'].$va;
                        }else{
                            $contet[]=_imgUrl().$va;
                        }
                    }
                    $data['data'][$k]['imagesArticleList']=$contet;
                }
            }else{
                $data['data'][$k]['description']=_substr($v['description'],100,'<a>...[查看全文]</a>'); 
                if($v['mychannel']==3 || $v['video'] || $v['mychannel']==4){
                    if($v['qiniu_video_type']){
                        $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/200/h/200';
                    }else{
                        if($v['image']){
                            $image=_imgUrl().'/'.$v['image'].'?imageView2/1/w/300/h/200';
                        }else{
                            $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                        }
                    }
                    $data['data'][$k]['image']=$image;
                    // $images='<div class="aui-col-xs-12" tapmode onclick=""><img id="ffxiangImgCache" ffxiang-src="'.$image.'" src="../image/loadingImage.png" class="fx-video-img"></div>';
                }else{
                    $kuandu="?imageView2/1/w/200/h/200";
                    $data['data'][$k]['images']=json_decode(htmlspecialchars_decode($v['images']),true);
                    if($data['data'][$k]['images']){
                        $images='';
                        $imagesPro='';
                        foreach ($data['data'][$k]['images'] as $ks=> $vsa) {
                            if(count($data['data'][$k]['images'])==1 || count($data['data'][$k]['images'])==2){
                                $col="aui-col-xs-6";
                            }else{
                                $col="aui-col-xs-4";
                            }
                            if(is_array($vsa)){
                                $vsa=$vsa['img'];
                            } 
                            $imagesPro[]=_imgUrl().$vsa;
                            $images.='<div class="'.$col.'" tapmode onclick="img('.$v['id'].','.$ks.')"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vsa.$kuandu.'" src="../image/loadingImage.png"></div>';
                        }
                        $data['data'][$k]['images']=$images;
                        $data['data'][$k]['imagesPro']=$imagesPro;
                    } 
                }                
                if($v['uid']){
                     
                    if($token){
                        // 是否关注会员 
                        $data['data'][$k]['guanzhu']=Db::name('member_guanzhu')->where(['uid'=>$uids,'gz_uid'=>$v['uid']])->value('id');
                        // 是否赞
                        $article_zan=Db::name('article_zan')->where(['aid'=>$v['id'],'uid'=>$uids])->cache(_cache('db'))->find();
                        if($article_zan['zan']){
                            $data['data'][$k]['zanUser']=1;
                        }else{
                            $data['data'][$k]['zanUser']=0;
                        }
                    }
                }
            }
            count($data['data'][$k]['content']);
            $data['data'][$k]['url']=url('article/view',['id'=>$v['id']]);
        }
        $ad=count($data['data']);
        // echo $ad;
        if($ad>1 && !$weitoutiao && $mychannel!=4){
            $sdDb=Db::name('ad')->where(['hide'=>1])->order('rand()')->find();
            if($sdDb){
                $arr=['mychannel'=>99,'jinbi'=>_get_jinbi($sdDb['jingbi']),'url'=>$sdDb['url'],'title'=>$sdDb['title'],'ad'=>$sdDb['ad'],'image'=>_imgUrl().$sdDb['image'],'source'=>$sdDb['source'],];
                array_push($data['data'],$arr);
                
            }
        }
        // var_dump($data);exit;
    //  exit(dump($data));
        echo json_encode($data);
    }
    public function slists(){
        $request = Request::instance();
        $token=$request->param('token');
        if($token){
            $uids=_Dencrypt($token,'D');
            if(!$uids){
                $token='';
            }
        }
        $typeid=$request->param('typeid/d') ? $request->param('typeid/d') : '';
        $flags=$request->param('flags/d') ? $request->param('flags/d') : '';
        $mychannel=$request->param('mychannel/d') ? $request->param('mychannel/d') : '';
        $pages=$request->param('pages/d') ? $request->param('pages/d') : '';
        $uid=$request->param('uid/d') ? $request->param('uid/d') : '';
        $search=$request->param('search') ? $request->param('search') : '';
        $weitoutiao=$request->param('weitoutiao/d') ? $request->param('weitoutiao/d') : '';
        $date=$request->param('date/d') ? $request->param('date/d') : '';
        $city=$request->param('city') ? urldecode($request->param('city')) : '';
        if($city=='index.php'){
            $city='';
        }
        //2018年1月21日 12:18:29　城市搜索
        if($city){
            $typeid="";
            $where['title|tags|content|description|keywords']=['like','%'.$city.'%'];
           // file_put_contents("alipay.txt",$city."\r\n",FILE_APPEND);   
        }
 

     //   file_put_contents("alipay.txt",$request->param('city')."\r\n",FILE_APPEND);    
        $where['hide']=1;
        if($typeid && $typeid != 999999999 && $typeid !=111111111){
            // 获取下属所有ID
            if($typeid!=132){
                $typeids=_typeTid($typeid);
                $typeids=implode(',',$typeids);
                $where['typeid']=['in',$typeids]; 
            }
        }

        //if($flags){
        //    $where['flags']=['in',$flags];
        //}
        if($mychannel){
            $where['mychannel']=['in',$mychannel]; 
        }else{
            $where['mychannel']=['<>',4];
        }
        if($typeid==132){
            $where['mychannel']=3;
        }
       
        if($typeid==111111111){
            $where['tuijian']=1;
        }
      
        if($uid){

            $where['uid']=$uid;
            // if($weitoutiao){
            //     $where['weitoutiao']=['in',1,2];
            // }else{
            //     $where['weitoutiao']=['in',0,2];
            // }
        }else{
            if($typeid != 999999999 && $typeid != 111111111){
                if($weitoutiao){
                    $where['weitoutiao']=['in',1,2];
                }else{
                    $where['weitoutiao']=['in',0,2];
                }
            }
        }


        if($search){
            $where['title|tags']=['like','%'.$search.'%'];
        }
         
        $where['qiniu_video_type']=0;
        // $time=date('Y-m-d' , strtotime("-30 day"));
        // $where['update_time']=['>= time',$time];
        if($date){
          //  $where['create_time']=['<= time',date('Y-m-d',$date)];
        }
        // $UidGuanzhuList=_userGuanzhuList(341);
       
        // echo $UidGuanzhuList;
        // exit();
        // 导航关注
        if($token && $typeid==999999999){
            $UidGuanzhuList=_userGuanzhuList($uids); 
            if(!$UidGuanzhuList){
                exit(json_encode(['per_page'=>1,'total'=>0,'current_page'=>1,'data'=>''])); 
            }
            $UidGuanzhuList=implode(',',$UidGuanzhuList);
            $where['uid']=['in',$UidGuanzhuList];  
        }
        // var_dump($where);
        $data=Db::name('article')->where($where)->fetchSql(false)->order('des desc,id desc')->field('id,title,description,mychannel,image,update_time,source,pingNum,images,videodate,uid,click,weitoutiao,zan,video,qiniu_video_type,content')->paginate(0,1000)->toarray();
       
       // var_dump($data);
        $config=config()['appConfig'];
       
      
        foreach ($data['data'] as $k => $v) {
            if($v['mychannel']==3 && $v['video'] || $v['mychannel']==4){
                $data['data'][$k]['video']=_imgUrl().$v['video'];
            }
           // $data['data'][$k]['image']=_imgUrl().$imageImg;
            if($v['image'] && $v['mychannel']!=3){
                if($v['mychannel']==4){
                    $data['data'][$k]['image']=_imgUrl().$v['image'];
                }else{
                    $data['data'][$k]['image']=_imgUrl().$v['image']."?imageView2/1/w/172/h/120";
                }
            }else{
                if($v['image']){
                    $data['data'][$k]['image']=_imgUrl().$v['image'];
                }else{
                    if($v['mychannel']==1){
                        
                    }else{
                        $data['data'][$k]['image']=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                    } 
                }
            }
            $data['data'][$k]['create_time']=_time($v['update_time']);
            if($search){
                $data['data'][$k]['title']=str_replace($search,'<b class="fx-search">'.$search.'</b>',$v['title']);
            }
            if($v['uid']){
                $user=_user($v['uid']);
                if($user){
                    $data['data'][$k]['user']['username']=$user['username'];
                    $data['data'][$k]['user']['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100"; 
                }
                $data['data'][$k]['uid']=$user['id'];
            }
            if($v['weitoutiao']==0){
                $kuandu="?imageView2/1/w/192/h/124";
                if($v['mychannel']==2){
                    $images='';

                    // foreach (json_decode($v['images'],true) as $ks => $vs) {
                    //     if($ks<3){
                    //         $images.='<div class="aui-col-xs-4"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vs['img'].$kuandu.'" src="../image/loadingImage.png"></div>';
                    //     }else{
                    //         continue;
                    //     }
                    // }
                    $data['data'][$k]['pcList']=json_decode($v['images'],true);
                    if($v['image']){
                        $images='<div class="aui-col-xs-12" style="height:11.5rem"><img style="height:11.5rem" id="ffxiangImgCache" ffxiang-src="'._imgUrl().$v['image'].'?imageView2/1/w/400/h/280" src="../image/loadingImage.png" /></div>';
                    }else{
                        $images=json_decode($v['images'],true);
                        $images=$images['0']['img'];
                        $images='<div class="aui-col-xs-12" style="height:11.5rem"><img style="height:11.5rem" id="ffxiangImgCache" ffxiang-src="'._imgUrl().$images.'?imageView2/1/w/400/h/280" src="../image/loadingImage.png" /></div>';
                    }
                    $data['data'][$k]['images']=$images.'<em class="aui-iconfont aui-icon-image"> '.count(json_decode($v['images'],true)).' 图</em>';
                }else if($v['mychannel']==3){
                    // $user=_user($v['uid']);
                    // if($user){
                    //     $data['data'][$k]['user']['username']=$user['username'];
                    //     $data['data'][$k]['user']['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100"; 
                    // }
                    // $data['data'][$k]['uid']=$user['id'];
                    
                }else if($v['mychannel']==1){
                    $contet=[];
                    preg_match_all("/img src=\"(.*?)\"/", $v['content'],$img);
                  //  dump($img[1]);
                    foreach($img[1] as $ka=>$va){
                        if(strpos($va,'http') !== false){ 
                            $contet[]=$va;
                        }else if(strpos($va,'/public/uploads/us/') !== false){
                            $contet[]=$config['url'].$va;
                        }else{
                            $contet[]=_imgUrl().$va;
                        }
                    }
                    $data['data'][$k]['imagesArticleList']=$contet;
                }
            }else{
                $data['data'][$k]['description']=_substr($v['description'],100,'<a>...[查看全文]</a>'); 
                if($v['mychannel']==3 || $v['video'] || $v['mychannel']==4){
                    if($v['qiniu_video_type']){
                        $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/200/h/200';
                    }else{
                        if($v['image']){
                            $image=_imgUrl().'/'.$v['image'].'?imageView2/1/w/300/h/200';
                        }else{
                            $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                        }
                    }
                    $data['data'][$k]['image']=$image;
                    // $images='<div class="aui-col-xs-12" tapmode onclick=""><img id="ffxiangImgCache" ffxiang-src="'.$image.'" src="../image/loadingImage.png" class="fx-video-img"></div>';
                }else{
                    $kuandu="?imageView2/1/w/200/h/200";
                    $data['data'][$k]['images']=json_decode(htmlspecialchars_decode($v['images']),true);
                    if($data['data'][$k]['images']){
                        $images='';
                        $imagesPro='';
                        foreach ($data['data'][$k]['images'] as $ks=> $vsa) {
                            if(count($data['data'][$k]['images'])==1 || count($data['data'][$k]['images'])==2){
                                $col="aui-col-xs-6";
                            }else{
                                $col="aui-col-xs-4";
                            }
                            if(is_array($vsa)){
                                $vsa=$vsa['img'];
                            } 
                            $imagesPro[]=_imgUrl().$vsa;
                            $images.='<div class="'.$col.'" tapmode onclick="img('.$v['id'].','.$ks.')"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vsa.$kuandu.'" src="../image/loadingImage.png"></div>';
                        }
                        $data['data'][$k]['images']=$images;
                        $data['data'][$k]['imagesPro']=$imagesPro;
                    } 
                }                
                if($v['uid']){
                     
                    if($token){
                        // 是否关注会员 
                        $data['data'][$k]['guanzhu']=Db::name('member_guanzhu')->where(['uid'=>$uids,'gz_uid'=>$v['uid']])->value('id');
                        // 是否赞
                        $article_zan=Db::name('article_zan')->where(['aid'=>$v['id'],'uid'=>$uids])->cache(_cache('db'))->find();
                        if($article_zan['zan']){
                            $data['data'][$k]['zanUser']=1;
                        }else{
                            $data['data'][$k]['zanUser']=0;
                        }
                    }
                }
            }
            count($data['data'][$k]['content']);
            $data['data'][$k]['url']=url('article/view',['id'=>$v['id']]);
        }
        $ad=count($data['data']);
        if($ad>1 && !$weitoutiao && $mychannel!=4&& $typeid != 999999999){
            $sdDb=Db::name('ad')->where(['hide'=>1])->order('rand()')->find();
            if($sdDb){
                $data['data'][$ad]['mychannel']=99;
                $data['data'][$ad]['url']=$sdDb['url'];
                $data['data'][$ad]['title']=$sdDb['title'];
                $data['data'][$ad]['ad']=$sdDb['ad'];
                $data['data'][$ad]['image']=_imgUrl().$sdDb['image'];
                $data['data'][$ad]['source']=$sdDb['source'];
				$data['data'][$ad]['jinbi']=_get_jinbi($sdDb['jingbi']);
            }
        } 
    //  exit(dump($data));
        echo json_encode($data);
    }

    // 分类栏目
    // id           指定分类 多个分类填写 ,
    // limit        调用显示数量
    // mychannel    频道ID
    // tid          调用指定下属分类 指定分类 多个分类填写 ,
    // field        字段

    public function typeid(){
        $request = Request::instance();
        $id=$request->param('id') ? $request->param('id') : '';
        $limit=$request->param('limit') ? $request->param('limit') : '';
        $mychannel=$request->param('mychannel') ? $request->param('mychannel') : '';
        $tid=$request->param('tid') ? $request->param('tid') : '';
        $field=$request->param('field') ? $request->param('field') : '';
        $where['hide']=1;
        if($id){
            $where['id']=['in',$id];
        }
        if($mychannel){
            $where[$mychannel]=1;
        }
        if($tid){
            $where['tid']=['in',$tid];
        }
        if($mychannel==3){
            $order='hide desc,des desc';
        }else{
            $order='hide desc,des desc';
        }
        if($mychannel!=3){
            $where['tid']=0;
        }
        $data=Db::name('typeid')->where($where)->order($order)->limit($limit)->cache(_cache('db'))->field($field)->select();
        $typeid=[];
        if(!$mychannel){
            $datas['0']=['id'=>999999999,'title'=>'关注'];
            $datas['1']=['id'=>111111111,'title'=>'推荐'];
            $datas['2']=['id'=>888888888,'title'=>'本地'];
            $data=array_merge($datas,$data);
        }
        foreach ($data as $k => $v) {
            if($field){
               if(strstr($field,"create_time")){
                    $data[$k]['create_time']=date('Y-m-d H:i:s',$v['create_time']);
                }
                if(strstr($field,"id")){
                    $typeid[$k]=$v['id'];
                }
            }else{
                $data[$k]['create_time']=date('m-d H:i',$v['create_time']);
                $typeid[$k]=$v['id'];
            }
            if($mychannel==3){
                $data[$k]['bg']='#fff';
                $data[$k]['bgSelected']='#fff';
            }else{
                $data[$k]['bg']='#fff';
                $data[$k]['bgSelected']='#fff';
                $data[$k]['size']['w']=$this->typeSize($v['title']);
            }
        }
        $data=['data'=> $data,'typeid'=>$typeid];
        //exit(dump($data));
        echo json_encode($data); 
    }
    public function typeSize($title=''){
        if(!$title){
            return 50;
        }
        $size=ceil(strlen($title)*8.3333333);
        return $size;
    }
    // 视频分类
    public function videoTypeid(){
        $where['hide']=1;
        $where['tid']=132; 
        $data=Db::name('typeid')->where($where)->order('hide desc,des desc')->cache(_cache('db'))->field('title,id')->select();
        $typeid=[];
        $datas['0']=['id'=>0,'title'=>'全部'];
        $data=array_merge($datas,$data);
        foreach ($data as $k => $v) {
            $data[$k]['bg']='#fff';
            $data[$k]['bgSelected']='#fff';
            $typeid[$k]=$v['id'];
        }
        $data=['data'=> $data,'typeid'=>$typeid];
        //dump($data);exit();
        echo json_encode($data); 
    }

    // 详细内容 
    public function view(){
        $request = Request::instance();
        $token=input('param.token');
        if($token){
            $uid=_Dencrypt($token,'D');
            if(!$uid){
                $token='';
            }
        }
        $id=$request->param('id/d');
        if(!$id){
            exit(json_encode(['err'=>'不存在或已删除']));
        }
        $db=Db::name('article')->where(['id'=>$id])->find();
        if(!$db){
            exit(json_encode(['err'=>'内容不存在']));
        }
        if($db['hide']==0){
            exit(json_encode(['err'=>'内容不存在']));
        }
        if($db['hide']==2){
            exit(json_encode(['err'=>'文章正在审核，无法查看']));
        }
        if($db['hide']==3){
            exit(json_encode(['err'=>'文章未通过，无法查看']));
        }
        $db['image']=_imgUrl().$db['image'];
        // 增加浏览次数
        Db::name('article')->where(['id'=>$id,'hide'=>1])->setInc('click');
        $config=config()['appConfig'];
        // exit($db['content']);
        if($db['mychannel']==1){   
            // 内容
            // $db=str_replace('/public',_imgUrl().'/public',$db);
            // $db=str_replace('src','src="../image/loadingImage.png" id="ffxiangImgCache" ffxiang-src',$db);
            preg_match_all("/img(.*?)src=\"(.*?)\"/", $db['content'],$content);
            $images_list=[];
            foreach ($content[2] as $k => $v) {
                if(strstr($v,'http')){
                    $db['content']=str_replace($v,'../image/loadingImage.png" Tapmode onclick="img('.$k.');" ffxiang-src="'.$v.'" id="ffxiangImgCache',$db['content']);
                    $images_list[]=$v;
                }else if(strpos($v,'/public/uploads/us/') !== false){
                    $db['content']=str_replace($v,'../image/loadingImage.png" Tapmode onclick="img('.$k.');" ffxiang-src="'.$config['url'].$v.'" id="ffxiangImgCache',$db['content']);
                    $images_list[]=$config['url'].$v;
                }else{
                    $db['content']=str_replace($v,'../image/loadingImage.png" Tapmode onclick="img('.$k.');" ffxiang-src="'._imgUrl().$v.'" id="ffxiangImgCache',$db['content']);
                    $images_list[]=_imgUrl().$v;
                }   
            }
            $db['images_list']=$images_list;
            // $images_list=str_replace('src="','',$images_list);
            // $images_list=str_replace('"','',$images_list);
            
        }else if($db['mychannel']==2){
            if($db['weitoutiao']){
                $images=json_decode(htmlspecialchars_decode($db['images']),true);
                $image=''; 
                $images_list=[];
                $kuandu="?imageView2/1/w/800/h/800";
				$kuandu='';
                foreach ($images as $k => $v) {
                    if(count($images)==1 || count($images)==2){
                        $col="aui-col-xs-6";
                    }else{
                        $col="aui-col-xs-4";
                    }
                    if(is_array($v)){
                        $v=$v['img'];
                    } 
                    $image.='<div class="'.$col.'" tapmode onclick="img('.$k.')"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$v.$kuandu.'" src="../image/loadingImage.png"></div>';
                    $images_list[]=_imgUrl().$v;
                }
                $content=$db['description'];
                if($content){
                    $content=$content."<br>";
                }
                $db['content']="<div class='fx-view-images aui-row aui-row-padded'>".$content.$image."</div>";
            }else{
                $images=json_decode($db['images'],true);
                $image=''; 
                $images_list=[];
                $images_text=[];
                foreach ($images as $k => $v) {
                    // $image.='<p><img Tapmode onclick="img('.$k.');" ffxiang-src="'._imgUrl().$v['img'].'" src="../image/loadingImage.png" id="ffxiangImgCache" /></p>';
                    // $image.='<p>'.$v['img_text'].'</p>';
                    $images_text[]=$v['img_text'];
                    $images_list[]=_imgUrl().$v['img'];
                } 
                //$db['content']="<div class='fx-view-images'>".$image."</div>"; 
                $db['images_text']=$images_text;
            }
            $db['images_list']=$images_list;
             
        }else if($db['mychannel']==3){
            $db['video']=_imgUrl().$db['video'];
        }
        if($db['tags']){
            $tags=$db['tags'];
            $tags=preg_replace("/\s/","",$tags);
            $tags=explode(',',$tags);
            $tag='';
            foreach ($tags as $k => $v) {
               // $v=str_replace(' ', '', $v);
                 
                $tag.='<div class="aui-btn" tapmode  onclick="_url({text:\''.$v.'\'},\'search_win\')">'.$v.'</div>';
            }
            $db['tags']=$tag;
        }

        if($db['uid']){
            $user=_user($db['uid']);
            $db['userUid']=$user['id'];
            $db['userUsername']=$user['username'];
            $db['userImg']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100";
            // 查找是否关注
            if($token){
                $guanzhu=Db::name('member_guanzhu')->where(['uid'=>$uid,'gz_uid'=>$db['uid']])->value('id');
                if($guanzhu){
                    $db['userGuanzhu']=1;
                }     
            }
        }
        if($token){
            // 会员是否赞和踩
            $article_zan=Db::name('article_zan')->where(['aid'=>$id,'uid'=>$uid])->cache(_cache('db'))->find();
            if($article_zan['zan']){
                $db['userZan']=1;
            }else if($article_zan['cai']){
                $db['userCai']=1;
            }
            // 会员是否收藏
            $shoucang=DB::name('member_favorite')->where(['aid'=>$id,'uid'=>$uid,'type'=>0])->value('id');
            $db['shoucang']=$shoucang;
            // 记录阅读历史
            $lishi=Db::name('member_favorite')->where(['aid'=>$id,'uid'=>$uid,'type'=>1])->value('id');
            if(!$lishi){
                db::name('member_favorite')->insert(['aid'=>$id,'uid'=>$uid,'time'=>time(),'type'=>1]);
            }
        }
        $db['update_time']=_time($db['update_time']);
        if($db['weitoutiao'] && $db['mychannel']==1){
            $db['content']="<div class='fx-view-images'>".$db['description']."</div>";
        }
        // 插入广告
		$awhere['hide'] = 1;
		
		if($db['mychannel'] == 3){
			$awhere['position'] = ['like','%v_detial%'];
		}
		if($db['weitoutiao'] == 1){
			$awhere['position'] = ['like','%w_detial%'];
		}
		if($db['weitoutiao'] == 3){
			$awhere['position'] = ['like','%t_detial%'];
		}
		if(!$db['weitoutiao'] && $db['mychannel'] !=3){
			$awhere['position'] = ['like','%n_detial%'];
		}
		
		
		
        $sdDb=Db::name('ad')->where($awhere)->order('rand()')->find();
        if($sdDb){
            $data['url']=$sdDb['url'];
            $data['title']=$sdDb['title'];
            $data['ad']=$sdDb['ad'];
            $data['image']=_imgUrl().$sdDb['image'];
            $data['source']=$sdDb['source'];
			$data['jinbi']=_get_jinbi($sdDb['jingbi']);
            $db['ad']=$data;
        }
     //   exit(dump($db));
        echo json_encode($db);
    }
    // 文章评论列表
    public function pingList(){
        $pages=input('post.pages/d') ? input('post.pages/d') : '';
        $aid=input('param.aid/d') ? input('param.aid/d') : ''; 
		$order=input('param.order') ? input('param.order') : 'time desc'; 
        $token=input('param.token');
        if($token){
            $uid=_Dencrypt($token,'D');
            if(!$uid){
                $token='';
            }
        }
        //开始验证
        $validate = new Validate([
            'pages'         =>      'number',
            'aid'           =>      'number|require',
        ]);
        $validata=[
            'pages'         =>      $pages,
            'aid'           =>      $aid,
        ];
        if (!$validate->check($validata)) {
            exit(json_encode(['err'=>$validate->getError()]));;
        }
        //结束验证
        $where['aid']=$aid;
        $data=Db::name('article_ping')->where($where)->order($order)->fetchSql(false)->paginate($pages)->toarray();
        foreach ($data['data'] as $k => $v) {
            $user=_user($v['uid']);
            $data['data'][$k]['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100";
            $data['data'][$k]['username']=$user['username'];
            $data['data'][$k]['time']=date('m-d H:i',$v['time']);
            $pingNum=Db::name('article_ping_xia')->where(['ping_id'=>$v['id']])->count();
            $data['data'][$k]['huifu']=$pingNum;
            if($pingNum){
				
                $pingData=Db::name('article_ping_xia')->where(['ping_id'=>$v['id']])->order($order)->limit(4)->select();
                $pingDataHtml='';
                foreach ($pingData as $ks => $vs) {
                    $pingDataHtml.='<li class="aui-ellipsis-2">';
                    $user=_user($vs['uid']);
                    $pingDataHtml.='<a Tapmode onclick="_url({id:'.$vs['uid'].'},\'u_win\')">'.$user['username'].' ：</a>'.$vs['content'].'</li>';
                }
                if($pingNum > 5){
                    $pingDataHtml.='<li class="aui-ellipsis-2"><a>查看全部'.$pingNum.'条回复</a></li>';
                }
                $data['data'][$k]['pingDataList']=$pingDataHtml;
            }
            $data['data'][$k]['aid']=$v['aid'];
            if($token){
                $zanUser=Db::name('article_ping_zan')->where(['pingId'=>$v['id'],'uid'=>$uid])->cache(_cache('db'))->value('id');
                $data['data'][$k]['zanUser']=$zanUser;
            }
            
        }
        exit(json_encode($data));
    }
    // 被评论列表
    public function pingList_xia(){
        $pages=input('post.pages/d') ? input('post.pages/d') : '';
        $pingId=input('param.pingId/d') ? input('param.pingId/d') : ''; 
        $token=input('param.token');
        if($token){
            $uid=_Dencrypt($token,'D');
            if(!$uid){
                $token='';
            }
        }
        //开始验证
        $validate = new Validate([
            'pages'         =>      'number',
            'pingId'        =>      'number|require',
        ]);
        $validata=[
            'pages'         =>      $pages,
            'pingId'        =>      $pingId,
        ];
        if (!$validate->check($validata)) {
            exit(json_encode(['err'=>$validate->getError()]));;
        }
        //结束验证
        $where['ping_id']=$pingId;
        $data=Db::name('article_ping_xia')->where($where)->order('time desc')->fetchSql(false)->paginate($pages)->toarray();
        foreach ($data['data'] as $k => $v) {
            $user=_user($v['uid']);
            $data['data'][$k]['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100";
            $data['data'][$k]['username']=$user['username'];
            $data['data'][$k]['time']=date('m-d H:i',$v['time']);

            // if($pingNum){
            //     $pingData=Db::name('article_ping_xia')->where(['ping_id'=>$v['id']])->order('time desc')->limit(4)->select();
            //     $pingDataHtml='';
            //     foreach ($pingData as $ks => $vs) {
            //         $pingDataHtml.='<li class="aui-ellipsis-2">';
            //         $user=_user($vs['uid']);
            //         $pingDataHtml.='<a Tapmode onclick="_url({id:'.$vs['uid'].'},\'u_win\')">'.$user['username'].' ：</a>'.$vs['content'].'</li>';
            //     }
            //     if($pingNum > 5){
            //         $pingDataHtml.='<li class="aui-ellipsis-2"><a>查看全部'.$pingNum.'条回复</a></li>';
            //     }
            //     $data['data'][$k]['pingDataList']=$pingDataHtml;
            // }
            // $data['data'][$k]['aid']=$v['aid'];
            if($token){
                // 评论是否赞
                $zanUser=Db::name('article_ping_xia_zan')->where(['pingId'=>$v['id'],'uid'=>$uid])->cache(_cache('db'))->value('id');
                $data['data'][$k]['zanUser']=$zanUser;
            }
        }
        exit(json_encode($data));
    }
    // 无限调用评论回复
    private function pingListPingId2($pingId){
        $db=db::name('article_ping')->where(['id'=>$pingId])->field('uid,content,pingId2')->find();
        if($db){
            $user=_user($db['uid']);
            $db['username']=$user['username'];
            $data="//<a>@".$user['username'].": </a>".$db['content'];
            $this->pingListPingId2($db['pingId2']); 
            return $data;
        }
    }
    // 调用单个评论内容
    public function pingView(){
        $pingId=input('param.pingId/d') ? input('param.pingId/d') : ''; 
        $token=input('param.token');
        if($token){
            $uid=_Dencrypt($token,'D');
            if(!$uid){
                $token='';
            }
        }
        //开始验证
        $validate = new Validate([
            'pingId'        =>      'number|require',
        ]);
        $validata=[
            'pingId'        =>      $pingId,
        ];
        if (!$validate->check($validata)) {
            exit(json_encode(['err'=>$validate->getError()]));;
        }
        //结束验证
        $db=Db::name('article_ping')->where(['id'=>$pingId])->order('time desc')->find();
        $user=_user($db['uid']);
        $db['uid']=$user['id'];
        $db['username']=$user['username'];
        $db['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100";
        $db['time']=date('m-d H:i',$db['time']);
        if($token){
            // 会员是否赞和踩
            $ping_zan=Db::name('article_ping_zan')->where(['pingId'=>$pingId,'uid'=>$uid])->cache(_cache('db'))->find();
            if($ping_zan['zan']){
                $db['user']['zan']=1;
            }
            // 是否关注
            $gz_uid=$db['uid'];
            if($uid != $gz_uid){
                $guanzhu=Db::name('member_guanzhu')->where(['uid'=>$uid,'gz_uid'=>$gz_uid])->value('id');
                if($guanzhu){
                    $db['user']['guanzhu']=1;
                } else{
                    $db['user']['guanzhu']=0;
                }   
            }else{
                $db['user']['guanzhu']=5;
            }
        }
        // 获取4个赞会员
        if($db['zan']){
            $zanList=Db::name('article_ping_zan')->where(['pingId'=>$db['id']])->order('time desc')->field('uid')->limit(4)->select();
            $imgList='';
            foreach ($zanList as $k => $v) {
                $user=_user($v['uid']);
                $imgList.='<img src="'._imgUrl().$user['img'].'?imageView2/1/w/100/h/100" />';
            }
            $db['zanList']=$imgList;
        }
        // 是否有评论
        $db['pingNum']=Db::name('article_ping_xia')->where(['ping_id'=>$pingId])->cache(_cache('db'))->value('id');
        // 评论数量
        $db['huifuNum']=db::name('article_ping_xia')->where(['ping_id'=>$pingId])->count();
        exit(json_encode($db));
    }
    // 评论赞赞列表
    // 评论列表
    public function zanList(){
        $pages=input('post.pages/d') ? input('post.pages/d') : '';
        $pingId=input('param.pingId/d') ? input('param.pingId/d') : ''; 
        //开始验证
        $validate = new Validate([
            'pages'         =>      'number',
            'pingId'        =>      'number|require',
        ]);
        $validata=[
            'pages'         =>      $pages,
            'pingId'        =>      $pingId,
        ];
        if (!$validate->check($validata)) {
            exit(json_encode(['err'=>$validate->getError()]));;
        }
        //结束验证
        $where['pingId']=$pingId;
        $data=Db::name('article_ping_zan')->where($where)->order('time desc')->field('uid')->fetchSql(false)->paginate($pages)->toarray();
        foreach ($data['data'] as $k => $v) {
            $user=_user($v['uid']);
            $data['data'][$k]['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100";
            $data['data'][$k]['username']=$user['username'];
            $data['data'][$k]['qianming']=$user['qianming'];
        }
        exit(json_encode($data));
    }

    // 热门关键词
    public function hot_words(){
        $html=file_get_contents('http://top.baidu.com/clip?b=1&hd_h_info=1&p_name=今日实时热点排行榜&hd_h=1&hd_meshline=1&hd_border=1&hd_searches=1&col=1');
        preg_match("/var BD_DATA=([\s\S]*?)}];/",$html,$data);
        if($data){
            $data=$data['1'].'}]';
            $data=json_decode($data,true);
        }
        if($data){
            echo json_encode($data); 
            exit();
        }
        echo "[]";
    }
    public function search(){
        echo json_encode([rand(1,1000000000)]);
        exit();
        $text=input('post.text') ? input('post.text') : '';
        //开始验证
        $validate = new Validate([
            'text'         =>      'require',
        ]);
        $validata=[
            'text'         =>      $text,
        ];
        if (!$validate->check($validata)) {
            exit(json_encode(['err'=>$validate->getError()]));;
        }
        //结束验证
        $data=Db::name('article')->limit(5)->select();
        echo json_encode($data);
    }
    // 测试
    public function sasd(){
        $data=file_get_contents('http://www.gongsimingzi.com/v/result.php?ch=%C4%CF%B2%FD&nametype=1&w1=&w2=&w3=&w4=&ce=%CD%F8%C2%E7%BF%C6%BC%BC%D3%D0%CF%DE%B9%AB%CB%BE&advset1=0&firstname=&lastname=&xb=0&bir_year=2017&bir_month=06&bir_day=20&bir_hour=05&p1=%D6%D0%B9%FA&p2=&p3=');
        $data = iconv("gb2312", "utf-8//IGNORE",$data);  
        preg_match_all("/<span class='n2'>(.*?)<\/span>/", $data, $content);
        //$data=json_decode($data,true);
        dump($content[1]);
    }
    // 随机热门文章
    public function imedia(){
        $limit=input('limit/d');
        if(!$limit){
            $limit=10;
        }
        $where['hide']=1;
        $where['uid']=['<>',0];
        $where['pingNum']=['<>',0];
        $where['click']=['<>',0];
        $time=date('Y-m-d' , strtotime("-2 day"));
        $where['update_time']=['>= time',$time];
        $data=Db::name('article')->field('id,title,uid,update_time')->where($where)->limit($limit)->order('rand()')->select();
        foreach ($data as $k => $v) {
            if($v['uid']){
                $user=_user($v['uid']);
                $data[$k]['user']['id']=$user['id'];
                $data[$k]['user']['img']=$user['img'];
                $data[$k]['user']['username']=$user['username'];
            }
            $data[$k]['update_time']=date('m-d H:i',$v['update_time']);
            $data[$k]['url']=url('index/article/view',['id'=>$v['id']]);
        }
        echo json_encode($data);
    }
    // 热门订阅/头条号
    public function channel(){
        $typeid=input('typeid/d');
        $limit=input('limit/d');
        if(!$limit){
            $limit=10;
        }
        $where['hide']=1;
        $data=Db::name('member')->field('id,img,mobile,nickname')->where($where)->limit($limit)->order('rand()')->select();
        foreach ($data as $k => $v) {
            if($v['nickname']){
                $data[$k]['username']=$v['nickname'];
            }else if($v['mobile']){
                $data[$k]['mobile']=$v['mobile'];
            }
            $data[$k]['mobile']='';
            $data[$k]['nickname']='';
            if(!$v['img']){
                $data[$k]['img']='/public/style/index/member/img/userimg.gif';
            }
        }
        echo json_encode($data);
    }
  
    // 2018-03-02 12:58:52  增加轮播图
    public function slide(){
        $data=Db::name('slide')->where(['tid'=>1])->order('des desc,id desc')->select();
        foreach ($data as $k => $v) {
            $slideImg[$k]=_imgUrl().$v['img'];
            $slideText[$k]=$v['title'];
            $slideUrl[$k]['url']=$v['url'];
            $slideUrl[$k]['title']=$v['title'];
     
        }
        echo json_encode(['slide'=>$slideImg,'typeid'=>$slideUrl,'title'=>$slideText]);
    }
     // 增加启动图广告
    public function ad(){
        $config=config()['ad'];
        if($config['hide']==0 || !$config['hide']){
            exit();
        }
        $where['hide']=1;
        $id=input('id/d');
        if($config['order']==1){
            $data=Db::name('appad')->where($where)->order('rand()')->find();
        }else{
            if($id){
                $where['id']=['<',$id];
                $order='id desc';
            }else{
                $order='id desc';
            }
            $data=Db::name('appad')->where($where)->order($order)->find();
            if(!$data){
                $data=Db::name('appad')->where(['hide'=>1])->order($order)->find();
            }
        }
        // dump($data);
        // exit();
        echo json_encode(['ret'=>['config'=>$config,'data'=>$data]]);
    }
    // 增加访问次数
    public function clickAjax(){
        $id=input('id/d');
        if(!$id) exit();
        // 增加浏览次数
        Db::name('article')->where(['id'=>$id,'hide'=>1])->setInc('click');
    }


    // 
    public function zhiding(){

        // $data=input('typeid');
        //$order="zsort";
       
        // return json($questionRes);



         $request = Request::instance();
        $token=$request->param('token');
        if($token){
            $uids=_Dencrypt($token,'D');
            if(!$uids){
                $token='';
            }
        }
        $typeid=$request->param('typeid/d') ? $request->param('typeid/d') : '';
        $flags=$request->param('flags/d') ? $request->param('flags/d') : '';
        $mychannel=$request->param('mychannel/d') ? $request->param('mychannel/d') : '';
        $pages=$request->param('pages/d') ? $request->param('pages/d') : '';
        $uid=$request->param('uid/d') ? $request->param('uid/d') : '';
        $search=$request->param('search') ? $request->param('search') : '';
        $weitoutiao=$request->param('weitoutiao/d') ? $request->param('weitoutiao/d') : '';
        $date=$request->param('date/d') ? $request->param('date/d') : '';
        $city=$request->param('city') ? urldecode($request->param('city')) : '';
        if($city=='index.php'){
            $city='';
        }
        //2018年1月21日 12:18:29　城市搜索
        if($city){
            $typeid="";
            $where['title|tags|content|description|keywords']=['like','%'.$city.'%'];
           // file_put_contents("alipay.txt",$city."\r\n",FILE_APPEND);   
        }
 

     //   file_put_contents("alipay.txt",$request->param('city')."\r\n",FILE_APPEND);    
        $where['hide']=1;$where['zhiding']=1;

        if($typeid == 999999999)
        {
$where['zhiding']=8;

        }

        if($typeid && $typeid != 999999999 && $typeid !=111111111){
            // 获取下属所有ID
            if($typeid!=132){
                $typeids=_typeTid($typeid);
                $typeids=implode(',',$typeids);
                $where['typeid']=['in',$typeids]; 
            }
        }

        //if($flags){
        //    $where['flags']=['in',$flags];
        //}
        // if($mychannel){
        //     $where['mychannel']=['in',$mychannel]; 
        // }else{
        //     $where['mychannel']=['<>',4];
        // }
        // if($typeid==132){
        //     $where['mychannel']=3;
        // }

       
        if($typeid==111111111){
            $where['tuijian']=1;
        }
        else
        {
            $where['tuijian']=0;
        }


      //    if($data=="111111111")
      //   {
      //    $questionRes=db('article')->where(['zhiding'=>1,'tuijian'=>'1'])->order($order)->select();
      //   }
      // else
      //   {
      //   $questionRes=db('article')->where(['zhiding'=>1,'typeid'=>$data,'tuijian'=>'0'])->order($order)->select();
      //   }
      
        if($uid){

            $where['uid']=$uid;
            // if($weitoutiao){
            //     $where['weitoutiao']=['in',1,2];
            // }else{
            //     $where['weitoutiao']=['in',0,2];
            // }
        }else{
            if($typeid != 999999999 && $typeid != 111111111){
                if($weitoutiao){
                    $where['weitoutiao']=['in',1,2];
                }else{
                    $where['weitoutiao']=['in',0,2];
                }
            }
        }


        if($search){
            $where['title|tags']=['like','%'.$search.'%'];
        }
         
        $where['qiniu_video_type']=0;
        // $time=date('Y-m-d' , strtotime("-30 day"));
        // $where['update_time']=['>= time',$time];
        if($date){
          //  $where['create_time']=['<= time',date('Y-m-d',$date)];
        }
        // $UidGuanzhuList=_userGuanzhuList(341);
       
        // echo $UidGuanzhuList;
        // exit();
        // 导航关注
        if($token && $typeid==999999999){
            $UidGuanzhuList=_userGuanzhuList($uids); 
            if(!$UidGuanzhuList){
                exit(json_encode(['per_page'=>1,'total'=>0,'current_page'=>1,'data'=>''])); 
            }
            $UidGuanzhuList=implode(',',$UidGuanzhuList);
            $where['uid']=['in',$UidGuanzhuList];  
        }
        // var_dump($where);
        $data=Db::name('article')->where($where)->fetchSql(false)->order('zsort')->field('id,title,description,mychannel,image,update_time,source,pingNum,images,videodate,uid,click,weitoutiao,zan,video,qiniu_video_type,content')->paginate($pages)->toarray();
       
       // var_dump($data);
        $config=config()['appConfig'];
       
      
        foreach ($data['data'] as $k => $v) {
            if($v['mychannel']==3 && $v['video'] || $v['mychannel']==4){
                $data['data'][$k]['video']=_imgUrl().$v['video'];
            }
           // $data['data'][$k]['image']=_imgUrl().$imageImg;
            if($v['image'] && $v['mychannel']!=3){
                if($v['mychannel']==4){
                    $data['data'][$k]['image']=_imgUrl().$v['image'];
                }else{
                    $data['data'][$k]['image']=_imgUrl().$v['image']."?imageView2/1/w/172/h/120";
                }
            }else{
                if($v['image']){
                    $data['data'][$k]['image']=_imgUrl().$v['image'];
                }else{
                    if($v['mychannel']==1){
                        
                    }else{
                        $data['data'][$k]['image']=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                    } 
                }
            }
            $data['data'][$k]['create_time']=_time($v['update_time']);
            if($search){
                $data['data'][$k]['title']=str_replace($search,'<b class="fx-search">'.$search.'</b>',$v['title']);
            }
            if($v['uid']){
                $user=_user($v['uid']);
                if($user){
                    $data['data'][$k]['user']['username']=$user['username'];
                    $data['data'][$k]['user']['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100"; 
                }
                $data['data'][$k]['uid']=$user['id'];
            }
            if($v['weitoutiao']==0){
                $kuandu="?imageView2/1/w/192/h/124";
                if($v['mychannel']==2){
                    $images='';

                    // foreach (json_decode($v['images'],true) as $ks => $vs) {
                    //     if($ks<3){
                    //         $images.='<div class="aui-col-xs-4"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vs['img'].$kuandu.'" src="../image/loadingImage.png"></div>';
                    //     }else{
                    //         continue;
                    //     }
                    // }
                    $data['data'][$k]['pcList']=json_decode($v['images'],true);
                    if($v['image']){
                        $images='<div class="aui-col-xs-12" style="height:11.5rem"><img style="height:11.5rem" id="ffxiangImgCache" ffxiang-src="'._imgUrl().$v['image'].'?imageView2/1/w/400/h/280" src="../image/loadingImage.png" /></div>';
                    }else{
                        $images=json_decode($v['images'],true);
                        $images=$images['0']['img'];
                        $images='<div class="aui-col-xs-12" style="height:11.5rem"><img style="height:11.5rem" id="ffxiangImgCache" ffxiang-src="'._imgUrl().$images.'?imageView2/1/w/400/h/280" src="../image/loadingImage.png" /></div>';
                    }
                    $data['data'][$k]['images']=$images.'<em class="aui-iconfont aui-icon-image"> '.count(json_decode($v['images'],true)).' 图</em>';
                }else if($v['mychannel']==3){
                    // $user=_user($v['uid']);
                    // if($user){
                    //     $data['data'][$k]['user']['username']=$user['username'];
                    //     $data['data'][$k]['user']['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100"; 
                    // }
                    // $data['data'][$k]['uid']=$user['id'];
                    
                }else if($v['mychannel']==1){
                    $contet=[];
                    preg_match_all("/img src=\"(.*?)\"/", $v['content'],$img);
                  //  dump($img[1]);
                    foreach($img[1] as $ka=>$va){
                        if(strpos($va,'http') !== false){ 
                            $contet[]=$va;
                        }else if(strpos($va,'/public/uploads/us/') !== false){
                            $contet[]=$config['url'].$va;
                        }else{
                            $contet[]=_imgUrl().$va;
                        }
                    }
                    $data['data'][$k]['imagesArticleList']=$contet;
                }
            }else{
                $data['data'][$k]['description']=_substr($v['description'],100,'<a>...[查看全文]</a>'); 
                if($v['mychannel']==3 || $v['video'] || $v['mychannel']==4){
                    if($v['qiniu_video_type']){
                        $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/200/h/200';
                    }else{
                        if($v['image']){
                            $image=_imgUrl().'/'.$v['image'].'?imageView2/1/w/300/h/200';
                        }else{
                            $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                        }
                    }
                    $data['data'][$k]['image']=$image;
                    // $images='<div class="aui-col-xs-12" tapmode onclick=""><img id="ffxiangImgCache" ffxiang-src="'.$image.'" src="../image/loadingImage.png" class="fx-video-img"></div>';
                }else{
                    $kuandu="?imageView2/1/w/200/h/200";
                    $data['data'][$k]['images']=json_decode(htmlspecialchars_decode($v['images']),true);
                    if($data['data'][$k]['images']){
                        $images='';
                        $imagesPro='';
                        foreach ($data['data'][$k]['images'] as $ks=> $vsa) {
                            if(count($data['data'][$k]['images'])==1 || count($data['data'][$k]['images'])==2){
                                $col="aui-col-xs-6";
                            }else{
                                $col="aui-col-xs-4";
                            }
                            if(is_array($vsa)){
                                $vsa=$vsa['img'];
                            } 
                            $imagesPro[]=_imgUrl().$vsa;
                            $images.='<div class="'.$col.'" tapmode onclick="img('.$v['id'].','.$ks.')"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vsa.$kuandu.'" src="../image/loadingImage.png"></div>';
                        }
                        $data['data'][$k]['images']=$images;
                        $data['data'][$k]['imagesPro']=$imagesPro;
                    } 
                }                
                if($v['uid']){
                     
                    if($token){
                        // 是否关注会员 
                        $data['data'][$k]['guanzhu']=Db::name('member_guanzhu')->where(['uid'=>$uids,'gz_uid'=>$v['uid']])->value('id');
                        // 是否赞
                        $article_zan=Db::name('article_zan')->where(['aid'=>$v['id'],'uid'=>$uids])->cache(_cache('db'))->find();
                        if($article_zan['zan']){
                            $data['data'][$k]['zanUser']=1;
                        }else{
                            $data['data'][$k]['zanUser']=0;
                        }
                    }
                }
            }
            count($data['data'][$k]['content']);
            $data['data'][$k]['url']=url('article/view',['id'=>$v['id']]);
        }
        $ad=count($data['data']);
        if($ad>1000 && !$weitoutiao && $mychannel!=4){
			
			
            $sdDb=Db::name('ad')->where(['hide'=>1])->order('rand()')->find();
            if($sdDb){
                $data['data'][$ad]['mychannel']=99;
                $data['data'][$ad]['url']=$sdDb['url'];
                $data['data'][$ad]['title']=$sdDb['title'];
                $data['data'][$ad]['ad']=$sdDb['ad'];
                $data['data'][$ad]['image']=_imgUrl().$sdDb['image'];
                $data['data'][$ad]['source']=$sdDb['source'];
				$data['data'][$ad]['jinbi']=_get_jinbi($sdDb['jingbi']);
            }
        } 
    //  exit(dump($data));
        echo json_encode($data);

    }


 public function tuijian(){
	
 $request = Request::instance();
        $token=$request->param('token');
        if($token){
            $uids=_Dencrypt($token,'D');
            if(!$uids){
                $token='';
            }
        }
		$id=$request->param('id/d') ? $request->param('id/d') : ''; //文章id
        $typeid=$request->param('typeid/d') ? $request->param('typeid/d') : '';
        $flags=$request->param('flags/d') ? $request->param('flags/d') : '';
        $mychannel=$request->param('mychannel/d') ? $request->param('mychannel/d') : '';
        $pages=$request->param('pages/d') ? $request->param('pages/d') : '';
        $uid=$request->param('uid/d') ? $request->param('uid/d') : '';
        $search=$request->param('search') ? $request->param('search') : '';
        $weitoutiao=$request->param('weitoutiao/d') ? $request->param('weitoutiao/d') : '';
        $date=$request->param('date/d') ? $request->param('date/d') : '';
		$stuijian=$request->param('stuijian') ? $request->param('stuijian') : '';
        $city=$request->param('city') ? urldecode($request->param('city')) : '';
        if($city=='index.php'){
            $city='';
        }
        //2018年1月21日 12:18:29　城市搜索
        if($city){
            $typeid="";
            $where['title|tags|content|description|keywords']=['like','%'.$city.'%'];
           // file_put_contents("alipay.txt",$city."\r\n",FILE_APPEND);   
        }
        $post=Db::name('article')->where(['id'=>$id])->find();
		
		//微头条贴吧不推荐
		if($post['weitoutiao']){
			echo json_encode(['error'=>'没推荐']);
			die();
		}

     //   file_put_contents("alipay.txt",$request->param('city')."\r\n",FILE_APPEND);    
        $where['hide']=1;
		if($stuijian){
			$where['stuijian']=1;
		}
		//$where['stuijian']=1;
        if($typeid && $typeid != 999999999 && $typeid !=111111111){
            // 获取下属所有ID
            if($typeid!=132){
                $typeids=_typeTid($typeid);
                $typeids=implode(',',$typeids);
                $where['typeid']=['in',$typeids]; 
            }
        }else{
			if($post && $post['typeid']){
			  $where['typeid'] = $post['typeid']; 
			}
			//if()
		}
	
	    if($id){
			$where['id']=['<>',$id];
		}
		if($post && $post['mychannel']){
			$where['mychannel']=$post['mychannel'];
		}
		

        //if($flags){
        //    $where['flags']=['in',$flags];
        //}
		/*
        if($mychannel){
            $where['mychannel']=['in',$mychannel]; 
        }else{
            $where['mychannel']=['<>',4];
        }
        if($typeid==132){
            $where['mychannel']=3;
        }
       
        if($typeid==111111111){
            //$where['tuijian']=1;
        }
		*/
      
        if($uid){

            $where['uid']=$uid;
            // if($weitoutiao){
            //     $where['weitoutiao']=['in',1,2];
            // }else{
            //     $where['weitoutiao']=['in',0,2];
            // }
        }else{
            if($typeid != 999999999 && $typeid != 111111111){
                if($weitoutiao){
                    $where['weitoutiao']=['in',1,2];
                }else{
                    $where['weitoutiao']=['in',0,2];
                }
            }
        }


        if($search){
            $where['title|tags']=['like','%'.$search.'%'];
        }
         
        $where['qiniu_video_type']=0;
        // $time=date('Y-m-d' , strtotime("-30 day"));
        // $where['update_time']=['>= time',$time];
        if($date){
          //  $where['create_time']=['<= time',date('Y-m-d',$date)];
        }
        // $UidGuanzhuList=_userGuanzhuList(341);
       
        // echo $UidGuanzhuList;
        // exit();
        // 导航关注
        if($token && $typeid==999999999){
            $UidGuanzhuList=_userGuanzhuList($uids); 
            if(!$UidGuanzhuList){
                exit(json_encode(['per_page'=>1,'total'=>0,'current_page'=>1,'data'=>''])); 
            }
            $UidGuanzhuList=implode(',',$UidGuanzhuList);
            $where['uid']=['in',$UidGuanzhuList];  
        }
		//echo json_encode($where);
		//die();
        // var_dump($where);
       // $data=Db::name('article')->where($where)->fetchSql(false)->order('des desc,id desc')->field('id,title,description,mychannel,image,update_time,source,pingNum,images,videodate,uid,click,weitoutiao,zan,video,qiniu_video_type,content')->paginate(0,1000)->toarray();
       $data=Db::name('article')->where($where)->fetchSql(false)->order('rand()')->field('id,title,description,mychannel,image,update_time,source,pingNum,images,videodate,uid,click,weitoutiao,zan,video,qiniu_video_type,content,tuijian')->paginate(6,1000)->toarray();
       // var_dump($data);
        $config=config()['appConfig'];
       
      
        foreach ($data['data'] as $k => $v) {
            if($v['mychannel']==3 && $v['video'] || $v['mychannel']==4){
                $data['data'][$k]['video']=_imgUrl().$v['video'];
            }
           // $data['data'][$k]['image']=_imgUrl().$imageImg;
            if($v['image'] && $v['mychannel']!=3){
                if($v['mychannel']==4){
                    $data['data'][$k]['image']=_imgUrl().$v['image'];
                }else{
                    $data['data'][$k]['image']=_imgUrl().$v['image']."?imageView2/1/w/172/h/120";
                }
            }else{
                if($v['image']){
                    $data['data'][$k]['image']=_imgUrl().$v['image'];
                }else{
                    if($v['mychannel']==1){
                        
                    }else{
                        $data['data'][$k]['image']=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                    } 
                }
            }
            $data['data'][$k]['create_time']=_time($v['update_time']);
            if($search){
                $data['data'][$k]['title']=str_replace($search,'<b class="fx-search">'.$search.'</b>',$v['title']);
            }
            if($v['uid']){
                $user=_user($v['uid']);
                if($user){
                    $data['data'][$k]['user']['username']=$user['username'];
                    $data['data'][$k]['user']['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100"; 
                }
                $data['data'][$k]['uid']=$user['id'];
            }
            if($v['weitoutiao']==0){
                $kuandu="?imageView2/1/w/192/h/124";
                if($v['mychannel']==2){
                    $images='';

                    // foreach (json_decode($v['images'],true) as $ks => $vs) {
                    //     if($ks<3){
                    //         $images.='<div class="aui-col-xs-4"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vs['img'].$kuandu.'" src="../image/loadingImage.png"></div>';
                    //     }else{
                    //         continue;
                    //     }
                    // }
                    $data['data'][$k]['pcList']=json_decode($v['images'],true);
                    if($v['image']){
                        $images='<div class="aui-col-xs-12" style="height:11.5rem"><img style="height:11.5rem" id="ffxiangImgCache" ffxiang-src="'._imgUrl().$v['image'].'?imageView2/1/w/400/h/280" src="../image/loadingImage.png" /></div>';
                    }else{
                        $images=json_decode($v['images'],true);
                        $images=$images['0']['img'];
                        $images='<div class="aui-col-xs-12" style="height:11.5rem"><img style="height:11.5rem" id="ffxiangImgCache" ffxiang-src="'._imgUrl().$images.'?imageView2/1/w/400/h/280" src="../image/loadingImage.png" /></div>';
                    }
                    $data['data'][$k]['images']=$images.'<em class="aui-iconfont aui-icon-image"> '.count(json_decode($v['images'],true)).' 图</em>';
                }else if($v['mychannel']==3){
                    // $user=_user($v['uid']);
                    // if($user){
                    //     $data['data'][$k]['user']['username']=$user['username'];
                    //     $data['data'][$k]['user']['img']=_imgUrl().$user['img']."?imageView2/1/w/100/h/100"; 
                    // }
                    // $data['data'][$k]['uid']=$user['id'];
                    
                }else if($v['mychannel']==1){
                    $contet=[];
                    preg_match_all("/img src=\"(.*?)\"/", $v['content'],$img);
                  //  dump($img[1]);
                    foreach($img[1] as $ka=>$va){
                        if(strpos($va,'http') !== false){ 
                            $contet[]=$va;
                        }else if(strpos($va,'/public/uploads/us/') !== false){
                            $contet[]=$config['url'].$va;
                        }else{
                            $contet[]=_imgUrl().$va;
                        }
                    }
                    $data['data'][$k]['imagesArticleList']=$contet;
                }
            }else{
                $data['data'][$k]['description']=_substr($v['description'],100,'<a>...[查看全文]</a>'); 
                if($v['mychannel']==3 || $v['video'] || $v['mychannel']==4){
                    if($v['qiniu_video_type']){
                        $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/200/h/200';
                    }else{
                        if($v['image']){
                            $image=_imgUrl().'/'.$v['image'].'?imageView2/1/w/300/h/200';
                        }else{
                            $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                        }
                    }
                    $data['data'][$k]['image']=$image;
                    // $images='<div class="aui-col-xs-12" tapmode onclick=""><img id="ffxiangImgCache" ffxiang-src="'.$image.'" src="../image/loadingImage.png" class="fx-video-img"></div>';
                }else{
                    $kuandu="?imageView2/1/w/200/h/200";
                    $data['data'][$k]['images']=json_decode(htmlspecialchars_decode($v['images']),true);
                    if($data['data'][$k]['images']){
                        $images='';
                        $imagesPro='';
                        foreach ($data['data'][$k]['images'] as $ks=> $vsa) {
                            if(count($data['data'][$k]['images'])==1 || count($data['data'][$k]['images'])==2){
                                $col="aui-col-xs-6";
                            }else{
                                $col="aui-col-xs-4";
                            }
                            if(is_array($vsa)){
                                $vsa=$vsa['img'];
                            } 
                            $imagesPro[]=_imgUrl().$vsa;
                            $images.='<div class="'.$col.'" tapmode onclick="img('.$v['id'].','.$ks.')"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vsa.$kuandu.'" src="../image/loadingImage.png"></div>';
                        }
                        $data['data'][$k]['images']=$images;
                        $data['data'][$k]['imagesPro']=$imagesPro;
                    } 
                }                
                if($v['uid']){
                     
                    if($token){
                        // 是否关注会员 
                        $data['data'][$k]['guanzhu']=Db::name('member_guanzhu')->where(['uid'=>$uids,'gz_uid'=>$v['uid']])->value('id');
                        // 是否赞
                        $article_zan=Db::name('article_zan')->where(['aid'=>$v['id'],'uid'=>$uids])->cache(_cache('db'))->find();
                        if($article_zan['zan']){
                            $data['data'][$k]['zanUser']=1;
                        }else{
                            $data['data'][$k]['zanUser']=0;
                        }
                    }
                }
            }
            count($data['data'][$k]['content']);
            $data['data'][$k]['url']=url('article/view',['id'=>$v['id']]);
        }
        $ad=count($data['data']);
        if($ad>1 && !$weitoutiao && $mychannel!=4){
			$awhere['hide'] = 1;
			if($data['data'][0]['mychannel'] == 3){
				$awhere['position'] = ['like','%v_tuijian%'];
			}
			if($data['data'][0]['weitoutiao'] == 1){
				//$awhere['position'] = 'weitoutiao';
			}
			if(!$data['data'][0]['weitoutiao'] && $data['data'][0]['mychannel'] !=3){
				$awhere['position'] = ['like','%n_tuijian%'];
			}
            $sdDb=Db::name('ad')->where($awhere)->order('rand()')->find();
            if($sdDb){
                $data['data'][$ad]['mychannel']=99;
                $data['data'][$ad]['url']=$sdDb['url'];
                $data['data'][$ad]['title']=$sdDb['title'];
                $data['data'][$ad]['ad']=$sdDb['ad'];
				$data['data'][$ad]['jinbi']=_get_jinbi($sdDb['jingbi']);
                $data['data'][$ad]['image']=_imgUrl().$sdDb['image'];
                $data['data'][$ad]['source']=$sdDb['source'];
            }
        } 
    //  exit(dump($data));
	$data['where'] = $where;
        echo json_encode($data);

    }

	 public function tieba_index(){
		 	
        $request = Request::instance();
		$faved = array();
		$tieba = array();
        $token=$request->param('token');
		$page=$request->param('page');
        if($token){
            $uids=_Dencrypt($token,'D');
            if(!$uids){
                $token='';
            }else{
				$faved_ob = Db::name('member_favorite')->where(['uid'=>$uids,'type'=>2])->select();
				if($faved_ob && count($faved_ob)>0){
					foreach ($faved_ob as  $v) {
					   $faved[] = $v['aid'];
					}
				}
				if($page == 1){
				  $tieba = Db::name('typeid')->where(['uid'=>$uids,'tid'=>get_tieba_rootid()])->find();
				  $tieba['logo'] = _imgUrl().$tieba['image'];
				}
			}
        }
		$pages=$request->param('pages/d') ? $request->param('pages/d') : '';
		$keyword=$request->param('keyword') ? $request->param('keyword') : '';
		$is_tuijian=$request->param('is_tuijian') ? $request->param('is_tuijian') : '';
		$is_faved =$request->param('is_faved') ? $request->param('is_faved') : '';
		$tid = get_tieba_rootid();
		$where['tid'] = $tid;
		$where['hide'] = 1;
		if($keyword){
			$where['title'] = ['like','%'.$keyword.'%'];
		}
		if($is_tuijian){
			$where['is_tuijian'] = 1;
		}
		if($is_faved){
			$where['id'] = ['in',$faved];
		}
		
		$posts=Db::name('typeid')->where($where)->fetchSql(false)->order('id desc')->field('uid,id,title,description,image,update_time,keywords,content,click,faved')->paginate($pages)->toarray();
        if($posts && count($posts)>0){
		  foreach ($posts['data'] as $k => $v) {
            $posts['data'][$k]['image']=_imgUrl().$v['image'];
			$posts['data'][$k]['article_num']= Db::name('article')->where(['hide'=>1,'typeid'=>$v['id']])->count();
			if (in_array($v['id'], $faved)){
				$posts['data'][$k]['is_faved']= 1;
				$posts['data'][$k]['class']= ' added';
			}else{
				$posts['data'][$k]['is_faved']= 0;
				$posts['data'][$k]['class']= '';
			}
          }	
		  $ret=['status'=>1,'output'=>$posts,'tieba'=>$tieba];
        }else{
		  $ret=['status'=>0,'error'=>'木有贴吧'];
		}		
		 exit(json_encode($ret));
	 }
	 
	 
	 public function tieba_home(){
		 	
        $request = Request::instance();
		$faved = array();
		$tieba = array();
        $token=$request->param('token');
		$page=$request->param('page');
        if($token){
            $uids=_Dencrypt($token,'D');
            if(!$uids){
                $token='';
            }else{
				$faved_ob = Db::name('member_favorite')->where(['uid'=>$uids,'type'=>2])->select();
				if($faved_ob && count($faved_ob)>0){
					foreach ($faved_ob as  $v) {
					   $faved[] = $v['aid'];
					}
				}
				if($page == 1){
				  $tieba = Db::name('typeid')->where(['uid'=>$uids,'tid'=>get_tieba_rootid()])->find();
				  $tieba['logo'] = _imgUrl().$tieba['image'];
				}
			}
        }
		$pages=$request->param('pages/d') ? $request->param('pages/d') : '';
		$keyword=$request->param('keyword') ? $request->param('keyword') : '';
		$is_tuijian=$request->param('is_tuijian') ? $request->param('is_tuijian') : '';
		$tid = get_tieba_rootid();
		
		$config = config()['appConfig'];
		$limit = $config['guanzhu_tieba_limit']?$config['guanzhu_tieba_limit']:0;
		
		//关注贴吧
		$guanzhu = array();
		if(count($faved)>0 && $limit){
		  $fwhere['tid'] = $tid;
		  $fwhere['hide'] = 1;
		  $fwhere['id'] = ['in',$faved];
		  $guanzhu=Db::name('typeid')->where($fwhere)->fetchSql(false)->order('id desc')->field('uid,id,title,description,image,update_time,keywords,content,click,faved')->limit($limit)->select();
		  if($guanzhu && count($guanzhu)>0){
		   foreach ($guanzhu as $k => $v) {
            $guanzhu[$k]['image']=_imgUrl().$v['image'];
			if (in_array($v['id'], $faved)){
				$guanzhu[$k]['is_faved']= 1;
				$guanzhu[$k]['class']= ' added';
			}else{
				$guanzhu[$k]['is_faved']= 0;
				$guanzhu[$k]['class']= '';
			}
			$guanzhu[$k]['article_num']= Db::name('article')->where(['hide'=>1,'typeid'=>$v['id']])->count();
           }	
          }
		}
		
		$limit = $config['tuijian_tieba_limit']?$config['tuijian_tieba_limit']:0;
		//推荐贴吧
		$tuijian = array();
		$twhere['tid'] = $tid;
		$twhere['hide'] = 1;
		$twhere['is_tuijian'] = 1;
		if($limit){
		$tuijian=Db::name('typeid')->where($twhere)->fetchSql(false)->order('id desc')->field('uid,id,title,description,image,update_time,keywords,content,click,faved')->limit($limit)->select();
		if($tuijian && count($tuijian)>0){
		   foreach ($tuijian as $k => $v) {
            $tuijian[$k]['image']=_imgUrl().$v['image'];
			if (in_array($v['id'], $faved)){
				$tuijian[$k]['is_faved']= 1;
				$tuijian[$k]['class']= ' added';
			}else{
				$tuijian[$k]['is_faved']= 0;
				$tuijian[$k]['class']= '';
			}
			$tuijian[$k]['article_num']= Db::name('article')->where(['hide'=>1,'typeid'=>$v['id']])->count();
           }	
        }
		}
		
		$limit = $config['zuixin_tieba_limit']?$config['zuixin_tieba_limit']:0;
		$posts = array();
		$lwhere['tid'] = $tid;
		$lwhere['hide'] = 1;
		if($limit){
		$posts=Db::name('typeid')->where($lwhere)->fetchSql(false)->order('id desc')->field('uid,id,title,description,image,update_time,keywords,content,click,faved')->limit($limit)->select();
        if($posts && count($posts)>0){
		  foreach ($posts as $k => $v) {
            $posts[$k]['image']=_imgUrl().$v['image'];
			if (in_array($v['id'], $faved)){
				$posts[$k]['is_faved']= 1;
				$posts[$k]['class']= ' added';
			}else{
				$posts[$k]['is_faved']= 0;
				$posts[$k]['class']= '';
			}
			$posts[$k]['article_num']= Db::name('article')->where(['hide'=>1,'typeid'=>$v['id']])->count();
          }	
		  
        }
		}
		$ret=['status'=>1,'guanzhu'=>$guanzhu,'tuijian'=>$tuijian,'zuixin'=>$posts,'tieba'=>$tieba];
		exit(json_encode($ret));
	 }
	 
	 public function tieba_list(){
		$request = Request::instance();
		$faved = array();
        $token=$request->param('token');
        if($token){
            $uids=_Dencrypt($token,'D');
            if(!$uids){
                $token='';
            }else{
				$faved_ob = Db::name('member_favorite')->where(['uid'=>$uids,'type'=>2])->select();
				if($faved_ob && count($faved_ob)>0){
					foreach ($faved_ob as  $v) {
					   $faved[] = $v['aid'];
					}
				}
			}
        }
		$pages=$request->param('pages/d') ? $request->param('pages/d') : '';
		$page=$request->param('page/d') ? $request->param('page/d') : '';
		$tid=$request->param('tid/d') ? $request->param('tid/d') : '';
		
		if($page == 1){
			//$twhere['t.uid']=$uids;
            //$twhere['t.id']= $tid;
			
            $join = [
                ['__MEMBER__ m','t.bazhu_id=m.id'],
            ];
			//$tieba=Db::name('typeid')->where($twhere)->alias('t')->join($join)->field('t.bazhu_id,t.uid,t.id,t.title,t.description,t.image,t.update_time,t.keywords,t.content,t.click,t.faved,m.nickname')->fetchSql(false)->find();
			//$tieba['image']=$tieba['image'];
			$twhere['id']= $tid;
			$tieba=Db::name('typeid')->where($twhere)->find();
			$tieba['content']= strip_tags($tieba['content']);
			if (in_array($tieba['id'], $faved)){
				$tieba['is_faved']= 1;
			}else{
				$tieba['is_faved']= 0;
			}
			if($tieba['bazhu_id']){
				//$user = Db::name('member')->where(["id"=>$tieba['bazhu_id']])->find();
				$user=_user($tieba['bazhu_id']);
				$tieba['nickname'] = $user['username'];
			}else{
				$tieba['nickname'] = 0;
			}
			$tieba['article_num']= Db::name('article')->where(['hide'=>1,'typeid'=>$tieba['id']])->count();
			
			$zwhere['typeid'] = $tid;
		    $zwhere['hide'] = 1;
			$zwhere['zhiding'] = 1;
			$toplist=Db::name('article')->where($zwhere)->field('id,title,description,mychannel,image,update_time,source,pingNum,images,videodate,uid,click,weitoutiao,zan,video,qiniu_video_type,content')->order('click desc')->fetchSql(false)->select();
			
			//$tieba['image']=_imgUrl().$tieba['image'];
			
		}
		
		$where['a.typeid'] = $tid;
		$where['a.hide'] = 1;
		$where['a.zhiding'] = array('in',[0,2]);
		$join = [
                ['__MEMBER__ m','a.uid=m.id'],
            ];
		$data=Db::name('article')->where($where)->alias('a')->join($join)->fetchSql(false)->order('a.update_time desc')->field('a.id,a.title,a.description,a.tuijian,a.mychannel,a.image,a.update_time,a.source,a.pingNum,a.images,a.videodate,a.uid,a.click,a.weitoutiao,a.zan,a.video,a.qiniu_video_type,a.content,m.nickname,m.img,m.mobile')->paginate($pages)->toarray();
		 if($data && count($data)>0){
		  foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['image']=_imgUrl().$v['image'];
			if(!$v['img']){
				$data['data'][$k]['img']='../image/touxiang.png';
			}else{
				$data['data'][$k]['img']=_imgUrl().$v['img'];
			}
			if(!$v['nickname']){
				$data['data'][$k]['nickname']=$v['mobile'];
			}
			
			$data['data'][$k]['video']=_imgUrl().$v['video'];
			
			$data['data'][$k]['create_time']=_time($v['update_time']);
			$data['data'][$k]['description']=_substr($v['description'],100,'<a>...[查看全文]</a>'); 
                if($v['mychannel']==3 || $v['video'] || $v['mychannel']==4){
                    if($v['qiniu_video_type']){
                        $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/200/h/200';
                    }else{
                        if($v['image']){
                            $image=_imgUrl().'/'.$v['image'].'?imageView2/1/w/300/h/200';
                        }else{
                            $image=_imgUrl().'/'.$v['video'].'?vframe/jpg/offset/2/w/300/h/200';
                        }
                    }
                    $data['data'][$k]['image']=$image;
                    // $images='<div class="aui-col-xs-12" tapmode onclick=""><img id="ffxiangImgCache" ffxiang-src="'.$image.'" src="../image/loadingImage.png" class="fx-video-img"></div>';
                }else{
                    $kuandu="?imageView2/1/w/200/h/200";
                    $data['data'][$k]['images']=json_decode(htmlspecialchars_decode($v['images']),true);
                    if($data['data'][$k]['images']){
                        $images='';
                        $imagesPro='';
                        foreach ($data['data'][$k]['images'] as $ks=> $vsa) {
                            if(count($data['data'][$k]['images'])==1 || count($data['data'][$k]['images'])==2){
                                $col="aui-col-xs-6";
                            }else{
                                $col="aui-col-xs-4";
                            }
                            if(is_array($vsa)){
                                $vsa=$vsa['img'];
                            } 
                            $imagesPro[]=_imgUrl().$vsa;
                            $images.='<div class="'.$col.'" tapmode onclick="img('.$v['id'].','.$ks.')"><img id="ffxiangImgCache" ffxiang-src="'._imgUrl().$vsa.$kuandu.'" src="../image/loadingImage.png"></div>';
                        }
                        $data['data'][$k]['images']=$images;
                        $data['data'][$k]['imagesPro']=$imagesPro;
                    } 
                }                
                if($v['uid']){
                     
                    if($token){
                        // 是否关注会员 
                        $data['data'][$k]['guanzhu']=Db::name('member_guanzhu')->where(['uid'=>$uids,'gz_uid'=>$v['uid']])->value('id');
                        // 是否赞
                        $article_zan=Db::name('article_zan')->where(['aid'=>$v['id'],'uid'=>$uids])->cache(_cache('db'))->find();
                        if($article_zan['zan']){
                            $data['data'][$k]['zanUser']=1;
                        }else{
                            $data['data'][$k]['zanUser']=0;
                        }
                    }
                }
				
			
			
			
          }
		  $awhere['hide'] = 1;
		  $awhere['position'] = ['like','%tieba%'];
		  $data['awhere'] = $awhere;
          $sdDb=Db::name('ad')->where($awhere)->order('rand()')->find();
          if($sdDb){
                $arr=['mychannel'=>99,'url'=>$sdDb['url'],'title'=>$sdDb['title'],'ad'=>$sdDb['ad'],'image'=>_imgUrl().$sdDb['image'],'source'=>$sdDb['source'],'jinbi'=>_get_jinbi($sdDb['jingbi'])];
                array_push($data['data'],$arr);
                
          }
		  $ret=['status'=>1,'output'=>$data];
        }else{
		  $ret=['status'=>0,'error'=>'木有帖子'];
		}
        
		if($page == 1){
			Db::name('typeid')->where(['id'=>$tid])->setInc('click');
			$ret['tieba'] = $tieba;
			$ret['toplist'] = $toplist;
		}


		
		 exit(json_encode($ret)); 
	 }
	 
	 
	 //下载任务
	 public function download_list(){
		$request = Request::instance();
		$faved = array();
        $token=$request->param('token');
        if($token){
            $uids=_Dencrypt($token,'D');
            if(!$uids){
                $token='';
            }else{
				
			}
        }
		
		
		$where['hide'] = 1;
		$where['position'] = ['like','%download%'];
		$data=Db::name('ad')->where($where)->select();
		
		 if($data && count($data)>0){
		  foreach ($data as $k => $v) {
            $data[$k]['image']=_imgUrl().$v['image'].'?imageView2/1/w/200/h/200';
          }
		  $ret=['status'=>1,'output'=>$data];
        }else{
		  $ret=['status'=>0,'error'=>'木有数据'];
		}
        
	


		
		 exit(json_encode($ret)); 
	 }
	 
	 //点击任务
	 public function clickad_list(){
		$request = Request::instance();
		$faved = array();
        $token=$request->param('token');
        if($token){
            $uids=_Dencrypt($token,'D');
            if(!$uids){
                $token='';
            }else{
				
			}
        }
		$ip = $request->ip();
		
		$where['hide'] = 1;
		$where['position'] = ['like','%clickad%'];
		$data=Db::name('ad')->where($where)->select();
		
		 if($data && count($data)>0){
		  foreach ($data as $k => $v) {
            $data[$k]['image']=_imgUrl().$v['image'].'?imageView2/1/w/200/h/200';
			$data[$k]['is_view']= 0;
			$content='IP:'.$ip.'查看广告:'.$data[$k]['source'].' 奖励金币';
			$count=Db::name('yaoqing_records')->where(['content'=>['like','%'.$content.'%']])->whereTime('time','today')->count();
			if($count>0){
				$data[$k]['is_view']= 1;
			}
          }
		  $ret=['status'=>1,'output'=>$data];
        }else{
		  $ret=['status'=>0,'error'=>'木有数据'];
		}
        
	


		
		 exit(json_encode($ret)); 
	 }
	 
	 
	 //每分享一篇文章奖励
	
	function  share_post(){
		$request = Request::instance();
        $ip = $request->ip();
		$shareuid=$request->param('shareuid');   // 我的id
		$uid = $shareuid;
		if(!$shareuid){
			echo json_encode(['status'=>0,'input'=>$_POST,'shareuid'=>$shareuid]);
            die(); 
		}
		$id=$request->param('id');  // 文章id
		$yaoqing=config()['yaoqing'];
		$name=Db::name('article')->where(['id'=>$id])->find();
		$row=Db::name('yaoqing')->where(['uid'=>$id])->find();
		
		$jinbi = $yaoqing['jinbi_article_share'];
		$jinbi = _get_jinbi($jinbi);
		$limit = $yaoqing['article_share_time'];
		$ft = '-文章分享被阅读';
		if($name['mychannel']==2){
			$jinbi = _get_jinbi($yaoqing['jinbi_article_share']);
			$limit = $yaoqing['article_share_time'];
			$ft = '-文章分享被阅读';
		}
		if($name['mychannel']==3){
			$jinbi = _get_jinbi($yaoqing['jinbi_video_share']);
			$limit = $yaoqing['video_share_time'];
			$ft = '-视频分享被浏览';
		}
		if($name['mychannel']==4){
			$jinbi = _get_jinbi($yaoqing['jinbi_smallvideo_share']);
			$limit = $yaoqing['smallvideo_share_time'];
			$ft = '-视频分享被浏览';
		}
		
		
		$ct = '分享奖励金币';
		$count=Db::name('yaoqing_records')->where(['uid'=>$uid,'content'=>['like','%'.$ct.'%']])->whereTime('time','today')->count();
		
		if($limit){
		if($count>=$limit){
			echo json_encode(['status'=>0,'input'=>$_POST]);
             die(); 
		}
		}
		//金币奖励每次分享
		if($jinbi){
			$content='IP:'.$ip.' 内容id:'.$id.'分享奖励金币';
			$count=Db::name('yaoqing_records')->where(['uid'=>$uid,'content'=>['like','%'.$content.'%']])->whereTime('time','today')->count();	
            if(!$count){
				    $db=_moneyDb([
                        'uid'=>$uid,
                        'jinbi'=>[
                            'money'=>$jinbi,
                            'content'=>$content.$ft
                        ]
                    ]);
					if($db){
                        echo json_encode(['status'=>1]);
                        die(); 
                    } 
			}			
		}
		
		 echo json_encode(['error'=>1]);
         die();
	
	}


}

 