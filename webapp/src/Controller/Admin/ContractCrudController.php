<?php

namespace App\Controller\Admin;

use App\Architecture\EContractType;
use App\Entity\Contract;
use App\Form\Type\JsonCodeEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContractCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contract::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Contract ABI')
            ->setEntityLabelInPlural('Contract ABIs');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('identifier')
            ->add('type')
            ->add('code');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(),
            TextField::new('name'),
            ChoiceField::new('type')->setChoices(EContractType::cases()),
            TextField::new('identifier'),
            CodeEditorField::new('code')->setFormType(JsonCodeEditorType::class)->addCssClass('vw-100 code-editor-max-width-fix')->hideOnIndex(),
        ];
    }
}
