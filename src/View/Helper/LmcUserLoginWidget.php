<?php

declare(strict_types=1);

namespace LmcUser\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Model\ViewModel;
use LmcUser\Form\Login as LoginForm;

use function array_key_exists;

class LmcUserLoginWidget extends AbstractHelper
{
    /**
     * Login Form
     *
     * @var LoginForm
     */
    protected $loginForm;

    /**
     * $var string template used for view
     */
    protected $viewTemplate;
    /**
     * __invoke
     *
     * @access public
     * @param array $options array of options
     * @return string
     */
    public function __invoke($options = [])
    {
        if (array_key_exists('render', $options)) {
            $render = $options['render'];
        } else {
            $render = true;
        }
        if (array_key_exists('redirect', $options)) {
            $redirect = $options['redirect'];
        } else {
            $redirect = false;
        }

        $vm = new ViewModel([
            'loginForm' => $this->getLoginForm(),
            'redirect'  => $redirect,
        ]);
        $vm->setTemplate($this->viewTemplate);
        if ($render) {
            return $this->getView()->render($vm);
        } else {
            return $vm;
        }
    }

    /**
     * Retrieve Login Form Object
     *
     * @return LoginForm
     */
    public function getLoginForm()
    {
        return $this->loginForm;
    }

    /**
     * Inject Login Form Object
     *
     * @return LmcUserLoginWidget
     */
    public function setLoginForm(LoginForm $loginForm): static
    {
        $this->loginForm = $loginForm;
        return $this;
    }

    /**
     * @param string $viewTemplate
     * @return LmcUserLoginWidget
     */
    public function setViewTemplate($viewTemplate): static
    {
        $this->viewTemplate = $viewTemplate;
        return $this;
    }
}
