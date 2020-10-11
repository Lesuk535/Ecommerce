<?php

declare(strict_types=1);

namespace App\Model\Category\MessageHandler\Command;

use App\Model\Category\Domain\Service\ICategoryRepository;
use App\Model\Category\Domain\Service\IImageUploader;
use App\Model\Category\Domain\ValueObject\Id;
use App\Model\Category\Message\Command\AttachSubcategories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AttachSubcategoriesHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private ICategoryRepository $repository;

    public function __construct(EntityManagerInterface $em, ICategoryRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }
    public function __invoke(AttachSubcategories $subcategories)
    {
        $category = $this->repository->getById(new Id($subcategories->id));
        $category->attachChildren($this->repository->getByIds($subcategories->children));
        $this->em->flush();
    }
}