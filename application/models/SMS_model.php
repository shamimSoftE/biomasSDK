<?php
class SMS_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->getSettings();
    }

    private $smsEnabled = "false";
    private $apiKey = "";
    private $url = "";
    private $bulkUrl = "";
    private $url2 = "";
    private $bulkUrl2 = "";
    private $senderId = "";
    private $senderId2 = "";
    private $userId = "";
    private $password = "";
    private $countryCode = "";
    private $smsType = "";
    private $senderName = "";
    private $senderPhone = "";

    private function getSmsFooter()
    {
        return "\n\nThank you,\n{$this->senderName}\nPhone: {$this->senderPhone}";
    }

    public function getSettings()
    {
        $query = $this->db->query("select * from tbl_sms_settings");
        if ($query->num_rows() == 0) {
            $this->smsEnabled = 'false';
            return;
        }

        $settings           = $query->row();
        $this->smsEnabled   = $settings->sms_enabled;
        $this->apiKey       = $settings->api_key;
        $this->url          = $settings->url;
        $this->bulkUrl      = $settings->bulk_url;
        $this->url2         = $settings->url_2;
        $this->bulkUrl2     = $settings->bulk_url_2;
        $this->smsType      = $settings->sms_type;
        $this->senderId     = $settings->sender_id;
        $this->senderId2    = $settings->sender_id_2;
        $this->userId       = $settings->user_id;
        $this->password     = $settings->password;
        $this->countryCode  = $settings->country_code;
        $this->senderName   = $settings->sender_name;
        $this->senderPhone  = $settings->sender_phone;
    }

    public function sendSms($recipient, $message)
    {
        if ($this->smsEnabled == 'false') {
            return false;
        }
        $recipient = trim($recipient);
        $smsText = $message . $this->getSmsFooter();

        if ($this->smsEnabled == 'gateway1') {
            $url = $this->url;
             $postData = array(
                "UserName" => "ceo@linktechbd.com",
                "Apikey" => $this->apiKey,
                "MobileNumber" => "88{$recipient}",
                "SenderName" => $this->senderId,
                "TransactionType" => "T",
                "Message" => $smsText
            );
        } else {
            $url = $this->url2;
            $postData = array(
                "user" => $this->userId,
                "sender" => $this->senderId2,
                "pwd" => $this->password,
                "CountryCode" => $this->countryCode,
                "mobileno" => $recipient,
                "msgtext" => $smsText
            );
        }

        // Initialize cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function sendBulkSms($recipients, $message)
    {
        if ($this->smsEnabled == 'false') {
            return false;
        }
        $smsText = urldecode($message);

        if ($this->smsEnabled == 'gateway1') {
            $url = $this->bulkUrl;

            $mobileNumbers = array_map(function($item) {
                $recipient = trim($item);
                return "88{$recipient}";
            }, $recipients);
            
            $numbers = implode(',', array_values($mobileNumbers));

            $postData = array(
                "UserName" => "ceo@linktechbd.com",
                "Apikey" => $this->apiKey,
                "MobileNumber" => $numbers,
                "SenderName" => $this->senderId,
                "TransactionType" => "T",
                "Message" => $smsText
            );
        } else {
            $url = $this->bulkUrl2;
            $recipient = implode(",", array_map('trim', $recipients));

            $postData = array(
                "UserName" => "ceo@linktechbd.com",
                "Apikey" => $this->apiKey,
                "MobileNumber" => $numbers,
                "SenderName" => $this->senderId,
                "TransactionType" => "T",
                "Message" => $smsText
            );
        }

        // Initialize cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
