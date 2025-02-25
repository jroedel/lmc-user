<?php

namespace LmcUser\Controller;

use Laminas\Mvc\Application;
use Laminas\Router\RouteInterface;
use Laminas\Router\Exception;
use Laminas\Http\PhpEnvironment\Response;
use LmcUser\Options\ModuleOptions;

/**
 * Buils a redirect response based on the current routing and parameters
 */
class RedirectCallback
{

    /** @var RouteInterface  */
    private $router;

    /** @var Application */
    private $application;

    /** @var ModuleOptions */
    private $options;

    /**
     * @param Application $application
     * @param RouteInterface $router
     * @param ModuleOptions $options
     */
    public function __construct(Application $application, RouteInterface $router, ModuleOptions $options)
    {
        $this->router = $router;
        $this->application = $application;
        $this->options = $options;
    }

    /**
     * @return Response
     */
    public function __invoke()
    {
        $routeMatch = $this->application->getMvcEvent()->getRouteMatch();
        $redirect = $this->getRedirect($routeMatch->getMatchedRouteName(), $this->getRedirectRouteFromRequest());

        /** @var Response $response */
        $response = $this->application->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $redirect);
        $response->setStatusCode(302);
        return $response;
    }

    /**
     * Return the redirect from param.
     * First checks GET then POST
     * @return string
     */
    private function getRedirectRouteFromRequest()
    {
        $request  = $this->application->getRequest();
        $redirect = $request->getQuery('redirect');
        if ($redirect && ($this->routeExists($redirect) || $this->urlExists($redirect))) {
            return urldecode($redirect);
        }

        $redirect = $request->getPost('redirect');
        if ($redirect && ($this->routeExists($redirect) || $this->urlExists($redirect))) {
            return urldecode($redirect);
        }

        return false;
    }

    /**
     * @param $route
     * @return bool
     */
    private function routeExists($route)
    {
        try {
            $this->router->assemble(array(), array('name' => $route));
            return true;
        } catch (Exception\RuntimeException $e) {
            return false;
        }
    }

    /**
     * @param $route
     * @return bool
     */
    private function urlExists($route)
    {
        try {
            $request = $this->application->getRequest();
            $request->setUri($route);
            $this->router->match($request);
            return true;
        } catch (Exception\RuntimeException $e) {
            return false;
        }
    }

    /**
     * Returns the url to redirect to based on current route.
     * If $redirect is set and the option to use redirect is set to true, it will return the $redirect url.
     *
     * @param string $currentRoute
     * @param bool $redirect
     * @return mixed
     */
    protected function getRedirect($currentRoute, $redirect = false)
    {
        $useRedirect = $this->options->getUseRedirectParameterIfPresent();
        $routeExists = ($redirect && ($this->routeExists($redirect) || $this->urlExists($redirect)));

        if (!$useRedirect || !$routeExists) {
            $redirect = false;
        }

        switch ($currentRoute) {
            case 'lmcuser/register':
            case 'lmcuser/login':
            case 'lmcuser/authenticate':
                $route = ($redirect) ?: $this->options->getLoginRedirectRoute();
                return $this->assembleUrl($route);
            case 'lmcuser/logout':
                $route = ($redirect) ?: $this->options->getLogoutRedirectRoute();
                return $this->assembleUrl($route);
            default:
                return $this->router->assemble(array(), array('name' => 'lmcuser'));
        }
    }
    
    /**
     * @param $route
     * @return bool|mixed
     */
    protected function assembleUrl($route)
    {
        try {
            return $this->router->assemble(array(), array('name' => $route));
        } catch (Exception\RuntimeException $e) {
            //This route matches already to an url, so return it of no route by name
            return $route;
        }
    }
}
