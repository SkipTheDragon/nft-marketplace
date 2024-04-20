<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class AccountCrudController extends AbstractCrudController
{
    public function __construct(
        protected readonly AdminUrlGenerator $adminUrlGenerator,
    )
    {
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Account')
            ->setEntityLabelInPlural('Accounts');
    }

    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        // TODO: add roles dynamically
        $roles = [
            'ROLE_USER' => 'ROLE_USER',
            'ROLE_ADMIN' => 'ROLE_ADMIN',
        ];

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('username')->setRequired(true),
            EmailField::new('email'),
            ChoiceField::new('roles')->setChoices($roles)->allowMultipleChoices(),
            AssociationField::new('wallets')->hideOnForm()->setDisabled()->formatValue(function ($value, $entity) {

                $url = $this->adminUrlGenerator
                    ->set('filters[account][value]', $entity->getId())
                    ->set('filters[account][comparison]', '=')
                    ->setController(AccountWalletCrudController::class)
                    ->setAction(Action::INDEX)
                    ->generateUrl();

                return '<a href="' . $url . '">' . count($value) . '</a>';
            }),
            AssociationField::new('sessions')->hideOnForm()->setDisabled()->formatValue(function ($value, $entity) {

                $url = $this->adminUrlGenerator
                    ->set('filters[account][value]', $entity->getId())
                    ->set('filters[account][comparison]', '=')
                    ->setController(AccountSessionCrudController::class)
                    ->setAction(Action::INDEX)
                    ->generateUrl();

                return '<a href="' . $url . '">' . count($value) . '</a>';
            }),
            DateTimeField::new('registeredAt')->hideOnForm(),
            BooleanField::new('isVerified'),
            BooleanField::new('isActive'),
        ];
    }
}
