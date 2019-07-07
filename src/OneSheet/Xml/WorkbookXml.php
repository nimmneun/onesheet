<?php

namespace OneSheet\Xml;

class WorkbookXml
{
    /**
     * Main Workbook XML file content
     */
    const WORKBOOK_XML = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><bookViews><workbookView activeTab="0" firstSheet="0" minimized="0" showHorizontalScroll="1" showSheetTabs="1" showVerticalScroll="1" tabRatio="600" visibility="visible"/></bookViews><sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets>%s</workbook>';

    /**
     * Optional XML for repeatable print headers / titles
     */
    const DEFINED_NAMES_XML = '<definedNames><definedName name="_xlnm.Print_Titles" localSheetId="0">Sheet1!$%d:$%d</definedName></definedNames>';
}