<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
namespace Elf;
abstract class DB {
		protected $msg=array();
		protected $tabName="";
		//protected $fieldList=array();
		public $fieldList=array();
		protected $auto;
		var $path;
		//SQL initialization
		protected $sql=array("field"=>"","where"=>"", "order"=>"", "limit"=>"", "group"=>"", "having"=>"");

		//get table name
		public function __get($pro){
			if($pro=="tabName")
				return $this->tabName;
		}

		//reset member properties
		protected function setNull(){
			$this->sql=array("field"=>"","where"=>"", "order"=>"", "limit"=>"", "group"=>"", "having"=>"");
		}

		//sql chain ( field() where() order() limit() group() having() )
		function __call($methodName, $args){
			$methodName=strtolower($methodName);
			if(array_key_exists($methodName, $this->sql)){
				if(empty($args[0]) || (is_string($args[0]) && trim($args[0])==='')){
					$this->sql[$methodName]="";
				}else{
					$this->sql[$methodName]=$args;
				}

				if($methodName=="limit"){
					if($args[0]=="0")
						$this->sql[$methodName]=$args;
				}	
			}else{
				Debug::addmsg("<font color='red'>调用类".get_class($this)."中的方法{$methodName}()不存在!</font>");
			}
			return $this;
		}

		//get total based on given conditions
		function total(){
			$where="";
			$data=array();
		
			$args=func_get_args();
			if(count($args)>0){
				$where = $this->comWhere($args);
				$data=$where["data"];
				$where= $where["where"];
			}else if($this->sql["where"] != ""){
				$where=$this->comWhere($this->sql["where"]);
				$data=$where["data"];
				$where=$where["where"];
				
			}
	
			$sql="SELECT COUNT(*) as count FROM {$this->tabName}{$where}";
			return $this->query($sql, __METHOD__,$data);			
		}

		//get multi rows,returning a dimensional array
		function select(){
			
			$fields = $this->sql["field"] != "" ?  $this->sql["field"][0] :  implode(",", $this->fieldList);
		
			$where="";
			$data=array();
		
			$args=func_get_args();
			if(count($args)>0){
				$where = $this->comWhere($args);
				$data=$where["data"];
				$where= $where["where"];
			}else if($this->sql["where"] != ""){
				$where=$this->comWhere($this->sql["where"]);
				$data=$where["data"];
				$where=$where["where"];
				
			}
		
		
			$order = $this->sql["order"] != "" ?  " ORDER BY {$this->sql["order"][0]}" : " ORDER BY {$this->fieldList["pri"]} ASC";
			$limit = $this->sql["limit"] != "" ? $this->comLimit($this->sql["limit"]) : "";
			$group = $this->sql["group"] != "" ? " GROUP BY {$this->sql["group"][0]}" : "";
			$having = $this->sql["having"] != "" ? " HAVING {$this->sql["having"][0]}" : "";

		
			$sql="SELECT {$fields} FROM {$this->tabName}{$where}{$group}{$having}{$order}{$limit}";
			return $this->query($sql, __METHOD__,$data);	

		}
		
		//get one row
		function find($pri=""){
			$fields = $this->sql["field"] != "" ?  $this->sql["field"][0] :  implode(",", $this->fieldList);
		
			if($pri==""){
				$where= $this->comWhere($this->sql["where"]) ;
				$data=$where["data"];
				$where = $this->sql["where"] != "" ? $where["where"] : "";		

			}else{
				$where=" where {$this->fieldList["pri"]}=?";  
				$data[]=$pri;
			}
			$order = $this->sql["order"] != "" ?  " ORDER BY {$this->sql["order"][0]}" : "";
			$sql="SELECT {$fields} FROM {$this->tabName}{$where}{$order} LIMIT 1";



  			return $this->query($sql,__METHOD__,$data);
		
		}
		//filter = 1 (remove ",' and html entities) , 0 (don't do anything)
		private function check($array, $filter){
			$arr=array();
		
			foreach($array as $key=>$value){
				$key=strtolower($key);
				if(in_array($key, $this->fieldList) && $value !== ''){
					if(is_array($filter) && !empty($filter)){
						if(in_array($key, $filter)){
							$arr[$key]=$value;	
						}else{
							$arr[$key]=stripslashes(htmlspecialchars($value));
						}
					}else if(!$filter) {
						$arr[$key]=$value;
					}else{
						$arr[$key]=stripslashes(htmlspecialchars($value));
					}
				}	
			}
			return $arr;
		}

		function insert($array=null, $filter=1, $validate=0){
			if(is_null($array))
				$array=$_POST;

			if($validate){
				$vali=Validate::check($array, "add", $this);
			}else{
				$vali=true;	
			}

			if($vali){  
				$array=$this->check($array, $filter);
	
            			$sql = "INSERT INTO {$this->tabName}(".implode(',', array_keys($array)).") VALUES (".implode(',', array_fill(0, count($array), '?')) . ")";

				return $this->query($sql,__METHOD__,array_values($array));
			}else{
				$this->msg=Validate::getMsg();
				return false;
			}
			
		}
		
