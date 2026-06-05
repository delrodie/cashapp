<?php

namespace App\Service;

use Zebra\Client;
use Zebra\Zpl\Builder;

class LabelPrinterService
{
    private string $printerShare; // Ex: '\\\\SERVEUR\\Honeywell-PC41E-Raw'

    public function __construct(string $printerShare)
    {
        $this->printerShare = $printerShare;
    }

    /**
     * Impression côté serveur (Windows)
     */
    public function printServerSide(array $data): bool
    {
        $zpl = $this->generateZPL($data);

        $tempFile = tempnam(sys_get_temp_dir(), 'label_') . '.zpl';
        file_put_contents($tempFile, $zpl);

        // Commande Windows pour impression RAW
        $command = 'print /D:"' . $this->printerShare . '" "' . $tempFile . '"';

        exec($command, $output, $returnCode);

        // Nettoyage
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        return $returnCode === 0;
    }

    private function generateZPL(array $produit): string
    {
        $libelle     = strtoupper(substr($produit['libelle'] ?? '', 0, 28)); // Limité pour tenir sur l'étiquette
        $prix        = number_format($produit['prixVente'] ?? 0, 0, '', '.');
        $reference   = $produit['reference'] ?? '';
        $codebarre   = $produit['codebarre'] ?? '';
        $categorie   = $produit['categorie']['libelle'] ?? '';

        return <<<ZPL
^XA
^CF0,22,22
^PW464
^LL320
^MD30
^PR3

; --- Titre (Produit) ---
^FO30,25^A0N,24,24^FD{$libelle}^FS

; --- Ligne séparatrice ---
^FO30,55^GB400,1,1^FS

; --- Prix en gros ---
^FO240,65^A0N,75,75^FR^FD{$prix}^FS
^FO330,130^A0N,22,22^FR^FD FCFA^FS

; --- Référence ---
^FO30,135^A0N,23,23^FD{$reference}^FS

; --- Code-barres ---
^FO30,175^BCN,75,Y,N,N^FD{$codebarre}^FS

; --- Catégorie ---
^FO300,285^A0N,20,20^FD{$categorie}^FS

^XZ
ZPL;
    }

//    private function generateZPL(array $data): string
//    {
//        $reference = strtoupper($data['reference'] ?? 'N/A');
//        $barcode   = $data['barcode'] ?? '123456789012';
//        $client    = $data['client'] ?? '';
//
//        return <<<ZPL
//^XA
//^CF0,22,22
//^PW470                 ; ~58mm à 203 dpi (58*8 ≈ 464)
//^LL320                 ; ~40mm à 203 dpi
//^MD28                  ; Densité
//^PR3
//
//; --- Contenu ---
//^FO40,25^A0N,32,32^FD{$reference}^FS
//
//^FO40,75^BCN,75,Y,N,N^FD{$barcode}^FS
//
//^FO40,175^A0N,23,23^FD{$client}^FS
//^FO40,205^A0N,20,20^FD{date('d/m/Y')}^FS
//
//^XZ
//ZPL;
//    }

    /**
     * Retourne le ZPL brut (utile pour debug ou QZ Tray)
     */
    public function getZPL(array $data): string
    {
        return $this->generateZPL($data);
    }
}