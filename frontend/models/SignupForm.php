<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $rePassword;
    public $verifyCode;//yii框架自带的验证组建


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' =>
                \Yii::t('common','用户名已存在')],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username','match','pattern'=>
                '/^[(\x{4E00}-\x{9FA5})a-zA-Z]+[(\x{4E00}-\x{9FA5})a-zA-Z_\d]*$/u','message'=>
                '用户名必须由字母、数字、汉字、下划线组成，而且不能以数字、下划线为首字符！！！'],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' =>
                \Yii::t('common','邮箱已存在')],

            [['password','rePassword'], 'required'],
            [['password','rePassword'], 'string', 'min' => 6],
            ['rePassword','compare','compareAttribute'=>'password','message'=>
                \Yii::t('common','两次密码输入不一致')],
            ['verifyCode','captcha']
        ];
    }

    //注册页语言包
    public function attributeLabels()
    {
        return
            [
                'username'=>'用户名',
                'email'=>'邮箱',
                'password'=>'密码',
                'rePassword'=>'重复密码',
                'verifyCode'=>'验证码'

            ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        return $user->save() ? $user : null;
    }
}
