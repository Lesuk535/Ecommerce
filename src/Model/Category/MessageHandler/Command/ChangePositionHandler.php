<?php

declare(strict_types=1);

namespace App\Model\Category\MessageHandler\Command;

use App\Model\Category\Domain\Entity\Category;
use App\Model\Category\Domain\Service\ICategoryRepository;
use App\Model\Category\Message\Command\ChangePosition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ChangePositionHandler implements MessageHandlerInterface
{
    private ICategoryRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(ICategoryRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    public function __invoke(ChangePosition $changePosition)
    {
        $categories = $this->repository->getByIds($changePosition->positions);

        foreach ($categories as $category) {
            /**@var $category Category */
            $category->changeOrder(null);
        }

        $this->em->flush();

        foreach ($changePosition->positions as $position) {
            $category = $this->em->getUnitOfWork()->tryGetById($position['id'], Category::class);
            if (!$category) {
                throw new \DomainException(sprintf("Couldn't find Category with 'id'= %s", $position['id']));
            }
            $category->changeOrder($position['position']);
        }
        $this->em->flush();
    }
}