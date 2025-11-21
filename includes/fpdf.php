<?php
/**
 * Minimal embedded FPDF-like implementation for HW DNS Manual PDFs.
 * Supports A5 pages, JPEG images, Helvetica font and simple text output.
 */

class FPDF
{
    private $k; // scale factor
    private $w;
    private $h;
    private $lMargin = 0;
    private $rMargin = 0;
    private $tMargin = 0;
    private $x = 0;
    private $y = 0;
    private $fontSizePt = 12;
    private $textColor = [0, 0, 0];
    private $pages = [];
    private $page = 0;
    private $images = [];
    private $pageImages = [];

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A5')
    {
        $orientation = strtoupper($orientation);
        $this->k = $this->getScaleFactor($unit);

        if ($size === 'A5') {
            $this->w = ($orientation === 'P') ? 148 : 210;
            $this->h = ($orientation === 'P') ? 210 : 148;
        } else {
            $this->w = 210;
            $this->h = 297;
        }
    }

    public function SetMargins($left, $top, $right = null)
    {
        $this->lMargin = $left;
        $this->tMargin = $top;
        $this->rMargin = $right ?? $left;
    }

    public function SetAutoPageBreak($auto, $margin = 0)
    {
        // Not used in this minimal implementation but kept for API parity.
    }

    public function AddPage()
    {
        $this->page++;
        $this->pages[$this->page] = '';
        $this->pageImages[$this->page] = [];
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
    }

    public function Image($file, $x, $y, $w, $h)
    {
        $info = $this->loadImage($file);
        if (!$info) {
            return;
        }

        if (!isset($this->images[$file])) {
            $this->images[$file] = $info + ['i' => count($this->images) + 1];
        }

        $this->pageImages[$this->page][$file] = $this->images[$file]['i'];

        $this->pages[$this->page] .= $this->imageStream($file, $x, $y, $w, $h);
    }

    public function SetFont($family, $style = '', $size = 12)
    {
        $this->fontSizePt = $size;
    }

    public function SetTextColor($r, $g = null, $b = null)
    {
        if ($g === null) {
            $this->textColor = [$r, $r, $r];
        } else {
            $this->textColor = [$r, $g, $b];
        }
    }

    public function SetXY($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function SetX($x)
    {
        $this->x = $x;
    }

    public function Ln($h)
    {
        $this->x = $this->lMargin;
        $this->y += $h;
    }

    public function Cell($w, $h, $txt)
    {
        $this->pages[$this->page] .= $this->textStream($this->x, $this->y + $h - ($h / 4), $txt);
        $this->x += $w;
    }

    public function Output($dest = 'I')
    {
        return $this->bufferPDF();
    }

    private function bufferPDF()
    {
        $offsets = [];
        $result = "%PDF-1.4\n";
        $n = 0;

        // Font object (Helvetica)
        $fontObj = ++$n;
        $offsets[$fontObj] = strlen($result);
        $result .= "$fontObj 0 obj\n<</Type /Font /Subtype /Type1 /BaseFont /Helvetica>>\nendobj\n";

        // Image objects
        foreach ($this->images as $file => $info) {
            $n++;
            $this->images[$file]['n'] = $n;
            $stream = $info['data'];
            $offsets[$n] = strlen($result);
            $result .= "$n 0 obj\n";
            $result .= "<</Type /XObject /Subtype /Image /Width {$info['w']} /Height {$info['h']} ";
            $result .= "/ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length " . strlen($stream) . ">>\n";
            $result .= "stream\n$stream\nendstream\nendobj\n";
        }

        $pageObjects = [];
        foreach ($this->pages as $i => $content) {
            $resources = "/Font <</F1 $fontObj 0 R>>";
            $imgRes = '';
            foreach ($this->pageImages[$i] as $file => $id) {
                $imgRes .= "/I$id {$this->images[$file]['n']} 0 R ";
            }
            if ($imgRes) {
                $resources .= " /XObject <<$imgRes>>";
            }

            $n++;
            $contentObj = $n;
            $stream = $content;
            $offsets[$n] = strlen($result);
            $result .= "$n 0 obj\n<</Length " . strlen($stream) . ">>\nstream\n$stream\nendstream\nendobj\n";

            $n++;
            $pageObj = $n;
            $offsets[$n] = strlen($result);
            $result .= "$n 0 obj\n<</Type /Page /Parent 1 0 R /MediaBox [0 0 " . $this->toPt($this->w) . " " . $this->toPt($this->h) . "] ";
            $result .= "/Resources <<$resources>> /Contents $contentObj 0 R>>\nendobj\n";
            $pageObjects[] = $pageObj;
        }

        // Pages root
        $offsets[1] = strlen($result);
        $result .= "1 0 obj\n<</Type /Pages /Count " . count($pageObjects) . " /Kids [" . implode(' ', array_map(function ($p) {
            return "$p 0 R";
        }, $pageObjects)) . "]>>\nendobj\n";

        // Catalog
        $catalogId = ++$n;
        $offsets[$catalogId] = strlen($result);
        $result .= "$catalogId 0 obj\n<</Type /Catalog /Pages 1 0 R>>\nendobj\n";

        $xrefPos = strlen($result);
        $result .= "xref\n0 " . ($n + 1) . "\n";
        $result .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $n; $i++) {
            $result .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $result .= "trailer\n<</Size " . ($n + 1) . " /Root $catalogId 0 R>>\nstartxref\n$xrefPos\n%%EOF";

        return $result;
    }

    private function textStream($x, $y, $txt)
    {
        $txt = $this->escape($txt);
        $xPt = $this->toPt($x);
        $yPt = $this->toPt($this->h - $y);
        $color = [$this->textColor[0] / 255, $this->textColor[1] / 255, $this->textColor[2] / 255];
        $out = "BT /F1 {$this->fontSizePt} Tf " . implode(' ', $color) . " rg $xPt $yPt Td ($txt) Tj ET\n";
        return $out;
    }

    private function imageStream($file, $x, $y, $w, $h)
    {
        $info = $this->images[$file];
        $name = '/I' . $info['i'];
        $xPt = $this->toPt($x);
        $yPt = $this->toPt($this->h - $y - $h);
        $wPt = $this->toPt($w);
        $hPt = $this->toPt($h);
        return "q $wPt 0 0 $hPt $xPt $yPt cm $name Do Q\n";
    }

    private function escape($s)
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
    }

    private function toPt($v)
    {
        return $v * $this->k;
    }

    private function getScaleFactor($unit)
    {
        switch (strtolower($unit)) {
            case 'pt':
                return 1;
            case 'cm':
                return 72 / 2.54;
            case 'in':
                return 72;
            default:
                return 72 / 25.4; // mm
        }
    }

    private function loadImage($file)
    {
        if (!function_exists('getimagesize')) {
            return null;
        }

        $info = @getimagesize($file);
        if (!$info) {
            return null;
        }

        $type = $info[2];
        $image = null;
        if ($type === IMAGETYPE_JPEG) {
            $image = @imagecreatefromjpeg($file);
        } elseif ($type === IMAGETYPE_PNG) {
            $image = @imagecreatefrompng($file);
        }

        if (!$image) {
            return null;
        }

        ob_start();
        imagejpeg($image, null, 100);
        $data = ob_get_clean();
        imagedestroy($image);

        return [
            'w' => $info[0],
            'h' => $info[1],
            'data' => $data,
        ];
    }
}
