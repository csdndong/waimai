<?php
 
class page
{
    private $nowpage = 0;
    private $pagesize = 20;
    private $pagenum = 0;
    
    public function setnum($num)
    {
    	$this->pagenum = $num;
    }
    
    public function setpage($nowpage,$pagesize=0)
    {
    	$this->nowpage = $nowpage > 0?$nowpage-1:$nowpage;
    	$this->pagesize = $pagesize == 0? $this->pagesize:$pagesize;
    }
    
    public function startnum()
    {
    	return $this->nowpage*$this->pagesize;
    }
    public function getsize()
    {
    	return $this->pagesize;
    } 
    public function totalpage()
    {
    	 $pagejisuan = intval($this->pagenum/$this->pagesize);//整页面计算 
       $pageyu = ($this->pagenum/$this->pagesize)-$pagejisuan;//计算余额
       $pagenum = $pageyu > 0?$pagejisuan+1:$pagejisuan;//计算完毕  
       return $pagenum;
    }
    
    public function getpagebar($url='')
    {
    	 $pagecontents = '';
    	 if($url == '')
    	 {
    	 	  $url = IUrl::getUri();
    	 } 
       $pagenum = $this->totalpage(); 
       $lookpage = $this->nowpage+1; 
       $is_static = Mysite::$app->config['is_static'];
       if($is_static ==  3){
       	 $url =  preg_replace('#&page=(\d+)#','',$url);
       }else{
          $url =  preg_replace('#/page/(\d+)#','',$url);
       }
       if($lookpage > 1){
         $uppage = $lookpage-1;
         $pagecontents = $is_static == 3 ? $pagecontents.'<a href="'.$url.'&page='.$uppage.'"><上一页</a>':$pagecontents.'<a href="'.$url.'/page/'.$uppage.'"><上一页</a>';
       } 
       if($pagenum < 10){
        for($i = 1;$i < $pagenum+1;$i++)
        {
         	$k= $i+1;
         	if($i == 0)
         	{
         		if( $lookpage == 0)
         		{
         			$pagecontents = $pagecontents.'<a href="#" class="current">1</a>';
         		}else{
         		   $pagecontents = $is_static == 3 ? $pagecontents.'<a href="'.$url.'&page='.$i.'">1</a>':$pagecontents.'<a href="'.$url.'/page/'.$i.'">1</a>';
         	  } 
         	}else{
         		
         	if( $lookpage == $i )
         	{		
         	   $pagecontents = $pagecontents.'<a href="#" class="current" >'.$i.'</a>';
         	    
           }else{
           	$pagecontents = $is_static == 3 ? $pagecontents.'<a href="'.$url.'&page='.$i.'">'.$i.'</a>':$pagecontents.'<a href="'.$url.'/page/'.$i.'">'.$i.'</a>';
           }
          }
        } 
     }else{
     	 for($i = 1;$i < 4;$i++)
        {
         	$k= $i+1;
         	if($i == 0)
         	{
         		if( $lookpage == 0)
         		{
         			$pagecontents = $pagecontents.'<a href="#"  class="current">1</a>';
         		}else{
         		   $pagecontents = $is_static == 3 ? $pagecontents.'<a href="'.$url.'&page='.$i.'">1</a>':$pagecontents.'<a href="'.$url.'/page/'.$i.'">1</a>';
         	  } 
         	}else{
         		
         	if( $lookpage == $i )
         	{		
         	   $pagecontents = $pagecontents.'<a href="#"  class="current">'.$i.'</a>';
         	    
           }else{
           	$pagecontents = $is_static == 3 ? $pagecontents.'<a href="'.$url.'&page='.$i.'">'.$i.'</a>':$pagecontents.'<a href="'.$url.'/page/'.$i.'">'.$i.'</a>';
           }
          }
        } 
        if($lookpage > 2 && $lookpage <  $pagenum){
        	$startpage = $lookpage > 7 ? $lookpage-3:4;
        	$checkpage = $lookpage + 3;
        	$dosumpage = $pagenum - 3;
        	$endpage = $checkpage > $dosumpage ? $dosumpage-3:$checkpage;
        for($i = $startpage;$i < $endpage+3;$i++)
        {
         	$k= $i+1;
         	if($i == 0)
         	{
         		if( $lookpage == 0)
         		{
         			$pagecontents = $pagecontents.'<a href="#"  class="current">1</a>';
         		}else{
         		   $pagecontents = $is_static == 3 ? $pagecontents.'<a href="'.$url.'&page='.$i.'">1</a>':$pagecontents.'<a href="'.$url.'/page/'.$i.'">1</a>';
         	  } 
         	}else{
         		
         	if( $lookpage == $i )
         	{		
         	   $pagecontents = $pagecontents.'<a href="#"  class="current">'.$i.'</a>';
         	    
           }else{
           	$pagecontents = $is_static == 3 ? $pagecontents.'<a href="'.$url.'&page='.$i.'">'.$i.'</a>':$pagecontents.'<a href="'.$url.'/page/'.$i.'">'.$i.'</a>';
           }
          }
        } 
      }
        for($i = $pagenum-3;$i < $pagenum+1;$i++)
        {
         	$k= $i+1;
         	if($i == 0)
         	{
         		if( $lookpage == 0)
         		{
         			$pagecontents = $pagecontents.'<a href="#"  class="current">1</a>';
         		}else{
         		   $pagecontents = $is_static == 3 ? $pagecontents.'<a href="'.$url.'&page='.$i.'">1</a>':$pagecontents.'<a href="'.$url.'/page/'.$i.'">1</a>';
         	  } 
         	}else{
         		
         	if( $lookpage == $i )
         	{		
         	   $pagecontents = $pagecontents.'<a href="#"  class="current">'.$i.'</a>';
         	    
           }else{
           	$pagecontents = $is_static == 3 ? $pagecontents.'<a href="'.$url.'&page='.$i.'">'.$i.'</a>':$pagecontents.'<a href="'.$url.'/page/'.$i.'">'.$i.'</a>';
           }
          }
        } 
     	
     }
     if($lookpage <  $pagenum){
         $uppage = $lookpage+1;
         $pagecontents = $is_static == 3 ? $pagecontents.'<a href="'.$url.'&page='.$uppage.'">下一页></a>':$pagecontents.'<a href="'.$url.'/page/'.$uppage.'">下一页></a>';
       } 
     $pagecontents .='<a href="#">共'.$pagenum.'页</a>';
       return $pagecontents;

    }
   
