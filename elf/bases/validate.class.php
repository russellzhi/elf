<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
namespace Elf;
class Validate {
	static $data;
	static $action;
	static $msg;
	static $flag=true;
	static $db=null;

	static function start($xml_parser, $tagName, $args){
		if(isset($args["NAME"]) && isset($args["MSG"])) {
			if(empty($args["ACTION"]) || $args["ACTION"]=="both" || $args["ACTION"]==self::$action) {
				if(is_array(self::$data)) {
					if (array_key_exists($args["NAME"],self::$data)) {
						if(empty($args["TYPE"])){
							$method="regex";
						}else{
							$method=strtolower($args["TYPE"]);
						}
					
						if(in_array($method, get_class_methods(__CLASS__))){
							self::$method(self::$data[$args["NAME"]],$args["MSG"],$args["VALUE"],$args["NAME"]);
						}else{
							self::$msg[]="验证的规则{$args["TYPE"]} 不存在，请检查！<br>";
							self::$flag=false;
						}
				
			
					}else{
						self::$msg[]="验证的字段 {$args["NAME"]} 和表单中的输出域名称不对应<br>";
						self::$flag=false;
					}
				}
			}
		}
	
	}

	static function end($xml_parser, $tagName){
		return true;
	}	

	static function check($data, $action, $db, $tabPrefix){
		$file=substr($db->tabName, strlen($tabPrefix));
	
		$xmlfile=$db->path."models/".$file.".xml";
		if(file_exists($xmlfile)) {
			self::$data=$data;
			self::$action=$action;
			self::$db=$db;
	
			if(is_array($data) && array_key_exists("code", $data)){
				self::vcode($data["code"], "验证码输入<font color='red'>".$data["code"]."</font>错误！");
			}

			$xml_parser = xml_parser_create("utf-8");

			xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
			xml_set_element_handler ($xml_parser, array(__CLASS__,"start"),array(__CLASS__, "end"));
			if (!($fp = fopen($xmlfile, "r"))) {
					die("无法读取XML文件$xmlfile");
			}

			$has_error = false;
			while ($data = fread($fp, 4096)) {
				if (!xml_parse($xml_parser, $data, feof($fp)))
				{
					$has_error = true;
					break;
				}
			}

			if($has_error) { 
				$error_line   = xml_get_current_line_number($xml_parser);
				$error_row   = xml_get_current_column_number($xml_parser);
				$error_string = xml_error_string(xml_get_error_code($xml_parser));

				$message = sprintf("XML文件 {$xmlfile}［第%d行，%d列］有误：%s", 
					$error_line,
					$error_row,
					$error_string);
					self::$msg[]= $message;
					self::$flag=false;
			}
			xml_parser_free($xml_parser);
			return self::$flag;
		}else{
			return true;
		}
			
	}

	static function regex($value, $msg,$rules) {
		if(!preg_match($rules, $value)) {
			self::$msg[]=$msg;
			self::$flag=false;
		}
	}

	static function unique($value,  $msg, $rules, $name) {
		if(self::$db->where("$name='$value'")->total() > 0){
			self::$msg[]=$msg;
			self::$flag=false;
		} 
	}

	static function notnull($value,  $msg) {
		if(strlen(trim($value))==0) {
			self::$msg[]=$msg;
			self::$flag=false;
		}
	}

	static function email($value, $msg) {
		$rules= "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";

		if(!preg_match($rules, $value)) {
			self::$msg[]=$msg;
			self::$flag=false;
		}
	}

	static function url($value, $msg) {

		$rules='/^http\:\/\/([\w-]+\.)+[\w-]+(\/[\w-.\/?%&=]*)?$/';
		if(!preg_match($rules, $value)) {
			self::$msg[]=$msg;
			self::$flag=false;
		}

	}

	static function number($value, $msg) {
	
		$rules='/^\d+$/';
		if(!preg_match($rules, $value)) {
			self::$msg[]=$msg;
			self::$flag=false;
		}
	}

	static function currency($value, $msg) {
	
		$rules='/^\d+(\.\d+)?$/';
		if(!preg_match($rules, $value)) {
			self::$msg[]=$msg;
			self::$flag=false;
		}
	}

	static function vcode($value, $msg){		
		if(strtoupper($value)!=$_SESSION["code"]) {
			self::$msg[]=$msg;
			self::$flag=false;
		}
	}

	static function callback($value, $msg, $rules) {
		if(!call_user_func_array($rules, array($value))) {
			self::$msg[]=$msg;
			self::$flag=false;
		}
	}

	static function confirm($value, $msg, $rules) {
		if($value!=self::$data[$rules]){
			self::$msg[]=$msg;
			self::$flag=false;
		}	
	}

	static function in($value,$msg, $rules) {
		if(strstr($rules, ",")){
			if(!in_array($value, explode(",", $rules))){
				self::$msg[]=$msg;
				self::$flag=false;
			}	
		}else if(strstr($rules, '-')){
			list($min, $max)=explode("-", $rules);

			if(!($value>=$min && $value <=$max) ){
				self::$msg[]=$msg;
				self::$flag=false;
			}
		}else{
			if($rules!=$value){
				self::$msg[]=$msg;
				self::$flag=false;
			}
		}
	}

	static function length($value,$msg, $rules) {
		$fg=strstr($rules, '-') ? "-" : ",";

		if(!strstr($rules, $fg)){
			if(strlen($value) != $rules){
				self::$msg[]=$msg;
				self::$flag=false;
			}
		}else{

			list($min, $max)=explode($fg, $rules);
			
			if(empty($max)){
				if(strlen($value) < $rules){
					self::$msg[]=$msg;
					self::$flag=false;
				}
			}else if(!(strlen($value)>=$min && strlen($value) <=$max) ){
				self::$msg[]=$msg;
				self::$flag=false;
			}
		}
	
	}

	static function getMsg(){
		$msg=self::$msg;
		self::$msg='';
		self::$data=null;
		self::$action='';
		self::$flag=true;
		self::$db=null;
		return $msg;
	}

}
