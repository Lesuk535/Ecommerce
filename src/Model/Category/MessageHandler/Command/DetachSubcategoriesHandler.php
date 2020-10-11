<?php

declare(strict_types=1);

namespace App\Model\Category\MessageHandler\Command;

use App\Model\Category\Domain\Service\ICategoryRepository;
use App\Model\Category\Domain\ValueObject\Id;
use App\Model\Category\Message\Command\DetachSubcategories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DetachSubcategoriesHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private ICategoryRepository $repository;

    public function __construct(EntityManagerInterface $em, ICategoryRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    public function __invoke(DetachSubcategories $subcategories)
    {
        $category = $this->repository->getById(new Id($subcategories->id));
        $category->detachChildren($this->repository->getByIds($subcategories->children));
        $this->em->flush();
    }
}