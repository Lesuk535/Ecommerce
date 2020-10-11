<?php

declare(strict_types=1);

namespace App\Model\Category\MessageHandler\Command;

use App\Controller\Admin\ChangeStatus;
use App\Model\Category\Domain\Service\ICategoryRepository;
use App\Model\Category\Domain\ValueObject\Id;
use App\Model\Category\Domain\ValueObject\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ChangeStatusHandler implements MessageHandlerInterface
{
    private ICategoryRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(ICategoryRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    public function __invoke(ChangeStatus $status)
    {
        $category = $this->repository->getById(new Id($status->id));
        $category->changeStatus(new Status($status->status));

        $this->em->flush();
    }
}