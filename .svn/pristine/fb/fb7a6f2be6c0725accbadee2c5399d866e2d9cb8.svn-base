<?php

/**
 * Simple excel generating from PHP5
 * 
 * This is one of my utility-classes.
 * 
 * The MIT License
 * 
 * Copyright (c) 2007 Oliver Schwarz
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package Utilities
 * @author Oliver Schwarz <oliver.schwarz@gmail.com>
 * @version 1.0
 */

/**
 * Generating excel documents on-the-fly from PHP5
 * 
 * Uses the excel XML-specification to generate a native
 * XML document, readable/processable by excel.
 * 
 * @package Utilities
 * @subpackage Excel
 * @author Oliver Schwarz <oliver.schwarz@vaicon.de>
 * @version 1.0
 *
  * @todo Add error handling (array corruption etc.)
 * @todo Write a wrapper method to do everything on-the-fly
 */
class Excel_XML
{

    /**
     * Header of excel document (prepended to the rows)
     * 
     * Copied from the excel xml-specs.
     * 
     * @access private
     * @var string
     */
    private $header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?\>
<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
 xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:html=\"http://www.w3.org/TR/REC-html40\">";

    /**
     * Footer of excel document (appended to the rows)
     * 
     * Copied from the excel xml-specs.
     * 
     * @access private
     * @var string
     */
    private $footer = "</Workbook>";

    /**
     * Document lines (rows in an array)
     * 
     * @access private
     * @var array
     */
    private $lines = array ();

    /**
     * Worksheet title
     *
     * Contains the title of a single worksheet
     *
     * @access private 
     * @var string
     */
    private $worksheet_title = "Table1";
	public function getLines(){
		return implode ("\n", $this->lines);
	}
    /**
     * Add a single row to the $document string
     * 
     * @access private
     * @param array 1-dimensional array
     * @todo Row-creation should be done by $this->addArray
     */
    /*
    private function addRow ($array)
    {

        // initialize all cells for this row
        $cells = "";
		
        // foreach key -> write value into cells
        
        foreach ($array as $k => $v):

            $cells .= "<Cell><Data ss:Type=\"String\">" . utf8_encode($v) . "</Data></Cell>\n"; 

        endforeach;
        
    	/*
        foreach ($array as $k => $v):
        $ssType = "String";
        if(is_numeric($v)) {
        	$ssType = "Number";
        }
        $cells .= "<Cell><Data ss:Type=\"".$ssType."\">" .
        utf8_encode($v) . "</Data></Cell>\n";

        endforeach;
		*/
    	/*
    	// transform $cells content into one row
        $this->lines[] = "<Row>\n" . $cells . "</Row>\n";

    }
	*/
	
    // bowikaxu
    public function rowClear(){
    	$this->lines=array();
    }
    public function addRow ($array)
    {

        // initialize all cells for this row
        $cells = "";

        // foreach key -> write value into cells
        foreach ($array as $k => $v):
                        if (is_numeric($v)) {
                                //if is numeric and starts with zero, and is not zero and is not zero-decimal:

                                if (substr($v, 0, 1) == "0" && $v != 0 && substr($v, 0, 2) !== "0.") {
                                        $type = "String";
                                } else {
                                        $type = "Number";
                                }
                        } else {
                                $type = "String";
                        }

                        $cells .= "<td><Data ss:Type=\"$type\">" . utf8_encode($v) . "</Data></td>\n";

        endforeach;

        // transform $cells content into one row
        $this->lines[] = "<tr>\n" . $cells . "</tr>\n";

    }
    
    /**
     * Add an array to the document
     * 
     * This should be the only method needed to generate an excel
     * document.
     * 
     * @access public
     * @param array 2-dimensional array
     * @todo Can be transfered to __construct() later on
     */
    public function addArray ($array)
    {

        // run through the array and add them into rows
        foreach ($array as $k => $v):
            $this->addRow ($v);
        endforeach;

    }

    /**
     * Set the worksheet title
     * 
     * Checks the string for not allowed characters (:\/?*),
     * cuts it to maximum 31 characters and set the title. Damn
     * why are not-allowed chars nowhere to be found? Windows
     * help's no help...
     *
     * @access public
     * @param string $title Designed title
     */
    public function setWorksheetTitle ($title)
    {

        // strip out special chars first
        $title = preg_replace ("/[\\\|:|\/|\?|\*|\[|\]]/", "", $title);

        // now cut it to the allowed length
        $title = substr ($title, 0, 31);

        // set title
        $this->worksheet_title = $title;

    }
	function SendHeaders($filename){
		// deliver header (as recommended in php manual)
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		header("Content-Disposition: inline; filename=\"" . $filename . ".xls\"");
	}
	function SendBody(){
		return 
		"\n<Worksheet ss:Name=\"" . $this->worksheet_title . "\">\n".//"<Table>\n"
		        "<Column ss:Index=\"1\" ss:AutoFitWidth=\"0\" ss:Width=\"110\"/>\n"
		//
		//"</Table>\n".
		;
	}
    /**
     * Generate the excel file
     * 
     * Finally generates the excel file and uses the header() function
     * to deliver it to the browser.
     * 
     * @access public
     * @param string $filename Name of excel file to generate (...xls)
     */
    function generateXML ($filename)
    {        
    	$this->SendHeaders($filename);
        echo $this->GetHeader();
        // print out document to the browser
        // need to use stripslashes for the damn ">"
        //echo stripslashes ($this->header);
        echo $this->SendBody();
        echo $this->getLines();
        echo $this->GetFooter();
        //echo $this->footer;

    }
    
