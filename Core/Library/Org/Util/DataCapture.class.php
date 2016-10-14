<?php
namespace Org\Util;
/**
 * 数据抓取
 * @author longguojun
 */
class DataCapture{
	//url 验证规则
    protected $RegExp='/^(http(s)?:\/\/)?([\w-]+\.)+[\w-]+\.((com)|(cn)|(com\.cn)|(net)|(cc)|(xyz)|(org)|(org\.cn)|(site)|(pw)|(info)|(vip)|(xin)|(club)|(win)|(top)|(wang))(\/[\w- .\/?%&=]*)?$/';
    //抓取公司配置
    protected $company_config = array();
    //抓取职位配置
    protected $job_config = array();
    //已被抓取的公司url 唯一标示
    protected $company_url_arr = array();
    //已被抓取的职位url唯一标示
    protected $job_url_arr = array();
    //memcache
    protected $memcache;

    /**
     * 构造函数
     * @param array  $company_config 公司匹配配置
     * @param array  $job_config     职位匹配配置
     * @param string $memcache       [description]
     */
    public	function __construct($company_config = array(),$job_config = array(),$memcache=''){
		header("Content-type: text/html; charset=utf-8");
    	//memcache
        if($memcache) $this->memcache = $memcache;
        //公司匹配配置
        if(!$company_config) exit('公司匹配配置数据错误');
        $this->company_config = $company_config;
        $is_exists = M()->query('SHOW TABLES LIKE \''.$this->company_config['db_name'].'\'');
        if(!$is_exists){
            if(false === M()->execute($this->company_config['db_create'])){
                exit('公司抓取数据表创建失败');
            }
        }else{
            $this->company_url_arr = $this->memcache ? $this->memcache->get('company_url_arr') : array();
            if(!$this->company_url_arr){
                $table = str_replace(C('DB_PREFIX'), '', $this->company_config['db_name']);
                $this->company_url_arr = D($table)->getField($this->company_config['unique_label']['name'],true);
                if($this->memcache)
                    $this->memcache->set('company_url_arr',$this->company_url_arr,0,86400);
            }
        }
        //职位匹配配置
        if(!$job_config) exit('职位匹配配置数据错误');
        $this->job_config = $job_config;
        $is_exists = M()->query('SHOW TABLES LIKE \''.$this->job_config['db_name'].'\'');
        if(!$is_exists){
            if(false === M()->execute($this->job_config['db_create'])){
                exit('职位抓取数据表创建失败');
            }
        }else{
            $this->job_url_arr = $this->memcache ? $this->memcache->get('job_url_arr') : array();
            if(!$this->job_url_arr){
                $table = str_replace(C('DB_PREFIX'), '', $this->job_config['db_name']);
                $this->job_url_arr = D($table)->getField($this->job_config['unique_label']['name'],true);
                if($this->memcache)
                    $this->memcache->set('job_url_arr',$this->job_url_arr,0,86400);
            }
        }
	}

