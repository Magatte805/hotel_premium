<?php

namespace Symfony\Config\Security\FirewallConfig\AccessToken\TokenHandler;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class OidcUserInfoConfig 
{
    private $baseUri;
    private $claim;
    private $client;
    private $_usedProperties = [];
    
    /**
     * Base URI of the userinfo endpoint on the OIDC server.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function baseUri($value): static
    {
        $this->_usedProperties['baseUri'] = true;
        $this->baseUri = $value;
    
        return $this;
    }
    
    /**
     * Claim which contains the user identifier (e.g. sub, email, etc.).
     * @default 'sub'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function claim($value): static
    {
        $this->_usedProperties['claim'] = true;
        $this->claim = $value;
    
        return $this;
    }
    
    /**
     * HttpClient service id to use to call the OIDC server.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function client($value): static
    {
        $this->_usedProperties['client'] = true;
        $this->client = $value;
    
        return $this;
    }
    
    public function __construct(array $config = [])
    {
        if (array_key_exists('base_uri', $config)) {
            $this->_usedProperties['baseUri'] = true;
            $this->baseUri = $config['base_uri'];
            unset($config['base_uri']);
        }
    
        if (array_key_exists('claim', $config)) {
            $this->_usedProperties['claim'] = true;
            $this->claim = $config['claim'];
            unset($config['claim']);
        }
    
        if (array_key_exists('client', $config)) {
            $this->_usedProperties['client'] = true;
            $this->client = $config['client'];
            unset($config['client']);
        }
    
        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }
    
    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['baseUri'])) {
            $output['base_uri'] = $this->baseUri;
        }
        if (isset($this->_usedProperties['claim'])) {
            $output['claim'] = $this->claim;
        }
        if (isset($this->_usedProperties['client'])) {
            $output['client'] = $this->client;
        }
    
        return $output;
    }

}
