<?php

declare(strict_types=1);

namespace LmcUser\Authentication\Adapter;

use Laminas\EventManager\Event;
use Laminas\Stdlib\RequestInterface as Request;

class AdapterChainEvent extends Event
{
    protected $request;
    /**
     * getIdentity
     *
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->getParam('identity');
    }

    /**
     * setIdentity
     *
     * @param mixed $identity
     * @return AdapterChainEvent
     */
    public function setIdentity($identity = null): static
    {
        if (null === $identity) {
            // Setting the identity to null resets the code and messages.
            $this->setCode();
            $this->setMessages();
        }
        $this->setParam('identity', $identity);
        return $this;
    }

    /**
     * getCode
     *
     * @return int
     */
    public function getCode()
    {
        return $this->getParam('code');
    }

    /**
     * setCode
     *
     * @param int $code
     * @return AdapterChainEvent
     */
    public function setCode($code = null): static
    {
        $this->setParam('code', $code);
        return $this;
    }

    /**
     * getMessages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->getParam('messages') ?: [];
    }

    /**
     * setMessages
     *
     * @param array $messages
     * @return AdapterChainEvent
     */
    public function setMessages($messages = []): static
    {
        $this->setParam('messages', $messages);
        return $this;
    }

    /**
     * getRequest
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->getParam('request');
    }

    /**
     * setRequest
     *
     * @return AdapterChainEvent
     */
    public function setRequest(Request $request): static
    {
        $this->setParam('request', $request);
        $this->request = $request;
        return $this;
    }
}
