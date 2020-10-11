<?php

declare(strict_types=1);

namespace App\Model\Category\MessageHandler\Command;

use App\Model\Category\Domain\Entity\Category;
use App\Model\Category\Domain\Service\ICategoryRepository;
use App\Model\Category\Domain\Service\IImageUploader;
use App\Model\Category\Domain\ValueObject\Id;
use App\Model\Category\Message\Command\CreateCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateCategoryHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private ICategoryRepository $repository;
    private IImageUploader $uploader;

    public function __construct(EntityManagerInterface $em, ICategoryRepository $repository, IImageUploader $uploader)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->uploader = $uploader;
    }

    public function __invoke(CreateCategory $category)
    {
        $image = \is_null($category->image)? null :$this->uploader->upload($category->image);
        $order = $this->repository->getMaxOrder() + 1;

        $categoryEntity = Category::create(
            Id::next(),
            $category->name,
            $order,
            $image,
            $category->description,
            $this->repository->getByIds($category->children),
        );

        $this->em->persist($categoryEntity);
        $this->em->flush();
    }
}