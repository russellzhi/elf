<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
namespace Elf;
	class Mytpl {
		
		public $template_dir;
		public $compile_dir;   
		public $left_delimiter  =  '<{';
		public $right_delimiter =  '}>';
		private $tpl_vars = array();    

		public function __construct(){
			$this->template_dir=APP_PATH.'views/'.TPLSTYLE;
			$this->compile_dir=PROJECT_PATH."runtime/comps/".TPLSTYLE."/".TMPPATH;
		}

		function assign($tpl_var, $value = null) {   
			if ($tpl_var != '')                   
				$this->tpl_vars[$tpl_var] = $value;
		}
		
        function display($fileName=null) { 
			$this->assign("root", B_ROOT);
			$this->assign("app",B_APP);
			$this->assign("url", B_URL);
			$this->assign("public", B_PUBLIC);
			$this->assign("res", B_RES);
			$this->assign("lang", $GLOBALS["lang"]);
			
			$this->assign("get",$_GET);
			$this->assign("post",$_POST);
			$this->assign("cookie",$_COOKIE);
			$this->assign("session",$_SESSION);

			if(is_null($fileName)){
				$fileName=str_replace('Controller','',$_GET['m'])."/{$_GET["a"]}.".TPLSUFFIX;
			}else if(strstr($fileName,"/")){
				$fileName=$fileName.".".TPLSUFFIX;
			}else{
				$fileName=str_replace('Controller','',$_GET['m'])."/".$fileName.".".TPLSUFFIX;
			}
			
			$tplFile = $this->template_dir.'/'.$fileName;  
			if(!file_exists($tplFile)) {               	
				die("模板文件{$tplFile}不存在！");
			}
			
			$compagename=str_replace('/','_',$fileName);
			$comFileName = $this->compile_dir."com_".$compagename.'.php';  
            
			if(!file_exists($comFileName) || filemtime($comFileName) < filemtime($tplFile)) {
				$repContent = $this->tpl_replace(file_get_contents($tplFile));  
				file_put_contents($comFileName, $repContent);
			}
			include($comFileName);		      	
		}

		private function tpl_replace($content) {

			$left = preg_quote($this->left_delimiter, '/');
			$right = preg_quote($this->right_delimiter, '/');

			$pattern = array(       
				/* "<{ $var }>"  */
				'/'.$left.'\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*'.$right.'/i', 
				'/'.$left.'\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*'.$right.'/i',			
				'/'.$left.'\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*'.$right.'/i',
								
				/* "<{ if $col == "sex" }> <{ /if }>" */
				'/'.$left.'\s*if\s*(.+?)\s*'.$right.'(.+?)'.$left.'\s*\/if\s*'.$right.'/ies', 
				/* "<{ elseif $col == "sex" }>" */
				'/'.$left.'\s*else\s*if\s*(.+?)\s*'.$right.'/ies', 
				/* "<{ else }>" */
				'/'.$left.'\s*else\s*'.$right.'/is',   
				/* "<{ loop $arrs $value }> <{ /loop}>" */
				'/'.$left.'\s*loop\s+\$(\S+)\s+\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*'.$right.'(.+?)'.$left.'\s*\/loop\s*'.$right.'/is',
				/* "<{ loop $arrs $key => $value }> <{ /loop}>"  */
				'/'.$left.'\s*loop\s+\$(\S+)\s+\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*=>\s*\$(\S+)\s*'.$right.'(.+?)'.$left.'\s*\/loop \s*'.$right.'/is', 
				/* '<{ include "header.html" }>' */
				'/'.$left.'\s*include\s+[\"\']?(.+?)[\"\']?\s*'.$right.'/ie'                	
			);
			
			$replacement = array(  
				/* <?php echo $this->tpl_vars["var"]; */
				'<?php echo $this->tpl_vars["${1}"]; ?>', 
				'<?php echo $this->tpl_vars["${1}"]["${2}"]; ?>', 
				'<?php echo $this->tpl_vars["${1}"]["${2}"]["${3}"]; ?>', 
				
				/*  <?php if($col == "sex") { ?> <?php } ?> */
				'$this->stripvtags(\'<?php if(${1}) { ?>\',\'${2}<?php } ?>\')', 	
				/*  <?php } elseif($col == "sex") { ?> */
				'$this->stripvtags(\'<?php } elseif(${1}) { ?>\',"")',  
				/* <?php } else { ?> */
				'<?php } else { ?>',   
				/* foreach */
				'<?php foreach($this->tpl_vars["${1}"] as $this->tpl_vars["${2}"]) { ?>${3}<?php } ?>',  
				'<?php foreach($this->tpl_vars["${1}"] as $this->tpl_vars["${2}"] => $this->tpl_vars["${3}"]) { ?>${4}<?php } ?>',    
				/* include */
				'file_get_contents($this->template_dir."/${1}")'         	
			);
			
			$repContent = preg_replace($pattern, $replacement, $content); 	
			if(preg_match('/'.$left.'([^('.$right.')]{1,})'.$right.'/', $repContent)) {       
				$repContent = $this->tpl_replace($repContent);         	
			} 
			return $repContent;                                   	
		}
		
		private function stripvtags($expr, $statement='') {
			$var_pattern = '/\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*/is'; 
			$expr = preg_replace($var_pattern, '$this->tpl_vars["${1}"]', $expr); 
			$expr = str_replace("\\\"", "\"", $expr);
			$statement = str_replace("\\\"", "\"", $statement); 
			return $expr.$statement;                         	
		}
	}

