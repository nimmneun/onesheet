<?php

namespace OneSheet\Xml;

class WorkbookXml
{
    /**
     * Main Workbook XML file content
     */
    const WORKBOOK_XML = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><bookViews><workbookView activeTab="0" firstSheet="0" minimized="0" showHorizontalScroll="1" showSheetTabs="1" showVerticalScroll="1" tabRatio="600" visibility="visible"/></bookViews><sheets>%s</sheets>%s</workbook>';

    /**
     * Repeat for each sheet
     */
    const WORKBOOK_SHEETS_XML = '<sheet name="%s" sheetId="%d" r:id="rId%d"/>';

    /**
     * Optional XML for repeatable print headers / titles
     */
    const DEFINED_NAMES_XML = '<definedNames>%s</definedNames>';

    /**
     * Required definedName fo repeatable print headers
     */
    const DEFINED_NAME_XML = '<definedName name="_xlnm.Print_Titles" localSheetId="%d">%s!$%d:$%d</definedName>';

    /**
     * A workbooks sheet file relations.
     */
    const WORKBOOK_RELS_XML = '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rIdStyles" Target="styles.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"/>%s</Relationships>';

    /**
     * A single sheet file relation.
     */
    const WORKBOOK_REL_XML = '<Relationship Id="rId%d" Target="worksheets/%s.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/>';
}
