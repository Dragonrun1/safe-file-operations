<?php
declare(strict_types = 1);
namespace Spec\SafeFileOperations;

use PhpSpec\ObjectBehavior;
use SafeFileOperations\SafeFileError;

/**
 * Class SafeFileErrorSpec
 *
 * @mixin \SafeFileOperations\SafeFileError
 */
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
    public function it_should_initially_have_empty_message_and_default_code()
    {
        $this->getMessage()->shouldReturn('');
        $this->getCode()->shouldReturn(0);
    }
    public function it_should_initially_return_default_error_message_when_used_as_string()
    {
        /** @noinspection ImplicitMagicMethodCallInspection */
        $this->__toString()->shouldStartWith('SafeFileOperations\SafeFileError: (0)  in ');
    }
    public function it_should_return_same_error_message_when_used_as_string()
    {
        $message = 'test error';
        $this->beConstructedWith($message);
        $expected = 'SafeFileOperations\SafeFileError: (0) ' . $message . ' in ';
        /** @noinspection ImplicitMagicMethodCallInspection */
        $this->__toString()
             ->shouldStartWith($expected);
    }
    public function it_should_return_same_error_code_when_used_as_string()
    {
        $message = 'test error';
        $code = 1;
        $this->beConstructedWith($message, $code);
        $expected = 'SafeFileOperations\SafeFileError: (' . $code . ') ' . $message . ' in ';
        /** @noinspection ImplicitMagicMethodCallInspection */
        $this->__toString()
             ->shouldStartWith($expected);
    }
    public function it_should_return_thrown_stack_when_used_as_string()
    {
        $message = 'test error';
        $code = 1;
        $previous = new \LogicException($message, $code);
        $this->beConstructedWith($message, $code, $previous);
        $expected = 'Thrown stack:' . PHP_EOL . 'LogicException: (' . $code . ') ' . $message . ' in ';
        /** @noinspection ImplicitMagicMethodCallInspection */
        $this->__toString()
             ->shouldStartWith($expected);
    }
}
