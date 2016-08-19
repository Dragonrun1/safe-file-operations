<?php
declare(strict_types = 1);
/**
 * Contains Bootstrap.
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
 * @copyright 2016 Michael Cummings
 * @license   LGPL-3.0+
 * @author    Michael Cummings <mgcummings@yahoo.com>
 */
/*
 * Nothing to do if Composer auto loader already exists.
 */
if (class_exists('\Composer\Autoload\ClassLoader', false)) {
    return;
}
/*
 * Find Composer auto loader after striping away any vendor path.
 */
$path = str_replace('\\', '/', dirname(__DIR__));
$vendorPos = strpos($path, 'vendor/');
if (false !== $vendorPos) {
    $path = substr($path, 0, $vendorPos);
}
/*
 * Turn off warning messages for the following include.
 */
$errorReporting = error_reporting(E_ALL & ~E_WARNING);
include_once $path . '/vendor/autoload.php';
error_reporting($errorReporting);
unset($errorReporting, $path, $vendorPos);
if (!class_exists('\Composer\Autoload\ClassLoader', false)) {
    $mess = 'Could NOT find required Composer class auto loader. Aborting ...';
    if ('cli' === PHP_SAPI) {
        fwrite(STDERR, $mess);
    } else {
        fwrite(STDOUT, $mess);
    }
    return 1;
}
