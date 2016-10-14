<?php
/*
|--------------------------------------------------------------------------
| 节点模型
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：2016-04-12
| updatename：zgt
*/
namespace Common\Model;

use Common\Model\SystemModel;

class NodeModel extends SystemModel
{

    protected $nodeDb;

    public function _initialize()
    {
    }

    /*
     * 获取所有节点内容-缓存
     * @author zgt
     * @return array
     */
    public function getAllNode($where = '', $field = '*', $order = 'sort ASC', $join = null)
    {
        if (F('Cache/node/node')) {
            $nodeAll = F('Cache/node/node');
        } else {
            $nodeAll = $this->where($where)->field($field)->join($join)->order($order)->select();
            //数组分级
            $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
            $nodeAll = $Arrayhelps->createTree($nodeAll, 0, 'id', 'pid');
            F('Cache/node/node', $nodeAll);
        }
        return $nodeAll;
    }

    /**
     * 获取所有节点内容
     * @author zgt
     * @return array
     */
    public function getAllNode2($where = '', $field = '*', $order = 'sort ASC', $join = null)
    {
        $nodeAll = $this->where(array('id'=>$this->id))->field($field)->join($join)->order($order)->select();

        return $nodeAll;
    }

    /*
     * 返回html 多级复选内容
     * @author zgt
     * @return array
     */
    public function getAllNodeHtml()
    {
        if (F('Cache/node/node')) {
            $nodeAll = F('Cache/node/node');
        } else {
            $nodeAll = $this->getAllNode();
        }
        $nodeAll_html = '';
        foreach ($nodeAll as $k => $v) {
            $nodeAll_html .=
                "<tr id='node-{$v['id']}' class=' collapsed '>
                    <td style='padding-left: 30px;'>
                        &nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='node_id[]' value='{$v['id']}' pid='0' level='0' class='radio radio-node-{$v['id']}'  onclick='javascript:checknode(this);' autocomplete='off'> {$v['title']} ({$v['name']})</td>
                </tr>";
            if (!empty($v['children'])) {
                foreach ($v['children'] as $k2 => $v2) {
                    $nodeAll_html .=
                        "<tr id='node-{$v2['id']}' class='tr lt child-of-node-{$v2['pid']}  collapsed ui-helper-hidden'>
                            <td style='padding-left: 49px;'>
                                &nbsp;&nbsp;&nbsp;&nbsp;├─
                                <input type='checkbox' name='node_id[]' value='{$v2['id']}' class='radio radio-node-{$v2['id']}' pid='{$v2['pid']}' level='1'  onclick='javascript:checknode(this);' autocomplete='off'> {$v2['title']} ({$v2['name']})</td>
                        </tr>";
                    if (!empty($v2['children'])) {
                        foreach ($v2['children'] as $k3 => $v3) {
                            $nodeAll_html .=
                                "<tr id='node-{$v3['id']}' class='tr lt child-of-node-{$v3['pid']} ui-helper-hidden'>
                                    <td style='padding-left: 68px;'>&nbsp;&nbsp;&nbsp;&nbsp;│ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─
                                        <input type='checkbox' name='node_id[]' value='{$v3['id']}' class='radio radio-node-{$v3['id']}' pid='{$v3['pid']}' level='2'  onclick='javascript:checknode(this);' autocomplete='off'> {$v3['title']} ({$v3['name']})</td>
                                </tr>";
                            if (!empty($v3['children'])) {
                                foreach ($v3['children'] as $k4 => $v4) {
                                    $nodeAll_html .=
                                        "<tr id='node-{$v4['id']}' class='tr lt child-of-node-{$v4['pid']} ui-helper-hidden'>
                                            <td style='padding-left: 68px;'>&nbsp;&nbsp;&nbsp;&nbsp;│ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─
                                                <input type='checkbox' name='node_id[]' value='{$v4['id']}' class='radio radio-node-{$v4['id']}' pid='{$v4['pid']}' level='3'  onclick='javascript:checknode(this);' autocomplete='off'> {$v4['title']} ({$v4['name']})</td>
                                        </tr>";
                                }
                            }
                        }
                    }
                }
            }
        }
        return $nodeAll_html;
    }

    /**
     * 获取指定id 和 字段的节点信息
     * @param $id
     * @param string $field
     * @return mixed
     */
    public  function  getNodeInfo($id, $field="*"){

         $parentData = $this->where("id=".$id)->field($field)->select();
         return $parentData;
    }

}