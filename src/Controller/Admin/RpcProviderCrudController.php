<?php

namespace App\Controller\Admin;

use App\Entity\RpcProvider;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RpcProviderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RpcProvider::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('RPC')
            ->setEntityLabelInPlural('RPCs');
    }

    public function configureFilters(Filters $filters) : Filters
    {
        return $filters
            ->add('id')
            ->add('url')
            ->add('headers')
            ->add('blockchain');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(),
            TextField::new('url'),
            TextareaField::new('headers', 'Headers (json)'),
            ArrayField::new('blockchain')->setDisabled()->hideOnForm(),
        ];
    }
}
