<?php
require_once(dirname(__FILE__) . "/config.php");

require_once(dirname(__FILE__) . "/wechat.php");

$w = new Wechat(TOKEN, DEBUG);

//首次验证，验证过以后可以删掉
if (isset($_GET['echostr'])) {
    $w->valid();
    exit();
}


//回复用户
$w->reply("reply_cb");

//后续必要的处理...
/* TODO */
exit();

function reply_cb($request, $w){
    $content = trim($request['Content']);
	$uname = trim($request['FromUserName']);
    if ($w->get_msg_type() == "event") {
		if(trim($request['Event'])=='subscribe'){
			return "猜数字玩法：系统设定一个没有重复的4位数字(1-9)，用户猜这个数字，每猜一次系统会给出提示几A几B，其中A前面的数字表示位置正确的数的个数，而B前的数字表示数字正确而位置不对的数的个数。用户再根据提示继续猜，连续10次都没猜中系统公布答案游戏结束，可开始新一局。输入一个4位没有重复的数字回复即开始游戏";
		}
	}

    if ($content !== "url"){
        if(!empty($content)){
			if(is_numeric($content)){return guess($content,$uname);}
			else{return "猜数字玩法：系统设定一个没有重复的4位数字(1-9)，用户猜这个数字，每猜一次系统会给出提示几A几B，其中A前面的数字表示位置正确的数的个数，而B前的数字表示数字正确而位置不对的数的个数。用户再根据提示继续猜，连续10次都没猜中系统公布答案游戏结束，可开始新一局。输入一个4位没有重复的数字回复即开始游戏（如：1234）";
			}
		}
    }
}


//获取四个不重复的数字
function getnum(){
	$arr = Array('1','2','3','4','5','6','7','8','9');
	shuffle($arr);
	$rand = array_slice($arr,0,4);
	$tempnum=implode('',$rand);
	return $tempnum;
}

//校验数字
function checknum($snum,$unum){
	$a = 0;
	$b = 0;
	$usernum = array(substr($unum,0,1),substr($unum,1,1),substr($unum,2,1),substr($unum,3,1));
	$sysnum = array(substr($snum,0,1),substr($snum,1,1),substr($snum,2,1),substr($snum,3,1));
	for($i=0;$i<4;$i++){
		if($usernum[$i]==$sysnum[$i]){$a = $a + 1;}
		if(in_array($usernum[$i],$sysnum)){$b = $b + 1;}
	}
	$b = $b - $a;
	$tarr = array($a,$b);
	return $tarr;
}


function guess($num,$uname){
	$cnum = str_replace('0','',$num);
	$cnum = preg_replace("/(.).*\\1+/i","$1",$cnum);
	$old = @file_get_contents("$uname.txt");
	if(strlen($old)>5){$garr = json_decode($old,true);}
	else{$garr = array('num'=>'','dtimes'=>0,'sysnum'=>getnum(),'atime'=>time());}
	if(strlen($cnum)!=4){
		$out = $garr['num'];
		$out .= "\n您输入了：".$num."，请输入4位不相同且不含0的数字！";
	}elseif(strpos($garr['num'],$num)>0){
		$out = $garr['num'];
		$out .= "\n您输入的：".$num."，前面已经猜过了，不要浪费次数！";
	}else{
		if(strlen($num)!=4 || $garr['dtimes']>9){
			$garr['num'] = '';
			$garr['dtimes'] = 0;
			$garr['sysnum'] = getnum();
			$garr['atime'] = time();
		}else{
			$garr['dtimes'] = $garr['dtimes'] + 1;
			if($garr['dtimes']<11){
				$check = checknum($garr['sysnum'],$num);
				if($check[0]==4 && $garr['dtimes']==1){
					$garr['sysnum'] = getnum();
					$check = checknum($garr['sysnum'],$num);
				}
				$a = $check[0];
				$b = $check[1];
			}
			$out = "第".$garr['dtimes']."次：".$num."  ".$a."A".$b."B";
			$out = $garr['num']."\n$out";
			$garr['num'] = $out;
			$atime = time() - $garr['atime'];
			@file_put_contents("$uname.txt",json_encode($garr));
			if($a==4){
				$nice = array("1"=>"99.9","2"=>"99","3"=>"98","4"=>"97","5"=>"89","6"=>"65","7"=>"20","8"=>"8","9"=>"4","10"=>"1");
				$good = $nice[$garr['dtimes']];
				$out .=  "\n您猜中了:".$garr['sysnum']."/:v/:@)\n总耗时".$atime."秒,共猜".$garr['dtimes']."次\n击败了".$good."%的高手/:strong\n再来一局？/:jj\n重新回复4个不重复的数字开始新的一局";
				@unlink("$uname.txt");
			}elseif($garr['dtimes']==10){
				$out .= "\n正确数字为:".$garr['sysnum']."/:pig/:,@f\n您已经猜了10次了,此局结束!\n再试一次？/:jj\n重新回复4个不重复的数字开始新的一局";
				@unlink("$uname.txt");
			}
		}
	}
	$out = "玩法介绍请回复：help".$out;
	return $out;
}
?>
