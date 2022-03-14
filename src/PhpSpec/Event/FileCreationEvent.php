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

namespace PhpSpec\Event;

final class FileCreationEvent extends BaseEvent implements PhpSpecEvent
{
    private string $filepath;

    public function __construct(string $filepath)
    {

        $this->filepath = $filepath;
    }

    
    public function getFilePath(): string
    {
        return $this->filepath;
    }
}
