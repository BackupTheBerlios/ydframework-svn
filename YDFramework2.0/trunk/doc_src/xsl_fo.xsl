<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:fo="http://www.w3.org/1999/XSL/Format"
                version='1.0'>

    <xsl:import href="docbook/fo/docbook.xsl" />
    <xsl:import href="xsl_common.xsl" />

    <xsl:param name="shade.verbatim" select="1"/>

    <xsl:param name="paper.type">A4</xsl:param>
    <xsl:param name="page.margin.inner">2cm</xsl:param>
    <xsl:param name="page.margin.outer">2cm</xsl:param>
    <xsl:param name="title.margin.left">0pc</xsl:param>

    <xsl:param name="draft.mode">no</xsl:param>
    <xsl:param name="double.sided">0</xsl:param>

    <xsl:param name="fop.extensions">1</xsl:param>

    <xsl:param name="body.font.family">LucidaSans</xsl:param>
    <xsl:param name="body.font.master">9</xsl:param>
    <xsl:param name="title.font.family">LucidaSans</xsl:param>
    <xsl:param name="monospace.font.family">LucidaTypewriter</xsl:param>

    <xsl:attribute-set name="verbatim.properties">
        <xsl:attribute name="font-size">8pt</xsl:attribute>
    </xsl:attribute-set>

    <xsl:param name="alignment">left</xsl:param>
    <xsl:param name="line-height">1.5</xsl:param>
    <xsl:param name="hyphenate">false</xsl:param>

    <xsl:param name="ulink.show">0</xsl:param>

    <xsl:attribute-set name="xref.properties">
        <xsl:attribute name="color">darkred</xsl:attribute>
    </xsl:attribute-set>

    <fo:simple-page-master master-name="titlepage-first">
    </fo:simple-page-master>

</xsl:stylesheet>
