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

namespace PhpSpec\Process\Context;

final class JsonExecutionContext implements ExecutionContext
{
    private const ENV_NAME = 'PHPSPEC_EXECUTION_CONTEXT';
    private array $generatedTypes = [];

    public static function fromEnv(array $env): JsonExecutionContext
    {
        $executionContext = new self();

        if (array_key_exists(self::ENV_NAME, $env)) {
            $serialized = json_decode($env[self::ENV_NAME], true);
            $executionContext->generatedTypes = $serialized['generated-types'];
        }
        else {
            $executionContext->generatedTypes = array();
        }

        return $executionContext;
    }


    public function addGeneratedType(string $type): void
    {
        $this->generatedTypes[] = $type;
    }


    public function getGeneratedTypes(): array
    {
        return $this->generatedTypes;
    }


    public function asEnv(): array
    {
        return array(
            self::ENV_NAME => json_encode(
                array(
                    'generated-types' => $this->generatedTypes
                )
            )
        );
    }
}
