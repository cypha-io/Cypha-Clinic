<?php

require('fpdf182/fpdf.php');

class LB_PDF extends FPDF {

    const COL_HDR_COLOR = 100;
    const ROW_FILL_COLOR = 200;
    const LINE_HEIGHT = 5;
    const HEADING_SIZE = 13;
    const BODY_SIZE = 10;
    const CAPTION_SIZE = 7;

    protected $pageHeaderRepeat;
    protected $pageHeaderAdded;
    protected $reportTitle;
    protected $fromDate;
    protected $toDate;
    protected $isFooterRequired;

    protected $widths;
    protected $aligns;
    protected $tblHdrWidths;//only used when pagebreak is needed.
    protected $tblHdrAligns;//only used when pagebreak is needed.
    protected $colsWithoutSpan1;
    protected $colsWithSpan1;
    protected $colsWithoutSpan2;
    protected $colsWithSpan2;
    protected $colsWithoutSpan3;
    protected $colsWithSpan3;
    protected $colsWithoutSpan4;
    protected $colsWithSpan4;
    protected $colsWithoutSpan5;
    protected $colsWithSpan5;

    public function __construct($orientation = 'P', $pageHeaderRepeat = false, $reportTitle = 'Report', $fromDate = '', $toDate = '', $isFooterRequired = true) {

        parent::__construct($orientation, 'mm', 'A4');
        $this->pageHeaderRepeat = $pageHeaderRepeat;
        $this->reportTitle = $reportTitle;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->isFooterRequired = $isFooterRequired;
        $this->resetTableHeaders();

        $this->SetFont('Times', '', self::BODY_SIZE);
        $this->SetLineWidth(0.3);
        $this->SetDrawColor(0);
    }

    public function resetTableHeaders() {
        $this->colsWithoutSpan1 = array();
        $this->colsWithSpan1 = array();
        $this->colsWithoutSpan2 = array();
        $this->colsWithSpan2 = array();
        $this->colsWithoutSpan3 = array();
        $this->colsWithSpan3 = array();
        $this->colsWithoutSpan4 = array();
        $this->colsWithSpan4 = array();
        $this->colsWithoutSpan5 = array();
        $this->colsWithSpan5 = array();
    }
    
    public function AddTableCaption($tableCaption) {
        $this->resetTableHeaders();
        //if caption and header(single or double height) and 
        //first row can be displayed
        $this->CheckPageBreak(self::CAPTION_SIZE + self::LINE_HEIGHT*3);
        $this->SetFont('', 'B', self::BODY_SIZE + 1);
        $this->Cell(0, self::CAPTION_SIZE, $tableCaption, 0, 0, 'L');
        $this->SetFont('', '', self::BODY_SIZE);
        $this->Ln();
    }

    public function AddTableHeader(
            $colsWithoutSpan1, $colsWithSpan1 = array(), 
            $colsWithoutSpan2 = array(), $colsWithSpan2 = array(), 
            $colsWithoutSpan3 = array(), $colsWithSpan3 = array(), 
            $colsWithoutSpan4 = array(), $colsWithSpan4 = array(), 
            $colsWithoutSpan5 = array(), $colsWithSpan5 = array()) {

        //if header and first row can be displayed
        $this->CheckPageBreak(self::LINE_HEIGHT*3);
        
        $this->colsWithoutSpan1 = $colsWithoutSpan1;
        $this->colsWithSpan1 = $colsWithSpan1;
        $this->colsWithoutSpan2 = $colsWithoutSpan2;
        $this->colsWithSpan2 = $colsWithSpan2;
        $this->colsWithoutSpan3 = $colsWithoutSpan3;
        $this->colsWithSpan3 = $colsWithSpan3;
        $this->colsWithoutSpan4 = $colsWithoutSpan4;
        $this->colsWithSpan4 = $colsWithSpan4;
        $this->colsWithoutSpan5 = $colsWithoutSpan5;
        $this->colsWithSpan5 = $colsWithSpan5;

        $this->SetFillColor(self::COL_HDR_COLOR);
        $this->SetTextColor(255);
        $this->SetFont('', 'B');

        //if no column needs a colspan
        if (count($this->colsWithSpan1) == 0) {
            if(isset($this->colsWithoutSpan1) && count($this->colsWithoutSpan1) > 0) {
                $this->AddRow($this->colsWithoutSpan1, true, false, true);
            }
        } else {
            $colIndex = 0;
            $this->AddTableHeaderCells($colIndex, $colsWithoutSpan1, $colsWithSpan1);
            $colIndex += count($colsWithoutSpan1) + (count($colsWithSpan1) == 0 ? 0 : count($colsWithSpan1) - 1);

            $this->AddTableHeaderCells($colIndex, $colsWithoutSpan2, $colsWithSpan2);
            $colIndex += count($colsWithoutSpan2) + (count($colsWithSpan2) == 0 ? 0 : count($colsWithSpan2) - 1);

            $this->AddTableHeaderCells($colIndex, $colsWithoutSpan3, $colsWithSpan3);
            $colIndex += count($colsWithoutSpan3) + (count($colsWithSpan3) == 0 ? 0 : count($colsWithSpan3) - 1);

            $this->AddTableHeaderCells($colIndex, $colsWithoutSpan4, $colsWithSpan4);
            $colIndex += count($colsWithoutSpan4) + (count($colsWithSpan4) == 0 ? 0 : count($colsWithSpan4) - 1);

            $this->AddTableHeaderCells($colIndex, $colsWithoutSpan5, $colsWithSpan5);

            $this->Ln(self::LINE_HEIGHT * 2);
        }

        $this->SetFont('', '');
        $this->SetTextColor(0);
        $this->tblHdrWidths = $this->widths;
        $this->tblHdrAligns = $this->aligns;
    }

