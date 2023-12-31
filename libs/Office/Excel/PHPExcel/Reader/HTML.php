<?php

if (!defined('PHPEXCEL_ROOT')) {
    /**
     * @ignore
     */
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}

/**
 * PHPExcel_Reader_HTML
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
/** PHPExcel root directory */
class PHPExcel_Reader_HTML extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{

    /**
     * Input encoding
     *
     * @var string
     */
    protected $inputEncoding = 'ANSI';

    /**
     * Sheet index to read
     *
     * @var int
     */
    protected $sheetIndex = 0;
    
    protected $startColumnIndex = 'A';
    protected $startRowIndex = 1;

    /**
     * Formats
     *
     * @var array
     */
    protected $formats = array(
        'h1' => array(
            'font' => array(
                'bold' => true,
                'size' => 24,
            ),
        ), //    Bold, 24pt
        'h2' => array(
            'font' => array(
                'bold' => true,
                'size' => 18,
            ),
        ), //    Bold, 18pt
        'h3' => array(
            'font' => array(
                'bold' => true,
                'size' => 13.5,
            ),
        ), //    Bold, 13.5pt
        'h4' => array(
            'font' => array(
                'bold' => true,
                'size' => 12,
            ),
        ), //    Bold, 12pt
        'h5' => array(
            'font' => array(
                'bold' => true,
                'size' => 10,
            ),
        ), //    Bold, 10pt
        'h6' => array(
            'font' => array(
                'bold' => true,
                'size' => 7.5,
            ),
        ), //    Bold, 7.5pt
        'a' => array(
            'font' => array(
                'underline' => true,
                'color' => array(
                    'argb' => PHPExcel_Style_Color::COLOR_BLUE,
                ),
            ),
        ), //    Blue underlined
        'hr' => array(
            'borders' => array(
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        PHPExcel_Style_Color::COLOR_BLACK,
                    ),
                ),
            ),
        ), //    Bottom border
    );

    protected $rowspan = array();

    /**
     * Create a new PHPExcel_Reader_HTML
     */
    public function __construct()
    {
        $this->readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }

    /**
     * Validate that the current file is an HTML file
     *
     * @return boolean
     */
    protected function isValidFormat()
    {
        //    Reading 2048 bytes should be enough to validate that the format is HTML
        $data = fread($this->fileHandle, 2048);
        if ((strpos($data, '<') !== false) &&
                (strlen($data) !== strlen(strip_tags($data)))) {
            return true;
        }

        return false;
    }

    /**
     * Loads PHPExcel from file
     *
     * @param  string                    $pFilename
     * @return PHPExcel
     * @throws PHPExcel_Reader_Exception
     */
    public function load($pFilename)
    {
        // Create new PHPExcel
        $objPHPExcel = new PHPExcel();

        // Load into this instance
        return $this->loadIntoExisting($pFilename, $objPHPExcel);
    }

    /**
     * Set input encoding
     *
     * @param string $pValue Input encoding
     */
    public function setInputEncoding($pValue = 'ANSI')
    {
        $this->inputEncoding = $pValue;

        return $this;
    }

    /**
     * Get input encoding
     *
     * @return string
     */
    public function getInputEncoding()
    {
        return $this->inputEncoding;
    }

    //    Data Array used for testing only, should write to PHPExcel object on completion of tests
    protected $dataArray = array();
    protected $tableLevel = 0;
    protected $nestedColumn = array('A', 'B', 'C', 'D');

    protected function setTableStartColumn($column)
    {
        if ($this->tableLevel == 0) {
            $column = 'A';
        }
        ++$this->tableLevel;
        $this->nestedColumn[$this->tableLevel] = $column;

        return $this->nestedColumn[$this->tableLevel];
    }

    protected function getTableStartColumn()
    {
        return $this->nestedColumn[$this->tableLevel];
    }

    protected function releaseTableStartColumn()
    {
        --$this->tableLevel;

        return array_pop($this->nestedColumn);
    }

    protected function flushCell($sheet, $column, $row, &$cellContent)
    {
        if (is_string($cellContent)) {
            //    Simple String content
            if (trim($cellContent) > '') {
                //    Only actually write it if there's content in the string
//                echo 'FLUSH CELL: ' , $column , $row , ' => ' , $cellContent , '<br />';
                //    Write to worksheet to be done here...
                //    ... we return the cell so we can mess about with styles more easily
                $sheet->setCellValue($column . $row, $cellContent, true);
                $this->dataArray[$row][$column] = $cellContent;
            }
        } else {
            //    We have a Rich Text run
            //    TODO
            $this->dataArray[$row][$column] = 'RICH TEXT: ' . $cellContent;
        }
        $cellContent = (string) '';
    }

    protected function processDomElement(DOMNode $element, $sheet, &$row, &$column, &$cellContent, $format = null)
    {   
        foreach ($element->childNodes as $child) {
            
            if ($child instanceof DOMText) {
                $domText = preg_replace('/\s+/u', ' ', trim($child->nodeValue));
                if (is_string($cellContent)) {
                    //    simply append the text if the cell content is a plain text string
                    $cellContent .= $domText;
                    
                    $this->flushCell($sheet, $column, $row, $cellContent);
                } else {
                    //    but if we have a rich text run instead, we need to append it correctly
                    //    TODO
                }
            } elseif ($child instanceof DOMElement) {
                
//                echo '<b>DOM ELEMENT: </b>' , strtoupper($child->nodeName) , '<br />';

                $attributeArray = array();
                foreach ($child->attributes as $attribute) {
//                    echo '<b>ATTRIBUTE: </b>' , $attribute->name , ' => ' , $attribute->value , '<br />';
                    $attributeArray[$attribute->name] = $attribute->value;
                }
                
                switch ($child->nodeName) {
                    case 'meta':
                        foreach ($attributeArray as $attributeName => $attributeValue) {
                            switch ($attributeName) {
                                case 'content':
                                    //    TODO
                                    //    Extract character set, so we can convert to UTF-8 if required
                                    break;
                            }
                        }
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        break;
                    case 'title':
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        $sheet->setTitle($cellContent);
                        $cellContent = '';
                        break;
                    case 'span':
                    case 'div':
                    case 'font':
                    case 'i':
                    case 'em':
                    case 'strong':
                    case 'b':
//                        echo 'STYLING, SPAN OR DIV<br />';
                        if ($cellContent > '') {
                            $cellContent .= ' ';
                        }
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        
                        if ($cellContent > '') {
                            $cellContent .= ' ';
                        }
//                        echo 'END OF STYLING, SPAN OR DIV<br />';
                        break;
                    case 'hr':
                        $this->flushCell($sheet, $column, $row, $cellContent);
                        ++$row;
                        if (isset($this->formats[$child->nodeName])) {
                            $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
                        } else {
                            $cellContent = '----------';
                            $this->flushCell($sheet, $column, $row, $cellContent);
                        }
                        ++$row;
                        // Add a break after a horizontal rule, simply by allowing the code to dropthru
                    case 'br':
                        if ($this->tableLevel > 0) {
                            //    If we're inside a table, replace with a \n
                            $cellContent .= "\n";
                        } else {
                            //    Otherwise flush our existing content and move the row cursor on
                            $this->flushCell($sheet, $column, $row, $cellContent);
                            ++$row;
                        }
//                        echo 'HARD LINE BREAK: ' , '<br />';
                        break;
                    case 'a':
//                        echo 'START OF HYPERLINK: ' , '<br />';
                        foreach ($attributeArray as $attributeName => $attributeValue) {
                            switch ($attributeName) {
                                case 'href':
//                                    echo 'Link to ' , $attributeValue , '<br />';
                                    $sheet->getCell($column . $row)->getHyperlink()->setUrl($attributeValue);
                                    if (isset($this->formats[$child->nodeName])) {
                                        $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
                                    }
                                    break;
                            }
                        }
                        $cellContent .= ' ';
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
//                        echo 'END OF HYPERLINK:' , '<br />';
                        break;
                    case 'h1':
                    case 'h2':
                    case 'h3':
                    case 'h4':
                    case 'h5':
                    case 'h6':
                    case 'ol':
                    case 'ul':
                    case 'p':
                        if ($this->tableLevel > 0) {
                            //    If we're inside a table, replace with a \n
                            $cellContent .= "\n";
//                            echo 'LIST ENTRY: ' , '<br />';
                            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
//                            echo 'END OF LIST ENTRY:' , '<br />';
                        } else {
                            if ($cellContent > '') {
                                $this->flushCell($sheet, $column, $row, $cellContent);
                                $row++;
                            }
//                            echo 'START OF PARAGRAPH: ' , '<br />';
                            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
//                            echo 'END OF PARAGRAPH:' , '<br />';
                            $this->flushCell($sheet, $column, $row, $cellContent);

                            if (isset($this->formats[$child->nodeName])) {
                                $sheet->getStyle($column . $row)->applyFromArray($this->formats[$child->nodeName]);
                            }

                            $row++;
                            $column = $this->startColumnIndex;
                        }
                        break;
                    case 'li':
                        if ($this->tableLevel > 0) {
                            //    If we're inside a table, replace with a \n
                            $cellContent .= "\n";
//                            echo 'LIST ENTRY: ' , '<br />';
                            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
//                            echo 'END OF LIST ENTRY:' , '<br />';
                        } else {
                            if ($cellContent > '') {
                                $this->flushCell($sheet, $column, $row, $cellContent);
                            }
                            ++$row;
//                            echo 'LIST ENTRY: ' , '<br />';
                            $this->processDomElement($child, $sheet, $row, $column, $cellContent);
//                            echo 'END OF LIST ENTRY:' , '<br />';
                            $this->flushCell($sheet, $column, $row, $cellContent);
                            $column = $this->startColumnIndex;
                        }
                        break;
                    case 'table':
                        $this->flushCell($sheet, $column, $row, $cellContent);
                        $column = $this->setTableStartColumn($column);
//                        echo 'START OF TABLE LEVEL ' , $this->tableLevel , '<br />';
                        if ($this->tableLevel > 1) {
                            --$row;
                        }
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
//                        echo 'END OF TABLE LEVEL ' , $this->tableLevel , '<br />';
                        $column = $this->releaseTableStartColumn();
                        if ($this->tableLevel > 1) {
                            ++$column;
                        } else {
                            ++$row;
                        }
                        break;
                    case 'thead':
                    case 'tbody':
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        break;
                    case 'tr':
                        $column = $this->getTableStartColumn();
                        $cellContent = '';
//                        echo 'START OF TABLE ' , $this->tableLevel , ' ROW<br />';
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        ++$row;
//                        echo 'END OF TABLE ' , $this->tableLevel , ' ROW<br />';
                        break;
                    case 'th':
                    case 'td':
//                        echo 'START OF TABLE ' , $this->tableLevel , ' CELL<br />';
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
//                        echo 'END OF TABLE ' , $this->tableLevel , ' CELL<br />';

                        while (isset($this->rowspan[$column . $row])) {
                            ++$column;
                        }

                        $this->flushCell($sheet, $column, $row, $cellContent);

                        if (isset($attributeArray['style']) && !empty($attributeArray['style'])) {
                            
                            $styleAry = $this->getPhpExcelStyleArray($attributeArray['style']);

                            if (!empty($styleAry)) {
                                $sheet->getStyle($column . $row)->applyFromArray($styleAry);
                            }
                        }

                        if (isset($attributeArray['rowspan']) && isset($attributeArray['colspan'])) {
                            //create merging rowspan and colspan
                            $columnTo = $column;
                            for ($i = 0; $i < $attributeArray['colspan'] - 1; $i++) {
                                ++$columnTo;
                            }
                            $range = $column . $row . ':' . $columnTo . ($row + $attributeArray['rowspan'] - 1);
                            foreach (\PHPExcel_Cell::extractAllCellReferencesInRange($range) as $value) {
                                $this->rowspan[$value] = true;
                            }
                            $sheet->mergeCells($range);
                            $column = $columnTo;
                        } elseif (isset($attributeArray['rowspan'])) {
                            //create merging rowspan
                            $range = $column . $row . ':' . $column . ($row + $attributeArray['rowspan'] - 1);
                            foreach (\PHPExcel_Cell::extractAllCellReferencesInRange($range) as $value) {
                                $this->rowspan[$value] = true;
                            }
                            $sheet->mergeCells($range);
                        } elseif (isset($attributeArray['colspan'])) {
                            //create merging colspan
                            $columnTo = $column;
                            for ($i = 0; $i < $attributeArray['colspan'] - 1; $i++) {
                                ++$columnTo;
                            }
                            $sheet->mergeCells($column . $row . ':' . $columnTo . $row);
                            $column = $columnTo;
                        }
                        ++$column;
                        break;
                    case 'body':
                        $row = $this->startRowIndex;
                        $column = $this->startColumnIndex;
                        $content = '';
                        $this->tableLevel = 0;
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                        break;
                    default:
                        $this->processDomElement($child, $sheet, $row, $column, $cellContent);
                }
            }
        }
    }

    /**
     * Loads PHPExcel from file into PHPExcel instance
     *
     * @param  string                    $pFilename
     * @param  PHPExcel                  $objPHPExcel
     * @return PHPExcel
     * @throws PHPExcel_Reader_Exception
     */
    public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
    {
        // Open file to validate
        $this->openFile($pFilename);
        if (!$this->isValidFormat()) {
            fclose($this->fileHandle);
            throw new PHPExcel_Reader_Exception($pFilename . " is an Invalid HTML file.");
        }
        //    Close after validating
        fclose($this->fileHandle);

        // Create new PHPExcel
        while ($objPHPExcel->getSheetCount() <= $this->sheetIndex) {
            $objPHPExcel->createSheet();
        }
        $objPHPExcel->setActiveSheetIndex($this->sheetIndex);

        //    Create a new DOM object
        $dom = new domDocument;
        //    Reload the HTML file into the DOM object
        $loaded = $dom->loadHTML(mb_convert_encoding($this->securityScanFile($pFilename), 'HTML-ENTITIES', 'UTF-8'));
        if ($loaded === false) {
            throw new PHPExcel_Reader_Exception('Failed to load ' . $pFilename . ' as a DOM Document');
        }

        //    Discard white space
        $dom->preserveWhiteSpace = false;

        $row = 0;
        $column = 'B';
        $content = '';
        $this->processDomElement($dom, $objPHPExcel->getActiveSheet(), $row, $column, $content);

        // Return
        return $objPHPExcel;
    }
    
    public function loadHtmlData($pHtmlData)
    {
        // Create new PHPExcel
        $objPHPExcel = new PHPExcel();

        // Load into this instance
        return $this->loadIntoExistingHtmlData('A', $pHtmlData, $objPHPExcel);
    }
        
       /**
	 * Loads PHPExcel from file or from HTML data into PHPExcel instance
	 *
	 * @param 	string 		$pFilename
	 * @param	PHPExcel	$objPHPExcel
	 * @return 	PHPExcel
	 * @throws 	PHPExcel_Reader_Exception
	 */
	public function loadIntoExistingHtmlData($startColumnIndex, $startRowIndex, $pHtmlData, PHPExcel $objPHPExcel)
	{
            $isHtmlFile = FALSE;
            if (is_file($pHtmlData)){
                $isHtmlFile = TRUE;
                // Open file to validate
                $this->_openFile($pHtmlData);
                if (!$this->_isValidFormat()) {
                    fclose ($this->fileHandle);
                    throw new PHPExcel_Reader_Exception($pHtmlData . " is an Invalid HTML file.");
                }
                //	Close after validating
                fclose ($this->fileHandle);
            }


            // Create new PHPExcel
            while ($objPHPExcel->getSheetCount() <= $this->sheetIndex) {
                $objPHPExcel->createSheet();
            }
            $objPHPExcel->setActiveSheetIndex( $this->sheetIndex );

            //	Create a new DOM object
            $dom = new domDocument;
            //	Reload the HTML file into the DOM object
            if ($isHtmlFile) {
                $loaded = $dom->loadHTMLFile($pHtmlData);
            } else {
                $pHtmlData = mb_convert_encoding($pHtmlData, 'HTML-ENTITIES', "UTF-8"); // added
                $loaded = $dom->loadHTML($pHtmlData);
            }

            if ($loaded === FALSE) {
                throw new PHPExcel_Reader_Exception('Failed to load ',$pHtmlData,' as a DOM Document');
            }

            //	Discard white space
            $dom->preserveWhiteSpace = false;

            $row = $startRowIndex;
            $column = $startColumnIndex;
            $this->startColumnIndex = $startColumnIndex;
            $this->startRowIndex = $startRowIndex;
            $content = '';
            
            $this->processDomElement($dom, $objPHPExcel->getActiveSheet(), $row, $column, $content);

            return $objPHPExcel;
    }

    /**
     * Get sheet index
     *
     * @return int
     */
    public function getSheetIndex()
    {
        return $this->sheetIndex;
    }

    /**
     * Set sheet index
     *
     * @param  int                  $pValue Sheet index
     * @return PHPExcel_Reader_HTML
     */
    public function setSheetIndex($pValue = 0)
    {
        $this->sheetIndex = $pValue;

        return $this;
    }

    /**
     * Scan theXML for use of <!ENTITY to prevent XXE/XEE attacks
     *
     * @param     string         $xml
     * @throws PHPExcel_Reader_Exception
     */
    public function securityScan($xml)
    {
        $pattern = '/\\0?' . implode('\\0?', str_split('<!ENTITY')) . '\\0?/';
        if (preg_match($pattern, $xml)) {
            throw new PHPExcel_Reader_Exception('Detected use of ENTITY in XML, spreadsheet file load() aborted to prevent XXE/XEE attacks');
        }
        return $xml;
    }
    
    /**
    * Converts an array of css style attributes to PHPExcel style values.
    * <p>
    * Any array element that is not a valid css attribute will be merged into
    * the returned array.
    *
    * @param  $mixed An array of css attributes
    * @return Array
    */
   public function getPHPExcelStyleArray()
   {
       $returnStyle = array();
       $args        = func_get_args();
       
       $argsArr = explode(';', $args[0]);
       
       $args = array();
       
       foreach ($argsArr as $argsArrRow) {
           if (!empty($argsArrRow)) {
               $argsArrExplode = explode(':', $argsArrRow);
               $args[][strtolower(trim($argsArrExplode[0]))] = trim($argsArrExplode[1]);
           }
       }
       
       foreach ($args as $key=>$css) {
           $style = array();
           // text-alignment
           if (isset($css['text-align'])) {
               $style['alignment'] = array();
               switch ($css['text-align']) {
               case 'left':$style['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                   break;
               case 'center':$style['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                   break;
               case 'right':$style['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
                   break;
               }

               unset($args[$key]['text-align']);
           }

           if (isset($css['vertical-align'])) {
               $style['alignment'] = isset($style['alignment']) ? $style['alignment'] : array();

               switch ($css['vertical-align']) {
               case 'top':$style['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_TOP;
                   break;
               case 'middle':$style['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_CENTER;
                   break;
               case 'bottom':$style['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
                   break;
               }

               unset($args[$key]['vertical-align']);
           }

           // background-color
           if (isset($css['background-color'])) {
               $style['fill'] = array(
                   'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                   'color' => array('rgb' => str_replace('#', '', $css['background-color'])),
               );

               unset($args[$key]['background-color']);
           }

           // font-size
           if (isset($css['font-size'])) {
               $style['font']         = isset($style['font']) ? $style['font'] : array();
               $style['font']['size'] = $css['font-size'];

               unset($args[$key]['font-size']);
           }

           // font-weight
           if (isset($css['font-weight'])) {
               $style['font']         = isset($style['font']) ? $style['font'] : array();
               $style['font'][$css['font-weight']] = true;

               unset($args[$key]['font-weight']);
           }

           // font-color
           if (isset($css['color'])) {
               $style['font']          = isset($style['font']) ? $style['font'] : array();
               $style['font']['color'] = array('rgb' => $css['color']);

               unset($args[$key]['color']);
           }

           // border
           if (isset($css['border'])) {
               $borderParts = explode(' ', $css['border']);

               $style['borders'] = isset($style['borders']) ? $style['borders'] : array();

               $border = array();

               foreach ($borderParts as $borderPart) {
                   switch ($borderPart) {
                   case 'thick':$border['style'] = PHPExcel_Style_Border::BORDER_THICK;
                       break;
                   case 'thin':$border['style'] = PHPExcel_Style_Border::BORDER_THIN;
                       break;
                   default:$border['color'] = array('rgb' => $borderPart);
                       break;
                   }
               }

               $style['borders'] = array(
                   'bottom' => $border,
                   'left'   => $border,
                   'top'    => $border,
                   'right'  => $border,
               );

               unset($args[$key]['borders']);
           }

           if (isset($css['border-top'])) {
               $borderParts = explode(' ', $css['border-top']);

               $style['borders'] = isset($style['borders']) ? $style['borders'] : array();

               $border = array();

               foreach ($borderParts as $borderPart) {
                   switch ($borderPart) {
                   case 'thick':$border['style'] = PHPExcel_Style_Border::BORDER_THICK;
                       break;
                   case 'thin':$border['style'] = PHPExcel_Style_Border::BORDER_THIN;
                       break;
                   default:$border['color'] = array('rgb' => $borderPart);
                       break;
                   }
               }

               $style['borders'] = array(
                   'top' => $border,
               );

               unset($args[$key]['border-top']);
           }

           if (isset($css['border-right'])) {
               $borderParts = explode(' ', $css['border-right']);

               $style['borders'] = isset($style['borders']) ? $style['borders'] : array();

               $border = array();

               foreach ($borderParts as $borderPart) {
                   switch ($borderPart) {
                   case 'thick':$border['style'] = PHPExcel_Style_Border::BORDER_THICK;
                       break;
                   case 'thin':$border['style'] = PHPExcel_Style_Border::BORDER_THIN;
                       break;
                   default:$border['color'] = array('rgb' => $borderPart);
                       break;
                   }
               }

               $style['borders'] = array(
                   'right' => $border,
               );

               unset($args[$key]['border-right']);
           }

           if (isset($css['border-bottom'])) {
               $borderParts = explode(' ', $css['border-bottom']);

               $style['borders'] = isset($style['borders']) ? $style['borders'] : array();

               $border = array();

               foreach ($borderParts as $borderPart) {
                   switch ($borderPart) {
                   case 'thick':$border['style'] = PHPExcel_Style_Border::BORDER_THICK;
                       break;
                   case 'thin':$border['style'] = PHPExcel_Style_Border::BORDER_THIN;
                       break;
                   default:$border['color'] = array('rgb' => $borderPart);
                       break;
                   }
               }

               $style['borders'] = array(
                   'bottom' => $border,
               );

               unset($args[$key]['border-bottom']);
           }

           if (isset($css['border-left'])) {
               $borderParts = explode(' ', $css['border-left']);

               $style['borders'] = isset($style['borders']) ? $style['borders'] : array();

               $border = array();

               foreach ($borderParts as $borderPart) {
                   switch ($borderPart) {
                   case 'thick':$border['style'] = PHPExcel_Style_Border::BORDER_THICK;
                       break;
                   case 'thin':$border['style'] = PHPExcel_Style_Border::BORDER_THIN;
                       break;
                   default:$border['color'] = array('rgb' => $borderPart);
                       break;
                   }
               }

               $style['borders'] = array(
                   'left' => $border,
               );

               unset($args[$key]['border-left']);
           }

           //$returnStyle = array_merge($returnStyle, $args[$key]);
           $returnStyle = array_merge($returnStyle, $style);
       }

       return $returnStyle;
    }
   
}
