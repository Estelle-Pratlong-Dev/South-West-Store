<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Entity\Brands;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('reference'),
            TextField::new('size'),
            TextField::new('color'),
			MoneyField::new('SalePrice')->setCurrency('EUR'),
			MoneyField::new('PurchasePrice')->setCurrency('EUR'),
			AssociationField::new('brandId'),
			DateField::new('created_date'),
			IntegerField::new('created_by'),
        ];
    }
   
}
