<?php

/*
 * This file is part of PhpSpec, A php toolset to drive emergent
 * design by specification.
 *
 * (c) Marcello Duarte <marcello.duarte@gmail.com>
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpSpec\Wrapper\Subject\Expectation;

use PhpSpec\Exception\Example\MatcherException;
use PhpSpec\Matcher\Matcher;
use PhpSpec\Util\Instantiator;
use PhpSpec\Wrapper\Subject\WrappedObject;

abstract class DuringCall
{
    private Matcher $matcher;

    private mixed $subject;
    private array $arguments = [];
    private ?WrappedObject $wrappedObject = null;

    public function __construct(Matcher $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * @return $this
     */
    public function match(string $alias, mixed $subject, array $arguments = array(), WrappedObject $wrappedObject = null): static
    {
        $this->subject = $subject;
        $this->arguments = $arguments;
        $this->wrappedObject = $wrappedObject;

        return $this;
    }

    public function during(string $method, array $arguments = array()): void
    {
        if (!$this->wrappedObject) {
            throw new \LogicException('Cannot call during on undefined object');
        }

        if ($method === '__construct') {
            $this->subject->beAnInstanceOf($this->wrappedObject->getClassName(), $arguments);

            $this->duringInstantiation();
            return;
        }

        $object = $this->wrappedObject->instantiate();

        $this->runDuring($object, $method, $arguments);
    }

    public function duringInstantiation(): void
    {
        if (!$this->wrappedObject) {
            throw new \LogicException('Cannot call during on undefined object');
        }

        if ($factoryMethod = $this->wrappedObject->getFactoryMethod()) {
            /** @var array{1:string}|string $factoryMethod */
            $method = \is_array($factoryMethod) ? $factoryMethod[1] : $factoryMethod;
        } else {
            $method = '__construct';
        }

        $instantiator = new Instantiator();

        /** @var class-string $className */
        $className = $this->wrappedObject->getClassName();
        $object = $instantiator->instantiate($className);

        $this->runDuring($object, $method, $this->wrappedObject->getArguments());
    }

    /**
     * @throws MatcherException
     */
    public function __call(string $method, array $arguments = array())
    {
        if (preg_match('/^during(.+)$/', $method, $matches)) {
            return $this->during(lcfirst($matches[1]), $arguments);
        }

        throw new MatcherException('Incorrect usage of matcher, '.
            'either prefix the method with "during" and capitalize the '.
            'first character of the method or use ->during(\'callable\', '.
            'array(arguments)).'.PHP_EOL.'E.g.'.PHP_EOL.'->during'.
            ucfirst($method).'(arguments)'.PHP_EOL.'or'.PHP_EOL.
            '->during(\''.$method.'\', array(arguments))');
    }

    protected function getArguments(): array
    {
        return $this->arguments;
    }

    protected function getMatcher(): Matcher
    {
        return $this->matcher;
    }

    abstract protected function runDuring(object $object, string $method, array $arguments = array()): void;
}
