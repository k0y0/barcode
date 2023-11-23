<?php

namespace App\Controller\Dashboard;

use App\Form\Dashboard\BarcodeType;
use App\Service\Barcode\BarcodeGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        Request $request,
        BarcodeGeneratorService $barcodeGeneratorService
    ): Response {
        $form = $this->createForm(BarcodeType::class);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('code')->getData();
            $imageName = $barcodeGeneratorService->generateBarcode($code);
        }

        return $this->render('dashboard/index.html.twig', [
            'form' => $form->createView(),
            'barcode' => $imageName ?? null,
        ]);
    }

    #[Route('/barcode/{filename}', name: 'app_dashboard_barcode_img')]
    public function getBarcodeImg(
        Request $request,
        BarcodeGeneratorService $barcodeGeneratorService,
        ?string $filename = null,
    ): Response {
        $barcodeImage = $barcodeGeneratorService->getBarcodeImage($filename);
        if ($barcodeImage === null) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        return new BinaryFileResponse($barcodeImage);
    }
}
