<?php

namespace App\Controller\Main;

use App\Repository\Main\FactureRepository;
use App\Entity\Main\Facture;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Omines\DataTablesBundle\Column\TwigColumn;
use Doctrine\ORM\QueryBuilder;

#[Route('/admin/facture')]
class AdminFactureController extends AbstractController
{
    public function __construct(
        private FactureRepository $factureRepository
    )
    {
    }

    #[Route('/', name:'app_main_admin_facture_index')]
    public function index(Request $request, DataTableFactory $dataTableFactory)
    {
        $table = $dataTableFactory->create(['searching' => true])
            ->add('createdAt', DateTimeColumn::class, ['label' => 'Date', 'format' => 'd-m-Y H:i', 'searchable' => false,'orderable' => true ])
            ->add('code', TextColumn::class, ['label' => 'Code', 'searchable' => true, 'globalSearchable' => true,])
            ->add('montant', NumberColumn::class, ['label' => 'Montant', 'searchable' => true])
            ->add('remise', NumberColumn::class, ['label' => 'Remise', 'searchable' => true])
            ->add('nap', NumberColumn::class, ['label' => 'NAP', 'searchable' => true])
            ->add('verse', NumberColumn::class, ['label' => 'Versé', 'searchable' => true])
            ->add('monnaie', NumberColumn::class, ['label' => 'Monnaie', 'searchable' => true])
            ->add('actions', TwigColumn::class, [
                'label' => 'Actions',
                'template' => 'main/facture/_actions.html.twig',
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Facture::class,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select('f')
                        ->from(Facture::class, 'f');
                },
            ])
                        ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('main/facture/liste.html.twig',[
            //'factures' => $this->factureRepository->findAllList(),
            'datatable' => $table
        ]);
    }
}