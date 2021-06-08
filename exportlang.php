<?php

require_once('excelclass/PHPExcel.php');
require_once('excelclass/PHPExcel/Writer/Excel5.php');
require_once('excelclass/html2text/Html2Text.php');

$dir    = './' . $_GET['module'] . '/en';

$files = scandir($dir);
$files = array_slice(scandir($dir), 2);

$content = array();
natsort($files);

$slides = array();
/**
 * Gather slide count information
 */
foreach ($files as $f) {

    if (strpos($f, 'slide') === false) {
        continue;
    }

    if (strpos($f, '-')) {

        continue;
    }

    $slides[] = $f;
}

if (count($slides) < 1) {
    exit('No slides found');
}

/**
 * Start excel
 */
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Muris Arndt");
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);

$rowcounter = 1;
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Slide')
    ->setCellValue('B1', 'Filename')
    ->setCellValue('C1', 'Translation Line')
    ->setCellValue('D1', 'Content location [HTML Title,Slide head title, Slide Body, JS]')
    ->setCellValue('E1', 'original string')
    ->setCellValue('F1', 'translated string');
$rowcounter++;
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(55);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(55);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(55);

$slidecounter = 1;
$translationlinecounter = 1;
$wordcounter = 0;
foreach ($slides as $slide) {
    $firstcontent = true;
    $file_path = $dir . '/' . $slide;
    /**
     * HTML Title section
     */
    $row = array('Slide ' . $slidecounter . ' of ' . count($slides), $slide, $translationlinecounter, 'HTML Title', page_title($file_path));
    $objPHPExcel->getActiveSheet()->fromArray($row, NULL, 'A' . $rowcounter);
    $rowcounter++;
    $translationlinecounter++;
    $wordscounter = $wordscounter + str_word_count(page_title($file_path));

    /**
     * Slide title section
     */
    $slidecontent = file_get_contents($file_path);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $slidecontent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    //print_object($dom);
    $xpath = new DOMXPath($dom);
    $body = $dom->getElementsByTagName('body')->item(0);

    $navLeft = $xpath->query('//div[contains(@class,"left-section")]')->item(0);
    $navhtml = getInnerHTML($navLeft);

    $container = $xpath->query('//div[contains(@class,"container")]')->item(0);
    $slidehtml = getInnerHTML($container);
    $html2text = new \Html2Text\Html2Text($slidehtml, array('width' => 0, 'do_links' => 'none'));
    $wordscounter = $wordscounter + str_word_count($html2text->getText());
    $lines = preg_split("/\r\n|\n|\r/", $html2text->getText());

    foreach ($lines as $l) {
        $l = trim($l);
        if (is_number($l)) continue;
        $l = str_replace('__', '', $l);
        if (strlen($l)) {
            if ($firstcontent) {
                $row = array('', '', $translationlinecounter, 'Slide Title', $l);
                $objPHPExcel->getActiveSheet()->fromArray($row, NULL, 'A' . $rowcounter);
                $rowcounter++;
                $translationlinecounter++;
                $firstcontent = false;
            } else {
                $row = array('', '', $translationlinecounter, '', $l);
                $objPHPExcel->getActiveSheet()->fromArray($row, NULL, 'A' . $rowcounter);
                $rowcounter++;
                $translationlinecounter++;
            }
        }
    }
    $objPHPExcel->getActiveSheet()->fromArray(array(), NULL, 'A' . $rowcounter);
    $objPHPExcel->getActiveSheet()
        ->getStyle('A' . $rowcounter . ':G' . $rowcounter)
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FFFACD');
    $rowcounter++;

    /**
     * SUBSLIDES WORK
     */
    $subslidecount = 1;
    $file_path_base = $dir . '/slide' . $slidecounter . '-';
    $hassubslides = false;
    while ($content = file_get_contents($file_path_base . $subslidecount . '.html')) {
        $firstsubcontent = true;
        $hassubslides = true;
        /**
         * HTML Title section
         */
        $row = array('', 'slide' . $slidecounter . '-' . $subslidecount . '.html', $translationlinecounter, 'HTML Title', page_title($file_path_base . $subslidecount . '.html'));
        $objPHPExcel->getActiveSheet()->fromArray($row, NULL, 'A' . $rowcounter);
        $rowcounter++;
        $translationlinecounter++;


        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        //print_object($dom);
        $xpath = new DOMXPath($dom);
        $body = $dom->getElementsByTagName('body')->item(0);

        $navLeft = $xpath->query('//div[contains(@class,"left-section")]')->item(0);
        $navhtml = getInnerHTML($navLeft);

        $container = $xpath->query('//div[contains(@class,"container")]')->item(0);
        $slidehtml = getInnerHTML($container);
        $html2text = new \Html2Text\Html2Text($slidehtml, array('width' => 0, 'do_links' => 'none'));
        $lines = preg_split("/\r\n|\n|\r/", $html2text->getText());
        $wordscounter = $wordscounter + str_word_count($html2text->getText());
        foreach ($lines as $l) {
            $trimmed = trim($l);
            if (is_number($trimmed)) continue;
            if ($trimmed == '__') continue;
            $l = str_replace('__', '', $l);
            if (strlen($trimmed)) {
                if ($firstsubcontent) {
                    $row = array('', '', $translationlinecounter, 'Slide Title', $l);
                    $objPHPExcel->getActiveSheet()->fromArray($row, NULL, 'A' . $rowcounter);
                    $rowcounter++;
                    $translationlinecounter++;
                    $firstsubcontent = false;
                } else {
                    $row = array('', '', $translationlinecounter, '', $l);
                    $objPHPExcel->getActiveSheet()->fromArray($row, NULL, 'A' . $rowcounter);
                    $rowcounter++;
                    $translationlinecounter++;
                    $firstcontent = false;
                }
            }
        }
        $objPHPExcel->getActiveSheet()->fromArray(array(), NULL, 'A' . $rowcounter);

        $objPHPExcel->getActiveSheet()
            ->getStyle('A' . $rowcounter . ':G' . $rowcounter)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFACD');
        $rowcounter++;
        $subslidecount++;
    }

    $objPHPExcel->getActiveSheet()
        ->getStyle('A' . $rowcounter . ':G' . $rowcounter)
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FF808080');
    $rowcounter++;



    $slidecounter++;
}
$objPHPExcel->getActiveSheet()->getStyle('E1:G'.$rowcounter)
    ->getAlignment()->setWrapText(true);

