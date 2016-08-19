<?php
declare(strict_types=1);
namespace Spec\SafeFileOperations;

use SafeFileOperations\SafeFileError;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SafeFileErrorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(SafeFileError::class);
    }
    public function let()
    {
        $this->beConstructedWith('');
    }
}