		function update($array=null, $filter=1, $validate=0){
            if(is_null($array))
				$array=$_POST; 

			if($validate){
				$vali=Validate::check($array, "mod", $this);
			}else{
				$vali=true;	
			}

			if($vali){  
				$data=array();
		      		if(is_array($array)){
					if(array_key_exists($this->fieldList["pri"], $array)){
						$pri_value=$array[$this->fieldList["pri"]];
						unset($array[$this->fieldList["pri"]]);	
			       	 	}

					$array=$this->check($array, $filter); 
       				 	$s = '';
       				 	foreach ($array as $k=>$v) {

					 	$s .="{$k}=?,";
					 	$data[]=$v;
				 	}
				 	$s=rtrim($s, ",");
        				$setfield=$s;
				}else{
					$setfield=$array;
					$pri_value='';
				
				}

		    
				$order = $this->sql["order"] != "" ?  " ORDER BY {$this->sql["order"][0]}" : "";
				$limit = $this->sql["limit"] != "" ? $this->comLimit($this->sql["limit"]) : "";

				if($this->sql["where"] != ""){
					$where=$this->comWhere($this->sql["where"]);
					$sql="UPDATE  {$this->tabName} SET {$setfield}".$where["where"];
					
					if(!empty($where["data"])) {
						foreach($where["data"] as $v){
							$data[]=$v;
						}
					}
					$sql.=$order.$limit;
				}else{
				
					$sql="UPDATE {$this->tabName} SET {$setfield}  WHERE {$this->fieldList["pri"]}=?";
					$data[]=$pri_value;
				}

				return $this->query($sql,__METHOD__,$data);	
			}else{
				$this->msg=Validate::getMsg();
				return false;
			}
		
		}
		
		function delete(){
			$where="";
			$data=array();
			
			$args=func_get_args();
			if(count($args)>0){
				$where = $this->comWhere($args);
				$data=$where["data"];
				$where= $where["where"];
			}else if($this->sql["where"] != ""){
				$where=$this->comWhere($this->sql["where"]);
				$data=$where["data"];
				$where=$where["where"];
				
			}

			$order = $this->sql["order"] != "" ?  " ORDER BY {$this->sql["order"][0]}" : "";
			$limit = $this->sql["limit"] != "" ? $this->comLimit($this->sql["limit"]) : "";
			
			if($where=="" && $limit==""){
				$where=" where {$this->fieldList["pri"]}=''";
			}
			

			$sql="DELETE FROM {$this->tabName}{$where}{$order}{$limit}";
		
			return $this->query($sql, __METHOD__,$data);
		}
	
		private function comLimit($args){
			if(count($args)==2){
				return " LIMIT {$args[0]},{$args[1]}";
			}else if(count($args)==1){
				return " LIMIT {$args[0]}";
			}else{
				return '';
			}	
		}
		
		private function comWhere($args){
			$where=" WHERE ";
			$data=array();
			
			if(empty($args))
				return array("where"=>"", "data"=>$data);
	
			foreach($args as $option) {
				if(empty($option)){
					$where = '';
					continue;
				}else if(is_string($option)){
			       	 	if (is_numeric($option[0])) {
						$option = explode(',', $option); //3
						$where .= "{$this->fieldList["pri"]} IN(" . implode(',', array_fill(0, count($option), '?')) . ")";
						$data=$option;
						
					} else {
						$where .= $option; //2
						
						if(is_array($args[1])) {
							$data = array_values($args[1]);
						}
					}	

					return array("where"=>$where, "data"=>$data);
				}else if(is_numeric($option)){
					$where .="{$this->fieldList["pri"]}=?";   //1
					$data[0]=$option;
					continue;
				}else if(is_array($option)){
					if (isset($option[0])) {
						$where .= "{$this->fieldList["pri"]} IN(" . implode(',', array_fill(0, count($option), '?')) . ")";
						$data=$option;
						continue;
        				}
					
					
					foreach($option as $k => $v ){
          				if (is_array($v)) {
							$where .= "{$k} IN(" . implode(',', array_fill(0, count($v), '?')) . ")";					
							foreach($v as $val){
								$data[]=$val;
							}
						} else if (strpos($k, ' ')) {               						 
							$where .= "{$k}?";
							$data[]=$v;
           				} else if (isset($v[0]) && $v[0] == '%' && substr($v, -1) == '%') {
							 $where .= "{$k} LIKE ?";
							 $data[]=$v;
               			} else {
							$where .= "{$k}=?";
							$data[]=$v;
                		}
						$where.=" AND ";
					}
				
					$where =rtrim($where, "AND ");
					$where.=" OR ";
					continue;
				}
			}
			$where=rtrim($where, "OR ");
		
			return array("where"=>$where, "data"=>$data);
		}
  		
		protected function escape_string_array($array){
			if(empty($array))
				return array();
		 	$value=array();
			foreach($array as $val){
				$value[]=str_replace(array('"', "'"), '', $val);
			}
		 	return $value;
		 }

