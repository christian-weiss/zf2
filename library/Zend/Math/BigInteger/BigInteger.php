<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace Zend\Math\BigInteger;

use Zend\Math\BigInteger\Adapter\AdapterInterface;
use Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Math
 * @subpackage BigInteger
 */
class BigInteger
{
    /**
     * Broker for loading adapters
     *
     * @var null|Broker
     */
    protected static $adapterBroker = null;

    /**
     * The default adapter.
     *
     * @var AdapterInterface
     */
    protected static $defaultAdapter = null;

    /**
     * Create a BigInteger adapter instance
     *
     * @param  string|AdapterInterface|null $adapterName
     * @return AdapterInterface
     */
    public static function factory($adapterName = null)
    {
        if (null === $adapterName) {
            return static::getAvailableAdapter();
        } else if ($adapterName instanceof AdapterInterface) {
            return $adapterName;
        } else {
            return self::getAdapterBroker()->load($adapterName);
        }
    }

    /**
     * Set adapter broker
     *
     * @param Broker $broker
     */
    public static function setAdapterBroker(Broker $broker)
    {
        self::$adapterBroker = $broker;
    }

    /**
     * Get the adapter broker
     *
     * @return Broker
     */
    public static function getAdapterBroker()
    {
        if (static::$adapterBroker === null) {
            static::$adapterBroker = new AdapterBroker();
        }
        return static::$adapterBroker;
    }

    /**
     * Set default BigInteger adapter
     *
     * @param string|AdapterInterface $adapter
     */
    public static function setDefaultAdapter($adapter)
    {
        static::$defaultAdapter = static::factory($adapter);
    }

    /**
     * Get default BigInteger adapter
     *
     * @return null|AdapterInterface
     */
    public static function getDefaultAdapter()
    {
        if (null === static::$defaultAdapter) {
            static::$defaultAdapter = static::getAvailableAdapter();
        }
        return static::$defaultAdapter;
    }

    /**
     * Determine and return available adapter
     *
     * @return AdapterInterface
     * @throws Exception\RuntimeException
     */
    public static function getAvailableAdapter()
    {
        if (extension_loaded('gmp')) {
            $adapterName = 'Gmp';
        } elseif (extension_loaded('bcmath')) {
            $adapterName = 'Bcmath';
        } else {
            throw new Exception\RuntimeException('Big integer math support is not detected');
        }
        return static::factory($adapterName);
    }

    /**
     * Call adapter methods statically
     *
     * @param  string $method
     * @param  mixed $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $adapter = static::getDefaultAdapter();
        return call_user_func_array(array($adapter, $method), $args);
    }
}