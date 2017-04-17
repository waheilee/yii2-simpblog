<?php
namespace frontend\models;

use common\models\PostModel;
use common\models\RelationPostTagModel;
use Prophecy\Exception\Prediction\NoCallsException;
use yii\base\Model;
use yii\db\Exception;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;


/**
 * 文章表单
 */

class PostForm extends Model
{
    public $id;
    public $title;
    public $content;
    public $label_img;
    public $cat_id;
    public $tags;

    public $_latError="";



    /*
     * 定义场景
     * SCENARIO_CREATE 场景创建
     * SCENARIO_UPDATE场景更新
     */
    const SCENARIOS_CREATE = 'create';
    const SCENARIOS_UPDATE = 'update';
    /*
     * 定义事件
     * EVENT_AFTER_CREATE创建之后的事件
     * EVENT_AFTER_UPDATE更新之后的事件
     */

    const EVENT_AFTER_CREATE ='eventAfterCreate';
    const EVENT_AFTER_UPDATE ='eventAfterUpdate';



    //场景设置

    public function scenarios()
    {
        $scenarios =
            [
              self::SCENARIOS_CREATE=> ['title','content','label_img','cat_id','tags'],
              self::SCENARIOS_UPDATE=> ['title','content','label_img','cat_id','tags']
            ];
        return array_merge(parent::scenarios(),$scenarios);
    }


   //文章的rules规则
    public function rules()
    {
        return
        [
            [['id','title','content','cat_id'],'required'],

            [['id','cat_id'],'integer'],

            ['title','string','min'=>4,'max'=>100],
        ];
    }

    public function attributeLabels()
    {
        return
            [
              'id'=>'编码',    //页可以使用用语言包\Yii::t()
              'title'=>'标题',
              'content'=>'内容',
              'label_img'=>'标签图',
              'tags'=>'标签',
              'cat_id'=>'分类',
            ];
    }

    public static function getList($cond,$curPage = 1,$pageSiz = 5,$orderBy = ['id'=>SORT_DESC])
    {
        $model = new PostModel();
        //查询语句
        $select = [
                    'id','title','summary','user_id',
                    'label_img','cat_id','user_name',
                    'is_valid','created_at','updated_at'
                  ];
        $query = $model->find()->select($select)
            ->where($cond)->with('extend')->orderBy($orderBy);

        //获取分页数据
        $res = $model->getPages($query,$curPage,$pageSiz);

        //格式化分页数据
        $res['data'] = self::_formatList($res['data']);
        return $res;
    }


    //数据格式化
    public static function _formatList($data)
    {
        foreach($data as &$list)
        {
            $list['tags'] = [];
            if(isset($list['relate'])&&!empty($list['relate']))
            {
                foreach ($list['relate'] as $lt)
                {
                    $list['tags'][] = $lt['tag']['tag_name'];
                }
            }
            unset($list['relate']);
        }
        return $data;
    }


    //文章创建
    public function create()
    {
        //事物的引用后传递给数据库
        $transaction = \Yii::$app->db->beginTransaction();
        try
        {   $model = new PostModel();
            $model->setAttributes($this->attributes);
            $model->summary = $this->_getSummary();
            $model->user_id = Yii::$app->user->identity->id;
            $model->user_name = Yii::$app->user->identity->username;
            $model->is_valid = PostModel::IS_VALID;
            $model->created_at = time();
            $model->updated_at = time();
            if (!$model->save())
                throw new \Exception('文件保存失败');
            $this->id = $model->id;

            //调用事件
            $data = array_merge($this->getAttributes(),$model->getAttributes());
            $this->_eventAfterCreate($data);

            $transaction->commit();
            return true;

        }catch (\Exception $e)
        {
            $transaction->rollBack();
            $this->_latError = $e->getMessage();

            return false;
        }
    }

    public function getViewById($id)
    {
        $res = PostModel::find()->with('extend')->where(['id'=>$id])->asArray()->one();

        if (!$res)
        {
            throw new NotFoundHttpException("文章不存在");
        }
        return $res;
    }


    //截取文章摘要
    private function _getSummary($s = 0,$e = 90,$char = 'utf-8')
    {
        if(empty($this->content))
            return null;
        return(mb_substr(str_replace('&nbsp','',strip_tags($this->content)),$s,$e,$char));
    }


    //创建完成后调用事件方法
    public function _eventAfterCreate($data)
    {
        //添加事件,比如积分，标签等等
        $this->on(self::EVENT_AFTER_CREATE,[$this,'_eventAddTag'],$data);
        //触发事件
        $this->trigger(self::EVENT_AFTER_CREATE);

    }
    //添加标签
    public function _eventAddTag($event)
    {
        // 保存标签
        $tag = new TagForm();
        $tag->tags = $event->data['tags'];
        $tagids = $tag->saveTags();

        //删除原先的关系
        RelationPostTagModel::deleteAll(['post_id'=>$event->data['id']]);

        //批量保存文章和标签的关联关系
        //判断标签是否为空
        if (!empty($tagids))
        {
            foreach ($tagids as $k=>$id)
            {
                $row[$k]['post_id'] = $this->id;
                $row[$k]['tag_id'] = $id;
            }
            //批量插入
            $res = (new Query())->createCommand()
                ->batchInsert(RelationPostTagModel::tableName(),['post_id','tag_id'],$row)
                ->execute();
            //如果失败，返回结果
            if (!$res)
                throw new\Exception("关联关系保存失败");
        }
    }




}