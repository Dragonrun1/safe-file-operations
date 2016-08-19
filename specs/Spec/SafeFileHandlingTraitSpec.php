<?php
declare(strict_types = 1);
namespace Spec\SafeFileOperations;

use PhpSpec\ObjectBehavior;
use SafeFileOperations\SafeFileHandlingTrait;

class SafeFileHandlingTraitSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(MockSafeFileHandlingTrait::class);
    }
    public function let()
    {
        $this->beAnInstanceOf('Spec\SafeFileOperations\MockSafeFileHandlingTrait');
    }
}
