<?php
header('Content-Type: text/html;charset=utf-8');
    header('Access-Control-Allow-Origin:*'); // *���������κ���ַ����
    header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // �������������
    header('Access-Control-Allow-Credentials: true'); // �����Ƿ������� cookies
    header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // ���������Զ�������ͷ���ֶ�
global $_W, $_GPC;
$content=trim($_GPC['content']);
$tbpid=$_GPC['tbpid'];
$qdid=$_GPC['qdid'];
$pddpid=$_GPC['pddpid'];
$jdpid=$_GPC['jdpid'];
$uid=$_GPC['uid'];
$cfg = $this->module['config'];

$share=pdo_fetch("select * from ".tablename('tiger_newhu_share')." where weid='{$_W['uniacid']}' and id='{$uid}'");

 
  //������ʼ
 $arr=strstr($content,"jd.com");
  if($arr!==false){
	  $jdset=pdo_fetch("select * from ".tablename('tuike_jd_jdset')." where weid='{$_W['uniacid']}' order by id desc");
	  if(empty($jdpid)){
		  $jdpid=$jdset['jdpid'];
	  }
	  $geturl=$this->geturl($content);
	  $goodsid=$this->jdgoodsID($geturl);
	  $csssurl=$_W['siteroot']."app/index.php?i=".$_W['uniacid']."&c=entry&jdlm=1&do=jdview&m=tuike_jd&cjcjss=1&itemid=".$goodsid;
	 // echo $csssurl;
	 // exit;
	  $ssview=$this->curl_request($csssurl);
	  $sssviewarr=@json_decode($ssview, true);
	  $yhjurl=$sssviewarr['discount_link'];//�Ż�ȯ����
	  if(empty($sssviewarr['itemtitle'])){
	  	//�����Ż�
	  }
	  // echo "<pre>";
	  // print_r($sssviewarr);
	  // exit;
	  $zl= $this->jdviewzl($jdset,$goodsid,$jdpid,$yhjurl);
	  // echo "<pre>";
	  // echo $goodsid."----<br>";
	  // echo $jdpid."----<br>";
	  // echo $yhjurl."----<br>";
	  // print_r($jdset);
	  // print_r($zl);
	  // exit;
	  $couponmoney=$sssviewarr['couponmoney'];
	  $vurl=$zl;
	  $itemprice=$sssviewarr['itemprice'];
	  $itemendprice=$sssviewarr['itemendprice'];
	  $rate=$sssviewarr['rate'];
	  //����
	  $bl=pdo_fetch("select * from ".tablename('tiger_wxdaili_set')." where weid='{$_W['uniacid']}'");
	  if($cfg['lbratetype']==3){//ȫ���ô������
	  	$flyj=$this->ptyjjl($itemendprice,$rate,$cfg);
	  	if($cfg['fxtype']==1){//����           
	     		 $lx=$cfg["hztype"];	
	     		  $flyj=intval($flyj);		           		 
	      }else{//���
	          $lx=$cfg["yetype"];
	          if($cfg['txtype']==3){
	              $lx='���ֱ�';            
	          }
	          $zyh=$couponmoney+$flyj;//�Żݽ��
	    	    $zyhhprice=$itemprice-$zyh;//�Żݺ�۸�
	      }
	  }else{
	         if($cfg['fxtype']==1){//����
	              $flyj=$this->sharejl($itemendprice,$rate,$bl,$share,$cfg);
	          }else{//���            
	              $flyj=$this->sharejl($itemendprice,$rate,$bl,$share,$cfg);
	              $zyh=$couponmoney+$flyj;//�Żݽ��
	              $zyhhprice=$itemprice-$zyh;//�Żݺ�۸�
	          }	
	  }
	 //    $msg=str_replace('#����#','', $cfg['pddwenan']);
		// $msg=str_replace('#ƴ������ַ#',$vurl, $msg);
		// $msg=str_replace('#����#',$sssviewarr['itemtitle'], $msg);
		// $msg=str_replace('#�Ƽ�����#',$sssviewarr['itemtitle'], $msg);
		// $msg=str_replace('#ԭ��#',$itemprice, $msg);
		// $msg=str_replace('#ȯ���#',$itemendprice, $msg);
		// $msg=str_replace('#�Ż�ȯ#',$couponmoney, $msg);			
		// $msg=str_replace('#����#',$flyj.$lx, $msg);
		if(empty($couponmoney)){
			$couponmoney=0;
		}
		$data=array(
			'pttype'=>1,
			'url'=>$vurl,
			'itemtitle'=>$sssviewarr['itemtitle'],
			'itemprice'=>$itemprice,
			'itemendprice'=>$itemendprice,
			'couponmoney'=>$couponmoney,
			'flyj'=>$flyj			
		);
		exit(json_encode(array('err' => 1, 'data' =>$data)));
		echo "<pre>";
		print_r($data);

	  //����
  }
