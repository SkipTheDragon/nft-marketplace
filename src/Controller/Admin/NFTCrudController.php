<?php

namespace App\Controller\Admin;

use App\Entity\AccountWallet;
use App\Entity\NFT;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class NFTCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('NFT')
            ->setEntityLabelInPlural('NFTs');
    }

    public function configureFilters(Filters $filters) : Filters
    {
        return $filters
            ->add('name')
            ->add('description')
            ->add('blockchain')
            ->add('importedOn');
    }

    public static function getEntityFqcn(): string
    {
        return NFT::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW, Action::EDIT, Action::DELETE);

        return $actions;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