$rowcounter++;
$objPHPExcel->getActiveSheet()->fromArray(array('Total words:',$wordscounter), NULL, 'A'.$rowcounter);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $_GET['module'] . '.xls"');
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$writer->save('php://output');
exit;

foreach ($files as $file) {

    $file_path = $dir . '/' . $file;

    if (strpos($file, 'slide') !== false) {
        $slide = new stdClass();

        $slide->slide_name = $file;
        $slide->title = page_title($file_path);

        $chars = ["&nbsp;", "/\s+/", "#\R+#"];

        $site = file_get_contents($file_path);
        $text =  preg_match("/<body[^>]*>(.*?)<\/body>/is", $site, $matches);
        $text = str_replace($chars, '', strip_tags(preg_replace('/<img[^>]+\>/i', '', preg_replace('#<script(.*?)>(.*?)</script>#is', '', $matches[1]))));
        $text = trim($text);

        $text = str_replace(array("\r", "\n"), '', $text);
        $text = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $text);

        $text = str_replace(['|', '|'], ["\r\n", "\r\n"], $text);

        $slide->text = $text;

        array_push($content, $slide);
    }
}

// get page title
function page_title($url)
{
    $fp = file_get_contents($url);
    if (!$fp)
        return null;

    $res = preg_match("/<title>(.*)<\/title>/siU", $fp, $title_matches);
    if (!$res)
        return null;

    // Clean up title: remove EOL's and excessive whitespace.
    $title = preg_replace('/\s+/', ' ', $title_matches[1]);
    $title = trim($title);
    return $title;
}

function getInnerHTML(\DOMElement $element)
{
    $doc = $element->ownerDocument;

    $html = '';

    foreach ($element->childNodes as $node) {
        $html .= $doc->saveHTML($node);
    }

    return $html;
}

function is_number($value)
{
    if (is_int($value)) {
        return true;
    } else if (is_string($value)) {
        return ((string)(int)$value) === $value;
    } else {
        return false;
    }
}
