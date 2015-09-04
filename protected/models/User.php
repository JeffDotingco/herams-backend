<?php 
    namespace app\models;
    class User extends \Befound\ActiveRecord\ActiveRecord implements \yii\web\IdentityInterface {
        
        public function getAuthKey() {
            return $this->authKey;
        }

        public function getId() {
            return $this->id;
        }

        public function validateAuthKey($authKey) {
            return $this->authKey === $authKey;
        }

        public static function findIdentity($id) {
            return static::findOne($id);
        }

        public static function findIdentityByAccessToken($token) {
            return static::findOne(['access_token' => $token]);
        }

}
?>