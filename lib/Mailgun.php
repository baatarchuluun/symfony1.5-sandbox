<?php
require_once 'Mailgun/autoload.php';
use Mailgun\Mailgun;

class MailgunEmail {
	
	public $api_key = 'key-7bb055b21c39e17f04fb9329cda89c5a'; // Barilgachin.mn домэйныг холбосны дараа API KEY авах шаардлагатай.
	public $domain = "mg.barilgachin.mn"; // barilgachin.mn домэйныг холбох шаардлагатай

    /**
     * @param $subject - Мэйлийн гарчиг
     * @param $body - Мэйлийн агуулга
     * @param $to_name - Хэрэглэгчийн нэр
     * @param $to_user - Илгээх имэйл
     * @param $cc_email - cc имэйл
     * @param string $type - Мэйлийн төрөл
     * @return int 1 - success, 2 - failed, 3 - log not written
     * @throws \Mailgun\Messages\Exceptions\MissingRequiredMIMEParameters
     */
    public function send($subject, $body, $to_name, $to_email, $cc_email, $type = '')
    {
        $client = new \Http\Adapter\Guzzle6\Client();
        $mailgun = new \Mailgun\Mailgun($this->api_key, $client);


        if ($cc_email != '')
        {
            $result = $mailgun->sendMessage($this->domain,
                array(
                    'from'    => sfConfig::get('app_from_name').' <'.sfConfig::get('app_from_email').'>',
                    'to'      => $to_name.' <'.$to_email.'>',
                    'cc'      => $cc_email,
                    'subject' => $subject,
                    'html'    => $body
                )
            );
        }
        else
        {
            $result = $mailgun->sendMessage($this->domain,
                array(
                    'from'    => sfConfig::get('app_from_name').' <'.sfConfig::get('app_from_email').'>',
                    'to'      => $to_name.' <'.$to_email.'>',
                    'subject' => $subject,
                    'html'    => $body
                )
            );
        }

        if ($result->http_response_code == 200)
        {
            try {
                $to_user = UserTable::getInstance()->findOneBy('email', $to_email);
                if ($to_user)
                {
                    if (MailgunEmail::writeLog($subject, $body, $to_user, $result, $type))
                    {
                        return 1;
                    }
                }
            } catch (Exception $ex) {
                return 3;
            }
        }
        else
        {
            return 2;
        }
    }

    public static function writeLog($subject, $body, $to_user, $result, $type)
    {
        $log = new EmailLog();
        $log->setToUser($to_user);
        $log->setToEmail($to_user->getEmail());
        $log->setSentAt(date("Y-m-d H:i:s"));
        if (sfContext::getInstance()->getUser()->getId())
        {
            $log->setSentBy(sfContext::getInstance()->getUser()->getId());
        }
        $log->setType($type);
        $log->setStatus($result->http_response_code);
        $log->setResponseJson($result->http_response_body->id.': '.$result->http_response_body->message);
        $log->setSubject($subject);
        $log->setBody($body);
        $log->save();

        return 1;
    }
}