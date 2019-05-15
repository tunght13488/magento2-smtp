<?php

namespace MageWonder\Smtp\Model;

use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterface;

class Transport implements TransportInterface
{
    /**
     * @var \MageWonder\Smtp\Model\Message
     */
    protected $_message;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \MageWonder\Smtp\Helper\Data
     */
    protected $_helper;

    /**
     * @var \MageWonder\Smtp\Model\Api
     */
    protected $_api;

    /**
     * @inheritDoc
     */
    public function __construct(
        MessageInterface $message,
        \Psr\Log\LoggerInterface $logger,
        \MageWonder\Smtp\Helper\Data $helper,
        \MageWonder\Smtp\Model\Api $api
    ) {
        $this->_message = $message;
        $this->_logger = $logger;
        $this->_helper = $helper;
        $this->_api = $api;
    }

    /**
     * @inheritDoc
     */
    public function sendMessage()
    {
        $message = [
            'subject'    => $this->_message->getSubject(),
            'from_name'  => $this->_message->getFromName(),
            'from_email' => $this->_message->getFrom(),
        ];

        foreach ($this->_message->getTo() as $to) {
            $message['to'][] = [
                'email' => $to,
            ];
        }

        foreach ($this->_message->getBcc() as $bcc) {
            $message['to'][] = [
                'email' => $bcc,
                'type'  => 'bcc',
            ];
        }

        if ($att = $this->_message->getAttachments()) {
            $message['attachments'] = $att;
        }

        if ($headers = $this->_message->getHeaders()) {
            $message['headers'] = $headers;
        }

        switch ($this->_message->getType()) {
            case MessageInterface::TYPE_HTML:
                $message['html'] = $this->_message->getBody();
                break;
            case MessageInterface::TYPE_TEXT:
                $message['text'] = $this->_message->getBody();
                break;
        }

        $this->_api->send($message);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->_message;
    }
}