   /*
    ajaxbar：获取AJAX翻页
    样式：
     <ul>
         
        <li class="active"><a href="javascript:getPingjia(1);">1</a></li>
        <li><a href="javascript:getPingjia(2);">2</a></li>
        <li><a href="javascript:getPingjia(2);">下一页</a></li>
    </ul>
    param  $functionname 定义的函数
    */
   
    public function ajaxbar($functionname)
    {
    	  
       $pagenum = $this->totalpage(); 
       $lookpage = $this->nowpage+1;  
       $pagecontents = '<ul>';
       if($lookpage > 1)
       	  {
       	  	$pagecontents .= '<li><a href="javascript:'.$functionname.'('.$this->nowpage.');">上一页</a></li>';
       	  }else{
       	  	$pagecontents .= '<li class="disabled"><a href="javascript:'.$functionname.'(1);">上一页</a></li>';
       	  }
     if($pagenum < 8){  	  
       for($i = 1;$i < $pagenum+1;$i++)
       {
       	  //构造上页  
       	  
         	$k= $i+1;
         	if($i == 0)
         	{
         		if( $lookpage == 0)
         		{
         			$pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'(1);">1</a></li>';
         		}else{
         		  $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'(1);">1</a></li>';
         	  }
         		
         		 
         	}else{
         		
         	if( $lookpage == $i )
         	{		
         	     $pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
         	    
           }else{
           	   $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
           }
          }
          
          //构造末尾页
          
       } 
     }else{
     	  $checkpage  =  $lookpage +3;
     	   if($checkpage < 7){
     	   	 for($i = 1;$i < 6;$i++)
           {
           	  //构造上页  
           	  
             	$k= $i+1;
             	if($i == 0)
             	{
             		if( $lookpage == 0)
             		{
             			$pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'(1);">1</a></li>';
             		}else{
             		  $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'(1);">1</a></li>';
             	  }
             		
             		 
             	}else{
             		
             	if( $lookpage == $i )
             	{		
             	     $pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
             	    
               }else{
               	   $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
               }
              } 
           } 
           $znumpage = $pagenum - 3;
           for($i = $znumpage;$i < $pagenum+1;$i++)
           {
           	  //构造上页  
           	  
             	$k= $i+1;
             	if($i == 0)
             	{
             		if( $lookpage == 0)
             		{
             			$pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'(1);">1</a></li>';
             		}else{
             		  $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'(1);">1</a></li>';
             	  }
             		
             		 
             	}else{
             		
             	if( $lookpage == $i )
             	{		
             	     $pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
             	    
               }else{
               	   $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
               }
              } 
           } 
           
     	   	
     	   	
     	   	
     	   }elseif($checkpage > $pagenum){
     	   	for($i = 1;$i < 6;$i++)
           {
           	  //构造上页  
           	  
             	$k= $i+1;
             	if($i == 0)
             	{
             		if( $lookpage == 0)
             		{
             			$pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'(1);">1</a></li>';
             		}else{
             		  $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'(1);">1</a></li>';
             	  }
             		
             		 
             	}else{
             		
             	if( $lookpage == $i )
             	{		
             	     $pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
             	    
               }else{
               	   $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
               }
              } 
           } 
           $znumpage = $pagenum - 3;
           for($i = $znumpage;$i < $pagenum+1;$i++)
           {
           	  //构造上页  
           	  
             	$k= $i+1;
             	if($i == 0)
             	{
             		if( $lookpage == 0)
             		{
             			$pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'(1);">1</a></li>';
             		}else{
             		  $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'(1);">1</a></li>';
             	  }
             		
             		 
             	}else{
             		
             	if( $lookpage == $i )
             	{		
             	     $pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
             	    
               }else{
               	   $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
               }
              } 
           } 
     	   }else{
     	    	for($i = 1;$i < 3;$i++)
           {
           	  //构造上页  
           	  
             	$k= $i+1;
             	if($i == 0)
             	{
             		if( $lookpage == 0)
             		{
             			$pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'(1);">1</a></li>';
             		}else{
             		  $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'(1);">1</a></li>';
             	  }
             		
             		 
             	}else{
             		
             	if( $lookpage == $i )
             	{		
             	     $pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
             	    
               }else{
               	   $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
               }
              } 
           } 
           $starpage = $lookpage-1;
           $endpage = $lookpage+2;
           	for($i = $starpage;$i < $endpage;$i++)
           {
           	  //构造上页  
           	  
             	$k= $i+1;
             	if($i == 0)
             	{
             		if( $lookpage == 0)
             		{
             			$pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'(1);">1</a></li>';
             		}else{
             		  $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'(1);">1</a></li>';
             	  }
             		
             		 
             	}else{
             		
             	if( $lookpage == $i )
             	{		
             	     $pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
             	    
               }else{
               	   $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
               }
              } 
           } 
           $znumpage = $pagenum - 1;
           for($i = $znumpage;$i < $pagenum+1;$i++)
           {
           	  //构造上页  
           	  
             	$k= $i+1;
             	if($i == 0)
             	{
             		if( $lookpage == 0)
             		{
             			$pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'(1);">1</a></li>';
             		}else{
             		  $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'(1);">1</a></li>';
             	  }
             		
             		 
             	}else{
             		
             	if( $lookpage == $i )
             	{		
             	     $pagecontents = $pagecontents.'<li class="active"><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
             	    
               }else{
               	   $pagecontents = $pagecontents.'<li><a href="javascript:'.$functionname.'('.$i.');">'.$i.'</a></li>';
               }
              } 
           } 
     	   }  
     	    
     	
     	
     }
        if($lookpage < $pagenum)
       	  {
       	  	$uppage = $lookpage+1;
       	  	$pagecontents .= '<li><a href="javascript:'.$functionname.'('.$uppage.');">下一页</a></li>';
       	  }else{
       	  	$pagecontents .= '<li class="disabled"><a href="javascript:'.$functionname.'('.$pagenum.');">下一页</a></li>';
       	  }
       return $pagecontents.'</ul>';

    }
	function multi($num, $perpage, $curpage, $mpurl, $maxpages = 0, $page = 10, $autogoto = true, $simple = false) {
		$multipage = '';
		$mpurl .= strpos($mpurl, '?') ? '&amp;' : '?';
		$realpages = 1;
		if ($num > $perpage) {
		$offset = 2;

		$realpages = @ceil($num / $perpage);
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

		if ($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if ($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if ($to - $from < $page) {
					$to = $page;
				} 
			} elseif ($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			} 
		} 
        $ajaxtarget = '';
		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<a href="' . $mpurl . 'page=1" class="first"' . $ajaxtarget . '>1 ...</a>' : '') .
		($curpage > 1 && !$simple ? '<a href="' . $mpurl . 'page=' . ($curpage - 1) . '" class="prev"' . $ajaxtarget . '>&lsaquo;&lsaquo;</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<strong>' . $i . '</strong>' :
			'<a href="' . $mpurl . 'page=' . $i . ($ajaxtarget && $i == $pages && $autogoto ? '#' : '') . '"' . $ajaxtarget . '>' . $i . '</a>';
		} 

		$multipage .= ($curpage < $pages && !$simple ? '<a href="' . $mpurl . 'page=' . ($curpage + 1) . '" class="next"' . $ajaxtarget . '>&rsaquo;&rsaquo;</a>' : '') .
		($to < $pages ? '<a href="' . $mpurl . 'page=' . $pages . '" class="last"' . $ajaxtarget . '>... ' . $realpages . '</a>' : '') .
		(!$simple && $pages > $page && !$ajaxtarget ? '<kbd><input type="text" name="custompage" size="3" onkeydown="if(event.keyCode==13) {window.location=\'' . $mpurl . 'page=\'+this.value; return false;}" /></kbd>' : '');

		$multipage = $multipage ? '</div><DIV id="quicklinks">' . (!$simple ? '<em>&nbsp;' . $num . '&nbsp;</em>' : '') . $multipage . '</div>' : '';
	} 
	$maxpage = $realpages;
	return $multipage;
	} 
    

}