<?php

namespace App\Service\Barcode;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeGeneratorService {

    const EXTENSIION_TYPE_PNG = ".png";
    const EXTENSIION_TYPE_WEBP = ".webp";

    private $barcodeDir;

    public function __construct(
        string $barcodeDir,
    ) {
        $this->barcodeDir = $barcodeDir;

        $this->createDir();
    }

    public function generateBarcode(string $code): string
    {
        $generator = new BarcodeGeneratorPNG();

        $filename = $this->getUniqueFilename();
        $pngFileData = $generator->getBarcode($code, $generator::TYPE_CODE_128);
        file_put_contents($this->barcodeDir . $filename . self::EXTENSIION_TYPE_PNG, $pngFileData);
        
        $img = imagecreatefrompng($this->barcodeDir . $filename . self::EXTENSIION_TYPE_PNG);
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
        imagewebp($img, $this->barcodeDir . $filename . self::EXTENSIION_TYPE_WEBP);
        imagedestroy($img);
        unlink($this->barcodeDir . $filename . self::EXTENSIION_TYPE_PNG);

        return $filename;
    }

    public function getBarcodeImage(?string $name): ?string
    {
        if (!$name || !file_exists($this->barcodeDir . $name . self::EXTENSIION_TYPE_WEBP)) {
            return null;
        }
        
        return $this->barcodeDir . $name . self::EXTENSIION_TYPE_WEBP;
    }

    private function createDir(): void {
        if (!file_exists($this->barcodeDir)) {
            mkdir($this->barcodeDir, 0777, true);
        }
    }

    private function getUniqueFilename(): string
    {
        do {
            $fileName = bin2hex(random_bytes(8));
        } while (file_exists($this->barcodeDir . $fileName));

        return $fileName;
    }
}