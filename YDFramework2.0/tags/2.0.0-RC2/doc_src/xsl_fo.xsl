<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:fo="http://www.w3.org/1999/XSL/Format"
                version='1.0'>

    <xsl:import href="docbook/fo/docbook.xsl" />
    <xsl:import href="xsl_common.xsl" />

    <xsl:param name="role">fo</xsl:param>

    <xsl:param name="shade.verbatim">1</xsl:param>

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

    <xsl:template name="nongraphical.admonition">
      <xsl:variable name="id">
        <xsl:call-template name="object.id"/>
      </xsl:variable>
    
      <fo:block space-before.minimum="0.8em"
                space-before.optimum="1em"
                space-before.maximum="1.2em"
                start-indent="0.25in"
                end-indent="0.25in"
                id="{$id}"
                background-color="#E0E0E0" color="darkred">
        <xsl:if test="$admon.textlabel != 0 or title">
          <fo:block keep-with-next='always'
                    xsl:use-attribute-sets="admonition.title.properties">
             <xsl:apply-templates select="." mode="object.title.markup"/>
          </fo:block>
        </xsl:if>
    
        <fo:block xsl:use-attribute-sets="admonition.properties">
          <xsl:apply-templates/>
        </fo:block>
      </fo:block>
    </xsl:template>

</xsl:stylesheet>
