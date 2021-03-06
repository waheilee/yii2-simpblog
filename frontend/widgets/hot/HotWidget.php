<?php
namespace frontend\widgets\hot;
/*
 * 热门浏览组件
 */
use common\models\PostExtendsModel;
use common\models\PostModel;
use Yii;
use yii\bootstrap\Widget;
use yii\db\Query;

class HotWidget extends Widget
{
    public $title = '';

    public  $limit = 6;

    public function run()
    {
        $res = (new Query())
            ->select('a.browser,b.id,b.title')->from(['a'=>PostExtendsModel::tableName()])
            ->join('LEFT JOIN',['b'=>PostModel::tableName()],'a.post_id = b.id')
            ->where('b.is_valid ='.PostModel::IS_VALID)
            
            ->limit($this->limit)
            ->all();
        $result['title'] = $this->title?:'热门浏览';
        $result['body'] = $res?:[];
        return $this->render('index',['data'=>$result]);
    }

}