<?php

namespace App\Controller\Admin;

use App\Entity\AccountWallet;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AccountWalletCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AccountWallet::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Account Wallet')
            ->setEntityLabelInPlural('Account Wallets');
    }

    public function configureFilters(Filters $filters) : Filters
    {
        return $filters
            ->add('account')
            ->add('type')
            ->add('address');
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action->displayIf(static function (AccountWallet $accountWallet) {
                return $accountWallet->getAccount()->getWallets()->count() > 1;
            });
        });

        $actions->disable(Action::NEW, Action::EDIT, Action::BATCH_DELETE);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(),
            ChoiceField::new('type')->setDisabled(),
            TextField::new('address')->setDisabled(),
            AssociationField::new('account')->setDisabled()->formatValue(fn ($value, $entity) => $value->getUsername()),
        ];
    }
}
