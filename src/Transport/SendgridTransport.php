<?php
namespace Sichikawa\LaravelSendgridDriver\Transport;

use GuzzleHttp\ClientInterface;
use Illuminate\Mail\Transport\Transport;
use Swift_Attachment;
use Swift_Mime_Message;
use Swift_MimePart;

class SendgridTransport extends Transport
{
    const MAXIMUM_FILE_SIZE = 7340032;
    const SMTP_API_NAME = 'sendgrid/x-smtpapi';

    private $client;
    private $options;

    public function __construct(ClientInterface $client, $api_key)
    {
        $this->client = $client;
        $this->options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $payload = $this->options;

        $data = [
            'personalizations' => $this->getPersonalizations($message),
            'from'             => $this->getFrom($message),
            'subject'          => $message->getSubject(),
            'content'          => $this->getContents($message),
        ];

        $attachments = $this->getAttachments($message);
        if (count($attachments) > 0) {
            $data['attachments'] = $attachments;
        }

        $payload['json'] = $data;
        return $this->client->post('https://api.sendgrid.com/v3/mail/send', $payload);
    }

    /**
     * @param Swift_Mime_Message $message
     * @return array
     */
    private function getPersonalizations(Swift_Mime_Message $message)
    {
        $setter = function (array $addresses) {
            $recipients = [];
            foreach ($addresses as $email => $name) {
                $address = [];
                $address['email'] = $email;
                if ($name) {
                    $address['name'] = $name;
                }
                $recipients[] = $address;
            }
            return $recipients;
        };

        $personalizatioin['to'] = $setter($message->getTo());

        if ($cc = $message->getCc()) {
            $personalizatioin['cc'] = $setter($cc);
        }

        if ($bcc = $message->getBcc()) {
            $personalizatioin['bcc'] = $setter($bcc);
        }

        return [$personalizatioin];
    }

    /**
     * Get From Addresses.
     *
     * @param Swift_Mime_Message $message
     * @return array
     */
    private function getFrom(Swift_Mime_Message $message)
    {
        if ($message->getFrom()) {
            foreach ($message->getFrom() as $email => $name) {
                return ['email' => $email, 'name' => $name];
            }
        }
        return [];
    }

    /**
     * Get contents.
     *
     * @param Swift_Mime_Message $message
     * @return array
     */
    private function getContents(Swift_Mime_Message $message)
    {
        $content = [];
        foreach ($message->getChildren() as $attachment) {
            if ($attachment instanceof Swift_MimePart) {
                $content[] = [
                    'type'  => 'text/plain',
                    'value' => $attachment->getBody(),
                ];
                break;
            }
        }

        if (empty($content) || strpos($message->getContentType(), 'multipart') !== false) {
            $content[] = [
                'type'  => 'text/html',
                'value' => $message->getBody(),
            ];
        }
        return $content;
    }

    /**
     * @param Swift_Mime_Message $message
     * @return array
     */
    private function getAttachments(Swift_Mime_Message $message)
    {
        $attachments = [];
        foreach ($message->getChildren() as $attachment) {
            if (!$attachment instanceof Swift_Attachment || !strlen($attachment->getBody()) > self::MAXIMUM_FILE_SIZE) {
                continue;
            }
            $attachments[] = [
                'content'     => base64_encode($attachment->getBody()),
                'filename'    => $attachment->getFilename(),
                'type'        => $attachment->getContentType(),
                'disposition' => $attachment->getDisposition(),
                'content_id'  => $attachment->getId(),
            ];
        }
        return $attachments;
    }

    /**
     * Set Sendgrid SMTP API
     *
     * @param Swift_Mime_Message $message
     */
    protected function setSmtpApi(Swift_Mime_Message $message)
    {
        // TODO
    }

}
