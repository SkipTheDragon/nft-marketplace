<?php

namespace App\Controller\Admin;

use App\Entity\AccountWallet;
use App\Entity\NFT;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
            ->add('id')
            ->add('address')
            ->add('tokenId')
            ->add('blockchain')
            ->add('isImported');

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


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('address'),
            TextField::new('tokenId'),
            AssociationField::new('blockchain'),
            BooleanField::new('isImported')->setDisabled(),
        ];
    }

}
