<?php
namespace frontend\models;
use common\models\TagModel;
use yii\base\Model;
use Yii;

//取消标签功能栏
class TagForm extends Model
{
    public $id;

    public  $tags;

    public function rules()
    {
        return
        [
          ['tags','required'],
            ['tags','each','rule'=>['string']],
        ];
    }
    //保存标签集合
    public function saveTags()
    {
        $ids = [];
        if(!empty($this->tags))
        {
            foreach ($this->tags as $tag)
            {
             $ids[] = $this->_saveTag($tag);
            }
        }
        return $ids;
    }

    //保存标签
    private function _saveTag($tag)
    {
        $model = new TagModel();
        $res = $model->find()->where(['tag_name'=>$tag])->one();
        if(!$res)
        {
            $model->tag_name = $tag;
            $model->post_num = 1;
            //新建标签
            if (!$model->save())
            {
                throw new \Exception("保存标签失败");


            }else
                {
                   $res->updateCounters(['post_num' => 1]);
                }
        }
        return $res->id;
    }
}