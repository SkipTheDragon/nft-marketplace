<?php

namespace App\Controller\Admin;

use App\Entity\AccountSession;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AccountSessionCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Account Session')
            ->setEntityLabelInPlural('Account Sessions');
    }

    public static function getEntityFqcn(): string
    {
        return AccountSession::class;
    }

    public function configureFilters(Filters $filters) : Filters
    {
        return $filters
            ->add('browser')
            ->add('os')
            ->add('startedAt')
            ->add('lastActivityAt')
            ->add('account');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(),
            TextField::new('browser'),
            TextField::new('os', 'Operating System'),
            DateTimeField::new('startedAt')->setDisabled(),
            DateTimeField::new('lastActivityAt')->setDisabled(),
            AssociationField::new('account')->setDisabled()->formatValue(fn ($value, $entity) => $value->getUsername()),
        ];
    }
}
