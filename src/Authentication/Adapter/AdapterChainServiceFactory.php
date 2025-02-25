<?php

declare(strict_types=1);

namespace LmcUser\Authentication\Adapter;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Authentication\Adapter\AdapterChain;
use LmcUser\Authentication\Adapter\Exception\OptionsNotFoundException;
use LmcUser\Options\ModuleOptions;
use Psr\Container\ContainerInterface;

use function is_callable;

class AdapterChainServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $chain = new AdapterChain();
        $chain->setEventManager($container->get('EventManager'));

        $options = $this->getOptions($container);

        //iterate and attach multiple adapters and events if offered
        foreach ($options->getAuthAdapters() as $priority => $adapterName) {
            $adapter = $container->get($adapterName);

            if (is_callable([$adapter, 'authenticate'])) {
                $chain->getEventManager()->attach('authenticate', [$adapter, 'authenticate'], $priority);
            }

            if (is_callable([$adapter, 'logout'])) {
                $chain->getEventManager()->attach('logout', [$adapter, 'logout'], $priority);
            }
        }

        return $chain;
    }

    /** @var ModuleOptions */
    protected $options;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->__invoke($serviceLocator, null);
    }

    /**
     * set options
     *
     * @return AdapterChainServiceFactory
     */
    public function setOptions(ModuleOptions $options): static
    {
        $this->options = $options;
        return $this;
    }

    /**
     * get options
     *
     * @param ServiceLocatorInterface $serviceLocator (optional) Service Locator
     * @return ModuleOptions $options
     * @throws OptionsNotFoundException If options tried to retrieve without being set but no SL was provided
     */
    public function getOptions(?ServiceLocatorInterface $serviceLocator = null)
    {
        if (! $this->options) {
            if (! $serviceLocator) {
                throw new OptionsNotFoundException(
                    'Options were tried to retrieve but not set '
                    . 'and no service locator was provided'
                );
            }

            $this->setOptions($serviceLocator->get('lmcuser_module_options'));
        }

        return $this->options;
    }
}
