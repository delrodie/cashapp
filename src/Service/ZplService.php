<?php

// src/Service/ZplService.php
namespace App\Service;

use App\Entity\Main\Produit;

class ZplService
{
    /**
     * Génère le code ZPL pour une étiquette 58x40mm
     * @ 203 dpi :  58mm = ~464 dots, 40mm = ~320 dots
     */
    public function genererEtiquette(Produit $produit): string
    {
        $libelle   = mb_strtoupper(mb_substr($produit->getLibelle(), 0, 28));
        $prix      = number_format($produit->getPrixVente(), 0, '', '.');
        $reference = $produit->getReference();
        $codebarre = $produit->getCodebarre() ?? $reference;
        $categorie = mb_strtoupper(mb_substr($produit->getCategorie()->getLibelle(), 0, 18));

        // ^XA / ^XZ  = début / fin d'étiquette
        // ^PW        = largeur en dots (464 pour 58mm @203dpi)
        // ^LL        = longueur en dots (320 pour 40mm @203dpi)
        // ^FO x,y    = position du champ (origine haut-gauche)
        // ^A0N,h,w   = police intégrée, hauteur, largeur en dots
        // ^FD        = données du champ
        // ^FS        = fin de champ
        // ^BCN,h,Y,N = CODE128, hauteur, afficher texte, pas de check
        // ^GB w,h,t  = boîte (rectangle), largeur, hauteur, épaisseur

        return <<<ZPL
        ^XA
        ^MMT
        ^PW464
        ^LL320
        ^LS0
        ^CI28

        ~TA000
        ~JSN

        ^FO10,8^A0N,26,24^FD{$libelle}^FS

        ^FO0,38^GB464,2,2^FS

        ^FO290,44^A0N,18,16^FDFCFA^FS
        ^FO200,58^A0N,62,58^FD{$prix}^FS

        ^FO12,44^A0N,20,18^FD{$reference}^FS

        ^FO10,148^BCN,68,Y,N,N^FD{$codebarre}^FS

        ^FO290,272^A0N,16,14^FD{$categorie}^FS

        ^XZ
        ZPL;
    }
}