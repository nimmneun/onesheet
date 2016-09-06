<?php

namespace OneSheet\Xml;

/**
 * Class DefaultXml
 *
 * @package OneSheet
 */
class DefaultXml
{
    const CONTENT_TYPES = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default ContentType="application/xml" Extension="xml"/><Default ContentType="application/vnd.openxmlformats-package.relationships+xml" Extension="rels"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml" PartName="/xl/workbook.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml" PartName="/xl/worksheets/sheet1.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml" PartName="/xl/styles.xml"/><Override ContentType="application/vnd.openxmlformats-package.core-properties+xml" PartName="/docProps/core.xml"/><Override ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml" PartName="/docProps/app.xml"/></Types>';

    const DOCPROPS_CORE = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dcterms:created xsi:type="dcterms:W3CDTF">%s</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">%s</dcterms:modified><cp:revision>0</cp:revision></cp:coreProperties>';

    const DOCPROPS_APP = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>Excel2007</Application><TotalTime>0</TotalTime></Properties>';

    const RELS_RELS = '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rIdWorkbook" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rIdCore" Type="http://schemas.openxmlformats.org/officedocument/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rIdApp" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>';

    const XL_RELS_WORKBOOK = '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rIdStyles" Target="styles.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"/><Relationship Id="rId1" Target="worksheets/sheet1.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/></Relationships>';

    const XL_WORKBOOK = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><bookViews><workbookView activeTab="0" firstSheet="0" minimized="0" showHorizontalScroll="1" showSheetTabs="1" showVerticalScroll="1" tabRatio="600" visibility="visible"/></bookViews><sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets></workbook>';
}

