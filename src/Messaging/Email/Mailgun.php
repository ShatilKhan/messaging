<?php

namespace Messaging\Email;

class Mailgun extends EmailAdapter
{
    /**
     * @param string $apiKey Your Mailgun API key to authenticate with the API.
     * @param string $domain Your Mailgun domain to send messages from.
     */
    public function __construct(
        private string $apiKey,
        private string $domain,
    )
    {
    }

    public function getName(): string
    {
        return 'Mailgun';
    }

    public function getMaxMessagesPerRequest(): int
    {
        return 1000;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function sendMessage(EmailMessage $message): string
    {
        return $this->request(
            method: 'POST',
            url: "https://api.mailgun.net/v3/{$this->domain}/messages",
            headers: [
                'Authorization: Basic ' . base64_encode('api:' . $this->apiKey)
            ],
            body: [
                'from' => $message->getFrom(),
                'to' => \join(',', $message->getTo()),
                'subject' => $message->getSubject(),
                'text' => $message->isHtml() ? null : $message->getContent(),
                'html' => $message->isHtml() ? $message->getContent() : null,
            ],
        );
    }
}
