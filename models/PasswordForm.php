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
                ['newpass','string','min'=>6,'tooShort'=>'كلمة السر الجديدة يجب أن تكون 6 أحرف على الأقل'],
                ['repeatnewpass','compare','compareAttribute'=>'newpass','message'=>'تأكيد كلمة السر غير مطابق'],
            ];
        }
        
        public function findPasswords($attribute){
            $user = User::findOne(Yii::$app->user->id);
            if(!$user || !$user->validatePassword($this->oldpass))
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