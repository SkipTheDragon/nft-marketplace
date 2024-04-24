<?php

namespace App\Controller\Admin;

use App\Architecture\EBlockchainType;
use App\Entity\Blockchain;
use App\Entity\RpcProvider;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class BlockchainCrudController extends AbstractCrudController
{
    public function __construct(
        protected readonly AdminUrlGenerator $adminUrlGenerator,
    )
    {
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Blockchain')
            ->setEntityLabelInPlural('Blockchains');
    }

    public function configureFilters(Filters $filters) : Filters
    {
        return $filters
            ->add('name')
            ->add('type')
            ->add('nfts')
            ->add('chainId');
    }

    public static function getEntityFqcn(): string
    {
        return Blockchain::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->setDisabled(),
            TextField::new('name'),
            ChoiceField::new('type')
                ->setChoices(EBlockchainType::cases()),
            AssociationField::new('rpcProviders')
                ->setFormTypeOption('by_reference', false)
                ->formatValue(function ($value, $entity) {
                    $url = $this->adminUrlGenerator
                        ->set('filters[id][value]', $entity->getId())
                        ->set('filters[id][comparison]', '=')
                        ->setController(RpcProviderCrudController::class)
                        ->setAction(Action::INDEX)
                        ->generateUrl();

                    return '<a href="' . $url . '">' . count($value) . '</a>';
                }),
            NumberField::new('chainId')->setNumberFormat('%d'),
            TextField::new('nativeToken'),
            AssociationField::new('nfts', 'NFTs')
                ->hideOnForm()
                ->setDisabled()
                ->setVirtual(true)
                ->formatValue(function ($value, $entity) {
                    $url = $this->adminUrlGenerator
                        ->set('filters[nft][value]', $entity->getId())
                        ->set('filters[nft][comparison]', '=')
                        ->setController(NFTCrudController::class)
                        ->setAction(Action::INDEX)
                        ->generateUrl();

                    return '<a href="' . $url . '">' . count($value) . '</a>';
                }),
            ImageField::new('icon')
                ->setUploadDir('public/uploads/blockchain')
                ->setBasePath('uploads/blockchain')
                ->setRequired(false)
        ];
    }
}