    function GetHeader()
    {
    	$header = <<<EOH
<html xmlns:o="urn:schemas-microsoft-com:office:office"
		xmlns:x="urn:schemas-microsoft-com:office:excel"
		xmlns="http://www.w3.org/TR/REC-html40">
    
		<head>
		<meta http-equiv=Content-Type content="text/html; charset=us-ascii">
		<meta name=ProgId content=Excel.Sheet>
		<!--[if gte mso 9]><xml>
		 <o:DocumentProperties>
		  <o:LastAuthor>Sriram</o:LastAuthor>
		  <o:LastSaved>2005-01-02T07:46:23Z</o:LastSaved>
		  <o:Version>10.2625</o:Version>
		 </o:DocumentProperties>
		 <o:OfficeDocumentSettings>
		  <o:DownloadComponents/>
		 </o:OfficeDocumentSettings>
		</xml><![endif]-->
		<style>
		<!--table
		    {mso-displayed-decimal-separator:"\.";
		    mso-displayed-thousand-separator:"\,";}
		@page
		    {margin:1.0in .75in 1.0in .75in;
		    mso-header-margin:.5in;
		    mso-footer-margin:.5in;}
		tr
		    {mso-height-source:auto;}
		col
		    {mso-width-source:auto;}
		br
		    {mso-data-placement:same-cell;}
		.style0
		    {mso-number-format:General;
		    text-align:general;
		    vertical-align:bottom;
		    white-space:nowrap;
		    mso-rotate:0;
		    mso-background-source:auto;
		    mso-pattern:auto;
		    color:windowtext;
		    font-size:10.0pt;
		    font-weight:400;
		    font-style:normal;
		    text-decoration:none;
		    font-family:Arial;
		    mso-generic-font-family:auto;
		    mso-font-charset:0;
		    border:none;
		    mso-protection:locked visible;
		    mso-style-name:Normal;
		    mso-style-id:0;}
		td
		    {mso-style-parent:style0;
		    padding-top:1px;
		    padding-right:1px;
		    padding-left:1px;
		    mso-ignore:padding;
		    color:windowtext;
		    font-size:10.0pt;
		    font-weight:400;
		    font-style:normal;
		    text-decoration:none;
		    font-family:Arial;
		    mso-generic-font-family:auto;
		    mso-font-charset:0;
		    mso-number-format:General;
		    text-align:general;
		    vertical-align:bottom;
		    border:none;
		    mso-background-source:auto;
		    mso-pattern:auto;
		    mso-protection:locked visible;
		    white-space:nowrap;
		    mso-rotate:0;}
		.xl24
		    {mso-style-parent:style0;
		    white-space:normal;}
		-->
		</style>
		<!--[if gte mso 9]><xml>
		 <x:ExcelWorkbook>
		  <x:ExcelWorksheets>
		   <x:ExcelWorksheet>
		    <x:Name>srirmam</x:Name>
		    <x:WorksheetOptions>
		     <x:Selected/>
		     <x:ProtectContents>False</x:ProtectContents>
		     <x:ProtectObjects>False</x:ProtectObjects>
		     <x:ProtectScenarios>False</x:ProtectScenarios>
		    </x:WorksheetOptions>
		   </x:ExcelWorksheet>
		  </x:ExcelWorksheets>
		  <x:WindowHeight>10005</x:WindowHeight>
		  <x:WindowWidth>10005</x:WindowWidth>
		  <x:WindowTopX>120</x:WindowTopX>
		  <x:WindowTopY>135</x:WindowTopY>
		  <x:ProtectStructure>False</x:ProtectStructure>
		  <x:ProtectWindows>False</x:ProtectWindows>
		 </x:ExcelWorkbook>
		</xml><![endif]-->
		</head>
    
		<body link=blue vlink=purple>
		<table x:str border=0 cellpadding=0 cellspacing=0 style='border-collapse: collapse;table-layout:fixed;'>
EOH;
    	return $header;
    }
    function GetFooter()
    {
    	return "</Worksheet>\n</table></body></html>";
    }

}

?>