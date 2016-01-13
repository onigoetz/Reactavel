<?php namespace Onigoetz\Reactavel;

use Illuminate\Container\Container;

/**
 * @property Container $parentApp
 */
trait ProxyContainer
{
    protected $usingChildContainer = [
        "Illuminate\\Http\\Request",
        "Laravel\\Lumen\\Application"
    ];

    protected function shouldSendToParent($abstract)
    {
        $normalized = $this->normalize($abstract);

        if ($this->isAlias($normalized)) {
            $normalized = $this->getAlias($normalized);
        }

        return !in_array($normalized, $this->usingChildContainer);
    }

    // Proxies
    // ----------------------------------------------------------------------
    // By default all call to the local Container, some proxies
    // are made to call the parent's app container

    //public function when($concrete)
    public function bound($abstract)
    {
        if ($this->shouldSendToParent($abstract)) {
            return $this->parentApp->bound($abstract);
        } else {
            return parent::bound($abstract);
        }
    }

    public function resolved($abstract)
    {
        if ($this->shouldSendToParent($abstract)) {
            return $this->parentApp->resolved($abstract);
        } else {
            return parent::resolved($abstract);
        }
    }

    public function isAlias($name)
    {
        return $this->parentApp->isAlias($name);
    }
    //public function bind($abstract, $concrete = null, $shared = false)
    //public function addContextualBinding($concrete, $abstract, $implementation)
    //public function bindIf($abstract, $concrete = null, $shared = false)
    //public function singleton($abstract, $concrete = null)
    //public function share(Closure $closure)
    //public function extend($abstract, Closure $closure)
    //public function instance($abstract, $instance)
    //public function tag($abstracts, $tags)
    //public function tagged($tag)
    public function alias($abstract, $alias)
    {
        $this->parentApp->alias($abstract, $alias);
    }

    //public function rebinding($abstract, Closure $callback)
    //public function refresh($abstract, $target, $method)
    //protected function rebound($abstract)
    //protected function getReboundCallbacks($abstract)
    //public function wrap(Closure $callback, array $parameters = [])
    //public function call($callback, array $parameters = [], $defaultMethod = null)
    //protected function getMethodDependencies($callback, array $parameters = [])
    //protected function addDependencyForCallParameter(ReflectionParameter $parameter, array &$parameters, &$dependencies)
    //protected function callClass($target, array $parameters = [], $defaultMethod = null)
    public function make($abstract, array $parameters = [])
    {
        if ($this->shouldSendToParent($abstract)) {
            return $this->parentApp->make($abstract, $parameters);
        } else {
            return parent::make($abstract, $parameters);
        }
    }
    //protected function getConcrete($abstract)
    //protected function getContextualConcrete($abstract)
    //protected function getExtenders($abstract)
    //public function build($concrete, array $parameters = [])
    //protected function getDependencies(array $parameters, array $primitives = [])
    //protected function resolveNonClass(ReflectionParameter $parameter)
    //protected function resolveClass(ReflectionParameter $parameter)
    //public function resolving($abstract, Closure $callback = null)
    //public function afterResolving($abstract, Closure $callback = null)
    //protected function resolvingCallback(Closure $callback)
    //protected function afterResolvingCallback(Closure $callback)
    //protected function getFunctionHint(Closure $callback)
    //protected function fireResolvingCallbacks($abstract, $object)
    //protected function getCallbacksForType($abstract, $object, array $callbacksPerType)
    //protected function fireCallbackArray($object, array $callbacks)
    //public function isShared($abstract)
    //protected function isBuildable($concrete, $abstract)
    protected function getAlias($abstract)
    {
        return $this->parentApp->getAlias($abstract);
    }
    //public function getBindings()
    //protected function dropStaleInstances($abstract)
    //public function forgetInstance($abstract)
    //public function forgetInstances()
    //public function flush()
    //public function offsetExists($key)
    //public function offsetGet($key)
    //public function offsetSet($key, $value)
    //public function offsetUnset($key)
    //public function __get($key)
    //public function __set($key, $value)

    // No side effects
    //protected function getClosure($abstract, $concrete)
    //protected function extractAlias(array $definition)
    //protected function isCallableWithAtSign($callback)
    //protected function getCallReflector($callback)
    //protected function normalize($service)
    //protected function keyParametersByArgument(array $dependencies, array $parameters)

    // Statics don't need override
    //public static function getInstance()
    //public static function setInstance(ContainerContract $container)
}
