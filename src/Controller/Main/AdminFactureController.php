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
        $table = $dataTableFactory->create()
            ->add('createdAt', DateTimeColumn::class, ['label' => 'Date', 'format' => 'Y-m-d H:i:s', 'searchable' => true])
            ->add('code', TextColumn::class, ['label' => 'Code', 'searchable' => true])
            ->add('montant', NumberColumn::class, ['label' => 'Montant'])
            ->add('nap', NumberColumn::class, ['label' => 'NAP'])
            ->add('verse', NumberColumn::class, ['label' => 'Versés'])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Facture::class,
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