    /**
     * 公司数据抓取
     * @param string  $url            公司列表页地址
     * @param boolean $is_capture_job 是否抓取公司对应的职位
     * @param integer $page           公司列表页当前页数
     */
    public function CompanyCapture($url,$page=1,$is_capture_job=true)
    {
        //设置脚本最大执行时间 
        set_time_limit(0);
        //返回信息 
        $return = array();
        //抓取的公司数、职位数
        $company_count = $job_count = 0;
        //配置的数据表
        $table = str_replace(C('DB_PREFIX'), '', $this->company_config['db_name']);
        $model = D($table);
        //获取公司列表页分页
        $url = $page <= 1 ? $url : str_replace('(*)',$page, $url.$this->company_config['capture_rule']);
        //获取网页内容
        $result = $this->fetch($url);
        if(!$result) return false;
        //获取分页总数
        $return['max_page'] = preg_match($this->company_config['page_rule'], $result,$match) ? $match[1] : 1;
        /*//判断是否有分页
        if($page == 1){
           if(preg_match($this->company_config['page_rule'], $result,$match)){
                $return['max_page'] = $match[1];
            }else{
                $return['max_page'] = 1;
            }
        }*/
        // 获取公司列表list
        if(!preg_match($this->company_config['list_rule'],$result,$list_arr)) return false;
        if(!$list_arr[1]) return false;
        //获取公司链接
        preg_match_all($this->company_config['link_rule'],$list_arr[1],$link_arr);

        if($link_arr[1]){
            //遍历获取公司详细页内容
            foreach ($link_arr[1] as $key => $value) {
                //获取公司内容页
                $company_content = $this->fetch($value);
                if(!$company_content) continue;
                //获取链接url 唯一标示
                if(!preg_match($this->company_config['unique_label']['rule'], $value,$int)) continue;
                $zp_company_url = $int[1];
                //判断是否已抓取
                if(in_array($zp_company_url, $this->company_url_arr)) continue;//已抓取
                //入库数组
                $data = array(
                    $this->company_config['unique_label']['name']=>$zp_company_url
                );
                //返回信息名
                $return_info_name = '';
                //获取采集字段匹配规则
                foreach ($this->company_config['rules'] as $k => $v) {
                    //配置项默认值
                    if($v['default']){
                        $data[$k] = $v['default'];
                        continue;
                    }
                    if($v['rule'] == '') continue;
                    if(preg_match($v['rule'], $company_content,$match)){
                        $data[$k] = $match[1];
                    }elseif($v['rule1'] && preg_match($v['rule1'], $company_content,$match)){
                        $data[$k] = $match[1];
                    }else{
                         $data[$k] = '';
                    }
                    if($v['required']) $return_info_name = $data[$k];
                    //要求不能为空
                    if($data[$k] == '' && $v['required']){
                        continue 2;
                    }
                }
                //过滤
                $data = $this->filterData($this->company_config['filters'],$data);
                //存入数据表
                $lastId = $model->add($data);
                if(false === $lastId){
                    continue;
                }
                $this->company_url_arr[] = $zp_company_url;
                $company_count++;
                //是否抓取该公司职位数据
                if($is_capture_job){
                    $return['info'][$return_info_name] = 0;
                    //获取页面下方的职位列表url
                    $job_total = $this->jobCapture($zp_company_url,$lastId,$ch);
                    $return['info'][$return_info_name] = $job_total;
                    $job_count = $job_total ? $job_count + $job_total : $job_count;
                }
            }//foreach
        }else{
            $return_data = array(
                'code'=>1,
                'msg'=>'没有可采集的数据',
                'data'=>$return
                );
            return IS_AJAX ? json_encode($return_data) : $return_data;
        }
        if($this->memcache){
            $this->memcache->set('companyUrlArr',$this->company_url_arr);
            $this->memcache->set('jobUrlArr',$this->job_url_arr);
        }
        $return['companyCount'] = $company_count;
        $return['jobCount'] = $job_count;
        return array(
            'code'=>0,
            'msg'=>'采集成功',
            'data'=>$return
            );
    }

