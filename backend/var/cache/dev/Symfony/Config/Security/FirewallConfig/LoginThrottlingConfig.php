<?php

namespace Symfony\Config\Security\FirewallConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class LoginThrottlingConfig 
{
    private $limiter;
    private $maxAttempts;
    private $interval;
    private $lockFactory;
    private $_usedProperties = [];
    
    /**
     * A service id implementing "Symfony\Component\HttpFoundation\RateLimiter\RequestRateLimiterInterface".
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function limiter($value): static
    {
        $this->_usedProperties['limiter'] = true;
        $this->limiter = $value;
    
        return $this;
    }
    
    /**
     * @default 5
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function maxAttempts($value): static
    {
        $this->_usedProperties['maxAttempts'] = true;
        $this->maxAttempts = $value;
    
        return $this;
    }
    
    /**
     * @default '1 minute'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function interval($value): static
    {
        $this->_usedProperties['interval'] = true;
        $this->interval = $value;
    
        return $this;
    }
    
    /**
     * The service ID of the lock factory used by the login rate limiter (or null to disable locking)
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function lockFactory($value): static
    {
        $this->_usedProperties['lockFactory'] = true;
        $this->lockFactory = $value;
    
        return $this;
    }
    
    public function __construct(array $config = [])
    {
        if (array_key_exists('limiter', $config)) {
            $this->_usedProperties['limiter'] = true;
            $this->limiter = $config['limiter'];
            unset($config['limiter']);
        }
    
        if (array_key_exists('max_attempts', $config)) {
            $this->_usedProperties['maxAttempts'] = true;
            $this->maxAttempts = $config['max_attempts'];
            unset($config['max_attempts']);
        }
    
        if (array_key_exists('interval', $config)) {
            $this->_usedProperties['interval'] = true;
            $this->interval = $config['interval'];
            unset($config['interval']);
        }
    
        if (array_key_exists('lock_factory', $config)) {
            $this->_usedProperties['lockFactory'] = true;
            $this->lockFactory = $config['lock_factory'];
            unset($config['lock_factory']);
        }
    
        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }
    
    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['limiter'])) {
            $output['limiter'] = $this->limiter;
        }
        if (isset($this->_usedProperties['maxAttempts'])) {
            $output['max_attempts'] = $this->maxAttempts;
        }
        if (isset($this->_usedProperties['interval'])) {
            $output['interval'] = $this->interval;
        }
        if (isset($this->_usedProperties['lockFactory'])) {
            $output['lock_factory'] = $this->lockFactory;
        }
    
        return $output;
    }

}
