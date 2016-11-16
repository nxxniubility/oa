<?php
/*
|--------------------------------------------------------------------------
| 分页类
|--------------------------------------------------------------------------
| createtime：2016-04-21
| updatetime：2016-04-21
| updatename：zgt
*/
namespace  Extend\TagLib;
use Think\Template\TagLib;

class Paging{
    /**
     *
     * page:页码  count:总记录数  shownum:每页显示录数  url:开头  urlhtml:url前半段/结尾 type:分页类型
     * @author nxx
     */
    public $page=null,$count=null,$shownum=null,$url=null,$urlhtmll=null,$urlhtml=null,$pagecount=null,$type=null;

    /**
     * 分页生成
     * $page =$_get['']
     * @author nxx
     */
    public function createPaging(){
        if($this->count==0) return null;
        $this->pagecount=ceil($this->count/$this->shownum);//最大页数
        $create_data = $this->typePaging($this->type);
        return $create_data;
    }

    /*
     * 分页风格
     * @author nxx
     */
    protected function typePaging($type){
        switch ($type)
        {
            case 'system':
                $list = $this->system_type();
                break;
            case 'system_userlist':
                $list = $this->systemUserList_type();
                break;
        }
        return $list;
    }

	/*
     * 后台分页
     * @author nxx
	 * 每页显示信息（n/条）:{$this->shownum}
     */
    protected function systemUserList_type(){
        $create_data =
            "<div class='r clearfix'>";
        $create_data.=
            "<div class='pageCount'>
                    <div>共有<em>{$this->count}</em>条/<em>{$this->pagecount}</em>页，</div>
                    <div>每页显示：<select class='page-shownum' style='width: 58px; padding-right: 4px; padding-left: 7px;'>
                    <option ".($this->shownum==30?'selected="selected"':'')." value='30'>30</option>
                    <option ".($this->shownum==50?'selected="selected"':'')." value='50'>50</option>
                    <option ".($this->shownum==100?'selected="selected"':'')." value='100'>100</option>
                    </select>条</div>
                </div>";
        $create_data.= "<div class='pageNum'>";
        //上一页
        $prev_page = $this->page-1;
        if($this->page!=1) $create_data.="<a class='prev' href='{$this->url}{$prev_page}{$this->urlhtml}'>&lt;</a>";
        //页码
        for($i=1;$i<=$this->pagecount;$i++){
            if($i>$this->page-3 && $i<$this->page+3){
                if($this->page==$i){
                    $create_data.="<span class='current'>{$this->page}</span>";
                }else{
                    $create_data.="<a class='num' href='{$this->url}{$i}{$this->urlhtml}'>{$i}</a>";
                }
            }
        }
        //下一页
        $next_page = $this->page+1;
        if($this->page!=$this->cont && $this->page<$this->pagecount) $create_data.="<a class='next' href='{$this->url}{$next_page}{$this->urlhtml}'>&gt;</a>";
        $create_data.= "</div>";

        $create_data.=
            "<div class='pageJump'>
                    <input type='tel' class='jumpNum' maxlength='4' id='paging_text'>
                    <a href='javascript:;' class='jumpBtn' onclick='paging();'>GO</a>
                </div><script>
                function paging(){var paging_page = document.getElementById(\"paging_text\").value;if(paging_page!=''){location.href=\"".$this->url."\"+paging_page+\"".$this->urlhtml."\";}; };
                $(document).on('change','.page-shownum',function(){location.href=\"".$this->url."\"+1+'&pagenum='+$(this).val()+\"".$this->urlhtml."\";});
                </script>";

        $create_data .= "</div>";
        return $create_data;
    }

    /*
     * 后台分页
     * @author nxx
     */
    protected function system_type(){
        $create_data =
            "<div class='r'>";
        $create_data.=
            "<div class='pageCount'>
                    <span>共有<em>{$this->count}</em>条/<em>{$this->pagecount}</em>页，</span>
                    <span>每页显示：<i>{$this->shownum}</i>条</span>
                </div>";
        $create_data.= "<div class='pageNum'>";
        //上一页
        $prev_page = $this->page-1;
        if($this->page!=1) $create_data.="<a class='prev' href='{$this->url}{$prev_page}{$this->urlhtml}'>&lt;</a>";
        //页码
        for($i=1;$i<=$this->pagecount;$i++){
            if($i>$this->page-3 && $i<$this->page+3){
                if($this->page==$i){
                    $create_data.="<span class='current'>{$this->page}</span>";
                }else{
                    $create_data.="<a class='num' href='{$this->url}{$i}{$this->urlhtml}'>{$i}</a>";
                }
            }
        }
        //下一页
        $next_page = $this->page+1;
        if($this->page!=$this->cont && $this->page<$this->pagecount) $create_data.="<a class='next' href='{$this->url}{$next_page}{$this->urlhtml}'>&gt;</a>";
        $create_data.= "</div>";

        $create_data.=
            "<div class='pageJump'>
                    <input type='tel' class='jumpNum' maxlength='4' id='paging_text'>
                    <a href='javascript:;' class='jumpBtn' onclick='paging();'>GO</a>
                </div><script>function paging(){var paging_page = document.getElementById(\"paging_text\").value;if(paging_page!=''){location.href=\"".$this->url."\"+paging_page+\"".$this->urlhtml."\";}; };</script>";

        $create_data .= "</div>";
        return $create_data;
    }
}