    /**
     * 职位数据抓取
     * @param  string $company_url 公司url 唯一标示
     * @param  int $company_id  公司的id
     * @return [type]           抓取职位的数量
     */
    public function jobCapture($company_url,$company_id){
        if(!$company_url) return false;
        $job_count = 0;
        //职位列表 最大分页数
        $s = $this->job_config['max_page'];
        $sg = '';
        $max_page = $s;
        $table = str_replace(C('DB_PREFIX'), '', $this->job_config['db_name']);
        $model = D($table);
        for ($i=1; $i <= $s; $i++) { 
            //获取抓取网页的url
            $url = $i <= 1 ? str_replace('(?1)', $company_url, $this->job_config['captrue_url']) : str_replace(array('(?1)','(?2)','(?3)'), array($company_url,$sg,$i), $this->job_config['captrue_rule']);
            //抓取职位列表页
            $job_list_content = $this->fetch($url);
            if(!$job_list_content) return false;
            //获取分页值
            if($i == 1){
               if(preg_match($this->job_config['page_rule'], $job_list_content,$page)){
                    $sg = $page[1];//职位列表分页标示
                    $max_page = $page[2];//分页最大数
                }else{
                    $max_page = 1;
                }
            }
            //最大分页
            if($max_page < $i) break;
            //获取职位列表
            preg_match_all($this->job_config['list_rule'], $job_list_content, $matches);
            // var_dump($matches);
            if(!$matches[1]) continue;
            foreach ($matches[1] as $key => $value) {
                //获取职位详细页链接
                if(preg_match($this->job_config['link_rule'], $value,$match)){
                    $job_content = $this->fetch($match[1]);
                    if(!$job_content) continue;
                    //获取链接url 数字
                    if(!preg_match($this->job_config['unique_label']['rule'], $match[1],$int)) continue;
                    $zp_job_url = $int[1];
                    //判断是否已抓取
                    if(in_array($zp_job_url, $this->job_url_arr)) continue;//已抓取
                    // 入库数据
                    $data = array(
                        $this->job_config['foreign_key']=>$company_id,
                        // 'zp_city'=>$this->job_config['cur_city']['city_id'],
                        $this->job_config['unique_label']['name']=>$zp_job_url
                    );
                    //获取匹配规则
                    foreach ($this->job_config['rules'] as $k => $v) {
                        //配置项默认值
                        if($v['default']){
                            $data[$k] = $v['default'];
                            continue;
                        }
                        if($v['rule'] == '') continue;
                        //规则匹配
                        if(preg_match($v['rule'], $job_content,$match)){
                            $data[$k] = trim($match[1]);
                        }elseif($v['rule1'] && preg_match($v['rule1'], $job_content,$match)){
                            $data[$k] = trim($match[1]);
                        }else{
                            $data[$k] = '';
                        }
                        //去除html php 标签 
                        if($v['strip_tags']){
                            $data[$k] = strip_tags($data[$k]);
                        }
                        //要求不能为空
                        if($data[$k] == '' && $v['required']){
                            continue 2;
                        }
                    }
                    //过滤
                    $data = $this->filterData($this->job_config['filters'],$data);
                    // var_dump($data);die;
                    if(false === $model->add($data)){
                        continue;
                    }
                    $this->job_url_arr[] = $zp_job_url;
                    $job_count++;
                }//if
            }//foreach
        }//for
        return $job_count;
    }

    /**
     *  获取网页内容
     * @param  string $url 抓取URL
     * @return [type]      [description]
     */
    public function fetch($url){
        if(!preg_match($this->RegExp, $url)) return false;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
        $result = curl_exec($ch);
        curl_close($ch);
        if(!$result) return false;
        $result = str_replace(PHP_EOL, '', $result);
        return $result;
    }

    /**
     * 过滤
     * @param  [type] $filter 要过滤的方法名
     * @param  [type] $data   被过滤的数据
     * @return [type]         过滤之后的数据
     */
    protected function array_map_recursive($filter, $data) {
        $result = array();
        foreach ($data as $key => $val) {
            $result[$key] = is_array($val)
             ? $this->array_map_recursive($filter, $val)
             : call_user_func($filter, $val);
        }
        return $result;
    }

    /**
     * 过滤匹配后得到的值
     * @param  [type] $filters 过滤的方法名
     * @param  [type] $data    被过滤的数据
     * @return [type]          过滤之后的数据
     */
    protected function  filterData($filters,$data){
        //过滤
        if($filters) {
            if(is_string($filters)){
                $filters    =   explode(',',$filters);
            }
            foreach($filters as $filter){
                $data   =   $this->array_map_recursive($filter,$data); // 参数过滤
            }
        }
        return $data;
    }
}