		 protected function sql($sql, $params_arr){		 	     
			if (false === strpos($sql, '?') || count($params_arr) == 0) return $sql;
        	if (false === strpos($sql, '%')) {
           		$sql = str_replace('?', "'%s'", $sql);
				array_unshift($params_arr, $sql);
            	return call_user_func_array('sprintf', $params_arr);
        	}
		 }

		 function r_select(){
			 $args=func_get_args();
			 if(count($args)==0 || !is_array($args[0]))
				 return false;

			 $one=$this->select();
			 $pri=$this->fieldList["pri"];
			 $pris=array();
			 
			 foreach($one as $row){
			
			 	$pris[]=$row[$pri];
			 }
			 
		

			foreach($args as $tab) {

				 list($tabName, $field, $fk)=$tab;

				 if(!empty($field)){
					if(!in_array($fk, explode(",", $field))){
						 $field=$field.",".$fk;	
					}else{
						$field=$field;
			 		 }
				 }else{
					$field='';
				 }	 
	 			 
				 if(!empty($tab[3])) {

					 $sub=$tab[3];
				
					 if(is_array($sub)) {
						
						 $obj=D($tabName);
	 					 $new=array();
						 foreach($one as $row){
							 //$where=array($fk=>$row[$pri]);
							 $where="{$fk}={$row[$pri]}";
							 if(!empty($sub[3])){
							 	$where.=" AND {$sub[3]}";
							 }

							 if(!empty($sub[1])){
								if(!empty($sub[2])){
									$row[$sub[0]]=$obj->field($field)->order($sub[1])->limit($sub[2])->where($where)->select();
								}else{
									$row[$sub[0]]=$obj->field($field)->order($sub[1])->where($where)->select();
								}
							 }else{
								 if(!empty($sub[2])){
							 
									 $row[$sub[0]]=$obj->field($field)->limit($sub[2])->where($where)->select();
								 }else{
									 $row[$sub[0]]=$obj->field($field)->where($where)->select();
								 }
							}
							 $new[]=$row;					 
						 }

					 	$one=$new;
					 
					 }else {
						 $new=array();
						 $npris=array();
			 		
					
						 foreach($one as $row){
			
			 				$npris[]=$row[$sub];
						 }
						
						$where=array($fk=>$npris);
				 
				 		if(!empty($where[$fk])) {	 
							$data=D($tabName)->field($field)->where($where)->select();
							$i=0;
							foreach($one as $row){
							
								foreach($data as $read){
								
									if($read[$fk]==$row[$sub]){
									
										foreach($read as $k3=>$v3) {
											if(array_key_exists($k3, $row)) {
												$row[$tabName.'_'.$k3]=$v3;
											}else{
												$row[$k3]=$v3;
											}
										}
										$new[$i]=$row;
										break;
												
									}
								}

								if(empty($new[$i])){
									$new[$i]=$one[$i];
								}
							
								$i++;							
							}
							$one=$new;
							
						}
					 }
				
				 }else {
					$new=array();
					
					$where=array($fk=>$pris);
				
				 	if(!empty($where[$fk])) {	 
						$data=D($tabName)->field($field)->where($where)->select();
							
						foreach($data as $row){
							foreach($one as $read){
								if($read[$pri]==$row[$fk]){
									foreach($row as $k3=>$v3) {
										if(array_key_exists($k3, $read)) {
											$read[$tabName.'_'.$k3]=$v3;
										}else{
											$read[$k3]=$v3;
										}
									}

					 				$new[]=$read;	
					 			}
				 			}
			 			}
						$one=$new;
					}
			    	 }
			
			 }
			 return $new;
		 }

		 function r_delete(){
			 $args=func_get_args();

			 if(count($args)==0 || !is_array($args[0]))
				 return false;

			 $one=$this->select();
			 $pri=$this->fieldList["pri"];
			 $pris=array();
			 
			 foreach($one as $row){
			
			 	$pris[]=$row[$pri];
			 }

			 $affected_rows=0;
			 foreach($args as $tab) {
 				 
				 $where=array($tab[1]=>$pris);

				
				 if(!empty($tab[2]))
					 $where=array_merge($where, $tab[2]);


				 if(!empty($where[$tab[1]]))
					$affected_rows+=D($tab[0])->where($where)->delete();
			 }

			
			 $affected_rows+=$this->where($pris)->delete();

			 return $affected_rows;
		 }

		 function setMsg($mess){
			 if(is_array($mess)){
				 foreach($mess as $one){
					 $this->msg[]=$one;
				 }
			 }else{
			 	$this->msg[]=$mess;
			 }
		 
		 }

		 function getMsg(){
		 	$str='';

			foreach($this->msg as $msg){
				$str.=$msg.'<br>';
			}
			return $str;
		 }

		abstract function query($sql, $method,$data=array());
		abstract function setTable($tabName);
		abstract function beginTransaction();
		abstract function commit();
		abstract function rollBack();
		abstract function dbSize();
		abstract function dbVersion();
	}
