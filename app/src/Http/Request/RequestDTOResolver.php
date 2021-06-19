<?php


namespace App\Http\Request;


use Generator;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDTOResolver implements ArgumentValueResolverInterface
{

    public function __construct(
        private ValidatorInterface $validator
    )
    {
    }

    /**
     * @throws ReflectionException
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $reflection = new ReflectionClass($argument->getType());
        if ($reflection->implementsInterface(RequestDTOInterface::class)) {
            return true;
        }

        return false;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $class = $argument->getType();
        $dto = new $class($request);
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $message = '';

            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $message .= $error->getPropertyPath() . ": " . $error->getMessage() . "\n";
            }
            throw new BadRequestHttpException($message);
        }

        yield $dto;
    }
}