    public function AddRow($data, $drawBorder = true, $fillRow = false, $isHeaderRow = false) {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = self::LINE_HEIGHT * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {

            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border and fill area
            if (!$isHeaderRow) {
                $this->SetFillColor($fillRow ? self::ROW_FILL_COLOR : 255);
            }
            $this->Rect($x, $y, $w, $h, $drawBorder ? 'DF' : 'F');
            //Print the text
            $this->MultiCell($w, self::LINE_HEIGHT, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }
    
    public function addLineSeparator() {
        $this->Ln(2);
        $this->Cell(0, 0.5, '', 'B', 1);
        $this->Ln(2);
    }

    // Page header
    public function Header() {
        if ($this->pageHeaderRepeat || (!$this->pageHeaderRepeat && !$this->pageHeaderAdded)) {
            // Logo
            $this->Image('dist/img/logo.jpg', $this->GetX(), $this->GetY(), 30);
            // Times bold 13
            $this->SetFont('', 'B', self::HEADING_SIZE);
            // Title
            $this->Cell(0, 7, 'Patient Management System', 0, 0, 'C');
            $this->Ln();
            $this->Cell(0, 7, $this->reportTitle, 0, 0, 'C');
            $this->Ln();
            if ($this->fromDate != '' || $this->toDate != '') {
                if ($this->fromDate != '' && $this->toDate != '') {
                $this->Cell(0, 7, 'From ' . $this->fromDate . '  to  ' . $this->toDate, 0, 0, 'C');
                } else if($this->fromDate != '') {
                    $this->Cell(0, 7,  $this->fromDate , 0, 0, 'C');
                }
            }
            // Line break.
            $this->Ln(13);    //height of break can be given

            $this->SetFont('', '', self::BODY_SIZE);
            $this->pageHeaderAdded = true;
        }
    }

    // Page footer
    public function Footer() {
        if ($this->isFooterRequired) {
            // Position at 1.0 cm from bottom
            $this->SetY(-11);
            // Times italic 8
            $this->SetFont('', 'B', self::BODY_SIZE);
            // Page number
            $this->Cell(0, 6, 'Page ' . $this->PageNo() . ' / {nb}', 0, 0, 'C');
        }
    }

    public function SetWidths($w) {
        //Set the array of column widths
        $this->widths = $w;
    }

    public function SetAligns($a) {
        //Set the array of column alignments for table headers
        $this->aligns = $a;
    }

    private function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger) {

            $this->AddPage($this->CurOrientation);
            
            $currentWidths = $this->widths;
            $currentAligns = $this->aligns;
            $this->widths = $this->tblHdrWidths;
            $this->aligns = $this->tblHdrAligns;
            $this->AddTableHeader(
                    $this->colsWithoutSpan1, $this->colsWithSpan1, 
                    $this->colsWithoutSpan2, $this->colsWithSpan2, 
                    $this->colsWithoutSpan3, $this->colsWithSpan3, 
                    $this->colsWithoutSpan4, $this->colsWithSpan4);
            $this->widths = $currentWidths;
            $this->aligns = $currentAligns;
        }
    }

    private function NbLines($w, $txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n") {
            $nb--;
        }

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    private function AddTableHeaderCells($colIndex, $cols, $spannedCols) {
        for ($i = 0; $i < count($cols); $i++) {
            $this->AddHdrCell($this->widths[$colIndex], self::LINE_HEIGHT * 2, $cols[$i], $this->aligns[$colIndex]);
            $colIndex++;
        }
        if (count($spannedCols) > 0) {
            $spanningColWidth = 0;
            for ($i = 1, $j = $colIndex; $i < count($spannedCols); $i++, $j++) {
                $spanningColWidth = $spanningColWidth + $this->widths[$j];
            }
            for ($i = 0; $i < count($spannedCols); $i++) {
                if ($i == 0) {
                    $this->AddHdrCell($spanningColWidth, self::LINE_HEIGHT, $spannedCols[$i], 'C');
                    $this->SetXY($this->GetX() - $spanningColWidth, $this->GetY() + self::LINE_HEIGHT);
                } else {
                    $this->AddHdrCell($this->widths[$colIndex], self::LINE_HEIGHT, $spannedCols[$i], $this->aligns[$colIndex]);
                    $colIndex++;
                }
            }
            $this->SetXY($this->GetX(), $this->GetY() - self::LINE_HEIGHT);
        }
    }
    
    private function AddHdrCell($width, $height, $text, $align) {
        //Calculate the height of the row
        $textHeight = self::LINE_HEIGHT;
        if($height > self::LINE_HEIGHT && $this->NbLines($width, $text) == 1) {
            $textHeight = $height;
        }
        //Save the current position
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Rect($x, $y, $width, $height, 'DF');
        //Print the text
        $this->MultiCell($width, $textHeight, $text, 0, $align);
        //Put the position to the right of the cell
        $this->SetXY($x + $width, $y);
    }
    
}
?>