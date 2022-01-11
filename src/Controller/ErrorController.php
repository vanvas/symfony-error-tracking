<?php
declare(strict_types=1);

namespace Vim\ErrorTracking\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Vim\Api\Attribute\Flush;
use Vim\Api\Attribute\Paginate;
use Vim\Api\Attribute\Resource;
use Vim\Api\Attribute\Schema\Schema;
use Vim\Api\Attribute\Groups;
use Vim\ErrorTracking\Entity\Error;
use Vim\ErrorTracking\Repository\ErrorRepository;
use Vim\ErrorTracking\Service\UnexpectedErrorLogService;
use Vim\Api\Attribute\Filter;

class ErrorController
{
    public function __construct(private ErrorRepository $errorRepository, private EntityManagerInterface $em)
    {
    }

    public function create(Request $request, UnexpectedErrorLogService $logService): JsonResponse
    {
        try {
            $content = json_decode($request->getContent(), true);
            $hash = md5(($content['message'] ?? '') . ($content['trace'] ?? ''));
            if (!($entity = $this->errorRepository->findOneByHash($hash))) {
                $entity = new Error();
                $entity
                    ->setCount(0)
                    ->setHash($hash)
                    ->setLevel($content['level'] ?? null)
                    ->setMessage($content['message'] ?? null)
                    ->setServer($content['server'] ?? null)
                    ->setEnv($content['env'] ?? null)
                    ->setProcess($content['process'] ?? null)
                    ->setTrace($content['trace'] ?? null)
                    ->setCode($content['code'] ?? null)
                    ->setFile($content['file'] ?? null)
                    ->setLine($content['line'] ?? null)
                    ->setNamespace($content['namespace'] ?? null)
                    ->setCreatedAt(new \DateTimeImmutable())
                ;
            }

            $entity
                ->setCount($entity->getCount() + 1)
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setDate($content['date'] ? new \DateTimeImmutable($content['date']) : null)
            ;

            $this->em->persist($entity);
            $this->em->flush();
        } catch (\Throwable $throwable) {
            $logService->logThrowable($throwable);
        }

        return new JsonResponse();
    }

    public function test(): void
    {
        throw new \Exception('This is error tracking test...');
    }

    #[Resource(Error::class)]
    #[Paginate]
    #[Filter\DateFrom('createdAt', 'createdAtFrom')]
    #[Filter\DateTo('createdAt', 'createdAtTo')]
    #[Filter\Strict('env')]
    #[Filter\Strict('process')]
    #[Filter\Like('level')]
    #[Filter\Like('message')]
    #[Filter\Like('trace')]
    #[Filter\Like('file')]
    #[Filter\Like('namespace')]
    #[Schema(Error::class)]
    #[Groups([Error::GROUP_LIST])]
    public function index(): void
    {
    }

    #[Resource('error')]
    #[Schema(Error::class)]
    #[Groups([Error::GROUP_VIEW])]
    public function view(Error $error): Error
    {
        return $error;
    }

    #[Resource('error')]
    #[Flush]
    public function delete(Error $error): void
    {
    }

    public function deleteAll(ErrorRepository $errorRepository): void
    {
        foreach ($errorRepository->findAll() as $error) {
            $this->em->remove($error);
        }

        $this->em->flush();
    }
}
