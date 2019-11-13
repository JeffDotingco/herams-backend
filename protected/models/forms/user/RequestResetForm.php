<?php


namespace prime\models\forms\user;


use prime\models\ar\User;
use SamIT\Yii2\UrlSigner\UrlSigner;
use yii\base\Model;
use yii\caching\CacheInterface;
use yii\mail\MailerInterface;
use yii\validators\EmailValidator;
use yii\validators\RequiredValidator;

class RequestResetForm extends Model
{
    public $email;

    /** @var CacheInterface  */
    private $cache;

    public function __construct(CacheInterface $cache, $config = [])
    {
        $this->cache = $cache;
        parent::__construct($config);
    }

    public function send(
        MailerInterface $mailer,
        UrlSigner $urlSigner
    ): bool {
        $user = $this->getUser();
        $params = [
            '/user/reset-password',
            'id' => $user->id,
            'crc' => crc32($user->password_hash),
        ];
        $urlSigner->signParams($params, false, \Carbon\Carbon::now()->addHours(4));

        $mailer->compose('password_reset', [
            'user' => $this,
            'resetRoute' => $params

        ])
            ->setTo($this->email)
            ->setSubject(\Yii::t('app', "Collecthor password reset"))
            ->send()
        ;
        $this->cache->set(__CLASS__ . $this->email, time() + 120);
        return true;
    }
    public function rules()
    {
        return [
            [['email'], RequiredValidator::class],
            [['email'], EmailValidator::class],
            [['email'], function() {
                $lastAttempt = $this->cache->get(__CLASS__ . $this->email);
                if ($lastAttempt > time()) {
                    $this->addError('email',\Yii::t('app', "Too many attempts, try again in {seconds} seconds", [
                        'seconds' => $lastAttempt - time()
                    ]));
                }
                if (!User::find()->andWhere(['email' => $this->email])->exists()) {
                    $this->addError('email',"This user is not known or not yet verified");
                }
            }]
        ];
    }

    public function getUser(): User
    {
        return User::findOne(['email' => $this->email]);
    }

    public function attributeHints()
    {
        return [
            'email' => \Yii::t('app', 'We will send you a secure link to the reset form')
        ];
    }
}