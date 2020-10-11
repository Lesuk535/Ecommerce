<?php

declare(strict_types=1);

namespace App\Model\Category\MessageHandler\Command;

use App\Model\Category\Domain\Service\ICategoryRepository;
use App\Model\Category\Domain\Service\IImageUploader;
use App\Model\Category\Domain\ValueObject\Id;
use App\Model\Category\Message\Command\EditCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EditCategoryHandler implements MessageHandlerInterface
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

    public function __invoke(EditCategory $editCategory)
    {
        $category = $this->repository->getById(new Id($editCategory->id));

        if (!empty($editCategory->image)) {
            $image = $this->uploader->upload($editCategory->image, $editCategory->existingFilename);
            $category->changeImage($image);
        }

        $category->edit($editCategory->name, $editCategory->description);

        $this->em->persist($category);
        $this->em->flush();
    }
}