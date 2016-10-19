<?php
declare(strict_types = 1);
/**
 * Contains class SafeFileError.
 *
 * PHP version 7.0+
 *
 * LICENSE:
 * This file is part of Safe File Operations which tries to proved as completely safe read and write file operations as
 * possible.
 * Copyright (C) 2016 Michael Cummings
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see
 * <http://spdx.org/licenses/LGPL-3.0.html>.
 *
 * You should be able to find a copy of this license in the COPYING-LESSER.md
 * file. A copy of the GNU GPL should also be available in the COPYING.md file.
 *
 * @author    Michael Cummings <mgcummings@yahoo.com>
 * @copyright 2016 Michael Cummings
 * @license   LGPL-3.0+
 */
namespace SafeFileOperations;

/**
 * Class SafeFileError.
 */
class SafeFileError extends \Error
{
    /** @noinspection MoreThanThreeArgumentsInspection */
    /** @noinspection MagicMethodsValidityInspection */
    /**
     * SafeFileError constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Throwable $previous
     * @param string     $file
     * @param int        $line
     */
    public function __construct(
        string $message,
        int $code = 0,
        \Throwable $previous = null,
        string $file = __FILE__,
        int $line = __LINE__
    ) {
        $this->file = $file;
        $this->line = $line;
        $this->trace = debug_backtrace();
        parent::__construct($message, $code, $previous);
    }
    public function __toString(): string
    {
        $format = '%1$s: (%2$s) %3$s in %4$s:%5$s';
        $mess = '';
        $previous = $this->getPrevious();
        $stack = [];
        while (null !== $previous) {
            $stack[] = sprintf($format, $this->getThrowableType($previous), $previous->getCode(),
                $previous->getMessage(), $previous->getFile(), $previous->getLine());
            $previous = $previous->getPrevious();
        }
        if (count($stack)) {
            $mess .= 'Thrown stack:' . PHP_EOL;
            $mess .= implode(PHP_EOL, array_reverse($stack)) . PHP_EOL;
        }
        $format .= PHP_EOL . 'Stack trace:' . PHP_EOL;
        $mess .= sprintf($format, $this->getThrowableType($this), $this->getCode(), $this->getMessage(),
            $this->getFile(), $this->getLine());
        return $mess . $this->getTraceAsString();
    }
    /**
     * @var string $file
     */
    protected $file;
    /**
     * @var int $line
     */
    protected $line;
    /**
     * @var array $trace
     */
    protected $trace;
    /**
     * @param $throwable
     *
     * @return string
     */
    private function getThrowableType($throwable)
    {
        $class = get_class($throwable);
        if (false === $class) {
            $class = 'Throwable';
        }
        return $class;
    }
}
