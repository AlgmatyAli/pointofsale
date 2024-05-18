<?php 
    namespace app\models;
    
    use Yii;
    use yii\base\Model;
    
    
    class PasswordForm extends Model{
        public $oldpass;
        public $newpass;
        public $repeatnewpass;
        
        public function rules(){
            return [
                [['oldpass','newpass','repeatnewpass'],'required'],
                ['oldpass','findPasswords'],
                // ['repeatnewpass','compare','compareAttribute'=>'newpass'],
            ];
        }
        
        public function findPasswords($attribute){
            $user = User::find()->where([
                'username'=>Yii::$app->user->identity->username
            ])->one();
            $password = $user->password;
            if($password!=md5($this->oldpass))
                $this->addError($attribute,'كلمة السر القديمة غير صحيحة');
        }
        
        public function attributeLabels(){
            return [
                'oldpass'=>Yii::t('app','Old Password'),
                'newpass'=>Yii::t('app','New Password'),
                'repeatnewpass'=>Yii::t('app','Repeat New Password'),
            ];
        }
    }