<?php
/*
	变量模版书写与替换，通过按照指定格式书写的变量名数组，生成指定格式的消息模版，然后再调用替换函数，可以根据消息模版和动态数据生成最终消息字符串。
	
*/


class template_replace{
	/*
		参数说明：
			$names		变量名数组
			$data		变量值数组
			$template	模版字符串
		替换思路：
			1、遍历变量名数组将模版字符串中的变量名替换为变量键名
			2、遍历变量值数组将第一步得到的模版字符串中的变量键名替换为变量值
	*/
	public function replace($names,$data,$template){
		$template=$this->name_replace($names,$template);
		$template=$this->value_replace($data,$template);
		return $template;
	}
	private function name_replace($names,$template,$pre_name='',$pre_title=''){
		if(!is_string($template) || $template===''){
			return '';
		}
		if(!is_array($names) || empty($names)){
			return $template;
		}
		foreach($names as $key=>$value){
			if(!$value['child']){
				//没有下级元素
				$template=str_replace("【{$pre_title}{$value['title']}】","{{$pre_name}{$key}}",$template);
			}elseif(!$value['is_array']){
				//非数值型数组
				$template=$this->name_replace($value['child'],$template,$pre_name.$key.'.',$pre_title.$value['title'].'.');
			}else{
				//数值型数组，找到遍历该数组的字符串，拿出来整理再放回去
				$start_str="【遍历【{$pre_title}{$value['title']}】】";
				$start_len=strlen($start_str);
				$start_index=strpos($template,$start_str);
				$end_str="【结束【{$pre_title}{$value['title']}】】";
				$end_len=strlen($end_str);
				$end_index=strpos($template,$end_str);
				if($start_index>=0 && $end_index>$start_index){
					$sun_template=substr($template,$start_index+$start_len,$end_index-($start_index+$start_len));
					$sun_template=$this->name_replace($value['child'],$sun_template,$pre_name.$key.'.',$pre_title.$value['title'].'.');
					$sun_template="{repeat:{$pre_name}{$key}}".$sun_template."{end:{$pre_name}{$key}}";
					$template=substr($template,0,$start_index).$sun_template.substr($template,$end_index+$end_len);//获取头尾字符串并拼接
				}
			}
		}
		return $template;
	}
	private function value_replace($data,$template,$pre_name=''){
		if(!is_string($template) || $template===''){
			return '';
		}
		if(!is_array($data) || empty($data)){
			return $template;
		}
		foreach($data as $key=>$value){
			if(!is_array($value)){
				//没有下级元素
				$template=str_replace("{{$pre_name}{$key}}",$value,$template);
			}elseif(!$this->isAssocArray($value)){
				//关联数组
				$template=$this->value_replace($value,$template,$pre_name.$key.'.');
			}else{
				//数值型数组
				$start_str="{repeat:{$pre_name}{$key}}";
				$start_len=strlen($start_str);
				$start_index=strpos($template,$start_str);
				$end_str="{end:{$pre_name}{$key}}";
				$end_len=strlen($end_str);
				$end_index=strpos($template,$end_str);
				if($start_index>=0 && $end_index>$start_index){
					$sun_template=trim(substr($template,$start_index+$start_len,$end_index-($start_index+$start_len)));//去除多余换行符
					$lens=array();
					if(strpos($sun_template,"\n")>0 && strpos($sun_template,'|')>0){
						$temp=explode("\n",$sun_template);
						if(count($temp)==2){
							$lens=explode("|",$temp['0']);
							$sun_template=$temp['1'];
						}
					}
					$temp=array();
					foreach($value as $sun_value){
						$temp[]=$this->value_replace($sun_value,$sun_template,$pre_name.$key.'.');
					}
					if(!empty($lens)){
						foreach($temp as &$string){
							$row=explode("|",$string);
							$string='';
							foreach($row as $key=>$field){
								$len=$lens[$key]+(strlen($field)-mb_strlen($field,'utf-8'))/2;
								$string.=str_pad($field,$len);
							}
						}
					}
					$template=substr($template,0,$start_index).implode("\n",$temp).substr($template,$end_index+$end_len);//获取头尾字符串并拼接
				}
			}
		}
		return $template;
	}
	//判断是否为数值型数组
	private function isAssocArray($arr){
        if(!is_array($arr)){
			return false;
		}
		if(!isset($arr[0])){
			return false;
		}
		$arr=array_keys($arr);
		$index = 0;
        foreach ($arr as $key){
            if ($index++ != $key) return false;
        }
        return true;
    }
}
