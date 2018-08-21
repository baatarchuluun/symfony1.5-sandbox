<?php
/**
 * Created by PhpStorm.
 * User: Baatarchuluun Altanbayar
 * Date: 1/20/2016
 * Time: 9:06 PM
 */

class ApiMail
{
    const api_key = 'iYpgACJu5CP4racvlT6x_g';

    /**
     * Mandrill-ийг ашиглаж и-мэйл илгээх функц
     *
     * @param $subject
     * @param $body
     * @param $to_user
     * @param $from_email
     * @param $from_name
     * @param $reply_to
     * @param string $type
     * @return int
     * @throws Exception
     * @throws Mandrill_Error
     */
    public static function sendInstant($subject, $body, $to_user, $from_email, $from_name, $reply_to, $type = '')
    {
        try {
            $api_key = self::api_key;
            $mandrill = new Mandrill($api_key);
            $message = array(
                'html' => $body,
                'text' => '',
                'subject' => $subject,
                'from_email' => $from_email,
                'from_name' => $from_name,
                'to' => array(
                    array(
                        'email' => $to_user->getEmail(),
                        'name' => $to_user->getFirstname(),
                        'type' => 'to'
                    )
                ),
                'headers' => array('Reply-To' => $reply_to),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => null,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                //'bcc_address' => 'message.bcc_address@example.com',
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
                'global_merge_vars' => array(
                    array(
                        'name' => 'merge1',
                        'content' => 'merge1 content'
                    )
                ),
                'tags' => array('password-resets'),

            );
            $async = false;
            $ip_pool = 'Main Pool';
            $send_at = 'example send_at';
            $result = $mandrill->messages->send($message, $async, $ip_pool);

            if (count($result))
            {
                try {
                    if (self::writeLog($subject, $body, $to_user, $result, $type))
                    {
                        return 1;
                    }
                } catch (Exception $ex) {

                }

                return 0;
            }

        } catch(Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
            throw $e;
        }
    }

    /**
     * И-мэйлийн лог бичих функц
     *
     * @param $subject
     * @param $body
     * @param $to_user
     * @param $result
     * @param $type
     * @return int
     */
    public static function writeLog($subject, $body, $to_user, $result, $type)
    {
        $result_array = $result[0];
        $result_json = json_encode($result);

        $log = new EmailLog();
        $log->setToUser($to_user);
        $log->setToEmail($result_array['email']);
        $log->setSentAt(date("Y-m-d H:i:s"));
        if (sfContext::getInstance()->getUser()->getId())
        {
            $log->setSentBy(sfContext::getInstance()->getUser()->getId());
        }
        $log->setType($type);
        $log->setStatus($result_array['status']);
        $log->setResponseJson($result_json);
        $log->setSubject($subject);
        $log->setBody($body);
        $log->save();

        return 1;
    }
}