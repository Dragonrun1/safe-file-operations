<?php
declare(strict_types = 1);
namespace Spec\SafeFileOperations;

use PhpSpec\ObjectBehavior;
use SafeFileOperations\SafeFile;

class SafeFileSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(SafeFile::class);
    }
}