//��������


 //ƴ���
         $arr=strstr($content,"yangkeduo.com");
        if($arr!==false){        	
			
			//PID����
        	$pddset=pdo_fetch("select * from ".tablename('tuike_pdd_set')." where weid='{$_W['uniacid']}'");
			if(empty($pddpid)){
				$p_id=$pddset['pddpid'];
			}else{
               $p_id=$pddpid;
            }
			$owner_name=$pddset['ddjbbuid'];
			
			
			//��ȡ����
			$geturl=$this->geturl($content);
			$itemid=$this->pddgoodsID($geturl);
			//return $this->respText($itemid);
			if(empty($itemid)){
				//�����Ż�ȯ
			}		
			include ROOT_PATH . "inc/sdk/tbk/pdd.php"; 
			//ת������
			$zl=pddviewzl($owner_name,$itemid,$p_id);	
			$goods=pddview($owner_name,$itemid);
			// echo "<pre>";
			// print_r($zl);
			// print_r($goods);
			// exit;
			$data=$goods['goods_detail_response']['goods_details'][0];
			
			$zldata=$zl['goods_promotion_url_generate_response']['goods_promotion_url_list'][0];		
            //exit(json_encode(array('err' => 100, 'data' =>$zldata)));
			
			$itemendprice=($data['min_group_price']-$data['coupon_discount'])/100;
			$itemtitle=$data['goods_name'];
			$itemprice=$data['min_group_price']/100;
			$couponmoney=$data['coupon_discount']/100;
			
			$url2=$zldata['we_app_web_view_url'];//����ַ
			$itemdesc=$data['goods_desc'];
			$rate=$data['promotion_rate']/10;//ʵ��Ӷ��
			if(!empty($zl['error_response'])){
				//$itemtitle=$zl['error_response']['error_msg'];
				//$itemtitle=$cfg['error2'];
				//return $this->respText($itemtitle);
			}
			if(empty($rate)){
				$itemtitle=$cfg['error2'];
				//return $this->respText($itemtitle);
			}
			$bl=pdo_fetch("select * from ".tablename('tiger_wxdaili_set')." where weid='{$_W['uniacid']}'");
			if($cfg['lbratetype']==3){//ȫ���ô������
				$flyj=$this->ptyjjl($itemendprice,$rate,$cfg);
	        	if($cfg['fxtype']==1){//����           
	           		 $lx=$cfg["hztype"];	
	           		  $flyj=intval($flyj);		           		 
		        }else{//���
		            $lx=$cfg["yetype"];
		            if($cfg['txtype']==3){
		                $lx='���ֱ�';            
		            }
		            $zyh=$couponmoney+$flyj;//�Żݽ��
	          	    $zyhhprice=$itemprice-$zyh;//�Żݺ�۸�
		        }
			}else{
	               if($cfg['fxtype']==1){//����
			            $flyj=$this->sharejl($itemendprice,$rate,$bl,$share,$cfg);
			        }else{//���            
			            $flyj=$this->sharejl($itemendprice,$rate,$bl,$share,$cfg);
			            $zyh=$couponmoney+$flyj;//�Żݽ��
			            $zyhhprice=$itemprice-$zyh;//�Żݺ�۸�
			        }	
			}
			
			// $tturl=$_W['siteroot'].str_replace('./','app/',$this->createMobileurl('pddgoodslist',array('key'=>$itemtitle)));			
   //          $ddwz=$this->pdddwzw($url2);
			
			// $msg=str_replace('#����#','', $cfg['pddwenan']);
			// $msg=str_replace('#����#',$itemtitle, $msg);
			// $msg=str_replace('#�Ƽ�����#',$itemdesc, $msg);
			// $msg=str_replace('#ԭ��#',$itemprice, $msg);
			// $msg=str_replace('#ȯ���#',$itemendprice, $msg);
			// $msg=str_replace('#�Ż�ȯ#',$couponmoney, $msg);
			// $msg=str_replace('#ƴ������ַ#',$ddwz, $msg);
			// $msg=str_replace('#����#',$flyj.$lx, $msg);
			// $msg=str_replace('#ͬ���Ʒ#',$ddwz, $msg);
			$data=array(
			    'pttype'=>2,
				'url'=>$url2,
				'itemtitle'=>$itemtitle,
				'itemprice'=>$itemprice,
				'itemendprice'=>$itemendprice,
				'couponmoney'=>$couponmoney,
				'flyj'=>$flyj			
			);
			exit(json_encode(array('err' => 100, 'data' =>$data)));
			echo "<pre>";
			print_r($data);
        }
         //ƴ������
		 
		 
		 
		 //�Ա�
		 if(empty($tbpid)){
			 $tbpid=$cfg['ptpid'];
		 }
		$pidSplit=explode('_',$tbpid);
		$memberid=$pidSplit[1];
		$cfg['siteid']=$pidSplit[2];
		$cfg['adzoneid']=$pidSplit[3];
		if(empty($memberid)){
			$tksign = pdo_fetch("SELECT * FROM " . tablename($this->modulename."_tksign") . " WHERE  tbuid='{$cfg['tbuid']}'");
		}else{
			$tksign = pdo_fetch("SELECT * FROM " . tablename($this->modulename."_tksign") . " WHERE  memberid='{$memberid}'");
		}
		
        include ROOT_PATH . "inc/sdk/tbk/tb.php"; 
		include ROOT_PATH . "inc/sdk/tbk/goodsapi.php"; 
		$tklinfo=tklnew($content,$cfg['adzoneid'],$cfg['siteid'],$tksign['sign']);
		if($tklinfo['sub_code']==10000){
			//��Ӷ��
		}
		$goodsid=$tklinfo['data']['num_iid'];
		if(!empty($goodsid)){
				  $geturl="https://item.taobao.com/item.htm?id=".$goodsid;
		}else{
			$geturl=$this->geturl($content);
		}
		
		 if(!empty($geturl)){ 
			 $istao=$this->myisexists($geturl);            
			  if(!empty($istao)){
				if($istao==1){//e22a��ַ				
					 $goodsid=$this->getgoodsid($geturl);
					 if(empty($goodsid)){
						$goodsid=$this->hqgoodsid($geturl); 
					 }
					  if(empty($goodsid)){
						 //��Ӷ��
					  }  
									 
				}elseif($istao==2){//�Ա���è��ַ
					$goodsid=$this->mygetID($geturl); 
					 if(empty($goodsid)){
						$goodsid=$this->getgoodsid($geturl);
					 }
					  if(empty($goodsid)){
						  //��Ӷ��
					  }                     
				}elseif($istao==3){
					 $geturlitemid=$this->getrhy($geturl);
					 $goodsid=$geturlitemid['itemid'];				 
				}
				$url="https://item.taobao.com/item.htm?id=".$goodsid;
				$key=urlencode($url);                     
				$goods=cjsearch(1,$cfg['ptpid'],$tksign['sign'],$tksign['tbuid'],$_W,$cfg,$key,2,'','','','',0,0,0);   
				$goods=$goods['result_list']['map_data'];//�����������  
				
				if(!empty($goods['coupon_amount'])){//�Ż�ȯ���					
					$conmany=$goods['coupon_amount'];     
				}else{
					$conmany=0;
				} 

				 $res=hqyongjin($url,0,$cfg,$this->modulename,'','',$tksign['sign'],$tksign['tbuid'],$_W,1,$goodsid,$qdid);//�����Ӷ��     
				 $erylj=$res['dcouponLink'];
				 if(!empty($erylj)){
				 	 $erylj=str_replace("http:","https:",$erylj);
				     $taokouling=$this->tkl($erylj,$goods['pict_url'],$goods['title']);
				     $res['taokouling']=$taokouling;
				 }
				  $itemprice=$goods['zk_final_price'];
				  $commissionRate=$res['commissionRate'];
				  if(empty($conmany)){//���IDΪ���Ż�ȯ���ż��ģ��Ͳ������Ż�ȯ
				    $yongjin=$itemprice*$commissionRate/100;//Ӷ��
				    $itemendprice=$itemprice;
				  }else{
				    $yongjin=($itemprice-$conmany)*$commissionRate/100;//Ӷ��
				    $itemendprice=$itemprice-$conmany;
				  }
				   $bl=pdo_fetch("select * from ".tablename('tiger_wxdaili_set')." where weid='{$_W['uniacid']}'");
				   if($cfg['lbratetype']==3){//ȫ���ô������
				   	$flyj=$this->ptyjjl($itemendprice,$commissionRate,$cfg);
				   	if($cfg['fxtype']==1){//����           
				      		 $lx=$cfg["hztype"];	
				      		  $flyj=intval($flyj);		           		 
				       }else{//���
				           $lx=$cfg["yetype"];
				           if($cfg['txtype']==3){
				               $lx='���ֱ�';            
				           }
				           $zyh=$conmany+$flyj;//�Żݽ��
				     	    $zyhhprice=$itemprice-$zyh;//�Żݺ�۸�
				       }
				   }else{
				   	 
				          if($cfg['fxtype']==1){//����
				               $flyj=$this->sharejl($itemendprice,$commissionRate,$bl,$share,$cfg);
				           }else{//���            
				           	 //return $this->respText("aacs".$share['dltype']);
				               $flyj=$this->sharejl($itemendprice,$commissionRate,$bl,$share,$cfg);
				               //return $this->respText("aacs".$flyj);
				              
				               $zyh=$conmany+$flyj;//�Żݽ��
				               $zyhhprice=$itemprice-$zyh;//�Żݺ�۸�
				           }	
				   }
				   $goodsviewtz=$_W['siteroot'].str_replace('./','app/',$this->createMobileurl('tklview',array('itemid'=>$goodsid,'itemendprice'=>$itemendprice,'couponmoney'=>$conmany,'itempic'=>urlencode($goods['pict_url']),'tkl'=>$res['taokouling'],'itemprice'=>$itemprice)))."&rhyurl=".urlencode($erylj)."&itemtitle=".urlencode($goods['title']);
				   
				   $tcn=$this->dwz($erylj,$goodsviewtz);//����ַ
		    	   $data=array(
				    'pttype'=>3,
				   	'url'=>$tcn,
				   	'itemtitle'=>$goods['title'],
				   	'itemprice'=>$itemprice,
				   	'itemendprice'=>$itemendprice,
				   	'couponmoney'=>$conmany,
				   	'flyj'=>$flyj,
					'tkl'=>$res['taokouling']
				   );
				   exit(json_encode(array('err' => 100, 'data' =>$data)));
				   echo "<pre>";
				   print_r($data);
				
				
			 }
		 }else{
         	  exit(json_encode(array('err' => 200, 'data' =>"�鲻��")));
         }
