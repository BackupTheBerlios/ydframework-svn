<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

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

    <xsl:param name="body.font.family">Helvetica</xsl:param>
    <xsl:param name="body.font.master">9</xsl:param>
    <xsl:param name="title.font.family">Helvetica</xsl:param>
    
    <xsl:param name="alignment">left</xsl:param>
    <xsl:param name="line-height">1.5</xsl:param>
    <xsl:param name="hyphenate">false</xsl:param>

</xsl:stylesheet>
